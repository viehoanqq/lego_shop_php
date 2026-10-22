/* =====================================================================
   HỘP THOẠI DÙNG CHUNG (thay cho alert / confirm của trình duyệt)
   - uiAlert(message, type)            -> Promise, type: 'error' | 'success' | 'info'
   - uiConfirm(message, { okText, danger }) -> Promise<boolean>
   - Thêm data-confirm="..." vào <a>, <button type="submit"> hoặc <form>
     để tự hỏi xác nhận trước khi đi tiếp / gửi form.
   ===================================================================== */
(function () {
    if (window.uiAlert) return;

    const css = `
    .ui-dlg-overlay { position: fixed; inset: 0; background: rgba(15, 23, 42, .45); display: flex; align-items: center; justify-content: center; z-index: 100000; padding: 16px; opacity: 0; transition: opacity .15s; }
    .ui-dlg-overlay.show { opacity: 1; }
    .ui-dlg { background: #fff; border-radius: 14px; width: 100%; max-width: 420px; box-shadow: 0 20px 50px rgba(0,0,0,.2); padding: 24px 24px 18px; transform: translateY(8px); transition: transform .15s; font-family: inherit; }
    .ui-dlg-overlay.show .ui-dlg { transform: none; }
    .ui-dlg-head { display: flex; align-items: center; gap: 12px; margin-bottom: 10px; }
    .ui-dlg-icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
    .ui-dlg-icon.error, .ui-dlg-icon.danger { background: #fee2e2; color: #dc2626; }
    .ui-dlg-icon.success { background: #dcfce7; color: #16a34a; }
    .ui-dlg-icon.info, .ui-dlg-icon.confirm { background: #e0e7ff; color: #4338ca; }
    .ui-dlg-title { font-size: 17px; font-weight: 700; color: #1e293b; }
    .ui-dlg-msg { font-size: 15px; color: #475569; line-height: 1.6; white-space: pre-line; margin: 0 0 20px 52px; }
    .ui-dlg-actions { display: flex; justify-content: flex-end; gap: 8px; }
    .ui-dlg-btn { border: 1px solid #e2e8f0; background: #fff; color: #334155; padding: 9px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; font-family: inherit; }
    .ui-dlg-btn:hover { background: #f8fafc; }
    .ui-dlg-btn.primary { background: #2563eb; border-color: #2563eb; color: #fff; }
    .ui-dlg-btn.primary:hover { background: #1d4ed8; }
    .ui-dlg-btn.danger { background: #dc2626; border-color: #dc2626; color: #fff; }
    .ui-dlg-btn.danger:hover { background: #b91c1c; }
    @media (max-width: 480px) { .ui-dlg-msg { margin-left: 0; } .ui-dlg-actions .ui-dlg-btn { flex: 1; } }`;
    const style = document.createElement('style');
    style.textContent = css;
    document.head.appendChild(style);

    const ICONS = { error: 'fa-circle-exclamation', danger: 'fa-triangle-exclamation', success: 'fa-circle-check', info: 'fa-circle-info', confirm: 'fa-circle-question' };
    const TITLES = { error: 'Thông báo', danger: 'Xác nhận', success: 'Thành công', info: 'Thông báo', confirm: 'Xác nhận' };

    // Chuỗi trong thuộc tính HTML có thể chứa "\n" dạng chữ -> đổi thành xuống dòng
    const normalize = (msg) => String(msg ?? '').replace(/\\n/g, '\n');

    function open({ message, type, buttons }) {
        return new Promise(resolve => {
            const overlay = document.createElement('div');
            overlay.className = 'ui-dlg-overlay';
            overlay.innerHTML = `
                <div class="ui-dlg" role="dialog" aria-modal="true">
                    <div class="ui-dlg-head">
                        <span class="ui-dlg-icon ${type}"><i class="fa-solid ${ICONS[type] || ICONS.info}"></i></span>
                        <span class="ui-dlg-title">${TITLES[type] || TITLES.info}</span>
                    </div>
                    <p class="ui-dlg-msg"></p>
                    <div class="ui-dlg-actions"></div>
                </div>`;
            overlay.querySelector('.ui-dlg-msg').textContent = normalize(message);

            const close = (value) => {
                overlay.classList.remove('show');
                document.removeEventListener('keydown', onKey);
                setTimeout(() => overlay.remove(), 150);
                resolve(value);
            };
            const actions = overlay.querySelector('.ui-dlg-actions');
            buttons.forEach(b => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'ui-dlg-btn ' + (b.cls || '');
                btn.textContent = b.text;
                btn.addEventListener('click', () => close(b.value));
                actions.appendChild(btn);
            });
            const onKey = (e) => {
                if (e.key === 'Escape') close(buttons[0].value);
                if (e.key === 'Enter') { e.preventDefault(); close(buttons[buttons.length - 1].value); }
            };
            document.addEventListener('keydown', onKey);
            overlay.addEventListener('click', (e) => { if (e.target === overlay) close(buttons[0].value); });

            document.body.appendChild(overlay);
            requestAnimationFrame(() => overlay.classList.add('show'));
            actions.lastElementChild.focus();
        });
    }

    window.uiAlert = function (message, type = 'error') {
        return open({ message, type, buttons: [{ text: 'Đóng', value: true, cls: 'primary' }] });
    };

    window.uiConfirm = function (message, opts = {}) {
        const danger = !!opts.danger;
        return open({
            message,
            type: danger ? 'danger' : 'confirm',
            buttons: [
                { text: opts.cancelText || 'Hủy', value: false },
                { text: opts.okText || 'Đồng ý', value: true, cls: danger ? 'danger' : 'primary' },
            ],
        });
    };

    // Phòng hờ: alert() còn sót cũng hiện bằng hộp thoại
    window.alert = (msg) => { window.uiAlert(msg); };

    // ----- data-confirm cho link / nút submit / form -----
    const isDanger = (el) => el.hasAttribute('data-danger') || /xóa|hủy|khóa|từ chối/i.test(el.getAttribute('data-confirm') || '');

    document.addEventListener('click', async (e) => {
        const el = e.target.closest('[data-confirm]');
        if (!el || el.tagName === 'FORM' || e.defaultPrevented) return;
        if (el.dataset.confirmed === '1') { delete el.dataset.confirmed; return; }
        e.preventDefault();
        if (!(await window.uiConfirm(el.getAttribute('data-confirm'), { danger: isDanger(el) }))) return;

        if (el.tagName === 'A') {
            window.location.href = el.href;
        } else if (el.form) {
            el.dataset.confirmed = '1';
            el.form.dataset.confirmed = '1';
            el.form.requestSubmit(el);
        }
    });

    document.addEventListener('submit', async (e) => {
        const form = e.target;
        if (!form.hasAttribute('data-confirm') || e.defaultPrevented) return;
        if (form.dataset.confirmed === '1') { delete form.dataset.confirmed; return; }
        e.preventDefault();
        const submitter = e.submitter;
        if (!(await window.uiConfirm(form.getAttribute('data-confirm'), { danger: isDanger(form) }))) return;
        form.dataset.confirmed = '1';
        form.requestSubmit(submitter && submitter.form === form ? submitter : undefined);
    });
})();
