const sidebarMount = document.getElementById('react-sidebar-root');
const navbarMount = document.getElementById('react-navbar-root');
const contentRoot = document.querySelector('.main-content .container-fluid');
const pageScriptsRoot = document.getElementById('page-scripts');

if (sidebarMount && navbarMount && contentRoot && pageScriptsRoot) {
    const loadedPageScripts = new Set(
        [...pageScriptsRoot.querySelectorAll('script[src]')].map((script) => script.src),
    );
    let activeRequest;
    let navigationId = 0;
    let scrollUpdateFrame;

    window.history.scrollRestoration = 'manual';

    const updateShell = (documentPage) => {
        const nextSidebar = documentPage.getElementById('react-sidebar-root');
        const nextNavbar = documentPage.getElementById('react-navbar-root');

        if (!nextSidebar || !nextNavbar) return false;

        sidebarMount.dataset.navigation = nextSidebar.dataset.navigation;
        navbarMount.dataset.title = nextNavbar.dataset.title;
        navbarMount.dataset.newTransaction = nextNavbar.dataset.newTransaction;
        document.title = documentPage.title;

        document.head.querySelectorAll('style[data-page-style]').forEach((style) => style.remove());
        documentPage.head.querySelectorAll('style[data-page-style]').forEach((style) => {
            document.head.appendChild(style.cloneNode(true));
        });

        return true;
    };

    const runPageScripts = async (documentPage) => {
        const nextScriptsRoot = documentPage.getElementById('page-scripts');
        pageScriptsRoot.replaceChildren();
        if (!nextScriptsRoot) return;

        for (const sourceScript of nextScriptsRoot.querySelectorAll('script')) {
            if (sourceScript.src && loadedPageScripts.has(sourceScript.src)) continue;

            const script = document.createElement('script');
            for (const attribute of sourceScript.attributes) {
                script.setAttribute(attribute.name, attribute.value);
            }

            if (sourceScript.src) {
                loadedPageScripts.add(sourceScript.src);
                await new Promise((resolve) => {
                    script.onload = resolve;
                    script.onerror = () => {
                        loadedPageScripts.delete(sourceScript.src);
                        console.error(`Could not load page script: ${sourceScript.src}`);
                        resolve();
                    };
                    pageScriptsRoot.appendChild(script);
                });
            } else {
                // A fresh function scope prevents page-level const declarations from colliding on repeat visits.
                try {
                    new Function(sourceScript.textContent)();
                } catch (error) {
                    console.error('Could not initialize the current page.', error);
                }
            }
        }
    };

    const navigate = async (destination, { push = true, scrollY = window.scrollY } = {}) => {
        const url = new URL(destination, window.location.href);
        if (url.origin !== window.location.origin) {
            window.location.assign(url.href);
            return;
        }

        if (activeRequest) activeRequest.abort();
        activeRequest = new AbortController();
        const requestId = ++navigationId;
        document.body.classList.add('app-navigating');
        contentRoot.setAttribute('aria-busy', 'true');

        try {
            const response = await fetch(url.href, {
                method: 'GET',
                credentials: 'same-origin',
                signal: activeRequest.signal,
                headers: { Accept: 'text/html', 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!response.ok) throw new Error(`Navigation returned ${response.status}`);

            const html = await response.text();
            const documentPage = new DOMParser().parseFromString(html, 'text/html');
            const nextContent = documentPage.querySelector('.main-content .container-fluid');

            if (!nextContent || !updateShell(documentPage)) {
                window.location.assign(response.url || url.href);
                return;
            }

            const pageUrl = response.url || url.href;
            if (push) {
                window.history.replaceState({ ...window.history.state, scrollY: window.scrollY }, '', window.location.href);
                window.history.pushState({ scrollY }, '', pageUrl);
            }

            window.dispatchEvent(new CustomEvent('app:navigation', {
                detail: { title: navbarMount.dataset.title, path: window.location.pathname },
            }));

            document.querySelectorAll('.modal-backdrop').forEach((backdrop) => backdrop.remove());
            document.body.classList.remove('modal-open');
            contentRoot.innerHTML = nextContent.innerHTML;
            await runPageScripts(documentPage);

            if (requestId !== navigationId) return;
            window.scrollTo(0, scrollY);
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Fast page navigation failed; using a regular page load.', error);
                window.location.assign(url.href);
            }
        } finally {
            if (requestId === navigationId) {
                document.body.classList.remove('app-navigating');
                contentRoot.removeAttribute('aria-busy');
                activeRequest = null;
            }
        }
    };

    window.navigateApp = (destination) => navigate(destination, { push: true, scrollY: window.scrollY });
    window.refreshAppPage = () => navigate(window.location.href, { push: false, scrollY: window.scrollY });

    document.addEventListener('click', (event) => {
        if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

        const link = event.target.closest('a[href]');
        if (!link || link.target || link.hasAttribute('download') || link.hasAttribute('data-bs-toggle') || link.getAttribute('href')?.startsWith('#')) return;

        const url = new URL(link.href, window.location.href);
        if (url.origin !== window.location.origin || url.pathname === window.location.pathname && url.search === window.location.search) return;

        event.preventDefault();
        window.navigateApp(url.href);
    });

    window.addEventListener('popstate', (event) => {
        navigate(window.location.href, { push: false, scrollY: event.state?.scrollY ?? 0 });
    });

    window.addEventListener('scroll', () => {
        if (scrollUpdateFrame) return;
        scrollUpdateFrame = window.requestAnimationFrame(() => {
            window.history.replaceState({ ...window.history.state, scrollY: window.scrollY }, '', window.location.href);
            scrollUpdateFrame = null;
        });
    }, { passive: true });

    document.addEventListener('input', (event) => {
        const input = event.target.closest('[data-table-search]');
        if (!input) return;

        const table = document.querySelector(input.dataset.tableSearch);
        if (!table) return;

        const query = input.value.trim().toLocaleLowerCase('id-ID');
        table.querySelectorAll('tbody tr').forEach((row) => {
            row.hidden = query !== '' && !row.textContent.toLocaleLowerCase('id-ID').includes(query);
        });
    });
}
