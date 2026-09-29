/* Primary Info Tech admin — progressive enhancements (every feature also works without JS where it matters). */
(() => {
    'use strict';
    const $ = (s, c = document) => c.querySelector(s);
    const $$ = (s, c = document) => Array.from(c.querySelectorAll(s));
    const csrf = ($('meta[name="csrf-token"]') || {}).content || '';

    const post = (url, data) => {
        const fd = new FormData();
        Object.entries(data).forEach(([k, v]) => (Array.isArray(v) ? v.forEach((x) => fd.append(k + '[]', x)) : fd.append(k, v)));
        fd.append('_csrf', csrf);
        return fetch(url, { method: 'POST', body: fd, credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' } }).then((r) => r.json());
    };

    /* Sidebar (mobile) */
    const sideBtn = $('[data-side-toggle]');
    if (sideBtn) {
        sideBtn.addEventListener('click', () => {
            const open = document.body.classList.toggle('side-open');
            sideBtn.setAttribute('aria-expanded', String(open));
        });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') document.body.classList.remove('side-open'); });
    }

    /* Confirm destructive actions */
    $$('form[data-confirm]').forEach((f) => f.addEventListener('submit', (e) => {
        if (!window.confirm(f.dataset.confirm)) e.preventDefault();
        else f.dataset.submitting = '1';
    }));

    /* Auto-submit filters */
    $$('[data-autosubmit]').forEach((s) => s.addEventListener('change', () => s.form.submit()));

    /* Toggle switches via AJAX */
    $$('form[data-toggle-form]').forEach((f) => f.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = $('.a-switch', f);
        const data = Object.fromEntries(new FormData(f));
        delete data._csrf;
        btn.classList.toggle('is-on');
        btn.setAttribute('aria-checked', btn.classList.contains('is-on') ? 'true' : 'false');
        try {
            const res = await post(f.getAttribute('action') || location.href, data);
            if (!res.ok) throw new Error(res.message);
        } catch (err) {
            btn.classList.toggle('is-on');
            alert('Could not update — please reload and try again.');
        }
    }));

    /* Drag-and-drop reordering */
    $$('tbody[data-sortable]').forEach((tbody) => {
        let dragged = null;
        $$('tr', tbody).forEach((tr) => {
            const grip = $('.a-grip', tr);
            if (!grip) return;
            grip.addEventListener('mousedown', () => tr.setAttribute('draggable', 'true'));
            grip.addEventListener('touchstart', () => tr.setAttribute('draggable', 'true'), { passive: true });
            tr.addEventListener('dragstart', (e) => { dragged = tr; tr.classList.add('is-dragging'); e.dataTransfer.effectAllowed = 'move'; });
            tr.addEventListener('dragend', async () => {
                tr.classList.remove('is-dragging');
                tr.removeAttribute('draggable');
                $$('.is-drop-target', tbody).forEach((x) => x.classList.remove('is-drop-target'));
                const ids = $$('tr', tbody).map((r) => r.dataset.id);
                const data = { action: 'reorder', ids };
                if (tbody.dataset.resource) data.r = tbody.dataset.resource;
                try { await post(tbody.dataset.sortable, data); } catch (e) { alert('Could not save the new order.'); }
            });
            tr.addEventListener('dragover', (e) => {
                e.preventDefault();
                if (!dragged || dragged === tr) return;
                const r = tr.getBoundingClientRect();
                const after = e.clientY > r.top + r.height / 2;
                tbody.insertBefore(dragged, after ? tr.nextSibling : tr);
            });
        });
    });

    /* Slug auto-generation */
    const slugify = (s) => s.toLowerCase().normalize('NFKD').replace(/[̀-ͯ]/g, '').replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    $$('[data-slug-from]').forEach((slug) => {
        const src = document.getElementById(slug.dataset.slugFrom);
        if (!src) return;
        let touched = slug.value !== '';
        slug.addEventListener('input', () => { touched = slug.value !== ''; });
        src.addEventListener('input', () => { if (!touched) slug.value = slugify(src.value); });
    });

    /* Character counters */
    $$('[data-counter]').forEach((el) => {
        const limit = parseInt(el.dataset.counter, 10);
        const out = document.createElement('small');
        out.className = 'a-counter';
        el.insertAdjacentElement('afterend', out);
        const upd = () => { out.textContent = `${el.value.length} / ${limit} recommended`; out.classList.toggle('is-over', el.value.length > limit); };
        el.addEventListener('input', upd);
        upd();
    });

    /* Image fields: live preview, clear */
    $$('[data-image-field]').forEach((wrap) => {
        const img = $('[data-img-preview]', wrap);
        const empty = $('.a-image__empty', wrap);
        const path = $('[data-img-path]', wrap);
        const file = $('[data-img-file]', wrap);
        const show = (src) => { if (src) { img.src = src; img.hidden = false; empty.hidden = true; } else { img.hidden = true; img.removeAttribute('src'); empty.hidden = false; } };
        file.addEventListener('change', () => { if (file.files[0]) show(URL.createObjectURL(file.files[0])); });
        path.addEventListener('change', () => {
            const v = path.value.trim();
            if (!v) return show('');
            const root = (document.querySelector('link[rel="icon"]').getAttribute('href') || '').replace(/assets\/img\/favicon\.svg$/, '');
            show(/^https?:\/\//.test(v) ? v : root + v);
        });
        $('[data-img-clear]', wrap).addEventListener('click', () => { path.value = ''; file.value = ''; show(''); });
    });

    /* Icon select preview */
    $$('[data-icon-select]').forEach((sel) => {
        sel.addEventListener('change', () => {
            const prev = sel.parentElement.querySelector('[data-icon-preview]');
            const src = document.querySelector(`[data-icon-sprites] [data-icon="${CSS.escape(sel.value)}"]`);
            if (prev && src) prev.innerHTML = src.innerHTML;
        });
    });

    /* Metrics repeater */
    $$('[data-repeater]').forEach((rep) => {
        const tpl = document.getElementById('metric-row');
        const add = rep.parentElement.querySelector('[data-repeater-add]');
        rep.addEventListener('click', (e) => {
            const b = e.target.closest('[data-repeater-remove]');
            if (!b) return;
            const rows = $$('[data-repeater-row]', rep);
            if (rows.length > 1) b.closest('[data-repeater-row]').remove();
            else $$('input', rows[0]).forEach((i) => { i.value = ''; });
        });
        if (add && tpl) add.addEventListener('click', () => { rep.appendChild(tpl.content.cloneNode(true)); $$('input', rep).slice(-2)[0].focus(); });
    });

    /* Bulk select */
    const all = $('[data-check-all]');
    if (all) all.addEventListener('change', () => $$('input[name="ids[]"]').forEach((c) => { c.checked = all.checked; }));
    const bulk = $('[data-bulk-form]');
    if (bulk) bulk.addEventListener('submit', (e) => {
        const act = bulk.elements.bulk.value;
        const n = $$('input[name="ids[]"]:checked', bulk).length;
        if (!act || !n) { e.preventDefault(); alert('Select messages and an action first.'); return; }
        if (act === 'delete' && !confirm(`Delete ${n} message(s)? This cannot be undone.`)) e.preventDefault();
    });

    /* Copy path */
    $$('[data-copy]').forEach((b) => b.addEventListener('click', async () => {
        const input = b.parentElement.querySelector('input');
        try { await navigator.clipboard.writeText(input.value); } catch (e) { input.select(); document.execCommand('copy'); }
        b.classList.add('is-copied');
        b.title = 'Copied!';
    }));

    /* Drag & drop upload */
    const drop = $('[data-drop]');
    if (drop) {
        const input = $('[data-drop-input]', drop);
        ['dragenter', 'dragover'].forEach((ev) => drop.addEventListener(ev, (e) => { e.preventDefault(); drop.classList.add('is-over'); }));
        ['dragleave', 'drop'].forEach((ev) => drop.addEventListener(ev, (e) => { e.preventDefault(); drop.classList.remove('is-over'); }));
        drop.addEventListener('drop', (e) => { input.files = e.dataTransfer.files; drop.submit(); });
        input.addEventListener('change', () => drop.submit());
    }

    /* Unsaved-changes guard */
    $$('form[data-dirty-guard]').forEach((f) => {
        let dirty = false;
        f.addEventListener('input', () => { dirty = true; });
        f.addEventListener('change', () => { dirty = true; });
        f.addEventListener('submit', () => { dirty = false; });
        window.addEventListener('beforeunload', (e) => { if (dirty) { e.preventDefault(); e.returnValue = ''; } });
    });
})();
