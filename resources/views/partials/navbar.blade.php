<header class="navbar" id="navbar">
  <div class="nav-inner">

    {{-- Logo --}}
    <a href="{{ route('home') }}" class="nav-logo">
      <div class="nav-logo-icon"><i class="fas fa-seedling"></i></div>
      <div class="nav-logo-text">
        <strong>AgriTech Pro</strong>
        <span>Smart Farming Platform</span>
      </div>
    </a>

    {{-- Desktop Nav Links --}}
    <nav class="nav-links">
      <a href="{{ route('home') }}"        class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"><i class="fas fa-home"></i> Home</a>
      <a href="{{ route('learn') }}"       class="nav-link {{ request()->routeIs('learn*') ? 'active' : '' }}"><i class="fas fa-graduation-cap"></i> Learn</a>
      <a href="{{ route('marketplace') }}" class="nav-link {{ request()->routeIs('marketplace*') ? 'active' : '' }}"><i class="fas fa-store"></i> Market</a>
      <a href="{{ route('innovation') }}"  class="nav-link {{ request()->routeIs('innovation*') ? 'active' : '' }}"><i class="fas fa-lightbulb"></i> Innovation</a>
      <a href="{{ route('delivery') }}"    class="nav-link {{ request()->routeIs('delivery*') ? 'active' : '' }}"><i class="fas fa-truck"></i> Delivery</a>
      <a href="{{ route('diseases') }}"    class="nav-link {{ request()->routeIs('diseases*') ? 'active' : '' }}"><i class="fas fa-bug"></i> Diseases</a>
      @auth
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard*') ? 'active' : '' }}"><i class="fas fa-th-large"></i> Dashboard</a>
      @endauth
    </nav>

    {{-- Right Section --}}
    <div class="nav-right">
      {{-- Dark mode toggle --}}
      <button class="dark-toggle" id="darkToggle" title="Toggle dark mode"><i class="fas fa-moon"></i></button>

      @auth
        @php
          $recentNotifs = Auth::user()->notifications()->limit(8)->get();
          $unreadCount = $recentNotifs->where('is_read', false)->count();
        @endphp

        {{-- Notification Bell --}}
        <div class="nav-notif-wrap" style="position:relative;">
          <button class="nav-notif" id="notifBell" onclick="toggleNotifDropdown()" title="Notifications">
            <i class="fas fa-bell"></i>
            <span class="badge-dot" id="notifBadge" style="{{ $unreadCount > 0 ? '' : 'display:none' }}">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
          </button>

          {{-- Notification Dropdown --}}
          <div class="notif-dropdown" id="notifDropdown" style="display:none;position:absolute;top:48px;right:0;width:340px;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);box-shadow:var(--shadow-lg);z-index:1000;overflow:hidden;">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--border);">
              <strong style="font-size:1.0625rem;letter-spacing:-0.01em;">Notifications</strong>
              @if($unreadCount > 0)
                <button onclick="markAllRead()" style="font-size:.75rem;color:var(--primary);font-weight:600;background:none;border:none;cursor:pointer;">Mark all read</button>
              @endif
            </div>
            <div id="notifList" style="max-height:320px;overflow-y:auto;">
              @forelse($recentNotifs as $notif)
                <a href="{{ $notif->action_url ?? '#' }}"
                   onclick="markNotifRead({{ $notif->id }}, this)"
                   style="display:flex;gap:12px;padding:12px 18px;text-decoration:none;border-bottom:1px solid var(--border);background:{{ !$notif->is_read ? 'var(--green-50)' : 'transparent' }};transition:background .15s;"
                   onmouseover="this.style.background='var(--bg-2)'" onmouseout="this.style.background='{{ !$notif->is_read ? 'var(--green-50)' : 'transparent' }}'">
                  <div style="width:36px;height:36px;border-radius:50%;background:var(--green-100);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="{{ $notif->icon }}" style="color:{{ $notif->icon_color }};font-size:.85rem;"></i>
                  </div>
                  <div style="flex:1;min-width:0;">
                    <div style="font-size:.8125rem;font-weight:{{ $notif->is_read ? '500' : '700' }};color:var(--text);margin-bottom:2px;">{{ $notif->title }}</div>
                    <div style="font-size:.75rem;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ Str::limit($notif->message, 60) }}</div>
                    <div style="font-size:.75rem;color:var(--text-muted);margin-top:3px;">{{ $notif->created_at->diffForHumans() }}</div>
                  </div>
                  @if(!$notif->is_read)
                    <div style="width:8px;height:8px;border-radius:50%;background:var(--primary);flex-shrink:0;margin-top:4px;"></div>
                  @endif
                </a>
              @empty
                <div style="padding:32px;text-align:center;color:var(--text-muted);">
                  <i class="fas fa-bell-slash" style="font-size:2rem;margin-bottom:8px;display:block;opacity:.4;"></i>
                  No notifications yet
                </div>
              @endforelse
            </div>
            <div style="padding:10px 18px;border-top:1px solid var(--border);text-align:center;">
              <a href="{{ route('notifications') }}" style="font-size:.8125rem;color:var(--primary);font-weight:600;">View all notifications</a>
            </div>
          </div>
        </div>

        {{-- User Avatar Dropdown --}}
        <div class="nav-user" id="navUser">
          <div class="avatar avatar-sm" style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;font-size:.75rem;cursor:pointer;">
            {{ Auth::user()->initials }}
          </div>
          <div>
            <div class="nav-user-name">{{ Auth::user()->full_name }}</div>
            <div class="nav-user-role">{{ ucfirst(Auth::user()->role) }}{{ Auth::user()->district ? ' · '.Auth::user()->district : '' }}</div>
          </div>
          <i class="fas fa-chevron-down" style="font-size:.65rem;color:var(--text-muted);"></i>
          <div class="nav-user-menu">
            <a href="{{ route('profile') }}"    class="nav-user-menu-item"><i class="fas fa-user"></i> My Profile</a>
            <a href="{{ route('dashboard') }}"  class="nav-user-menu-item"><i class="fas fa-th-large"></i> Dashboard</a>
            <a href="{{ route('learn.my-courses') }}" class="nav-user-menu-item"><i class="fas fa-graduation-cap"></i> My Courses</a>
            <a href="{{ route('marketplace.my-orders') }}" class="nav-user-menu-item"><i class="fas fa-box"></i> My Orders</a>
            <a href="{{ route('notifications') }}" class="nav-user-menu-item">
              <i class="fas fa-bell"></i> Notifications
              @if(Auth::user()->unreadNotifications()->count() > 0)
                <span class="badge badge-green" style="margin-left:auto;font-size:.75rem;padding:2px 7px;">{{ Auth::user()->unreadNotifications()->count() }}</span>
              @endif
            </a>
            @if(Auth::user()->isAdmin())
              <div class="nav-user-menu-divider"></div>
              <a href="{{ route('admin.index') }}" class="nav-user-menu-item"><i class="fas fa-shield-alt"></i> Admin Panel</a>
            @endif
            <div class="nav-user-menu-divider"></div>
            <a href="#" class="nav-user-menu-item danger"
               onclick="event.preventDefault();document.getElementById('nav-logout-form').submit();">
              <i class="fas fa-sign-out-alt"></i> Sign Out
            </a>
          </div>
        </div>

      @else
        {{-- Guest buttons --}}
        <div style="display:flex;gap:10px;align-items:center;">
          <a href="{{ route('login') }}"    class="btn btn-outline btn-sm">Sign In</a>
          <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Get Started</a>
        </div>
      @endauth

      <button class="nav-hamburger" id="hamburger" aria-expanded="false" aria-label="Open menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>

  {{-- Hidden logout form --}}
  @auth
    <form id="nav-logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
  @endauth
</header>

<style>
.notif-dropdown { animation: fadeInUp .2s ease; }
.badge-dot {
  position:absolute;top:-4px;right:-4px;
  background:#ef4444;color:#fff;font-size:.6rem;font-weight:800;
  padding:1px 4px;border-radius:10px;min-width:16px;text-align:center;
}
</style>

<script>
function toggleNotifDropdown() {
  const d = document.getElementById('notifDropdown');
  d.style.display = d.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', e => {
  if (!e.target.closest('#notifBell') && !e.target.closest('#notifDropdown')) {
    const d = document.getElementById('notifDropdown');
    if (d) d.style.display = 'none';
  }
});
</script>
