<?php
/**
 * Declarative CRUD definitions. resource.php renders list/edit screens,
 * validates input and persists with prepared statements from these specs.
 *
 * Field types: text, textarea, number, decimal, select, checkbox, list (one per line → JSON),
 * image, url, email, icon, slug, relation
 */
declare(strict_types=1);

function admin_resources(): array
{
    $icons = array_combine(icon_names(), icon_names());
    $yes   = ['type' => 'checkbox', 'label' => 'Active (visible on site)', 'default' => 1];
    $order = ['type' => 'number', 'label' => 'Sort order', 'default' => 0, 'help' => 'Lower numbers appear first. You can also drag rows in the list.'];

    return [
        'services' => [
            'title' => 'Services & Process', 'singular' => 'Item', 'table' => 'services', 'icon' => 'workflow',
            'order' => 'section, sort_order, id', 'toggle' => 'is_active', 'sortable' => true,
            'group' => 'section',
            'columns' => ['title' => 'Title', 'section' => 'Section', 'meta' => 'Meta', 'is_active' => 'Active'],
            'filter' => ['section', ['core' => 'Home — core services', 'agent_feature' => 'AI Solutions — agent features', 'automation' => 'AI Solutions — automation cards', 'process' => 'AI Solutions — process steps']],
            'fields' => [
                'section'    => ['type' => 'select', 'label' => 'Section', 'required' => true, 'options' => ['core' => 'Home — core services', 'agent_feature' => 'AI Solutions — agent features', 'automation' => 'AI Solutions — automation cards', 'process' => 'AI Solutions — process steps']],
                'title'      => ['type' => 'text', 'label' => 'Title', 'required' => true, 'max' => 150],
                'slug'       => ['type' => 'slug', 'label' => 'Slug', 'from' => 'title', 'max' => 160],
                'icon'       => ['type' => 'icon', 'label' => 'Icon', 'options' => $icons],
                'excerpt'    => ['type' => 'textarea', 'label' => 'Description', 'rows' => 3, 'max' => 1000],
                'features'   => ['type' => 'list', 'label' => 'Features / deliverables (one per line)'],
                'meta'       => ['type' => 'text', 'label' => 'Meta', 'max' => 150, 'help' => 'Process steps: timeline (e.g. “1–2 weeks”). Core services: visual — network, pipeline or dashboard.'],
                'cta_label'  => ['type' => 'text', 'label' => 'CTA label', 'max' => 80],
                'cta_url'    => ['type' => 'text', 'label' => 'CTA link', 'max' => 255, 'help' => 'Internal path like /ai-solutions/#automation or a full URL.'],
                'sort_order' => $order,
                'is_active'  => $yes,
            ],
        ],
        'metrics' => [
            'title' => 'Metrics', 'singular' => 'Metric', 'table' => 'metrics', 'icon' => 'bar-chart',
            'order' => 'sort_order, id', 'toggle' => 'is_active', 'sortable' => true,
            'columns' => ['label' => 'Label', 'value' => 'Value', 'suffix' => 'Suffix', 'animate' => 'Animated', 'is_active' => 'Active'],
            'fields' => [
                'label'      => ['type' => 'text', 'label' => 'Label', 'required' => true, 'max' => 120],
                'prefix'     => ['type' => 'text', 'label' => 'Prefix', 'max' => 10, 'help' => 'e.g. “<” or “$”'],
                'value'      => ['type' => 'decimal', 'label' => 'Value', 'required' => true],
                'suffix'     => ['type' => 'text', 'label' => 'Suffix', 'max' => 10, 'help' => 'e.g. “+”, “%”, “x”'],
                'decimals'   => ['type' => 'number', 'label' => 'Decimal places', 'default' => 0, 'min' => 0, 'max' => 3],
                'animate'    => ['type' => 'checkbox', 'label' => 'Real, verified statistic — animate the counter', 'default' => 1],
                'sort_order' => $order,
                'is_active'  => $yes,
            ],
        ],
        'categories' => [
            'title' => 'Case Study Categories', 'singular' => 'Category', 'table' => 'case_study_categories', 'icon' => 'git-branch',
            'order' => 'sort_order, id', 'sortable' => true,
            'columns' => ['name' => 'Name', 'slug' => 'Slug', 'sort_order' => 'Order'],
            'unique' => ['slug'],
            'fields' => [
                'name'        => ['type' => 'text', 'label' => 'Name', 'required' => true, 'max' => 80],
                'slug'        => ['type' => 'slug', 'label' => 'Slug', 'from' => 'name', 'max' => 90],
                'description' => ['type' => 'text', 'label' => 'Description', 'max' => 255],
                'sort_order'  => $order,
            ],
        ],
        'team' => [
            'title' => 'Team', 'singular' => 'Team member', 'table' => 'team_members', 'icon' => 'users',
            'order' => 'sort_order, id', 'toggle' => 'is_active', 'sortable' => true,
            'columns' => ['photo' => 'Photo', 'name' => 'Name', 'role' => 'Position', 'is_active' => 'Active'],
            'fields' => [
                'name'       => ['type' => 'text', 'label' => 'Full name', 'required' => true, 'max' => 120],
                'role'       => ['type' => 'text', 'label' => 'Position', 'required' => true, 'max' => 150],
                'photo'      => ['type' => 'image', 'label' => 'Photo', 'help' => 'Portrait, ideally 960×1120. Leave empty for an elegant monogram.'],
                'bio'        => ['type' => 'textarea', 'label' => 'Short bio', 'rows' => 4, 'max' => 1500],
                'expertise'  => ['type' => 'list', 'label' => 'Expertise (one per line)'],
                'linkedin'   => ['type' => 'url', 'label' => 'LinkedIn URL', 'max' => 255],
                'sort_order' => $order,
                'is_active'  => $yes,
            ],
        ],
        'timeline' => [
            'title' => 'Company Timeline', 'singular' => 'Milestone', 'table' => 'timeline', 'icon' => 'clock',
            'order' => 'sort_order, year, id', 'toggle' => 'is_active', 'sortable' => true,
            'columns' => ['year' => 'Year', 'title' => 'Title', 'is_active' => 'Active'],
            'fields' => [
                'year'        => ['type' => 'text', 'label' => 'Year', 'required' => true, 'max' => 10],
                'title'       => ['type' => 'text', 'label' => 'Title', 'required' => true, 'max' => 150],
                'description' => ['type' => 'textarea', 'label' => 'Description', 'rows' => 3, 'max' => 1000],
                'sort_order'  => $order,
                'is_active'   => $yes,
            ],
        ],
        'technologies' => [
            'title' => 'Technologies', 'singular' => 'Technology', 'table' => 'technology_stack', 'icon' => 'cpu',
            'order' => 'category, sort_order, id', 'toggle' => 'is_active', 'sortable' => true,
            'group' => 'category',
            'columns' => ['name' => 'Name', 'category' => 'Category', 'show_in_hero' => 'Hero', 'show_in_ticker' => 'Ticker', 'is_active' => 'Active'],
            'filter' => ['category', ['AI & LLMs' => 'AI & LLMs', 'Automation' => 'Automation', 'Web & Cloud' => 'Web & Cloud', 'Data & Storage' => 'Data & Storage']],
            'fields' => [
                'name'           => ['type' => 'text', 'label' => 'Name', 'required' => true, 'max' => 80],
                'category'       => ['type' => 'select', 'label' => 'Category', 'required' => true, 'options' => ['AI & LLMs' => 'AI & LLMs', 'Automation' => 'Automation', 'Web & Cloud' => 'Web & Cloud', 'Data & Storage' => 'Data & Storage']],
                'show_in_hero'   => ['type' => 'checkbox', 'label' => 'Show as a home hero badge'],
                'show_in_ticker' => ['type' => 'checkbox', 'label' => 'Show in the home technology ticker'],
                'sort_order'     => $order,
                'is_active'      => $yes,
            ],
        ],
        'clients' => [
            'title' => 'Client Logos', 'singular' => 'Client', 'table' => 'clients', 'icon' => 'star',
            'order' => 'sort_order, id', 'toggle' => 'is_active', 'sortable' => true,
            'columns' => ['name' => 'Name', 'url' => 'URL', 'is_active' => 'Active'],
            'fields' => [
                'name'       => ['type' => 'text', 'label' => 'Company name', 'required' => true, 'max' => 120],
                'logo'       => ['type' => 'image', 'label' => 'Logo (optional)'],
                'url'        => ['type' => 'url', 'label' => 'Website', 'max' => 255],
                'sort_order' => $order,
                'is_active'  => $yes,
            ],
        ],
        'testimonials' => [
            'title' => 'Testimonials', 'singular' => 'Testimonial', 'table' => 'testimonials', 'icon' => 'quote',
            'order' => 'sort_order, id', 'toggle' => 'is_active', 'sortable' => true,
            'columns' => ['name' => 'Name', 'designation' => 'Designation', 'case_study_id' => 'Case study', 'is_active' => 'Active'],
            'fields' => [
                'quote'         => ['type' => 'textarea', 'label' => 'Quote', 'required' => true, 'rows' => 4, 'max' => 2000],
                'name'          => ['type' => 'text', 'label' => 'Client name', 'required' => true, 'max' => 120],
                'designation'   => ['type' => 'text', 'label' => 'Designation', 'max' => 150],
                'company'       => ['type' => 'text', 'label' => 'Company', 'max' => 150],
                'case_study_id' => ['type' => 'relation', 'label' => 'Linked case study', 'query' => 'SELECT id, title AS label FROM case_studies ORDER BY sort_order, id'],
                'sort_order'    => $order,
                'is_active'     => $yes,
            ],
        ],
        'faqs' => [
            'title' => 'FAQs', 'singular' => 'FAQ', 'table' => 'faqs', 'icon' => 'message',
            'order' => 'page, sort_order, id', 'toggle' => 'is_active', 'sortable' => true,
            'columns' => ['question' => 'Question', 'page' => 'Page', 'is_active' => 'Active'],
            'fields' => [
                'question'   => ['type' => 'text', 'label' => 'Question', 'required' => true, 'max' => 255],
                'answer'     => ['type' => 'textarea', 'label' => 'Answer', 'required' => true, 'rows' => 5, 'max' => 3000],
                'page'       => ['type' => 'select', 'label' => 'Show on', 'options' => ['contact' => 'Contact page']],
                'sort_order' => $order,
                'is_active'  => $yes,
            ],
        ],
        'social' => [
            'title' => 'Social Links', 'singular' => 'Social link', 'table' => 'social_links', 'icon' => 'globe',
            'order' => 'sort_order, id', 'toggle' => 'is_active', 'sortable' => true,
            'columns' => ['platform' => 'Platform', 'url' => 'URL', 'is_active' => 'Active'],
            'fields' => [
                'platform'   => ['type' => 'text', 'label' => 'Platform', 'required' => true, 'max' => 50],
                'url'        => ['type' => 'url', 'label' => 'URL', 'required' => true, 'max' => 255],
                'icon'       => ['type' => 'icon', 'label' => 'Icon', 'required' => true, 'options' => array_intersect_key($icons, array_flip(['linkedin', 'x-social', 'github', 'instagram', 'youtube', 'facebook', 'dribbble', 'globe', 'mail']))],
                'sort_order' => $order,
                'is_active'  => $yes,
            ],
        ],
        'seo' => [
            'title' => 'SEO Settings', 'singular' => 'Page SEO', 'table' => 'seo_meta', 'icon' => 'search',
            'order' => 'page_key',
            'columns' => ['page_key' => 'Page', 'title' => 'Title', 'noindex' => 'Noindex'],
            'unique' => ['page_key'],
            'intro' => 'Override the title, description and social image per page. Case studies have their own SEO fields in the case study editor.',
            'fields' => [
                'page_key'    => ['type' => 'select', 'label' => 'Page', 'required' => true, 'options' => ['home' => 'Home', 'ai-solutions' => 'AI Solutions', 'case-studies' => 'Case Studies', 'about' => 'About', 'contact' => 'Get Started / Contact', 'privacy-policy' => 'Privacy Policy', 'terms' => 'Terms']],
                'title'       => ['type' => 'text', 'label' => 'SEO title', 'max' => 200, 'help' => 'Aim for 50–60 characters. The site name is appended automatically.', 'counter' => 60],
                'description' => ['type' => 'textarea', 'label' => 'Meta description', 'rows' => 3, 'max' => 300, 'help' => 'Aim for 140–160 characters.', 'counter' => 160],
                'og_image'    => ['type' => 'image', 'label' => 'Social share image (1200×630)'],
                'noindex'     => ['type' => 'checkbox', 'label' => 'Hide from search engines (noindex)'],
            ],
        ],
    ];
}
