/**
 * WP Clothing Theme — main.js
 * Handles: hero carousel (Swiper), sticky header, search overlay,
 *          mobile drawer, dropdown keyboard nav, product tabs
 */

(function () {
    'use strict';

    // ── Hero Carousel (Swiper) ────────────────────────────────
    document.querySelectorAll('.js-hero-swiper').forEach(function (el) {
        new Swiper(el, {
            loop: true,
            autoplay: { delay: 5000, disableOnInteraction: false, pauseOnMouseEnter: true },
            effect: 'fade',
            fadeEffect: { crossFade: true },
            speed: 800,
            navigation: {
                nextEl: el.querySelector('.swiper-button-next'),
                prevEl: el.querySelector('.swiper-button-prev'),
            },
            pagination: { el: el.querySelector('.swiper-pagination'), clickable: true },
            a11y: { prevSlideMessage: 'Slide anterior', nextSlideMessage: 'Slide siguiente' },
            keyboard: { enabled: true },
        });
    });

    // ── Sticky header ────────────────────────────────────────
    const header = document.querySelector('.js-header');
    if (header) {
        window.addEventListener('scroll', () => {
            header.classList.toggle('is-scrolled', window.scrollY > 60);
        }, { passive: true });
    }

    // ── Overlay background (shared) ───────────────────────────
    const overlayBg = document.querySelector('.js-overlay-bg');

    function showOverlay() {
        if (overlayBg) overlayBg.classList.add('is-visible');
    }
    function hideOverlay() {
        if (overlayBg) overlayBg.classList.remove('is-visible');
    }

    // ── Search overlay ────────────────────────────────────────
    const searchOverlay = document.querySelector('.js-search-overlay');
    const searchToggle  = document.querySelector('.js-search-toggle');
    const searchClose   = document.querySelector('.js-search-close');

    function openSearch() {
        if (!searchOverlay) return;
        searchOverlay.removeAttribute('hidden');
        searchToggle?.setAttribute('aria-expanded', 'true');
        searchOverlay.querySelector('.search-field')?.focus();
        showOverlay();
        document.body.style.overflow = 'hidden';
    }

    function closeSearch() {
        if (!searchOverlay) return;
        searchOverlay.setAttribute('hidden', '');
        searchToggle?.setAttribute('aria-expanded', 'false');
        searchToggle?.focus();
        hideOverlay();
        document.body.style.overflow = '';
    }

    searchToggle?.addEventListener('click', openSearch);
    searchClose?.addEventListener('click', closeSearch);

    // ── Mobile drawer ─────────────────────────────────────────
    const drawer      = document.querySelector('.js-mobile-drawer');
    const burger      = document.querySelector('.js-burger');
    const drawerClose = document.querySelector('.js-drawer-close');

    function openDrawer() {
        if (!drawer) return;
        drawer.removeAttribute('hidden');
        // RAF so transition fires after display:flex
        requestAnimationFrame(() => drawer.classList.add('is-open'));
        burger?.setAttribute('aria-expanded', 'true');
        showOverlay();
        document.body.style.overflow = 'hidden';
        // Move focus inside
        drawerClose?.focus();
    }

    function closeDrawer() {
        if (!drawer) return;
        drawer.classList.remove('is-open');
        burger?.setAttribute('aria-expanded', 'false');
        hideOverlay();
        document.body.style.overflow = '';
        burger?.focus();
        // Hide after transition
        drawer.addEventListener('transitionend', () => {
            if (!drawer.classList.contains('is-open')) {
                drawer.setAttribute('hidden', '');
            }
        }, { once: true });
    }

    burger?.addEventListener('click', openDrawer);
    drawerClose?.addEventListener('click', closeDrawer);

    // Close on overlay click
    overlayBg?.addEventListener('click', () => {
        closeDrawer();
        closeSearch();
    });

    // ── Escape key closes everything ──────────────────────────
    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;
        if (searchOverlay && !searchOverlay.hasAttribute('hidden')) closeSearch();
        if (drawer?.classList.contains('is-open')) closeDrawer();
    });

    // ── Mobile sub-menu accordion ─────────────────────────────
    document.querySelectorAll('.js-mobile-drawer .wpc-nav__toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            const item     = btn.closest('.wpc-nav__item');
            const dropdown = item?.querySelector('.wpc-nav__dropdown');
            if (!dropdown) return;

            const isOpen = dropdown.classList.contains('is-open');
            // Close siblings
            btn.closest('.wpc-mobile-nav, .wpc-nav__dropdown')
               ?.querySelectorAll('.wpc-nav__dropdown.is-open')
               .forEach(d => {
                   d.classList.remove('is-open');
                   d.closest('.wpc-nav__item')
                    ?.querySelector('.wpc-nav__toggle')
                    ?.setAttribute('aria-expanded', 'false');
               });

            if (!isOpen) {
                dropdown.classList.add('is-open');
                btn.setAttribute('aria-expanded', 'true');
            }
        });
    });

    // ── Desktop dropdown — keyboard support ───────────────────
    // Arrow keys navigate within open dropdown
    document.querySelectorAll('.wpc-header__nav .wpc-nav__item.has-children').forEach(item => {
        const trigger  = item.querySelector(':scope > .wpc-nav__link');
        const dropdown = item.querySelector(':scope > .wpc-nav__dropdown');
        if (!trigger || !dropdown) return;

        trigger.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowDown') {
                e.preventDefault();
                // Force show
                dropdown.style.opacity = '1';
                dropdown.style.pointerEvents = 'auto';
                dropdown.style.transform = 'translateX(-50%) translateY(0)';
                trigger.setAttribute('aria-expanded', 'true');
                dropdown.querySelector('.wpc-nav__link')?.focus();
            }
        });

        // Arrow navigation inside dropdown
        dropdown.addEventListener('keydown', (e) => {
            const links = [...dropdown.querySelectorAll('.wpc-nav__link')];
            const idx   = links.indexOf(document.activeElement);

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                links[(idx + 1) % links.length]?.focus();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                links[(idx - 1 + links.length) % links.length]?.focus();
            } else if (e.key === 'Escape' || e.key === 'Tab') {
                // Reset inline styles so CSS hover still works
                dropdown.style.cssText = '';
                trigger.setAttribute('aria-expanded', 'false');
                trigger.focus();
            }
        });

        // Reset on blur out of item
        item.addEventListener('focusout', (e) => {
            if (!item.contains(e.relatedTarget)) {
                dropdown.style.cssText = '';
                trigger.setAttribute('aria-expanded', 'false');
            }
        });
    });

    // ── Product tab swipers ───────────────────────────────────
    const tabSwipers = new Map();

    function initProductTabSwipers(root) {
        root = root || document;
        root.querySelectorAll('.wpc-tabs-swiper:not(.swiper-initialized)').forEach(el => {
            const panel  = el.closest('.wpc-product-tabs__panel');
            const prevEl = panel?.querySelector('.wpc-tabs-nav--prev');
            const nextEl = panel?.querySelector('.wpc-tabs-nav--next');

            const swiper = new Swiper(el, {
                slidesPerView: 1,
                spaceBetween: 16,
                navigation: { prevEl, nextEl },
                breakpoints: {
                    480:  { slidesPerView: 2 },
                    768:  { slidesPerView: 3 },
                    1024: { slidesPerView: 4 },
                },
                a11y: {
                    prevSlideMessage: 'Productos anteriores',
                    nextSlideMessage: 'Productos siguientes',
                },
            });

            if (panel?.id) tabSwipers.set(panel.id, swiper);
        });
    }

    initProductTabSwipers();

    // Re-inicializar dentro del editor de Elementor (el preview es un iframe
    // donde Elementor re-renderiza widgets dinámicamente)
    if (typeof window.elementorFrontend !== 'undefined') {
        window.elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
            if ($scope[0]?.querySelector('.wpc-tabs-swiper')) {
                initProductTabSwipers($scope[0]);
            }
        });
    }

    // ── Product tabs switching ────────────────────────────────
    document.querySelectorAll('.wpc-product-tabs__tab').forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.dataset.tab;
            const parent = tab.closest('.wpc-product-tabs');

            parent.querySelectorAll('.wpc-product-tabs__tab').forEach(t => {
                t.classList.remove('is-active');
                t.setAttribute('aria-selected', 'false');
            });
            parent.querySelectorAll('.wpc-product-tabs__panel')
                  .forEach(p => p.classList.remove('is-active'));

            tab.classList.add('is-active');
            tab.setAttribute('aria-selected', 'true');

            const panel = parent.querySelector(`#wpc-panel-${target}`);
            if (panel) {
                panel.classList.add('is-active');
                const swiper = tabSwipers.get(`wpc-panel-${target}`);
                if (swiper) { swiper.update(); swiper.slideTo(0, 0); }
            }
        });
    });

    // Activate first tab of each widget
    document.querySelectorAll('.wpc-product-tabs').forEach(widget => {
        widget.querySelector('.wpc-product-tabs__tab')?.click();
    });

    // ── Scroll-reveal (IntersectionObserver) ──────────────────
    if ('IntersectionObserver' in window) {
        // Auto-assign data-scroll a componentes sin atributo manual
        const autoScroll = [
            { sel: '.wpc-banner',     anim: 'zoom-in' },
            { sel: '.wpc-newsletter', anim: 'fade-up' },
        ];
        autoScroll.forEach(({ sel, anim }) => {
            document.querySelectorAll(sel).forEach(el => {
                if (!el.hasAttribute('data-scroll')) {
                    el.setAttribute('data-scroll', anim);
                }
            });
        });

        const scrollObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    scrollObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.12,
            rootMargin: '0px 0px -40px 0px',
        });

        document.querySelectorAll('[data-scroll]').forEach(el => scrollObserver.observe(el));
    }

})();

