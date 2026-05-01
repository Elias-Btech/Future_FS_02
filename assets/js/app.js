/* ===== CRM Pulse – App JS ===== */

// ══════════════════════════════════════════════════════════
// 1. DARK MODE
// ══════════════════════════════════════════════════════════
const darkToggle = document.getElementById('darkToggle');

function applyDark(isDark) {
    document.body.classList.toggle('dark-mode', isDark);
    if (darkToggle) {
        darkToggle.innerHTML = isDark
            ? '<i class="bi bi-sun-fill" style="font-size:.9rem;color:#fbbf24;"></i>'
            : '<i class="bi bi-moon-fill" style="font-size:.9rem;"></i>';
        darkToggle.title = isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode';
    }
}

// Restore saved preference immediately
applyDark(localStorage.getItem('crmDark') === '1');

if (darkToggle) {
    darkToggle.addEventListener('click', () => {
        const isDark = !document.body.classList.contains('dark-mode');
        localStorage.setItem('crmDark', isDark ? '1' : '0');
        applyDark(isDark);
    });
}

// ══════════════════════════════════════════════════════════
// 2. MOBILE SIDEBAR — open/close
// ══════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
    const toggle   = document.getElementById('sidebarToggle');
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebarOverlay');

    function openSidebar() {
        sidebar?.classList.add('open');
        overlay?.classList.add('show');
        document.body.style.overflow = 'hidden'; // prevent background scroll
    }

    function closeSidebar() {
        sidebar?.classList.remove('open');
        overlay?.classList.remove('show');
        document.body.style.overflow = '';
    }

    toggle?.addEventListener('click', () => {
        sidebar?.classList.contains('open') ? closeSidebar() : openSidebar();
    });

    overlay?.addEventListener('click', closeSidebar);

    // Close sidebar when a nav link is tapped on mobile
    sidebar?.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 768) closeSidebar();
        });
    });
});

// ══════════════════════════════════════════════════════════
// 3. ANIMATED COUNTERS
// ══════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.counter').forEach(el => {
        const target = parseInt(el.dataset.target, 10);
        if (isNaN(target)) return;
        if (target === 0) { el.textContent = '0'; return; }
        let current = 0;
        const step  = Math.max(1, Math.ceil(target / 50));
        const timer = setInterval(() => {
            current = Math.min(current + step, target);
            el.textContent = current;
            if (current >= target) clearInterval(timer);
        }, 20);
    });
});

// ══════════════════════════════════════════════════════════
// 4. CLICKABLE STAT CARDS
// ══════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.stat-card[data-href]').forEach(card => {
        card.style.cursor = 'pointer';
        card.addEventListener('click', () => {
            window.location.href = card.dataset.href;
        });
    });
});

// ══════════════════════════════════════════════════════════
// 5. AUTO-DISMISS FLASH MESSAGES
// ══════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.flash-msg').forEach(el => {
        setTimeout(() => {
            el.style.opacity   = '0';
            el.style.transform = 'translateY(-8px)';
            setTimeout(() => el.remove(), 600);
        }, 4000);
    });
});

// ══════════════════════════════════════════════════════════
// 6. CONFIRM DELETE
// ══════════════════════════════════════════════════════════
document.addEventListener('click', e => {
    const btn = e.target.closest('[data-confirm]');
    if (!btn) return;
    if (!confirm(btn.dataset.confirm)) e.preventDefault();
});
