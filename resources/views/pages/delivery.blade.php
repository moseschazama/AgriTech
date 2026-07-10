@extends('layouts.app')
@section('title', 'Delivery Tracking — AgriTech Pro')
@section('extra_css')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"/>
<link rel="stylesheet" href="{{ asset('css/home.css') }}"/>
<style>
.delivery-hero{background:linear-gradient(135deg,#0f172a,#1e3a5f,#0f172a);padding:48px 0 36px;color:#fff;}
.track-search{display:flex;gap:0;max-width:520px;margin:0 auto 16px;}
.track-search input{flex:1;padding:13px 18px;border:none;border-radius:var(--radius-md) 0 0 var(--radius-md);font-family:var(--font-body);font-size:.9rem;background:rgba(255,255,255,.95);color:var(--text);}
.track-search button{padding:13px 20px;background:#3b82f6;color:#fff;border:none;border-radius:0 var(--radius-md) var(--radius-md) 0;cursor:pointer;font-size:.9rem;}
.delivery-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:26px;margin-bottom:22px;}
.delivery-card-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px;}
.tracking-number{font-family:var(--font-mono);font-size:.88rem;font-weight:700;color:var(--primary);background:var(--green-50);border:1px solid var(--green-200);padding:4px 12px;border-radius:var(--radius-full);}
.status-timeline{display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;position:relative;overflow-x:auto;padding-bottom:4px;}
.status-timeline::before{content:'';position:absolute;top:20px;left:5%;right:5%;height:3px;background:var(--border);z-index:0;}
.timeline-step{display:flex;flex-direction:column;align-items:center;gap:8px;z-index:1;flex:1;min-width:70px;}
.timeline-dot{width:40px;height:40px;border-radius:50%;border:3px solid var(--border);background:var(--bg-card);display:flex;align-items:center;justify-content:center;font-size:.85rem;color:var(--text-muted);transition:all .3s;position:relative;z-index:2;}
.timeline-dot.done{background:var(--primary);border-color:var(--primary);color:#fff;}
.timeline-dot.active{background:var(--primary);border-color:var(--primary);color:#fff;box-shadow:0 0 0 6px rgba(22,163,74,.2);animation:pulse 2s infinite;}
.timeline-label{font-size:.68rem;font-weight:600;color:var(--text-muted);text-align:center;line-height:1.3;}
.timeline-label.done,.timeline-label.active{color:var(--primary);}
.progress-track{background:var(--bg-2);border-radius:20px;height:8px;margin-bottom:22px;overflow:hidden;position:relative;}
.progress-fill{height:8px;background:linear-gradient(90deg,var(--primary),#4ade80);border-radius:20px;transition:width .8s ease;}
.map-placeholder{background:linear-gradient(135deg,#e8f5e9,#c8e6c9);border-radius:var(--radius-lg);height:220px;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:12px;margin-bottom:20px;position:relative;overflow:hidden;border:1px solid var(--green-200);}
.map-pin{font-size:2.5rem;animation:bounce 2s infinite;}
@keyframes bounce{0%,100%{transform:translateY(0)}50%{transform:translateY(-12px)}}
.delivery-info-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
.info-block{background:var(--bg-2);border-radius:var(--radius-md);padding:18px;}
.info-block-title{font-size:.78rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px;}
.info-row{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;gap:8px;}
.info-row:last-child{margin-bottom:0;}
.info-key{font-size:.8rem;color:var(--text-muted);}
.info-val{font-size:.82rem;font-weight:600;color:var(--text);text-align:right;}
.sms-log{background:var(--bg-2);border-radius:var(--radius-md);padding:14px;margin-top:14px;}
.sms-entry{font-size:.75rem;color:var(--text-muted);padding:6px 0;border-bottom:1px solid var(--border);display:flex;gap:10px;}
.sms-entry:last-child{border-bottom:none;}
.sms-time{font-family:var(--font-mono);color:var(--primary);font-weight:600;white-space:nowrap;}
.history-table{width:100%;border-collapse:collapse;}
.history-table th{font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);padding:10px 14px;text-align:left;border-bottom:2px solid var(--border);}
.history-table td{padding:12px 14px;font-size:.83rem;border-bottom:1px solid var(--border);color:var(--text);}
.history-table tr:hover td{background:var(--bg-2);}
.star-rating-input{display:flex;gap:6px;flex-direction:row-reverse;justify-content:flex-end;}
.star-rating-input input{display:none;}
.star-rating-input label{cursor:pointer;font-size:1.4rem;color:var(--border);transition:color .15s;}
.star-rating-input input:checked~label,.star-rating-input label:hover,.star-rating-input label:hover~label{color:#f59e0b;}
@media(max-width:768px){.delivery-info-grid{grid-template-columns:1fr;}.status-timeline{gap:0;}.delivery-hero{padding:36px 0 28px;}.delivery-card{padding:18px 14px;}.timeline-step{min-width:50px;}.timeline-dot{width:32px;height:32px;font-size:.75rem;}.timeline-label{font-size:.6rem;}.history-table{min-width:500px;}}
@media(max-width:480px){.track-search{flex-direction:column;gap:8px;}.track-search input,.track-search button{border-radius:var(--radius-md);width:100%;}.delivery-hero{padding:28px 0 22px;}.delivery-card{padding:14px 12px;}.delivery-info-grid{gap:14px;}.info-block{padding:14px;}}
@media(max-width:360px){.timeline-step{min-width:40px;}.timeline-dot{width:26px;height:26px;font-size:.65rem;}}
</style>
@endsection

@section('content')

{{-- Hero --}}
<section class="delivery-hero">
  <div class="container">
    <div style="text-align:center;margin-bottom:28px;">
      <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(59,130,246,.15);border:1px solid rgba(59,130,246,.3);border-radius:var(--radius-full);padding:5px 14px;font-size:.78rem;font-weight:700;color:#93c5fd;margin-bottom:14px;">
        <i class="fas fa-truck"></i> Delivery Tracking
      </span>
      <h1 style="font-family:var(--font-display);font-size:clamp(1.8rem,4vw,2.8rem);font-weight:800;margin-bottom:10px;">Track Your Deliveries in <span style="color:#60a5fa;">Real-Time</span></h1>
      <p style="opacity:.8;max-width:480px;margin:0 auto 24px;line-height:1.7;">Live GPS tracking from seller to your door. Get SMS updates at every step.</p>
    </div>
    <form class="track-search" onsubmit="trackOrder(event)">
      <input type="text" id="trackInput" placeholder="Enter tracking number e.g. TRK-20240601-001" value="{{ request('tracking') }}"/>
      <button type="submit"><i class="fas fa-search"></i> Track</button>
    </form>
    <p style="text-align:center;font-size:.78rem;opacity:.6;">Find your tracking number in your order confirmation SMS or email</p>
    <div style="display:flex;gap:24px;justify-content:center;flex-wrap:wrap;margin-top:20px;">
      @foreach(['fas fa-map-marker-alt'=>'Live GPS','fas fa-sms'=>'SMS Updates','fas fa-star'=>'Rate Delivery','fas fa-undo'=>'Easy Returns'] as $icon=>$label)
        <span style="display:flex;align-items:center;gap:6px;font-size:.78rem;opacity:.8;">
          <i class="{{ $icon }}" style="color:#60a5fa;"></i> {{ $label }}
        </span>
      @endforeach
    </div>
  </div>
</section>

<div class="section" style="background:var(--bg-2);">
  <div class="container">

    @auth
    {{-- Active Deliveries --}}
    @if(isset($activeDeliveries)&&$activeDeliveries->count()>0)
      <h2 style="font-family:var(--font-display);font-size:1.25rem;font-weight:800;color:var(--text);margin-bottom:20px;">📦 Active Deliveries</h2>
      @foreach($activeDeliveries as $delivery)
        @php
          $steps=[
            'pending'          =>['Order Placed',     'fas fa-shopping-cart'],
            'assigned'         =>['Driver Assigned',  'fas fa-user'],
            'collected'        =>['Picked Up',        'fas fa-box'],
            'in_transit'       =>['On the Way',       'fas fa-truck'],
            'near_destination' =>['Almost There!',    'fas fa-map-marker-alt'],
            'delivered'        =>['Delivered ✓',      'fas fa-check-circle'],
          ];
          $stepKeys=array_keys($steps);
          $currentIdx=array_search($delivery->status,$stepKeys);
          if($currentIdx===false) $currentIdx=0;
          $pct=$delivery->progressPercentage();
        @endphp
        <div class="delivery-card">
          {{-- Header --}}
          <div class="delivery-card-header">
            <div>
              <div style="font-family:var(--font-display);font-size:1rem;font-weight:800;color:var(--text);margin-bottom:6px;">
                Order #{{ $delivery->order->order_number }}
              </div>
              <span class="tracking-number">{{ $delivery->tracking_number }}</span>
            </div>
            <div style="text-align:right;">
              @php $statusColors=['pending'=>'badge-gray','assigned'=>'badge-sky','collected'=>'badge-earth','in_transit'=>'badge-sky','near_destination'=>'badge-earth','delivered'=>'badge-green','failed'=>'badge-coral']; @endphp
              <span class="badge {{ $statusColors[$delivery->status]??'badge-gray' }}" style="font-size:.78rem;padding:5px 12px;margin-bottom:6px;display:inline-block;">
                {{ ucwords(str_replace('_',' ',$delivery->status)) }}
              </span>
              @if($delivery->estimated_arrival_at)
                <div style="font-size:.75rem;color:var(--text-muted);">ETA: {{ $delivery->estimated_arrival_at->format('g:i A, M j') }}</div>
              @endif
              @if($delivery->distance_remaining_km)
                <div style="font-size:.75rem;color:var(--primary);font-weight:600;">{{ $delivery->distance_remaining_km }} km remaining</div>
              @endif
            </div>
          </div>

          {{-- Progress bar --}}
          <div class="progress-track">
            <div class="progress-fill" style="width:{{ $pct }}%;" id="progress-{{ $delivery->id }}"></div>
          </div>

          {{-- Status timeline --}}
          <div class="status-timeline">
            @foreach($steps as $stepKey=>[$label,$icon])
              @php
                $stepIdx=array_search($stepKey,$stepKeys);
                $isDone=$stepIdx<$currentIdx;
                $isActive=$stepIdx===$currentIdx;
              @endphp
              <div class="timeline-step">
                <div class="timeline-dot {{ $isDone?'done':($isActive?'active':'') }}">
                  <i class="{{ $icon }}"></i>
                </div>
                <div class="timeline-label {{ $isDone?'done':($isActive?'active':'') }}">{{ $label }}</div>
              </div>
            @endforeach
          </div>

          {{-- Map placeholder with real coords if available --}}
          <div class="map-placeholder" id="map-{{ $delivery->id }}">
            @if($delivery->driver_current_lat&&$delivery->driver_current_lng)
              <div class="map-pin">🚚</div>
              <div style="font-size:.85rem;font-weight:600;color:var(--green-700);">Driver is {{ $delivery->distance_remaining_km ?? '?' }} km away</div>
              <div style="font-size:.75rem;color:var(--text-muted);">GPS: {{ number_format($delivery->driver_current_lat,4) }}, {{ number_format($delivery->driver_current_lng,4) }}</div>
              <div style="font-size:.72rem;color:var(--text-muted);">Updated {{ $delivery->driver_location_updated_at?->diffForHumans() }}</div>
            @else
              <div class="map-pin">📍</div>
              <div style="font-size:.85rem;font-weight:600;color:var(--green-700);">
                @if($delivery->status==='pending') Waiting for driver assignment
                @elseif($delivery->status==='assigned') Driver assigned — awaiting pickup
                @else Live tracking will appear when driver is on route
                @endif
              </div>
              <div style="font-size:.75rem;color:var(--text-muted);">
                From: {{ $delivery->origin_district }} → To: {{ $delivery->destination_district }}
              </div>
            @endif
            <div style="position:absolute;bottom:10px;right:10px;" data-delivery="{{ $delivery->id }}">
              <button onclick="refreshTracking({{ $delivery->id }})" class="btn btn-outline btn-sm" style="font-size:.72rem;padding:4px 10px;">
                <i class="fas fa-sync"></i> Refresh
              </button>
            </div>
          </div>

          {{-- Info grid --}}
          <div class="delivery-info-grid">
            {{-- Order details --}}
            <div class="info-block">
              <div class="info-block-title"><i class="fas fa-box" style="color:var(--primary);"></i> Order Items</div>
              @foreach($delivery->order->items as $item)
                <div class="info-row">
                  <span class="info-key">{{ $item->quantity }}× {{ $item->product_name }}</span>
                  <span class="info-val">{{ $delivery->order->currency }} {{ number_format($item->total_price) }}</span>
                </div>
              @endforeach
              <div class="info-row" style="border-top:1px solid var(--border);padding-top:8px;margin-top:4px;">
                <span class="info-key">Delivery fee</span>
                <span class="info-val">{{ $delivery->order->currency }} {{ number_format($delivery->order->delivery_fee) }}</span>
              </div>
              <div class="info-row">
                <span class="info-key" style="font-weight:700;color:var(--text);">Total</span>
                <span class="info-val" style="color:var(--primary);">{{ $delivery->order->currency }} {{ number_format($delivery->order->total) }}</span>
              </div>
              <div class="info-row">
                <span class="info-key">Payment</span>
                <span class="info-val">
                  <span class="badge {{ $delivery->order->payment_status==='paid'?'badge-green':'badge-gray' }}" style="font-size:.65rem;">
                    {{ ucfirst($delivery->order->payment_status) }}
                  </span>
                </span>
              </div>
            </div>

            {{-- Driver details --}}
            <div class="info-block">
              <div class="info-block-title"><i class="fas fa-user" style="color:var(--primary);"></i> Driver Information</div>
              @if($delivery->driver)
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
                  <div class="avatar avatar-md" style="background:linear-gradient(135deg,#1e3a5f,#2563eb);color:#fff;font-size:.75rem;">
                    {{ substr($delivery->driver->user->first_name??'D',0,1) }}{{ substr($delivery->driver->user->last_name??'R',0,1) }}
                  </div>
                  <div>
                    <div style="font-weight:700;color:var(--text);font-size:.88rem;">{{ $delivery->driver->user->full_name??'Driver' }}</div>
                    <div style="font-size:.75rem;color:var(--text-muted);">{{ $delivery->driver->vehicle_type }} · {{ $delivery->driver->vehicle_plate }}</div>
                    <div style="display:flex;gap:2px;color:#f59e0b;font-size:.8rem;">{{ str_repeat('★',round($delivery->driver->average_rating)) }}</div>
                  </div>
                </div>
                <div style="display:flex;gap:8px;">
                  <button onclick="showToast('📞 Calling {{ $delivery->driver->user->phone??'+265 ...' }}...','info')" class="btn btn-outline btn-sm" style="flex:1;justify-content:center;">
                    <i class="fas fa-phone"></i> Call
                  </button>
                  <button onclick="showToast('📱 SMS sent to driver','success')" class="btn btn-outline btn-sm" style="flex:1;justify-content:center;">
                    <i class="fas fa-sms"></i> SMS
                  </button>
                </div>
              @else
                <div style="text-align:center;padding:20px;color:var(--text-muted);">
                  <i class="fas fa-user-clock" style="font-size:2rem;margin-bottom:10px;display:block;opacity:.3;"></i>
                  Driver being assigned...<br>
                  <span style="font-size:.75rem;">Usually within 2 hours</span>
                </div>
              @endif

              {{-- SMS Log --}}
              @if($delivery->statusLogs->count()>0)
                <div class="sms-log" style="margin-top:14px;">
                  <div style="font-size:.75rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">Status Updates</div>
                  @foreach($delivery->statusLogs->take(4) as $log)
                    <div class="sms-entry">
                      <span class="sms-time">{{ $log->created_at->format('H:i') }}</span>
                      <span>{{ $log->note??ucwords(str_replace('_',' ',$log->status)) }}</span>
                    </div>
                  @endforeach
                </div>
              @endif
            </div>
          </div>

          {{-- Rate delivery if delivered --}}
          @if($delivery->status==='delivered'&&!$delivery->buyer_rating)
            <div style="background:var(--green-50);border:1.5px solid var(--green-200);border-radius:var(--radius-md);padding:18px;margin-top:18px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;">
              <div>
                <div style="font-weight:700;color:var(--green-700);margin-bottom:4px;">🎉 Order Delivered! How was your experience?</div>
                <div style="font-size:.78rem;color:var(--text-muted);">Rate the driver to help other farmers</div>
              </div>
              <form method="POST" action="{{ route('delivery.rate',$delivery) }}" style="display:flex;align-items:center;gap:10px;">
                @csrf
                <div class="star-rating-input">
                  @for($i=5;$i>=1;$i--)
                    <input type="radio" name="rating" id="star-{{ $delivery->id }}-{{ $i }}" value="{{ $i }}"/>
                    <label for="star-{{ $delivery->id }}-{{ $i }}">★</label>
                  @endfor
                </div>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-check"></i> Submit</button>
              </form>
            </div>
          @elseif($delivery->buyer_rating)
            <div style="background:var(--green-50);border:1.5px solid var(--green-200);border-radius:var(--radius-md);padding:14px 18px;margin-top:18px;">
              <span style="font-size:.83rem;color:var(--green-700);font-weight:600;">
                <i class="fas fa-star" style="color:#f59e0b;"></i> You rated this delivery {{ $delivery->buyer_rating }}/5. Thank you!
              </span>
            </div>
          @endif

        </div>{{-- end delivery-card --}}
      @endforeach
    @else
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:60px;text-align:center;margin-bottom:28px;">
        <i class="fas fa-truck" style="font-size:3rem;margin-bottom:16px;display:block;color:var(--text-muted);opacity:.3;"></i>
        <h3 style="font-family:var(--font-display);font-size:1.2rem;margin-bottom:8px;color:var(--text);">No active deliveries</h3>
        <p style="color:var(--text-muted);margin-bottom:20px;">Order something from the marketplace to start tracking!</p>
        <a href="{{ route('marketplace') }}" class="btn btn-primary btn-md"><i class="fas fa-store"></i> Browse Marketplace</a>
      </div>
    @endif

    {{-- Delivery History --}}
    @if(isset($deliveryHistory)&&$deliveryHistory->count()>0)
      <h2 style="font-family:var(--font-display);font-size:1.25rem;font-weight:800;color:var(--text);margin-bottom:16px;">📋 Delivery History</h2>
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;">
        <div style="overflow-x:auto;">
          <table class="history-table">
            <thead>
              <tr>
                <th>Tracking #</th>
                <th>Order #</th>
                <th>Items</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($deliveryHistory as $del)
                <tr>
                  <td><span style="font-family:var(--font-mono);font-size:.78rem;color:var(--primary);">{{ $del->tracking_number }}</span></td>
                  <td style="font-weight:600;">#{{ $del->order->order_number }}</td>
                  <td>{{ $del->order->items->count() }} item(s)</td>
                  <td style="font-weight:700;color:var(--primary);">{{ $del->order->currency }} {{ number_format($del->order->total) }}</td>
                  <td>
                    <span class="badge {{ $del->status==='delivered'?'badge-green':'badge-coral' }}" style="font-size:.68rem;">
                      {{ ucfirst($del->status) }}
                    </span>
                  </td>
                  <td style="color:var(--text-muted);font-size:.8rem;">{{ $del->delivered_at?->format('M j, Y')??$del->created_at->format('M j, Y') }}</td>
                  <td>
                    <div style="display:flex;gap:6px;">
                      <button onclick="showToast('📄 Invoice downloading...','success')" class="btn btn-outline btn-sm" style="font-size:.72rem;padding:4px 10px;">
                        <i class="fas fa-file-invoice"></i> Invoice
                      </button>
                      <a href="{{ route('marketplace') }}" class="btn btn-primary btn-sm" style="font-size:.72px;padding:4px 10px;">
                        <i class="fas fa-redo"></i> Reorder
                      </a>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @if($deliveryHistory->hasPages())
          <div style="padding:16px 20px;border-top:1px solid var(--border);">
            {{ $deliveryHistory->links() }}
          </div>
        @endif
      </div>
    @endif
    @endauth

    {{-- Guest CTA --}}
    @guest
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:48px;text-align:center;">
        <i class="fas fa-truck" style="font-size:3rem;margin-bottom:16px;display:block;color:var(--primary);"></i>
        <h3 style="font-family:var(--font-display);font-size:1.3rem;margin-bottom:8px;">Track Your Agri Deliveries</h3>
        <p style="color:var(--text-muted);margin-bottom:20px;max-width:400px;margin-left:auto;margin-right:auto;">
          Sign in to see your active deliveries and full delivery history.
        </p>
        <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
          <a href="{{ route('login') }}" class="btn btn-primary btn-lg"><i class="fas fa-sign-in-alt"></i> Sign In</a>
          <a href="{{ route('register') }}" class="btn btn-outline btn-lg">Create Free Account</a>
        </div>
      </div>
    @endguest

  </div>
</div>

@include('partials.footer')
@endsection

@section('extra_js')
<script>
function trackOrder(e) {
  e.preventDefault();
  const num = document.getElementById('trackInput').value.trim();
  if (!num) { showToast('Please enter a tracking number', 'error'); return; }
  window.location.href = `/track/${num}`;
}

// Poll live status every 15 seconds for active deliveries
@if(isset($activeDeliveries)&&$activeDeliveries->count()>0)
  @foreach($activeDeliveries as $delivery)
    @if(!in_array($delivery->status,['delivered','failed']))
    setInterval(async () => {
      try {
        const res = await fetch(`/track/{{ $delivery->id }}/live-status`);
        const data = await res.json();
        // Update progress bar
        const pb = document.getElementById('progress-{{ $delivery->id }}');
        if (pb) pb.style.width = data.progress_percentage + '%';
        // Update distance
        if (data.distance_remaining_km) {
          document.querySelectorAll('[data-delivery="{{ $delivery->id }}"]').forEach(el => {});
        }
      } catch(e) {}
    }, 15000);
    @endif
  @endforeach
@endif

function refreshTracking(id) {
  showToast('🔄 Refreshing tracking data...', 'info');
  setTimeout(() => window.location.reload(), 1000);
}
</script>
@endsection
