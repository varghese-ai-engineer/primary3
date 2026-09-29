<?php
/**
 * Default content. Everything here is editable in /admin afterwards.
 * Uses prepared inserts (DB::insert) so it is portable across MySQL and SQLite.
 */
declare(strict_types=1);

function ht_seed(): void
{
    $j = static fn(array $a) => json_encode($a, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    /* ---------------- Settings ---------------- */
    $settings = [
        // group, key, label, type, value
        ['general', 'site_name', 'Site name', 'text', 'Primary Infotech'],
        ['general', 'logo_text', 'Logo wordmark', 'text', 'Primary'],
        ['general', 'logo_suffix', 'Logo second word', 'text', 'Infotech'],
        ['general', 'tagline', 'Tagline', 'text', 'Premium AI & Automation Agency'],
        ['general', 'company_description', 'Company description (footer & SEO fallback)', 'textarea', 'We build intelligent systems that automate workflows, deploy AI agents, and create custom web applications for forward-thinking businesses worldwide.'],
        ['general', 'footer_tagline', 'Footer tagline', 'text', 'Design with intent. Engineered to last.'],
        ['general', 'founding_year', 'Founding year', 'text', '2014'],

        ['contact', 'contact_email', 'Public email', 'email', 'contact@primaryinfotech.com'],
        ['contact', 'contact_phone', 'Public phone', 'text', '+91-866-776-1197'],
        ['contact', 'availability', 'Availability line', 'text', 'Our agents work for you 24/7 × 365'],
        ['contact', 'response_time', 'Response time line', 'text', 'Typical response within 24 hours'],
        ['contact', 'address_locality', 'City', 'text', 'Coimbatore'],
        ['contact', 'address_region', 'State / region', 'text', 'Tamil Nadu'],
        ['contact', 'address_country', 'Country code', 'text', 'IN'],
        ['contact', 'notify_email', 'Send enquiry notifications to', 'email', 'contact@primaryinfotech.com'],
        ['contact', 'budget_options', 'Budget options (one per line)', 'list', "$5,000 – $10,000\n$10,000 – $25,000\n$25,000 – $50,000\n$50,000+\nNot sure yet"],

        ['home', 'hero_eyebrow', 'Hero eyebrow', 'text', 'Built by Engineers, Driven by Impact'],
        ['home', 'hero_title', 'Hero title (new line = new masked line, *text* = gradient)', 'textarea', "Intelligent Systems.\n*Built to Work.*"],
        ['home', 'hero_description', 'Hero description', 'textarea', 'We build intelligent systems that automate workflows, deploy AI agents, and create custom web applications for forward-thinking businesses worldwide.'],
        ['home', 'hero_cta_primary', 'Hero primary CTA label', 'text', 'Start Your AI Journey'],
        ['home', 'hero_cta_primary_url', 'Hero primary CTA link', 'text', '/contact/'],
        ['home', 'hero_cta_secondary', 'Hero secondary CTA label', 'text', 'See Our Work'],
        ['home', 'hero_cta_secondary_url', 'Hero secondary CTA link', 'text', '/case-studies/'],
        ['home', 'clients_heading', 'Client strip label', 'text', 'Trusted by teams building what comes next'],
        ['home', 'services_eyebrow', 'Services eyebrow', 'text', 'What we build'],
        ['home', 'services_heading', 'Services heading', 'text', 'Solutions That *Think & Act*'],
        ['home', 'services_description', 'Services description', 'textarea', "We don't just write code — we engineer intelligent systems that learn, adapt, and automate."],
        ['home', 'metrics_heading', 'Metrics heading', 'text', 'Engineering outcomes, *measured*'],
        ['home', 'work_heading', 'Featured work heading', 'text', 'Selected *work*'],
        ['home', 'cta_heading', 'Closing CTA heading', 'text', "Let's Build Your\n*AI-Powered Future*"],
        ['home', 'cta_description', 'Closing CTA description', 'textarea', 'Tell us where your team loses time. We will show you what an intelligent system could take off its plate.'],
        ['home', 'cta_primary', 'Closing CTA primary label', 'text', 'Start a Conversation'],
        ['home', 'cta_secondary', 'Closing CTA secondary label', 'text', 'Explore Solutions'],

        ['ai', 'ai_hero_eyebrow', 'Hero eyebrow', 'text', 'AI Solutions'],
        ['ai', 'ai_hero_title', 'Hero title', 'textarea', "Intelligence That\n*Works For You*"],
        ['ai', 'ai_hero_description', 'Hero description', 'textarea', 'From autonomous AI agents to end-to-end workflow automation — we build intelligent systems that transform how your business operates.'],
        ['ai', 'ai_workflow_heading', 'Agentic workflows heading', 'text', 'Agentic *Workflows*'],
        ['ai', 'ai_workflow_description', 'Agentic workflows description', 'textarea', 'Autonomous AI agents that collaborate and execute complex multi-step tasks end-to-end — with a human in the loop wherever it matters.'],
        ['ai', 'ai_automation_heading', 'Automation heading', 'text', 'Automate *Everything*'],
        ['ai', 'ai_automation_description', 'Automation description', 'textarea', 'Reliable, observable automations that remove repetitive work across documents, inboxes, APIs and data.'],
        ['ai', 'ai_stack_heading', 'Tech stack heading', 'text', 'Built With the *Best Tools*'],
        ['ai', 'ai_process_heading', 'Process heading', 'text', 'How We *Work*'],
        ['ai', 'ai_cta_heading', 'CTA heading', 'text', "Ready to Build Your\n*AI Solution?*"],
        ['ai', 'ai_cta_description', 'CTA description', 'textarea', "Share a few details about your project and we'll come back with a clear, practical plan."],
        ['ai', 'ai_cta_label', 'CTA label', 'text', 'Get in Touch'],

        ['cases', 'cs_hero_eyebrow', 'Hero eyebrow', 'text', 'Case Studies'],
        ['cases', 'cs_hero_title', 'Hero title', 'textarea', "Proven Results,\n*Real Impact*"],
        ['cases', 'cs_hero_description', 'Hero description', 'textarea', "See how we've helped businesses automate workflows, deploy AI agents, and build custom applications."],
        ['cases', 'cs_cta_heading', 'Detail page CTA heading', 'text', 'Want Similar *Results?*'],
        ['cases', 'cs_cta_description', 'Detail page CTA description', 'textarea', "Let's talk about what an intelligent system could do for your team."],
        ['cases', 'cs_cta_label', 'Detail page CTA label', 'text', 'Start Your Project'],

        ['about', 'about_hero_eyebrow', 'Hero eyebrow', 'text', 'About Us'],
        ['about', 'about_hero_title', 'Hero title', 'textarea', "Built by Engineers,\n*Driven by Impact*"],
        ['about', 'about_hero_description', 'Hero description', 'textarea', 'From a small WordPress studio to a global AI & Automation partner — we solve real business problems with technology.'],
        ['about', 'about_story_heading', 'Story heading', 'text', 'A decade of *shipping*'],
        ['about', 'about_story', 'Story (blank line = new paragraph)', 'textarea', "We started in 2014 as a small WordPress studio in Bangalore, building websites for local businesses. Every year since, the problems we were trusted with got harder — custom web applications, mobile products, cloud infrastructure and, eventually, enterprise systems for the semiconductor industry.\n\nIn 2024 we made a deliberate pivot. Large language models had crossed the line from impressive demos to dependable tools, and our engineering background meant we could put them into production properly: observable, secure and measured against business outcomes.\n\nToday we design AI agents, automation pipelines and AI-native web applications for companies around the world — built by engineers, judged by impact."],
        ['about', 'about_team_heading', 'Team heading', 'text', 'The people *behind the systems*'],
        ['about', 'about_timeline_heading', 'Journey heading', 'text', 'Our *journey*'],
        ['about', 'about_cta_heading', 'CTA heading', 'text', "Let's talk about\n*what's next*"],
        ['about', 'about_cta_description', 'CTA description', 'textarea', "Let's talk about how we can help automate and scale your business."],

        ['contactpage', 'contact_hero_eyebrow', 'Hero eyebrow', 'text', 'Get Started'],
        ['contactpage', 'contact_hero_title', 'Hero title', 'textarea', "Let's Build Something\n*Intelligent*"],
        ['contactpage', 'contact_hero_description', 'Hero description', 'textarea', "Ready to automate your workflows or deploy AI agents? Reach out — we'd love to hear about your project."],
        ['contactpage', 'contact_success_title', 'Success title', 'text', 'Message received.'],
        ['contactpage', 'contact_success_text', 'Success text', 'textarea', "Thanks for reaching out. A member of our engineering team will reply within one business day."],

        ['legal', 'privacy_content', 'Privacy policy', 'textarea', "Last updated: September 2026\n\nWe collect only the information you choose to share with us through our contact form — your name, email address, phone number, company and project details — and use it solely to respond to your enquiry.\n\nWe do not sell or rent personal data. Enquiries are stored securely and access is limited to authorised team members.\n\nWe use no third-party advertising trackers. Server logs may record IP addresses for security purposes and are rotated regularly.\n\nTo request access to or deletion of your data, email us at contact@primaryinfotech.com."],
        ['legal', 'terms_content', 'Terms of service', 'textarea', "Last updated: September 2026\n\nThe content on this website is provided for general information about our services. It does not constitute a binding offer.\n\nAll project engagements are governed by a separate written agreement that sets out scope, deliverables, fees and intellectual-property terms.\n\nCase-study results describe outcomes for specific clients and are not a guarantee of future results.\n\nQuestions about these terms can be sent to contact@primaryinfotech.com."],

        ['seo', 'default_og_image', 'Default social share image', 'image', 'assets/img/og-default.png'],
        ['seo', 'twitter_handle', 'X / Twitter handle (e.g. @primaryinfotech)', 'text', ''],
    ];
    foreach ($settings as $i => [$g, $k, $l, $t, $v]) {
        DB::insert('site_settings', ['group_name' => $g, 'setting_key' => $k, 'label' => $l, 'type' => $t, 'value' => $v, 'sort_order' => $i]);
    }

    /* ---------------- Services ---------------- */
    $services = [
        ['core', 'AI Agents', 'bot', 'Autonomous AI agents that handle complex tasks from data analysis to customer interactions.', ['Custom LLM-powered agents', 'Multi-agent orchestration', 'Tool-use & function calling', 'Memory & context management'], 'network'],
        ['core', 'Workflow Automation', 'workflow', 'End-to-end automation that eliminates repetitive work, reduces errors, and scales operations.', ['n8n & Zapier integrations', 'API orchestration', 'Document processing', 'Scheduled automation'], 'pipeline'],
        ['core', 'Custom Web Applications', 'layout', 'High-performance web applications with AI integrated into the experience.', ['AI dashboards', 'Real-time data visualization', 'Enterprise applications', 'Secure architecture'], 'dashboard'],

        ['agent_feature', 'Multi-Agent Orchestration', 'network', 'A central orchestrator decomposes goals and coordinates specialised agents — research, action and validation — so complex work runs end-to-end.', ['Planner / executor patterns', 'Parallel task routing', 'Graceful failure recovery'], ''],
        ['agent_feature', 'Tool-Use & Function Calling', 'wrench', 'Agents invoke your APIs, query databases and execute code safely inside guard-railed sandboxes.', ['Typed tool schemas', 'Scoped credentials', 'Full audit trail'], ''],
        ['agent_feature', 'Memory & Context', 'database', 'Short- and long-term memory with retrieval-augmented generation, so agents retain knowledge and improve over time.', ['Vector search (RAG)', 'Session memory', 'Knowledge-base sync'], ''],
        ['agent_feature', 'Human-in-the-Loop', 'user-check', 'Critical decisions route to people for approval, with clear context and one-click overrides.', ['Approval queues', 'Confidence thresholds', 'Escalation rules'], ''],

        ['automation', 'Document Processing', 'file-text', 'Extract, classify and route data from PDFs, invoices and forms with OCR plus LLM understanding.', [], ''],
        ['automation', 'Email & Communication', 'mail', 'Auto-triage inboxes, draft context-aware replies and schedule follow-ups automatically.', [], ''],
        ['automation', 'Scheduled Workflows', 'calendar', 'Time-based triggers for reports, reconciliations, alerts and recurring operations.', [], ''],
        ['automation', 'API Orchestration', 'plug', 'Connect your SaaS tools with retries, error handling and observability built in.', [], ''],
        ['automation', 'Data Pipelines', 'git-branch', 'ETL workflows that extract, transform and load data into the systems that need it.', [], ''],
        ['automation', 'Compliance & Monitoring', 'shield-check', 'Automated checks, alerting and immutable audit logs for regulated processes.', [], ''],

        ['process', 'Discovery & Audit', 'search', 'We map your workflows, interview the people doing the work and pinpoint where AI and automation create measurable value.', ['Process Map', 'Opportunity Report', 'ROI Estimate'], '1–2 weeks'],
        ['process', 'Architect & Design', 'compass', 'We design the solution architecture, choose the right models and tools, and validate the riskiest assumptions with a prototype.', ['Technical Blueprint', 'System Design', 'Prototype'], '1–2 weeks'],
        ['process', 'Build & Deploy', 'rocket', 'We build in 2-week sprint cycles with continuous demos, automated tests and staged rollouts to production.', ['Working Software', 'Staging Environment', 'Documentation'], '4–12 weeks'],
        ['process', 'Optimize & Support', 'gauge', 'We monitor performance, tune prompts and models against real data, and provide ongoing support as you scale.', ['Performance Dashboard', '24/7 Monitoring', 'Monthly Reviews'], 'Ongoing'],
    ];
    $order = [];
    foreach ($services as [$section, $title, $icon, $excerpt, $features, $meta]) {
        $order[$section] = ($order[$section] ?? 0) + 1;
        DB::insert('services', [
            'section'    => $section,
            'title'      => $title,
            'slug'       => slugify($title),
            'icon'       => $icon,
            'excerpt'    => $excerpt,
            'features'   => $j($features),
            'meta'       => $section === 'core' ? $meta : ($meta ?: null),
            'cta_label'  => $section === 'core' ? 'Learn More' : null,
            'cta_url'    => $section === 'core' ? ['network' => '/ai-solutions/#agentic-workflows', 'pipeline' => '/ai-solutions/#automation', 'dashboard' => '/case-studies/?category=web-apps'][$meta] : null,
            'sort_order' => $order[$section],
            'is_active'  => 1,
        ]);
    }

    /* ---------------- Metrics ---------------- */
    foreach ([['Projects Delivered', 50, '', '+', 0], ['Global Clients', 30, '', '+', 0], ['Uptime Guaranteed', 99.9, '', '%', 1], ['Faster Deployment', 10, '', 'x', 0]] as $i => [$l, $v, $p, $s, $d]) {
        DB::insert('metrics', ['label' => $l, 'value' => $v, 'prefix' => $p, 'suffix' => $s, 'decimals' => $d, 'animate' => 1, 'sort_order' => $i + 1, 'is_active' => 1]);
    }

    /* ---------------- Clients ---------------- */
    foreach (['TechCorp Global', 'Nexus AI', 'CloudScale', 'DataFlow Inc', 'InnovateTech', 'QuantumEdge', 'SynapseIO', 'AutomateHQ'] as $i => $c) {
        DB::insert('clients', ['name' => $c, 'sort_order' => $i + 1, 'is_active' => 1]);
    }

    /* ---------------- Technology stack ---------------- */
    $tech = [
        'AI & LLMs'      => ['OpenAI / GPT', 'Claude / Anthropic', 'LangChain', 'LangGraph', 'CrewAI', 'Hugging Face'],
        'Automation'     => ['n8n', 'Zapier', 'Make', 'Temporal', 'Apache Airflow', 'Celery'],
        'Web & Cloud'    => ['Next.js', 'React', 'TypeScript', 'Python', 'Vercel', 'AWS'],
        'Data & Storage' => ['PostgreSQL', 'Redis', 'Pinecone', 'Supabase', 'MongoDB', 'Prisma'],
    ];
    $hero   = ['Next.js', 'LangChain', 'OpenAI / GPT', 'n8n', 'Python', 'TypeScript'];
    $ticker = ['OpenAI / GPT', 'Claude / Anthropic', 'LangChain', 'n8n', 'Python', 'Next.js', 'PostgreSQL', 'AWS', 'Pinecone', 'Temporal'];
    $i = 0;
    foreach ($tech as $cat => $names) {
        foreach ($names as $name) {
            DB::insert('technology_stack', [
                'name' => $name, 'category' => $cat,
                'show_in_hero' => in_array($name, $hero, true) ? 1 : 0,
                'show_in_ticker' => in_array($name, $ticker, true) ? 1 : 0,
                'sort_order' => ++$i, 'is_active' => 1,
            ]);
        }
    }

    /* ---------------- Categories ---------------- */
    $cat = [];
    foreach ([['AI Agents', 'ai-agents'], ['Automation', 'automation'], ['Web Apps', 'web-apps']] as $i => [$n, $s]) {
        $cat[$s] = DB::insert('case_study_categories', ['name' => $n, 'slug' => $s, 'sort_order' => $i + 1]);
    }

    /* ---------------- Case studies ---------------- */
    $cases = [
        [
            'cat' => 'ai-agents', 'slug' => 'ai-customer-support-agent', 'title' => 'AI Customer Support Agent',
            'tag' => 'AI Agents / LLM', 'industry' => 'Global SaaS Company', 'visual' => 'chat', 'featured' => 1,
            'excerpt' => 'Deployed an autonomous AI agent that handles 80% of customer inquiries without human intervention, reducing response time from hours to seconds.',
            'challenge' => "The client was receiving more than 5,000 support tickets every month. Response times stretched into hours, customers were churning, and the support team was stuck answering the same questions again and again.\n\nOver 60% of inquiries were routine — billing questions, password resets and onboarding help — yet each one still waited in the same queue as genuinely complex issues.",
            'solution' => "We built a multi-model AI agent powered by GPT-4 and grounded in the client's own knowledge. A retrieval-augmented generation (RAG) layer pulls the most relevant passages from help-centre articles, historical tickets and product documentation before every answer.\n\nThe agent resolves routine requests end-to-end through tool calls into billing and account systems. Anything complex, sensitive or low-confidence is escalated to a human specialist with a structured hand-off summary, so customers never repeat themselves.",
            'technology' => 'A retrieval pipeline indexes documentation and resolved tickets into a vector store. The agent layer uses function calling for account actions, with guard-rails and confidence thresholds deciding when to escalate.',
            'tags' => ['GPT-4', 'RAG', 'LangChain', 'Pinecone', 'Python', 'Zendesk API'],
            'architecture' => "Ticket intake | Email, chat and in-app messages normalised into one queue\nRetrieval | Relevant help articles and past tickets fetched from the vector store\nReasoning agent | GPT-4 drafts an answer and decides on tool calls\nGuard-rails | Confidence and policy checks gate every response\nHuman hand-off | Low-confidence cases escalate with a full summary",
            'results' => "Within the first month, the agent was autonomously resolving 80% of incoming tickets. Average first response time dropped from 4.2 hours to under 3 seconds, and customer satisfaction rose by 32%.\n\nThe support team now spends its time on complex, high-value conversations instead of password resets.",
            'metrics' => [['Response Time', '< 3s'], ['Resolution Rate', '80%'], ['Cost Reduction', '60%']],
            'testimonial' => ["Primary Infotech's AI agent completely transformed our support operations. We went from drowning in tickets to proactively delighting customers.", 'Sarah K.', 'VP of Customer Success', 'Global SaaS Company'],
        ],
        [
            'cat' => 'automation', 'slug' => 'invoice-automation-pipeline', 'title' => 'Invoice Processing Automation',
            'tag' => 'Automation / Document AI', 'industry' => 'Manufacturing Enterprise', 'visual' => 'pipeline', 'featured' => 1,
            'excerpt' => 'Built an end-to-end pipeline that extracts data from PDF invoices, validates it against purchase orders, and routes it for approval automatically.',
            'challenge' => "The finance team processed more than 2,000 invoices every month from 15 vendors — each using a different format. Manual data entry consumed over 200 hours a month and carried a 4.5% error rate.\n\nErrors caused payment delays, strained vendor relationships and made month-end close a dreaded, all-hands exercise.",
            'solution' => "We designed a document-AI pipeline that combines OCR with LLM-based extraction, so any invoice layout is converted into a standard, structured record.\n\nEach invoice is validated against purchase orders in the ERP. Matches are approved and routed automatically; discrepancies are flagged with a clear explanation for a human to review. The whole workflow is orchestrated in n8n with Supabase as the system of record.",
            'technology' => 'n8n orchestrates ingestion from email and SFTP, OCR and LLM extraction, three-way matching against ERP purchase orders, and approval routing. Supabase stores every document, extraction and decision for auditability.',
            'tags' => ['OCR', 'GPT-4o', 'n8n', 'Supabase', 'PostgreSQL', 'ERP Integration'],
            'architecture' => '',
            'results' => "Processing time per invoice fell from 12 minutes to 45 seconds. The error rate dropped from 4.5% to 0.5%, and more than 200 hours a month were redirected to strategic finance work.\n\nMonth-end close is now a routine review instead of a scramble.",
            'metrics' => [['Faster Processing', '90%'], ['Accuracy', '99.5%'], ['Hours Saved / Month', '200+']],
            'testimonial' => ['We used to dread month-end invoice processing. Now it runs itself.', 'Michael R.', 'CFO', 'Manufacturing Enterprise'],
        ],
        [
            'cat' => 'web-apps', 'slug' => 'analytics-dashboard', 'title' => 'AI-Powered Analytics Dashboard',
            'tag' => 'Web App / Data Visualization', 'industry' => 'FinTech Startup', 'visual' => 'dashboard', 'featured' => 1,
            'excerpt' => 'Created a real-time dashboard with natural-language querying — ask questions in plain English and get instant visual answers.',
            'challenge' => "The data team had become a bottleneck for every business decision. Non-technical stakeholders couldn't query data themselves, and the backlog of ad-hoc report requests had grown past 40.\n\nLeaders were making decisions on week-old spreadsheets while analysts spent their days writing one-off SQL.",
            'solution' => "We built a Next.js dashboard with an AI-powered natural-language query interface. Anyone can ask a question in plain English — \"Show me revenue by region for Q3\" — and get an instant, interactive visualisation.\n\nBehind the scenes, LangChain translates each question into validated, read-only SQL against a PostgreSQL warehouse that unifies more than twelve data sources.",
            'technology' => 'Next.js front end with streaming responses, a LangChain text-to-SQL agent with schema-aware prompting and query validation, and a PostgreSQL warehouse fed by scheduled ELT jobs.',
            'tags' => ['Next.js', 'LangChain', 'PostgreSQL', 'TypeScript', 'OpenAI', 'Airflow'],
            'architecture' => "Natural-language question | User asks in plain English from the dashboard\nSchema-aware agent | LangChain maps intent to tables, metrics and filters\nSQL validation | Queries are checked, read-only and cost-limited\nWarehouse | PostgreSQL unifies 12+ data sources via scheduled ELT\nVisual answer | Results stream back as the most suitable chart",
            'results' => "95% of intended users adopted the dashboard within the first month. The ad-hoc report backlog was eliminated entirely, and decisions that used to wait days now happen in the meeting where the question is asked.",
            'metrics' => [['Query Speed', '< 2s'], ['User Adoption', '95%'], ['Data Sources', '12+']],
            'testimonial' => ["Our entire leadership team now self-serves their data. It's like having a data analyst available 24/7 who never sleeps.", 'Priya M.', 'CEO', 'FinTech Startup'],
        ],
    ];
    foreach ($cases as $i => $c) {
        $id = DB::insert('case_studies', [
            'category_id' => $cat[$c['cat']], 'title' => $c['title'], 'slug' => $c['slug'], 'tag_label' => $c['tag'],
            'industry' => $c['industry'], 'excerpt' => $c['excerpt'], 'visual' => $c['visual'],
            'challenge' => $c['challenge'], 'solution' => $c['solution'], 'technology' => $c['technology'],
            'tech_tags' => $j($c['tags']), 'architecture' => $c['architecture'], 'results' => $c['results'],
            'seo_title' => $c['title'] . ' — Case Study', 'seo_description' => $c['excerpt'],
            'is_published' => 1, 'is_featured' => $c['featured'], 'sort_order' => $i + 1,
            'published_at' => date('Y-m-d H:i:s', strtotime('-' . (30 * (3 - $i)) . ' days')),
        ]);
        foreach ($c['metrics'] as $k => [$label, $value]) {
            DB::insert('case_study_metrics', ['case_study_id' => $id, 'label' => $label, 'value' => $value, 'sort_order' => $k + 1]);
        }
        [$q, $n, $d, $co] = $c['testimonial'];
        DB::insert('testimonials', ['case_study_id' => $id, 'quote' => $q, 'name' => $n, 'designation' => $d, 'company' => $co, 'sort_order' => $i + 1, 'is_active' => 1]);
    }

    /* ---------------- Team ---------------- */
    DB::insert('team_members', [
        'name' => 'Ashok Chandrasekaran', 'role' => 'Founder & Head of Technology',
        'bio' => 'Over a decade of experience architecting technology solutions for major international brands across multiple continents, with a background at Nokia Siemens Networks.',
        'expertise' => $j(['Software Architecture & Security', 'Enterprise & E-Commerce Solutions', 'AI Systems Design']),
        'linkedin' => 'https://www.linkedin.com/', 'sort_order' => 1, 'is_active' => 1,
    ]);
    DB::insert('team_members', [
        'name' => 'Premkumar Arumugam', 'role' => 'Co-Founder & CMO',
        'bio' => 'Enterprise sales strategist with deep experience building C-suite relationships and driving pipeline growth across diverse markets. Published research author.',
        'expertise' => $j(['B2B Growth & LinkedIn Strategy', 'Enterprise Software Sales', 'Go-to-Market Strategy']),
        'linkedin' => 'https://www.linkedin.com/', 'sort_order' => 2, 'is_active' => 1,
    ]);

    /* ---------------- Timeline ---------------- */
    $tl = [
        ['2014', 'Founded', 'Started as a WordPress development studio in Bangalore.'],
        ['2015', 'Headquarters Relocated', 'Moved our headquarters to Coimbatore to build a long-term engineering base.'],
        ['2016', 'SEZ Office & Custom Apps', 'Expanded to 16 people in an SEZ office and shifted focus to custom web applications.'],
        ['2017', 'Mobile Development', 'Added native Android and iOS development services.'],
        ['2018', 'AWS Partnership', 'Became an Amazon Web Services partner for cloud solutions.'],
        ['2019', 'Enterprise Segment', 'Delivered a USB Packet Analyzer for the semiconductor industry.'],
        ['2020', 'Enterprise Expansion', 'Scaled delivery for enterprise and mid-market customers.'],
        ['2024', 'AI & Automation Pivot', 'Repositioned as a premium AI & Automation solutions partner.'],
    ];
    foreach ($tl as $i => [$y, $t, $d]) {
        DB::insert('timeline', ['year' => $y, 'title' => $t, 'description' => $d, 'sort_order' => $i + 1, 'is_active' => 1]);
    }

    /* ---------------- Social ---------------- */
    foreach ([['LinkedIn', 'https://www.linkedin.com/', 'linkedin'], ['X', 'https://x.com/', 'x-social'], ['GitHub', 'https://github.com/', 'github'], ['Instagram', 'https://www.instagram.com/', 'instagram']] as $i => [$p, $u, $ic]) {
        DB::insert('social_links', ['platform' => $p, 'url' => $u, 'icon' => $ic, 'sort_order' => $i + 1, 'is_active' => 1]);
    }

    /* ---------------- FAQs ---------------- */
    $faq = [
        ['How long does a typical AI project take?', 'Most engagements move from discovery to a production pilot in 6–10 weeks. We work in 2-week sprints with a live demo at the end of each one, so you see progress continuously.'],
        ['Do you work with our existing tools and data?', 'Yes. We integrate with your CRM, ERP, help desk, databases and SaaS tools through their APIs, and we keep your data inside your infrastructure wherever required.'],
        ['How do you keep AI outputs accurate and safe?', 'Every system ships with grounding (RAG), validation layers, confidence thresholds and human-in-the-loop approval for critical decisions — plus monitoring so quality is measured, not assumed.'],
        ['What does an engagement cost?', 'Projects typically start from $5,000 for focused automations. After a short discovery call we provide a fixed-scope proposal with a clear ROI estimate.'],
        ['Do you provide support after launch?', 'Yes. We offer ongoing optimisation and 24/7 monitoring, with monthly reviews to tune models, prompts and workflows against real-world data.'],
    ];
    foreach ($faq as $i => [$q, $a]) {
        DB::insert('faqs', ['question' => $q, 'answer' => $a, 'page' => 'contact', 'sort_order' => $i + 1, 'is_active' => 1]);
    }

    /* ---------------- SEO ---------------- */
    $seo = [
        ['home', 'AI Agents, Workflow Automation & Custom Web Apps', 'Primary Infotech builds AI agents, workflow automation and AI-native web applications for forward-thinking businesses worldwide.'],
        ['ai-solutions', 'AI Solutions — Agentic Workflows & Automation', 'Autonomous AI agents, end-to-end workflow automation and a proven four-stage delivery process. See how we build intelligent systems.'],
        ['case-studies', 'Case Studies — Proven Results, Real Impact', 'How we helped businesses automate workflows, deploy AI agents and build custom AI-powered applications — with measurable results.'],
        ['about', 'About Us — Built by Engineers, Driven by Impact', 'From a WordPress studio in 2014 to a global AI & automation partner. Meet the team and explore our journey.'],
        ['contact', "Get Started — Let's Build Something Intelligent", 'Tell us about your project. Our engineers respond within 24 hours with a practical plan for AI agents, automation or custom web apps.'],
        ['privacy-policy', 'Privacy Policy', 'How Primary Infotech collects, uses and protects your information.'],
        ['terms', 'Terms of Service', 'Terms that govern the use of the Primary Infotech website.'],
    ];
    foreach ($seo as [$k, $t, $d]) {
        DB::insert('seo_meta', ['page_key' => $k, 'title' => $t, 'description' => $d, 'noindex' => 0]);
    }
}
