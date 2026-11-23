import React, { useEffect, useMemo, useState } from 'react';
import { createRoot } from 'react-dom/client';

const parseNavigation = (element) => {
    try {
        return JSON.parse(element?.dataset.navigation || '{}');
    } catch (error) {
        console.error('Navigation data is invalid.', error);
        return {};
    }
};

function Sidebar({ mount }) {
    const [navigationVersion, setNavigationVersion] = useState(0);
    const groups = useMemo(() => parseNavigation(mount), [mount, navigationVersion]);
    const [query, setQuery] = useState('');
    const [expanded, setExpanded] = useState(true);
    const normalizedQuery = query.trim().toLocaleLowerCase('id-ID');

    useEffect(() => {
        const focusMenuSearch = (event) => {
            if (event.key === '/' && !['INPUT', 'TEXTAREA'].includes(document.activeElement?.tagName)) {
                event.preventDefault();
                mount.querySelector('input[type="search"]')?.focus();
            }
        };
        window.addEventListener('keydown', focusMenuSearch);
        return () => window.removeEventListener('keydown', focusMenuSearch);
    }, [mount]);

    useEffect(() => {
        const refreshNavigation = () => setNavigationVersion((version) => version + 1);
        window.addEventListener('app:navigation', refreshNavigation);
        return () => window.removeEventListener('app:navigation', refreshNavigation);
    }, []);

    return (
        <div className="react-sidebar">
            <header className="react-sidebar-header">
                <a className="react-brand" href={Object.values(groups).flat()[0]?.href || '/dashboard'}>
                    <span className="react-brand-mark"><i className="fas fa-futbol" aria-hidden="true" /></span>
                    <span className="react-brand-copy"><strong>{mount.dataset.brand}</strong><small>OPERATIONS DESK</small></span>
                </a>
                <button type="button" className="sidebar-toggle" aria-label="Tutup menu" onClick={() => window.toggleSidebar?.()}>
                    <i className="fas fa-xmark" aria-hidden="true" />
                </button>
            </header>

            <div className="react-workspace-pill"><span className="react-online-dot" /><span>Kasir sedang aktif</span><span className="react-role">{mount.dataset.role}</span></div>

            <label className="react-nav-search">
                <i className="fas fa-search" aria-hidden="true" />
                <input type="search" value={query} onChange={(event) => setQuery(event.target.value)} placeholder="Cari menu..." aria-label="Cari menu navigasi" />
                <kbd>/</kbd>
            </label>

            <nav className="react-nav" aria-label="Navigasi utama">
                {Object.entries(groups).map(([group, items]) => {
                    const visibleItems = items.filter((item) => !normalizedQuery || item.label.toLocaleLowerCase('id-ID').includes(normalizedQuery));
                    if (!visibleItems.length) return null;
                    return (
                        <section className="react-nav-group" key={group}>
                            <div className="react-nav-label">{group}</div>
                            {visibleItems.map((item) => item.restricted ? (
                                <div key={item.href} className="nav-link is-restricted" aria-disabled="true">
                                    <i className={item.icon} aria-hidden="true" />
                                    <span className="restricted-copy"><span>{item.label}</span><small>Hanya admin yang dapat mengakses</small></span>
                                    <i className="fas fa-lock restricted-lock" aria-hidden="true" />
                                </div>
                            ) : (
                                <a key={item.href} href={item.href} className={`nav-link ${item.active ? 'active' : ''}`} aria-current={item.active ? 'page' : undefined} onClick={() => window.innerWidth <= 768 && document.getElementById('sidebar')?.classList.remove('show')}>
                                    <i className={item.icon} aria-hidden="true" /><span>{item.label}</span>
                                    {item.label === 'Transaksi Baru' && <span className="react-nav-plus">+</span>}
                                </a>
                            ))}
                        </section>
                    );
                })}
                {normalizedQuery && !Object.values(groups).flat().some((item) => item.label.toLocaleLowerCase('id-ID').includes(normalizedQuery)) && <p className="react-nav-empty">Menu tidak ditemukan.</p>}
            </nav>

            <footer className="react-sidebar-footer">
                <button type="button" className="react-collapse-button" onClick={() => { setExpanded(!expanded); document.getElementById('sidebar')?.classList.toggle('collapsed'); }} aria-label={expanded ? 'Ciutkan menu' : 'Perluas menu'}>
                    <i className={`fas ${expanded ? 'fa-angles-left' : 'fa-angles-right'}`} aria-hidden="true" /><span>{expanded ? 'Ciutkan menu' : 'Perluas menu'}</span>
                </button>
                <form method="POST" action={mount.dataset.logout}>
                    <input type="hidden" name="_token" value={mount.dataset.csrf} />
                    <button className="react-logout" type="submit"><i className="fas fa-arrow-right-from-bracket" aria-hidden="true" /><span>Keluar dari akun</span></button>
                </form>
            </footer>
        </div>
    );
}

function Topbar({ mount }) {
    const [now, setNow] = useState(() => new Date());
    const [pageTitle, setPageTitle] = useState(mount.dataset.title);
    const [currentPath, setCurrentPath] = useState(window.location.pathname);
    useEffect(() => {
        const timer = window.setInterval(() => setNow(new Date()), 60_000);
        return () => window.clearInterval(timer);
    }, []);
    useEffect(() => {
        const updatePage = (event) => {
            setPageTitle(mount.dataset.title);
            setCurrentPath(event.detail?.path || window.location.pathname);
        };
        window.addEventListener('app:navigation', updatePage);
        return () => window.removeEventListener('app:navigation', updatePage);
    }, [mount]);

    const dateLabel = new Intl.DateTimeFormat('id-ID', { timeZone: 'Asia/Jakarta', weekday: 'long', day: 'numeric', month: 'long' }).format(now);
    const timeLabel = new Intl.DateTimeFormat('id-ID', { timeZone: 'Asia/Jakarta', hour: '2-digit', minute: '2-digit' }).format(now);

    return (
        <header className="top-navbar react-topbar">
            <div className="react-topbar-title"><button className="react-mobile-menu" type="button" aria-label="Buka menu" onClick={() => window.toggleSidebar?.()}><i className="fas fa-bars" aria-hidden="true" /></button><div><h1>{pageTitle}</h1><p>{dateLabel} | {timeLabel} WIB</p></div></div>
            <div className="react-topbar-actions">
                <button type="button" className="react-command-trigger" onClick={() => window.dispatchEvent(new KeyboardEvent('keydown', { key: 'k', ctrlKey: true }))}><i className="fas fa-search" aria-hidden="true" /><span>Cari halaman</span><kbd>Ctrl K</kbd></button>
                {!currentPath.includes('/transactions/create') && <a href={mount.dataset.newTransaction} className="btn btn-primary react-new-sale"><i className="fas fa-plus me-2" aria-hidden="true" />Transaksi baru</a>}
                <span className="react-topbar-divider" />
                <div className="react-user"><span className="user-avatar">{mount.dataset.initial}</span><span className="react-user-copy"><strong>{mount.dataset.user}</strong><small>{mount.dataset.role}</small></span><i className="fas fa-chevron-down react-user-chevron" aria-hidden="true" /></div>
            </div>
        </header>
    );
}

function CommandPalette({ mount }) {
    const [navigationVersion, setNavigationVersion] = useState(0);
    const groups = useMemo(() => parseNavigation(mount), [mount, navigationVersion]);
    const links = Object.values(groups).flat();
    const [open, setOpen] = useState(false);
    const [query, setQuery] = useState('');
    const matches = links.filter((item) => !item.restricted && item.label.toLocaleLowerCase('id-ID').includes(query.trim().toLocaleLowerCase('id-ID')));
    const [selected, setSelected] = useState(0);

    useEffect(() => {
        const handleKey = (event) => {
            if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
                event.preventDefault();
                setOpen((value) => !value);
                setQuery('');
                setSelected(0);
            }
            if (event.key === 'Escape') setOpen(false);
        };
        window.addEventListener('keydown', handleKey);
        return () => window.removeEventListener('keydown', handleKey);
    }, []);
    useEffect(() => {
        const refreshNavigation = () => setNavigationVersion((version) => version + 1);
        window.addEventListener('app:navigation', refreshNavigation);
        return () => window.removeEventListener('app:navigation', refreshNavigation);
    }, []);

    if (!open) return null;
    return (
        <div className="react-command-backdrop" role="presentation" onMouseDown={(event) => event.target === event.currentTarget && setOpen(false)}>
            <section className="react-command-palette" role="dialog" aria-modal="true" aria-labelledby="command-title" onKeyDown={(event) => {
                if (event.key === 'ArrowDown') { event.preventDefault(); setSelected((index) => (index + 1) % Math.max(matches.length, 1)); }
                if (event.key === 'ArrowUp') { event.preventDefault(); setSelected((index) => (index - 1 + Math.max(matches.length, 1)) % Math.max(matches.length, 1)); }
                if (event.key === 'Enter' && matches[selected]) { window.navigateApp?.(matches[selected].href); setOpen(false); }
            }}>
                <div className="react-command-input"><i className="fas fa-search" aria-hidden="true" /><input autoFocus value={query} onChange={(event) => { setQuery(event.target.value); setSelected(0); }} placeholder="Cari halaman atau fitur..." aria-label="Cari halaman" /><kbd>ESC</kbd></div>
                <div className="react-command-caption" id="command-title">NAVIGASI CEPAT</div>
                <div className="react-command-results">{matches.map((item, index) => <a href={item.href} className={`react-command-item ${index === selected ? 'is-selected' : ''}`} key={item.href} onClick={() => setOpen(false)}><span className="react-command-icon"><i className={item.icon} aria-hidden="true" /></span><span>{item.label}</span><i className="fas fa-arrow-up-right-from-square ms-auto" aria-hidden="true" /></a>)}{!matches.length && <p className="react-command-empty">Tidak ada halaman yang cocok.</p>}</div>
                <footer className="react-command-footer"><span><kbd>UP</kbd><kbd>DOWN</kbd> untuk memilih</span><span><kbd>Enter</kbd> untuk membuka</span></footer>
            </section>
        </div>
    );
}

document.querySelectorAll('#react-sidebar-root').forEach((mount) => createRoot(mount).render(<Sidebar mount={mount} />));
document.querySelectorAll('#react-navbar-root').forEach((mount) => createRoot(mount).render(<Topbar mount={mount} />));
document.querySelectorAll('#react-command-root').forEach((mount) => createRoot(mount).render(<CommandPalette mount={mount} />));
