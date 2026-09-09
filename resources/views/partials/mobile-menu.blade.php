<div class="mobile-overlay" id="mobileOverlay" onclick="closeMobileMenu()"></div>
<div class="mobile-menu" id="mobileMenu" role="navigation" aria-label="Mobile navigation">
  <div class="mobile-menu-header">
    <a href="{{ route('home') }}" class="mobile-menu-logo">
      <div class="nav-logo-icon"><i class="fas fa-seedling"></i></div>
      <div class="mobile-menu-logo-text">
        <strong>AgriTech Pro</strong>
        <span>Smart Farming</span>
      </div>
    </a>
    <button class="mobile-menu-close" id="mobileMenuClose" aria-label="Close menu" onclick="closeMobileMenu()">
      <i class="fas fa-times"></i>
    </button>
  </div>

  <form action="{{ route('marketplace') }}" method="GET" class="mobile-menu-search">
    <i class="fas fa-search"></i>
    <input type="text" name="q" placeholder="Search courses, products, diseases..." autocomplete="off">
  </form>

  <nav class="mobile-menu-links">
    <a href="{{ route('home') }}" class="mobile-menu-link {{ request()->routeIs('home') ? 'active' : '' }}">
      <div class="mobile-menu-link-icon"><i class="fas fa-home"></i></div>
      <span>Home</span>
    </a>
    <a href="{{ route('learn') }}" class="mobile-menu-link {{ request()->routeIs('learn*') ? 'active' : '' }}">
      <div class="mobile-menu-link-icon"><i class="fas fa-graduation-cap"></i></div>
      <span>Learning Center</span>
    </a>
    <a href="{{ route('marketplace') }}" class="mobile-menu-link {{ request()->routeIs('marketplace*') ? 'active' : '' }}">
      <div class="mobile-menu-link-icon"><i class="fas fa-store"></i></div>
      <span>Marketplace</span>
    </a>
    <a href="{{ route('innovation') }}" class="mobile-menu-link {{ request()->routeIs('innovation*') ? 'active' : '' }}">
      <div class="mobile-menu-link-icon"><i class="fas fa-lightbulb"></i></div>
      <span>Innovation Hub</span>
    </a>
    <a href="{{ route('delivery') }}" class="mobile-menu-link {{ request()->routeIs('delivery*') ? 'active' : '' }}">
      <div class="mobile-menu-link-icon"><i class="fas fa-truck"></i></div>
      <span>Delivery Tracking</span>
    </a>
    <a href="{{ route('diseases') }}" class="mobile-menu-link {{ request()->routeIs('diseases*') ? 'active' : '' }}">
      <div class="mobile-menu-link-icon"><i class="fas fa-bug"></i></div>
      <span>Disease Detection</span>
    </a>

    @auth
      <div class="mobile-menu-divider"></div>
      <a href="{{ route('dashboard') }}" class="mobile-menu-link {{ request()->routeIs('dashboard*') ? 'active' : '' }}">
        <div class="mobile-menu-link-icon"><i class="fas fa-th-large"></i></div>
        <span>My Dashboard</span>
      </a>
      <a href="{{ route('profile') }}" class="mobile-menu-link {{ request()->routeIs('profile*') ? 'active' : '' }}">
        <div class="mobile-menu-link-icon"><i class="fas fa-user"></i></div>
        <span>My Profile</span>
      </a>
      <a href="{{ route('notifications') }}" class="mobile-menu-link {{ request()->routeIs('notifications*') ? 'active' : '' }}">
        <div class="mobile-menu-link-icon"><i class="fas fa-bell"></i></div>
        <span>Notifications</span>
        @if(Auth::user()->unreadNotifications()->count() > 0)
          <span class="mobile-menu-badge">{{ Auth::user()->unreadNotifications()->count() }}</span>
        @endif
      </a>
      @if(Auth::user()->isAdmin())
        <a href="{{ route('admin.index') }}" class="mobile-menu-link">
          <div class="mobile-menu-link-icon"><i class="fas fa-shield-alt"></i></div>
          <span>Admin Panel</span>
        </a>
      @endif
    @endauth
  </nav>

  <div class="mobile-menu-footer">
    @auth
      <div class="mobile-menu-user">
        <div class="avatar avatar-md" style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;font-size:.85rem;">{{ Auth::user()->initials }}</div>
        <div class="mobile-menu-user-info">
          <div class="mobile-menu-user-name">{{ Auth::user()->full_name }}</div>
          <div class="mobile-menu-user-role">{{ ucfirst(Auth::user()->role) }}{{ Auth::user()->district ? ' · '.Auth::user()->district : '' }}</div>
        </div>
      </div>
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline btn-md mobile-menu-signout">
          <i class="fas fa-sign-out-alt"></i> Sign Out
        </button>
      </form>
    @else
      <a href="{{ route('login') }}" class="btn btn-outline btn-md mobile-menu-cta">
        <i class="fas fa-sign-in-alt"></i> Sign In
      </a>
      <a href="{{ route('register') }}" class="btn btn-primary btn-md mobile-menu-cta">
        <i class="fas fa-seedling"></i> Get Started Free
      </a>
    @endauth
  </div>
</div>

<style>
/* ── Mobile Menu Overlay ── */
.mobile-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.55);
  backdrop-filter: blur(6px);
  -webkit-backdrop-filter: blur(6px);
  z-index: 998;
  opacity: 0;
  visibility: hidden;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.mobile-overlay.active {
  opacity: 1;
  visibility: visible;
}

/* ── Mobile Menu Panel (slide from left) ── */
.mobile-menu {
  position: fixed;
  top: 0;
  left: 0;
  bottom: 0;
  width: 300px;
  max-width: 85vw;
  background: var(--bg-card);
  border-right: 1px solid var(--border);
  padding: 0 20px 24px;
  transform: translateX(-100%);
  opacity: 0;
  visibility: hidden;
  transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: 999;
  box-shadow: 4px 0 40px rgba(0, 0, 0, 0.15);
  display: flex;
  flex-direction: column;
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
}
.mobile-menu.open {
  transform: translateX(0);
  opacity: 1;
  visibility: visible;
}

/* ── Header ── */
.mobile-menu-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 0;
  margin-bottom: 12px;
  border-bottom: 1px solid var(--border);
}
.mobile-menu-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
}
.mobile-menu-logo-text {
  display: flex;
  flex-direction: column;
  line-height: 1.15;
}
.mobile-menu-logo-text strong {
  font-family: var(--font-display);
  font-size: 1.05rem;
  font-weight: 800;
  color: var(--text);
  letter-spacing: -0.01em;
}
.mobile-menu-logo-text span {
  font-size: 0.7rem;
  color: var(--primary);
  font-weight: 600;
  letter-spacing: 0.06em;
  text-transform: uppercase;
}
.mobile-menu-close {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: var(--bg-2);
  border: 1px solid var(--border);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--text-muted);
  font-size: 1.05rem;
  transition: all var(--t-fast);
  cursor: pointer;
  flex-shrink: 0;
}
.mobile-menu-close:hover {
  background: var(--gray-200);
  color: var(--text);
  border-color: var(--gray-300);
}
[data-theme="dark"] .mobile-menu-close:hover {
  background: var(--gray-700);
}

/* ── Search ── */
.mobile-menu-search {
  display: flex;
  align-items: center;
  gap: 10px;
  background: var(--bg-2);
  border: 1.5px solid var(--border);
  border-radius: var(--radius-full);
  padding: 10px 16px;
  margin-bottom: 16px;
  transition: border-color var(--t-fast);
}
.mobile-menu-search:focus-within {
  border-color: var(--primary);
  box-shadow: 0 0 0 3px var(--primary-glow);
}
.mobile-menu-search i {
  color: var(--text-muted);
  font-size: 0.9rem;
}
.mobile-menu-search input {
  flex: 1;
  background: none;
  border: none;
  font-size: 0.95rem;
  color: var(--text);
  min-height: 24px;
}
.mobile-menu-search input::placeholder {
  color: var(--gray-400);
}

/* ── Navigation Links ── */
.mobile-menu-links {
  display: flex;
  flex-direction: column;
  gap: 2px;
  flex: 1;
}
.mobile-menu-link {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 12px 14px;
  border-radius: var(--radius-md);
  font-size: 0.95rem;
  font-weight: 500;
  color: var(--text);
  transition: all var(--t-fast);
  min-height: 50px;
  position: relative;
}
.mobile-menu-link:hover,
.mobile-menu-link.active {
  background: var(--green-50);
  color: var(--primary);
}
[data-theme="dark"] .mobile-menu-link:hover,
[data-theme="dark"] .mobile-menu-link.active {
  background: rgba(22, 163, 74, 0.12);
}
.mobile-menu-link.active {
  font-weight: 600;
}
.mobile-menu-link-icon {
  width: 34px;
  height: 34px;
  border-radius: 9px;
  background: var(--green-50);
  color: var(--primary);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.85rem;
  flex-shrink: 0;
  transition: all var(--t-fast);
}
[data-theme="dark"] .mobile-menu-link-icon {
  background: rgba(22, 163, 74, 0.12);
}
.mobile-menu-link:hover .mobile-menu-link-icon,
.mobile-menu-link.active .mobile-menu-link-icon {
  background: var(--primary);
  color: #fff;
  box-shadow: 0 2px 8px rgba(22, 163, 74, 0.3);
}
.mobile-menu-badge {
  margin-left: auto;
  background: var(--danger);
  color: #fff;
  font-size: 0.7rem;
  font-weight: 800;
  padding: 2px 8px;
  border-radius: 10px;
  min-width: 22px;
  text-align: center;
}
.mobile-menu-divider {
  height: 1px;
  background: var(--border);
  margin: 8px 0;
}

/* ── Footer / CTA ── */
.mobile-menu-footer {
  padding-top: 16px;
  border-top: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  gap: 10px;
  flex-shrink: 0;
}
.mobile-menu-user {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 14px;
  background: var(--green-50);
  border-radius: var(--radius-md);
  margin-bottom: 4px;
}
[data-theme="dark"] .mobile-menu-user {
  background: rgba(22, 163, 74, 0.08);
}
.mobile-menu-user-name {
  font-weight: 700;
  color: var(--text);
  font-size: 0.92rem;
}
.mobile-menu-user-role {
  font-size: 0.75rem;
  color: var(--text-muted);
}
.mobile-menu-signout,
.mobile-menu-cta {
  justify-content: center;
  min-height: 48px;
  width: 100%;
}
.mobile-menu-cta {
  justify-content: center;
}
</style>

<script>
function closeMobileMenu() {
  const btn = document.getElementById('hamburger');
  const menu = document.getElementById('mobileMenu');
  const overlay = document.getElementById('mobileOverlay');
  if (btn) {
    btn.classList.remove('open');
    btn.setAttribute('aria-expanded', 'false');
  }
  if (menu) menu.classList.remove('open');
  if (overlay) overlay.classList.remove('active');
  document.body.style.overflow = '';
}
</script>
