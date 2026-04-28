document.addEventListener('DOMContentLoaded', () => {
    const root = document.documentElement;
    const body = document.body;
    const themeToggle = document.getElementById('theme-toggle');
    const themeToggleLabel = document.getElementById('theme-toggle-label');
    const themeToggleIcon = document.getElementById('theme-toggle-icon');
    const savedTheme = localStorage.getItem('pes-theme');
    const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const navbar = document.getElementById('portal-navbar');
    const progress = document.getElementById('scroll-progress');
    const pageTransition = document.querySelector('[data-page-transition]');
    const pageTransitionLabel = document.querySelector('[data-page-transition-label]');
    const scrollScenes = Array.from(document.querySelectorAll('[data-scroll-scene]'));
    const currentPageTheme = body?.dataset.pageTheme || 'pes';
    const intro = document.getElementById('portal-intro');
    const introStage = document.getElementById('portal-intro-stage');
    const introLogoWrap = introStage instanceof HTMLElement ? introStage.querySelector('.portal-intro-logo-wrap') : null;
    const introParticles = document.getElementById('portal-intro-particles');
    const introProgressFill = document.getElementById('portal-intro-progress-fill');
    const introProgressValue = document.getElementById('portal-intro-progress-value');
    const introLoadingLabel = document.getElementById('portal-intro-loading-label');
    const introStorageKey = 'pes-home-intro-seen-session';
    let introAlreadySeen = false;
    try {
        introAlreadySeen = sessionStorage.getItem(introStorageKey) === '1';
    } catch (error) {
        introAlreadySeen = false;
    }
    const introEnabled = body?.classList.contains('portal-page-home')
        && intro instanceof HTMLElement
        && introStage instanceof HTMLElement
        && !introAlreadySeen;
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let scrollTicking = false;

    let themeAnimationTimer = null;

    const applyTheme = (theme, options = {}) => {
        const { animate = false } = options;
        root.setAttribute('data-bs-theme', theme);
        localStorage.setItem('pes-theme', theme);
        root.classList.remove('theme-switching-to-dark', 'theme-switching-to-light');
        if (themeAnimationTimer) {
            window.clearTimeout(themeAnimationTimer);
            themeAnimationTimer = null;
        }
        if (animate) {
            root.classList.add(theme === 'dark' ? 'theme-switching-to-dark' : 'theme-switching-to-light');
            themeAnimationTimer = window.setTimeout(() => {
                root.classList.remove('theme-switching-to-dark', 'theme-switching-to-light');
                themeAnimationTimer = null;
            }, 1100);
        }
        if (themeToggle) {
            const isDark = theme === 'dark';
            themeToggle.setAttribute('aria-checked', isDark ? 'true' : 'false');
            if (themeToggleLabel) {
                themeToggleLabel.textContent = isDark ? 'Light Mode' : 'Dark Mode';
            }
            if (themeToggleIcon) {
                themeToggleIcon.className = isDark ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
            }
        }
    };

    if (body?.classList.contains('admin-auth-page') || body?.classList.contains('admin-dashboard-page')) {
        root.setAttribute('data-bs-theme', 'light');
    } else {
        applyTheme(savedTheme || (systemDark ? 'dark' : 'light'));
    }

    if (introEnabled) {
        body.classList.add('intro-active');
    } else if (intro instanceof HTMLElement) {
        intro.classList.add('is-hidden');
        intro.setAttribute('aria-hidden', 'true');
        intro.style.pointerEvents = 'none';
    }

    themeToggle?.addEventListener('click', () => {
        applyTheme(root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark', { animate: true });
    });

    if (introEnabled) {
        let introAudioContext = null;
        let introAudioUnlocked = false;
        let introDismissed = false;
        let introReady = false;
        const introLabels = Array.from(intro.querySelectorAll('.portal-intro-label'));
        const introLoadingStates = [
            { progress: 18, label: 'Authenticating systems' },
            { progress: 42, label: 'Mapping service modules' },
            { progress: 67, label: 'Initializing solutions' },
            { progress: 86, label: 'Synchronizing interfaces' },
            { progress: 100, label: 'System ready' },
        ];

        const setIntroProgress = (value) => {
            const normalizedValue = Math.max(0, Math.min(100, Math.round(value)));
            const progressText = `${normalizedValue}%`;

            if (intro instanceof HTMLElement) {
                intro.style.setProperty('--intro-progress', progressText);
            }

            if (introProgressFill instanceof HTMLElement) {
                introProgressFill.style.width = progressText;
            }

            if (introProgressValue instanceof HTMLElement) {
                introProgressValue.textContent = progressText;
            }

            const state = introLoadingStates.find((item) => normalizedValue <= item.progress) || introLoadingStates[introLoadingStates.length - 1];
            if (introLoadingLabel instanceof HTMLElement && state) {
                introLoadingLabel.textContent = state.label;
            }
        };

        const completeIntroLoading = () => {
            introReady = true;
            intro.classList.add('is-ready');
            setIntroProgress(100);
        };

        const startIntroLoading = () => {
            if (reducedMotion) {
                completeIntroLoading();
                return;
            }

            const startedAt = performance.now();
            const duration = 4300;

            const tick = (now) => {
                const elapsed = now - startedAt;
                const progressValue = Math.min(elapsed / duration, 1);
                const eased = 1 - Math.pow(1 - progressValue, 3);

                setIntroProgress(eased * 100);

                if (progressValue < 1) {
                    window.requestAnimationFrame(tick);
                    return;
                }

                completeIntroLoading();
            };

            window.requestAnimationFrame(tick);
        };

        const spawnIntroParticles = () => {
            if (!(introParticles instanceof HTMLElement)) {
                return;
            }

            const particleCount = window.innerWidth < 768 ? 18 : 34;

            for (let i = 0; i < particleCount; i += 1) {
                const particle = document.createElement('span');
                particle.className = 'portal-intro-particle';
                particle.style.left = `${Math.random() * 100}%`;
                particle.style.top = `${50 + (Math.random() * 40)}%`;
                particle.style.animationDuration = `${7 + (Math.random() * 8)}s`;
                particle.style.animationDelay = `${Math.random() * 5}s`;
                particle.style.opacity = `${0.16 + (Math.random() * 0.34)}`;
                introParticles.appendChild(particle);
            }
        };

        const getIntroAudioContext = () => {
            const AudioContextClass = window.AudioContext || window.webkitAudioContext;
            if (!AudioContextClass) {
                return null;
            }

            if (!introAudioContext) {
                introAudioContext = new AudioContextClass();
            }

            return introAudioContext;
        };

        const unlockIntroAudio = async () => {
            if (introAudioUnlocked) {
                return getIntroAudioContext();
            }

            const context = getIntroAudioContext();
            if (!context) {
                return null;
            }

            if (context.state === 'suspended') {
                try {
                    await context.resume();
                } catch (error) {
                    return null;
                }
            }

            introAudioUnlocked = true;
            return context;
        };

        const playIntroTone = async (frequency, duration, volume, type = 'sine') => {
            const context = await unlockIntroAudio();
            if (!context) {
                return;
            }

            const oscillator = context.createOscillator();
            const gainNode = context.createGain();
            const now = context.currentTime;

            oscillator.type = type;
            oscillator.frequency.setValueAtTime(frequency, now);
            gainNode.gain.setValueAtTime(0.0001, now);
            gainNode.gain.exponentialRampToValueAtTime(volume, now + 0.04);
            gainNode.gain.exponentialRampToValueAtTime(0.0001, now + duration);

            oscillator.connect(gainNode);
            gainNode.connect(context.destination);
            oscillator.start(now);
            oscillator.stop(now + duration + 0.02);
        };

        const playIntroStartSound = async () => {
            await playIntroTone(392, 0.45, 0.012, 'sine');
            window.setTimeout(() => playIntroTone(523.25, 0.52, 0.01, 'triangle'), 90);
        };

        const dismissIntro = async () => {
            if (introDismissed || !introReady) {
                return;
            }

            introDismissed = true;
            try {
                sessionStorage.setItem(introStorageKey, '1');
            } catch (error) {
                // Ignore storage failures and continue dismissing the intro.
            }
            await playIntroStartSound();

            body.classList.remove('intro-active');
            intro.classList.add('is-hidden');
            intro.style.pointerEvents = 'none';

            window.setTimeout(() => {
                intro.setAttribute('aria-hidden', 'true');
                const homeTarget = document.getElementById('top');
                const homeTop = homeTarget instanceof HTMLElement
                    ? Math.max(window.scrollY + homeTarget.getBoundingClientRect().top, 0)
                    : 0;

                window.scrollTo({
                    top: homeTop,
                    behavior: reducedMotion ? 'auto' : 'smooth',
                });
            }, reducedMotion ? 0 : 120);
        };

        spawnIntroParticles();
        setIntroProgress(0);
        startIntroLoading();

        if (!reducedMotion && introStage instanceof HTMLElement && introLogoWrap instanceof HTMLElement) {
            introStage.addEventListener('pointermove', (event) => {
                const rect = introStage.getBoundingClientRect();
                const offsetX = ((event.clientX - rect.left) / rect.width) - 0.5;
                const offsetY = ((event.clientY - rect.top) / rect.height) - 0.5;
                const tiltX = offsetX * 12;
                const tiltY = offsetY * -9;
                const shiftX = offsetX * 12;
                const shiftY = offsetY * 9;

                introStage.style.setProperty('--intro-tilt-x', `${tiltX.toFixed(2)}deg`);
                introStage.style.setProperty('--intro-tilt-y', `${tiltY.toFixed(2)}deg`);
                introStage.style.setProperty('--intro-shift-x', `${shiftX.toFixed(2)}px`);
                introStage.style.setProperty('--intro-shift-y', `${shiftY.toFixed(2)}px`);

                introLabels.forEach((label) => {
                    if (!(label instanceof HTMLElement)) {
                        return;
                    }

                    const depth = Number.parseFloat(label.dataset.depth || '1');
                    const labelX = offsetX * 18 * depth;
                    const labelY = offsetY * 12 * depth;

                    label.style.setProperty('--intro-label-offset-x', `${labelX.toFixed(2)}px`);
                    label.style.setProperty('--intro-label-offset-y', `${labelY.toFixed(2)}px`);
                });
            });

            introStage.addEventListener('pointerleave', () => {
                introStage.style.setProperty('--intro-tilt-x', '0deg');
                introStage.style.setProperty('--intro-tilt-y', '0deg');
                introStage.style.setProperty('--intro-shift-x', '0px');
                introStage.style.setProperty('--intro-shift-y', '0px');
                introLabels.forEach((label) => {
                    if (!(label instanceof HTMLElement)) {
                        return;
                    }

                    label.style.setProperty('--intro-label-offset-x', '0px');
                    label.style.setProperty('--intro-label-offset-y', '0px');
                });
            });
        }

        intro.addEventListener('pointerdown', () => {
            unlockIntroAudio();
        }, { once: true });

        intro.addEventListener('click', () => {
            dismissIntro();
        });
    }

    const setPageTransitionTheme = (theme) => {
        if (!(pageTransition instanceof HTMLElement)) {
            return;
        }

        pageTransition.dataset.transitionTheme = theme || currentPageTheme;
    };

    const setPageTransitionLabel = (label) => {
        if (!(pageTransitionLabel instanceof HTMLElement)) {
            return;
        }

        pageTransitionLabel.textContent = label || 'DOST PES';
    };

    const cleanupBootTransition = () => {
        body?.classList.remove('page-transition-revealing', 'page-transition-lock');
        root.classList.remove('portal-transition-boot');

        try {
            sessionStorage.removeItem('portal-transition-pending');
            sessionStorage.removeItem('portal-transition-theme');
            sessionStorage.removeItem('portal-transition-label');
        } catch (error) {
            // Ignore storage cleanup issues.
        }
    };

    setPageTransitionTheme(root.dataset.portalTransitionTheme || currentPageTheme);
    setPageTransitionLabel(root.dataset.portalTransitionLabel || document.title.replace(/\s*\|\s*.*$/, '').trim() || 'DOST PES');

    if (root.classList.contains('portal-transition-boot') && pageTransition instanceof HTMLElement) {
        body?.classList.add('page-transition-lock');

        window.requestAnimationFrame(() => {
            window.requestAnimationFrame(() => {
                body?.classList.add('page-transition-revealing');
            });
        });

        window.setTimeout(cleanupBootTransition, window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 980);
    }

    const resolveTransitionTheme = (url) => {
        const path = url.pathname.toLowerCase();
        const hash = url.hash.toLowerCase();

        if (path.startsWith('/gates-projects') || hash.includes('gates')) {
            return 'gates';
        }

        if (path.startsWith('/dost-dx') || hash.includes('dost-dx')) {
            return 'dx';
        }

        return 'pes';
    };

    const resolveTransitionLabel = (link, theme) => {
        if (!(link instanceof HTMLAnchorElement)) {
            return theme === 'gates' ? 'DOST GATES Project 1' : 'DOST PES';
        }

        const explicitLabel = link.dataset.transitionLabel?.trim();
        if (explicitLabel) {
            return explicitLabel;
        }

        const heading = link.querySelector('h1, h2, h3, h4, h5, h6');
        const headingText = (heading?.textContent || '').replace(/\s+/g, ' ').trim();
        if (headingText) {
            return headingText;
        }

        const text = (link.textContent || '').replace(/\s+/g, ' ').trim();
        if (text) {
            return text;
        }

        return theme === 'gates' ? 'DOST GATES Project 1' : theme === 'dx' ? 'DOST DX' : 'DOST PES';
    };

    const shouldHandlePageTransition = (link, url) => {
        if (!(link instanceof HTMLAnchorElement) || !(pageTransition instanceof HTMLElement)) {
            return false;
        }

        if (!url || url.origin !== window.location.origin) {
            return false;
        }

        if (!['http:', 'https:'].includes(url.protocol)) {
            return false;
        }

        if (link.target === '_blank' || link.hasAttribute('download') || link.closest('[data-no-page-transition]')) {
            return false;
        }

        if (url.pathname.startsWith('/admin')) {
            return false;
        }

        const currentUrl = new URL(window.location.href);
        const isSameDocument = url.pathname === currentUrl.pathname && url.search === currentUrl.search;

        if (isSameDocument) {
            return false;
        }

        return true;
    };

    document.addEventListener('click', (event) => {
        if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return;
        }

        const link = event.target instanceof Element ? event.target.closest('a[href]') : null;
        if (!(link instanceof HTMLAnchorElement)) {
            return;
        }

        if (link.dataset.bsToggle === 'modal' || link.getAttribute('data-bs-toggle') === 'modal') {
            return;
        }

        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:') || href.startsWith('javascript:')) {
            return;
        }

        const destination = new URL(link.href, window.location.href);
        if (!shouldHandlePageTransition(link, destination)) {
            return;
        }

        event.preventDefault();

        const nextTheme = resolveTransitionTheme(destination);
        const nextLabel = resolveTransitionLabel(link, nextTheme);

        setPageTransitionTheme(nextTheme);
        setPageTransitionLabel(nextLabel);
        body?.classList.remove('page-transition-revealing');
        body?.classList.add('page-transition-entering', 'page-transition-lock');

        try {
            sessionStorage.setItem('portal-transition-pending', '1');
            sessionStorage.setItem('portal-transition-theme', nextTheme);
            sessionStorage.setItem('portal-transition-label', nextLabel);
        } catch (error) {
            // Ignore storage issues and continue navigation.
        }

        window.setTimeout(() => {
            window.location.href = destination.toString();
        }, window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 620);
    });

    const supportsFinePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    const heroSection = document.querySelector('.hero-section');
    const heroPointerGlow = document.getElementById('hero-pointer-glow');
    const heroContentShell = heroSection instanceof HTMLElement ? heroSection.querySelector('.hero-content-shell') : null;
    const navLinks = Array.from(document.querySelectorAll('.portal-navbar-menu .nav-link'));

    if (supportsFinePointer && heroSection instanceof HTMLElement) {
        const cursorOrb = document.createElement('div');
        cursorOrb.className = 'interactive-cursor-orb';
        const cursorCore = document.createElement('div');
        cursorCore.className = 'interactive-cursor-core';
        document.body.append(cursorOrb, cursorCore);

        let targetX = window.innerWidth * 0.5;
        let targetY = window.innerHeight * 0.5;
        let orbX = targetX;
        let orbY = targetY;
        let tick = 0;

        const spawnParticle = (x, y) => {
            const particle = document.createElement('span');
            particle.className = 'interactive-cursor-particle';
            particle.style.left = `${x}px`;
            particle.style.top = `${y}px`;
            document.body.appendChild(particle);
            window.setTimeout(() => particle.remove(), 620);
        };

        const updateHeroGlowPosition = (x, y) => {
            const heroRect = heroSection.getBoundingClientRect();
            if (x < heroRect.left || x > heroRect.right || y < heroRect.top || y > heroRect.bottom) {
                if (heroPointerGlow instanceof HTMLElement) {
                    heroPointerGlow.style.opacity = '.15';
                }
                if (heroContentShell instanceof HTMLElement) {
                    heroContentShell.classList.remove('is-hovered');
                }
                heroSection.classList.remove('is-reveal-active');
                heroSection.style.setProperty('--hero-reveal-size', '0px');
                return;
            }

            const relativeX = ((x - heroRect.left) / heroRect.width) * 100;
            const relativeY = ((y - heroRect.top) / heroRect.height) * 100;
            if (heroPointerGlow instanceof HTMLElement) {
                heroPointerGlow.style.opacity = '.85';
            }
            heroSection.classList.add('is-reveal-active');
            heroSection.style.setProperty('--hero-pointer-x', `${relativeX}%`);
            heroSection.style.setProperty('--hero-pointer-y', `${relativeY}%`);
            heroSection.style.setProperty('--hero-reveal-size', '220px');

            if (heroContentShell instanceof HTMLElement) {
                const shellRect = heroContentShell.getBoundingClientRect();
                const shellX = ((x - shellRect.left) / shellRect.width) * 100;
                const shellY = ((y - shellRect.top) / shellRect.height) * 100;
                const inShell = x >= shellRect.left && x <= shellRect.right && y >= shellRect.top && y <= shellRect.bottom;
                heroContentShell.style.setProperty('--hero-card-x', `${Math.max(0, Math.min(100, shellX))}%`);
                heroContentShell.style.setProperty('--hero-card-y', `${Math.max(0, Math.min(100, shellY))}%`);
                heroContentShell.classList.toggle('is-hovered', inShell);
            }
        };

        const animateCursor = () => {
            orbX += (targetX - orbX) * 0.17;
            orbY += (targetY - orbY) * 0.17;
            cursorOrb.style.left = `${orbX}px`;
            cursorOrb.style.top = `${orbY}px`;
            cursorCore.style.left = `${targetX}px`;
            cursorCore.style.top = `${targetY}px`;
            updateHeroGlowPosition(targetX, targetY);
            window.requestAnimationFrame(animateCursor);
        };

        window.addEventListener('mousemove', (event) => {
            targetX = event.clientX;
            targetY = event.clientY;
            tick += 1;
            if (tick % 2 === 0) {
                spawnParticle(targetX, targetY);
            }
        }, { passive: true });

        document.addEventListener('mouseleave', () => {
            cursorOrb.style.opacity = '.3';
            cursorCore.style.opacity = '.3';
            if (heroPointerGlow instanceof HTMLElement) {
                heroPointerGlow.style.opacity = '.2';
            }
            heroSection.classList.remove('is-reveal-active');
            heroSection.style.setProperty('--hero-reveal-size', '0px');
        });

        document.addEventListener('mouseenter', () => {
            cursorOrb.style.opacity = '.88';
            cursorCore.style.opacity = '1';
        });

        heroSection.addEventListener('mouseleave', () => {
            heroSection.classList.remove('is-reveal-active');
            heroSection.style.setProperty('--hero-reveal-size', '0px');
            if (heroPointerGlow instanceof HTMLElement) {
                heroPointerGlow.style.opacity = '.2';
            }
            if (heroContentShell instanceof HTMLElement) {
                heroContentShell.classList.remove('is-hovered');
            }
        });

        document.querySelectorAll('[data-magnetic]').forEach((element) => {
            if (!(element instanceof HTMLElement)) return;

            element.addEventListener('mousemove', (event) => {
                const rect = element.getBoundingClientRect();
                const moveX = (event.clientX - rect.left - rect.width / 2) * 0.16;
                const moveY = (event.clientY - rect.top - rect.height / 2) * 0.16;
                element.style.transform = `translate3d(${moveX}px, ${moveY}px, 0)`;
                element.classList.add('is-magnetic-active');
            });

            element.addEventListener('mouseleave', () => {
                element.style.transform = 'translate3d(0, 0, 0)';
                element.classList.remove('is-magnetic-active');
            });
        });

        document.querySelectorAll('[data-ripple]').forEach((element) => {
            if (!(element instanceof HTMLElement)) return;

            element.addEventListener('mousemove', (event) => {
                const rect = element.getBoundingClientRect();
                element.style.setProperty('--ripple-x', `${event.clientX - rect.left}px`);
                element.style.setProperty('--ripple-y', `${event.clientY - rect.top}px`);
            });
        });

        animateCursor();
    }

    const updateActiveNavLink = () => {
        if (!navLinks.length) return;

        let activeId = 'top';
        navLinks.forEach((link) => {
            if (!(link instanceof HTMLAnchorElement)) return;
            const hash = link.hash?.replace('#', '');
            if (!hash) return;
            const section = document.getElementById(hash);
            if (!(section instanceof HTMLElement)) return;
            const rect = section.getBoundingClientRect();
            if (rect.top <= 140 && rect.bottom > 160) {
                activeId = hash;
            }
        });

        navLinks.forEach((link) => {
            if (!(link instanceof HTMLAnchorElement)) return;
            const isActive = link.hash === `#${activeId}`;
            link.classList.toggle('is-active', isActive);
        });
    };

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

        updateActiveNavLink();
    };

    onScroll();
    updateActiveNavLink();
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

        const title = (row.dataset.dxLibraryTitle || '').toLowerCase();
        const program = (row.dataset.dxLibraryProgram || '').toLowerCase();
        const searchText = (row.dataset.dxLibrarySearch || '').toLowerCase();
        const titleWords = title.split(/\s+/).filter(Boolean);

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
    const gatesLibraryForm = document.querySelector('[data-gates-library-form]');
    const gatesLibrarySearch = document.querySelector('[data-gates-library-search]');
    const gatesLibraryApply = document.querySelector('[data-gates-library-apply]');
    const gatesLibraryRows = Array.from(document.querySelectorAll('[data-gates-library-row]'));
    const gatesLibraryEmpty = document.querySelector('[data-gates-library-empty-row]');
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

    const filterGatesLibraryRows = () => {
        if (!(gatesLibrarySearch instanceof HTMLInputElement)) {
            return;
        }

        const query = gatesLibrarySearch.value.trim().toLowerCase();
        let visibleCount = 0;

        gatesLibraryRows.forEach((row) => {
            if (!(row instanceof HTMLElement)) return;

            const searchText = (row.dataset.gatesLibrarySearch || '').toLowerCase();
            const visible = matchesStartOrContains(searchText, query);
            row.hidden = !visible;

            if (visible) visibleCount += 1;
        });

        if (gatesLibraryEmpty instanceof HTMLElement) {
            gatesLibraryEmpty.hidden = visibleCount > 0;
        }
    };

    gatesLibraryForm?.addEventListener('submit', (event) => {
        event.preventDefault();
        filterGatesLibraryRows();
    });
    gatesLibrarySearch?.addEventListener('input', filterGatesLibraryRows);
    gatesLibrarySearch?.addEventListener('search', filterGatesLibraryRows);
    gatesLibraryApply?.addEventListener('click', filterGatesLibraryRows);
    filterGatesLibraryRows();

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
    const dxDomainCards = document.querySelectorAll('.dx-domain-card[data-dx-domain]');
    const dxProgramCards = document.querySelectorAll('.dx-sub-card[data-program-slug]');
    let activeDxDomain = '';

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
        activeDxDomain = domainSlug || '';
        let primaryMarked = false;

        dxProgramCards.forEach((card) => {
            if (!(card instanceof HTMLElement)) return;

            const inDomain = domainSlug && card.dataset.domain === domainSlug;
            card.classList.toggle('is-domain-active', inDomain);
            card.classList.toggle('is-domain-dimmed', Boolean(domainSlug) && !inDomain);
            card.classList.toggle('is-domain-primary', false);

            if (inDomain && !primaryMarked) {
                card.classList.add('is-domain-primary');
                primaryMarked = true;
            }
        });

        dxDomainCards.forEach((card) => {
            if (!(card instanceof HTMLElement)) return;

            const isActive = domainSlug !== '' && card.dataset.dxDomain === domainSlug;
            card.classList.toggle('is-selected', isActive);
            card.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
    };

    const dxGoToSubProgram = (domain, programSlug) => {
        setDxTab('programs');

        window.setTimeout(() => {
            const preferredProgram = programSlug
                ? document.querySelector(`.dx-sub-card[data-domain="${domain}"][data-program-slug="${programSlug}"]`)
                : null;
            const firstInDomain = preferredProgram instanceof HTMLElement
                ? preferredProgram
                : document.querySelector(`.dx-sub-card[data-domain="${domain}"]`);

            if (!domain) return;

            activateDxDomain(domain);

            if (firstInDomain instanceof HTMLElement) {
                firstInDomain.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }, 100);
    };

    window.dxGoToSubProgram = dxGoToSubProgram;

    dxDomainCards.forEach((card) => {
        card.addEventListener('click', () => {
            if (!(card instanceof HTMLElement)) {
                return;
            }

            const domain = card.dataset.dxDomain || '';
            const defaultProgram = card.dataset.dxDefaultProgram || '';

            if (!domain) {
                return;
            }

            dxGoToSubProgram(domain, defaultProgram);
        });
    });

    const dxOverviewModal = document.getElementById('dxOverviewModal');
    let pendingDxAction = null;
    const dxProgramSearchForm = document.querySelector('.dx-program-page-controls');
    const dxProgramSearchInput = document.querySelector('[data-dx-project-search]');
    const dxProjectItems = Array.from(document.querySelectorAll('[data-dx-project-item]'));
    const dxProjectCount = document.querySelector('[data-dx-project-count]');
    const dxProjectEmpty = document.querySelector('[data-dx-project-empty]');

    const dxProjectMatchesSearch = (title, query) => {
        const normalizedQuery = query.trim().toLowerCase();

        if (normalizedQuery === '') {
            return true;
        }

        const normalizedTitle = title.trim().toLowerCase();
        const titleWords = normalizedTitle.split(/\s+/).filter(Boolean);

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

            const title = item.dataset.projectTitle || '';
            const visible = dxProjectMatchesSearch(title, query);

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

    // GATES Collection Page Search (Client-side filtering)
    const gatesSearchForm = document.querySelector('[data-gates-search-form]');
    const gatesSearchInput = document.querySelector('[data-gates-search-input]');
    const gatesSearchRows = Array.from(document.querySelectorAll('[data-gates-search-row]'));
    const gatesSearchEmpty = document.querySelector('[data-gates-library-empty-row]');
    const gatesSearchCount = document.querySelector('[data-gates-search-count]');

    const filterGatesSearchRows = () => {
        if (!(gatesSearchInput instanceof HTMLInputElement)) {
            return;
        }

        const query = gatesSearchInput.value.trim().toLowerCase();
        let visibleCount = 0;

        gatesSearchRows.forEach((row) => {
            if (!(row instanceof HTMLElement)) return;

            const searchText = (row.dataset.gatesSearchRow || '').toLowerCase();
            const visible = matchesStartOrContains(searchText, query);
            row.hidden = !visible;

            if (visible) visibleCount += 1;
        });

        if (gatesSearchCount instanceof HTMLElement) {
            gatesSearchCount.textContent = visibleCount;
        }

        // Show/hide empty state based on filter results (not form submission)
        const gatesPageEmpty = document.querySelector('.gates-page-empty');
        if (gatesPageEmpty instanceof HTMLElement) {
            gatesPageEmpty.hidden = visibleCount > 0;
        }
    };

    gatesSearchForm?.addEventListener('submit', (event) => {
        event.preventDefault();
        filterGatesSearchRows();
    });
    gatesSearchInput?.addEventListener('input', filterGatesSearchRows);
    gatesSearchInput?.addEventListener('search', filterGatesSearchRows);
    filterGatesSearchRows();

    const gatesNewsSections = Array.from(document.querySelectorAll('[data-gates-news]'));

    gatesNewsSections.forEach((section) => {
        if (!(section instanceof HTMLElement)) {
            return;
        }

        const slides = Array.from(section.querySelectorAll('[data-gates-news-slide]'));
        const dots = Array.from(section.closest('.gates-news-shell')?.querySelectorAll('[data-gates-news-dot]') || []);
        const prevButton = section.querySelector('[data-gates-news-prev]');
        const nextButton = section.querySelector('[data-gates-news-next]');

        if (slides.length === 0) {
            return;
        }

        let activeIndex = 0;
        let autoTimer = null;

        const setActive = (index) => {
            const maxIndex = slides.length - 1;
            activeIndex = index < 0 ? maxIndex : (index > maxIndex ? 0 : index);

            slides.forEach((slide, slideIndex) => {
                if (!(slide instanceof HTMLElement)) {
                    return;
                }

                slide.classList.toggle('is-active', slideIndex === activeIndex);
            });

            dots.forEach((dot, dotIndex) => {
                if (!(dot instanceof HTMLElement)) {
                    return;
                }

                dot.classList.toggle('is-active', dotIndex === activeIndex);
            });
        };

        const startAuto = () => {
            if (slides.length <= 1) {
                return;
            }

            stopAuto();
            autoTimer = window.setInterval(() => {
                setActive(activeIndex + 1);
            }, 6000);
        };

        const stopAuto = () => {
            if (autoTimer) {
                window.clearInterval(autoTimer);
                autoTimer = null;
            }
        };

        prevButton?.addEventListener('click', () => {
            setActive(activeIndex - 1);
            startAuto();
        });

        nextButton?.addEventListener('click', () => {
            setActive(activeIndex + 1);
            startAuto();
        });

        dots.forEach((dot) => {
            dot.addEventListener('click', () => {
                if (!(dot instanceof HTMLElement)) {
                    return;
                }

                const target = Number.parseInt(dot.dataset.gatesNewsIndex || '0', 10);
                if (!Number.isNaN(target)) {
                    setActive(target);
                    startAuto();
                }
            });
        });

        section.addEventListener('mouseenter', stopAuto);
        section.addEventListener('mouseleave', startAuto);
        section.addEventListener('focusin', stopAuto);
        section.addEventListener('focusout', startAuto);

        if (slides.length <= 1) {
            prevButton?.setAttribute('hidden', 'hidden');
            nextButton?.setAttribute('hidden', 'hidden');
            dots.forEach((dot) => dot.setAttribute('hidden', 'hidden'));
        } else {
            setActive(0);
            startAuto();
        }
    });
});
