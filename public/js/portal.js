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
    const getNavbarOffset = () => (navbar instanceof HTMLElement ? navbar.offsetHeight : 0);
    const getStandardTargetTop = (target) => {
        if (!(target instanceof HTMLElement)) {
            return null;
        }

        return window.scrollY + target.getBoundingClientRect().top - getNavbarOffset() - 12;
    };

    const scrollToHashTarget = (hash, behavior = 'smooth') => {
        if (!hash) {
            return false;
        }

        const normalizedHash = hash.startsWith('#') ? hash : `#${hash}`;
        const panelLink = document.querySelector(`a[data-about-panel][href$="${normalizedHash}"]`);
        const panelName = panelLink instanceof HTMLElement ? panelLink.getAttribute('data-about-panel') : null;

        if (panelName) {
            return scrollToAboutPanel(panelName, behavior);
        }

        const target = document.getElementById(normalizedHash.slice(1));
        const targetTop = getStandardTargetTop(target);

        if (targetTop === null) {
            return false;
        }

        window.scrollTo({
            top: Math.max(targetTop, 0),
            behavior,
        });

        return true;
    };
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

    const scrollToAboutPanel = (panelName, behavior = 'smooth') => {
        const targetTop = getAboutPanelTargetTop(panelName);

        if (targetTop === null) {
            return false;
        }

        window.scrollTo({
            top: Math.max(targetTop, 0),
            behavior,
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

    document.querySelectorAll('a[href*="#"]').forEach((link) => {
        link.addEventListener('click', (event) => {
            if (!(link instanceof HTMLAnchorElement)) {
                return;
            }

            const href = link.getAttribute('href') || '';

            if (!href.includes('#')) {
                return;
            }

            const url = new URL(href, window.location.href);
            const samePage = url.origin === window.location.origin && url.pathname === window.location.pathname;

            if (!samePage || !url.hash) {
                return;
            }

            const didScroll = scrollToHashTarget(url.hash);

            if (!didScroll) {
                return;
            }

            event.preventDefault();
            window.history.replaceState(null, '', `${url.pathname}${url.hash}`);
        });
    });

    if (window.location.hash) {
        window.requestAnimationFrame(() => {
            window.requestAnimationFrame(() => {
                scrollToHashTarget(window.location.hash, 'auto');
            });
        });
    }

    document.querySelectorAll('#mainNav .nav-link, #mainNav .portal-nav-menu-link').forEach((link) => {
        link.addEventListener('click', () => {
            if (window.innerWidth >= 992 || !mainNav || !mainNav.classList.contains('show') || !window.bootstrap?.Collapse) {
                return;
            }

            window.bootstrap.Collapse.getOrCreateInstance(mainNav).hide();
        });
    });

    const issuanceForm = document.querySelector('[data-issuance-form]');
    const issuanceSearchInput = document.querySelector('[data-issuance-search]');
    const issuanceCategorySelect = document.querySelector('[data-issuance-category]');
    const issuanceApplyButton = document.querySelector('[data-issuance-apply]');
    const issuanceRows = Array.from(document.querySelectorAll('[data-issuance-row]'));
    const issuanceEmptyRow = document.querySelector('[data-issuance-empty-row]');

    const filterIssuanceRows = () => {
        if (!(issuanceSearchInput instanceof HTMLInputElement) || !(issuanceCategorySelect instanceof HTMLSelectElement)) {
            return;
        }

        const query = issuanceSearchInput.value.trim().toLowerCase();
        const selectedCategory = issuanceCategorySelect.value.trim().toLowerCase();
        let visibleCount = 0;

        issuanceRows.forEach((row) => {
            if (!(row instanceof HTMLElement)) {
                return;
            }

            const rowSearchText = (row.dataset.issuanceSearch || '').toLowerCase();
            const rowCategory = (row.dataset.issuanceCategory || '').toLowerCase();
            const matchesQuery = query === '' || rowSearchText.includes(query);
            const matchesCategory = selectedCategory === '' || selectedCategory === 'all' || rowCategory === selectedCategory;
            const visible = matchesQuery && matchesCategory;

            row.hidden = !visible;

            if (visible) {
                visibleCount += 1;
            }
        });

        if (issuanceEmptyRow instanceof HTMLElement) {
            issuanceEmptyRow.hidden = visibleCount > 0;
        }
    };

    issuanceForm?.addEventListener('submit', (event) => {
        event.preventDefault();
        filterIssuanceRows();
    });

    issuanceSearchInput?.addEventListener('input', filterIssuanceRows);
    issuanceSearchInput?.addEventListener('search', filterIssuanceRows);
    issuanceCategorySelect?.addEventListener('change', filterIssuanceRows);
    issuanceApplyButton?.addEventListener('click', filterIssuanceRows);

    filterIssuanceRows();

    const dxLibraryForm = document.querySelector('[data-dx-library-form]');
    const dxLibrarySearchInput = document.querySelector('[data-dx-library-search]');
    const dxLibraryApplyButton = document.querySelector('[data-dx-library-apply]');
    const dxLibraryRows = Array.from(document.querySelectorAll('[data-dx-library-row]'));
    const dxLibraryEmptyRow = document.querySelector('[data-dx-library-empty-row]');

    const dxLibraryMatchesSearch = (row, query) => {
        if (!(row instanceof HTMLElement)) {
            return false;
        }

        if (query === '') {
            return true;
        }

        const code = (row.dataset.dxLibraryCode || '').toLowerCase();
        const title = (row.dataset.dxLibraryTitle || '').toLowerCase();
        const program = (row.dataset.dxLibraryProgram || '').toLowerCase();
        const searchText = (row.dataset.dxLibrarySearch || '').toLowerCase();
        const titleWords = title.split(/\s+/).filter(Boolean);

        if (code !== '' && code.startsWith(query)) {
            return true;
        }

        if (title !== '' && title.startsWith(query)) {
            return true;
        }

        if (program !== '' && program.startsWith(query)) {
            return true;
        }

        for (const word of titleWords) {
            if (word.startsWith(query)) {
                return true;
            }
        }

        return searchText.includes(query);
    };

    const filterDxLibraryRows = () => {
        if (!(dxLibrarySearchInput instanceof HTMLInputElement)) {
            return;
        }

        const query = dxLibrarySearchInput.value.trim().toLowerCase();
        let visibleCount = 0;

        dxLibraryRows.forEach((row) => {
            if (!(row instanceof HTMLElement)) {
                return;
            }

            const visible = dxLibraryMatchesSearch(row, query);
            row.hidden = !visible;

            if (visible) {
                visibleCount += 1;
            }
        });

        if (dxLibraryEmptyRow instanceof HTMLElement) {
            dxLibraryEmptyRow.hidden = visibleCount > 0;
        }
    };

    dxLibraryForm?.addEventListener('submit', (event) => {
        event.preventDefault();
        filterDxLibraryRows();
    });

    dxLibrarySearchInput?.addEventListener('input', filterDxLibraryRows);
    dxLibrarySearchInput?.addEventListener('search', filterDxLibraryRows);
    dxLibraryApplyButton?.addEventListener('click', filterDxLibraryRows);

    filterDxLibraryRows();

    const adminIssuanceLibraryForm = document.querySelector('[data-admin-issuance-library-form]');
    const adminIssuanceLibrarySearch = document.querySelector('[data-admin-issuance-library-search]');
    const adminIssuanceLibraryApply = document.querySelector('[data-admin-issuance-library-apply]');
    const adminIssuanceLibraryRows = Array.from(document.querySelectorAll('[data-admin-issuance-library-row]'));
    const adminIssuanceLibraryEmpty = document.querySelector('[data-admin-issuance-library-empty-row]');

    const matchesStartOrContains = (searchText, query) => {
        const normalized = (searchText || '').toLowerCase().trim();
        if (query === '') {
            return true;
        }
        if (normalized.startsWith(query)) {
            return true;
        }
        const words = normalized.split(/\s+/).filter(Boolean);
        if (words.some((word) => word.startsWith(query))) {
            return true;
        }
        return normalized.includes(query);
    };

    const filterAdminIssuanceLibraryRows = () => {
        if (!(adminIssuanceLibrarySearch instanceof HTMLInputElement)) {
            return;
        }

        const query = adminIssuanceLibrarySearch.value.trim().toLowerCase();
        let visibleCount = 0;

        adminIssuanceLibraryRows.forEach((row) => {
            if (!(row instanceof HTMLElement)) return;

            const searchText = (row.dataset.adminIssuanceLibrarySearch || '').toLowerCase();
            const visible = matchesStartOrContains(searchText, query);
            row.hidden = !visible;

            if (visible) visibleCount += 1;
        });

        if (adminIssuanceLibraryEmpty instanceof HTMLElement) {
            adminIssuanceLibraryEmpty.hidden = visibleCount > 0;
        }
    };

    adminIssuanceLibraryForm?.addEventListener('submit', (event) => {
        event.preventDefault();
        filterAdminIssuanceLibraryRows();
    });
    adminIssuanceLibrarySearch?.addEventListener('input', filterAdminIssuanceLibraryRows);
    adminIssuanceLibrarySearch?.addEventListener('search', filterAdminIssuanceLibraryRows);
    adminIssuanceLibraryApply?.addEventListener('click', filterAdminIssuanceLibraryRows);
    filterAdminIssuanceLibraryRows();

    const materialLibraryForm = document.querySelector('[data-material-library-form]');
    const materialLibrarySearch = document.querySelector('[data-material-library-search]');
    const materialLibraryApply = document.querySelector('[data-material-library-apply]');
    const materialLibraryRows = Array.from(document.querySelectorAll('[data-material-library-row]'));
    const materialLibraryEmpty = document.querySelector('[data-material-library-empty-row]');
    const newsLibraryForm = document.querySelector('[data-news-library-form]');
    const newsLibrarySearch = document.querySelector('[data-news-library-search]');
    const newsLibraryApply = document.querySelector('[data-news-library-apply]');
    const newsLibraryRows = Array.from(document.querySelectorAll('[data-news-library-row]'));
    const newsLibraryEmpty = document.querySelector('[data-news-library-empty-row]');

    const filterMaterialLibraryRows = () => {
        if (!(materialLibrarySearch instanceof HTMLInputElement)) {
            return;
        }

        const query = materialLibrarySearch.value.trim().toLowerCase();
        let visibleCount = 0;

        materialLibraryRows.forEach((row) => {
            if (!(row instanceof HTMLElement)) return;

            const searchText = (row.dataset.materialLibrarySearch || '').toLowerCase();
            const visible = matchesStartOrContains(searchText, query);
            row.hidden = !visible;

            if (visible) visibleCount += 1;
        });

        if (materialLibraryEmpty instanceof HTMLElement) {
            materialLibraryEmpty.hidden = visibleCount > 0;
        }
    };

    materialLibraryForm?.addEventListener('submit', (event) => {
        event.preventDefault();
        filterMaterialLibraryRows();
    });
    materialLibrarySearch?.addEventListener('input', filterMaterialLibraryRows);
    materialLibrarySearch?.addEventListener('search', filterMaterialLibraryRows);
    materialLibraryApply?.addEventListener('click', filterMaterialLibraryRows);
    filterMaterialLibraryRows();

    const filterNewsLibraryRows = () => {
        if (!(newsLibrarySearch instanceof HTMLInputElement)) {
            return;
        }

        const query = newsLibrarySearch.value.trim().toLowerCase();
        let visibleCount = 0;

        newsLibraryRows.forEach((row) => {
            if (!(row instanceof HTMLElement)) return;

            const searchText = (row.dataset.newsLibrarySearch || '').toLowerCase();
            const visible = matchesStartOrContains(searchText, query);
            row.hidden = !visible;

            if (visible) visibleCount += 1;
        });

        if (newsLibraryEmpty instanceof HTMLElement) {
            newsLibraryEmpty.hidden = visibleCount > 0;
        }
    };

    newsLibraryForm?.addEventListener('submit', (event) => {
        event.preventDefault();
        filterNewsLibraryRows();
    });
    newsLibrarySearch?.addEventListener('input', filterNewsLibraryRows);
    newsLibrarySearch?.addEventListener('search', filterNewsLibraryRows);
    newsLibraryApply?.addEventListener('click', filterNewsLibraryRows);
    filterNewsLibraryRows();

    document.querySelectorAll('[data-project-chart]').forEach((chart) => {
        if (!(chart instanceof HTMLElement)) return;

        const centerValue = chart.querySelector('[data-project-center-value]');
        const centerLabel = chart.querySelector('[data-project-center-label]');
        const centerPercent = chart.querySelector('[data-project-center-percent]');
        const tooltip = chart.querySelector('[data-project-tooltip]');
        const tooltipValue = chart.querySelector('[data-project-tooltip-value]');
        const tooltipLabel = chart.querySelector('[data-project-tooltip-label]');
        const segments = Array.from(chart.querySelectorAll('[data-project-segment]'));
        const triggers = Array.from(chart.querySelectorAll('[data-project-trigger]'));

        const activateProjectState = (source) => {
            if (!(source instanceof HTMLElement)) return;

            const value = source.dataset.projectValue || '';
            const label = source.dataset.projectLabel || '';
            const percent = source.dataset.projectPercent || '';
            const color = source.getAttribute('stroke') || source.style.getPropertyValue('--legend-color') || '#1fb6ff';

            if (centerValue instanceof HTMLElement) centerValue.textContent = value;
            if (centerLabel instanceof HTMLElement) centerLabel.textContent = label;
            if (centerPercent instanceof HTMLElement) centerPercent.textContent = percent;
            if (tooltipValue instanceof HTMLElement) tooltipValue.textContent = value;
            if (tooltipLabel instanceof HTMLElement) tooltipLabel.textContent = label;
            if (tooltip instanceof HTMLElement) {
                tooltip.style.background = `linear-gradient(135deg, ${color}, ${color}dd)`;
            }

            segments.forEach((segment) => segment.classList.toggle('is-active', segment === source));
            triggers.forEach((trigger) => {
                trigger.classList.toggle(
                    'is-active',
                    trigger.dataset.projectLabel === label && trigger.dataset.projectValue === value
                );
            });
        };

        segments.forEach((segment) => {
            segment.addEventListener('mouseenter', () => activateProjectState(segment));
            segment.addEventListener('focus', () => activateProjectState(segment));
        });

        triggers.forEach((trigger) => {
            trigger.addEventListener('mouseenter', () => activateProjectState(trigger));
            trigger.addEventListener('focus', () => activateProjectState(trigger));
        });

        if (segments[0] instanceof HTMLElement) {
            activateProjectState(segments[0]);
        }
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
    const dxProgramCards = document.querySelectorAll('.dx-sub-card[data-program-slug]');

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
            if (tabName) {
                setDxTab(tabName);

                if (tabName === 'programs' && !document.querySelector('.dx-sub-card.is-active')) {
                    const firstProgram = document.querySelector('.dx-sub-card[data-program-slug]');
                    const firstSlug = firstProgram instanceof HTMLElement ? firstProgram.dataset.programSlug : '';

                    if (firstSlug) activateDxProgram(firstSlug);
                }
            }
        });
    });

    const dxMetricNumbers = document.querySelectorAll('.dx-metric-number[data-target]');

    if ('IntersectionObserver' in window) {
        const dxMetricObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;

                const el = entry.target;
                const target = Number.parseInt(el.getAttribute('data-target') || '0', 10);

                if (!Number.isFinite(target)) {
                    el.textContent = '0';
                    observer.unobserve(el);
                    return;
                }

                let start = 0;
                const duration = target > 99 ? 1100 : 800;
                const startedAt = performance.now();

                const tick = (now) => {
                    const progress = Math.min((now - startedAt) / duration, 1);
                    const eased = 1 - Math.pow(1 - progress, 3);
                    const value = Math.round(target * eased);

                    el.textContent = `${value}`;

                    if (progress < 1) {
                        window.requestAnimationFrame(tick);
                    }
                };

                window.requestAnimationFrame(tick);
                observer.unobserve(el);
            });
        }, {
            threshold: 0.5,
        });

        dxMetricNumbers.forEach((metric) => dxMetricObserver.observe(metric));
    } else {
        dxMetricNumbers.forEach((metric) => {
            metric.textContent = metric.getAttribute('data-target') || '0';
        });
    }

    const activateDxDomain = (domainSlug) => {
        dxProgramCards.forEach((card) => {
            if (!(card instanceof HTMLElement)) return;

            const inDomain = domainSlug && card.dataset.domain === domainSlug;
            card.classList.toggle('is-domain-active', inDomain);
            card.classList.toggle('is-domain-dimmed', Boolean(domainSlug) && !inDomain);
        });
    };

    const dxGoToSubProgram = (domain, programSlug) => {
        setDxTab('programs');

        window.setTimeout(() => {
            const firstInDomain = document.querySelector(`.dx-sub-card[data-domain="${domain}"]`);

            if (!domain) return;

            activateDxDomain(domain);

            if (firstInDomain instanceof HTMLElement) {
                firstInDomain.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }, 100);
    };

    window.dxGoToSubProgram = dxGoToSubProgram;

    const dxOverviewModal = document.getElementById('dxOverviewModal');
    let pendingDxAction = null;
    const dxProgramSearchForm = document.querySelector('.dx-program-page-controls');
    const dxProgramSearchInput = document.querySelector('[data-dx-project-search]');
    const dxProjectItems = Array.from(document.querySelectorAll('[data-dx-project-item]'));
    const dxProjectCount = document.querySelector('[data-dx-project-count]');
    const dxProjectEmpty = document.querySelector('[data-dx-project-empty]');

    const dxProjectMatchesSearch = (code, title, query) => {
        const normalizedQuery = query.trim().toLowerCase();

        if (normalizedQuery === '') {
            return true;
        }

        const normalizedCode = code.trim().toLowerCase();
        const normalizedTitle = title.trim().toLowerCase();
        const titleWords = normalizedTitle.split(/\s+/).filter(Boolean);

        if (normalizedCode && normalizedCode.startsWith(normalizedQuery)) {
            return true;
        }

        if (normalizedTitle && normalizedTitle.startsWith(normalizedQuery)) {
            return true;
        }

        if (titleWords.some((word) => word.startsWith(normalizedQuery))) {
            return true;
        }

        return normalizedTitle.includes(normalizedQuery);
    };

    const syncDxProjectSearch = () => {
        if (!(dxProgramSearchInput instanceof HTMLInputElement)) {
            return;
        }

        const query = dxProgramSearchInput.value;
        let visibleCount = 0;

        dxProjectItems.forEach((item) => {
            if (!(item instanceof HTMLElement)) {
                return;
            }

            const code = item.dataset.projectCode || '';
            const title = item.dataset.projectTitle || '';
            const visible = dxProjectMatchesSearch(code, title, query);

            item.hidden = !visible;

            if (visible) {
                visibleCount += 1;
            }
        });

        if (dxProjectCount instanceof HTMLElement) {
            dxProjectCount.textContent = `${visibleCount}`;
        }

        if (dxProjectEmpty instanceof HTMLElement) {
            dxProjectEmpty.hidden = visibleCount > 0;
        }

        if (dxProgramSearchForm instanceof HTMLFormElement) {
            const nextUrl = new URL(dxProgramSearchForm.action, window.location.origin);
            const normalizedQuery = query.trim();

            if (normalizedQuery !== '') {
                nextUrl.searchParams.set('search', normalizedQuery);
            }

            window.history.replaceState({}, '', normalizedQuery !== '' ? nextUrl.toString() : nextUrl.pathname);
        }
    };

    dxProgramSearchInput?.addEventListener('input', syncDxProjectSearch);
    dxProgramSearchInput?.addEventListener('search', syncDxProjectSearch);
    dxProgramSearchForm?.addEventListener('submit', (event) => {
        event.preventDefault();
        syncDxProjectSearch();
    });
    syncDxProjectSearch();

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

    document.querySelectorAll('.gates-tab').forEach(function(tab) {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.gates-tab').forEach(function(t) { t.classList.remove('is-active'); });
            tab.classList.add('is-active');
            var filter = tab.dataset.gatesFilter;
            document.querySelectorAll('.gates-card').forEach(function(card) {
                card.classList.toggle('is-hidden', filter !== 'all' && card.dataset.type !== filter);
            });
        });
    });
    
    function openGatesLightbox(src, title, desc, type) {
        var media = document.getElementById('gatesLightboxMedia');
        media.innerHTML = type === 'video'
            ? '<video src="' + src + '" controls autoplay playsinline></video>'
            : '<img src="' + src + '" alt="' + title + '">';
        document.getElementById('gatesLightboxTitle').textContent = title;
        document.getElementById('gatesLightboxDesc').textContent  = desc;
        document.getElementById('gatesLightbox').classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }
    function closeGatesLightboxDirect() {
        document.getElementById('gatesLightbox').classList.remove('is-open');
        document.getElementById('gatesLightboxMedia').innerHTML = '';
        document.body.style.overflow = '';
    }
    function closeGatesLightbox(e) {
        if (e.target === document.getElementById('gatesLightbox')) closeGatesLightboxDirect();
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeGatesLightboxDirect();
    });
});
