/* ============================================================
   AGRITECH PRO — GLOBAL JAVASCRIPT
   ============================================================ */
'use strict';

const $ = (s, c = document) => c.querySelector(s);
const $$ = (s, c = document) => [...c.querySelectorAll(s)];

/* ============================================================
   1. DARK MODE
   ============================================================ */
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
    // Notify any polling components to refresh with new theme
    document.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: next } }));
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
  const overlay = $('#mobileOverlay');
  if (!btn || !menu) return;

  btn.addEventListener('click', () => {
    const open = btn.classList.toggle('open');
    menu.classList.toggle('open', open);
    if (overlay) overlay.classList.toggle('active', open);
    btn.setAttribute('aria-expanded', open);
    btn.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
    document.body.style.overflow = open ? 'hidden' : '';
  });

  $$('.mobile-menu-link', menu).forEach(l => l.addEventListener('click', close));

  if (overlay) overlay.addEventListener('click', close);
  document.addEventListener('click', e => {
    if (!btn.contains(e.target) && !menu.contains(e.target) && (!overlay || !overlay.contains(e.target))) close();
  });

  document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });

  function close() {
    btn.classList.remove('open');
    menu.classList.remove('open');
    if (overlay) overlay.classList.remove('active');
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

/* ============================================================
   2. ENHANCED TOAST SYSTEM — stacking, progress bars, dismiss
   ============================================================ */
(function initToastSystem() {
  const container = $('#toast-container');
  if (!container) return;

  const icons = {
    success: 'fa-check-circle',
    error: 'fa-exclamation-circle',
    warning: 'fa-exclamation-triangle',
    info: 'fa-info-circle',
    loading: 'fa-circle-notch fa-spin',
  };

  window.showToast = function (msg, type = 'success', duration = 3500) {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `<i class="fas ${icons[type] || icons.success} toast-icon"></i>
      <p class="toast-msg">${msg}</p>
      <button class="toast-dismiss" aria-label="Dismiss">&times;</button>`;

    container.appendChild(toast);

    // Animate in
    requestAnimationFrame(() => toast.classList.add('toast-visible'));

    // Dismiss handler
    const dismissBtn = toast.querySelector('.toast-dismiss');
    dismissBtn.addEventListener('click', () => dismissToast(toast));

    // Auto-dismiss
    if (duration > 0) {
      setTimeout(() => dismissToast(toast), duration);
    }

    return toast; // return for manual control (e.g. loading toasts)
  };

  window.dismissToast = function (toast) {
    if (!toast || toast.classList.contains('toast-dismissing')) return;
    toast.classList.remove('toast-visible');
    toast.classList.add('toast-dismissing');
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(40px)';
    setTimeout(() => { if (toast.parentNode) toast.parentNode.removeChild(toast); }, 400);
  };

  // Allow clicking on toast body to dismiss
  container.addEventListener('click', (e) => {
    const toast = e.target.closest('.toast');
    if (toast && !e.target.classList.contains('toast-dismiss')) {
      dismissToast(toast);
    }
  });
})();

/* ============================================================
   3. BUTTON LOADING STATES — inline spinner, disable
   ============================================================ */
(function initButtonLoading() {
  // Store original button content
  const originalTexts = new WeakMap();

  window.setButtonLoading = function (btn, loading) {
    if (!btn) return;
    if (loading) {
      // Save original content
      originalTexts.set(btn, btn.innerHTML);
      btn.disabled = true;
      const spinner = '<i class="fas fa-circle-notch fa-spin btn-spinner"></i> ';
      // Replace icon-only buttons gracefully
      const hasText = btn.textContent.trim().length > 0;
      if (hasText) {
        btn.innerHTML = spinner + btn.innerHTML;
      } else {
        btn.innerHTML = spinner;
      }
      btn.dataset.loading = 'true';
    } else {
      btn.disabled = false;
      btn.innerHTML = originalTexts.get(btn) || btn.innerHTML;
      delete btn.dataset.loading;
    }
  };

  // Auto-disable forms on submit (prevents double submission)
  document.addEventListener('submit', (e) => {
    const form = e.target;
    if (form.dataset.ajax === 'true') return; // AJAX forms handle themselves
    const submitBtn = form.querySelector('[type="submit"]');
    if (submitBtn && !submitBtn.dataset.loading) {
      setButtonLoading(submitBtn, true);
    }
  });
})();

/* ============================================================
   4. AJAX FORM HANDLER — data-ajax forms
   ============================================================ */
(function initAjaxForms() {
  function handleFormSubmit(e) {
    const form = e.target;
    if (form.dataset.ajax !== 'true') return;
    e.preventDefault();

    const submitBtn = form.querySelector('[type="submit"]');
    const method = (form.querySelector('[name="_method"]')?.value || form.method || 'POST').toUpperCase();
    const action = form.action;
    const formData = new FormData(form);

    // Clear previous inline errors
    form.querySelectorAll('.ajax-error').forEach(el => el.remove());
    form.querySelectorAll('.form-input-error').forEach(el => el.classList.remove('form-input-error'));

    // Show button loading
    if (submitBtn) setButtonLoading(submitBtn, true);

    // Determine if this has file uploads (use FormData directly for files)
    const hasFiles = form.querySelector('[type="file"]');
    const contentType = hasFiles ? 'multipart/form-data' : 'application/json';

    const fetchOptions = {
      method: method,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
        'Accept': 'application/json',
      },
    };

    if (hasFiles) {
      fetchOptions.body = formData;
    } else {
      // For non-file forms, send as JSON
      const jsonData = {};
      formData.forEach((value, key) => { jsonData[key] = value; });
      fetchOptions.body = JSON.stringify(jsonData);
      fetchOptions.headers['Content-Type'] = 'application/json';
    }

    // Handle _method override
    if (method !== 'POST' && !hasFiles) {
      // Laravel handles _method via form field
    }

    fetch(action, fetchOptions)
      .then(async response => {
        const data = await response.json().catch(() => null);
        if (!response.ok) {
          const err = new Error(data?.message || `Request failed (${response.status})`);
          err.status = response.status;
          err.data = data;
          throw err;
        }
        return data;
      })
      .then(data => {
        if (submitBtn) setButtonLoading(submitBtn, false);

        // Show success toast
        const msg = data?.message || data?.msg || 'Done!';
        if (data?.toast !== false) showToast(msg, 'success');

        // Auto-close modal if inside one
        const modal = form.closest('.modal, [class*="modal"]');
        if (modal && data?.closeModal !== false) {
          const modalId = modal.id;
          if (window.closeModal) window.closeModal(modalId);
        }

        // Fire success event for page-specific handlers
        form.dispatchEvent(new CustomEvent('ajax:success', { detail: data }));

        // If there's a redirect, do it (but show toast first)
        if (data?.redirect) {
          setTimeout(() => { window.location.href = data.redirect; }, 600);
          return;
        }

        // If there's a replace target, update it
        if (data?.replace && data?.html) {
          const target = $(data.replace);
          if (target) target.outerHTML = data.html;
        }

        // If there's an update target, set innerHTML
        if (data?.update && data?.html) {
          const target = $(data.update);
          if (target) target.innerHTML = data.html;
        }

        // If the form has data-reset, reset it
        if (form.dataset.reset !== 'false') {
          form.reset();
        }

        // Remove file preview if any
        if (hasFiles) {
          const preview = form.querySelector('.upload-preview');
          if (preview) preview.innerHTML = '';
        }
      })
      .catch(err => {
        if (submitBtn) setButtonLoading(submitBtn, false);

        // Handle validation errors (Laravel style: { errors: { field: [msg] } })
        if (err.data?.errors) {
          const errors = err.data.errors;
          Object.keys(errors).forEach(field => {
            const input = form.querySelector(`[name="${field}"], [name="${field}[]"]`);
            if (input) {
              input.classList.add('form-input-error');
              const errorEl = document.createElement('span');
              errorEl.className = 'ajax-error';
              errorEl.textContent = errors[field][0];
              input.parentNode.appendChild(errorEl);
            }
          });
          showToast('Please fix the highlighted errors.', 'error', 5000);
        } else if (err.data?.message) {
          showToast(err.data.message, 'error', 5000);
        } else {
          showToast(err.message || 'Something went wrong. Please try again.', 'error', 5000);
        }

        form.dispatchEvent(new CustomEvent('ajax:error', { detail: err }));
      });
  }

  // Listen for form submissions (delegated)
  document.addEventListener('submit', handleFormSubmit);
})();

/* ============================================================
   5. AJAX LINK HANDLER — for pagination, sorting, filtering
   ============================================================ */
(function initAjaxLinks() {
  document.addEventListener('click', (e) => {
    const link = e.target.closest('[data-ajax-link]');
    if (!link) return;
    e.preventDefault();

    const url = link.href || link.dataset.url;
    const target = link.dataset.ajaxLink || link.dataset.target;
    const replace = link.dataset.replace || false;
    const pushState = link.dataset.pushState !== 'false';

    if (!url) return;

    // Show loading spinner in the target area
    const targetEl = target ? $(target) : null;
    if (targetEl && !replace) {
      targetEl.style.opacity = '0.4';
      targetEl.style.pointerEvents = 'none';
    }

    fetch(url, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'text/html, application/json',
      },
    })
      .then(r => {
        const ct = r.headers.get('content-type') || '';
        if (ct.includes('json')) return r.json().then(d => ({ json: d, html: null }));
        return r.text().then(t => ({ json: null, html: t }));
      })
      .then(({ json, html }) => {
        if (targetEl) {
          if (replace) {
            targetEl.outerHTML = html || json?.html || '';
          } else {
            targetEl.innerHTML = html || json?.html || '';
            targetEl.style.opacity = '';
            targetEl.style.pointerEvents = '';
          }
        }

        // Update URL without page reload
        if (pushState && url !== window.location.href) {
          window.history.pushState({ ajax: true }, '', url);
        }

        // Fire custom event for page-specific logic
        document.dispatchEvent(new CustomEvent('ajax:content-updated', { detail: { url, target } }));
      })
      .catch(err => {
        if (targetEl) {
          targetEl.style.opacity = '';
          targetEl.style.pointerEvents = '';
        }
        showToast('Failed to load content. Please try again.', 'error');
      });
  });

  // Handle browser back/forward for AJAX history
  window.addEventListener('popstate', (e) => {
    if (e.state?.ajax) {
      // Reload the page content from the current URL
      const mainContent = $('#main-content, .ajax-content, [data-ajax-content]');
      if (mainContent) {
        fetch(window.location.href, {
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
        })
          .then(r => r.text())
          .then(html => {
            mainContent.innerHTML = html;
            document.dispatchEvent(new CustomEvent('ajax:content-updated'));
          });
      }
    }
  });
})();

/* ============================================================
   6. UPLOAD PROGRESS — for file inputs with progress bar
   ============================================================ */
(function initUploadProgress() {
  // Auto-bind to file inputs with data-upload="true"
  document.addEventListener('change', (e) => {
    const input = e.target.closest('[data-upload="true"]');
    if (!input || !input.files?.length) return;

    const form = input.closest('form');
    if (!form) return;

    // Create or get progress container
    let progressEl = form.querySelector('.upload-progress');
    if (!progressEl) {
      progressEl = document.createElement('div');
      progressEl.className = 'upload-progress';
      progressEl.innerHTML = `
        <div class="upload-progress-bar">
          <div class="upload-progress-fill"></div>
        </div>
        <div class="upload-progress-info">
          <span class="upload-progress-text">Preparing...</span>
          <span class="upload-progress-pct">0%</span>
        </div>
        <button type="button" class="upload-cancel-btn" title="Cancel upload">&times;</button>
      `;
      form.appendChild(progressEl);
    }

    const fill = progressEl.querySelector('.upload-progress-fill');
    const text = progressEl.querySelector('.upload-progress-text');
    const pct = progressEl.querySelector('.upload-progress-pct');
    const cancelBtn = progressEl.querySelector('.upload-cancel-btn');
    let aborted = false;
    let xhr = null;

    // Show progress
    progressEl.classList.add('upload-progress-active');

    // Cancel handler
    cancelBtn.onclick = () => {
      aborted = true;
      if (xhr) xhr.abort();
      text.textContent = 'Cancelled';
      pct.textContent = '';
      fill.style.width = '0%';
      setTimeout(() => progressEl.classList.remove('upload-progress-active'), 1500);
    };

    // Override form submission to use XHR with progress
    const origSubmit = form.onsubmit;
    form.onsubmit = (e) => {
      if (aborted) return;
      e.preventDefault();

      const submitBtn = form.querySelector('[type="submit"]');
      if (submitBtn) setButtonLoading(submitBtn, true);

      const formData = new FormData(form);
      const method = (form.querySelector('[name="_method"]')?.value || form.method || 'POST').toUpperCase();
      xhr = new XMLHttpRequest();

      xhr.upload.onprogress = (e) => {
        if (!e.lengthComputable || aborted) return;
        const percent = Math.round((e.loaded / e.total) * 100);
        fill.style.width = percent + '%';
        pct.textContent = percent + '%';
        if (percent < 100) {
          text.textContent = 'Uploading...';
        } else {
          text.textContent = 'Processing...';
          pct.textContent = '';
        }
      };

      xhr.onload = () => {
        if (aborted) return;
        if (submitBtn) setButtonLoading(submitBtn, false);

        try {
          const data = JSON.parse(xhr.responseText);
          if (xhr.status >= 200 && xhr.status < 300) {
            text.textContent = '✓ Upload Complete!';
            pct.textContent = '100%';
            fill.style.width = '100%';
            fill.style.background = 'var(--success)';
            setTimeout(() => progressEl.classList.remove('upload-progress-active'), 2000);
            if (data?.message) showToast(data.message, 'success');
            if (data?.redirect) setTimeout(() => window.location.href = data.redirect, 800);
            form.dispatchEvent(new CustomEvent('ajax:success', { detail: data }));
          } else {
            text.textContent = data?.message || 'Upload failed';
            pct.textContent = '';
            showToast(data?.message || 'Upload failed. Please try again.', 'error');
          }
        } catch (e) {
          text.textContent = 'Upload failed';
          showToast('Upload failed. Please try again.', 'error');
        }
      };

      xhr.onerror = () => {
        if (aborted) return;
        if (submitBtn) setButtonLoading(submitBtn, false);
        text.textContent = 'Upload failed';
        showToast('Network error. Please try again.', 'error');
      };

      xhr.open(method, form.action);
      xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name=csrf-token]')?.content || '');
      xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
      if (method !== 'POST') {
        // Laravel handles _method via form field in FormData
      }
      xhr.send(formData);
    };
  });
})();

/* ============================================================
   7. POLLING SYSTEM — real-time updates without WebSockets
   ============================================================ */
(function initPolling() {
  const pollers = [];

  window.startPolling = function (url, callback, interval = 15000, options = {}) {
    const poller = {
      url,
      callback,
      interval,
      options,
      timer: null,
      active: true,
      lastResult: null,
    };

    const poll = () => {
      if (!poller.active || document.hidden) return;

      fetch(url, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
        },
      })
        .then(r => r.json().catch(() => null))
        .then(data => {
          if (data && poller.active) {
            const changed = JSON.stringify(data) !== JSON.stringify(poller.lastResult);
            if (changed || options.force) {
              poller.lastResult = data;
              poller.callback(data, changed);
            }
          }
        })
        .catch(() => { /* silent fail for polling */ });

      if (poller.active) {
        poller.timer = setTimeout(poll, interval);
      }
    };

    // Start polling
    poller.timer = setTimeout(poll, interval);

    // Stop on page hide, restart on show
    document.addEventListener('visibilitychange', () => {
      if (document.hidden) {
        if (poller.timer) clearTimeout(poller.timer);
      } else if (poller.active) {
        poll(); // immediate poll on return
      }
    });

    pollers.push(poller);
    return poller;
  };

  window.stopPolling = function (poller) {
    if (poller) {
      poller.active = false;
      if (poller.timer) clearTimeout(poller.timer);
    }
  };

  window.stopAllPolling = function () {
    pollers.forEach(p => {
      p.active = false;
      if (p.timer) clearTimeout(p.timer);
    });
    pollers.length = 0;
  };

  // Auto-poll notification badge
  document.addEventListener('DOMContentLoaded', () => {
    const badge = $('.notification-badge');
    const notifUrl = badge?.dataset?.pollUrl;
    if (badge && notifUrl) {
      startPolling(notifUrl, (data) => {
        if (data?.count !== undefined) {
          if (data.count > 0) {
            badge.textContent = data.count > 99 ? '99+' : data.count;
            badge.style.display = 'flex';
          } else {
            badge.style.display = 'none';
          }
        }
      }, 30000, { force: true });
    }
  });
})();

/* ============================================================
   8. SEARCH & FILTER UTILITIES — AJAX instant search
   ============================================================ */
(function initSearchFilters() {
  // Debounce utility
  function debounce(fn, ms = 300) {
    let timer;
    return (...args) => {
      clearTimeout(timer);
      timer = setTimeout(() => fn(...args), ms);
    };
  }

  // Auto-submit filter forms on change with debounce
  document.addEventListener('change', debounce((e) => {
    const form = e.target.closest('[data-auto-submit]');
    if (!form) return;
    // Trigger form submission (will be caught by AJAX handler if data-ajax="true")
    const submitBtn = form.querySelector('[type="submit"]');
    if (submitBtn) submitBtn.click();
  }, 300));

  // Live search input (debounced)
  document.addEventListener('input', debounce((e) => {
    const input = e.target.closest('[data-live-search]');
    if (!input) return;
    const form = input.closest('form');
    if (form && form.dataset.ajax === 'true') {
      const submitBtn = form.querySelector('[type="submit"]');
      if (submitBtn) submitBtn.click();
    }
  }, 400));

  // Client-side table search (existing, enhanced)
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
})();

/* ============================================================
   9. INLINE SKELETON LOADER — for table/content loading
   ============================================================ */
(function initSkeleton() {
  window.showSkeleton = function (container, rows = 3) {
    const el = typeof container === 'string' ? $(container) : container;
    if (!el) return;
    el.innerHTML = '<div class="skeleton-loader">' +
      Array(rows).fill('<div class="skeleton-row"><div class="skeleton-line w-75"></div><div class="skeleton-line w-50"></div><div class="skeleton-line w-60"></div></div>').join('') +
      '</div>';
  };

  window.hideSkeleton = function (container) {
    const el = typeof container === 'string' ? $(container) : container;
    if (!el) return;
    const skeleton = el.querySelector('.skeleton-loader');
    if (skeleton) skeleton.remove();
  };
})();

/* ============================================================
   10. COUNTER ANIMATION
   ============================================================ */
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

/* ============================================================
   11. CHATBOT
   ============================================================ */
(function initChatbot() {
  const toggleBtn = $('#chatbotToggle');
  const panel = $('#chatbotPanel');
  const input = $('#chatInput');
  const sendBtn = $('#chatSend');
  const messages = $('#chatMessages');
  if (!toggleBtn || !panel) return;

  const responses = [
    { keys: ['maize', 'corn', 'maze'], reply: '🌽 For maize: plant at start of rains (Oct-Nov), use certified seed, apply NPK fertilizer at planting and top-dress with urea at knee height. Watch for Fall Armyworm - spray early if spotted!' },
    { keys: ['disease', 'sick', 'pest', 'bug', 'worm'], reply: '🔍 Describe the symptoms - yellowing leaves, spots, wilting? Our Crop Disease Library has 200+ conditions with treatments. Also try our AI Disease Detection tool (upload a photo).' },
    { keys: ['weather', 'rain', 'forecast', 'climate'], reply: '🌤 Current forecast: Lusaka 28°C, partly cloudy. Rains expected Friday-Saturday. Visit the Weather tab in your dashboard for 7-day forecasts by district.' },
    { keys: ['price', 'market', 'sell', 'buy'], reply: '💰 Current market prices: Maize K280/50kg · Tomatoes K120/box · Soybeans K450/50kg · Groundnuts K550/50kg. Updated daily in the Marketplace!' },
    { keys: ['deliver', 'order', 'track', 'shipping'], reply: '🚛 Track your orders in real-time at the Delivery Tracking page. You\'ll also get SMS updates at every stage - from dispatch to arrival.' },
    { keys: ['fertilizer', 'npk', 'compost', 'manure'], reply: '🌱 For most crops: apply NPK (10:20:10) at planting + Urea top-dress 6 weeks later. Organic compost improves soil structure - aim for 2 tonnes/hectare.' },
    { keys: ['course', 'learn', 'training', 'lesson'], reply: '📚 We have 342 courses! Bestsellers: Modern Maize Farming, Drone Precision Ag (K450), Profitable Dairy (K280). Go to the Learning Center to explore all.' },
    { keys: ['register', 'sign', 'join', 'account'], reply: '✅ Joining is quick! Click "Get Started" at the top of the page. You\'ll get instant access to all courses and marketplace features.' },
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
  $$('.dropdown-menu.open').forEach(m => { if (m !== menu) m.classList.remove('open'); });
  menu.classList.toggle('open', !wasOpen);
  e.stopPropagation();
});
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

/* ---- ISSUE REPORT MODAL ---- */
(function initReportIssue() {
  const modalHtml =
    '<div id="issueModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;" onclick="if(event.target===this)closeIssueModal()">' +
      '<div style="background:var(--bg-card);border-radius:var(--radius-xl);max-width:480px;width:90%;padding:24px;box-shadow:var(--shadow-xl);">' +
        '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">' +
          '<div style="font-size:1rem;font-weight:800;color:var(--text);">⚠️ Report Issue</div>' +
          '<button onclick="closeIssueModal()" style="background:none;border:none;font-size:1.4rem;cursor:pointer;color:var(--text-muted);">&times;</button>' +
        '</div>' +
        '<form id="issueForm" method="POST" style="display:flex;flex-direction:column;gap:14px;">' +
          '<input type="hidden" name="_token" value="' + (document.querySelector('meta[name="csrf-token"]')?.content||'') + '">' +
          '<div class="form-group">' +
            '<label class="form-label">Subject *</label>' +
            '<input type="text" name="subject" class="form-input" placeholder="e.g. Wrong item delivered" required>' +
          '</div>' +
          '<div class="form-group">' +
            '<label class="form-label">Describe the issue *</label>' +
            '<textarea name="message" class="form-input" rows="4" placeholder="Tell us what went wrong..." required></textarea>' +
          '</div>' +
          '<div style="display:flex;gap:8px;justify-content:flex-end;">' +
            '<button type="button" onclick="closeIssueModal()" class="btn btn-outline btn-md">Cancel</button>' +
            '<button type="submit" class="btn btn-primary btn-md"><i class="fas fa-paper-plane"></i> Submit</button>' +
          '</div>' +
        '</form>' +
      '</div>' +
    '</div>';
  const div = document.createElement('div');
  div.innerHTML = modalHtml;
  document.body.appendChild(div);
})();

window.reportIssue = function(orderId, orderNumber) {
  const modal = document.getElementById('issueModal');
  const form = document.getElementById('issueForm');
  if (!modal || !form) return;
  form.action = '/orders/' + orderId + '/report-issue';
  form.querySelector('[name=subject]').value = '';
  form.querySelector('[name=message]').value = '';
  modal.style.display = 'flex';
};

window.closeIssueModal = function() {
  const modal = document.getElementById('issueModal');
  if (modal) modal.style.display = 'none';
};
