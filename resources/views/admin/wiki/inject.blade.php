<script>
    window.extendedTranslationWikiInject = @json($payload);
</script>
<script>
    (function () {
        const cfg = window.extendedTranslationWikiInject;

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

        function injectSortable(map, selector, className) {
            if (!map) {
                return;
            }

            document.querySelectorAll(selector).forEach(function (item) {
                const attr = item.hasAttribute('data-category-id') ? 'data-category-id' : 'data-id';
                const id = parseInt(item.getAttribute(attr), 10);
                const href = map[id];
                const actions = item.querySelector(':scope > .card > .card-body > span:last-child');

                if (!href || !actions) {
                    return;
                }

                actions.prepend(translateLink(href, className));
            });
        }

        injectSortable(cfg.wikiCategories, 'li.sortable-item[data-category-id]', 'mx-1');
        injectSortable(cfg.wikiPages, 'li.sortable-item[data-id]', 'm-1');

        if (cfg.indexUrl && (cfg.wikiPages || cfg.wikiCategories)) {
            const addPageBtn = document.querySelector('a.btn.btn-primary[href*="pages/create"]');
            const addCatBtn = document.querySelector('a.btn.btn-primary[href*="categories/create"]');
            const addBtn = addPageBtn || addCatBtn;

            if (addBtn) {
                const link = document.createElement('a');
                link.href = cfg.indexUrl;
                link.className = 'btn btn-outline-primary ms-2';
                link.innerHTML = '<i class="bi bi-translate"></i> ' + cfg.label;
                addBtn.insertAdjacentElement('afterend', link);
            }
        }
    })();
</script>
