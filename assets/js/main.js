/*!
 * Primary Info Tech— interaction & motion layer
 * Vanilla JS, no dependencies. Animates transform/opacity/clip-path only,
 * uses IntersectionObserver + requestAnimationFrame, and respects
 * prefers-reduced-motion and coarse (touch) pointers.
 */
(() => {
    'use strict';

    const doc = document;
    const root = doc.documentElement;
    const $ = (s, c = doc) => c.querySelector(s);
    const $$ = (s, c = doc) => Array.from(c.querySelectorAll(s));
    const mq = (q) => window.matchMedia(q);

    const RM = mq('(prefers-reduced-motion: reduce)').matches;
    const FINE = mq('(hover: hover) and (pointer: fine)').matches;
    const isDesktop = () => window.innerWidth > 1024;
    const clamp = (v, a, b) => Math.min(b, Math.max(a, v));
    const easeOutExpo = (t) => (t === 1 ? 1 : 1 - Math.pow(2, -10 * t));

    /* ------------------------------------------------------------------ *
     * Scroll loop — one rAF-throttled listener drives header, progress,
     * parallax and the timeline fill.
     * ------------------------------------------------------------------ */
    const header = $('[data-header]');
    const progress = $('.scroll-progress');
    const parallaxEls = RM ? [] : $$('[data-parallax]');
    const timelineWrap = $('[data-timeline]');
    let ticking = false;

    function onScroll() {
        const y = window.scrollY;
        const max = doc.documentElement.scrollHeight - window.innerHeight;

        if (header) header.classList.toggle('is-scrolled', y > 24);
        if (progress) progress.style.setProperty('--p', max > 0 ? (y / max).toFixed(4) : 0);

        if (parallaxEls.length && isDesktop()) {
            parallaxEls.forEach((el) => {
                const f = parseFloat(el.dataset.parallax) || 0;
                el.style.translate = `0 ${(y * f).toFixed(1)}px`;
            });
        }

        if (timelineWrap) {
            const r = timelineWrap.getBoundingClientRect();
            const vh = window.innerHeight;
            const p = clamp((vh * 0.6 - r.top) / r.height, 0, 1);
            timelineWrap.style.setProperty('--tp', p.toFixed(3));
        }
        ticking = false;
    }
    window.addEventListener('scroll', () => {
        if (!ticking) { ticking = true; requestAnimationFrame(onScroll); }
    }, { passive: true });
    window.addEventListener('resize', () => requestAnimationFrame(onScroll), { passive: true });
    onScroll();

    /* ------------------------------------------------------------------ *
     * Mobile navigation drawer
     * ------------------------------------------------------------------ */
    const toggle = $('[data-nav-toggle]');
    const drawer = $('[data-mobile-nav]');
    if (toggle && drawer) {
        const links = $$('a', drawer);
        const setOpen = (open) => {
            doc.body.classList.toggle('nav-open', open);
            toggle.setAttribute('aria-expanded', String(open));
            toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
            drawer.setAttribute('aria-hidden', String(!open));
            links.forEach((a) => a.setAttribute('tabindex', open ? '0' : '-1'));
            if (open) setTimeout(() => links[0] && links[0].focus({ preventScroll: true }), 350);
        };
        toggle.addEventListener('click', () => setOpen(!doc.body.classList.contains('nav-open')));
        doc.addEventListener('keydown', (e) => {
            if (!doc.body.classList.contains('nav-open')) return;
            if (e.key === 'Escape') { setOpen(false); toggle.focus(); }
            if (e.key === 'Tab') {                         // simple focus trap
                const f = [toggle, ...links];
                const i = f.indexOf(doc.activeElement);
                if (e.shiftKey && i <= 0) { e.preventDefault(); f[f.length - 1].focus(); }
                else if (!e.shiftKey && i === f.length - 1) { e.preventDefault(); f[0].focus(); }
            }
        });
        links.forEach((a) => a.addEventListener('click', () => setOpen(false)));
        mq('(min-width: 1025px)').addEventListener('change', (e) => e.matches && setOpen(false));
    }

    /* ------------------------------------------------------------------ *
     * Scroll reveals, counters and one-shot "in view" hooks
     * ------------------------------------------------------------------ */
    const revealSel = '[data-reveal], [data-lines], [data-img-reveal], .metric, [data-cta], .timeline__item, .dash';
    function animateCount(el) {
        const target = parseFloat(el.dataset.count);
        const dec = parseInt(el.dataset.decimals || '0', 10);
        if (isNaN(target)) return;
        if (RM) { el.textContent = target.toFixed(dec); return; }
        const dur = 1800;
        const t0 = performance.now();
        const step = (now) => {
            const t = clamp((now - t0) / dur, 0, 1);
            el.textContent = (target * easeOutExpo(t)).toFixed(dec);
            if (t < 1) requestAnimationFrame(step);
        };
        el.textContent = (0).toFixed(dec);
        requestAnimationFrame(step);
    }
    function typeText(el) {
        const text = el.dataset.typed || el.textContent;
        if (RM) return;
        el.textContent = '';
        el.classList.add('is-typing');
        let i = 0;
        const tick = () => {
            el.textContent = text.slice(0, ++i);
            if (i < text.length) setTimeout(tick, 32 + Math.random() * 40);
            else setTimeout(() => el.classList.remove('is-typing'), 1200);
        };
        setTimeout(tick, 400);
    }
    function onIn(el) {
        el.classList.add('is-in');
        $$('[data-count]', el).forEach(animateCount);
        const typed = $('[data-typed]', el);
        if (typed) typeText(typed);
    }
    function observeReveals(scope = doc) {
        const els = $$(revealSel, scope).filter((el) => !el.classList.contains('is-in'));
        if (!('IntersectionObserver' in window) || RM) { els.forEach(onIn); return; }
        els.forEach((el) => revealIO.observe(el));
    }
    const revealIO = 'IntersectionObserver' in window ? new IntersectionObserver((entries) => {
        entries.forEach((en) => {
            if (en.isIntersecting) { onIn(en.target); revealIO.unobserve(en.target); }
        });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.12 }) : null;
    observeReveals();

    /* Pause continuous animations when off-screen (saves CPU/battery). */
    if ('IntersectionObserver' in window) {
        const pauseIO = new IntersectionObserver((entries) => {
            entries.forEach((en) => en.target.classList.toggle('is-offscreen', !en.isIntersecting));
        }, { rootMargin: '100px' });
        $$('.hero-visual, .marquee, .cta-box, .ticker-band, .service-card__visual, .case-card__media').forEach((el) => pauseIO.observe(el));
    }

    /* ------------------------------------------------------------------ *
     * Agentic workflow — wires from live node geometry + travelling pulses
     * ------------------------------------------------------------------ */
    const flow = $('[data-flow]');
    if (flow) {
        const svg = $('.flow__wires', flow);
        const NS = 'http://www.w3.org/2000/svg';
        const edges = [
            ['request', 'orchestrator'],
            ['orchestrator', 'research'], ['orchestrator', 'action'], ['orchestrator', 'validator'],
            ['research', 'output'], ['action', 'output'], ['validator', 'output'],
        ];
        let paths = [];
        let running = false;
        let raf = 0;

        const node = (k) => $(`[data-node="${k}"]`, flow);
        function build() {
            $$('.flow__wire, .flow__pulse', svg).forEach((n) => n.remove());
            const box = flow.getBoundingClientRect();
            const vertical = window.innerWidth <= 1024;
            paths = edges.map(([a, b], i) => {
                const ra = node(a).getBoundingClientRect();
                const rb = node(b).getBoundingClientRect();
                let d;
                if (vertical) {
                    const x1 = ra.left + ra.width / 2 - box.left, y1 = ra.bottom - box.top;
                    const x2 = rb.left + rb.width / 2 - box.left, y2 = rb.top - box.top;
                    const my = (y1 + y2) / 2;
                    d = `M${x1},${y1} C${x1},${my} ${x2},${my} ${x2},${y2}`;
                } else {
                    const x1 = ra.right - box.left, y1 = ra.top + ra.height / 2 - box.top;
                    const x2 = rb.left - box.left, y2 = rb.top + rb.height / 2 - box.top;
                    const mx = (x1 + x2) / 2;
                    d = `M${x1},${y1} C${mx},${y1} ${mx},${y2} ${x2},${y2}`;
                }
                const p = doc.createElementNS(NS, 'path');
                p.setAttribute('d', d);
                p.setAttribute('class', 'flow__wire');
                svg.appendChild(p);
                const dot = doc.createElementNS(NS, 'circle');
                dot.setAttribute('r', '3.5');
                dot.setAttribute('class', 'flow__pulse');
                dot.style.opacity = '0';
                svg.appendChild(dot);
                // stage: 0 = request→orch, 1 = orch→agents, 2 = agents→output
                return { p, dot, len: p.getTotalLength(), stage: i === 0 ? 0 : i < 4 ? 1 : 2, to: b, offset: (i % 3) * 0.08 };
            });
        }
        const CYCLE = 4200; // ms for a full request→output run
        function frame(now) {
            const t = (now % CYCLE) / CYCLE;              // 0..1 across 3 stages
            paths.forEach((e) => {
                const s0 = e.stage / 3 + e.offset / 3;
                const local = (t - s0) * 3.4;
                if (local >= 0 && local <= 1) {
                    const pt = e.p.getPointAtLength(e.len * easeOutExpo(local * 0.98));
                    e.dot.setAttribute('cx', pt.x);
                    e.dot.setAttribute('cy', pt.y);
                    e.dot.style.opacity = String(Math.sin(local * Math.PI));
                    if (local > 0.92) {
                        const n = node(e.to);
                        n.classList.add('is-pinged');
                        clearTimeout(n._pt);
                        n._pt = setTimeout(() => n.classList.remove('is-pinged'), 500);
                    }
                } else {
                    e.dot.style.opacity = '0';
                }
            });
            raf = requestAnimationFrame(frame);
        }
        build();
        let rt;
        window.addEventListener('resize', () => { clearTimeout(rt); rt = setTimeout(build, 150); }, { passive: true });
        if (doc.fonts && doc.fonts.ready) doc.fonts.ready.then(build);
        if (!RM && 'IntersectionObserver' in window) {
            new IntersectionObserver(([en]) => {
                if (en.isIntersecting && !running) { running = true; raf = requestAnimationFrame(frame); }
                else if (!en.isIntersecting && running) { running = false; cancelAnimationFrame(raf); }
            }).observe(flow);
        }
    }

    /* ------------------------------------------------------------------ *
     * Sticky process — active step follows scroll position
     * ------------------------------------------------------------------ */
    const proc = $('[data-process]');
    if (proc && 'IntersectionObserver' in window) {
        const steps = $$('[data-process-step]', proc);
        const navItems = $$('[data-process-nav]', proc);
        const num = $('[data-process-num]', proc);
        const fill = $('.process__fill', proc);
        let active = -1;
        const setActive = (i) => {
            if (i === active) return;
            active = i;
            steps.forEach((s, k) => s.classList.toggle('is-active', k === i));
            navItems.forEach((n, k) => n.classList.toggle('is-active', k === i));
            if (fill) fill.style.setProperty('--pp', ((i + 1) / steps.length).toFixed(3));
            if (num) {
                num.classList.add('is-swapping');
                setTimeout(() => { num.textContent = String(i + 1).padStart(2, '0'); num.classList.remove('is-swapping'); }, RM ? 0 : 220);
            }
        };
        const io = new IntersectionObserver((entries) => {
            entries.forEach((en) => { if (en.isIntersecting) setActive(parseInt(en.target.dataset.processStep, 10)); });
        }, { rootMargin: '-45% 0px -45% 0px' });
        steps.forEach((s) => io.observe(s));
        setActive(0);
    }

    /* ------------------------------------------------------------------ *
     * Case-study table of contents — highlight current chapter
     * ------------------------------------------------------------------ */
    const tocLinks = $$('[data-toc]');
    if (tocLinks.length && 'IntersectionObserver' in window) {
        const io = new IntersectionObserver((entries) => {
            entries.forEach((en) => {
                if (!en.isIntersecting) return;
                tocLinks.forEach((a) => a.classList.toggle('is-active', a.dataset.toc === en.target.id));
            });
        }, { rootMargin: '-40% 0px -55% 0px' });
        $$('[data-chapter]').forEach((c) => io.observe(c));
    }

    /* ------------------------------------------------------------------ *
     * Case-study filters — AJAX with graceful fallback to real links
     * ------------------------------------------------------------------ */
    const filters = $('[data-filters]');
    const grid = $('[data-case-grid]');
    const STATIC = root.hasAttribute('data-static');     // static HTML build: no PHP API
    if (filters && grid && STATIC) {
        const status = $('[data-filter-status]');
        const apply = (slug) => {
            $$('[data-filter]', filters).forEach((b) => {
                const on = b.dataset.filter === slug;
                b.classList.toggle('is-active', on);
                b.setAttribute('aria-pressed', String(on));
            });
            let n = 0;
            $$('.case-card', grid).forEach((c) => {
                const show = !slug || c.dataset.category === slug;
                c.hidden = !show;
                if (show) { n++; c.classList.remove('is-in'); requestAnimationFrame(() => c.classList.add('is-in')); }
            });
            if (status) status.textContent = `${n} case ${n === 1 ? 'study' : 'studies'} shown`;
        };
        filters.addEventListener('click', (e) => {
            const a = e.target.closest('[data-filter]');
            if (!a || e.metaKey || e.ctrlKey) return;
            e.preventDefault();
            apply(a.dataset.filter);
            history.replaceState(null, '', a.dataset.filter ? `?category=${encodeURIComponent(a.dataset.filter)}` : location.pathname);
        });
        const initial = new URLSearchParams(location.search).get('category');
        if (initial) apply(initial);
    } else if (filters && grid && window.fetch) {
        const status = $('[data-filter-status]');
        const base = filters.querySelector('[data-filter=""]').getAttribute('href');
        const load = async (slug, push = true) => {
            $$('[data-filter]', filters).forEach((b) => {
                const on = b.dataset.filter === slug;
                b.classList.toggle('is-active', on);
                b.setAttribute('aria-pressed', String(on));
            });
            grid.classList.add('is-loading');
            try {
                const api = base.replace(/case-studies\/?$/, 'api/case-studies');
                const res = await fetch(`${api}?category=${encodeURIComponent(slug)}`, { headers: { Accept: 'application/json' } });
                const data = await res.json();
                if (!data.ok) throw new Error(data.message || 'Request failed');
                await new Promise((r) => setTimeout(r, RM ? 0 : 180));
                grid.innerHTML = data.html;
                observeReveals(grid);
                bindCursorTargets(grid);
                bindReset();
                if (status) status.textContent = `${data.count} case ${data.count === 1 ? 'study' : 'studies'} shown`;
                if (push) history.replaceState({ slug }, '', slug ? `${base}?category=${encodeURIComponent(slug)}` : base);
            } catch (err) {
                window.location.href = slug ? `${base}?category=${encodeURIComponent(slug)}` : base;
            } finally {
                grid.classList.remove('is-loading');
            }
        };
        filters.addEventListener('click', (e) => {
            const a = e.target.closest('[data-filter]');
            if (!a || e.metaKey || e.ctrlKey) return;
            e.preventDefault();
            load(a.dataset.filter);
        });
        const bindReset = () => $$('[data-filter-reset]', grid).forEach((b) => b.addEventListener('click', (e) => { e.preventDefault(); load(''); }));
        bindReset();
    }

    /* ------------------------------------------------------------------ *
     * FAQ accordion — animated <details>
     * ------------------------------------------------------------------ */
    $$('[data-faq] details').forEach((d) => {
        const summary = $('summary', d);
        const panel = $('.faq__a', d);
        if (d.open) d.classList.add('is-open');
        summary.addEventListener('click', (e) => {
            e.preventDefault();
            if (d.open) {
                d.classList.remove('is-open');
                const done = () => { d.open = false; panel.removeEventListener('transitionend', done); };
                if (RM) done(); else { panel.addEventListener('transitionend', done); setTimeout(done, 600); }
            } else {
                d.open = true;
                requestAnimationFrame(() => requestAnimationFrame(() => d.classList.add('is-open')));
            }
        });
    });

    /* ------------------------------------------------------------------ *
     * Contact form — client validation + AJAX submit
     * ------------------------------------------------------------------ */
    const form = $('[data-contact-form]');
    if (form && window.fetch) {
        const statusBox = $('[data-form-status]', form);
        const success = $('[data-form-success]');
        const submit = $('button[type="submit"]', form);
        const rules = {
            name: (v) => (v.trim().length >= 2 ? '' : 'Please enter your full name.'),
            email: (v) => (/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v.trim()) ? '' : 'Please enter a valid email address.'),
            phone: (v) => (!v.trim() || /^[+()\d\s.-]{6,40}$/.test(v.trim()) ? '' : 'Please enter a valid phone number.'),
            message: (v) => (v.trim().length >= 20 ? '' : 'Please share a little more detail (at least 20 characters).'),
        };
        const setError = (name, msg) => {
            const input = form.elements[name];
            const out = $(`[data-error-for="${name}"]`, form);
            if (!input) return;
            const field = input.closest('.field');
            if (field) field.classList.toggle('has-error', !!msg);
            input.setAttribute('aria-invalid', msg ? 'true' : 'false');
            if (out) out.textContent = msg || '';
        };
        const showStatus = (msg) => {
            statusBox.hidden = !msg;
            statusBox.className = 'form__status' + (msg ? ' is-error' : '');
            statusBox.textContent = msg || '';
        };
        const validate = () => {
            let first = null;
            Object.keys(rules).forEach((k) => {
                const msg = rules[k](form.elements[k].value);
                setError(k, msg);
                if (msg && !first) first = form.elements[k];
            });
            return first;
        };
        Object.keys(rules).forEach((k) => {
            const el = form.elements[k];
            el.addEventListener('blur', () => { if (el.value) setError(k, rules[k](el.value)); });
            el.addEventListener('input', () => { if (el.getAttribute('aria-invalid') === 'true') setError(k, rules[k](el.value)); });
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            showStatus('');
            const invalid = validate();
            if (invalid) { invalid.focus(); showStatus('Please correct the highlighted fields.'); return; }

            // Static build: post to a form service if configured, otherwise hand off to the visitor's email app.
            if (STATIC && !form.dataset.endpoint) {
                const f = form.elements;
                const body = [`Name: ${f.name.value}`, `Email: ${f.email.value}`, `Phone: ${f.phone.value}`, `Company: ${f.company.value}`, `Budget: ${f.budget.value}`, '', f.message.value].join('\n');
                window.location.href = `mailto:${form.dataset.mailto}?subject=${encodeURIComponent('Project enquiry from ' + f.name.value)}&body=${encodeURIComponent(body)}`;
                form.hidden = true;
                success.hidden = false;
                success.focus();
                return;
            }
            submit.classList.add('is-loading');
            submit.setAttribute('aria-busy', 'true');
            const label = $('.btn__label', submit);
            const orig = label.textContent;
            label.textContent = 'Sending…';
            try {
                const res = await fetch(form.dataset.endpoint || form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
                    credentials: 'same-origin',
                });
                const data = await res.json().catch(() => ({ ok: false, message: 'Unexpected response. Please try again.' }));
                if (data.ok) {
                    form.hidden = true;
                    success.hidden = false;
                    success.focus();
                    success.scrollIntoView({ behavior: RM ? 'auto' : 'smooth', block: 'center' });
                } else {
                    const errs = data.errors || {};
                    Object.keys(errs).forEach((k) => setError(k, errs[k]));
                    showStatus(data.message || 'Something went wrong. Please try again.');
                    const firstErr = Object.keys(errs)[0];
                    if (firstErr && form.elements[firstErr]) form.elements[firstErr].focus();
                }
            } catch (err) {
                showStatus('Network error — please check your connection and try again.');
            } finally {
                submit.classList.remove('is-loading');
                submit.removeAttribute('aria-busy');
                label.textContent = orig;
            }
        });
    }

    /* ------------------------------------------------------------------ *
     * Pointer-driven effects (desktop, fine pointer, motion allowed)
     * ------------------------------------------------------------------ */
    if (FINE && !RM) {
        // Card spotlight + button highlight follow the cursor (one shared listener)
        const SPOT = '.service-card, .auto-card, .case-card, .feature, .stack-col, .info-card, .timeline__card';
        doc.addEventListener('pointermove', (e) => {
            if (!e.target.closest) return;
            const card = e.target.closest(SPOT);
            if (card) {
                const r = card.getBoundingClientRect();
                card.style.setProperty('--mx', `${e.clientX - r.left}px`);
                card.style.setProperty('--my', `${e.clientY - r.top}px`);
            }
            const btn = e.target.closest('.btn--primary');
            if (btn) {
                const r = btn.getBoundingClientRect();
                btn.style.setProperty('--hx', `${((e.clientX - r.left) / r.width * 100).toFixed(1)}%`);
            }
        }, { passive: true });

        // Magnetic CTAs (max ~7px)
        $$('[data-magnetic]').forEach((el) => {
            const strength = 7;
            el.addEventListener('pointermove', (e) => {
                if (!isDesktop()) return;
                const r = el.getBoundingClientRect();
                const dx = (e.clientX - (r.left + r.width / 2)) / (r.width / 2);
                const dy = (e.clientY - (r.top + r.height / 2)) / (r.height / 2);
                el.style.translate = `${(dx * strength).toFixed(1)}px ${(dy * strength * 0.6).toFixed(1)}px`;
            });
            el.addEventListener('pointerleave', () => { el.style.translate = ''; });
        });
    }

    /* Custom cursor */
    const cursor = $('.cursor');
    function bindCursorTargets(scope = doc) {
        if (!cursor || !root.classList.contains('has-cursor')) return;
        $$('a, button, summary, [data-cursor], input, select, textarea, label', scope).forEach((el) => {
            if (el._cur) return;
            el._cur = true;
            el.addEventListener('pointerenter', () => {
                if (el.closest('[data-cursor="view"]') || el.dataset.cursor === 'view') cursor.classList.add('is-view');
                else if (/INPUT|SELECT|TEXTAREA/.test(el.tagName)) cursor.classList.add('is-hidden');
                else cursor.classList.add('is-link');
            });
            el.addEventListener('pointerleave', () => cursor.classList.remove('is-link', 'is-view', 'is-hidden'));
        });
    }
    if (cursor && FINE && !RM && window.innerWidth > 1024) {
        root.classList.add('has-cursor');
        let mx = -100, my = -100, rx = -100, ry = -100;
        doc.addEventListener('pointermove', (e) => { mx = e.clientX; my = e.clientY; }, { passive: true });
        doc.addEventListener('pointerleave', () => { mx = my = -100; });
        const loop = () => {
            rx += (mx - rx) * 0.18;
            ry += (my - ry) * 0.18;
            cursor.style.setProperty('--cx', `${mx}px`);
            cursor.style.setProperty('--cy', `${my}px`);
            cursor.style.setProperty('--rx', `${rx.toFixed(1)}px`);
            cursor.style.setProperty('--ry', `${ry.toFixed(1)}px`);
            requestAnimationFrame(loop);
        };
        requestAnimationFrame(loop);
        bindCursorTargets();
    }

    /* ------------------------------------------------------------------ *
     * Page transitions — short fade-out on internal navigation
     * ------------------------------------------------------------------ */
    if (!RM) {
        doc.addEventListener('click', (e) => {
            const a = e.target.closest('a[href]');
            if (!a || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
            if (a.target && a.target !== '_self') return;
            if (a.hasAttribute('download') || a.dataset.filter !== undefined || a.hasAttribute('data-filter-reset')) return;
            const url = new URL(a.href, location.href);
            if (url.origin !== location.origin) return;
            if (/\/(admin|api)\//.test(url.pathname) || /\.(xml|txt|pdf|zip)$/i.test(url.pathname)) return;
            if (url.pathname === location.pathname && url.search === location.search) return; // hash / same page
            e.preventDefault();
            doc.body.classList.add('is-leaving');
            setTimeout(() => { window.location.href = url.href; }, 280);
        });
        window.addEventListener('pageshow', (e) => { if (e.persisted) doc.body.classList.remove('is-leaving'); });
    }
})();
