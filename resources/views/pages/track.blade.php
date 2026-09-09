@extends('layouts.app')
@section('title', 'Track Order {{ $delivery->tracking_number }} — AgriTech Pro')
@section('extra_css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
.track-hero{background:#0f172a;padding:36px 0;color:#fff;}
.track-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:24px;margin-bottom:18px;}
.map-container{border-radius:var(--radius-lg);overflow:hidden;height:380px;border:1px solid var(--border);position:relative;}
#deliveryMap{width:100%;height:100%;z-index:1;}
.map-overlay-status{position:absolute;top:12px;left:12px;z-index:1000;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);padding:10px 16px;box-shadow:0 4px 12px rgba(0,0,0,.15);display:flex;align-items:center;gap:10px;}
.map-overlay-status .status-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0;animation:pulse-dot 2s infinite;}
@keyframes pulse-dot{0%,100%{opacity:1;}50%{opacity:.4;}}
.map-overlay-eta{position:absolute;bottom:12px;right:12px;z-index:1000;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);padding:10px 16px;box-shadow:0 4px 12px rgba(0,0,0,.15);text-align:right;}
.progress-outer{background:var(--bg-2);border-radius:20px;height:8px;overflow:hidden;margin:12px 0;}
.progress-inner{height:8px;background:var(--primary);border-radius:20px;transition:width .8s ease;}
.steps-row{display:flex;justify-content:space-between;gap:2px;margin-top:14px;}
.step-item{flex:1;text-align:center;position:relative;}
.step-circle{width:32px;height:32px;border-radius:50%;margin:0 auto 5px;display:flex;align-items:center;justify-content:center;font-size:.7rem;border:2px solid var(--border);background:var(--bg-card);color:var(--text-muted);transition:all .3s;}
.step-circle.done{background:var(--primary);border-color:var(--primary);color:#fff;}
.step-circle.active{background:var(--primary);border-color:var(--primary);color:#fff;box-shadow:0 0 0 4px rgba(22,163,74,.2);}
.step-label{font-size:.65rem;font-weight:600;color:var(--text-muted);line-height:1.2;}
.step-label.done,.step-label.active{color:var(--primary);}
.proximity-banner{border-radius:var(--radius-md);padding:16px;margin-bottom:18px;text-align:center;display:flex;align-items:center;gap:14px;}
.proximity-banner .prox-icon{font-size:2rem;flex-shrink:0;}
.proximity-banner .prox-text h3{font-weight:700;margin-bottom:2px;}
.proximity-banner .prox-text p{opacity:.8;font-size:.82rem;}
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.info-block{background:var(--bg-2);border-radius:var(--radius-md);padding:16px;}
.info-row{display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--border);font-size:.82rem;}
.info-row:last-child{border-bottom:none;}
.info-key{color:var(--text-muted);}
.info-val{font-weight:600;color:var(--text);}
.timeline{position:relative;padding-left:32px;margin-top:14px;}
.timeline::before{content:'';position:absolute;left:12px;top:6px;bottom:6px;width:2px;background:var(--border);}
.tl-item{position:relative;margin-bottom:18px;}
.tl-item:last-child{margin-bottom:0;}
.tl-dot{position:absolute;left:-26px;top:2px;width:14px;height:14px;border-radius:50%;border:2px solid var(--border);background:var(--bg-card);}
.tl-dot.done{background:var(--primary);border-color:var(--primary);}
.tl-dot.active{background:var(--primary);border-color:var(--primary);box-shadow:0 0 0 3px rgba(22,163,74,.2);}
.tl-time{font-size:.72rem;color:var(--text-muted);font-family:'JetBrains Mono',monospace;}
.tl-label{font-weight:600;font-size:.85rem;}
.tl-note{color:var(--text-muted);font-size:.78rem;margin-top:2px;}
.waypoint-list{max-height:200px;overflow-y:auto;}
.waypoint-item{display:flex;align-items:center;gap:8px;padding:8px 0;border-bottom:1px solid var(--border);font-size:.82rem;}
.waypoint-item:last-child{border-bottom:none;}
.waypoint-dot{width:8px;height:8px;border-radius:50%;background:var(--border);flex-shrink:0;}
.waypoint-dot.passed{background:var(--primary);}
.waypoint-dot.current{background:#f59e0b;animation:pulse-dot 1.5s infinite;}
@media(max-width:768px){.info-grid{grid-template-columns:1fr;}.map-container{height:280px;}.steps-row{gap:1px;}.step-label{font-size:.55rem;}}
</style>
@endsection

@section('content')

@if(!isset($delivery) || !$delivery)
  {{-- Tracking number not found --}}
  <section class="track-hero">
    <div class="container">
      <div style="text-align:center;">
        <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(59,130,246,.15);border:1px solid rgba(59,130,246,.3);border-radius:var(--radius-full);padding:5px 14px;font-size:.78rem;font-weight:700;color:#93c5fd;margin-bottom:12px;">
          <i class="fas fa-truck"></i> Live Tracking
        </span>
        <h1 style="font-size:1.4rem;font-weight:800;margin-bottom:6px;">Track Your Order</h1>
        @if(!empty($query))
          <span style="background:rgba(239,68,68,.15);border:1px solid rgba(239,68,68,.35);border-radius:var(--radius-full);padding:5px 14px;font-weight:700;color:#fca5a5;font-size:.82rem;">
            No delivery found for "<span class="code">{{ $query }}</span>"
          </span>
        @endif
      </div>
    </div>
  </section>

  <div style="background:var(--bg-2);min-height:80vh;">
    <div class="container" style="max-width:640px;padding-top:36px;padding-bottom:40px;">
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:32px;text-align:center;box-shadow:var(--shadow-md);">
        <i class="fas fa-box-open" style="font-size:2.6rem;color:var(--text-muted);margin-bottom:14px;display:block;"></i>
        <h2 style="font-size:1.15rem;font-weight:800;color:var(--text);margin-bottom:8px;">
          {{ !empty($query) ? 'Delivery not found' : 'Enter your tracking number' }}
        </h2>
        <p style="font-size:.9rem;color:var(--text-muted);margin-bottom:20px;line-height:1.6;">
          @if(!empty($query))
            We couldn't find a delivery with the number <strong>{{ $query }}</strong>. Check the tracking number on your order confirmation — it looks like <strong>TRK-YYYYMMDD-XXXXXX</strong>.
          @else
            Enter the tracking number from your SMS or order confirmation to see live delivery status.
          @endif
        </p>
        <form action="{{ route('delivery.track', '__tracking__') }}" method="GET" id="trackForm" style="display:flex;gap:8px;">
          <input type="text" id="trackInput" name="trackingNumber" class="form-input" placeholder="e.g. TRK-20260909-ABC123" style="flex:1;" value="{{ $query ?? '' }}"/>
          <button type="submit" class="btn btn-primary btn-md"><i class="fas fa-search"></i> Track</button>
        </form>
        @if(Auth::check())
        <a href="{{ route('marketplace.my-orders') }}" style="display:inline-flex;align-items:center;gap:6px;margin-top:18px;color:var(--primary);font-weight:700;font-size:.85rem;text-decoration:underline;"><i class="fas fa-clipboard-list"></i> View my orders</a>
        @endif
      </div>
    </div>
  </div>

  <script>
  document.getElementById('trackForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const val = document.getElementById('trackInput').value.trim();
    if (!val) return showToast('Enter a tracking number first', 'error');
    window.location.href = '{{ route("delivery.track", "__tracking__") }}'.replace('__tracking__', encodeURIComponent(val));
  });
  </script>
@else
@php
  $statusColors=['pending'=>'#94a3b8','assigned'=>'#60a5fa','collected'=>'#fb923c','in_transit'=>'#60a5fa','near_destination'=>'#8b5cf6','delivered'=>'#22c55e','failed'=>'#ef4444'];
  $color=$statusColors[$delivery->status]??'#94a3b8';
  $steps=['pending'=>['Order Placed','fas fa-shopping-cart'],'assigned'=>['Assigned','fas fa-user-tie'],'collected'=>['Picked Up','fas fa-box'],'in_transit'=>['In Transit','fas fa-truck'],'near_destination'=>['Almost There','fas fa-map-marker-alt'],'delivered'=>['Delivered','fas fa-check-circle']];
  $stepKeys=array_keys($steps);
  $currentIdx=array_search($delivery->status,$stepKeys);
  if($currentIdx===false)$currentIdx=0;
  $pct=$delivery->progressPercentage();
  $waypointsData = ($waypoints && $waypoints->count())
      ? $waypoints->map(fn($w) => ['name' => $w->name, 'lat' => (float)$w->latitude, 'lng' => (float)$w->longitude, 'district' => $w->district->name ?? ''])->values()
      : [];
@endphp

{{-- Hero --}}
<section class="track-hero">
  <div class="container">
    <div style="text-align:center;">
      <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(59,130,246,.15);border:1px solid rgba(59,130,246,.3);border-radius:var(--radius-full);padding:5px 14px;font-size:.78rem;font-weight:700;color:#93c5fd;margin-bottom:12px;">
        <i class="fas fa-truck"></i> Live Tracking
      </span>
      <h1 style="font-size:1.4rem;font-weight:800;margin-bottom:6px;">
        Tracking: <span class="code" style="color:#60a5fa;">{{ $delivery->tracking_number }}</span>
      </h1>
      <span style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);border-radius:var(--radius-full);padding:5px 14px;font-weight:700;color:{{ $color }};font-size:.82rem;">
        {{ ucwords(str_replace('_',' ',$delivery->status)) }}
      </span>
      @if($delivery->distance_remaining_km !== null && !in_array($delivery->status, ['delivered','failed']))
        <div style="margin-top:8px;font-size:.82rem;opacity:.8;">
          <i class="fas fa-route"></i> {{ $delivery->distance_remaining_km }} km remaining
          @if($nearestCentre)
            · Near <strong>{{ $nearestCentre['name'] }}</strong> ({{ $nearestCentre['distance'] }}km away)
          @endif
        </div>
      @endif
    </div>
  </div>
</section>

<div style="background:var(--bg-2);min-height:80vh;">
  <div class="container" style="max-width:900px;padding-top:24px;padding-bottom:40px;">

    {{-- Proximity Alert Banner --}}
    @if($delivery->distance_remaining_km !== null && $delivery->distance_remaining_km <= 10 && !in_array($delivery->status, ['delivered','failed']))
      @php
        $proximityLevel = match(true) {
          $delivery->distance_remaining_km <= 0.5 => ['bg:#dcfce7;border:#86efac;color:#166534','fa-check-circle','Driver has arrived!','Please meet the driver to collect your order.'],
          $delivery->distance_remaining_km <= 2   => ['bg:#f5f3ff;border:#d8b4fe;color:#6d28d9','fa-bell','Arriving very soon!','Your package is just '.$delivery->distance_remaining_km.'km away. Get ready!'],
          $delivery->distance_remaining_km <= 5   => ['bg:#eff6ff;border:#bfdbfe;color:#1d4ed8','fa-truck','Approaching destination','Driver is '.$delivery->distance_remaining_km.'km from your area.'],
          default                                 => ['bg:#fefce8;border:#fde68a;color:#92400e','fa-info-circle','Getting close','Driver is '.$delivery->distance_remaining_km.'km away and making progress.'],
        };
        $parts = $proximityLevel;
      @endphp
      <div class="proximity-banner" style="background:{{ explode(';',$parts[0])[0] }};border:2px solid {{ explode(';',$parts[0])[1] }};color:{{ explode(';',$parts[0])[2] }};">
        <div class="prox-icon"><i class="fas {{ $parts[1] }}"></i></div>
        <div class="prox-text">
          <h3>{{ $parts[2] }}</h3>
          <p>{{ $parts[3] }}</p>
        </div>
      </div>
    @endif

    {{-- Progress --}}
    <div class="track-card">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2px;">
        <span style="font-weight:600;font-size:.85rem;">Delivery Progress</span>
        <span style="font-weight:700;color:var(--primary);font-size:.85rem;">{{ $pct }}%</span>
      </div>
      <div class="progress-outer"><div class="progress-inner" id="progressBar" style="width:{{ $pct }}%;"></div></div>
      <div class="steps-row">
        @foreach($steps as $key=>[$label,$icon])
          @php $idx=array_search($key,$stepKeys); $done=$idx<$currentIdx; $active=$idx===$currentIdx; @endphp
          <div class="step-item">
            <div class="step-circle {{ $done?'done':($active?'active':'') }}"><i class="{{ $icon }}"></i></div>
            <div class="step-label {{ $done?'done':($active?'active':'') }}">{{ $label }}</div>
          </div>
        @endforeach
      </div>
    </div>

    {{-- Live Map --}}
    <div class="track-card">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
        <div style="font-weight:700;font-size:.95rem;"><i class="fas fa-map-marked-alt" style="color:var(--primary);"></i> Live Location</div>
        @if($delivery->estimated_arrival_at && !in_array($delivery->status, ['delivered','failed']))
          <div style="background:var(--green-50);border:1px solid var(--green-200);border-radius:var(--radius-full);padding:4px 12px;font-weight:700;color:var(--green-700);font-size:.78rem;">
            <i class="fas fa-clock"></i> ETA: {{ $delivery->estimated_arrival_at->format('g:i A') }}
          </div>
        @endif
      </div>

      @if(in_array($delivery->status, ['delivered','failed']))
        <div style="background:var(--green-50);border:1px solid var(--green-200);border-radius:var(--radius-lg);padding:40px;text-align:center;">
          <div style="font-size:3rem;margin-bottom:10px;">{{ $delivery->status==='delivered' ? '✅' : '❌' }}</div>
          <div style="font-weight:700;font-size:1.1rem;color:{{ $delivery->status==='delivered'?'var(--green-700)':'#ef4444' }};">
            {{ $delivery->status==='delivered' ? 'Order Delivered!' : 'Delivery Failed' }}
          </div>
          <div style="color:var(--text-muted);margin-top:4px;font-size:.85rem;">
            {{ $delivery->status==='delivered' ? 'Delivered on '.$delivery->delivered_at?->format('M j, Y g:i A') : $delivery->failure_reason }}
          </div>
          @if($delivery->destination_lat && $delivery->destination_lng)
            <a href="https://www.google.com/maps/search/?api=1&query={{ $delivery->destination_lat }},{{ $delivery->destination_lng }}" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:4px;margin-top:12px;color:var(--primary);font-weight:600;text-decoration:underline;font-size:.85rem;">
              <i class="fab fa-google"></i> View destination on Google Maps
            </a>
          @endif
        </div>
      @else
        <div class="map-container">
          <div id="deliveryMap"></div>
          <div class="map-overlay-status" id="mapStatusOverlay">
            <div class="status-dot" style="background:{{ $color }};"></div>
            <div>
              <div style="font-weight:700;font-size:.82rem;" id="overlayStatusText">{{ ucwords(str_replace('_',' ',$delivery->status)) }}</div>
              <div style="font-size:.72rem;color:var(--text-muted);" id="overlayProximityText">
                @if($nearestCentre)
                  Near {{ $nearestCentre['name'] }} · {{ $nearestCentre['distance'] }}km
                @else
                  {{ $delivery->origin_district }} → {{ $delivery->destination_district }}
                @endif
              </div>
            </div>
          </div>
          <div class="map-overlay-eta" id="mapEtaOverlay">
            @if($delivery->estimated_arrival_at)
              <div style="font-size:.72rem;color:var(--text-muted);">ETA</div>
              <div style="font-weight:700;font-size:.95rem;color:var(--primary);" id="etaText">{{ $delivery->estimated_arrival_at->format('g:i A') }}</div>
            @endif
            @if($delivery->distance_remaining_km)
              <div style="font-size:.78rem;font-weight:600;color:var(--text);" id="distText">{{ $delivery->distance_remaining_km }} km</div>
            @endif
          </div>
        </div>
      @endif
    </div>

    {{-- Delivery Info --}}
    <div class="info-grid">
      <div class="info-block">
        <div style="font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;font-size:.72rem;margin-bottom:10px;"><i class="fas fa-box" style="color:var(--primary);"></i> Order Details</div>
        @foreach($delivery->order->items as $item)
          <div class="info-row">
            <span class="info-key">{{ $item->quantity }}× {{ $item->product_name }}</span>
            <span class="info-val">MWK {{ number_format($item->total_price) }}</span>
          </div>
        @endforeach
        <div class="info-row"><span class="info-key">Delivery fee</span><span class="info-val">MWK {{ number_format($delivery->order->delivery_fee) }}</span></div>
        <div class="info-row"><span class="info-key" style="font-weight:700;">Total</span><span class="info-val" style="color:var(--primary);">MWK {{ number_format($delivery->order->total) }}</span></div>
        <div class="info-row">
          <span class="info-key">Payment</span>
          <span class="info-val"><span style="font-size:.65rem;background:{{ $delivery->order->payment_status==='paid'?'var(--green-50)':'var(--bg-2)' }};border:1px solid {{ $delivery->order->payment_status==='paid'?'var(--green-200)':'var(--border)' }};padding:2px 8px;border-radius:var(--radius-full);color:{{ $delivery->order->payment_status==='paid'?'var(--green-700)':'var(--text-muted)' }};font-weight:600;">{{ ucfirst($delivery->order->payment_status) }}</span></span>
        </div>
        @php $sticker=$delivery->trackingStickers()->first(); @endphp
        @if($sticker)
          <div class="info-row">
            <span class="info-key">Sticker</span>
            <span class="info-val code" style="font-size:.78rem;background:#f5f3ff;color:#6d28d9;padding:2px 8px;border-radius:4px;">{{ $sticker->sticker_code }}</span>
          </div>
        @endif
      </div>

      <div class="info-block">
        <div style="font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;font-size:.72rem;margin-bottom:10px;"><i class="fas fa-user-tie" style="color:var(--primary);"></i> Driver Details</div>
        @if($delivery->driver)
          @php $driverPhone=$delivery->driver->user?->phone; @endphp
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid var(--border);">
            <div style="width:40px;height:40px;border-radius:50%;background:#1e3a5f;color:#fff;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;flex-shrink:0;">
              {{ substr($delivery->driver->user->first_name??'D',0,1) }}{{ substr($delivery->driver->user->last_name??'R',0,1) }}
            </div>
            <div style="min-width:0;">
              <div style="font-weight:700;font-size:.85rem;">{{ $delivery->driver->user->full_name??'Driver' }}</div>
              <div style="color:var(--text-muted);font-size:.75rem;">{{ $delivery->driver->vehicle_type }} · {{ $delivery->driver->vehicle_plate }}</div>
              @if($driverPhone)
                <div style="font-size:.75rem;margin-top:1px;"><i class="fas fa-phone" style="font-size:.65rem;"></i> {{ $driverPhone }}</div>
              @endif
              <div style="color:#f59e0b;font-size:.72rem;margin-top:2px;">{{ str_repeat('★',round($delivery->driver->average_rating??4)) }} <span style="color:var(--text-muted);">({{ $delivery->driver->total_reviews }})</span></div>
            </div>
          </div>
          @if($driverPhone)
            <div style="display:flex;gap:8px;margin-top:10px;">
              <a href="tel:{{ $driverPhone }}" style="flex:1;display:flex;align-items:center;justify-content:center;gap:4px;padding:8px;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.78rem;font-weight:600;color:var(--text);text-decoration:none;background:var(--bg-card);">
                <i class="fas fa-phone" style="color:var(--primary);"></i> Call
              </a>
              <a href="sms:{{ $driverPhone }}" style="flex:1;display:flex;align-items:center;justify-content:center;gap:4px;padding:8px;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.78rem;font-weight:600;color:var(--text);text-decoration:none;background:var(--bg-card);">
                <i class="fas fa-sms" style="color:var(--primary);"></i> SMS
              </a>
            </div>
          @endif
        @else
          <div style="text-align:center;padding:16px;color:var(--text-muted);">
            <i class="fas fa-user-clock" style="font-size:1.5rem;margin-bottom:6px;display:block;opacity:.3;"></i>
            <div style="font-size:.85rem;">Driver being assigned...</div>
          </div>
        @endif
      </div>
    </div>

    {{-- Route Waypoints --}}
    @if($waypoints && $waypoints->count() > 0 && !in_array($delivery->status, ['delivered','failed']))
    <div class="track-card" style="margin-top:18px;">
      <div style="font-weight:700;font-size:.9rem;margin-bottom:12px;"><i class="fas fa-route" style="color:var(--primary);"></i> Route — Trading Centres Along the Way</div>
      <div class="waypoint-list">
        @foreach($waypoints as $wp)
          @php
            $isCurrent = $nearestCentre && $wp->name === $nearestCentre['name'];
            $isPassed = $nearestCentre && $wp->distanceTo((float)$delivery->origin_lat, (float)$delivery->origin_lng) < ($nearestCentre['distance'] ?? PHP_FLOAT_MAX);
          @endphp
          <div class="waypoint-item">
            <div class="waypoint-dot {{ $isCurrent?'current':($isPassed?'passed':'') }}"></div>
            <div style="flex:1;">
              <div style="font-weight:600;{{ $isCurrent?'color:var(--primary);':'' }}">{{ $wp->name }}</div>
              <div style="font-size:.72rem;color:var(--text-muted);">{{ $wp->district->name ?? '' }}</div>
            </div>
            @if($delivery->driver_current_lat && $delivery->driver_current_lng)
              <div style="font-size:.72rem;color:var(--text-muted);font-family:'JetBrains Mono',monospace;">
                {{ round($wp->distanceTo((float)$delivery->driver_current_lat, (float)$delivery->driver_current_lng), 1) }}km
              </div>
            @endif
          </div>
        @endforeach
      </div>
    </div>
    @endif

    {{-- Status Log --}}
    @if($delivery->statusLogs->count()>0)
    <div class="track-card" style="margin-top:18px;">
      <div style="font-weight:700;font-size:.9rem;margin-bottom:14px;"><i class="fas fa-history" style="color:var(--primary);"></i> Status History</div>
      <div class="timeline">
        @foreach($delivery->statusLogs->sortByDesc('created_at') as $log)
          <div class="tl-item">
            <div class="tl-dot {{ $loop->first?'active':'done' }}"></div>
            <div class="tl-time">{{ $log->created_at->format('M j, Y g:i A') }}</div>
            <div class="tl-label">{{ ucwords(str_replace('_',' ',$log->status)) }}</div>
            @if($log->note)<div class="tl-note">{{ $log->note }}</div>@endif
          </div>
        @endforeach
      </div>
    </div>
    @endif

    {{-- Back --}}
    <div style="text-align:center;margin-top:20px;">
      <a href="{{ route('delivery') }}" style="display:inline-flex;align-items:center;gap:6px;padding:10px 24px;border:1px solid var(--border);border-radius:var(--radius-md);font-weight:600;color:var(--text);text-decoration:none;background:var(--bg-card);font-size:.85rem;">
        <i class="fas fa-arrow-left"></i> Back to My Deliveries
      </a>
    </div>

  </div>
</div>
@include('partials.footer')
@endsection

@section('extra_js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  @if(!in_array($delivery->status, ['delivered','failed']) && $delivery->destination_lat)
  // ── Initialize Leaflet map ──
  const destLat = {{ $delivery->destination_lat ?? 'null' }};
  const destLng = {{ $delivery->destination_lng ?? 'null' }};
  const driverLat = {{ $delivery->driver_current_lat ?? 'null' }};
  const driverLng = {{ $delivery->driver_current_lng ?? 'null' }};
  const originLat = {{ $delivery->origin_lat ?? 'null' }};
  const originLng = {{ $delivery->origin_lng ?? 'null' }};

  const mapCenter = driverLat ? [driverLat, driverLng] : (destLat ? [destLat, destLng] : [-13.9669, 33.7873]);
  const map = L.map('deliveryMap', {
    center: mapCenter,
    zoom: driverLat ? 11 : 8,
    zoomControl: false,
    attributionControl: false,
  });

  L.control.zoom({ position: 'topright' }).addTo(map);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18,
  }).addTo(map);

  // Destination marker
  const destIcon = L.divIcon({
    html: '<div style="width:28px;height:28px;border-radius:50%;background:#ef4444;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.3);display:flex;align-items:center;justify-content:center;"><i class="fas fa-home" style="color:#fff;font-size:12px;"></i></div>',
    className: '',
    iconSize: [28, 28],
    iconAnchor: [14, 14],
  });
  if (destLat && destLng) {
    L.marker([destLat, destLng], { icon: destIcon })
      .addTo(map)
      .bindPopup('<strong>Destination</strong><br>{{ $delivery->destination_district }}@if($delivery->order?->delivery_town) · {{ $delivery->order->delivery_town }}@endif');
  }

  // Origin marker
  const originIcon = L.divIcon({
    html: '<div style="width:24px;height:24px;border-radius:50%;background:#22c55e;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.3);display:flex;align-items:center;justify-content:center;"><i class="fas fa-store" style="color:#fff;font-size:10px;"></i></div>',
    className: '',
    iconSize: [24, 24],
    iconAnchor: [12, 12],
  });
  if (originLat && originLng) {
    L.marker([originLat, originLng], { icon: originIcon })
      .addTo(map)
      .bindPopup('<strong>Origin</strong><br>{{ $delivery->origin_district }}');
  }

  // Driver marker
  let driverMarker = null;
  const driverIcon = L.divIcon({
    html: '<div style="width:36px;height:36px;border-radius:50%;background:#3b82f6;border:3px solid #fff;box-shadow:0 2px 12px rgba(59,130,246,.5);display:flex;align-items:center;justify-content:center;animation:pulse-dot 2s infinite;"><i class="fas fa-truck" style="color:#fff;font-size:14px;"></i></div>',
    className: '',
    iconSize: [36, 36],
    iconAnchor: [18, 18],
  });
  if (driverLat && driverLng) {
    driverMarker = L.marker([driverLat, driverLng], { icon: driverIcon })
      .addTo(map)
      .bindPopup('<strong>Driver Location</strong><br>{{ $delivery->distance_remaining_km ?? "?" }}km remaining');
  }

  // Route waypoints
  @if($waypoints && $waypoints->count() > 0)
    const waypoints = @json($waypointsData);
    const wpIcon = L.divIcon({
      html: '<div style="width:16px;height:16px;border-radius:50%;background:#f59e0b;border:2px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,.2);"></div>',
      className: '',
      iconSize: [16, 16],
      iconAnchor: [8, 8],
    });
    waypoints.forEach(wp => {
      L.marker([wp.lat, wp.lng], { icon: wpIcon })
        .addTo(map)
        .bindPopup('<strong>' + wp.name + '</strong><br>' + wp.district);
    });

    // Draw route line
    const routeCoords = [];
    if (originLat && originLng) routeCoords.push([originLat, originLng]);
    waypoints.forEach(wp => routeCoords.push([wp.lat, wp.lng]));
    if (destLat && destLng) routeCoords.push([destLat, destLng]);
    if (routeCoords.length > 1) {
      L.polyline(routeCoords, { color: '#3b82f6', weight: 3, opacity: 0.6, dashArray: '8,8' }).addTo(map);
    }
  @endif

  // Draw line from origin to driver, driver to destination
  if (driverLat && driverLng && originLat && originLng) {
    L.polyline([[originLat, originLng], [driverLat, driverLng]], { color: '#22c55e', weight: 2, opacity: 0.5 }).addTo(map);
  }
  if (driverLat && driverLng && destLat && destLng) {
    L.polyline([[driverLat, driverLng], [destLat, destLng]], { color: '#ef4444', weight: 2, opacity: 0.5, dashArray: '6,6' }).addTo(map);
  }

  // Fit map to show all points
  const allPoints = [];
  if (originLat && originLng) allPoints.push([originLat, originLng]);
  if (driverLat && driverLng) allPoints.push([driverLat, driverLng]);
  if (destLat && destLng) allPoints.push([destLat, destLng]);
  if (allPoints.length > 1) {
    map.fitBounds(allPoints, { padding: [40, 40] });
  }

  // ── Live polling every 10 seconds ──
  let poller = null;
  if (typeof startPolling !== 'undefined') {
    poller = startPolling('/track/{{ $delivery->id }}/live-status', function(data) {
      // Update progress bar
      const pb = document.getElementById('progressBar');
      if (pb) pb.style.width = data.progress_percentage + '%';

      // Update ETA
      if (data.estimated_arrival_at) {
        const eta = new Date(data.estimated_arrival_at);
        const etaText = document.getElementById('etaText');
        if (etaText) etaText.textContent = eta.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
      }

      // Update distance
      const distText = document.getElementById('distText');
      if (distText && data.distance_remaining_km) distText.textContent = data.distance_remaining_km + ' km';

      // Update overlay status
      const statusText = document.getElementById('overlayStatusText');
      if (statusText && data.proximity_label) statusText.textContent = data.proximity_label;

      // Update driver marker
      if (data.driver_lat && data.driver_lng) {
        const newLatLng = L.latLng(data.driver_lat, data.driver_lng);
        if (driverMarker) {
          driverMarker.setLatLng(newLatLng);
        } else {
          driverMarker = L.marker(newLatLng, { icon: driverIcon }).addTo(map)
            .bindPopup('<strong>Driver Location</strong><br>' + (data.distance_remaining_km || '?') + 'km remaining');
        }
      }

      // Reload page on status change
      if (data.status !== '{{ $delivery->status }}') {
        window.location.reload();
      }
    }, 10000);
  }

  // ── WebSocket live updates (delivery.status_updated / driver.location_updated) ──
  window.updateDriverMarker = function(lat, lng) {
    if (!lat || !lng) return;
    const newLatLng = L.latLng(lat, lng);
    if (driverMarker) {
      driverMarker.setLatLng(newLatLng);
    } else {
      driverMarker = L.marker(newLatLng, { icon: driverIcon }).addTo(map)
        .bindPopup('<strong>Driver Location</strong><br>' + (window.lastDistance || '?') + 'km remaining');
    }
  };

  window.updateTrackingMap = function(data) {
    const trackingNumber = @json($delivery->tracking_number);
    if (data.tracking_number && data.tracking_number !== trackingNumber) return;

    if (data.driver_lat && data.driver_lng) {
      window.updateDriverMarker(data.driver_lat, data.driver_lng);
    }

    if (data.distance_remaining_km != null) {
      window.lastDistance = data.distance_remaining_km;
      const distText = document.getElementById('distText');
      if (distText) distText.textContent = data.distance_remaining_km + ' km';
      const distRemain = document.getElementById('distance-remaining');
      if (distRemain) distRemain.textContent = data.distance_remaining_km + ' km remaining';
    }

    if (data.new_status && data.new_status !== '{{ $delivery->status }}') {
      window.location.reload();
    }
  };
  @endif
});

function refreshLive() {
  window.location.reload();
}
</script>
@endif
@endsection
