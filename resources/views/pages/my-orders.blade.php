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
.status-lbl{font-size:.75rem;font-weight:600;color:var(--text-muted);text-align:center;line-height:1.3;}
.status-lbl.done,.status-lbl.active{color:var(--primary);}
.badge-purple{background:#f5f3ff;color:#6d28d9;border:1px solid #d8b4fe;}
.badge-earth{background:#fffbeb;color:#b45309;border:1px solid #fcd34d;}
.blue-50{background:#f0f9ff;}
@media(max-width:600px){.order-card{padding:16px 12px;}.status-dot{width:26px;height:26px;font-size:.65rem;}.status-step{min-width:50px;}.status-lbl{font-size:.75rem;}}
</style>
@endsection

@section('content')
<div class="section" style="background:var(--bg-2);">
  <div class="container">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
      <div>
        <h1 class="heading-md" style="color:var(--text);">📦 My Orders</h1>
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
        $steps=['pending','confirmed','packing','dispatched','on_the_way','delivered'];
        $currentIdx=array_search($order->status,$steps);
        if($currentIdx===false) $currentIdx=0;
        $sl=\App\Models\Order::STATUS_LABELS;
        $sc=['pending'=>'badge-gray','confirmed'=>'badge-sky','packing'=>'badge-earth','dispatched'=>'badge-sky','on_the_way'=>'badge-purple','delivered'=>'badge-green','cancelled'=>'badge-coral','refunded'=>'badge-gray'];
        $icons=['pending'=>'fas fa-clock','confirmed'=>'fas fa-check','packing'=>'fas fa-box','dispatched'=>'fas fa-truck','on_the_way'=>'fas fa-map-marker-alt','delivered'=>'fas fa-home'];
        $sticker=$order->trackingStickers()->first();
      @endphp
      <div class="order-card {{ $order->status==='cancelled'?'opacity-60':'' }}" data-order-number="{{ $order->order_number }}">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
          <div>
            <div class="code" style="font-weight:700;color:var(--primary);">{{ $order->order_number }}</div>
            <div class="body-sm" style="color:var(--text-muted);">Placed {{ $order->created_at->format('M j, Y g:i A') }}</div>
          </div>
          <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            <span class="badge {{ $sc[$order->status]??'badge-gray' }}">{{ $sl[$order->status]??ucfirst($order->status) }}</span>
            <span class="badge {{ $order->payment_status==='paid'?'badge-green':'badge-gray' }}" style="font-size:.75rem;">
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
                <div class="status-lbl {{ $done?'done':($active?'active':'') }}">{{ $sl[$step]??ucfirst($step) }}</div>
              </div>
            @endforeach
          </div>
        @else
          <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:var(--radius-md);padding:10px 14px;margin-bottom:12px;color:#dc2626;" class="body-sm">
            <i class="fas fa-times-circle"></i> This order was {{ $order->status }}
            @if($order->cancellation_reason) — {{ $order->cancellation_reason }} @endif
          </div>
        @endif

        {{-- Items --}}
        <div style="display:flex;gap:10px;flex-wrap:wrap;margin:14px 0;">
          @foreach($order->items->take(3) as $item)
            <div style="background:var(--bg-2);border-radius:var(--radius-md);padding:7px 12px;" class="body-sm">
              <span style="font-weight:600;">{{ $item->quantity }}×</span> {{ $item->product_name }}
            </div>
          @endforeach
          @if($order->items->count()>3)
            <div style="background:var(--bg-2);border-radius:var(--radius-md);padding:7px 12px;color:var(--text-muted);" class="body-sm">+{{ $order->items->count()-3 }} more</div>
          @endif
        </div>

        {{-- Delivery info --}}
        @php $del=$order->delivery; @endphp
        @if($del && $del->driver_name)
          <div style="background:var(--green-50);border:1px solid var(--green-200);border-radius:var(--radius-md);padding:12px 14px;margin-bottom:12px;" class="body-sm">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
              <span style="color:var(--green-700);"><i class="fas fa-truck"></i> Tracking: <strong class="code">{{ $del->tracking_number }}</strong></span>
              <a href="{{ route('delivery.track',$del->tracking_number) }}" class="btn btn-primary btn-sm" style="font-size:.75rem;"><i class="fas fa-map-marker-alt"></i> Track Live</a>
            </div>
            @if($sticker)
              <div style="margin-top:6px;color:var(--text-muted);" class="body-xs">
                <i class="fas fa-tag"></i> Sticker: <span class="code" style="background:#f5f3ff;color:#6d28d9;padding:1px 6px;border-radius:4px;">{{ $sticker->sticker_code }}</span>
              </div>
            @endif
            <div style="margin-top:6px;padding-top:6px;border-top:1px solid rgba(0,0,0,.06);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:6px;">
              <span class="body-sm" style="color:var(--text);">
                <i class="fas fa-user"></i> Driver: <strong>{{ $del->driver_name }}</strong>
                @if($del->driver_phone)
                  <span class="code" style="color:var(--text-muted);margin-left:8px;">{{ $del->driver_phone }}</span>
                @endif
              </span>
              @if($del->driver_phone)
                <a href="tel:{{ $del->driver_phone }}" class="btn btn-sm" style="background:#e0f2fe;color:#0369a1;font-size:.75rem;padding:3px 10px;"><i class="fas fa-phone"></i> Call</a>
              @endif
            </div>
            @if($del->driver_vehicle_plate)
              <div style="margin-top:4px;color:var(--text-muted);font-size:.72rem;">
                <i class="fas fa-car"></i> Vehicle: {{ $del->driver_vehicle_type ? $del->driver_vehicle_type.' · ' : '' }}{{ $del->driver_vehicle_plate }}
              </div>
            @endif
          </div>
        @elseif($del && $del->tracking_number)
          <div style="background:var(--blue-50);border:1px solid #bae6fd;border-radius:var(--radius-md);padding:10px 14px;margin-bottom:12px;" class="body-sm">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
              <span style="color:#0369a1;"><i class="fas fa-truck"></i> Tracking: <strong class="code">{{ $del->tracking_number }}</strong></span>
              <a href="{{ route('delivery.track',$del->tracking_number) }}" class="btn btn-sm" style="background:#e0f2fe;color:#0369a1;font-size:.75rem;"><i class="fas fa-map-marker-alt"></i> Track</a>
            </div>
            @if($sticker)
              <div style="margin-top:4px;color:var(--text-muted);">
                <i class="fas fa-tag"></i> Sticker: <span class="code" style="background:#f5f3ff;color:#6d28d9;padding:1px 6px;border-radius:4px;">{{ $sticker->sticker_code }}</span>
              </div>
            @endif
            <div style="margin-top:4px;color:var(--text-muted);" class="body-xs">
              <i class="fas fa-clock"></i> 
              @if($order->status === 'on_the_way')
                Driver assigned — heading to pick up your order
              @else
                Assigning driver...
              @endif
            </div>
          </div>
        @elseif($order->status === 'on_the_way' || $order->status === 'dispatched')
          <div style="background:var(--blue-50);border:1px solid #bae6fd;border-radius:var(--radius-md);padding:10px 14px;margin-bottom:12px;color:#0369a1;" class="body-sm">
            <i class="fas fa-spinner fa-spin"></i> Preparing your delivery — tracking details will appear shortly
          </div>
        @else
          <div style="background:var(--bg-2);border-radius:var(--radius-md);padding:10px 14px;margin-bottom:12px;color:var(--text-muted);" class="body-sm">
            <i class="fas fa-clock"></i> Delivery will be assigned once order is confirmed by seller
          </div>
        @endif

        {{-- Footer --}}
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;padding-top:12px;border-top:1px solid var(--border);">
          <div class="heading-xs" style="color:var(--primary);">{{ $order->currency }} {{ number_format($order->total) }}</div>
          <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button onclick="showToast('📄 Invoice downloading...','success')" class="btn btn-outline btn-sm"><i class="fas fa-file-invoice"></i> Invoice</button>
            @if($order->status==='delivered')
              <a href="{{ route('marketplace') }}" class="btn btn-primary btn-sm"><i class="fas fa-redo"></i> Reorder</a>
            @endif
            <button onclick="reportIssue({{ $order->id }},'{{ $order->order_number }}')" class="btn btn-sm" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;font-size:.75rem;"><i class="fas fa-flag"></i> Need Help?</button>
          </div>
        </div>
      </div>
    @empty
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:60px;text-align:center;">
        <i class="fas fa-shopping-bag" style="font-size:3rem;color:var(--text-muted);margin-bottom:16px;display:block;opacity:.25;"></i>
        <h3 class="heading-md" style="margin-bottom:8px;">No orders yet</h3>
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
