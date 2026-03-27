document.addEventListener('DOMContentLoaded', () => {
    const root = document.documentElement;
    const body = document.body;
    const themeToggle = document.getElementById('theme-toggle');
    const savedTheme = localStorage.getItem('pes-theme');
    const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const navbar = document.getElementById('portal-navbar');
    const progress = document.getElementById('scroll-progress');
    const scrollScenes = Array.from(document.querySelectorAll('[data-scroll-scene]'));
    let scrollTicking = false;

    const applyTheme = (theme) => {
        root.setAttribute('data-bs-theme', theme);
        localStorage.setItem('pes-theme', theme);
        if (themeToggle) {
            themeToggle.innerHTML = theme === 'dark' ? '<i class="bi bi-sun-fill"></i>' : '<i class="bi bi-moon-stars-fill"></i>';
        }
    };

    if (body?.classList.contains('admin-auth-page') || body?.classList.contains('admin-dashboard-page')) {
        root.setAttribute('data-bs-theme', 'light');
    } else {
        applyTheme(savedTheme || (systemDark ? 'dark' : 'light'));
    }

    themeToggle?.addEventListener('click', () => {
        applyTheme(root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark');
    });

    const onScroll = () => {
        const scrollTop = window.scrollY;
        const pageHeight = document.documentElement.scrollHeight - window.innerHeight;
        if (navbar) navbar.classList.toggle('scrolled', scrollTop > 40);
        if (progress) progress.style.width = `${pageHeight > 0 ? (scrollTop / pageHeight) * 100 : 0}%`;

        if (!scrollTicking) {
            scrollTicking = true;
            window.requestAnimationFrame(() => {
                scrollScenes.forEach((scene) => {
                    if (!(scene instanceof HTMLElement)) return;

                    const rect = scene.getBoundingClientRect();
                    const viewport = window.innerHeight || 1;
                    const total = rect.height + viewport;
                    const progressValue = Math.min(Math.max((viewport - rect.top) / total, 0), 1);
                    const enterValue = Math.min(Math.max((viewport - rect.top) / (viewport * 0.9), 0), 1);

                    scene.style.setProperty('--scene-progress', progressValue.toFixed(4));
                    scene.style.setProperty('--scene-enter', enterValue.toFixed(4));
                });

                scrollTicking = false;
            });
        }
    };

    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll);

    const mainNav = document.getElementById('mainNav');
    const navDropdowns = document.querySelectorAll('.portal-nav-dropdown');

    navDropdowns.forEach((dropdown) => {
        const toggle = dropdown.querySelector('.portal-nav-toggle');

        toggle?.addEventListener('click', () => {
            if (window.innerWidth >= 992) {
                dropdown.classList.toggle('is-open');
                toggle.setAttribute('aria-expanded', dropdown.classList.contains('is-open') ? 'true' : 'false');
                return;
            }

            navDropdowns.forEach((other) => {
                if (other !== dropdown) {
                    other.classList.remove('is-open');
                    other.querySelector('.portal-nav-toggle')?.setAttribute('aria-expanded', 'false');
                }
            });

            dropdown.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', dropdown.classList.contains('is-open') ? 'true' : 'false');
        });
    });

    document.addEventListener('click', (event) => {
        if (!(event.target instanceof Element)) return;

        navDropdowns.forEach((dropdown) => {
            if (dropdown.contains(event.target)) return;

            dropdown.classList.remove('is-open');
            dropdown.querySelector('.portal-nav-toggle')?.setAttribute('aria-expanded', 'false');
        });
    });

    const aboutShell = document.getElementById('about-horizontal-shell');
    const aboutStage = document.getElementById('about-horizontal-stage');
    const aboutTrack = document.getElementById('about-horizontal-track');
    const aboutPanels = Array.from(document.querySelectorAll('.about-panel[data-about-panel]'));
    const aboutPanelLinks = document.querySelectorAll('a[data-about-panel]');
    const getNavbarOffset = () => (navbar instanceof HTMLElement ? navbar.offsetHeight : 0);
    const getAboutPanelTargetTop = (panelName) => {
        const panel = panelName ? aboutPanels.find((item) => item.getAttribute('data-about-panel') === panelName) : null;

        if (!(panel instanceof HTMLElement)) {
            return null;
        }

        if (window.innerWidth >= 992 && aboutShell instanceof HTMLElement) {
            const panelIndex = aboutPanels.indexOf(panel);
            const panelStep = window.innerHeight || 1;
            const threshold = panelStep * 1.8;

            return aboutShell.offsetTop + (panelIndex * threshold);
        }

        return window.scrollY + panel.getBoundingClientRect().top - getNavbarOffset() - 12;
    };

    const scrollToAboutPanel = (panelName) => {
        const targetTop = getAboutPanelTargetTop(panelName);

        if (targetTop === null) {
            return false;
        }

        window.scrollTo({
            top: Math.max(targetTop, 0),
            behavior: 'smooth',
        });

        return true;
    };

    const syncAboutScroll = () => {
        if (!(aboutShell instanceof HTMLElement) || !(aboutStage instanceof HTMLElement) || !(aboutTrack instanceof HTMLElement)) {
            return;
        }

        const desktop = window.innerWidth >= 992;

        if (!desktop) {
            aboutShell.style.height = 'auto';
            aboutTrack.style.transform = 'translate3d(0, 0, 0)';
            return;
        }

        const maxIndex = Math.max(aboutPanels.length - 1, 0);
        const panelStep = window.innerHeight || 1;
        const threshold = panelStep * 1.8;
        const totalScroll = maxIndex * threshold;
        aboutShell.style.height = `${window.innerHeight + totalScroll}px`;

        const start = aboutShell.offsetTop;
        const progress = Math.min(Math.max(window.scrollY - start, 0), totalScroll);
        const activeIndex = Math.min(maxIndex, Math.floor(progress / threshold));
        const translateX = activeIndex * window.innerWidth;

        aboutTrack.style.transform = `translate3d(${-translateX}px, 0, 0)`;
    };

    syncAboutScroll();
    window.addEventListener('scroll', syncAboutScroll, { passive: true });
    window.addEventListener('resize', syncAboutScroll);

    aboutPanelLinks.forEach((link) => {
        link.addEventListener('click', (event) => {
            const panelName = link.getAttribute('data-about-panel');
            if (!panelName) return;

            const didScroll = scrollToAboutPanel(panelName);

            if (!didScroll) {
                return;
            }

            event.preventDefault();

            window.history.replaceState(null, '', link.getAttribute('href') || '#about');
        });
    });

    document.querySelectorAll('#mainNav .nav-link, #mainNav .portal-nav-menu-link').forEach((link) => {
        link.addEventListener('click', () => {
            if (window.innerWidth >= 992 || !mainNav || !mainNav.classList.contains('show') || !window.bootstrap?.Collapse) {
                return;
            }

            window.bootstrap.Collapse.getOrCreateInstance(mainNav).hide();
        });
    });

    const assistantToggle = document.getElementById('assistant-toggle');
    const assistantPanel = document.getElementById('assistant-panel');
    const assistantClose = document.getElementById('assistant-close');
    const assistantForm = document.getElementById('assistant-form');
    const assistantInput = document.getElementById('assistant-input');
    const assistantBody = document.getElementById('assistant-body');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const setAssistantOpen = (open) => assistantPanel?.classList.toggle('d-none', !open);
    const addBubble = (text, role) => {
        if (!assistantBody) return;
        const bubble = document.createElement('div');
        bubble.className = `assistant-bubble assistant-bubble-${role}`;
        bubble.textContent = text;
        assistantBody.appendChild(bubble);
        assistantBody.scrollTop = assistantBody.scrollHeight;
    };

    assistantToggle?.addEventListener('click', () => setAssistantOpen(assistantPanel?.classList.contains('d-none')));
    assistantClose?.addEventListener('click', () => setAssistantOpen(false));

    document.querySelectorAll('.assistant-suggestion').forEach((button) => {
        button.addEventListener('click', () => {
            if (assistantInput instanceof HTMLInputElement) {
                assistantInput.value = button.textContent.trim();
                assistantForm?.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
            }
        });
    });

    assistantForm?.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!(assistantInput instanceof HTMLInputElement) || !assistantInput.value.trim()) return;

        const message = assistantInput.value.trim();
        assistantInput.value = '';
        addBubble(message, 'user');

        try {
            const response = await fetch('/assistant', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: JSON.stringify({ message }),
            });
            const data = await response.json();
            addBubble(data.reply || 'I could not process that request.', 'model');
        } catch {
            addBubble('I am having trouble connecting right now. Please try again later.', 'model');
        }
    });

    const dxTabs = document.querySelectorAll('[data-dx-tab]');
    const dxPanels = document.querySelectorAll('[data-dx-panel]');
    const dxSection = document.getElementById('dost-dx');

    if (dxSection instanceof HTMLElement) {
        if ('IntersectionObserver' in window) {
            const dxRevealObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;

                    dxSection.classList.add('is-revealed');
                    observer.unobserve(entry.target);
                });
            }, {
                threshold: 0.18,
                rootMargin: '0px 0px -10% 0px',
            });

            dxRevealObserver.observe(dxSection);
        } else {
            dxSection.classList.add('is-revealed');
        }
    }

    const setDxTab = (tabName) => {
        dxTabs.forEach((tab) => {
            const active = tab.getAttribute('data-dx-tab') === tabName;
            tab.classList.toggle('is-active', active);
            tab.setAttribute('aria-pressed', active ? 'true' : 'false');
        });

        dxPanels.forEach((panel) => {
            const active = panel.getAttribute('data-dx-panel') === tabName;
            panel.classList.toggle('is-active', active);
            panel.toggleAttribute('hidden', !active);
        });
    };

    dxTabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            const tabName = tab.getAttribute('data-dx-tab');
            if (tabName) setDxTab(tabName);
        });
    });

    const dxOverviewModal = document.getElementById('dxOverviewModal');
    let pendingDxAction = null;

    document.querySelectorAll('[data-dx-modal-action="domains"]').forEach((button) => {
        button.addEventListener('click', () => {
            pendingDxAction = 'domains';

            if (dxOverviewModal && window.bootstrap?.Modal) {
                window.bootstrap.Modal.getOrCreateInstance(dxOverviewModal).hide();
                return;
            }

            setDxTab('domains');
            const target = document.getElementById('dx-content');
            target?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    dxOverviewModal?.addEventListener('hidden.bs.modal', () => {
        if (pendingDxAction !== 'domains') return;

        pendingDxAction = null;
        setDxTab('domains');

        const target = document.getElementById('dx-content');
        target?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});
