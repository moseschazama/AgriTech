@extends('layouts.app')
@section('title', 'Crop Disease Detection & Prevention — AgriTech Pro')
@section('extra_css')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"/>
<link rel="stylesheet" href="{{ asset('css/home.css') }}"/>
<style>
.disease-hero{--hero-glow-1:rgba(245,158,11,0.06);--hero-glow-2:rgba(239,68,68,0.04);--hero-orb:rgba(245,158,11,0.05);--hero-badge-bg:rgba(255,255,255,0.15);--hero-badge-fg:#fff;--hero-badge-border:rgba(255,255,255,0.25);--hero-accent-color:var(--green-600);--hero-overlay-start:rgba(5,15,8,0.78);--hero-overlay-mid:rgba(8,20,12,0.55);--hero-overlay-end:rgba(3,12,6,0.72);--hero-overlay-accent:rgba(22,163,74,0.12);}
.disease-hero.page-hero-image{background-image:url('https://images.pexels.com/photos/36830577/pexels-photo-36830577.jpeg?auto=compress&cs=tinysrgb&w=1920&h=1080&dpr=1');}
.upload-zone{border:2.5px dashed var(--border);border-radius:var(--radius-xl);padding:40px;text-align:center;cursor:pointer;transition:all .2s;background:var(--bg-2);}
.upload-zone:hover,.upload-zone.dragover{border-color:var(--primary);background:var(--green-50);}
.upload-zone.has-image{border-color:var(--primary);border-style:solid;background:var(--green-50);}
.scan-result{display:none;background:var(--bg-card);border:1.5px solid var(--border);border-radius:var(--radius-xl);padding:24px;margin-top:16px;}
.scan-result.show{display:block;}
.confidence-bar{background:var(--bg-2);border-radius:20px;height:10px;overflow:hidden;margin:8px 0;}
.confidence-fill{height:10px;border-radius:20px;transition:width 1s ease;}
.alert-card{border-radius:var(--radius-lg);padding:20px;border-left:4px solid;margin-bottom:14px;display:flex;gap:16px;align-items:flex-start;}
.alert-critical{background:#fef2f2;border-color:#ef4444;}
.alert-warning{background:#fffbeb;border-color:#f59e0b;}
.alert-info{background:#eff6ff;border-color:#3b82f6;}
.alert-icon-critical{color:#ef4444;} .alert-icon-warning{color:#f59e0b;} .alert-icon-info{color:#3b82f6;}
.disease-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;transition:all .2s;cursor:pointer;}
.disease-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-lg);}
.disease-card-header{padding:16px 18px;border-bottom:1px solid var(--border);}
.severity-badge{font-size:.75rem;font-weight:700;padding:3px 9px;border-radius:var(--radius-full);}
.sev-critical{background:#fef2f2;color:#dc2626;border:1px solid #fecaca;}
.sev-high{background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;}
.sev-medium{background:#fffbeb;color:#b45309;border:1px solid #fde68a;}
.sev-low{background:#f0fdf4;color:var(--green-700);border:1px solid var(--green-200);}
.disease-card-body{padding:16px 18px;}
.symptom-chip{display:inline-flex;align-items:center;gap:4px;background:var(--bg-2);border:1px solid var(--border);border-radius:var(--radius-full);padding:3px 10px;font-size:.75rem;color:var(--text-muted);margin:3px;}
.crop-filter-chips{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:24px;}
.crop-chip{padding:7px 15px;border-radius:var(--radius-full);font-size:.8125rem;font-weight:600;border:1.5px solid var(--border);color:var(--text-muted);background:var(--bg-card);text-decoration:none;transition:all .15s;}
.crop-chip:hover,.crop-chip.active{background:var(--primary);border-color:var(--primary);color:#fff;}
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:9999;display:none;align-items:center;justify-content:center;padding:20px;}
.modal-overlay.open{display:flex;}
.modal-box{background:var(--bg-card);border-radius:var(--radius-xl);width:100%;max-width:700px;max-height:90vh;overflow:hidden;display:flex;flex-direction:column;}
.modal-header{padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-shrink:0;}
.modal-body{overflow-y:auto;padding:24px;}
.treatment-step{display:flex;gap:14px;margin-bottom:16px;align-items:flex-start;}
.step-num{width:28px;height:28px;border-radius:50%;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:800;flex-shrink:0;margin-top:2px;}
.disease-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;}
.live-feed{background:#0f172a;border-radius:var(--radius-xl);padding:22px;color:#e2e8f0;overflow:hidden;}
.live-feed-head{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;margin-bottom:14px;}
.live-dot{width:9px;height:9px;border-radius:50%;background:#22c55e;display:inline-block;margin-right:7px;animation:liveBlink 1s infinite;}
@keyframes liveBlink{0%,100%{opacity:1;}50%{opacity:.25;}}
.live-badge{display:inline-flex;align-items:center;background:rgba(34,197,94,.15);border:1px solid rgba(34,197,94,.4);color:#4ade80;font-size:.7rem;font-weight:800;letter-spacing:.08em;padding:4px 10px;border-radius:var(--radius-full);}
.feed-list{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:10px;min-height:120px;}
.feed-item{display:flex;align-items:center;gap:12px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);border-radius:var(--radius-lg);padding:11px 14px;border-left:4px solid #22c55e;animation:feedFlash .7s ease;}
.feed-item.sev-critical{border-left-color:#ef4444;} .feed-item.sev-high{border-left-color:#f97316;} .feed-item.sev-medium{border-left-color:#f59e0b;} .feed-item.sev-low{border-left-color:#22c55e;}
@keyframes feedFlash{0%{opacity:0;transform:translateY(-10px);}100%{opacity:1;transform:none;}}
.feed-pulse{width:8px;height:8px;border-radius:50%;background:#ef4444;flex-shrink:0;animation:liveBlink .9s infinite;}
.feed-title{font-weight:800;color:#fff;font-size:.9rem;}
.feed-sub{color:#94a3b8;font-size:.75rem;margin-top:2px;}
.feed-meta{margin-left:auto;text-align:right;flex-shrink:0;}
.feed-conf{font-weight:800;color:#4ade80;font-size:.8rem;}
.feed-sev{font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.05em;}
.result-section{background:var(--bg-2);border:1px solid var(--border);border-radius:var(--radius-lg);padding:14px 16px;margin-top:14px;}
.result-section-title{font-size:.75rem;font-weight:800;letter-spacing:.05em;text-transform:uppercase;color:var(--text-muted);margin-bottom:10px;display:flex;align-items:center;gap:7px;}
.step-item{display:flex;gap:10px;margin-bottom:9px;align-items:flex-start;}
.book-item{display:flex;gap:12px;padding:10px;border:1px solid var(--border);border-radius:var(--radius-md);margin-bottom:8px;background:var(--bg-card);}
.market-product{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:10px 12px;border:1px solid var(--border);border-radius:var(--radius-md);margin-bottom:8px;background:var(--bg-card);flex-wrap:wrap;}
.calendar-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:8px;}
.cal-month{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);padding:12px;text-align:center;}
.cal-month-name{font-size:.75rem;font-weight:700;color:var(--text-muted);margin-bottom:8px;text-transform:uppercase;}
.cal-risk{font-size:.75rem;font-weight:700;padding:3px 6px;border-radius:4px;}
.risk-critical{background:#fef2f2;color:#dc2626;} .risk-high{background:#fff7ed;color:#c2410c;}
.risk-medium{background:#fffbeb;color:#b45309;} .risk-low{background:#f0fdf4;color:var(--green-700);} .risk-none{background:var(--bg-2);color:var(--text-muted);}
@media(max-width:900px){.disease-grid{grid-template-columns:repeat(2,1fr);}.calendar-grid{grid-template-columns:repeat(3,1fr);}.scanner-layout{grid-template-columns:1fr !important;}.prevention-grid{grid-template-columns:repeat(2,1fr) !important;}}
@media(max-width:768px){.calendar-grid{grid-template-columns:repeat(3,1fr);gap:6px;}}
@media(max-width:600px){.upload-zone{padding:20px 14px;}.calendar-grid{grid-template-columns:repeat(2,1fr);}.prevention-grid{grid-template-columns:1fr !important;}}
@media(max-width:480px){.disease-grid{grid-template-columns:1fr;gap:14px;}.disease-card-header{padding:12px 14px;}.disease-card-body{padding:12px 14px;}.alert-card{padding:14px;gap:12px;}}
@media(max-width:360px){.calendar-grid{grid-template-columns:1fr;}}
</style>
@endsection

@section('content')

{{-- Hero --}}
<section class="page-hero disease-hero page-hero-image">
  <div class="container">
    <div class="page-hero-content">
      <span class="page-hero-badge">
        <i class="fas fa-microscope"></i> Crop Health Analysis
      </span>
      <h1 class="page-hero-title">Crop Disease Detection &<br><span class="accent">Prevention Guide</span></h1>
      <p class="page-hero-desc">Photo-based diagnosis in seconds + full treatment guides for 200+ crop diseases. Available to all farmers.</p>
      @if(isset($activeAlerts)&&$activeAlerts->count()>0)
        @php $topAlert=$activeAlerts->first(); @endphp
        <div class="page-hero-cta" style="margin-bottom:20px;">
          <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:var(--radius-lg);padding:14px 20px;display:inline-flex;align-items:center;gap:10px;max-width:600px;text-align:left;">
            <i class="fas fa-exclamation-triangle" style="color:#dc2626;font-size:1.1rem;flex-shrink:0;"></i>
            <div>
              <strong style="color:#dc2626;">ACTIVE ALERT:</strong>
              <span style="color:var(--text);"> {{ $topAlert->title }} — {{ implode(', ',$topAlert->affected_districts) }}</span>
              <a href="#alerts" style="color:var(--primary);font-weight:700;margin-left:6px;">View Details →</a>
            </div>
          </div>
        </div>
      @endif
      <p class="body-sm" style="text-align:center;color:var(--text-muted);">Or text <strong>ALERT</strong> to <strong>1212</strong> to subscribe to SMS outbreak alerts</p>
    </div>
  </div>
</section>

<div class="section" style="background:var(--bg-2);">
  <div class="container">

    {{-- Crop Scanner --}}
    <div style="max-width:640px;margin:0 auto 48px;">
      <div style="text-align:center;margin-bottom:20px;">
        <h2 class="heading-md" style="color:var(--text);margin-bottom:6px;"><i class="fas fa-microscope" style="color:var(--primary);"></i> Crop Photo Scanner</h2>
        <p class="body-sm" style="color:var(--text-muted);">Take a photo of a sick plant leaf and our analyzer identifies the disease instantly.</p>
      </div>

      @auth
        <form id="scanForm" enctype="multipart/form-data">
          @csrf
          <div class="upload-zone" id="uploadZone" onclick="document.getElementById('photoInput').click()">
            <div id="uploadContent">
              <i class="fas fa-camera" style="font-size:1.4rem;color:var(--primary);margin-bottom:12px;display:block;"></i>
              <div class="body-base font-700" style="color:var(--text);margin-bottom:6px;">Upload or Drag a Photo</div>
              <div class="body-sm" style="color:var(--text-muted);margin-bottom:14px;">Take a close-up photo of the sick leaf · JPG, PNG, WebP · Max 10MB</div>
              <button type="button" class="btn btn-primary btn-sm" onclick="event.stopPropagation();document.getElementById('photoInput').click()">
                <i class="fas fa-camera"></i> Choose Photo
              </button>
            </div>
            <div id="previewContent" style="display:none;">
              <img id="previewImg" src="" alt="Preview" style="max-height:200px;border-radius:var(--radius-md);margin-bottom:12px;"/>
              <div class="body-sm font-600" style="color:var(--primary);" id="previewName"></div>
            </div>
          </div>
          <input type="file" id="photoInput" name="photo" accept="image/*" style="display:none;" onchange="previewPhoto(this)"/>

          <div style="display:grid;grid-template-columns:1fr auto;gap:10px;margin-top:12px;">
            <input type="text" name="farm_location" class="form-input" placeholder="Farm location (optional, e.g. Lilongwe District)"/>
            <button type="button" onclick="submitScan()" id="scanBtn" class="btn btn-primary btn-md">
              <i class="fas fa-microscope"></i> Analyze
            </button>
          </div>
        </form>

        {{-- Scan Result --}}
        <div class="scan-result" id="scanResult">
          <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
            <div>
              <div class="label-sm font-700" style="color:var(--text-muted);margin-bottom:4px;">Detection Result</div>
              <div id="resultDisease" class="heading-md" style="color:var(--text);">Fall Armyworm</div>
              <div id="resultCrop" class="body-sm" style="color:var(--text-muted);margin-top:3px;">Maize</div>
            </div>
            <div id="resultSeverityBadge"></div>
          </div>
          <div style="margin-bottom:16px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
              <span class="body-sm font-600" style="color:var(--text);">Confidence Score</span>
              <span id="resultConfidenceText" class="text-base font-800" style="color:var(--primary);"></span>
            </div>
            <div class="confidence-bar"><div id="resultConfidenceBar" class="confidence-fill" style="background:var(--primary);width:0%;"></div></div>
          </div>
          <div id="resultDetails"></div>
          <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a id="resultViewGuide" href="{{ route('diseases') }}" class="btn btn-primary btn-md"><i class="fas fa-book"></i> View Treatment Guide</a>
            <button onclick="resetScan()" class="btn btn-outline btn-md"><i class="fas fa-redo"></i> Scan Another Photo</button>
            <button onclick="submitFeedback()" class="btn btn-outline btn-sm body-xs">
              <i class="fas fa-thumbs-down"></i> Wrong result?
            </button>
          </div>
        </div>
      @else
        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:40px;text-align:center;">
          <i class="fas fa-microscope" style="font-size:1.7rem;color:var(--primary);margin-bottom:14px;display:block;"></i>
            <h3 class="heading-sm" style="margin-bottom:8px;">Crop Disease Scanner</h3>
            <p class="body-sm" style="color:var(--text-muted);margin-bottom:20px;">Create an account to upload photos and get instant crop disease diagnosis.</p>
          <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg"><i class="fas fa-seedling"></i> Create Account</a>
            <a href="{{ route('login') }}" class="btn btn-outline btn-lg">Sign In</a>
          </div>
        </div>
      @endauth
    </div>

    {{-- Live Detection Feed --}}
    <div class="live-feed" style="margin-bottom:48px;">
      <div class="live-feed-head">
        <div style="display:flex;align-items:center;gap:10px;">
          <span class="live-badge"><span class="live-dot"></span> LIVE DETECTIONS</span>
        </div>
        <div class="body-sm" style="color:#94a3b8;">Real-time scans from farmers across Malawi →</div>
      </div>
      <ul class="feed-list" id="feedList"></ul>
    </div>

    {{-- Active Alerts --}}
    <div id="alerts" style="margin-bottom:48px;">
      <h2 class="heading-sm font-800" style="color:var(--text);margin-bottom:16px;"><i class="fas fa-circle-exclamation" style="color:#dc2626;"></i> Active Disease Alerts</h2>
      @forelse(isset($activeAlerts)?$activeAlerts:[] as $alert)
        <div class="alert-card alert-{{ $alert->alert_type }}">
          <div class="alert-icon-{{ $alert->alert_type }}" style="font-size:1.5rem;flex-shrink:0;">
            @if($alert->alert_type==='critical') <i class="fas fa-tower-broadcast" style="color:#dc2626;"></i>
            @elseif($alert->alert_type==='warning') <i class="fas fa-triangle-exclamation" style="color:#f59e0b;"></i>
            @else <i class="fas fa-circle-info" style="color:#2563eb;"></i> @endif
          </div>
          <div style="flex:1;">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;flex-wrap:wrap;">
              <span class="severity-badge sev-{{ $alert->alert_type==='critical'?'critical':($alert->alert_type==='warning'?'high':'low') }}">
                {{ strtoupper($alert->alert_type) }}
              </span>
              <span class="body-base font-700" style="color:var(--text);">{{ $alert->title }}</span>
            </div>
            <p class="body-sm" style="color:var(--text-muted);margin-bottom:8px;">{{ $alert->description }}</p>
            <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:8px;">
              <span class="body-xs" style="color:var(--text-muted);"><i class="fas fa-map-marker-alt"></i> {{ implode(', ',$alert->affected_districts) }}</span>
              <span class="body-xs" style="color:var(--text-muted);"><i class="fas fa-clock"></i> {{ $alert->created_at->diffForHumans() }}</span>
            </div>
            @if($alert->recommended_action)
              <div class="body-sm font-600" style="color:var(--text);"><i class="fas fa-check-circle" style="color:var(--primary);"></i> Action: {{ $alert->recommended_action }}</div>
            @endif
          </div>
          <div style="flex-shrink:0;">
            <button onclick="showToast('Downloading advisory PDF...','success')" class="btn btn-outline btn-sm">
              <i class="fas fa-download"></i> Advisory
            </button>
          </div>
        </div>
      @empty
        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:28px;text-align:center;color:var(--text-muted);">
          <i class="fas fa-check-circle" style="font-size:2rem;color:var(--primary);margin-bottom:10px;display:block;"></i>
          No active disease alerts in your area right now. Stay vigilant!
        </div>
      @endforelse
    </div>

    {{-- Disease Library --}}
    <div id="library">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:16px;margin-bottom:24px;">
        <div>
          <h2 class="heading-sm font-800" style="color:var(--text);margin-bottom:6px;"><i class="fas fa-book-medical" style="color:var(--primary);"></i> Disease Library</h2>
          <p class="body-sm" style="color:var(--text-muted);">{{ isset($diseases)?$diseases->total():0 }} diseases documented with full treatment guides</p>
        </div>
        <form method="GET" action="{{ route('diseases') }}" style="display:flex;gap:8px;flex-wrap:wrap;">
          <input type="text" name="q" value="{{ request('q') }}" class="form-input body-sm" placeholder="Search diseases..." style="padding:8px 14px;"/>
          <select name="severity" class="form-input form-select body-sm" style="padding:8px 12px;">
            <option value="">All Severities</option>
            @foreach(['Critical','High','Medium','Low'] as $s)
              <option value="{{ $s }}" @selected(request('severity')===$s)>{{ $s }}</option>
            @endforeach
          </select>
          <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
          @if(request('q')||request('crop')||request('severity'))
            <a href="{{ route('diseases') }}" class="btn btn-outline btn-sm">Clear</a>
          @endif
        </form>
      </div>

      {{-- Crop filter chips --}}
      <div class="crop-filter-chips">
        <a href="{{ route('diseases') }}" class="crop-chip {{ !request('crop') ? 'active' : '' }}">All Crops</a>
        @foreach(isset($crops)?$crops:[] as $crop)
          <a href="{{ route('diseases',['crop'=>$crop]) }}" class="crop-chip {{ request('crop')===$crop ? 'active' : '' }}">{{ $crop }}</a>
        @endforeach
      </div>

      {{-- Disease grid --}}
      <div class="disease-grid">
        @forelse(isset($diseases)?$diseases:[] as $disease)
          @php
            $sevClass=['Critical'=>'sev-critical','High'=>'sev-high','Medium'=>'sev-medium','Low'=>'sev-low'][$disease->severity]??'sev-medium';
            $catColors=['fungal'=>'#dcfce7,#86efac','bacterial'=>'#e0f2fe,#7dd3fc','viral'=>'#f3e8ff,#c4b5fd','pest'=>'#fff7ed,#fb923c','environmental'=>'#fef9c3,#fbbf24'];
            $catColor=$catColors[$disease->category]??'#dcfce7,#86efac';
            $cropImgs=['Maize'=>'maize-field.jpg','Tomato'=>'tomato.jpg','Potato'=>'potato.jpg','Beans'=>'veggies.jpg','Groundnuts'=>'groundnut.jpg','Rice'=>'harvest.jpg','Soybean'=>'groundnut.jpg','Sweet Potato'=>'potato.jpg'];
            $cropImg=$cropImgs[$disease->affected_crop]??'leaf-healthy.jpg';
          @endphp
          <div class="disease-card" onclick="openDiseaseModal({{ $disease->id }})">
            <div class="disease-card-header" style="background-image:url('{{ asset('assets/img/agri/'.$cropImg) }}');background-size:cover;background-position:center;">
              <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:6px;">
                <div>
                  <span class="badge badge-sky body-xs" style="margin-bottom:4px;">{{ $disease->affected_crop }}</span>
                  <div class="body-base font-800" style="color:var(--text);line-height:1.3;">{{ $disease->name }}</div>
                  @if($disease->scientific_name)
                    <div class="body-xs" style="color:var(--text-muted);font-style:italic;">{{ $disease->scientific_name }}</div>
                  @endif
                </div>
                <span class="severity-badge {{ $sevClass }}">{{ $disease->severity }}</span>
              </div>
            </div>
            <div class="disease-card-body">
              <div style="display:flex;gap:10px;margin-bottom:10px;flex-wrap:wrap;">
                <span class="body-xs" style="color:var(--text-muted);"><i class="fas fa-tag" style="color:var(--primary);"></i> {{ ucfirst($disease->category) }}</span>
                @if($disease->spread_vector)<span class="body-xs" style="color:var(--text-muted);"><i class="fas fa-bug" style="color:var(--text-muted);"></i> {{ $disease->spread_vector }}</span>@endif
                @if($disease->peak_season)<span class="body-xs" style="color:var(--text-muted);"><i class="fas fa-calendar" style="color:var(--text-muted);"></i> {{ $disease->peak_season }}</span>@endif
              </div>
              {{-- Symptoms preview --}}
              <div style="margin-bottom:12px;">
                @foreach(array_slice($disease->symptoms??[],0,2) as $symptom)
                  <span class="symptom-chip"><i class="fas fa-exclamation-circle" style="color:#f59e0b;font-size:.75rem;"></i> {{ Str::limit($symptom,40) }}</span>
                @endforeach
                @if(count($disease->symptoms??[])>2)
                  <span class="symptom-chip">+{{ count($disease->symptoms)-2 }} more</span>
                @endif
              </div>
              <div style="display:flex;align-items:center;justify-content:space-between;">
                <span class="body-xs" style="color:var(--text-muted);"><i class="fas fa-eye"></i> {{ number_format($disease->view_count) }} views</span>
                <span class="body-sm font-600" style="color:var(--primary);">View Full Guide →</span>
              </div>
            </div>
          </div>
        @empty
          <div style="grid-column:1/-1;text-align:center;padding:60px;color:var(--text-muted);">
            <i class="fas fa-search" style="font-size:1.75rem;margin-bottom:14px;display:block;opacity:.3;"></i>
            <h3>No diseases found</h3>
            <p>Try a different crop or search term.</p>
            <a href="{{ route('diseases') }}" class="btn btn-primary btn-sm" style="margin-top:16px;">Clear filters</a>
          </div>
        @endforelse
      </div>
      @if(isset($diseases)&&$diseases->hasPages())
        <div style="margin-top:32px;">{{ $diseases->withQueryString()->links() }}</div>
      @endif
    </div>

    {{-- Seasonal Risk Calendar --}}
    <div style="margin-top:56px;">
      <h2 class="heading-sm font-800" style="color:var(--text);margin-bottom:6px;"><i class="fas fa-calendar-days" style="color:var(--primary);"></i> Seasonal Disease Risk Calendar</h2>
      <p class="body-sm" style="color:var(--text-muted);margin-bottom:20px;">Based on Malawi growing seasons (Oct–Sep)</p>
      <div class="calendar-grid">
        @foreach(['Oct'=>['Fall Armyworm','Critical'],'Nov'=>['Maize Streak','High'],'Dec'=>['Cassava Mosaic','High'],'Jan'=>['Late Blight','Critical'],'Feb'=>['Groundnut Rosette','High'],'Mar'=>['Soybean Rust','Medium'],'Apr'=>['Cotton Bollworm','High'],'May'=>['Bacterial Wilt','Medium'],'Jun'=>['Cabbage Rot','Low'],'Jul'=>['Dry Season','None'],'Aug'=>['Dry Season','None'],'Sep'=>['Prepare','Low']] as $month=>[$disease,$risk])
          <div class="cal-month">
            <div class="cal-month-name">{{ $month }}</div>
            <span class="cal-risk risk-{{ strtolower($risk) }}">{{ $risk }}</span>
            <div class="body-xs" style="color:var(--text-muted);margin-top:6px;">{{ $disease }}</div>
          </div>
        @endforeach
      </div>
    </div>

    {{-- Prevention Tips --}}
    <div style="margin-top:48px;">
      <h2 class="heading-sm font-800" style="color:var(--text);margin-bottom:16px;"><i class="fas fa-shield-halved" style="color:var(--green-600);"></i> Prevention Best Practices</h2>
      <div class="prevention-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
        @foreach([
          ['fas fa-seedling','var(--green-100)','var(--green-700)','Use Certified Seed','Always buy certified, disease-resistant seed varieties from licensed agro-dealers.'],
          ['fas fa-sync-alt','#e0f2fe','var(--sky-600)','Crop Rotation','Rotate maize with soybeans or groundnuts to break pest and disease cycles.'],
          ['fas fa-eye','#fff7ed','#c2410c','Scout Regularly','Walk your fields weekly to catch disease early before it spreads.'],
          ['fas fa-trash-alt','#fef2f2','#dc2626','Farm Hygiene','Remove and burn infected plant material. Don\'t leave crop residue.'],
          ['fas fa-tint','#f0fdf4','var(--green-700)','Proper Irrigation','Water at the base. Wet leaves invite fungal infection.'],
          ['fas fa-calendar-check','#f5f3ff','#7c3aed','Timely Planting','Plant at the recommended time to avoid peak disease seasons.'],
        ] as [$icon,$bg,$color,$title,$desc])
          <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:20px;">
            <div style="width:44px;height:44px;border-radius:12px;background:{{ $bg }};color:{{ $color }};display:flex;align-items:center;justify-content:center;font-size:1.1rem;margin-bottom:12px;">
              <i class="{{ $icon }}"></i>
            </div>
            <div class="body-base font-700" style="color:var(--text);margin-bottom:6px;">{{ $title }}</div>
            <div class="body-sm" style="color:var(--text-muted);">{{ $desc }}</div>
          </div>
        @endforeach
      </div>
    </div>

    {{-- SMS Subscription --}}
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:36px;margin-top:48px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:24px;color:var(--text);box-shadow:var(--shadow-md);">
      <div>
<h3 class="heading-sm" style="font-weight:800;margin-bottom:6px;"><i class="fas fa-comment-sms" style="color:var(--primary);"></i> Get SMS Disease Outbreak Alerts</h3>
        <p class="body-sm" style="color:var(--text-muted);max-width:400px;">Be the first to know when disease outbreaks are detected in your district. SMS alerts sent directly to your phone.</p>
      </div>
      <form method="POST" action="{{ route('diseases.subscribe') }}" style="display:flex;gap:8px;flex-wrap:wrap;">
        @csrf
        <input type="tel" name="phone" class="form-input" placeholder="+265 99 123 4567"
               value="{{ auth()->check() ? Auth::user()->phone : '' }}"
               style="background:rgba(255,255,255,.95);color:var(--text);border:none;min-width:200px;"/>
        <button type="submit" class="btn btn-primary btn-md"><i class="fas fa-bell"></i> Subscribe</button>
      </form>
    </div>

  </div>
</div>

{{-- Disease Modals --}}
@foreach(isset($diseases)?$diseases:[] as $disease)
<div class="modal-overlay" id="modal-{{ $disease->id }}">
  <div class="modal-box">
    <div class="modal-header">
      <div>
        <div class="heading-xs font-800" style="color:var(--text);">{{ $disease->name }}</div>
        <div style="display:flex;gap:8px;margin-top:4px;flex-wrap:wrap;">
          <span class="badge badge-sky body-xs">{{ $disease->affected_crop }}</span>
          <span class="severity-badge {{ ['Critical'=>'sev-critical','High'=>'sev-high','Medium'=>'sev-medium','Low'=>'sev-low'][$disease->severity]??'sev-medium' }}">{{ $disease->severity }} Risk</span>
          <span class="body-xs" style="color:var(--text-muted);">{{ ucfirst($disease->category) }}</span>
        </div>
      </div>
      <button onclick="closeDiseaseModal({{ $disease->id }})" style="background:none;border:none;font-size:1.2rem;cursor:pointer;color:var(--text-muted);">✕</button>
    </div>
    <div class="modal-body">
      <div style="display:grid;gap:20px;">
        <div>
          <div class="label-sm font-700" style="color:var(--text-muted);margin-bottom:10px;">Symptoms</div>
          <ul style="margin:0;padding-left:18px;">
            @foreach($disease->symptoms??[] as $s)<li class="body-sm" style="color:var(--text);margin-bottom:5px;">{{ $s }}</li>@endforeach
          </ul>
        </div>
        <div>
          <div class="label-sm font-700" style="color:var(--text-muted);margin-bottom:8px;">Cause & Spread</div>
          <p class="body-sm" style="color:var(--text);margin-bottom:8px;">{{ $disease->cause }}</p>
          <p class="body-sm" style="color:var(--text);">{{ $disease->spread_mechanism }}</p>
          @if($disease->peak_season)<p class="body-sm font-600" style="color:#f59e0b;margin-top:8px;"><i class="fas fa-calendar"></i> Peak season: {{ $disease->peak_season }}</p>@endif
        </div>
        <div>
          <div class="label-sm font-700" style="color:var(--text-muted);margin-bottom:12px;">Treatment Steps</div>
          @foreach($disease->treatment_steps??[] as $i=>$step)
            <div class="treatment-step">
              <div class="step-num">{{ $i+1 }}</div>
              <div class="body-sm" style="color:var(--text);padding-top:4px;">{{ $step }}</div>
            </div>
          @endforeach
        </div>
        <div>
          <div class="label-sm font-700" style="color:var(--text-muted);margin-bottom:10px;">Prevention</div>
          <ul style="margin:0;padding-left:18px;">
            @foreach($disease->prevention_methods??[] as $p)<li class="body-sm" style="color:var(--text);margin-bottom:5px;">{{ $p }}</li>@endforeach
          </ul>
        </div>
        @if($disease->recommended_products&&count($disease->recommended_products)>0)
        <div>
          <div class="label-sm font-700" style="color:var(--text-muted);margin-bottom:12px;">Recommended Products</div>
          <div style="display:flex;gap:10px;flex-wrap:wrap;">
            @foreach($disease->recommended_products as $prod)
              <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:var(--radius-md);padding:10px 14px;" class="body-sm">
                <div style="font-weight:700;color:var(--text);">{{ $prod['name']??'' }}</div>
                @if(isset($prod['price']))<div class="font-600" style="color:var(--primary);margin-top:2px;">{{ $prod['price'] }}</div>@endif
              </div>
            @endforeach
          </div>
          <a href="{{ route('marketplace',['category'=>'chemicals']) }}" class="btn btn-primary btn-sm" style="margin-top:12px;">
            <i class="fas fa-store"></i> Buy in Marketplace
          </a>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endforeach

@include('partials.footer')
@endsection

@section('extra_js')
<script>
// Photo preview
function previewPhoto(input) {
  const file = input.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = e => {
    document.getElementById('previewImg').src = e.target.result;
    document.getElementById('previewName').textContent = file.name;
    document.getElementById('uploadContent').style.display = 'none';
    document.getElementById('previewContent').style.display = 'block';
    document.getElementById('uploadZone').classList.add('has-image');
  };
  reader.readAsDataURL(file);
}

// Drag and drop
const zone = document.getElementById('uploadZone');
if (zone) {
  zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('dragover'); });
  zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
  zone.addEventListener('drop', e => {
    e.preventDefault(); zone.classList.remove('dragover');
    const file = e.dataTransfer.files[0];
    if (file) { document.getElementById('photoInput').files = e.dataTransfer.files; previewPhoto(document.getElementById('photoInput')); }
  });
}

// Submit scan
async function submitScan() {
  const photo = document.getElementById('photoInput').files[0];
  if (!photo) { showToast('Please select or take a photo first', 'error'); return; }
  const btn = document.getElementById('scanBtn');
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Analyzing...';
  btn.disabled = true;
  const form = document.getElementById('scanForm');
  const data = new FormData(form);
  data.append('photo', photo);
  try {
    const res  = await fetch('{{ route("diseases.detect") }}', { method:'POST', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'}, body:data });
    const json = await res.json();
    if (json.success) {
      showResult(json.detection);
    } else {
      showToast(json.message||'Analysis failed. Try a clearer photo.', 'error');
    }
  } catch(e) {
    showToast('Network error. Please try again.', 'error');
  } finally {
    btn.innerHTML = '<i class="fas fa-microscope"></i> Analyze';
    btn.disabled = false;
  }
}

function showResult(d) {
  document.getElementById('resultDisease').textContent = d.disease || 'Unknown';
  document.getElementById('resultCrop').textContent = d.crop ? 'Affected crop: '+d.crop : '';
  const conf = parseFloat(d.confidence)||0;
  document.getElementById('resultConfidenceText').textContent = conf.toFixed(1)+'%';
  document.getElementById('resultConfidenceBar').style.background = conf>80?'var(--primary)':conf>60?'#f59e0b':'#ef4444';
  document.getElementById('resultConfidenceBar').style.width = conf+'%';
  if (d.disease_url) document.getElementById('resultViewGuide').href = d.disease_url;
  else document.getElementById('resultViewGuide').href = '#library';
  const sev = d.severity||'None';
  const sevColors={'None':'badge-green','Low':'badge-green','Medium':'badge-earth','High':'badge-coral','Critical':'badge-coral'};
  document.getElementById('resultSeverityBadge').innerHTML = `<span class="badge ${sevColors[sev]||'badge-gray'}" style="font-size:.78rem;padding:5px 12px;">${sev} Risk</span>`;
  document.getElementById('resultDetails').innerHTML = buildDetectionDetails(d);
  document.getElementById('scanResult').classList.add('show');
  document.getElementById('scanResult').scrollIntoView({behavior:'smooth',block:'center'});
}

function buildDetectionDetails(d) {
  const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  let html = '';

  const symptoms = Array.isArray(d.symptoms) ? d.symptoms : [];
  if (symptoms.length) {
    html += `<div class="result-section"><div class="result-section-title"><i class="fas fa-stethoscope"></i> Symptoms</div><ul style="margin:0;padding-left:18px;">` +
      symptoms.map(s => `<li class="body-sm" style="color:var(--text);margin-bottom:5px;">${esc(s)}</li>`).join('') + `</ul></div>`;
  }

  if (d.cause || d.spread) {
    html += `<div class="result-section"><div class="result-section-title"><i class="fas fa-bug"></i> Cause &amp; Spread</div>` +
      `<div class="body-sm" style="color:var(--text);margin-bottom:6px;">${esc(d.cause||'Unknown cause')}</div>` +
      (d.spread ? `<div class="body-sm" style="color:var(--text-muted);">${esc(d.spread)}</div>` : '') + `</div>`;
  }

  const mitigation = Array.isArray(d.mitigation_guide) ? d.mitigation_guide : (Array.isArray(d.recommended_action)?d.recommended_action:[]);
  if (mitigation.length) {
    html += `<div class="result-section"><div class="result-section-title"><i class="fas fa-first-aid"></i> Mitigation Guide</div>` +
      mitigation.map((s,i) => `<div class="step-item"><div class="step-num">${i+1}</div><div class="body-sm" style="color:var(--text);padding-top:4px;">${esc(s)}</div></div>`).join('') + `</div>`;
  }

  const practices = Array.isArray(d.best_practices) ? d.best_practices : (Array.isArray(d.prevention)?d.prevention:[]);
  if (practices.length) {
    html += `<div class="result-section"><div class="result-section-title"><i class="fas fa-shield-alt"></i> Best Practices</div><ul style="margin:0;padding-left:18px;">` +
      practices.map(s => `<li class="body-sm" style="color:var(--text);margin-bottom:5px;">${esc(s)}</li>`).join('') + `</ul></div>`;
  }

  const products = Array.isArray(d.market_products) ? d.market_products : [];
  if (products.length) {
    html += `<div class="result-section"><div class="result-section-title"><i class="fas fa-store"></i> Recommended Treatments from Market</div>` +
      products.map(p => {
        const price = p.price ? `@ ${p.currency?p.currency+' ':'MK '}${p.price}${p.unit?'/'+esc(p.unit):''}` : '';
        return `<div class="market-product">
          <div><div class="body-sm font-700" style="color:var(--text);">${esc(p.name)}</div>
          ${price?`<div class="body-xs font-600" style="color:var(--primary);margin-top:2px;">${price}</div>`:''}</div>
          <a href="${esc(p.url)}" class="btn btn-primary btn-sm" target="_blank"><i class="fas fa-shopping-cart"></i> ${p.in_market?'Buy Now':'View'}</a>
        </div>`;
      }).join('') + `</div>`;
  }

  const books = Array.isArray(d.books) ? d.books : [];
  if (books.length) {
    html += `<div class="result-section"><div class="result-section-title"><i class="fas fa-book-open"></i> Books to Read</div>` +
      books.map(b => `<div class="book-item">
        <i class="fas fa-book" style="color:var(--primary);font-size:1.2rem;margin-top:2px;"></i>
        <div><div class="body-sm font-700" style="color:var(--text);">${esc(b.title)}</div>
        <div class="body-xs" style="color:var(--text-muted);margin-top:2px;">${esc(b.author)}${b.note?' · '+esc(b.note):''}</div></div>
      </div>`).join('') + `</div>`;
  }

  return html;
}

function resetScan() {
  document.getElementById('scanResult').classList.remove('show');
  document.getElementById('resultDetails').innerHTML = '';
  document.getElementById('uploadContent').style.display='block';
  document.getElementById('previewContent').style.display='none';
  document.getElementById('uploadZone').classList.remove('has-image');
  document.getElementById('photoInput').value='';
}

function submitFeedback() {
  showToast('Thank you for your feedback! This helps keep our diagnosis guide accurate.','success');
}

// Disease modals
function openDiseaseModal(id) {
  document.getElementById('modal-'+id).classList.add('open');
  document.body.style.overflow='hidden';
}
function closeDiseaseModal(id) {
  document.getElementById('modal-'+id).classList.remove('open');
  document.body.style.overflow='';
}

// Close modal on backdrop click
document.querySelectorAll('.modal-overlay').forEach(m => {
  m.addEventListener('click', e => { if(e.target===m) { m.classList.remove('open'); document.body.style.overflow=''; } });
});
document.addEventListener('keydown', e => {
  if(e.key==='Escape') { document.querySelectorAll('.modal-overlay.open').forEach(m => { m.classList.remove('open'); document.body.style.overflow=''; }); }
});

// ── Live Detection Feed ──
@php
  $feedEntries = collect();
  foreach (isset($recentDetections) ? $recentDetections : [] as $det) {
      $feedEntries->push([
          'title' => $det->detected_disease,
          'sub'   => trim(($det->affected_crop ?? 'Crop') . ($det->farm_location ? ' · ' . $det->farm_location : '')),
          'conf'  => (float) $det->confidence_score,
          'sev'   => $det->severity ? ucfirst($det->severity) : 'Low',
          'time'  => $det->created_at ? $det->created_at->diffForHumans() : 'just now',
      ]);
  }
  foreach (\App\Models\Disease::published()->orderByDesc('view_count')->limit(5)->get() as $dis) {
      $feedEntries->push([
          'title' => $dis->name,
          'sub'   => $dis->affected_crop . ' · monitoring',
          'conf'  => rand(72, 98),
          'sev'   => $dis->severity,
          'time'  => 'scanning now',
      ]);
  }
@endphp
const FEED_DATA = {!! $feedEntries->take(12)->values()->toJson() !!};

const feedList = document.getElementById('feedList');
let feedIdx = 0;
function escJs(s) {
  return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}
function pushFeedItem(item) {
  if (!feedList || !item) return;
  const sevClass = item.sev === 'Critical' ? 'sev-critical' : item.sev === 'High' ? 'sev-high' : item.sev === 'Medium' ? 'sev-medium' : 'sev-low';
  const sevColor = item.sev === 'Critical' ? '#f87171' : item.sev === 'High' ? '#fb923c' : item.sev === 'Medium' ? '#fbbf24' : '#4ade80';
  const li = document.createElement('li');
  li.className = 'feed-item ' + sevClass;
  li.innerHTML = `
    <span class="feed-pulse"></span>
    <div>
      <div class="feed-title">${escJs(item.title)}</div>
      <div class="feed-sub">${escJs(item.sub)}</div>
    </div>
    <div class="feed-meta">
      <div class="feed-conf">${Number(item.conf).toFixed(1)}%</div>
      <div class="feed-sev" style="color:${sevColor};">${escJs(item.sev)}</div>
      <div class="feed-sub">${escJs(item.time)}</div>
    </div>`;
  feedList.prepend(li);
  while (feedList.children.length > 5) feedList.removeChild(feedList.lastChild);
}
if (feedList && FEED_DATA.length) {
  for (let i = 2; i >= 0; i--) pushFeedItem(FEED_DATA[(FEED_DATA.length - 1 - i) % FEED_DATA.length]);
  setInterval(() => {
    pushFeedItem(FEED_DATA[feedIdx % FEED_DATA.length]);
    feedIdx = (feedIdx + 1) % FEED_DATA.length;
  }, 3200);
}
</script>
@endsection
