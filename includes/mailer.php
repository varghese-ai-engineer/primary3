<?php
/**
 * Minimal mailer: PHP mail(), a dependency-free SMTP client, or "log" (dev).
 * For heavy transactional volume, swap in PHPMailer/Symfony Mailer.
 */
declare(strict_types=1);

function send_mail(string $to, string $subject, string $text, ?string $replyTo = null): bool
{
    $cfg      = config('mail');
    $from     = $cfg['from'];
    $fromName = $cfg['from_name'];
    $subject  = trim(preg_replace('/[\r\n]+/', ' ', $subject));
    $replyTo  = $replyTo && filter_var($replyTo, FILTER_VALIDATE_EMAIL) ? $replyTo : null;

    if ($cfg['driver'] === 'log') {
        log_error("MAIL to=$to subject=$subject\n$text");
        return true;
    }

    $headers = [
        'From'         => mb_encode_mimeheader($fromName) . " <$from>",
        'MIME-Version' => '1.0',
        'Content-Type' => 'text/plain; charset=UTF-8',
        'Content-Transfer-Encoding' => '8bit',
        'X-Mailer'     => 'HT-Site',
    ];
    if ($replyTo) {
        $headers['Reply-To'] = $replyTo;
    }

    if ($cfg['driver'] === 'smtp' && $cfg['host']) {
        return smtp_send($cfg, $to, $subject, $text, $headers);
    }

    $h = '';
    foreach ($headers as $k => $v) {
        $h .= "$k: $v\r\n";
    }
    return @mail($to, mb_encode_mimeheader($subject), $text, $h, '-f' . $from);
}

function smtp_send(array $cfg, string $to, string $subject, string $body, array $headers): bool
{
    $host = ($cfg['secure'] === 'ssl' ? 'ssl://' : '') . $cfg['host'];
    $fp   = @stream_socket_client($host . ':' . $cfg['port'], $errno, $errstr, 15);
    if (!$fp) {
        log_error("SMTP connect failed: $errstr");
        return false;
    }
    stream_set_timeout($fp, 15);
    $read = static function () use ($fp): string {
        $data = '';
        while (($line = fgets($fp, 515)) !== false) {
            $data .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        return $data;
    };
    $cmd = static function (string $c, array $ok) use ($fp, $read): bool {
        fwrite($fp, $c . "\r\n");
        $r = $read();
        if (!in_array((int)substr($r, 0, 3), $ok, true)) {
            log_error('SMTP error on "' . (str_starts_with($c, 'AUTH') ? 'AUTH' : strtok($c, ' ')) . '": ' . trim($r));
            return false;
        }
        return true;
    };
    try {
        $read();
        $ehlo = 'EHLO ' . (parse_url(config('url') ?: 'http://localhost', PHP_URL_HOST) ?: 'localhost');
        if (!$cmd($ehlo, [250])) return false;
        if ($cfg['secure'] === 'tls') {
            if (!$cmd('STARTTLS', [220])) return false;
            if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT)) return false;
            if (!$cmd($ehlo, [250])) return false;
        }
        if ($cfg['user'] !== '') {
            if (!$cmd('AUTH LOGIN', [334])) return false;
            if (!$cmd(base64_encode($cfg['user']), [334])) return false;
            if (!$cmd(base64_encode($cfg['pass']), [235])) return false;
        }
        if (!$cmd('MAIL FROM:<' . $cfg['from'] . '>', [250])) return false;
        if (!$cmd('RCPT TO:<' . $to . '>', [250, 251])) return false;
        if (!$cmd('DATA', [354])) return false;
        $headers['To']      = $to;
        $headers['Subject'] = mb_encode_mimeheader($subject);
        $headers['Date']    = date('r');
        $msg = '';
        foreach ($headers as $k => $v) {
            $msg .= "$k: $v\r\n";
        }
        $body = preg_replace('/^\./m', '..', str_replace(["\r\n", "\n"], "\r\n", $body));
        if (!$cmd($msg . "\r\n" . $body . "\r\n.", [250])) return false;
        $cmd('QUIT', [221]);
        return true;
    } finally {
        fclose($fp);
    }
}
