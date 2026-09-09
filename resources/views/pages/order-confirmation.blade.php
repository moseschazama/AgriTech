@extends('layouts.app')
@section('title', 'Order Confirmed — AgriTech Pro')
@section('extra_css')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"/>
<style>
.confirm-hero{background:linear-gradient(135deg,#f8fafc,#f0fdf4,#f8fafc);padding:56px 0;color:var(--text);text-align:center;}
.confirm-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:32px;max-width:660px;margin:0 auto;}
.step-row{display:flex;align-items:flex-start;gap:14px;padding:14px 0;border-bottom:1px solid var(--border);}
.step-row:last-child{border-bottom:none;}
.step-icon{width:42px;height:42px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.95rem;}
.pay-box{border-radius:var(--radius-lg);padding:22px;margin-bottom:24px;}
.pay-step{display:flex;gap:12px;margin-bottom:12px;align-items:flex-start;}
.pay-num{width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:800;flex-shrink:0;}
@media(max-width:768px){.confirm-hero{padding:40px 0;}.confirm-hero h1{font-size:1.5rem !important;}.confirm-card{padding:24px 16px;}.pay-box{padding:18px 14px;}.pay-step div:last-child{font-size:.8125rem !important;}}
</style>
@endsection

@section('content')

{{-- Hero --}}
<section class="confirm-hero">
  <div class="container">
    <div style="font-size:4rem;margin-bottom:16px;">🎉</div>
    <h1 class="heading-lg" style="margin-bottom:8px;">Order Placed Successfully!</h1>
    <p style="color:var(--text-muted);">Order <strong class="code">{{ $order->order_number }}</strong> received</p>
    <div style="display:inline-flex;align-items:center;gap:8px;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-full);padding:6px 18px;margin-top:12px;" class="body-sm">
      <i class="fas fa-clock" style="color:var(--text-muted);"></i> {{ now()->format('M j, Y g:i A') }}
    </div>
  </div>
</section>

<div class="section" style="background:var(--bg-2);">
  <div class="container" style="max-width:700px;">

    {{-- ── PAYMENT INSTRUCTIONS ── --}}
    @php
      $paymentMethod = $order->payment_method;
      $total = $order->currency . ' ' . number_format($order->total);
    @endphp

    @if($paymentMethod === 'airtel_money')
      <div class="pay-box" style="background:linear-gradient(135deg,#fff7ed,#ffedd5);border:1.5px solid #fb923c;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
          <div style="width:48px;height:48px;background:#ea580c;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;">📱</div>
          <div><div class="heading-xs" style="color:#9a3412;">Pay via Airtel Money</div><div class="body-sm" style="color:#c2410c;">Complete payment now to confirm your order</div></div>
        </div>
        <div class="pay-step"><div class="pay-num" style="background:#ea580c;color:#fff;">1</div><div style="color:#7c2d12;" class="body-sm">Dial <strong>*299#</strong> on your Airtel line</div></div>
        <div class="pay-step"><div class="pay-num" style="background:#ea580c;color:#fff;">2</div><div style="color:#7c2d12;" class="body-sm">Select <strong>Send Money</strong></div></div>
        <div class="pay-step"><div class="pay-num" style="background:#ea580c;color:#fff;">3</div><div style="color:#7c2d12;" class="body-sm">Enter merchant number: <strong>1234567</strong></div></div>
        <div class="pay-step"><div class="pay-num" style="background:#ea580c;color:#fff;">4</div><div style="color:#7c2d12;" class="body-sm">Enter amount: <strong>{{ $total }}</strong></div></div>
        <div class="pay-step"><div class="pay-num" style="background:#ea580c;color:#fff;">5</div><div style="color:#7c2d12;" class="body-sm">Reference / Reason: <strong>{{ $order->order_number }}</strong></div></div>
        <div class="pay-step" style="margin-bottom:0;"><div class="pay-num" style="background:#ea580c;color:#fff;">6</div><div style="color:#7c2d12;" class="body-sm">Enter PIN and confirm</div></div>
        <div style="margin-top:14px;background:rgba(234,88,12,.1);border-radius:var(--radius-md);padding:10px 14px;color:#9a3412;" class="body-sm">
          <i class="fas fa-info-circle"></i> You'll receive an SMS confirmation from Airtel Money. Your order will be confirmed within 30 minutes.
        </div>
      </div>

    @elseif($paymentMethod === 'tnm_mpamba')
      <div class="pay-box" style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border:1.5px solid #3b82f6;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
          <div style="width:48px;height:48px;background:#1d4ed8;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;">📱</div>
          <div><div class="heading-xs" style="color:#1e3a8a;">Pay via TNM Mpamba</div><div class="body-sm" style="color:#1d4ed8;">Complete payment to confirm your order</div></div>
        </div>
        <div class="pay-step"><div class="pay-num" style="background:#1d4ed8;color:#fff;">1</div><div style="color:#1e3a8a;" class="body-sm">Dial <strong>*116#</strong> on your TNM line</div></div>
        <div class="pay-step"><div class="pay-num" style="background:#1d4ed8;color:#fff;">2</div><div style="color:#1e3a8a;" class="body-sm">Select <strong>Send Money</strong></div></div>
        <div class="pay-step"><div class="pay-num" style="background:#1d4ed8;color:#fff;">3</div><div style="color:#1e3a8a;" class="body-sm">Merchant number: <strong>7654321</strong></div></div>
        <div class="pay-step"><div class="pay-num" style="background:#1d4ed8;color:#fff;">4</div><div style="color:#1e3a8a;" class="body-sm">Amount: <strong>{{ $total }}</strong></div></div>
        <div class="pay-step" style="margin-bottom:0;"><div class="pay-num" style="background:#1d4ed8;color:#fff;">5</div><div style="color:#1e3a8a;" class="body-sm">Reference: <strong>{{ $order->order_number }}</strong>, then confirm</div></div>
      </div>

    @elseif($paymentMethod === 'mtn_momo')
      <div class="pay-box" style="background:linear-gradient(135deg,#fefce8,#fef9c3);border:1.5px solid #ca8a04;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
          <div style="width:48px;height:48px;background:#ca8a04;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;">📱</div>
          <div><div class="heading-xs" style="color:#713f12;">Pay via MTN MoMo</div><div class="body-sm" style="color:#92400e;">Complete payment to confirm your order</div></div>
        </div>
        <div class="pay-step"><div class="pay-num" style="background:#ca8a04;color:#fff;">1</div><div style="color:#713f12;" class="body-sm">Dial <strong>*165#</strong> on your MTN line</div></div>
        <div class="pay-step"><div class="pay-num" style="background:#ca8a04;color:#fff;">2</div><div style="color:#713f12;" class="body-sm">Select <strong>Send Money → Pay Merchant</strong></div></div>
        <div class="pay-step"><div class="pay-num" style="background:#ca8a04;color:#fff;">3</div><div style="color:#713f12;" class="body-sm">Merchant: <strong>9876543</strong></div></div>
        <div class="pay-step" style="margin-bottom:0;"><div class="pay-num" style="background:#ca8a04;color:#fff;">4</div><div style="color:#713f12;" class="body-sm">Amount: <strong>{{ $total }}</strong> · Reference: <strong>{{ $order->order_number }}</strong></div></div>
      </div>

    @endif

    {{-- ── ORDER SUMMARY ── --}}
    <div class="confirm-card">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
        <div>
          <div class="heading-sm" style="color:var(--text);">Order Summary</div>
          <div class="code" style="color:var(--text-muted);">{{ $order->order_number }}</div>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
          @php $sc=['pending'=>'badge-gray','confirmed'=>'badge-sky','packing'=>'badge-earth','dispatched'=>'badge-sky','on_the_way'=>'badge-purple','delivered'=>'badge-green','cancelled'=>'badge-coral']; $sl=\App\Models\Order::STATUS_LABELS; @endphp
          <span class="badge {{ $sc[$order->status]??'badge-gray' }}">{{ $sl[$order->status]??ucfirst($order->status) }}</span>
          <span class="badge {{ $order->payment_status==='paid'?'badge-green':'badge-gray' }}">
            <i class="fas fa-{{ $order->payment_status==='paid'?'check-circle':'clock' }}"></i>
            Payment {{ ucfirst($order->payment_status) }}
          </span>
        </div>
      </div>

      {{-- Items --}}
      @foreach($order->items as $item)
        <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);" class="body-sm">
          <span>{{ $item->quantity }}× {{ $item->product_name }}</span>
          <span style="font-weight:700;color:var(--primary);">{{ $order->currency }} {{ number_format($item->total_price) }}</span>
        </div>
      @endforeach

      {{-- Totals --}}
      <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);color:var(--text-muted);" class="body-sm">
        <span>Subtotal</span><span style="font-weight:600;color:var(--text);">{{ $order->currency }} {{ number_format($order->subtotal) }}</span>
      </div>
      <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);color:var(--text-muted);" class="body-sm">
        <span>Delivery fee</span><span style="font-weight:600;color:var(--text);">{{ $order->currency }} {{ number_format($order->delivery_fee) }}</span>
      </div>
      <div style="display:flex;justify-content:space-between;padding:14px 0;" class="heading-sm">
        <span>Total</span><span style="color:var(--primary);">{{ $order->currency }} {{ number_format($order->total) }}</span>
      </div>

      {{-- Delivery details --}}
      <div style="background:var(--bg-2);border-radius:var(--radius-md);padding:14px;margin-top:6px;">
        <div class="label-sm" style="color:var(--text-muted);margin-bottom:10px;"><i class="fas fa-truck" style="color:var(--primary);"></i> Delivery Details</div>
        <div style="color:var(--text);" class="body-sm">
          <strong>{{ $order->delivery_address }}</strong><br>
          @if($order->delivery_town){{ $order->delivery_town }}, @endif{{ $order->delivery_district }}
        </div>
        <div style="color:var(--text-muted);margin-top:6px;" class="body-sm">Payment method: <strong>{{ ucwords(str_replace('_',' ',$order->payment_method)) }}</strong></div>
      </div>

      {{-- What happens next --}}
      <div style="background:var(--bg-2);border-radius:var(--radius-md);padding:16px;margin-top:14px;">
        <div style="font-weight:700;margin-bottom:12px;" class="body-sm">What happens next?</div>
        @foreach([
          ['fas fa-check-circle','var(--green-100)','var(--green-700)','Order Confirmed','Payment verified — seller notified'],
          ['fas fa-box','#fff7ed','#c2410c','Packing','Seller prepares your items for dispatch'],
          ['fas fa-truck','#e0f2fe','var(--sky-600)','Dispatched','Package left the seller heading to you'],
          ['fas fa-map-marker-alt','#f5f3ff','#6d28d9','On the Way','Driver assigned — track live on map'],
          ['fas fa-home','var(--green-100)','var(--green-700)','Delivered','Items arrive · Rate your experience'],
        ] as [$icon,$bg,$color,$title,$desc])
          <div class="step-row">
            <div class="step-icon" style="background:{{ $bg }};color:{{ $color }};"><i class="{{ $icon }}"></i></div>
            <div><div style="font-weight:600;color:var(--text);" class="body-sm">{{ $title }}</div><div style="color:var(--text-muted);" class="body-sm">{{ $desc }}</div></div>
          </div>
        @endforeach
      </div>

      {{-- Actions --}}
      <div style="display:flex;gap:10px;margin-top:22px;flex-wrap:wrap;">
        <a href="{{ route('marketplace.my-orders') }}" class="btn btn-primary btn-md"><i class="fas fa-box"></i> Track My Order</a>
        <a href="{{ route('marketplace') }}" class="btn btn-outline btn-md"><i class="fas fa-store"></i> Continue Shopping</a>
        <button onclick="reportIssue({{ $order->id }},'{{ $order->order_number }}')" class="btn btn-sm" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;"><i class="fas fa-flag"></i> Need Help?</button>
      </div>
    </div>

    {{-- SMS confirmation note --}}
    <div style="text-align:center;margin-top:20px;color:var(--text-muted);" class="body-sm">
      <i class="fas fa-sms" style="color:var(--primary);"></i>
      An SMS confirmation has been sent to <strong>{{ Auth::user()->phone ?? 'your registered number' }}</strong>
    </div>

  </div>
</div>
@include('partials.footer')
@endsection
