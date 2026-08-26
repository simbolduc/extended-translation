(function () {
    const cfg = window.extendedTranslationInject;

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

    function addEditButton() {
        if (!cfg.editUrl) {
            return;
        }

        const saveBtn = document.querySelector('form button[type="submit"]');

        if (!saveBtn) {
            return;
        }

        const link = document.createElement('a');
        link.href = cfg.editUrl;
        link.className = 'btn btn-outline-primary ms-2';
        link.innerHTML = '<i class="bi bi-translate"></i> ' + cfg.label;
        saveBtn.insertAdjacentElement('afterend', link);
    }

    if (cfg.postId || cfg.pageId || cfg.elementId) {
        addEditButton();
    }

    function injectTable(map, addHrefPart) {
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

        const addBtn = document.querySelector('a.btn.btn-primary[href*="' + addHrefPart + '"]');

        if (addBtn && cfg.indexUrl) {
            const link = document.createElement('a');
            link.href = cfg.indexUrl;
            link.className = 'btn btn-outline-primary';
            link.innerHTML = '<i class="bi bi-translate"></i> ' + cfg.label;

            const wrap = document.createElement('span');
            wrap.className = 'd-inline-flex align-items-center gap-2';
            Array.from(addBtn.classList).forEach(function (cls) {
                if (/^m([btxyse])?-/.test(cls)) {
                    wrap.classList.add(cls);
                    addBtn.classList.remove(cls);
                }
            });
            addBtn.replaceWith(wrap);
            wrap.append(addBtn, link);
        }
    }

    injectTable(cfg.posts, 'posts');
    injectTable(cfg.pages, 'pages');

    if (cfg.elements) {
        document.querySelectorAll('li.sortable-item[data-id]').forEach(function (item) {
            const id = parseInt(item.getAttribute('data-id'), 10);
            const href = cfg.elements[id];
            const actions = item.querySelector(':scope > .card > .card-body > span:last-child');

            if (!href || !actions) {
                return;
            }

            actions.prepend(translateLink(href, 'm-1'));
        });

        const addBtn = document.querySelector('a.btn.btn-primary[href*="navbar-elements"]');

        if (addBtn && cfg.indexUrl) {
            const link = document.createElement('a');
            link.href = cfg.indexUrl;
            link.className = 'btn btn-outline-primary ms-2';
            link.innerHTML = '<i class="bi bi-translate"></i> ' + cfg.label;
            addBtn.insertAdjacentElement('afterend', link);
        }
    }
})();
