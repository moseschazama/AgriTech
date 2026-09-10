@extends('layouts.app')
@section('title', 'Delivery Tracking — AgriTech Pro')
@section('extra_css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"/>
<link rel="stylesheet" href="{{ asset('css/home.css') }}"/>
<style>
.delivery-hero{--hero-glow-1:rgba(59,130,246,0.07);--hero-glow-2:rgba(59,130,246,0.04);--hero-orb:rgba(59,130,246,0.06);--hero-badge-bg:#eff6ff;--hero-badge-fg:#1d4ed8;--hero-badge-border:#bfdbfe;--hero-accent-color:#60a5fa;--hero-overlay-start:rgba(8,18,38,0.78);--hero-overlay-mid:rgba(10,22,45,0.58);--hero-overlay-end:rgba(5,15,35,0.72);--hero-overlay-accent:rgba(96,165,250,0.15);}
.delivery-hero.page-hero-image{background-image:url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=1920&q=80');}
.delivery-hero.page-hero-image .page-hero-badge{background:rgba(96,165,250,0.2);border-color:rgba(96,165,250,0.35);color:#93c5fd;}
.delivery-hero.page-hero-image .page-hero-title .accent{color:#60a5fa;}
.delivery-hero.page-hero-image .page-hero-pill i,.delivery-hero.page-hero-image .page-hero-stat i{color:#60a5fa;}
.track-search{display:flex;gap:0;max-width:520px;margin:0 auto;}
.track-search input{flex:1;padding:12px 16px;border:none;border-radius:var(--radius-md) 0 0 var(--radius-md);font-size:.88rem;background:rgba(255,255,255,.95);color:var(--text);}
.track-search button{padding:12px 18px;background:var(--primary);color:#fff;border:none;border-radius:0 var(--radius-md) var(--radius-md) 0;cursor:pointer;font-size:.88rem;}
.delivery-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:24px;margin-bottom:20px;transition:box-shadow .2s;}
.delivery-card:hover{box-shadow:0 4px 20px rgba(0,0,0,.06);}
.dc-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:10px;}
.dc-track-num{font-size:.82rem;font-weight:700;color:var(--primary);background:var(--green-50);border:1px solid var(--green-200);padding:3px 10px;border-radius:var(--radius-full);}
.dc-map{border-radius:var(--radius-lg);height:260px;overflow:hidden;border:1px solid var(--border);margin-bottom:16px;position:relative;}
.dc-map .leaflet-container{height:100%;}
.dc-map-overlay{position:absolute;top:8px;left:8px;z-index:1000;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);padding:8px 12px;box-shadow:0 2px 8px rgba(0,0,0,.12);font-size:.78rem;font-weight:600;display:flex;align-items:center;gap:8px;}
.dc-map-overlay .dot{width:8px;height:8px;border-radius:50%;flex-shrink:0;}
.dc-progress{background:var(--bg-2);border-radius:20px;height:7px;margin-bottom:16px;overflow:hidden;}
.dc-progress-fill{height:7px;background:var(--primary);border-radius:20px;transition:width .8s ease;}
.dc-timeline{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;position:relative;overflow-x:auto;padding-bottom:4px;gap:2px;}
.dc-timeline::before{content:'';position:absolute;top:16px;left:6%;right:6%;height:2px;background:var(--border);z-index:0;}
.dc-step{display:flex;flex-direction:column;align-items:center;gap:6px;z-index:1;flex:1;min-width:56px;}
.dc-dot{width:34px;height:34px;border-radius:50%;border:2px solid var(--border);background:var(--bg-card);display:flex;align-items:center;justify-content:center;color:var(--text-muted);font-size:.7rem;transition:all .3s;position:relative;z-index:2;}
.dc-dot.done{background:var(--primary);border-color:var(--primary);color:#fff;}
.dc-dot.active{background:var(--primary);border-color:var(--primary);color:#fff;box-shadow:0 0 0 5px rgba(22,163,74,.2);animation:pulse 2s infinite;}
.dc-step-label{font-size:.65rem;font-weight:600;color:var(--text-muted);text-align:center;line-height:1.2;}
.dc-step-label.done,.dc-step-label.active{color:var(--primary);}
.dc-proximity{border-radius:var(--radius-md);padding:14px;margin-bottom:14px;display:flex;align-items:center;gap:12px;}
.dc-prox-icon{font-size:1.6rem;flex-shrink:0;}
.dc-prox-text{flex:1;}
.dc-prox-text h4{font-weight:700;font-size:.88rem;margin-bottom:1px;}
.dc-prox-text p{font-size:.78rem;opacity:.8;}
.dc-info{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.dc-info-block{background:var(--bg-2);border-radius:var(--radius-md);padding:14px;}
.dc-info-block-title{font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;font-size:.68rem;margin-bottom:10px;}
.dc-info-row{display:flex;justify-content:space-between;margin-bottom:8px;gap:6px;font-size:.82rem;}
.dc-info-row:last-child{margin-bottom:0;}
.dc-info-key{color:var(--text-muted);}
.dc-info-val{font-weight:600;color:var(--text);text-align:right;}
.dc-history-table{width:100%;border-collapse:collapse;}
.dc-history-table th{font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);padding:10px 14px;text-align:left;border-bottom:2px solid var(--border);font-size:.72rem;}
.dc-history-table td{padding:12px 14px;border-bottom:1px solid var(--border);color:var(--text);font-size:.85rem;}
.dc-history-table tr:hover td{background:var(--bg-2);}
.star-input{display:flex;gap:4px;flex-direction:row-reverse;justify-content:flex-end;}
.star-input input{display:none;}
.star-input label{cursor:pointer;font-size:1.3rem;color:var(--border);transition:color .15s;}
.star-input input:checked~label,.star-input label:hover,.star-input label:hover~label{color:#f59e0b;}
@keyframes pulse{0%,100%{opacity:1;}50%{opacity:.6;}}
@media(max-width:768px){.dc-info{grid-template-columns:1fr;}.delivery-card{padding:16px 12px;}.dc-map{height:220px;}.dc-dot{width:28px;height:28px;font-size:.6rem;}.dc-step-label{font-size:.55rem;}.dc-history-table{min-width:480px;}}
@media(max-width:480px){.track-search{flex-direction:column;gap:8px;}.track-search input,.track-search button{border-radius:var(--radius-md);width:100%;}}
</style>
@endsection

@section('content')

{{-- Hero --}}
<section class="page-hero delivery-hero page-hero-image">
  <div class="container">
    <div class="page-hero-content">
      <span class="page-hero-badge">
        <i class="fas fa-truck"></i> Delivery Tracking
      </span>
      <h1 class="page-hero-title">Track Your Deliveries in <span class="accent">Real-Time</span></h1>
      <p class="page-hero-desc">Live GPS tracking from seller to your door. Get SMS alerts as your package approaches.</p>
      <div class="page-hero-cta">
        <form class="track-search" onsubmit="trackOrder(event)">
          <input type="text" id="trackInput" placeholder="Enter tracking number e.g. TRK-20250601-001" value="{{ request('tracking') }}"/>
          <button type="submit"><i class="fas fa-search"></i> Track</button>
        </form>
        <p style="text-align:center;color:var(--text-muted);font-size:.72rem;margin-top:8px;">Find your tracking number in your order confirmation SMS</p>
      </div>
      <div class="page-hero-pills">
        @foreach(['fas fa-map-marker-alt'=>'Live GPS','fas fa-bell'=>'Proximity Alerts','fas fa-sms'=>'SMS Updates','fas fa-star'=>'Rate'] as $icon=>$label)
          <span class="page-hero-pill"><i class="{{ $icon }}"></i> {{ $label }}</span>
        @endforeach
      </div>
    </div>
  </div>
</section>

<div style="background:var(--bg-2);min-height:60vh;">
  <div class="container" style="padding-top:24px;padding-bottom:40px;">

    @auth
    {{-- Active Deliveries --}}
    @if(isset($activeDeliveries) && $activeDeliveries->count() > 0)
      <h2 style="font-size:1.1rem;font-weight:800;margin-bottom:16px;"><i class="fas fa-box" style="color:var(--primary);"></i> Active Deliveries</h2>

      @foreach($activeDeliveries as $delivery)
        @php
          $steps=[
            'pending'=>['Order Placed','fas fa-shopping-cart'],
            'assigned'=>['Assigned','fas fa-user-tie'],
            'collected'=>['Picked Up','fas fa-box'],
            'in_transit'=>['In Transit','fas fa-truck'],
            'near_destination'=>['Almost There','fas fa-map-marker-alt'],
            'delivered'=>['Delivered','fas fa-check-circle'],
          ];
          $stepKeys=array_keys($steps);
          $currentIdx=array_search($delivery->status,$stepKeys);
          if($currentIdx===false) $currentIdx=0;
          $pct=$delivery->progressPercentage();
          $hasCoords=$delivery->driver_current_lat && $delivery->driver_current_lng;
          $destLat=$delivery->destination_lat ?? $delivery->order?->delivery_lat;
          $destLng=$delivery->destination_lng ?? $delivery->order?->delivery_lng;
        @endphp

        <div class="delivery-card">
          {{-- Header --}}
          <div class="dc-header">
            <div>
              <div style="font-weight:700;font-size:1rem;margin-bottom:4px;">Order #{{ $delivery->order->order_number }}</div>
              <span class="dc-track-num code">{{ $delivery->tracking_number }}</span>
              @php $sticker=$delivery->trackingStickers->first(); @endphp
              @if($sticker)
                <span class="code" style="background:#f5f3ff;color:#6d28d9;border:1px solid #d8b4fe;padding:2px 8px;border-radius:var(--radius-full);font-weight:600;font-size:.72rem;margin-left:6px;">
                  <i class="fas fa-qrcode"></i> {{ $sticker->sticker_code }}
                </span>
              @endif
            </div>
            <div style="text-align:right;">
              @php $statusColors=['pending'=>'badge-gray','assigned'=>'badge-sky','collected'=>'badge-earth','in_transit'=>'badge-sky','near_destination'=>'badge-earth','delivered'=>'badge-green','failed'=>'badge-coral']; @endphp
              <span class="badge {{ $statusColors[$delivery->status]??'badge-gray' }}" style="font-size:.72rem;padding:4px 10px;margin-bottom:4px;display:inline-block;">
                {{ ucwords(str_replace('_',' ',$delivery->status)) }}
              </span>
              @if($delivery->estimated_arrival_at)
                <div style="color:var(--text-muted);font-size:.75rem;">ETA: {{ $delivery->estimated_arrival_at->format('g:i A, M j') }}</div>
              @endif
              @if($delivery->distance_remaining_km)
                <div style="color:var(--primary);font-weight:600;font-size:.78rem;">{{ $delivery->distance_remaining_km }} km left</div>
              @endif
            </div>
          </div>

          {{-- Proximity Alert --}}
          @if($delivery->distance_remaining_km !== null && $delivery->distance_remaining_km <= 10 && !in_array($delivery->status, ['delivered','failed']))
            @php
              $pLevel = match(true) {
                $delivery->distance_remaining_km <= 0.5 => ['background:#dcfce7;border:2px solid #86efac;','fa-check-circle','Driver has arrived!','Please meet them to collect your package.'],
                $delivery->distance_remaining_km <= 2   => ['background:#f5f3ff;border:2px solid #d8b4fe;','fa-bell','Arriving very soon!','Your package is just '.$delivery->distance_remaining_km.'km away. Be ready!'],
                $delivery->distance_remaining_km <= 5   => ['background:#eff6ff;border:2px solid #bfdbfe;','fa-truck','Approaching your area','Driver is '.$delivery->distance_remaining_km.'km from your destination.'],
                default                                 => ['background:#fefce8;border:2px solid #fde68a;','fa-info-circle','Getting closer','Driver is '.$delivery->distance_remaining_km.'km away.'],
              };
            @endphp
            <div class="dc-proximity" style="{{ explode(';',$pLevel[0])[0] }};{{ explode(';',$pLevel[0])[1] }}">
              <div class="dc-prox-icon"><i class="fas {{ $pLevel[1] }}" style="color:var(--primary);"></i></div>
              <div class="dc-prox-text">
                <h4>{{ $pLevel[2] }}</h4>
                <p>{{ $pLevel[3] }} {{ $delivery->order?->delivery_town ? '→ '.$delivery->order->delivery_town.', ' : '' }}{{ $delivery->destination_district }}</p>
              </div>
            </div>
          @endif

          {{-- Progress --}}
          <div class="dc-progress">
            <div class="dc-progress-fill" style="width:{{ $pct }}%;" id="progress-{{ $delivery->id }}"></div>
          </div>

          {{-- Timeline --}}
          <div class="dc-timeline">
            @foreach($steps as $sKey=>[$sLabel,$sIcon])
              @php $sIdx=array_search($sKey,$stepKeys); $isDone=$sIdx<$currentIdx; $isActive=$sIdx===$currentIdx; @endphp
              <div class="dc-step">
                <div class="dc-dot {{ $isDone?'done':($isActive?'active':'') }}"><i class="{{ $sIcon }}"></i></div>
                <div class="dc-step-label {{ $isDone?'done':($isActive?'active':'') }}">{{ $sLabel }}</div>
              </div>
            @endforeach
          </div>

          {{-- Map --}}
          <div class="dc-map" id="map-{{ $delivery->id }}">
            <div id="mapInner-{{ $delivery->id }}" style="height:100%;"></div>
            <div class="dc-map-overlay" id="mapOverlay-{{ $delivery->id }}">
              <div class="dot" style="background:{{ match($delivery->status){'pending'=>'#94a3b8','assigned'=>'#60a5fa','collected'=>'#fb923c','in_transit'=>'#60a5fa','near_destination'=>'#8b5cf6','delivered'=>'#22c55e','failed'=>'#ef4444',default=>'#94a3b8'} }};"></div>
              <div>
                <div style="font-weight:700;">{{ $hasCoords ? ($delivery->distance_remaining_km ?? '?').' km remaining' : ($delivery->status==='pending'?'Awaiting driver':'Driver assigned — pickup pending') }}</div>
                <div style="font-size:.7rem;color:var(--text-muted);">From {{ $delivery->origin_district }} → {{ $delivery->destination_district }}</div>
              </div>
            </div>
          </div>

          {{-- Info --}}
          <div class="dc-info">
            {{-- Order --}}
            <div class="dc-info-block">
              <div class="dc-info-block-title"><i class="fas fa-box" style="color:var(--primary);"></i> Order Items</div>
              @foreach($delivery->order->items as $item)
                <div class="dc-info-row">
                  <span class="dc-info-key">{{ $item->quantity }}× {{ $item->product_name }}</span>
                  <span class="dc-info-val">{{ $delivery->order->currency }} {{ number_format($item->total_price) }}</span>
                </div>
              @endforeach
              <div class="dc-info-row" style="border-top:1px solid var(--border);padding-top:8px;margin-top:4px;">
                <span class="dc-info-key">Delivery fee</span>
                <span class="dc-info-val">{{ $delivery->order->currency }} {{ number_format($delivery->order->delivery_fee) }}</span>
              </div>
              <div class="dc-info-row">
                <span class="dc-info-key" style="font-weight:700;">Total</span>
                <span class="dc-info-val" style="color:var(--primary);">{{ $delivery->order->currency }} {{ number_format($delivery->order->total) }}</span>
              </div>
              <div class="dc-info-row">
                <span class="dc-info-key">Payment</span>
                <span class="dc-info-val"><span style="font-size:.65rem;background:{{ $delivery->order->payment_status==='paid'?'var(--green-50)':'var(--bg-2)' }};border:1px solid {{ $delivery->order->payment_status==='paid'?'var(--green-200)':'var(--border)' }};padding:2px 8px;border-radius:var(--radius-full);color:{{ $delivery->order->payment_status==='paid'?'var(--green-700)':'var(--text-muted)' }};font-weight:600;">{{ ucfirst($delivery->order->payment_status) }}</span></span>
              </div>
            </div>

            {{-- Driver --}}
            <div class="dc-info-block">
              <div class="dc-info-block-title"><i class="fas fa-user" style="color:var(--primary);"></i> Driver</div>
              @if($delivery->driver_name)
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                  <div style="width:36px;height:36px;border-radius:50%;background:#1e3a5f;color:#fff;display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;flex-shrink:0;">
                    {{ strtoupper(substr($delivery->driver_name,0,1)) }}{{ strtoupper(substr(str_word_count($delivery->driver_name,1),-1)[0]??'D') }}
                  </div>
                  <div>
                    <div style="font-weight:700;font-size:.85rem;">{{ $delivery->driver_name }}</div>
                    <div style="color:var(--text-muted);font-size:.72rem;">{{ $delivery->driver_vehicle_type ? $delivery->driver_vehicle_type.' · ' : '' }}{{ $delivery->driver_vehicle_plate ?? '' }}</div>
                  </div>
                </div>
                @if($delivery->driver_phone)
                  <div style="display:flex;gap:6px;">
                    <a href="tel:{{ $delivery->driver_phone }}" style="flex:1;display:flex;align-items:center;justify-content:center;gap:4px;padding:7px;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.75rem;font-weight:600;color:var(--text);text-decoration:none;background:var(--bg-card);"><i class="fas fa-phone" style="color:var(--primary);"></i> Call</a>
                    <a href="sms:{{ $delivery->driver_phone }}" style="flex:1;display:flex;align-items:center;justify-content:center;gap:4px;padding:7px;border:1px solid var(--border);border-radius:var(--radius-md);font-size:.75rem;font-weight:600;color:var(--text);text-decoration:none;background:var(--bg-card);"><i class="fas fa-sms" style="color:var(--primary);"></i> SMS</a>
                  </div>
                @endif
              @else
                <div style="text-align:center;padding:16px;color:var(--text-muted);">
                  <i class="fas fa-user-clock" style="font-size:1.5rem;margin-bottom:6px;display:block;opacity:.3;"></i>
                  <div style="font-size:.82rem;">Driver being assigned...</div>
                </div>
              @endif

              {{-- Status log --}}
              @if($delivery->statusLogs->count()>0)
                <div style="margin-top:12px;padding-top:10px;border-top:1px solid var(--border);">
                  <div style="font-size:.68rem;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Recent Updates</div>
                  @foreach($delivery->statusLogs->take(3) as $log)
                    <div style="display:flex;gap:8px;padding:5px 0;border-bottom:1px solid var(--border);font-size:.78rem;">
                      <span style="color:var(--primary);font-weight:600;white-space:nowrap;">{{ $log->created_at->format('H:i') }}</span>
                      <span style="color:var(--text-muted);">{{ $log->note??ucwords(str_replace('_',' ',$log->status)) }}</span>
                    </div>
                  @endforeach
                </div>
              @endif
            </div>
          </div>

          {{-- Rate --}}
          @if($delivery->status==='delivered' && !$delivery->buyer_rating)
            <div style="background:var(--green-50);border:1.5px solid var(--green-200);border-radius:var(--radius-md);padding:16px;margin-top:16px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
              <div>
                <div style="font-weight:700;color:var(--green-700);margin-bottom:2px;">Order Delivered! How was your experience?</div>
                <div style="color:var(--text-muted);font-size:.75rem;">Rate the driver to help other farmers</div>
              </div>
              <form method="POST" action="{{ route('delivery.rate',$delivery) }}" style="display:flex;align-items:center;gap:8px;">
                @csrf
                <div class="star-input">
                  @for($i=5;$i>=1;$i--)
                    <input type="radio" name="rating" id="star-{{ $delivery->id }}-{{ $i }}" value="{{ $i }}"/>
                    <label for="star-{{ $delivery->id }}-{{ $i }}">★</label>
                  @endfor
                </div>
                <button type="submit" style="padding:6px 14px;background:var(--primary);color:#fff;border:none;border-radius:var(--radius-md);font-weight:600;font-size:.78rem;cursor:pointer;"><i class="fas fa-check"></i> Submit</button>
              </form>
            </div>
          @elseif($delivery->buyer_rating)
            <div style="background:var(--green-50);border:1.5px solid var(--green-200);border-radius:var(--radius-md);padding:12px 16px;margin-top:16px;">
              <span style="color:var(--green-700);font-weight:600;font-size:.85rem;"><i class="fas fa-star" style="color:#f59e0b;"></i> You rated this delivery {{ $delivery->buyer_rating }}/5. Thank you!</span>
            </div>
          @endif

        </div>
      @endforeach
    @else
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:50px;text-align:center;margin-bottom:24px;">
        <i class="fas fa-truck" style="font-size:1.4rem;margin-bottom:12px;display:block;color:var(--text-muted);opacity:.25;"></i>
        <h3 style="font-weight:700;margin-bottom:6px;">No active deliveries</h3>
        <p style="color:var(--text-muted);margin-bottom:18px;font-size:.88rem;">Order something from the marketplace to start tracking!</p>
        <a href="{{ route('marketplace') }}" style="display:inline-flex;align-items:center;gap:6px;padding:10px 24px;background:var(--primary);color:#fff;border-radius:var(--radius-md);font-weight:600;text-decoration:none;font-size:.88rem;"><i class="fas fa-store"></i> Browse Marketplace</a>
      </div>
    @endif

    {{-- History --}}
    @if(isset($deliveryHistory) && $deliveryHistory->count() > 0)
      <h2 style="font-size:1.1rem;font-weight:800;margin-bottom:14px;"><i class="fas fa-history" style="color:var(--primary);"></i> Delivery History</h2>
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;">
        <div style="overflow-x:auto;">
          <table class="dc-history-table">
            <thead><tr><th>Tracking #</th><th>Order</th><th>Items</th><th>Total</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody>
              @foreach($deliveryHistory as $del)
                <tr>
                  <td><span class="code" style="color:var(--primary);">{{ $del->tracking_number }}</span></td>
                  <td style="font-weight:600;">#{{ $del->order->order_number }}</td>
                  <td>{{ $del->order->items->count() }} item(s)</td>
                  <td style="font-weight:700;color:var(--primary);">{{ $del->order->currency }} {{ number_format($del->order->total) }}</td>
                  <td><span class="badge {{ $del->status==='delivered'?'badge-green':'badge-coral' }}" style="font-size:.72rem;">{{ ucfirst($del->status) }}</span></td>
                  <td style="color:var(--text-muted);font-size:.82rem;">{{ $del->delivered_at?->format('M j, Y')??$del->created_at->format('M j, Y') }}</td>
                  <td>
                    <a href="{{ route('delivery.track',$del->tracking_number) }}" style="color:var(--primary);font-weight:600;text-decoration:underline;font-size:.78rem;">Track</a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @if($deliveryHistory->hasPages())
          <div style="padding:14px 18px;border-top:1px solid var(--border);">{{ $deliveryHistory->links() }}</div>
        @endif
      </div>
    @endif
    @endauth

    @guest
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:44px;text-align:center;">
        <i class="fas fa-truck" style="font-size:2.5rem;margin-bottom:14px;display:block;color:var(--primary);"></i>
        <h3 style="font-weight:700;margin-bottom:6px;">Track Your Agri Deliveries</h3>
        <p style="color:var(--text-muted);margin-bottom:18px;max-width:400px;margin-left:auto;margin-right:auto;font-size:.88rem;">Sign in to see your active deliveries, live maps, and proximity alerts.</p>
        <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
          <a href="{{ route('login') }}" style="padding:10px 24px;background:var(--primary);color:#fff;border-radius:var(--radius-md);font-weight:600;text-decoration:none;font-size:.88rem;"><i class="fas fa-sign-in-alt"></i> Sign In</a>
          <a href="{{ route('register') }}" style="padding:10px 24px;border:1px solid var(--border);border-radius:var(--radius-md);font-weight:600;color:var(--text);text-decoration:none;background:var(--bg-card);font-size:.88rem;">Create Account</a>
        </div>
      </div>
    @endguest

  </div>
</div>

@include('partials.footer')
@endsection

@section('extra_js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
function trackOrder(e) {
  e.preventDefault();
  const num = document.getElementById('trackInput').value.trim();
  if (!num) { showToast('Please enter a tracking number', 'error'); return; }
  window.location.href = '/track/' + num;
}

// ── Init Leaflet maps for each active delivery ──
document.addEventListener('DOMContentLoaded', function() {
  @if(isset($activeDeliveries))
  @foreach($activeDeliveries as $delivery)
    @if(!in_array($delivery->status, ['delivered','failed']))
    (function() {
      const destLat = {{ $delivery->destination_lat ?? $delivery->order?->delivery_lat ?? 'null' }};
      const destLng = {{ $delivery->destination_lng ?? $delivery->order?->delivery_lng ?? 'null' }};
      const driverLat = {{ $delivery->driver_current_lat ?? 'null' }};
      const driverLng = {{ $delivery->driver_current_lng ?? 'null' }};
      const originLat = {{ $delivery->origin_lat ?? 'null' }};
      const originLng = {{ $delivery->origin_lng ?? 'null' }};

      if (!destLat) return;

      const mapEl = document.getElementById('mapInner-{{ $delivery->id }}');
      if (!mapEl) return;

      const center = driverLat ? [driverLat, driverLng] : [destLat, destLng];
      const map = L.map(mapEl, { center, zoom: driverLat ? 11 : 8, zoomControl: false, attributionControl: false });
      L.control.zoom({ position: 'topright' }).addTo(map);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18 }).addTo(map);

      const destIcon = L.divIcon({ html:'<div style="width:24px;height:24px;border-radius:50%;background:#ef4444;border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.3);"></div>', className:'', iconSize:[24,24], iconAnchor:[12,12] });
      L.marker([destLat, destLng], {icon:destIcon}).addTo(map).bindPopup('<strong>Destination</strong>');

      if (originLat && originLng) {
        const originIcon = L.divIcon({ html:'<div style="width:20px;height:20px;border-radius:50%;background:#22c55e;border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.3);"></div>', className:'', iconSize:[20,20], iconAnchor:[10,10] });
        L.marker([originLat, originLng], {icon:originIcon}).addTo(map).bindPopup('<strong>Origin</strong>');
      }

      if (driverLat && driverLng) {
        const truckIcon = L.divIcon({ html:'<div style="width:30px;height:30px;border-radius:50%;background:#3b82f6;border:2px solid #fff;box-shadow:0 2px 8px rgba(59,130,246,.4);display:flex;align-items:center;justify-content:center;"><i class="fas fa-truck" style="color:#fff;font-size:11px;"></i></div>', className:'', iconSize:[30,30], iconAnchor:[15,15] });
        L.marker([driverLat, driverLng], {icon:truckIcon}).addTo(map).bindPopup('<strong>Driver</strong><br>' + ({{ $delivery->distance_remaining_km ?? 'null' }} || '?') + 'km remaining');
        if (destLat && destLng) L.polyline([[driverLat,driverLng],[destLat,destLng]],{color:'#ef4444',weight:2,dashArray:'6,6',opacity:.5}).addTo(map);
      }

      const pts = [];
      if (originLat && originLng) pts.push([originLat,originLng]);
      if (driverLat && driverLng) pts.push([driverLat,driverLng]);
      pts.push([destLat,destLng]);
      if (pts.length > 1) map.fitBounds(pts, {padding:[30,30]});

      // Poll every 10s
      startPolling('/track/{{ $delivery->id }}/live-status', function(data) {
        const pb = document.getElementById('progress-{{ $delivery->id }}');
        if (pb) pb.style.width = data.progress_percentage + '%';
      }, 10000);
    })();
    @endif
  @endforeach
  @endif
});
</script>
@endsection
