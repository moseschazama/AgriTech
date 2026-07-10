<div class="mobile-menu" id="mobileMenu" role="navigation" aria-label="Mobile navigation">
  <div class="mobile-menu-header">
    <a href="{{ route('home') }}" class="mobile-menu-logo">
      <div class="nav-logo-icon"><i class="fas fa-seedling"></i></div>
      <strong>AgriTech Pro</strong>
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
    <a href="{{ route('home') }}"        class="mobile-menu-link {{ request()->routeIs('home') ? 'active' : '' }}"><i class="fas fa-home"></i> Home</a>
    <a href="{{ route('learn') }}"       class="mobile-menu-link {{ request()->routeIs('learn*') ? 'active' : '' }}"><i class="fas fa-graduation-cap"></i> Learning Center</a>
    <a href="{{ route('marketplace') }}" class="mobile-menu-link {{ request()->routeIs('marketplace*') ? 'active' : '' }}"><i class="fas fa-store"></i> Marketplace</a>
    <a href="{{ route('innovation') }}"  class="mobile-menu-link {{ request()->routeIs('innovation*') ? 'active' : '' }}"><i class="fas fa-lightbulb"></i> Innovation Hub</a>
    <a href="{{ route('delivery') }}"    class="mobile-menu-link {{ request()->routeIs('delivery*') ? 'active' : '' }}"><i class="fas fa-truck"></i> Delivery Tracking</a>
    <a href="{{ route('diseases') }}"    class="mobile-menu-link {{ request()->routeIs('diseases*') ? 'active' : '' }}"><i class="fas fa-bug"></i> Disease Detection</a>

    @auth
      <div class="mobile-menu-divider"></div>
      <a href="{{ route('dashboard') }}" class="mobile-menu-link {{ request()->routeIs('dashboard*') ? 'active' : '' }}"><i class="fas fa-th-large"></i> My Dashboard</a>
      <a href="{{ route('profile') }}"   class="mobile-menu-link {{ request()->routeIs('profile*') ? 'active' : '' }}"><i class="fas fa-user"></i> My Profile</a>
      <a href="{{ route('notifications') }}" class="mobile-menu-link {{ request()->routeIs('notifications*') ? 'active' : '' }}"><i class="fas fa-bell"></i> Notifications</a>
      @if(Auth::user()->isAdmin())
        <a href="{{ route('admin.index') }}" class="mobile-menu-link"><i class="fas fa-shield-alt"></i> Admin Panel</a>
      @endif
    @endauth
  </nav>

  <div class="mobile-menu-btns">
    @auth
      <div class="mobile-menu-user">
        <div class="avatar avatar-md" style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;font-size:.8rem;">{{ Auth::user()->initials }}</div>
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
      <a href="{{ route('login') }}"    class="btn btn-outline btn-md mobile-menu-cta"><i class="fas fa-sign-in-alt"></i> Sign In</a>
      <a href="{{ route('register') }}" class="btn btn-primary btn-md mobile-menu-cta"><i class="fas fa-seedling"></i> Get Started Free</a>
    @endauth
  </div>
</div>

<style>
.mobile-menu-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 12px 0; margin-bottom: 16px;
}
.mobile-menu-logo {
  display: flex; align-items: center; gap: 10px; text-decoration: none;
}
.mobile-menu-logo strong {
  font-family: var(--font-display); font-size: 1.05rem; color: var(--text); font-weight: 800;
}
.mobile-menu-close {
  width: 36px; height: 36px; border-radius: 50%; background: var(--bg-2);
  display: flex; align-items: center; justify-content: center;
  color: var(--text-muted); font-size: 1rem; transition: all var(--t-fast);
  border: none; cursor: pointer;
}
.mobile-menu-close:hover { background: var(--gray-200); color: var(--text); }
.mobile-menu-user {
  display: flex; align-items: center; gap: 12px;
  padding: 14px 16px; background: var(--green-50); border-radius: var(--radius-md); margin-bottom: 4px;
}
.mobile-menu-user-name { font-weight: 700; color: var(--text); font-size: .9rem; }
.mobile-menu-user-role { font-size: .75rem; color: var(--text-muted); }
.mobile-menu-signout, .mobile-menu-cta { justify-content: center; min-height: 48px; width: 100%; }
.mobile-menu-cta { justify-content: center; }
</style>

<script>
function closeMobileMenu() {
  const btn = document.getElementById('hamburger');
  const menu = document.getElementById('mobileMenu');
  if (btn && menu) {
    btn.classList.remove('open');
    btn.setAttribute('aria-expanded', 'false');
    menu.classList.remove('open');
    document.body.style.overflow = '';
  }
}
</script>
