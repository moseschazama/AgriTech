<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}"/>
  @auth
    <meta name="user-id" content="{{ Auth::id() }}"/>
    <meta name="is-admin" content="{{ Auth::user()->isAdmin() ? 'true' : 'false' }}"/>
    <meta name="reverb-app-id" content="{{ env('REVERB_APP_ID', 'agritech') }}"/>
    <meta name="reverb-key" content="{{ env('REVERB_APP_KEY', 'agritech_key') }}"/>
    <meta name="reverb-host" content="{{ env('REVERB_HOST', 'localhost') }}"/>
    <meta name="reverb-port" content="{{ env('REVERB_PORT', '8080') }}"/>
    <meta name="reverb-scheme" content="{{ env('REVERB_SCHEME', 'http') }}"/>
  @endauth
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}"/>
  <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}"/>
  <meta name="theme-color" content="#16a34a"/>
  <title>@yield('title', 'AgriTech Pro — Smart Agriculture Platform')</title>
  <link rel="stylesheet" href="{{ asset('css/app.min.css') }}"/>
  <link rel="stylesheet" href="{{ asset('css/global.css') }}"/>
  <link rel="stylesheet" href="{{ asset('css/premium.css') }}"/>
  @yield('extra_css')
  <link rel="preconnect" href="https://cdnjs.cloudflare.com"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'"/>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" media="print" onload="this.media='all'"/>
  <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet"/></noscript>
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
    /* ── Toast System ── */
    #toast-container{position:fixed;bottom:24px;right:24px;z-index:99999;display:flex;flex-direction:column-reverse;gap:10px;max-width:380px;width:100%;pointer-events:none;}
    #toast-container .toast{pointer-events:auto;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:14px 16px;display:flex;align-items:center;gap:12px;box-shadow:0 8px 32px rgba(0,0,0,.15);transform:translateX(120%);opacity:0;transition:all .35s cubic-bezier(.4,0,.2,1);position:relative;}
    #toast-container .toast.toast-visible{transform:translateX(0);opacity:1;}
    #toast-container .toast-dismissing{transform:translateX(40px);opacity:0;}
    #toast-container .toast-icon{font-size:1.1rem;flex-shrink:0;}
    #toast-container .toast-msg{flex:1;font-size:.88rem;font-weight:600;color:var(--text);line-height:1.3;margin:0;}
    #toast-container .toast-dismiss{background:none;border:none;font-size:1.1rem;cursor:pointer;color:var(--text-muted);padding:0 2px;flex-shrink:0;line-height:1;opacity:.6;}
    #toast-container .toast-dismiss:hover{opacity:1;}
    #toast-container .toast.toast-success{border-left:4px solid var(--success);}
    #toast-container .toast.toast-success .toast-icon{color:var(--success);}
    #toast-container .toast.toast-error{border-left:4px solid var(--danger);}
    #toast-container .toast.toast-error .toast-icon{color:var(--danger);}
    #toast-container .toast.toast-warning{border-left:4px solid var(--warning);}
    #toast-container .toast.toast-warning .toast-icon{color:var(--warning);}
    #toast-container .toast.toast-info{border-left:4px solid var(--info);}
    #toast-container .toast.toast-info .toast-icon{color:var(--info);}
    @media(max-width:480px){#toast-container{right:12px;left:12px;max-width:100%;bottom:16px;}#toast-container .toast{padding:12px 14px;}}
    /* ── Back to Top ── */
    #backToTop{position:fixed;bottom:24px;right:24px;width:46px;height:46px;border-radius:50%;border:none;background:var(--green-600);color:#fff;font-size:1rem;cursor:pointer;box-shadow:0 4px 16px rgba(22,163,74,.3);display:flex;align-items:center;justify-content:center;opacity:0;visibility:hidden;transform:translateY(8px);transition:opacity .2s ease,transform .2s ease,visibility .2s;z-index:900;}
    #backToTop.show{opacity:1;visibility:visible;transform:none;}
    #backToTop:hover{background:var(--green-700);box-shadow:0 6px 20px rgba(22,163,74,.4);}
    [data-theme="dark"] #backToTop{background:var(--green-500);}
    [data-theme="dark"] #backToTop:hover{background:var(--green-600);}
    #backToTop:focus-visible{outline:3px solid var(--primary);outline-offset:3px;}
    @media(max-width:639px){#backToTop{bottom:20px;right:16px;width:42px;height:42px;}}
    /* ── Button Loading States ── */
    .btn-spinner{animation:fa-spin 1s linear infinite;margin-right:6px;}
    button:disabled,.btn:disabled{opacity:.6;cursor:not-allowed;pointer-events:auto;}
    /* ── Upload Progress ── */
    .upload-progress{display:none;margin:12px 0;padding:12px;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);gap:10px;align-items:center;}
    .upload-progress.upload-progress-active{display:flex;flex-wrap:wrap;}
    .upload-progress-bar{flex:1;min-width:120px;height:6px;background:var(--gray-200);border-radius:20px;overflow:hidden;}
    .upload-progress-fill{height:100%;width:0%;background:var(--primary);border-radius:20px;transition:width .2s ease;}
    .upload-progress-info{display:flex;align-items:center;gap:8px;}
    .upload-progress-text{font-size:.78rem;color:var(--text-muted);font-weight:600;}
    .upload-progress-pct{font-size:.72rem;color:var(--primary);font-weight:700;min-width:32px;text-align:right;}
    .upload-cancel-btn{background:none;border:1px solid var(--border);border-radius:50%;width:24px;height:24px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--text-muted);font-size:.85rem;padding:0;line-height:1;margin-left:auto;transition:all .15s;}
    .upload-cancel-btn:hover{background:#fef2f2;border-color:#fca5a5;color:#dc2626;}
    /* ── Skeleton Loader ── */
    @keyframes skeleton-shimmer{0%{background-position:-200px 0}100%{background-position:calc(200px + 100%) 0}}
    .skeleton-loader{display:flex;flex-direction:column;gap:16px;padding:12px 0;}
    .skeleton-row{display:flex;flex-direction:column;gap:8px;}
    .skeleton-line{height:14px;border-radius:8px;background:linear-gradient(90deg,var(--gray-200) 25%,var(--gray-100) 50%,var(--gray-200) 75%);background-size:200px 100%;animation:skeleton-shimmer 1.5s ease-in-out infinite;}
    .skeleton-line.w-25{width:25%;}.skeleton-line.w-50{width:50%;}.skeleton-line.w-60{width:60%;}.skeleton-line.w-75{width:75%;}.skeleton-line.w-80{width:80%;}.skeleton-line.w-100{width:100%;}
    [data-theme="dark"] .skeleton-line{background:linear-gradient(90deg,var(--gray-700) 25%,var(--gray-600) 50%,var(--gray-700) 75%);}
    /* ── Form Input Error ── */
    .form-input-error{border-color:#ef4444!important;background:#fef2f2!important;}
    .ajax-error{display:block;color:#ef4444;font-size:.73rem;font-weight:600;margin-top:4px;}
    /* ── Inline Loader ── */
    .inline-loader{display:inline-flex;align-items:center;gap:6px;font-size:.82rem;color:var(--text-muted);}
    .inline-loader i{font-size:.85rem;}
  </style>
</head>
<body id="top">

{{-- Flash Messages — converted to toasts by JS --}}
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

{{-- Back to top floating button — shown after scrolling --}}
<button id="backToTop" type="button" title="Back to top" aria-label="Back to top">
  <i class="fas fa-arrow-up"></i>
</button>

<div id="toast-container"></div>

@auth
<script>
  window.Laravel = window.Laravel || {};
  window.Laravel.userId = {{ Auth::id() }};
  window.Laravel.isAdmin = {{ Auth::user()->isAdmin() ? 'true' : 'false' }};
  window.Laravel.reverbKey = '{{ env("REVERB_APP_KEY", "agritech_key") }}';
  window.Laravel.reverbHost = '{{ env("REVERB_HOST", "localhost") }}';
  window.Laravel.reverbPort = '{{ env("REVERB_PORT", "8080") }}';
  window.Laravel.reverbScheme = '{{ env("REVERB_SCHEME", "http") }}';
</script>
@endauth

<script src="{{ asset('js/global.min.js') }}" defer></script>
@vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/realtime.js'])
@yield('extra_js')
<script>
  // Convert flash messages to toast notifications
  (function(){
    var flash = document.getElementById('flashMsg');
    if (flash) {
      var type = 'success';
      if (flash.classList.contains('flash-error')) type = 'error';
      else if (flash.classList.contains('flash-warning')) type = 'warning';
      else if (flash.classList.contains('flash-info')) type = 'info';
      var msg = flash.textContent.trim().replace('✕','').trim();
      // Wait for toast system to be ready
      var check = function() {
        if (typeof showToast === 'function') {
          flash.remove();
          showToast(msg, type);
        } else {
          setTimeout(check, 100);
        }
      };
      setTimeout(check, 300);
    }
  })();
</script>

<script>
  // Back to top — show after scrolling, smooth-scroll to top on click
  (function(){
    var btn = document.getElementById('backToTop');
    if (!btn) return;
    var toggle = function(){
      var y = window.scrollY || document.documentElement.scrollTop || document.body.scrollTop;
      btn.classList.toggle('show', y > 400);
    };
    window.addEventListener('scroll', toggle, { passive: true });
    toggle();
    btn.addEventListener('click', function(){
      try { window.scrollTo({ top: 0, behavior: 'smooth' }); }
      catch (e) { window.scrollTo(0, 0); }
    });
  })();
</script>
</body>
</html>
