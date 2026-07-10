@extends('layouts.app')
@section('title', 'My Orders — AgriTech Pro')
@section('extra_css')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"/>
<style>
.order-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:22px;margin-bottom:16px;transition:all .2s;}
.order-card:hover{box-shadow:var(--shadow-md);}
.status-bar{display:flex;align-items:center;justify-content:space-between;position:relative;margin:18px 0 6px;overflow-x:auto;padding-bottom:6px;}
.status-bar::before{content:'';position:absolute;top:16px;left:5%;right:5%;height:3px;background:var(--border);z-index:0;}
.status-step{display:flex;flex-direction:column;align-items:center;gap:6px;z-index:1;flex:1;min-width:60px;}
.status-dot{width:32px;height:32px;border-radius:50%;border:2px solid var(--border);background:var(--bg-card);display:flex;align-items:center;justify-content:center;font-size:.75rem;z-index:2;}
.status-dot.done{background:var(--primary);border-color:var(--primary);color:#fff;}
.status-dot.active{background:var(--primary);border-color:var(--primary);color:#fff;box-shadow:0 0 0 5px rgba(22,163,74,.2);}
.status-lbl{font-size:.62rem;font-weight:600;color:var(--text-muted);text-align:center;line-height:1.3;}
.status-lbl.done,.status-lbl.active{color:var(--primary);}
@media(max-width:600px){.order-card{padding:16px 12px;}.status-dot{width:26px;height:26px;font-size:.65rem;}.status-step{min-width:50px;}.status-lbl{font-size:.55rem;}}
</style>
@endsection

@section('content')
<div class="section" style="background:var(--bg-2);">
  <div class="container">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
      <div>
        <h1 style="font-family:var(--font-display);font-size:1.5rem;font-weight:800;color:var(--text);">📦 My Orders</h1>
        <p style="color:var(--text-muted);">Track all your purchases</p>
      </div>
      <a href="{{ route('marketplace') }}" class="btn btn-primary btn-sm"><i class="fas fa-store"></i> Browse More</a>
    </div>

    @if(session('success'))
      <div style="background:var(--green-50);border:1.5px solid var(--green-200);border-radius:var(--radius-md);padding:12px 18px;margin-bottom:20px;color:var(--green-700);font-weight:600;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
      </div>
    @endif

    @forelse(isset($orders) ? $orders : [] as $order)
      @php
        $steps=['pending','confirmed','processing','dispatched','in_transit','delivered'];
        $currentIdx=array_search($order->status,$steps);
        if($currentIdx===false) $currentIdx=0;
        $sc=['pending'=>'badge-gray','confirmed'=>'badge-sky','processing'=>'badge-earth','dispatched'=>'badge-sky','in_transit'=>'badge-earth','delivered'=>'badge-green','cancelled'=>'badge-coral','refunded'=>'badge-gray'];
        $icons=['pending'=>'fas fa-clock','confirmed'=>'fas fa-check','processing'=>'fas fa-box','dispatched'=>'fas fa-truck','in_transit'=>'fas fa-map-marker-alt','delivered'=>'fas fa-home'];
        $labels=['pending'=>'Ordered','confirmed'=>'Confirmed','processing'=>'Packing','dispatched'=>'Dispatched','in_transit'=>'On the Way','delivered'=>'Delivered'];
      @endphp
      <div class="order-card {{ $order->status==='cancelled'?'opacity-60':'' }}">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
          <div>
            <div style="font-family:var(--font-mono);font-size:.85rem;font-weight:700;color:var(--primary);">{{ $order->order_number }}</div>
            <div style="font-size:.78rem;color:var(--text-muted);">Placed {{ $order->created_at->format('M j, Y g:i A') }}</div>
          </div>
          <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            <span class="badge {{ $sc[$order->status]??'badge-gray' }}">{{ ucfirst($order->status) }}</span>
            <span class="badge {{ $order->payment_status==='paid'?'badge-green':'badge-gray' }}" style="font-size:.68rem;">
              <i class="fas fa-{{ $order->payment_status==='paid'?'check-circle':'clock' }}"></i>
              Payment {{ ucfirst($order->payment_status) }}
            </span>
          </div>
        </div>

        {{-- Progress bar (only for non-cancelled orders) --}}
        @if(!in_array($order->status,['cancelled','refunded']))
          <div class="status-bar">
            @foreach($steps as $i=>$step)
              @php $done=$i<$currentIdx; $active=$i===$currentIdx; @endphp
              <div class="status-step">
                <div class="status-dot {{ $done?'done':($active?'active':'') }}">
                  <i class="{{ $icons[$step] }}" style="font-size:.7rem;"></i>
                </div>
                <div class="status-lbl {{ $done?'done':($active?'active':'') }}">{{ $labels[$step] }}</div>
              </div>
            @endforeach
          </div>
        @else
          <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:var(--radius-md);padding:10px 14px;margin-bottom:12px;font-size:.82rem;color:#dc2626;">
            <i class="fas fa-times-circle"></i> This order was {{ $order->status }}
            @if($order->cancellation_reason) — {{ $order->cancellation_reason }} @endif
          </div>
        @endif

        {{-- Items --}}
        <div style="display:flex;gap:10px;flex-wrap:wrap;margin:14px 0;">
          @foreach($order->items->take(3) as $item)
            <div style="background:var(--bg-2);border-radius:var(--radius-md);padding:7px 12px;font-size:.8rem;">
              <span style="font-weight:600;">{{ $item->quantity }}×</span> {{ $item->product_name }}
            </div>
          @endforeach
          @if($order->items->count()>3)
            <div style="background:var(--bg-2);border-radius:var(--radius-md);padding:7px 12px;font-size:.8rem;color:var(--text-muted);">+{{ $order->items->count()-3 }} more</div>
          @endif
        </div>

        {{-- Delivery info --}}
        @if($order->delivery)
          <div style="background:var(--green-50);border:1px solid var(--green-200);border-radius:var(--radius-md);padding:10px 14px;margin-bottom:12px;font-size:.82rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
            <span style="color:var(--green-700);"><i class="fas fa-truck"></i> Tracking: <strong style="font-family:var(--font-mono);">{{ $order->delivery->tracking_number }}</strong></span>
            <a href="{{ route('delivery.track',$order->delivery->tracking_number) }}" class="btn btn-primary btn-sm" style="font-size:.74rem;"><i class="fas fa-map-marker-alt"></i> Track Live</a>
          </div>
        @else
          <div style="background:var(--bg-2);border-radius:var(--radius-md);padding:10px 14px;margin-bottom:12px;font-size:.8rem;color:var(--text-muted);">
            <i class="fas fa-clock"></i> Delivery will be assigned once order is confirmed by seller
          </div>
        @endif

        {{-- Footer --}}
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;padding-top:12px;border-top:1px solid var(--border);">
          <div style="font-family:var(--font-display);font-size:1.1rem;font-weight:800;color:var(--primary);">{{ $order->currency }} {{ number_format($order->total) }}</div>
          <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button onclick="showToast('📄 Invoice downloading...','success')" class="btn btn-outline btn-sm"><i class="fas fa-file-invoice"></i> Invoice</button>
            @if($order->status==='delivered')
              <a href="{{ route('marketplace') }}" class="btn btn-primary btn-sm"><i class="fas fa-redo"></i> Reorder</a>
            @endif
          </div>
        </div>
      </div>
    @empty
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:60px;text-align:center;">
        <i class="fas fa-shopping-bag" style="font-size:3rem;color:var(--text-muted);margin-bottom:16px;display:block;opacity:.25;"></i>
        <h3 style="font-family:var(--font-display);margin-bottom:8px;">No orders yet</h3>
        <p style="color:var(--text-muted);margin-bottom:20px;">Browse our marketplace and place your first order.</p>
        <a href="{{ route('marketplace') }}" class="btn btn-primary btn-lg"><i class="fas fa-store"></i> Browse Marketplace</a>
      </div>
    @endforelse

    @if(isset($orders)&&$orders->hasPages())
      <div style="margin-top:24px;">{{ $orders->links() }}</div>
    @endif
  </div>
</div>
@include('partials.footer')
@endsection
