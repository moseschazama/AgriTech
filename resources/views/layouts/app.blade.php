<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}"/>
  <title>@yield('title', 'AgriTech Pro — Smart Agriculture Platform')</title>
  <link rel="stylesheet" href="{{ asset('css/global.css') }}"/>
  <link rel="stylesheet" href="{{ asset('css/navbar.css') }}"/>
  <link rel="stylesheet" href="{{ asset('css/design-system.css') }}"/>
  @yield('extra_css')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet"/>
  <style>
    /* ── Flash Messages ── */
    .flash-success,.flash-error,.flash-info,.flash-warning{
      position:fixed;top:80px;right:20px;z-index:9999;
      padding:14px 20px;border-radius:12px;font-weight:600;font-size:.9rem;
      display:flex;align-items:center;gap:10px;max-width:380px;
      box-shadow:0 8px 32px rgba(0,0,0,0.18);animation:slideInRight .3s ease;
    }
    .flash-success{background:#f0fdf4;color:#15803d;border:1.5px solid #86efac;}
    .flash-error  {background:#fef2f2;color:#dc2626;border:1.5px solid #fca5a5;}
    .flash-info   {background:#eff6ff;color:#1d4ed8;border:1.5px solid #93c5fd;}
    .flash-warning{background:#fffbeb;color:#b45309;border:1.5px solid #fcd34d;}
    @keyframes slideInRight{from{opacity:0;transform:translateX(40px)}to{opacity:1;transform:translateX(0)}}
    /* ── Pagination ── */
    .pagination{display:flex;gap:6px;justify-content:center;margin:32px 0;flex-wrap:wrap;}
    .pagination .page-item .page-link{
      padding:8px 14px;border-radius:8px;border:1.5px solid var(--border);
      color:var(--text-muted);font-size:.85rem;font-weight:600;transition:all .15s;
      text-decoration:none;display:block;
    }
    .pagination .page-item.active .page-link{background:var(--primary);border-color:var(--primary);color:#fff;}
    .pagination .page-item.disabled .page-link{opacity:.4;pointer-events:none;}
    .pagination .page-item .page-link:hover:not(.disabled){border-color:var(--primary);color:var(--primary);}
  </style>
</head>
<body>

{{-- Page Loader --}}
<div id="page-loader">
  <div class="loader-logo">🌱 AgriTech Pro</div>
  <div class="loader-bar"><div class="loader-fill"></div></div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
  <div class="flash-success" id="flashMsg">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
    <button onclick="this.parentElement.remove()" style="background:none;border:none;margin-left:auto;cursor:pointer;color:inherit;">✕</button>
  </div>
@endif
@if(session('error'))
  <div class="flash-error" id="flashMsg">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    <button onclick="this.parentElement.remove()" style="background:none;border:none;margin-left:auto;cursor:pointer;color:inherit;">✕</button>
  </div>
@endif
@if(session('warning'))
  <div class="flash-warning" id="flashMsg">
    <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
    <button onclick="this.parentElement.remove()" style="background:none;border:none;margin-left:auto;cursor:pointer;color:inherit;">✕</button>
  </div>
@endif
@if(session('info'))
  <div class="flash-info" id="flashMsg">
    <i class="fas fa-info-circle"></i> {{ session('info') }}
    <button onclick="this.parentElement.remove()" style="background:none;border:none;margin-left:auto;cursor:pointer;color:inherit;">✕</button>
  </div>
@endif

@include('partials.navbar')
@include('partials.mobile-menu')

@yield('content')

@include('partials.chatbot')

<div id="toast-container"></div>

<script src="{{ asset('js/global.js') }}"></script>
@yield('extra_js')
<script>
  // Auto-dismiss flash messages after 5 seconds
  setTimeout(() => {
    const flash = document.getElementById('flashMsg');
    if (flash) flash.style.animation = 'slideInRight .3s ease reverse';
    setTimeout(() => flash?.remove(), 300);
  }, 5000);
</script>
</body>
</html>
