<script>
    window.extendedTranslationChangelogInject = @json($payload);
</script>
<script>
    (function () {
        const cfg = window.extendedTranslationChangelogInject;

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

        function addButton(href, afterEl) {
            if (!href || !afterEl) {
                return;
            }

            const link = document.createElement('a');
            link.href = href;
            link.className = 'btn btn-outline-primary ms-2';
            link.innerHTML = '<i class="bi bi-translate"></i> ' + cfg.label;
            afterEl.insertAdjacentElement('afterend', link);
        }

        if (cfg.editUrl) {
            const saveBtn = document.querySelector('form button[type="submit"]');
            addButton(cfg.editUrl, saveBtn);
        }

        if (cfg.titleUrl) {
            const titleInput = document.getElementById('titleInput');
            addButton(cfg.titleUrl, titleInput);
        }

        if (cfg.changelogCategories) {
            document.querySelectorAll('li.sortable-item[data-category-id]').forEach(function (item) {
                const id = parseInt(item.getAttribute('data-category-id'), 10);
                const href = cfg.changelogCategories[id];
                const actions = item.querySelector(':scope > .card > .card-body > span:last-child');

                if (!href || !actions) {
                    return;
                }

                actions.prepend(translateLink(href, 'mx-1'));
            });

            const addCatBtn = document.querySelector('a.btn.btn-primary[href*="categories/create"]');

            if (addCatBtn && cfg.indexUrl) {
                addButton(cfg.indexUrl, addCatBtn);
            }
        }

        if (cfg.changelogUpdates) {
            document.querySelectorAll('table tbody tr').forEach(function (row) {
                const heading = row.querySelector('th');

                if (!heading) {
                    return;
                }

                const id = parseInt(heading.textContent, 10);
                const href = cfg.changelogUpdates[id];
                const actions = row.querySelector('td:last-child');

                if (!href || !actions) {
                    return;
                }

                actions.prepend(translateLink(href, 'mx-1'));
            });

            const addUpdateBtn = document.querySelector('a.btn.btn-primary[href*="updates/create"]');

            if (addUpdateBtn && cfg.indexUrl) {
                addButton(cfg.indexUrl, addUpdateBtn);
            }
        }
    })();
</script>
