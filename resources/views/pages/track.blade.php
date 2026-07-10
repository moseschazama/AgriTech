@extends('layouts.app')
@section('title', 'Track Order {{ $delivery->tracking_number }} — AgriTech Pro')
@section('extra_css')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"/>
<style>
.track-hero{background:linear-gradient(135deg,#0f172a,#1e3a5f);padding:40px 0;color:#fff;}
.track-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:28px;margin-bottom:20px;}
.timeline{position:relative;padding-left:36px;}
.timeline::before{content:'';position:absolute;left:14px;top:8px;bottom:8px;width:2px;background:var(--border);}
.timeline-item{position:relative;margin-bottom:24px;}
.timeline-item:last-child{margin-bottom:0;}
.timeline-dot{position:absolute;left:-29px;top:3px;width:16px;height:16px;border-radius:50%;border:2px solid var(--border);background:var(--bg-card);}
.timeline-dot.done{background:var(--primary);border-color:var(--primary);}
.timeline-dot.active{background:var(--primary);border-color:var(--primary);box-shadow:0 0 0 4px rgba(22,163,74,.2);}
.timeline-time{font-size:.74rem;color:var(--text-muted);font-family:var(--font-mono);}
.timeline-label{font-weight:600;font-size:.88rem;color:var(--text);}
.timeline-note{font-size:.78rem;color:var(--text-muted);margin-top:2px;}
.map-box{background:linear-gradient(135deg,#e8f5e9,#c8e6c9);border-radius:var(--radius-lg);height:260px;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:10px;border:1px solid var(--green-200);position:relative;}
.map-truck{font-size:3rem;animation:bounce 2s infinite;}
@keyframes bounce{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}
.progress-outer{background:var(--bg-2);border-radius:20px;height:10px;overflow:hidden;margin:14px 0;}
.progress-inner{height:10px;background:linear-gradient(90deg,var(--primary),#4ade80);border-radius:20px;transition:width 1s ease;}
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.info-block{background:var(--bg-2);border-radius:var(--radius-md);padding:16px;}
.info-row{display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--border);font-size:.82rem;}
.info-row:last-child{border-bottom:none;}
.info-key{color:var(--text-muted);}
.info-val{font-weight:600;color:var(--text);}
@media(max-width:768px){.info-grid{grid-template-columns:1fr;}}
</style>
@endsection

@section('content')
{{-- Hero --}}
<section class="track-hero">
  <div class="container">
    <div style="text-align:center;">
      <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(59,130,246,.15);border:1px solid rgba(59,130,246,.3);border-radius:var(--radius-full);padding:5px 14px;font-size:.78rem;font-weight:700;color:#93c5fd;margin-bottom:14px;">
        <i class="fas fa-truck"></i> Order Tracking
      </span>
      <h1 style="font-family:var(--font-display);font-size:1.8rem;font-weight:800;margin-bottom:6px;">
        Tracking: <span style="font-family:var(--font-mono);color:#60a5fa;">{{ $delivery->tracking_number }}</span>
      </h1>
      @php
        $statusColors=['pending'=>'#94a3b8','assigned'=>'#60a5fa','collected'=>'#fb923c','in_transit'=>'#60a5fa','near_destination'=>'#4ade80','delivered'=>'#4ade80','failed'=>'#ef4444'];
        $color=$statusColors[$delivery->status]??'#94a3b8';
      @endphp
      <span style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);border-radius:var(--radius-full);padding:6px 16px;font-size:.88rem;font-weight:700;color:{{ $color }};">
        {{ ucwords(str_replace('_',' ',$delivery->status)) }}
      </span>
    </div>
  </div>
</section>

<div class="section" style="background:var(--bg-2);">
  <div class="container" style="max-width:860px;">

    {{-- Progress --}}
    <div class="track-card">
      @php
        $steps=['pending'=>['Order Placed','fas fa-shopping-cart'],
                'assigned'=>['Driver Assigned','fas fa-user-tie'],
                'collected'=>['Picked Up','fas fa-box'],
                'in_transit'=>['On the Way','fas fa-truck'],
                'near_destination'=>['Almost There','fas fa-map-marker-alt'],
                'delivered'=>['Delivered ✓','fas fa-check-circle']];
        $stepKeys=array_keys($steps);
        $currentIdx=array_search($delivery->status,$stepKeys);
        if($currentIdx===false)$currentIdx=0;
        $pct=$delivery->progressPercentage();
      @endphp

      <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
        <span style="font-size:.82rem;font-weight:600;color:var(--text);">Delivery Progress</span>
        <span style="font-size:.82rem;font-weight:700;color:var(--primary);">{{ $pct }}%</span>
      </div>
      <div class="progress-outer"><div class="progress-inner" id="progressBar" style="width:{{ $pct }}%;"></div></div>

      <div style="display:flex;justify-content:space-between;overflow-x:auto;padding-bottom:8px;margin-top:16px;gap:4px;">
        @foreach($steps as $key=>[$label,$icon])
          @php $idx=array_search($key,$stepKeys); $done=$idx<$currentIdx; $active=$idx===$currentIdx; @endphp
          <div style="flex:1;text-align:center;min-width:60px;">
            <div style="width:36px;height:36px;border-radius:50%;margin:0 auto 6px;display:flex;align-items:center;justify-content:center;font-size:.8rem;border:2px solid {{ $done||$active?'var(--primary)':'var(--border)' }};background:{{ $done||$active?'var(--primary)':'var(--bg-card)' }};color:{{ $done||$active?'#fff':'var(--text-muted)' }};{{ $active?'box-shadow:0 0 0 4px rgba(22,163,74,.2);':'' }}">
              <i class="{{ $icon }}"></i>
            </div>
            <div style="font-size:.65rem;font-weight:{{ $active?'700':'500' }};color:{{ $active?'var(--primary)':($done?'var(--text-muted)':'var(--text-muted)') }};line-height:1.3;">{{ $label }}</div>
          </div>
        @endforeach
      </div>
    </div>

    {{-- Map & ETA --}}
    <div class="track-card">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
        <div style="font-family:var(--font-display);font-size:.95rem;font-weight:700;">📍 Live Location</div>
        @if($delivery->estimated_arrival_at)
          <div style="background:var(--green-50);border:1px solid var(--green-200);border-radius:var(--radius-full);padding:5px 14px;font-size:.82rem;font-weight:700;color:var(--green-700);">
            <i class="fas fa-clock"></i> ETA: {{ $delivery->estimated_arrival_at->format('g:i A') }}
          </div>
        @endif
      </div>
      <div class="map-box" id="liveMap">
        @if($delivery->status==='delivered')
          <div style="font-size:3rem;">✅</div>
          <div style="font-weight:700;color:var(--green-700);">Order Delivered!</div>
          <div style="font-size:.8rem;color:var(--text-muted);">Delivered on {{ $delivery->delivered_at?->format('M j, Y g:i A') }}</div>
        @elseif($delivery->driver_current_lat)
          <div class="map-truck">🚚</div>
          <div style="font-weight:700;color:var(--green-700);">Driver is {{ $delivery->distance_remaining_km??'?' }} km away</div>
          <div style="font-size:.78rem;color:var(--text-muted);">Last updated {{ $delivery->driver_location_updated_at?->diffForHumans() }}</div>
          <div style="position:absolute;bottom:10px;right:10px;">
            <button onclick="refreshLive()" class="btn btn-outline btn-sm" style="font-size:.72rem;"><i class="fas fa-sync" id="refreshIcon"></i> Refresh</button>
          </div>
        @else
          <div class="map-truck">📦</div>
          <div style="font-weight:700;color:var(--green-700);">
            @if($delivery->status==='pending') Awaiting driver assignment
            @elseif($delivery->status==='assigned') Driver assigned — awaiting pickup
            @else Tracking will appear when driver is en route @endif
          </div>
          <div style="font-size:.78rem;color:var(--text-muted);">From {{ $delivery->origin_district }} → {{ $delivery->destination_district }}</div>
        @endif
      </div>
    </div>

    {{-- Delivery Info --}}
    <div class="info-grid">
      <div class="info-block">
        <div style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:12px;"><i class="fas fa-box" style="color:var(--primary);"></i> Order Details</div>
        @foreach($delivery->order->items as $item)
          <div class="info-row">
            <span class="info-key">{{ $item->quantity }}× {{ $item->product_name }}</span>
            <span class="info-val">MWK {{ number_format($item->total_price) }}</span>
          </div>
        @endforeach
        <div class="info-row"><span class="info-key">Delivery fee</span><span class="info-val">MWK {{ number_format($delivery->order->delivery_fee) }}</span></div>
        <div class="info-row"><span class="info-key" style="font-weight:700;color:var(--text);">Total</span><span class="info-val" style="color:var(--primary);">MWK {{ number_format($delivery->order->total) }}</span></div>
        <div class="info-row">
          <span class="info-key">Payment</span>
          <span class="info-val"><span class="badge {{ $delivery->order->payment_status==='paid'?'badge-green':'badge-gray' }}" style="font-size:.65rem;">{{ ucfirst($delivery->order->payment_status) }}</span></span>
        </div>
      </div>

      <div class="info-block">
        <div style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:12px;"><i class="fas fa-user-tie" style="color:var(--primary);"></i> Driver Details</div>
        @if($delivery->driver)
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid var(--border);">
            <div class="avatar avatar-md" style="background:linear-gradient(135deg,#1e3a5f,#2563eb);color:#fff;font-size:.7rem;">{{ substr($delivery->driver->user->first_name??'D',0,1) }}{{ substr($delivery->driver->user->last_name??'R',0,1) }}</div>
            <div>
              <div style="font-weight:700;color:var(--text);font-size:.88rem;">{{ $delivery->driver->user->full_name??'Driver' }}</div>
              <div style="font-size:.75rem;color:var(--text-muted);">{{ $delivery->driver->vehicle_type }} · {{ $delivery->driver->vehicle_plate }}</div>
              <div style="color:#f59e0b;font-size:.8rem;">{{ str_repeat('★',round($delivery->driver->average_rating??4)) }}</div>
            </div>
          </div>
          <div class="info-row"><span class="info-key">Total deliveries</span><span class="info-val">{{ $delivery->driver->total_deliveries }}</span></div>
          <div class="info-row"><span class="info-key">Success rate</span><span class="info-val" style="color:var(--primary);">98%</span></div>
          <div style="display:flex;gap:8px;margin-top:14px;">
            <button onclick="showToast('📞 Calling driver...','info')" class="btn btn-outline btn-sm" style="flex:1;justify-content:center;"><i class="fas fa-phone"></i> Call</button>
            <button onclick="showToast('📱 Message sent','success')" class="btn btn-outline btn-sm" style="flex:1;justify-content:center;"><i class="fas fa-sms"></i> SMS</button>
          </div>
        @else
          <div style="text-align:center;padding:20px;color:var(--text-muted);">
            <i class="fas fa-user-clock" style="font-size:2rem;margin-bottom:8px;display:block;opacity:.3;"></i>
            Driver being assigned...
          </div>
        @endif
      </div>
    </div>

    {{-- Status Log --}}
    @if($delivery->statusLogs->count()>0)
    <div class="track-card" style="margin-top:20px;">
      <div style="font-family:var(--font-display);font-size:.95rem;font-weight:700;margin-bottom:18px;">📋 Status History</div>
      <div class="timeline">
        @foreach($delivery->statusLogs->sortByDesc('created_at') as $log)
          <div class="timeline-item">
            <div class="timeline-dot {{ $loop->first?'active':'done' }}"></div>
            <div class="timeline-time">{{ $log->created_at->format('M j, Y g:i A') }}</div>
            <div class="timeline-label">{{ ucwords(str_replace('_',' ',$log->status)) }}</div>
            @if($log->note)<div class="timeline-note">{{ $log->note }}</div>@endif
          </div>
        @endforeach
      </div>
    </div>
    @endif

    {{-- Back to tracking --}}
    <div style="text-align:center;margin-top:24px;">
      <a href="{{ route('delivery') }}" class="btn btn-outline btn-md"><i class="fas fa-arrow-left"></i> Back to My Deliveries</a>
    </div>

  </div>
</div>
@include('partials.footer')
@endsection
@section('extra_js')
<script>
// Poll live status every 15s
@if(!in_array($delivery->status,['delivered','failed']))
setInterval(async () => {
  try {
    const res = await fetch('/track/{{ $delivery->id }}/live-status');
    const d = await res.json();
    document.getElementById('progressBar').style.width = d.progress_percentage + '%';
  } catch(e){}
}, 15000);
@endif

function refreshLive() {
  document.getElementById('refreshIcon').className = 'fas fa-spinner fa-spin';
  setTimeout(() => { window.location.reload(); }, 1200);
}
</script>
@endsection
