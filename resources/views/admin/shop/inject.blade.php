<script>
    window.extendedTranslationShopInject = @json($payload);
</script>
<script>
    (function () {
        const cfg = window.extendedTranslationShopInject;

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

        if (cfg.shopCategories) {
            document.querySelectorAll('li.sortable-item[data-category-id]').forEach(function (item) {
                const id = parseInt(item.getAttribute('data-category-id'), 10);
                const href = cfg.shopCategories[id];
                const actions = item.querySelector(':scope > .card > .card-body > span:last-child');

                if (!href || !actions) {
                    return;
                }

                actions.prepend(translateLink(href, 'mx-1'));
            });
        }

        if (cfg.shopPackages) {
            document.querySelectorAll('li.sortable-item[data-package-id]').forEach(function (item) {
                const id = parseInt(item.getAttribute('data-package-id'), 10);
                const href = cfg.shopPackages[id];
                const actions = item.querySelector(':scope > .card > .card-body > .d-inline-block');

                if (!href || !actions) {
                    return;
                }

                actions.prepend(translateLink(href, 'mx-1'));
            });
        }

        function injectTable(map) {
            if (!map) {
                return;
            }

            document.querySelectorAll('table tbody tr').forEach(function (row) {
                const heading = row.querySelector('th');

                if (!heading) {
                    return;
                }

                const id = parseInt(heading.textContent, 10);
                const href = map[id];
                const actions = row.querySelector('td:last-child');

                if (!href || !actions) {
                    return;
                }

                actions.prepend(translateLink(href, 'mx-1'));
            });
        }

        injectTable(cfg.shopOffers);
        injectTable(cfg.shopVariables);

        const addBtn = document.querySelector('a.btn.btn-primary[href*="categories/create"]')
            || document.querySelector('a.btn.btn-primary[href*="packages/create"]')
            || document.querySelector('a.btn.btn-primary[href*="offers/create"]')
            || document.querySelector('a.btn.btn-primary[href*="variables/create"]');

        if (addBtn && cfg.indexUrl && (cfg.shopCategories || cfg.shopPackages || cfg.shopOffers || cfg.shopVariables)) {
            addButton(cfg.indexUrl, addBtn);
        }
    })();
</script>
