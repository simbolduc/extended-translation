<script>
    window.extendedTranslationVoteInject = @json($payload);
</script>
<script>
    (function () {
        const cfg = window.extendedTranslationVoteInject;

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

        if (cfg.rewards) {
            document.querySelectorAll('table tbody tr').forEach(function (row) {
                const heading = row.querySelector('th');

                if (!heading) {
                    return;
                }

                const id = parseInt(heading.textContent, 10);
                const href = cfg.rewards[id];
                const actions = row.querySelector('td:last-child');

                if (!href || !actions) {
                    return;
                }

                actions.prepend(translateLink(href, 'mx-1'));
            });

            const addBtn = document.querySelector('a.btn.btn-primary[href*="rewards/create"]');

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
