@extends('layouts.app')
@section('title', $disease->name . ' — Treatment & Prevention Guide | AgriTech Pro')
@section('extra_css')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"/>
<link rel="stylesheet" href="{{ asset('css/home.css') }}"/>
<style>
.detail-hero{background:#0f172a;color:#fff;padding:56px 0 44px;}
.breadcrumb{font-size:.78rem;color:#94a3b8;margin-bottom:14px;}
.breadcrumb a{color:#4ade80;text-decoration:none;}
.severity-badge{font-size:.75rem;font-weight:700;padding:4px 12px;border-radius:var(--radius-full);}
.sev-critical{background:#fef2f2;color:#dc2626;border:1px solid #fecaca;}
.sev-high{background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;}
.sev-medium{background:#fffbeb;color:#b45309;border:1px solid #fde68a;}
.sev-low{background:#f0fdf4;color:var(--green-700);border:1px solid var(--green-200);}
.guide-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:26px;box-shadow:var(--shadow-sm);}
.section-label{display:inline-flex;align-items:center;gap:8px;background:var(--green-50);color:var(--green-700);border:1px solid var(--green-200);border-radius:var(--radius-full);padding:5px 14px;font-size:.72rem;font-weight:800;letter-spacing:.05em;text-transform:uppercase;margin-bottom:14px;}
.step-item{display:flex;gap:14px;margin-bottom:18px;align-items:flex-start;}
.step-num{width:30px;height:30px;border-radius:50%;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:.78rem;font-weight:800;flex-shrink:0;margin-top:2px;}
.market-product{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:12px 14px;border:1px solid var(--border);border-radius:var(--radius-lg);margin-bottom:10px;background:var(--bg-2);flex-wrap:wrap;}
.book-item{display:flex;gap:14px;padding:14px;border:1px solid var(--border);border-radius:var(--radius-lg);margin-bottom:10px;background:var(--bg-2);}
.alert-strip{background:#fef2f2;border:1px solid #fecaca;border-radius:var(--radius-lg);padding:14px 18px;margin-bottom:26px;display:flex;gap:12px;align-items:flex-start;}
</style>
@endsection

@section('content')

<section class="detail-hero">
  <div class="container">
    <div class="breadcrumb"><a href="{{ route('diseases') }}"><i class="fas fa-arrow-left"></i> Disease Library</a></div>
    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:12px;">
      <span class="badge badge-sky body-xs">{{ $disease->affected_crop }}</span>
      <span class="severity-badge {{ ['Critical'=>'sev-critical','High'=>'sev-high','Medium'=>'sev-medium','Low'=>'sev-low'][$disease->severity]??'sev-medium' }}">{{ $disease->severity }} Risk</span>
      <span class="body-xs" style="color:#94a3b8;">{{ ucfirst($disease->category) }}</span>
    </div>
    <h1 style="font-size:clamp(1.6rem,3.5vw,2.4rem);font-weight:800;line-height:1.15;margin-bottom:6px;">{{ $disease->name }}</h1>
    @if($disease->scientific_name)<div style="color:#94a3b8;font-style:italic;font-size:.9rem;">{{ $disease->scientific_name }}</div>@endif
    @if($disease->impact_stat)
      <div class="alert-strip" style="margin-top:18px;">
        <i class="fas fa-exclamation-triangle" style="color:#dc2626;font-size:1.1rem;flex-shrink:0;margin-top:2px;"></i>
        <div class="body-sm" style="color:var(--text);">{{ $disease->impact_stat }}</div>
      </div>
    @endif
  </div>
</section>

<div class="section" style="background:var(--bg-2);">
  <div class="container">

    {{-- Overview --}}
    <div class="guide-card" style="margin-bottom:24px;">
      <span class="section-label"><i class="fas fa-info-circle"></i> Overview</span>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:20px;">
        <div>
          <div class="label-sm font-700" style="color:var(--text-muted);margin-bottom:8px;">Cause</div>
          <p class="body-sm" style="color:var(--text);">{{ $disease->cause ?? 'Not specified.' }}</p>
        </div>
        <div>
          <div class="label-sm font-700" style="color:var(--text-muted);margin-bottom:8px;">How it spreads</div>
          <p class="body-sm" style="color:var(--text);">{{ $disease->spread_mechanism ?? 'Not specified.' }}
          @if($disease->spread_vector)<br/><span style="color:var(--text-muted);"><i class="fas fa-bug"></i> Spread vector: {{ $disease->spread_vector }}</span>@endif</p>
        </div>
        <div>
          <div class="label-sm font-700" style="color:var(--text-muted);margin-bottom:8px;">Peak season</div>
          <p class="body-sm font-600" style="color:#f59e0b;"><i class="fas fa-calendar"></i> {{ $disease->peak_season ?? 'Year-round risk' }}</p>
          @if($disease->seasonal_info)<p class="body-xs" style="color:var(--text-muted);margin-top:6px;">{{ $disease->seasonal_info }}</p>@endif
        </div>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
      {{-- Symptoms --}}
      <div class="guide-card">
        <span class="section-label"><i class="fas fa-stethoscope"></i> Symptoms</span>
        <ul style="margin:0;padding-left:18px;">
          @forelse($disease->symptoms ?? [] as $s)
            <li class="body-sm" style="color:var(--text);margin-bottom:8px;">{{ $s }}</li>
          @empty
            <li class="body-sm" style="color:var(--text-muted);">Symptoms not yet documented.</li>
          @endforelse
        </ul>
      </div>

      {{-- Mitigation Guide --}}
      <div class="guide-card">
        <span class="section-label"><i class="fas fa-first-aid"></i> Mitigation Guide</span>
        @forelse(($disease->treatment_steps ?? []) ?: [] as $i => $step)
          <div class="step-item">
            <div class="step-num">{{ $i + 1 }}</div>
            <div class="body-sm" style="color:var(--text);padding-top:5px;">{{ $step }}</div>
          </div>
        @empty
          <div class="body-sm" style="color:var(--text-muted);">
            Isolate affected plants, apply the recommended treatment, and consult your agricultural extension officer.
          </div>
        @endforelse
      </div>
    </div>

    {{-- Best Practices + Market Products --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-top:24px;">
      <div class="guide-card">
        <span class="section-label"><i class="fas fa-shield-alt"></i> Best Practices & Prevention</span>
        <ul style="margin:0;padding-left:18px;">
          @forelse($disease->prevention_methods ?? [] as $p)
            <li class="body-sm" style="color:var(--text);margin-bottom:8px;">
              <i class="fas fa-check-circle" style="color:var(--primary);margin-right:6px;"></i>{{ $p }}
            </li>
          @empty
            <li class="body-sm" style="color:var(--text-muted);">No prevention notes yet.</li>
          @endforelse
        </ul>
      </div>

      <div class="guide-card">
        <span class="section-label"><i class="fas fa-store"></i> Buy Treatment in the Marketplace</span>
        @forelse(($products ?? []) ?: [] as $product)
          <div class="market-product">
            <div>
              <div class="body-sm font-700" style="color:var(--text);">{{ $product['name'] }}</div>
              @if($product['price'])
                <div class="body-xs font-600" style="color:var(--primary);margin-top:2px;">
                  {{ $product['currency'] ? $product['currency'].' ' : 'MK ' }}{{ $product['price'] }}{{ $product['unit'] ? '/'. $product['unit'] : '' }}
                </div>
              @endif
            </div>
            <a href="{{ $product['url'] }}" target="_blank" class="btn btn-primary btn-sm">
              <i class="fas fa-shopping-cart"></i> {{ $product['in_market'] ? 'Buy Now' : 'View' }}
            </a>
          </div>
        @empty
          @forelse(($disease->recommended_products ?? []) ?: [] as $rec)
            <div class="market-product">
              <div>
                <div class="body-sm font-700" style="color:var(--text);">{{ $rec['name'] ?? '' }}</div>
                @if(isset($rec['price']))<div class="body-xs font-600" style="color:var(--primary);margin-top:2px;">{{ $rec['price'] }}</div>@endif
              </div>
              <a href="{{ route('marketplace', ['category'=>'chemicals']) }}" class="btn btn-outline btn-sm"><i class="fas fa-store"></i> Browse</a>
            </div>
          @empty
            <div class="body-sm" style="color:var(--text-muted);">No treatments listed yet. Ask your agro-dealer or extension officer.</div>
          @endforelse
        @endforelse
        <a href="{{ route('marketplace', ['category'=>'chemicals']) }}" class="btn btn-outline btn-sm" style="margin-top:6px;"><i class="fas fa-store"></i> Browse all agro-chemicals</a>
      </div>
    </div>

    {{-- Books --}}
    <div class="guide-card" style="margin-top:24px;">
      <span class="section-label"><i class="fas fa-book-open"></i> Books to Read</span>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:14px;">
        @forelse(($books ?? []) ?: [] as $book)
          <div class="book-item">
            <i class="fas fa-book" style="color:var(--primary);font-size:1.3rem;margin-top:2px;"></i>
            <div>
              <div class="body-sm font-700" style="color:var(--text);">{{ $book['title'] }}</div>
              <div class="body-xs" style="color:var(--text-muted);margin-top:2px;">{{ $book['author'] }}</div>
              @if(isset($book['note']))<div class="body-xs" style="color:var(--text-muted);margin-top:4px;">{{ $book['note'] }}</div>@endif
            </div>
          </div>
        @empty
          <div class="body-sm" style="color:var(--text-muted);">
            <a href="{{ route('diseases') }}" style="color:var(--primary);font-weight:700;">Scan a photo</a> or check with your local extension officer for printed guides.
          </div>
        @endforelse
      </div>
    </div>

    {{-- Keep Learning — curated course recommendations --}}
    @if(isset($courses) && count($courses) > 0)
    <div class="guide-card" style="margin-top:24px;">
      <span class="section-label"><i class="fas fa-graduation-cap"></i> Keep Learning — Recommended Courses</span>
      <p class="body-sm" style="color:var(--text-muted);margin-bottom:14px;">
        Treat it today, and learn how to prevent it next season. These courses complete the picture with hands-on, long-term disease management skills.
      </p>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:14px;">
        @foreach($courses as $rec)
          <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:var(--radius-lg);padding:18px;display:flex;flex-direction:column;gap:8px;">
            <div>
              <span class="badge badge-sky body-xs" style="display:inline-block;margin-bottom:6px;">{{ $rec['category_label'] }}</span>
              <div class="body-base font-700" style="color:var(--text);line-height:1.3;">{{ $rec['title'] }}</div>
            </div>
            <div class="body-xs" style="color:var(--text-muted);">
              <i class="fas fa-play-circle"></i> {{ $rec['lessons'] }} lessons · {{ intdiv($rec['duration_minutes'], 60) }}h {{ $rec['duration_minutes'] % 60 }}m
              @if($rec['is_free'])
                <span class="badge badge-green" style="margin-left:4px;">Open</span>
              @else
                <span class="badge badge-earth" style="margin-left:4px;">{{ number_format($rec['price']) }} {{ $rec['currency'] }}</span>
              @endif
            </div>
            @if($rec['reason'])
              <div class="body-xs font-600" style="color:var(--primary);"><i class="fas fa-check-circle"></i> {{ $rec['reason'] }}</div>
            @endif
            <a href="{{ $rec['url'] }}" class="btn btn-primary btn-sm" style="margin-top:auto;justify-content:center;">
              <i class="fas fa-graduation-cap"></i> Open in Learning Center
            </a>
          </div>
        @endforeach
      </div>
    </div>
    @endif

    {{-- CTA --}}
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:32px;margin-top:32px;text-align:center;">
      <h3 class="heading-sm font-800" style="color:var(--text);margin-bottom:8px;">Spotted this on your farm?</h3>
      <p class="body-sm" style="color:var(--text-muted);margin-bottom:18px;">Upload a photo of the affected plant and get an instant diagnosis with treatment recommendations.</p>
      <a href="{{ route('diseases') }}#library" class="btn btn-primary btn-lg"><i class="fas fa-microscope"></i> Scan a Photo</a>
    </div>

  </div>
</div>

@include('partials.footer')
@endsection