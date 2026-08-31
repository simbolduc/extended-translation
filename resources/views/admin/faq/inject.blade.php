<script>
    window.extendedTranslationFaqInject = @json($payload);
</script>
<script>
    (function () {
        const cfg = window.extendedTranslationFaqInject;

        if (!cfg) {
            return;
        }

        function translateLink(href, className) {
            const link = document.createElement('a');
            link.href = href;
            link.className = className;
            link.title = cfg.label;
            link.setAttribute('data-bs-toggle', 'tooltip');
            link.innerHTML = '<i class="bi bi-translate"></i>';

            return link;
        }

        if (cfg.editUrl) {
            const saveBtn = document.querySelector('form button[type="submit"]');

            if (saveBtn) {
                const link = document.createElement('a');
                link.href = cfg.editUrl;
                link.className = 'btn btn-outline-primary ms-2';
                link.innerHTML = '<i class="bi bi-translate"></i> ' + cfg.label;
                saveBtn.insertAdjacentElement('afterend', link);
            }
        }

        if (cfg.questions) {
            document.querySelectorAll('li.sortable-item[data-id]').forEach(function (item) {
                const id = parseInt(item.getAttribute('data-id'), 10);
                const href = cfg.questions[id];
                const actions = item.querySelector(':scope > .card > .card-body > span:last-child');

                if (!href || !actions) {
                    return;
                }

                actions.prepend(translateLink(href, 'm-1'));
            });

            const addBtn = document.querySelector('a.btn.btn-primary[href*="questions/create"]');

            if (addBtn && cfg.indexUrl) {
                const link = document.createElement('a');
                link.href = cfg.indexUrl;
                link.className = 'btn btn-outline-primary ms-2';
                link.innerHTML = '<i class="bi bi-translate"></i> ' + cfg.label;
                addBtn.insertAdjacentElement('afterend', link);
            }
        }
    })();
</script>
