@extends('layouts.app')
@section('title', 'Order Confirmed — AgriTech Pro')
@section('extra_css')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"/>
<style>
.confirm-hero{background:linear-gradient(135deg,#052e0f,#0d4a1e);padding:56px 0;color:#fff;text-align:center;}
.confirm-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:32px;max-width:660px;margin:0 auto;}
.step-row{display:flex;align-items:flex-start;gap:14px;padding:14px 0;border-bottom:1px solid var(--border);}
.step-row:last-child{border-bottom:none;}
.step-icon{width:42px;height:42px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.95rem;}
.pay-box{border-radius:var(--radius-lg);padding:22px;margin-bottom:24px;}
.pay-step{display:flex;gap:12px;margin-bottom:12px;align-items:flex-start;}
.pay-num{width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:800;flex-shrink:0;}
@media(max-width:768px){.confirm-hero{padding:40px 0;}.confirm-hero h1{font-size:1.5rem !important;}.confirm-card{padding:24px 16px;}.pay-box{padding:18px 14px;}.pay-step div:last-child{font-size:.82rem !important;}}
</style>
@endsection

@section('content')

{{-- Hero --}}
<section class="confirm-hero">
  <div class="container">
    <div style="font-size:4rem;margin-bottom:16px;">🎉</div>
    <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:800;margin-bottom:8px;">Order Placed Successfully!</h1>
    <p style="opacity:.8;font-size:1rem;">Order <strong style="font-family:var(--font-mono);">{{ $order->order_number }}</strong> received</p>
    <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);border-radius:var(--radius-full);padding:6px 18px;margin-top:12px;font-size:.84rem;">
      <i class="fas fa-clock"></i> {{ now()->format('M j, Y g:i A') }}
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
          <div><div style="font-family:var(--font-display);font-size:1.05rem;font-weight:800;color:#9a3412;">Pay via Airtel Money</div><div style="font-size:.82rem;color:#c2410c;">Complete payment now to confirm your order</div></div>
        </div>
        <div class="pay-step"><div class="pay-num" style="background:#ea580c;color:#fff;">1</div><div style="font-size:.88rem;color:#7c2d12;">Dial <strong>*299#</strong> on your Airtel line</div></div>
        <div class="pay-step"><div class="pay-num" style="background:#ea580c;color:#fff;">2</div><div style="font-size:.88rem;color:#7c2d12;">Select <strong>Send Money</strong></div></div>
        <div class="pay-step"><div class="pay-num" style="background:#ea580c;color:#fff;">3</div><div style="font-size:.88rem;color:#7c2d12;">Enter merchant number: <strong style="font-size:1rem;">1234567</strong></div></div>
        <div class="pay-step"><div class="pay-num" style="background:#ea580c;color:#fff;">4</div><div style="font-size:.88rem;color:#7c2d12;">Enter amount: <strong style="font-size:1rem;">{{ $total }}</strong></div></div>
        <div class="pay-step"><div class="pay-num" style="background:#ea580c;color:#fff;">5</div><div style="font-size:.88rem;color:#7c2d12;">Reference / Reason: <strong>{{ $order->order_number }}</strong></div></div>
        <div class="pay-step" style="margin-bottom:0;"><div class="pay-num" style="background:#ea580c;color:#fff;">6</div><div style="font-size:.88rem;color:#7c2d12;">Enter PIN and confirm</div></div>
        <div style="margin-top:14px;background:rgba(234,88,12,.1);border-radius:var(--radius-md);padding:10px 14px;font-size:.8rem;color:#9a3412;">
          <i class="fas fa-info-circle"></i> You'll receive an SMS confirmation from Airtel Money. Your order will be confirmed within 30 minutes.
        </div>
      </div>

    @elseif($paymentMethod === 'tnm_mpamba')
      <div class="pay-box" style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border:1.5px solid #3b82f6;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
          <div style="width:48px;height:48px;background:#1d4ed8;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;">📱</div>
          <div><div style="font-family:var(--font-display);font-size:1.05rem;font-weight:800;color:#1e3a8a;">Pay via TNM Mpamba</div><div style="font-size:.82rem;color:#1d4ed8;">Complete payment to confirm your order</div></div>
        </div>
        <div class="pay-step"><div class="pay-num" style="background:#1d4ed8;color:#fff;">1</div><div style="font-size:.88rem;color:#1e3a8a;">Dial <strong>*116#</strong> on your TNM line</div></div>
        <div class="pay-step"><div class="pay-num" style="background:#1d4ed8;color:#fff;">2</div><div style="font-size:.88rem;color:#1e3a8a;">Select <strong>Send Money</strong></div></div>
        <div class="pay-step"><div class="pay-num" style="background:#1d4ed8;color:#fff;">3</div><div style="font-size:.88rem;color:#1e3a8a;">Merchant number: <strong style="font-size:1rem;">7654321</strong></div></div>
        <div class="pay-step"><div class="pay-num" style="background:#1d4ed8;color:#fff;">4</div><div style="font-size:.88rem;color:#1e3a8a;">Amount: <strong style="font-size:1rem;">{{ $total }}</strong></div></div>
        <div class="pay-step" style="margin-bottom:0;"><div class="pay-num" style="background:#1d4ed8;color:#fff;">5</div><div style="font-size:.88rem;color:#1e3a8a;">Reference: <strong>{{ $order->order_number }}</strong>, then confirm</div></div>
      </div>

    @elseif($paymentMethod === 'mtn_momo')
      <div class="pay-box" style="background:linear-gradient(135deg,#fefce8,#fef9c3);border:1.5px solid #ca8a04;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
          <div style="width:48px;height:48px;background:#ca8a04;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;">📱</div>
          <div><div style="font-family:var(--font-display);font-size:1.05rem;font-weight:800;color:#713f12;">Pay via MTN MoMo</div><div style="font-size:.82rem;color:#92400e;">Complete payment to confirm your order</div></div>
        </div>
        <div class="pay-step"><div class="pay-num" style="background:#ca8a04;color:#fff;">1</div><div style="font-size:.88rem;color:#713f12;">Dial <strong>*165#</strong> on your MTN line</div></div>
        <div class="pay-step"><div class="pay-num" style="background:#ca8a04;color:#fff;">2</div><div style="font-size:.88rem;color:#713f12;">Select <strong>Send Money → Pay Merchant</strong></div></div>
        <div class="pay-step"><div class="pay-num" style="background:#ca8a04;color:#fff;">3</div><div style="font-size:.88rem;color:#713f12;">Merchant: <strong style="font-size:1rem;">9876543</strong></div></div>
        <div class="pay-step" style="margin-bottom:0;"><div class="pay-num" style="background:#ca8a04;color:#fff;">4</div><div style="font-size:.88rem;color:#713f12;">Amount: <strong>{{ $total }}</strong> · Reference: <strong>{{ $order->order_number }}</strong></div></div>
      </div>

    @else
      {{-- Cash on Delivery --}}
      <div class="pay-box" style="background:var(--green-50);border:1.5px solid var(--green-200);">
        <div style="display:flex;align-items:center;gap:12px;">
          <div style="width:48px;height:48px;background:var(--primary);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;color:#fff;">💵</div>
          <div>
            <div style="font-family:var(--font-display);font-size:1.05rem;font-weight:800;color:var(--green-700);">Cash on Delivery</div>
            <div style="font-size:.84rem;color:var(--green-600);">Pay <strong>{{ $total }}</strong> to the driver when your order arrives. No payment needed now.</div>
          </div>
        </div>
      </div>
    @endif

    {{-- ── ORDER SUMMARY ── --}}
    <div class="confirm-card">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
        <div>
          <div style="font-family:var(--font-display);font-size:1rem;font-weight:800;color:var(--text);">Order Summary</div>
          <div style="font-size:.78rem;color:var(--text-muted);font-family:var(--font-mono);">{{ $order->order_number }}</div>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
          @php $sc=['pending'=>'badge-gray','confirmed'=>'badge-sky','processing'=>'badge-earth','dispatched'=>'badge-sky','delivered'=>'badge-green','cancelled'=>'badge-coral']; @endphp
          <span class="badge {{ $sc[$order->status]??'badge-gray' }}">{{ ucfirst($order->status) }}</span>
          <span class="badge {{ $order->payment_status==='paid'?'badge-green':'badge-gray' }}">
            <i class="fas fa-{{ $order->payment_status==='paid'?'check-circle':'clock' }}"></i>
            Payment {{ ucfirst($order->payment_status) }}
          </span>
        </div>
      </div>

      {{-- Items --}}
      @foreach($order->items as $item)
        <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);font-size:.88rem;">
          <span>{{ $item->quantity }}× {{ $item->product_name }}</span>
          <span style="font-weight:700;color:var(--primary);">{{ $order->currency }} {{ number_format($item->total_price) }}</span>
        </div>
      @endforeach

      {{-- Totals --}}
      <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);font-size:.85rem;color:var(--text-muted);">
        <span>Subtotal</span><span style="font-weight:600;color:var(--text);">{{ $order->currency }} {{ number_format($order->subtotal) }}</span>
      </div>
      <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);font-size:.85rem;color:var(--text-muted);">
        <span>Delivery fee</span><span style="font-weight:600;color:var(--text);">{{ $order->currency }} {{ number_format($order->delivery_fee) }}</span>
      </div>
      <div style="display:flex;justify-content:space-between;padding:14px 0;font-family:var(--font-display);font-size:1.2rem;font-weight:800;">
        <span>Total</span><span style="color:var(--primary);">{{ $order->currency }} {{ number_format($order->total) }}</span>
      </div>

      {{-- Delivery details --}}
      <div style="background:var(--bg-2);border-radius:var(--radius-md);padding:14px;margin-top:6px;">
        <div style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:10px;"><i class="fas fa-truck" style="color:var(--primary);"></i> Delivery Details</div>
        <div style="font-size:.85rem;color:var(--text);">
          <strong>{{ $order->delivery_address }}</strong><br>
          @if($order->delivery_town){{ $order->delivery_town }}, @endif{{ $order->delivery_district }}
        </div>
        <div style="font-size:.8rem;color:var(--text-muted);margin-top:6px;">Payment method: <strong>{{ ucwords(str_replace('_',' ',$order->payment_method)) }}</strong></div>
      </div>

      {{-- What happens next --}}
      <div style="background:var(--bg-2);border-radius:var(--radius-md);padding:16px;margin-top:14px;">
        <div style="font-weight:700;font-size:.88rem;margin-bottom:12px;">What happens next?</div>
        @foreach([
          ['fas fa-check-circle','var(--green-100)','var(--green-700)','Order Confirmed','Payment verified — seller notified'],
          ['fas fa-box','#fff7ed','#c2410c','Processing','Seller prepares your items'],
          ['fas fa-truck','#e0f2fe','var(--sky-600)','Out for Delivery','Driver picks up and heads to you'],
          ['fas fa-home','var(--green-100)','var(--green-700)','Delivered','Items arrive · Rate your experience'],
        ] as [$icon,$bg,$color,$title,$desc])
          <div class="step-row">
            <div class="step-icon" style="background:{{ $bg }};color:{{ $color }};"><i class="{{ $icon }}"></i></div>
            <div><div style="font-weight:600;font-size:.85rem;color:var(--text);">{{ $title }}</div><div style="font-size:.78rem;color:var(--text-muted);">{{ $desc }}</div></div>
          </div>
        @endforeach
      </div>

      {{-- Actions --}}
      <div style="display:flex;gap:10px;margin-top:22px;flex-wrap:wrap;">
        <a href="{{ route('marketplace.my-orders') }}" class="btn btn-primary btn-md"><i class="fas fa-box"></i> Track My Order</a>
        <a href="{{ route('marketplace') }}" class="btn btn-outline btn-md"><i class="fas fa-store"></i> Continue Shopping</a>
      </div>
    </div>

    {{-- SMS confirmation note --}}
    <div style="text-align:center;margin-top:20px;font-size:.82rem;color:var(--text-muted);">
      <i class="fas fa-sms" style="color:var(--primary);"></i>
      An SMS confirmation has been sent to <strong>{{ Auth::user()->phone ?? 'your registered number' }}</strong>
    </div>

  </div>
</div>
@include('partials.footer')
@endsection
