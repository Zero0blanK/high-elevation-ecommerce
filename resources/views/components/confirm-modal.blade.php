<div id="global-confirm-modal" class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div id="global-confirm-backdrop" class="absolute inset-0 bg-gray-900/60"></div>
    <div class="relative z-10 flex min-h-full items-center justify-center p-4">
        <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 id="global-confirm-title" class="text-lg font-semibold text-gray-900">Please confirm</h3>
            </div>
            <div class="px-6 py-5">
                <p id="global-confirm-message" class="text-sm text-gray-600">Are you sure you want to continue?</p>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                <button id="global-confirm-cancel" type="button" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors">
                    Cancel
                </button>
                <button id="global-confirm-accept" type="button" class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(() => {
    if (window.__heConfirmModalInitialized) {
        return;
    }
    window.__heConfirmModalInitialized = true;

    const modal = document.getElementById('global-confirm-modal');
    const backdrop = document.getElementById('global-confirm-backdrop');
    const titleNode = document.getElementById('global-confirm-title');
    const messageNode = document.getElementById('global-confirm-message');
    const cancelBtn = document.getElementById('global-confirm-cancel');
    const acceptBtn = document.getElementById('global-confirm-accept');

    if (!modal || !backdrop || !titleNode || !messageNode || !cancelBtn || !acceptBtn) {
        return;
    }

    let onConfirm = null;
    let lastFocusedElement = null;

    const extractConfirmMessage = (handler) => {
        if (!handler || !handler.includes('confirm(')) {
            return null;
        }

        const match = handler.match(/confirm\s*\(\s*(['"`])([\s\S]*?)\1\s*\)/i);
        return match && match[2] ? match[2] : null;
    };

    const normalizeMessage = (message) => {
        if (!message) {
            return 'Are you sure you want to continue?';
        }

        return message.replace(/\\"/g, '"').replace(/\\'/g, "'");
    };

    const actionKeywordMessage = (action, label) => {
        const haystack = `${action} ${label}`.toLowerCase();

        if (haystack.includes('bulk-action') || haystack.includes('bulk action')) {
            return 'Are you sure you want to perform this bulk action?';
        }
        if (haystack.includes('update-status') || haystack.includes('update status')) {
            return 'Are you sure you want to update this order status?';
        }
        if (haystack.includes('force-delete') || haystack.includes('force delete')) {
            return 'Are you sure you want to permanently delete this item?';
        }
        if (haystack.includes('delete')) {
            return 'Are you sure you want to delete this item?';
        }
        if (haystack.includes('remove')) {
            return 'Are you sure you want to remove this item?';
        }
        if (haystack.includes('clear')) {
            return 'Are you sure you want to clear these items?';
        }
        if (haystack.includes('cancel')) {
            return 'Are you sure you want to cancel this?';
        }
        if (haystack.includes('refund')) {
            return 'Are you sure you want to process this refund?';
        }
        if (haystack.includes('confirm-received') || haystack.includes('confirm received')) {
            return 'Are you sure you want to confirm this order as received?';
        }
        if (haystack.includes('restore')) {
            return 'Are you sure you want to restore this item?';
        }

        return null;
    };

    const inferFormMessage = (form, submitter) => {
        const action = form.getAttribute('action') || '';
        const methodOverride = form.querySelector('input[name="_method"]')?.value || '';
        const method = (methodOverride || form.getAttribute('method') || 'GET').toUpperCase();
        const label = (submitter?.textContent || submitter?.value || '').trim();

        if (method === 'DELETE') {
            return actionKeywordMessage(action, label) || 'Are you sure you want to delete this item?';
        }

        return actionKeywordMessage(action, label);
    };

    const openModal = ({ title, message, confirmText, confirmClass }, callback) => {
        onConfirm = callback;
        lastFocusedElement = document.activeElement;

        titleNode.textContent = title || 'Please confirm';
        messageNode.textContent = normalizeMessage(message);
        acceptBtn.textContent = confirmText || 'Confirm';
        acceptBtn.className = confirmClass || 'px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors';

        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
        acceptBtn.focus();
    };

    const closeModal = (confirmed = false) => {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');

        const callback = onConfirm;
        onConfirm = null;

        if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
            lastFocusedElement.focus();
        }

        if (confirmed && typeof callback === 'function') {
            callback();
        }
    };

    const bootstrapInlineConfirm = () => {
        document.querySelectorAll('[onsubmit], [onclick]').forEach((element) => {
            ['onsubmit', 'onclick'].forEach((attribute) => {
                const handler = element.getAttribute(attribute);
                const message = extractConfirmMessage(handler);

                if (!message) {
                    return;
                }

                if (!element.dataset.confirmMessage) {
                    element.dataset.confirmMessage = message;
                }
                element.dataset.confirm = 'true';
                element.removeAttribute(attribute);
            });
        });
    };

    bootstrapInlineConfirm();

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        if (form.dataset.confirmBypass === 'true') {
            form.dataset.confirmBypass = 'false';
            return;
        }

        const submitter = event.submitter || null;

        if (submitter?.dataset.confirm === 'false' || form.dataset.confirm === 'false') {
            return;
        }

        const explicitMessage = submitter?.dataset.confirmMessage || form.dataset.confirmMessage || '';
        const message = explicitMessage || inferFormMessage(form, submitter);

        if (!message) {
            return;
        }

        event.preventDefault();

        openModal(
            {
                title: submitter?.dataset.confirmTitle || form.dataset.confirmTitle || 'Please confirm',
                message,
                confirmText: submitter?.dataset.confirmButton || form.dataset.confirmButton || 'Confirm',
                confirmClass: submitter?.dataset.confirmVariant === 'primary'
                    ? 'px-4 py-2 rounded-lg bg-amber-600 text-white hover:bg-amber-700 transition-colors'
                    : submitter?.dataset.confirmVariant === 'danger' || form.querySelector('input[name="_method"][value="DELETE"]')
                        ? 'px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors'
                        : 'px-4 py-2 rounded-lg bg-amber-600 text-white hover:bg-amber-700 transition-colors'
            },
            () => {
                form.dataset.confirmBypass = 'true';
                if (submitter) {
                    form.requestSubmit(submitter);
                } else {
                    form.requestSubmit();
                }
            }
        );
    }, true);

    acceptBtn.addEventListener('click', () => closeModal(true));
    cancelBtn.addEventListener('click', () => closeModal(false));
    backdrop.addEventListener('click', () => closeModal(false));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal(false);
        }
    });
})();
</script>
