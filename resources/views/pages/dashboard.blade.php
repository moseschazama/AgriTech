@extends('layouts.app')
@section('title', 'Farmer Dashboard — AgriTech Pro')
@section('extra_css')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"/>
<link rel="stylesheet" href="{{ asset('css/home.css') }}"/>
<style>
.dash-layout{display:flex;min-height:calc(100vh - 70px);}
.sidebar{width:260px;background:var(--bg-card);border-right:1px solid var(--border);position:fixed;top:var(--nav-h);left:0;height:calc(100vh - var(--nav-h));overflow-y:auto;z-index:100;transition:transform .3s ease;display:flex;flex-direction:column;}
.sidebar-header{padding:20px 20px 16px;border-bottom:1px solid var(--border);}
.sidebar-user{display:flex;align-items:center;gap:12px;margin-bottom:10px;}
.sidebar-user-info strong{display:block;font-size:.9rem;color:var(--text);}
.sidebar-user-info span{font-size:.75rem;color:var(--text-muted);}
.sidebar-farm-badge{display:flex;align-items:center;gap:6px;font-size:.75rem;color:var(--primary);font-weight:600;background:var(--green-50);border-radius:var(--radius-full);padding:4px 12px;}
.sidebar-nav{padding:12px 0;flex:1;}
.sidebar-section-label{font-size:.68rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--text-muted);padding:10px 20px 4px;}
.sidebar-link{display:flex;align-items:center;gap:10px;padding:9px 20px;font-size:.85rem;color:var(--text-muted);font-weight:500;text-decoration:none;transition:all .15s;border-left:3px solid transparent;}
.sidebar-link:hover{background:var(--bg-2);color:var(--primary);border-left-color:var(--primary);}
.sidebar-link.active{background:var(--green-50);color:var(--primary);border-left-color:var(--primary);font-weight:700;}
.sidebar-link i{width:16px;text-align:center;font-size:.85rem;}
.sidebar-link .badge{margin-left:auto;font-size:.62rem;padding:2px 7px;}
.sidebar-footer{padding:16px 20px;border-top:1px solid var(--border);}
.dash-main{margin-left:260px;flex:1;padding:32px;background:var(--bg-2);min-height:calc(100vh - 70px);}
.dash-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:16px;}
.page-title{font-size:1.5rem;font-weight:800;color:var(--text);margin-bottom:4px;}
.page-subtitle{font-size:.85rem;color:var(--text-muted);}
.stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-bottom:28px;}
.stat-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:22px;transition:all .2s;}
.stat-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-md);}
.stat-card-icon{width:48px;height:48px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;margin-bottom:14px;}
.stat-card-val{font-size:1.5rem;font-weight:800;color:var(--text);line-height:1;}
.stat-card-label{font-size:.78rem;color:var(--text-muted);margin-top:4px;}
.stat-card-change{font-size:.74rem;font-weight:700;margin-top:8px;display:flex;align-items:center;gap:4px;}
.change-up{color:#22c55e;} .change-down{color:#ef4444;}
.dash-grid{display:grid;grid-template-columns:1fr 1fr;gap:22px;}
.widget{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;}
.widget-header{display:flex;align-items:center;justify-content:space-between;padding:18px 20px;border-bottom:1px solid var(--border);}
.widget-title{font-size:.9375rem;font-weight:700;color:var(--text);}
.widget-body{padding:18px 20px;}
.activity-item{display:flex;gap:12px;padding:10px 0;border-bottom:1px solid var(--border);}
.activity-item:last-child{border-bottom:none;}
.activity-icon{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.85rem;}
.activity-text{font-size:.83rem;color:var(--text);flex:1;line-height:1.4;}
.activity-time{font-size:.72rem;color:var(--text-muted);white-space:nowrap;}
.notif-item{display:flex;gap:12px;padding:10px 0;border-bottom:1px solid var(--border);cursor:pointer;transition:background .15s;}
.notif-item:last-child{border-bottom:none;}
.notif-item:hover{background:var(--bg-2);margin:0 -20px;padding:10px 20px;}
.notif-unread .notif-title{font-weight:700;}
.notif-title{font-size:.83rem;color:var(--text);margin-bottom:2px;}
.notif-msg{font-size:.76rem;color:var(--text-muted);line-height:1.4;}
.notif-time{font-size:.7rem;color:var(--text-muted);margin-top:3px;}
.notif-dot{width:8px;height:8px;border-radius:50%;background:var(--primary);flex-shrink:0;margin-top:5px;}
.order-row{display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border);}
.order-row:last-child{border-bottom:none;}
.course-progress-item{padding:12px 0;border-bottom:1px solid var(--border);}
.course-progress-item:last-child{border-bottom:none;}
.progress-bar-wrap{background:var(--bg-2);border-radius:var(--radius-full);height:6px;margin:8px 0 4px;overflow:hidden;}
.progress-fill-bar{height:6px;background:var(--primary);border-radius:var(--radius-full);transition:width .5s ease;}
.weather-widget-card{background:#0c4a2e;border-radius:var(--radius-lg);padding:20px;color:#fff;margin-bottom:18px;}
.mobile-sidebar-btn{display:none;position:fixed;bottom:20px;right:20px;width:48px;height:48px;background:var(--primary);color:#fff;border-radius:50%;border:none;font-size:1.2rem;cursor:pointer;z-index:200;box-shadow:var(--shadow-green);}
@media(max-width:768px){
  .sidebar{transform:translateX(-100%);z-index:300;width:280px;box-shadow:var(--shadow-xl);}.sidebar.open{transform:translateX(0);}
  .dash-main{margin-left:0;padding:20px 14px;}
  .stats-row{grid-template-columns:1fr 1fr;gap:12px;}
  .dash-grid{grid-template-columns:1fr;}
  .mobile-sidebar-btn{display:flex;align-items:center;justify-content:center;}
  .stat-card{padding:16px;}.stat-card-val{font-size:1.4rem;}
  .page-title{font-size:1.3rem;}
}
@media(max-width:480px){
  .stats-row{grid-template-columns:1fr 1fr;gap:8px;}.stat-card{padding:12px;}.stat-card-val{font-size:1.2rem;}.stat-card-label{font-size:.7rem;}.dash-main{padding:12px 10px;}
}
</style>
@endsection

@section('content')
<div class="dash-layout">

{{-- ── SIDEBAR ── --}}
<aside class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <div class="sidebar-user">
      <div class="avatar avatar-md" style="background:#16a34a;color:#fff;font-size:.8rem;">{{ Auth::user()->initials }}</div>
      <div class="sidebar-user-info">
        <strong>{{ Auth::user()->full_name }}</strong>
        <span>{{ Auth::user()->district ?? 'AgriTech Pro' }}</span>
      </div>
    </div>
    <div class="sidebar-farm-badge">
      <i class="fas fa-leaf"></i>
      {{ Auth::user()->farm?->name ?? Auth::user()->first_name."'s Farm" }}
      @if(Auth::user()->farm?->size_hectares) · {{ Auth::user()->farm->size_hectares }} ha @endif
    </div>
  </div>

  <nav class="sidebar-nav">
    <div class="sidebar-section-label">Main</div>
    <a href="{{ route('dashboard') }}" class="sidebar-link active"><i class="fas fa-th-large"></i> Dashboard</a>
    <a href="{{ route('profile') }}"   class="sidebar-link"><i class="fas fa-user"></i> My Profile</a>
    <a href="{{ route('farm-records') }}" class="sidebar-link"><i class="fas fa-tractor"></i> Farm Records</a>
    <a href="#weather"                 class="sidebar-link"><i class="fas fa-cloud-sun"></i> Weather</a>

    <div class="sidebar-section-label">Learning</div>
    <a href="{{ route('learn.my-courses') }}" class="sidebar-link">
      <i class="fas fa-graduation-cap"></i> My Courses
      @php $activeEnrollments = Auth::user()->enrollments()->where('status','active')->count(); @endphp
      @if($activeEnrollments > 0) <span class="badge badge-green">{{ $activeEnrollments }}</span> @endif
    </a>
    <a href="{{ route('learn') }}"     class="sidebar-link"><i class="fas fa-book"></i> Browse Courses</a>

    <div class="sidebar-section-label">Marketplace</div>
    <a href="{{ route('marketplace') }}"          class="sidebar-link"><i class="fas fa-store"></i> Browse Products</a>
    <a href="{{ route('marketplace.my-orders') }}" class="sidebar-link">
      <i class="fas fa-box"></i> My Orders
      @php $pendingOrders = Auth::user()->orders()->whereNotIn('status',['delivered','cancelled'])->count(); @endphp
      @if($pendingOrders > 0) <span class="badge badge-earth">{{ $pendingOrders }}</span> @endif
    </a>
    <a href="{{ route('marketplace.my-listings') }}" class="sidebar-link"><i class="fas fa-tag"></i> My Listings</a>

    <div class="sidebar-section-label">Community</div>
    <a href="{{ route('innovation') }}"     class="sidebar-link"><i class="fas fa-lightbulb"></i> Innovation Hub</a>
    <a href="{{ route('innovation.mine') }}" class="sidebar-link"><i class="fas fa-star"></i> My Innovations</a>
    <a href="{{ route('diseases') }}"       class="sidebar-link"><i class="fas fa-bug"></i> Disease Scanner</a>
    <a href="{{ route('delivery') }}"       class="sidebar-link"><i class="fas fa-truck"></i> Deliveries</a>
    <a href="{{ route('notifications') }}"  class="sidebar-link">
      <i class="fas fa-bell"></i> Notifications
      @php $unread = Auth::user()->unreadNotifications()->count(); @endphp
      @if($unread > 0) <span class="badge badge-green">{{ $unread }}</span> @endif
    </a>
  </nav>

  <div class="sidebar-footer">
    @if(Auth::user()->isAdmin())
      <a href="{{ route('admin.index') }}" class="sidebar-link" style="color:var(--primary);font-weight:700;padding:10px 0;margin-bottom:6px;">
        <i class="fas fa-shield-alt"></i> Admin Panel
      </a>
    @endif
    <a href="#" class="sidebar-link" style="color:var(--danger);"
       onclick="event.preventDefault();document.getElementById('sidebar-logout').submit();">
      <i class="fas fa-sign-out-alt"></i> Sign Out
    </a>
    <form id="sidebar-logout" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
  </div>
</aside>

{{-- Mobile sidebar toggle button --}}
<button class="mobile-sidebar-btn" onclick="document.getElementById('sidebar').classList.toggle('open')">
  <i class="fas fa-bars"></i>
</button>

{{-- ── MAIN CONTENT ── --}}
<main class="dash-main">

  {{-- Page Header --}}
  <div class="dash-header">
    <div>
      <div class="page-title">
        Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ Auth::user()->first_name }}! 🌱
      </div>
      <div class="page-subtitle">{{ now()->format('l, F j, Y') }} · Your farm is looking great today</div>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
      <a href="{{ route('farm-records') }}" class="btn btn-outline btn-sm"><i class="fas fa-tractor"></i> Farm Records</a>
      <a href="{{ route('diseases') }}" class="btn btn-outline btn-sm"><i class="fas fa-bug"></i> Scan Disease</a>
      <a href="{{ route('marketplace') }}" class="btn btn-primary btn-sm"><i class="fas fa-shopping-cart"></i> Browse Market</a>
    </div>
  </div>

  {{-- Weather quick card --}}
  <div class="weather-widget-card" id="weather">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
      <div style="display:flex;align-items:center;gap:16px;">
        <i class="fas fa-sun" style="font-size:2.5rem;color:#facc15;"></i>
        <div>
          <div style="font-size:2rem;font-weight:800;">28°C</div>
          <div style="font-size:.82rem;opacity:.8;">{{ Auth::user()->district ?? 'Lilongwe' }} — Sunny</div>
        </div>
      </div>
      <div style="display:flex;gap:24px;flex-wrap:wrap;">
        @foreach(['fas fa-tint'=>'Humidity: 62%','fas fa-wind'=>'Wind: 14 km/h NE','fas fa-cloud-rain'=>'Rain: Friday'] as $icon => $label)
          <div style="display:flex;align-items:center;gap:6px;font-size:.82rem;opacity:.85;">
            <i class="{{ $icon }}"></i> {{ $label }}
          </div>
        @endforeach
      </div>
      <div style="font-size:.78rem;opacity:.7;background:rgba(255,255,255,.1);border-radius:20px;padding:4px 12px;">
        <i class="fas fa-seedling"></i> Good planting conditions this week
      </div>
    </div>
  </div>

  {{-- Stats Cards --}}
  <div class="stats-row">
    @php
      $statCards = [
        ['icon'=>'fas fa-ruler-combined','bg'=>'var(--green-100)','color'=>'var(--green-700)',
         'val' => $stats['farm_area'] > 0 ? $stats['farm_area'].' ha' : 'Not set',
         'label'=>'Farm Area','change'=>null],
        ['icon'=>'fas fa-coins','bg'=>'#fff7ed','color'=>'#c2410c',
         'val' => 'K '.number_format($stats['monthly_sales']),
         'label'=>'Monthly Sales','change'=>'+12% vs last month'],
        ['icon'=>'fas fa-graduation-cap','bg'=>'#e0f2fe','color'=>'var(--sky-600)',
         'val' => $stats['courses_completed'].'/'.$stats['courses_total'],
         'label'=>'Courses Completed','change'=>$stats['courses_total'] > 0 ? round(($stats['courses_completed']/$stats['courses_total'])*100).'% complete' : null],
        ['icon'=>'fas fa-truck','bg'=>'#f0fdf4','color'=>'var(--green-700)',
         'val' => $stats['active_deliveries'],
         'label'=>'Active Deliveries','change'=>'Real-time tracking'],
      ];
    @endphp
    @foreach($statCards as $s)
      <div class="stat-card">
        <div class="stat-card-icon" style="background:{{ $s['bg'] }};color:{{ $s['color'] }};"><i class="{{ $s['icon'] }}"></i></div>
        <div class="stat-card-val">{{ $s['val'] }}</div>
        <div class="stat-card-label">{{ $s['label'] }}</div>
        @if($s['change'])
          <div class="stat-card-change change-up"><i class="fas fa-arrow-up"></i> {{ $s['change'] }}</div>
        @endif
      </div>
    @endforeach
  </div>

  {{-- Dashboard Grid --}}
  <div class="dash-grid">

    {{-- Production Chart --}}
    <div class="widget">
      <div class="widget-header">
        <div class="widget-title">📈 Sales Overview (Last 7 Days)</div>
        <span class="badge badge-green" style="font-size:.7rem;">Live</span>
      </div>
      <div class="widget-body">
        <canvas id="salesChart" height="200"></canvas>
        @if(isset($chartData) && $chartData->count() === 0)
          <div style="text-align:center;padding:40px;color:var(--text-muted);">
            <i class="fas fa-chart-bar" style="font-size:2rem;margin-bottom:10px;display:block;opacity:.3;"></i>
            No sales data yet. <a href="{{ route('marketplace') }}" style="color:var(--primary);">Browse marketplace →</a>
          </div>
        @endif
      </div>
    </div>

    {{-- Notifications --}}
    <div class="widget">
      <div class="widget-header">
        <div class="widget-title">🔔 Notifications</div>
        @if(Auth::user()->unreadNotifications()->count() > 0)
          <button onclick="markAllRead()" class="btn btn-outline btn-sm" style="font-size:.72rem;padding:4px 10px;">
            Mark all read
          </button>
        @endif
      </div>
      <div class="widget-body" style="padding:0 20px;">
        @forelse(isset($notifications) ? $notifications : Auth::user()->notifications()->limit(5)->get() as $notif)
          <div class="notif-item {{ !$notif->is_read ? 'notif-unread' : '' }}"
               onclick="markNotifRead({{ $notif->id }}, this)">
            <div class="activity-icon" style="background:var(--green-50);">
              <i class="{{ $notif->icon }}" style="color:{{ $notif->icon_color }};font-size:.85rem;"></i>
            </div>
            <div style="flex:1;">
              <div class="notif-title">{{ $notif->title }}</div>
              <div class="notif-msg">{{ Str::limit($notif->message, 70) }}</div>
              <div class="notif-time">{{ $notif->created_at->diffForHumans() }}</div>
            </div>
            @if(!$notif->is_read)
              <div class="notif-dot"></div>
            @endif
          </div>
        @empty
          <div style="text-align:center;padding:32px;color:var(--text-muted);">
            <i class="fas fa-bell-slash" style="font-size:2rem;margin-bottom:10px;display:block;opacity:.3;"></i>
            No notifications yet
          </div>
        @endforelse
        @if(Auth::user()->notifications()->count() > 5)
          <div style="padding:12px 0;text-align:center;border-top:1px solid var(--border);">
            <a href="{{ route('notifications') }}" style="font-size:.82rem;color:var(--primary);font-weight:600;">
              View all notifications →
            </a>
          </div>
        @endif
      </div>
    </div>

    {{-- Active Courses --}}
    <div class="widget">
      <div class="widget-header">
        <div class="widget-title">🎓 Continue Learning</div>
        <a href="{{ route('learn') }}" class="btn btn-outline btn-sm" style="font-size:.72rem;padding:4px 10px;">Browse more</a>
      </div>
      <div class="widget-body" style="padding:0 20px;">
        @forelse(isset($activeCourses) ? $activeCourses : Auth::user()->enrollments()->where('status','active')->with('course')->limit(3)->get() as $enrollment)
          @php $pct = $enrollment->progressPercentage(); @endphp
          <div class="course-progress-item">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
              <div style="font-size:.85rem;font-weight:600;color:var(--text);">{{ $enrollment->course->title }}</div>
              <span style="font-size:.75rem;color:var(--primary);font-weight:700;">{{ $pct }}%</span>
            </div>
            <div style="font-size:.74rem;color:var(--text-muted);margin-bottom:8px;">
              {{ ucwords(str_replace('_',' ',$enrollment->course->category)) }} ·
              {{ $enrollment->course->total_lessons }} lessons
            </div>
            <div class="progress-bar-wrap">
              <div class="progress-fill-bar" style="width:{{ $pct }}%;"></div>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:8px;">
              <span style="font-size:.72rem;color:var(--text-muted);">{{ round($enrollment->course->total_lessons * $pct / 100) }}/{{ $enrollment->course->total_lessons }} lessons done</span>
              <a href="{{ route('learn.show', $enrollment->course) }}" class="btn btn-primary btn-sm" style="font-size:.72rem;padding:4px 10px;">
                <i class="fas fa-play"></i> Continue
              </a>
            </div>
          </div>
        @empty
          <div style="text-align:center;padding:32px;color:var(--text-muted);">
            <i class="fas fa-graduation-cap" style="font-size:2rem;margin-bottom:10px;display:block;opacity:.3;"></i>
            No courses enrolled yet.
            <br><a href="{{ route('learn') }}" class="btn btn-primary btn-sm" style="margin-top:12px;">Browse Courses</a>
          </div>
        @endforelse
      </div>
    </div>

    {{-- Recent Orders --}}
    <div class="widget">
      <div class="widget-header">
        <div class="widget-title">📦 Recent Orders</div>
        <a href="{{ route('marketplace.my-orders') }}" class="btn btn-outline btn-sm" style="font-size:.72rem;padding:4px 10px;">View all</a>
      </div>
      <div class="widget-body" style="padding:0 20px;">
        @forelse(isset($recentOrders) ? $recentOrders : Auth::user()->orders()->with('items')->latest()->limit(4)->get() as $order)
          @php
            $statusColors = ['pending'=>'badge-gray','confirmed'=>'badge-sky','processing'=>'badge-earth','dispatched'=>'badge-sky','in_transit'=>'badge-earth','delivered'=>'badge-green','cancelled'=>'badge-coral','refunded'=>'badge-gray'];
            $statusColor  = $statusColors[$order->status] ?? 'badge-gray';
          @endphp
          <div class="order-row">
            <div style="width:36px;height:36px;border-radius:50%;background:var(--bg-2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <i class="fas fa-box" style="color:var(--text-muted);font-size:.8rem;"></i>
            </div>
            <div style="flex:1;min-width:0;">
              <div style="font-size:.83rem;font-weight:600;color:var(--text);">#{{ $order->order_number }}</div>
              <div style="font-size:.73rem;color:var(--text-muted);">{{ $order->items->count() }} item(s) · {{ $order->created_at->format('M j, Y') }}</div>
            </div>
            <div style="text-align:right;">
              <div style="font-size:.85rem;font-weight:700;color:var(--primary);">{{ $order->currency }} {{ number_format($order->total) }}</div>
              <span class="badge {{ $statusColor }}" style="font-size:.62rem;">{{ ucfirst($order->status) }}</span>
            </div>
          </div>
        @empty
          <div style="text-align:center;padding:32px;color:var(--text-muted);">
            <i class="fas fa-shopping-bag" style="font-size:2rem;margin-bottom:10px;display:block;opacity:.3;"></i>
            No orders yet.
            <br><a href="{{ route('marketplace') }}" class="btn btn-primary btn-sm" style="margin-top:12px;">Browse Marketplace</a>
          </div>
        @endforelse
      </div>
    </div>

  </div>{{-- end dash-grid --}}
</main>
</div>
@endsection

@section('extra_js')
<script>
/* ── Sales Chart ── */
@php
  $chartLabels = isset($chartData) ? $chartData->pluck('day')->map(fn($d) => \Carbon\Carbon::parse($d)->format('D'))->toJson() : '[]';
  $chartValues = isset($chartData) ? $chartData->pluck('total')->toJson() : '[]';
@endphp
const chartLabels = {!! $chartLabels !!};
const chartValues = {!! $chartValues !!};

if (chartLabels.length > 0 && document.getElementById('salesChart')) {
  const ctx = document.getElementById('salesChart').getContext('2d');
  const maxVal = Math.max(...chartValues, 1);
  const canvas = document.getElementById('salesChart');
  canvas.width  = canvas.parentElement.offsetWidth - 40;
  canvas.height = 200;
  const w = canvas.width, h = 200, pad = 30;
  const barW = (w - pad * 2) / chartLabels.length - 8;

  ctx.clearRect(0, 0, w, h);
  const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
  const textColor = isDark ? '#9ca3af' : '#64748b';
  const gridColor = isDark ? '#2d3748' : '#e2e8f0';

  // Grid lines
  for (let i = 0; i <= 4; i++) {
    const y = pad + (h - pad * 2) * i / 4;
    ctx.beginPath(); ctx.strokeStyle = gridColor; ctx.lineWidth = 1; ctx.setLineDash([4,4]);
    ctx.moveTo(pad, y); ctx.lineTo(w - pad, y); ctx.stroke();
    ctx.setLineDash([]);
    ctx.fillStyle = textColor; ctx.font = '11px Inter,sans-serif'; ctx.textAlign = 'right';
    ctx.fillText('K ' + Math.round(maxVal * (1 - i/4) / 1000) + 'k', pad - 4, y + 4);
  }

  // Bars
  chartLabels.forEach((label, i) => {
    const val  = chartValues[i] || 0;
    const bH   = ((h - pad * 2) * val) / maxVal;
    const x    = pad + i * ((w - pad * 2) / chartLabels.length) + 4;
    const y    = h - pad - bH;

    const grad = ctx.createLinearGradient(0, y, 0, h - pad);
    grad.addColorStop(0, '#16a34a'); grad.addColorStop(1, '#86efac');
    ctx.fillStyle = grad;
    ctx.beginPath();
    ctx.roundRect(x, y, barW, bH, [4, 4, 0, 0]);
    ctx.fill();

    ctx.fillStyle = textColor; ctx.font = 'bold 11px Inter,sans-serif'; ctx.textAlign = 'center';
    ctx.fillText(label, x + barW/2, h - 8);
  });
}

/* ── Notification read ── */
function markNotifRead(id, el) {
  fetch(`/notifications/${id}/read`, {
    method:'POST',
    headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content}
  });
  el.classList.remove('notif-unread');
  el.querySelector('.notif-dot')?.remove();
  const titleEl = el.querySelector('.notif-title');
  if (titleEl) titleEl.style.fontWeight = '500';
}

function markAllRead() {
  fetch('/notifications/mark-all-read', {
    method:'POST',
    headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content}
  }).then(() => {
    document.querySelectorAll('.notif-unread').forEach(el => {
      el.classList.remove('notif-unread');
      el.querySelector('.notif-dot')?.remove();
    });
    showToast('All notifications marked as read', 'success');
  });
}

/* ── Mobile sidebar overlay close ── */
document.addEventListener('click', e => {
  const sidebar = document.getElementById('sidebar');
  if (sidebar?.classList.contains('open') && !e.target.closest('#sidebar') && !e.target.closest('.mobile-sidebar-btn')) {
    sidebar.classList.remove('open');
  }
});
</script>
@endsection
