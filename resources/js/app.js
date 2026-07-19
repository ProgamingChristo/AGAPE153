document.addEventListener('DOMContentLoaded', () => {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const finePointer = window.matchMedia('(pointer: fine)').matches;
    const siteHeader = document.querySelector('[data-site-header]');

    if (siteHeader) {
        const updateHeaderTransparency = () => {
            document.body.classList.toggle('nav-transparent', window.scrollY > 18);
        };

        updateHeaderTransparency();
        window.addEventListener('scroll', updateHeaderTransparency, { passive: true });
    }

    if (!reducedMotion) {
        const revealItems = document.querySelectorAll('[data-reveal]');

        if ('IntersectionObserver' in window && revealItems.length) {
            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('reveal-visible');
                        revealObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.16 });

            revealItems.forEach((item) => {
                item.classList.add('reveal');
                revealObserver.observe(item);
            });
        }
    }

    if (finePointer && !reducedMotion) {
        const ring = document.querySelector('[data-cursor-ring]');
        const dot = document.querySelector('[data-cursor-dot]');
        const glow = document.querySelector('[data-cursor-glow]');

        if (ring && dot && glow) {
            let mouseX = window.innerWidth / 2;
            let mouseY = window.innerHeight / 2;
            let ringX = mouseX;
            let ringY = mouseY;
            let glowX = mouseX;
            let glowY = mouseY;

            document.body.classList.add('cursor-ready');

            const moveCursor = (event) => {
                mouseX = event.clientX;
                mouseY = event.clientY;
                dot.style.transform = `translate3d(${mouseX}px, ${mouseY}px, 0)`;
            };

            const animateRing = () => {
                ringX += (mouseX - ringX) * 0.18;
                ringY += (mouseY - ringY) * 0.18;
                glowX += (mouseX - glowX) * 0.06;
                glowY += (mouseY - glowY) * 0.06;
                ring.style.transform = `translate3d(${ringX}px, ${ringY}px, 0)`;
                glow.style.transform = `translate3d(${glowX}px, ${glowY}px, 0)`;
                window.requestAnimationFrame(animateRing);
            };

            window.addEventListener('mousemove', moveCursor, { passive: true });
            document.querySelectorAll('a, button, input, textarea, select, summary').forEach((element) => {
                element.addEventListener('mouseenter', () => document.body.classList.add('cursor-active'));
                element.addEventListener('mouseleave', () => document.body.classList.remove('cursor-active'));
            });

            animateRing();
        }
    }

    const timeoutMinutes = Number(document.body.dataset.adminTimeoutMinutes || 0);

    if (timeoutMinutes <= 0) {
        return;
    }

    let timeoutId;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const logoutUrl = document.body.dataset.adminLogoutUrl;
    const loginUrl = document.body.dataset.adminLoginUrl;

    const logout = () => {
        fetch(logoutUrl, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        }).finally(() => {
            window.location.href = loginUrl;
        });
    };

    const resetTimer = () => {
        window.clearTimeout(timeoutId);
        timeoutId = window.setTimeout(logout, timeoutMinutes * 60 * 1000);
    };

    ['click', 'keydown', 'mousemove', 'scroll', 'touchstart'].forEach((eventName) => {
        window.addEventListener(eventName, resetTimer, { passive: true });
    });

    resetTimer();
});
