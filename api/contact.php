<?php
/**
 * POST /api/contact — AJAX contact form endpoint.
 * 1 validate request → 2 CSRF → 3 spam checks → 4 validate & sanitise
 * 5 store (prepared) → 6 notify by email → 7 JSON response
 * Falls back to a redirect for non-JS submissions.
 */
declare(strict_types=1);

if (!defined('HT_API')) {           // only reachable through the front controller
    http_response_code(404);
    exit;
}

require_once root_path('includes/mailer.php');

$respond = static function (bool $ok, string $message, array $errors = [], int $code = 200, array $extra = []) {
    if (!is_ajax()) {                      // progressive-enhancement fallback
        flash($ok ? 'contact_ok' : 'contact_err', $message);
        redirect('/contact/' . ($ok ? '?sent=1' : ''), 303);
    }
    json_response(['ok' => $ok, 'message' => $message, 'errors' => (object)$errors] + $extra, $code);
};

// 1. Method
if (!is_post()) {
    $respond(false, 'Method not allowed.', [], 405);
}

// 2. CSRF
if (!csrf_valid()) {
    $respond(false, 'Your session expired. Please refresh the page and try again.', [], 419);
}

// 3. Spam protection: honeypot, signed time-trap, per-IP rate limit
$ip = client_ip();
if ((string)($_POST['website'] ?? '') !== '') {
    $respond(true, 'Thanks — your message has been sent.');      // silently accept bots
}
$ts = verify_signed((string)($_POST['_ts'] ?? ''));
if ($ts === null || (time() - (int)$ts) < 3 || (time() - (int)$ts) > 86400) {
    $respond(false, 'Please take a moment to review your message and submit again.', [], 422);
}
$recent = (int)DB::value(
    'SELECT COUNT(*) FROM contact_submissions WHERE ip = ? AND created_at > ?',
    [$ip, date('Y-m-d H:i:s', time() - 3600)]
);
if ($recent >= 5) {
    $respond(false, 'Too many messages from your network. Please email us directly.', [], 429);
}

// 4. Validate & sanitise
$clean = static fn(string $k, int $max) => mb_substr(trim(strip_tags((string)($_POST[$k] ?? ''))), 0, $max);
$data = [
    'name'    => $clean('name', 120),
    'email'   => strtolower($clean('email', 190)),
    'phone'   => $clean('phone', 40),
    'company' => $clean('company', 150),
    'budget'  => $clean('budget', 60),
    'message' => mb_substr(trim(strip_tags((string)($_POST['message'] ?? ''))), 0, 5000),
];
$errors = [];
if (mb_strlen($data['name']) < 2) {
    $errors['name'] = 'Please enter your full name.';
}
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Please enter a valid email address.';
}
if ($data['phone'] !== '' && !preg_match('/^[+()\d\s.-]{6,40}$/', $data['phone'])) {
    $errors['phone'] = 'Please enter a valid phone number.';
}
if ($data['budget'] !== '' && !in_array($data['budget'], setting_list('budget_options'), true)) {
    $errors['budget'] = 'Please choose a budget from the list.';
}
if (mb_strlen($data['message']) < 20) {
    $errors['message'] = 'Please share a little more detail (at least 20 characters).';
}
if (preg_match_all('#https?://#i', $data['message']) > 3) {
    $errors['message'] = 'Please include no more than three links.';
}
if ($errors) {
    $respond(false, 'Please correct the highlighted fields.', $errors, 422);
}

// 5. Store
$id = DB::insert('contact_submissions', $data + [
    'status'     => 'new',
    'ip'         => $ip,
    'user_agent' => mb_substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
    'created_at' => now(),
]);

// 6. Notify (failure is logged but never blocks the visitor)
$to = setting('notify_email', setting('contact_email'));
if ($to && filter_var($to, FILTER_VALIDATE_EMAIL)) {
    $body = "New project enquiry #$id\n\n"
          . "Name:    {$data['name']}\nEmail:   {$data['email']}\nPhone:   {$data['phone']}\n"
          . "Company: {$data['company']}\nBudget:  {$data['budget']}\n\n{$data['message']}\n\n"
          . "Manage: " . url('admin/message.php?id=' . $id) . "\n";
    if (!send_mail($to, 'New enquiry from ' . $data['name'], $body, $data['email'])) {
        log_error("Contact notification failed for submission #$id");
    }
}

// 6b. Acknowledgment email to the visitor
$ackBody = "Hi {$data['name']},\n\n"
    . "Thank you for contacting " . setting('site_name', 'Primary Infotech') . " — we have received your message:\n\n"
    . "\"{$data['message']}\"\n\n"
    . "A member of our team will reply within one business day. If your enquiry is urgent, "
    . "call us at " . setting('contact_phone') . ".\n\n"
    . "— " . setting('site_name', 'Primary Infotech') . "\n";
if (!send_mail($data['email'], 'We received your message — ' . setting('site_name', 'Primary Infotech'), $ackBody)) {
    log_error("Contact acknowledgment failed for submission #$id to {$data['email']}");
}

// 7. Respond
$respond(true, setting('contact_success_text', 'Thanks — we will be in touch shortly.'));
