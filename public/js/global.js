/* ============================================================
   AGRITECH PRO — GLOBAL JAVASCRIPT
   ============================================================ */
'use strict';

const $ = (s, c = document) => c.querySelector(s);
const $$ = (s, c = document) => [...c.querySelectorAll(s)];

/* ---- PAGE LOADER ---- */
(function initLoader() {
  const loader = $('#page-loader');
  if (!loader) return;
  // Instant hide on back/forward navigation (bfcache)
  if (performance && performance.getEntriesByType) {
    const nav = performance.getEntriesByType('navigation')[0];
    if (nav && nav.type === 'back_forward') {
      loader.remove();
      return;
    }
  }
  setTimeout(() => {
    loader.classList.add('done');
    setTimeout(() => loader.remove(), 350);
  }, 300);
})();

/* ---- DARK MODE ---- */
(function initDarkMode() {
  const btn = $('#darkToggle');
  const html = document.documentElement;
  const stored = localStorage.getItem('agri-theme') || 'light';
  html.setAttribute('data-theme', stored);
  updateIcon();

  function updateIcon() {
    if (!btn) return;
    const isDark = html.getAttribute('data-theme') === 'dark';
    btn.innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
    btn.title = isDark ? 'Switch to light mode' : 'Switch to dark mode';
  }

  btn?.addEventListener('click', () => {
    const current = html.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('agri-theme', next);
    updateIcon();
  });
})();

/* ---- NAVBAR SCROLL ---- */
(function initNavbar() {
  const navbar = $('#navbar');
  if (!navbar) return;
  const onScroll = () => navbar.classList.toggle('scrolled', window.scrollY > 30);
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
})();

/* ---- MOBILE MENU ---- */
(function initMobileMenu() {
  const btn = $('#hamburger');
  const menu = $('#mobileMenu');
  if (!btn || !menu) return;

  btn.addEventListener('click', () => {
    const open = btn.classList.toggle('open');
    menu.classList.toggle('open', open);
    btn.setAttribute('aria-expanded', open);
    btn.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
    document.body.style.overflow = open ? 'hidden' : '';
  });

  $$('.mobile-menu-link', menu).forEach(l => l.addEventListener('click', close));

  document.addEventListener('click', e => {
    if (!btn.contains(e.target) && !menu.contains(e.target)) close();
  });

  document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });

  function close() {
    btn.classList.remove('open');
    menu.classList.remove('open');
    btn.setAttribute('aria-expanded', 'false');
    btn.setAttribute('aria-label', 'Open menu');
    document.body.style.overflow = '';
  }
})();

/* ---- ACTIVE NAV LINK ---- */
(function initActiveNav() {
  const links = $$('.nav-link');
  const path = window.location.pathname.split('/').pop() || 'index.html';
  links.forEach(l => {
    const href = l.getAttribute('href')?.split('/').pop() || '';
    if (href === path) l.classList.add('active');
    else l.classList.remove('active');
  });
})();

/* ---- SMOOTH SCROLL ---- */
$$('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const id = a.getAttribute('href');
    if (id === '#') return;
    const el = $(id);
    if (!el) return;
    e.preventDefault();
    const offset = document.getElementById('navbar')?.offsetHeight || 72;
    window.scrollTo({ top: el.getBoundingClientRect().top + window.scrollY - offset, behavior: 'smooth' });
  });
});

/* ---- SCROLL REVEAL ---- */
(function initReveal() {
  const els = $$('[data-reveal]');
  if (!els.length) return;

  const observer = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (!e.isIntersecting) return;
      const el = e.target;
      const delay = parseInt(el.dataset.revealDelay || '0');
      setTimeout(() => el.classList.add('revealed'), delay);
      observer.unobserve(el);
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

  els.forEach(el => observer.observe(el));
})();

/* ---- TOAST ---- */
function showToast(msg, type = 'success', duration = 3500) {
  const container = $('#toast-container');
  if (!container) return;

  const icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle', warning: 'fa-exclamation-triangle', info: 'fa-info-circle' };
  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.innerHTML = `<i class="fas ${icons[type] || icons.success}"></i><p>${msg}</p>`;
  container.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(20px)';
    toast.style.transition = 'all 0.4s ease';
    setTimeout(() => toast.remove(), 400);
  }, duration);
}
window.showToast = showToast;

/* ---- COUNTER ANIMATION ---- */
(function initCounters() {
  const counters = $$('[data-count]');
  if (!counters.length) return;

  const observer = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (!e.isIntersecting) return;
      const el = e.target;
      const target = parseInt(el.dataset.count);
      const duration = 2000;
      const step = target / (duration / 16);
      let current = 0;

      const tick = () => {
        current = Math.min(current + step, target);
        el.textContent = Math.floor(current).toLocaleString();
        if (current < target) requestAnimationFrame(tick);
        else el.textContent = target.toLocaleString() + (target > 1000 ? '+' : '');
      };
      requestAnimationFrame(tick);
      observer.unobserve(el);
    });
  }, { threshold: 0.5 });

  counters.forEach(c => observer.observe(c));
})();

/* ---- CHATBOT ---- */
(function initChatbot() {
  const toggleBtn = $('#chatbotToggle');
  const panel = $('#chatbotPanel');
  const input = $('#chatInput');
  const sendBtn = $('#chatSend');
  const messages = $('#chatMessages');
  if (!toggleBtn || !panel) return;

  const responses = [
    { keys: ['maize', 'corn', 'maze'], reply: '🌽 For maize: plant at start of rains (Oct–Nov), use certified seed, apply NPK fertilizer at planting and top-dress with urea at knee height. Watch for Fall Armyworm — spray early if spotted!' },
    { keys: ['disease', 'sick', 'pest', 'bug', 'worm'], reply: '🔍 Describe the symptoms — yellowing leaves, spots, wilting? Our Crop Disease Library has 200+ conditions with treatments. Also try our AI Disease Detection tool (upload a photo).' },
    { keys: ['weather', 'rain', 'forecast', 'climate'], reply: '🌤 Current forecast: Lusaka 28°C, partly cloudy. Rains expected Friday–Saturday. Visit the Weather tab in your dashboard for 7-day forecasts by district.' },
    { keys: ['price', 'market', 'sell', 'buy'], reply: '💰 Current market prices: Maize K280/50kg · Tomatoes K120/box · Soybeans K450/50kg · Groundnuts K550/50kg. Updated daily in the Marketplace!' },
    { keys: ['deliver', 'order', 'track', 'shipping'], reply: '🚛 Track your orders in real-time at the Delivery Tracking page. You\'ll also get SMS updates at every stage — from dispatch to arrival.' },
    { keys: ['fertilizer', 'npk', 'compost', 'manure'], reply: '🌱 For most crops: apply NPK (10:20:10) at planting + Urea top-dress 6 weeks later. Organic compost improves soil structure — aim for 2 tonnes/hectare.' },
    { keys: ['course', 'learn', 'training', 'lesson'], reply: '📚 We have 342 courses! Bestsellers: Modern Maize Farming (free), Drone Precision Ag (K450), Profitable Dairy (K280). Go to the Learning Center to explore all.' },
    { keys: ['register', 'sign', 'join', 'account'], reply: '✅ Joining is completely free! Click "Get Started Free" at the top of the page. You\'ll get instant access to 50+ free courses and marketplace features.' },
    { keys: ['water', 'irrigation', 'drip'], reply: '💧 Drip irrigation can save 40-60% water vs flood irrigation. Your local AgriTech Pro dealer can help size a system for your farm. Check our Irrigation course for full guidance.' },
  ];
  const defaultReply = "🤔 Great question! For detailed help on that topic, try our search bar or visit the Learning Center. You can also call our support line: +260 123 456 78.";

  toggleBtn.addEventListener('click', () => {
    panel.classList.toggle('open');
    if (panel.classList.contains('open')) input?.focus();
  });

  function addMessage(text, type) {
    const msg = document.createElement('div');
    msg.className = `chat-msg ${type}`;
    if (type === 'bot') {
      msg.innerHTML = `<div class="avatar avatar-sm" style="background:var(--green-100);color:var(--green-700);font-size:.8rem;"><i class="fas fa-seedling"></i></div><div class="chat-bubble">${text}</div>`;
    } else {
      msg.innerHTML = `<div class="chat-bubble">${text}</div><div class="avatar avatar-sm" style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;font-size:.75rem;">JM</div>`;
    }
    messages.appendChild(msg);
    messages.scrollTop = messages.scrollHeight;
  }

  function getReply(text) {
    const lower = text.toLowerCase();
    for (const r of responses) {
      if (r.keys.some(k => lower.includes(k))) return r.reply;
    }
    return defaultReply;
  }

  function send() {
    const val = input.value.trim();
    if (!val) return;
    addMessage(val, 'user');
    input.value = '';
    setTimeout(() => {
      addMessage('<i class="fas fa-circle-notch fa-spin"></i> Thinking...', 'bot');
      setTimeout(() => {
        const lastBot = messages.querySelectorAll('.chat-msg.bot');
        const last = lastBot[lastBot.length - 1];
        if (last) last.querySelector('.chat-bubble').textContent = getReply(val);
      }, 900);
    }, 300);
  }

  sendBtn?.addEventListener('click', send);
  input?.addEventListener('keypress', e => { if (e.key === 'Enter') send(); });
})();

/* ---- NEWSLETTER FORM ---- */
(function initNewsletter() {
  const btn = $('#newsletterBtn');
  const input = $('#newsletterEmail');
  if (!btn || !input) return;
  btn.addEventListener('click', () => {
    const val = input.value.trim();
    if (!val || !val.includes('@')) {
      showToast('Please enter a valid email address.', 'error');
      return;
    }
    showToast('🌱 You\'ve subscribed! Farming tips incoming.', 'success');
    input.value = '';
  });
})();

/* ---- SIDEBAR TOGGLE (for dashboard pages) ---- */
(function initSidebarToggle() {
  const toggleBtns = $$('[data-sidebar-toggle]');
  const sidebar = $('#sidebar');
  if (!sidebar) return;
  toggleBtns.forEach(btn => {
    btn.addEventListener('click', () => sidebar.classList.toggle('open'));
  });
  // Click outside to close on mobile
  document.addEventListener('click', e => {
    if (window.innerWidth < 900 && sidebar.classList.contains('open')) {
      if (!sidebar.contains(e.target) && !e.target.closest('[data-sidebar-toggle]')) {
        sidebar.classList.remove('open');
      }
    }
  });
})();

/* ---- PRODUCT WISHLIST TOGGLE ---- */
document.addEventListener('click', e => {
  const btn = e.target.closest('.product-wishlist');
  if (!btn) return;
  const wasActive = btn.classList.toggle('active');
  btn.innerHTML = wasActive ? '<i class="fas fa-heart"></i>' : '<i class="far fa-heart"></i>';
  showToast(wasActive ? '❤️ Added to wishlist!' : 'Removed from wishlist', wasActive ? 'success' : 'info');
});

/* ---- TABLE SEARCH (for admin/dashboard tables) ---- */
document.addEventListener('input', e => {
  const input = e.target.closest('.table-search-input');
  if (!input) return;
  const tableId = input.dataset.table;
  const tbody = $(`#${tableId} tbody`);
  if (!tbody) return;
  const val = input.value.toLowerCase();
  $$('tr', tbody).forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(val) ? '' : 'none';
  });
});

/* ---- FILTER BUTTONS ---- */
document.addEventListener('click', e => {
  const btn = e.target.closest('.filter-btn');
  if (!btn) return;
  const group = btn.closest('[id*="Filter"], .filter-group');
  if (!group) return;
  $$('.filter-btn', group).forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
});

/* ---- MODAL SYSTEM ---- */
window.openModal = function(id) {
  const modal = $(`#${id}`);
  const overlay = $('#overlay');
  if (modal) modal.classList.add('active');
  if (overlay) overlay.classList.add('active');
  document.body.style.overflow = 'hidden';
};
window.closeModal = function(id) {
  const modal = $(`#${id}`);
  const overlay = $('#overlay');
  if (modal) modal.classList.remove('active');
  if (overlay) overlay.classList.remove('active');
  document.body.style.overflow = '';
};
document.addEventListener('click', e => {
  if (e.target.classList.contains('modal-close') || e.target.id === 'overlay') {
    $$('.modal.active').forEach(m => m.classList.remove('active'));
    $('#overlay')?.classList.remove('active');
    document.body.style.overflow = '';
  }
});

/* ---- SMS TAB SWITCHING ---- */
document.addEventListener('click', e => {
  const tab = e.target.closest('.sms-tab');
  if (!tab) return;
  const container = tab.closest('.sms-type-tabs');
  if (!container) return;
  $$('.sms-tab', container).forEach(t => t.classList.remove('active'));
  tab.classList.add('active');
});

/* ---- SMS CHAR COUNT ---- */
document.addEventListener('input', e => {
  const ta = e.target.closest('.sms-textarea');
  if (!ta) return;
  const counter = ta.parentElement?.querySelector('.sms-char-count');
  if (counter) counter.textContent = `${ta.value.length}/160`;
});

/* ---- SIDEBAR ACTIVE LINK ---- */
(function highlightSidebarLink() {
  const path = window.location.pathname.split('/').pop();
  $$('.sidebar-link').forEach(l => {
    const href = l.getAttribute('href')?.split('/').pop();
    if (href === path) {
      l.classList.add('active');
      l.closest('.sidebar')?.querySelectorAll('.sidebar-section-label');
    }
  });
})();

/* ---- ENHANCED DROPDOWN TOGGLE ---- */
document.addEventListener('click', e => {
  const toggle = e.target.closest('[data-dropdown-toggle]');
  if (!toggle) return;
  const targetId = toggle.dataset.dropdownToggle;
  const menu = document.getElementById(targetId) || toggle.parentElement.querySelector('.dropdown-menu');
  if (!menu) return;
  const wasOpen = menu.classList.contains('open');
  // Close other dropdowns
  $$('.dropdown-menu.open').forEach(m => { if (m !== menu) m.classList.remove('open'); });
  menu.classList.toggle('open', !wasOpen);
  e.stopPropagation();
});
// Close dropdowns on outside click
document.addEventListener('click', () => {
  $$('.dropdown-menu.open').forEach(m => m.classList.remove('open'));
});

/* ---- TAB SWITCHING (enhanced) ---- */
document.addEventListener('click', e => {
  const tab = e.target.closest('[data-tab]');
  if (!tab) return;
  const tabGroup = tab.closest('[data-tab-group]') || tab.parentElement;
  if (!tabGroup) return;
  const tabs = $$('[data-tab]', tabGroup);
  const panes = tabGroup.dataset.tabGroup
    ? $$(`[data-tab-pane="${tabGroup.dataset.tabGroup}"]`)
    : [];
  tabs.forEach(t => t.classList.remove('active'));
  tab.classList.add('active');
  panes.forEach(p => p.classList.toggle('active', p.dataset.tabPane === tab.dataset.tab));
});

/* ---- SIDEBAR OVERLAY (mobile) ---- */
(function initSidebarOverlay() {
  const sidebar = $('#sidebar');
  if (!sidebar) return;
  const overlay = document.createElement('div');
  overlay.className = 'sidebar-overlay';
  overlay.id = 'sidebarOverlay';
  document.body.appendChild(overlay);
  document.addEventListener('click', e => {
    const toggle = e.target.closest('[data-sidebar-toggle]');
    if (toggle) {
      sidebar.classList.toggle('open');
      overlay.classList.toggle('active');
      document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
    }
    if (e.target === overlay) {
      sidebar.classList.remove('open');
      overlay.classList.remove('active');
      document.body.style.overflow = '';
    }
  });
})();

/* ---- CONFIRM DIALOG ---- */
window.confirmAction = function(msg, callback) {
  if (confirm(msg || 'Are you sure?')) callback();
};
