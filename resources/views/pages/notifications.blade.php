@extends('layouts.app')
@section('title', 'Notifications — AgriTech Pro')
@section('extra_css')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"/>
@endsection

@section('content')
<div class="section" style="background:var(--bg-2);">
  <div class="container" style="max-width:760px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
      <div>
        <h1 style="font-family:var(--font-display);font-size:1.5rem;font-weight:800;color:var(--text);">🔔 Notifications</h1>
        <p style="color:var(--text-muted);">{{ Auth::user()->unreadNotifications()->count() }} unread</p>
      </div>
      @if(Auth::user()->unreadNotifications()->count()>0)
        <form method="POST" action="{{ route('notifications.mark-all-read') }}">
          @csrf
          <button type="submit" class="btn btn-outline btn-sm">Mark all as read</button>
        </form>
      @endif
    </div>

    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);overflow:hidden;">
      @forelse(isset($notifications)?$notifications:Auth::user()->notifications()->paginate(20) as $notif)
        <a href="{{ $notif->action_url??route('dashboard') }}"
           style="display:flex;gap:14px;padding:18px 20px;text-decoration:none;border-bottom:1px solid var(--border);background:{{ !$notif->is_read?'var(--green-50)':'var(--bg-card)' }};transition:background .15s;"
           onmouseover="this.style.background='var(--bg-2)'" onmouseout="this.style.background='{{ !$notif->is_read?'var(--green-50)':'var(--bg-card)' }}'">
          <div style="width:44px;height:44px;border-radius:50%;background:var(--green-100);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="{{ $notif->icon }}" style="color:{{ $notif->icon_color }};font-size:1rem;"></i>
          </div>
          <div style="flex:1;">
            <div style="font-weight:{{ $notif->is_read?'500':'700' }};color:var(--text);font-size:.88rem;margin-bottom:3px;">{{ $notif->title }}</div>
            <div style="font-size:.82rem;color:var(--text-muted);line-height:1.5;margin-bottom:5px;">{{ $notif->message }}</div>
            <div style="font-size:.74rem;color:var(--text-muted);">{{ $notif->created_at->diffForHumans() }}</div>
          </div>
          @if(!$notif->is_read)
            <div style="width:10px;height:10px;border-radius:50%;background:var(--primary);flex-shrink:0;margin-top:6px;"></div>
          @endif
        </a>
      @empty
        <div style="padding:60px;text-align:center;color:var(--text-muted);">
          <i class="fas fa-bell-slash" style="font-size:3rem;margin-bottom:16px;display:block;opacity:.3;"></i>
          <h3 style="font-family:var(--font-display);margin-bottom:8px;">No notifications yet</h3>
          <p>When you get orders, course updates or alerts, they'll appear here.</p>
        </div>
      @endforelse
    </div>
    @if(isset($notifications)&&$notifications->hasPages())
      <div style="margin-top:24px;">{{ $notifications->links() }}</div>
    @endif
  </div>
</div>
@include('partials.footer')
@endsection
