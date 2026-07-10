@extends('layouts.app')
@section('title', 'AgriTech Pro — Empowering Farmers Through Technology')
@section('extra_css')
<link rel="stylesheet" href="{{ asset('css/home.css') }}"/>
<style>
/* ── Weather Bar ── */
.weather-bar{background:linear-gradient(90deg,#0c4a2e,#166534);color:#fff;padding:9px 0;font-size:.82rem;overflow:hidden;}
.weather-bar-inner{display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;}
.weather-items{display:flex;gap:20px;flex-wrap:wrap;}
.weather-item{display:flex;align-items:center;gap:6px;opacity:.9;}
.weather-alert{background:rgba(239,68,68,.2);border:1px solid rgba(239,68,68,.4);border-radius:20px;padding:3px 12px;font-weight:700;font-size:.78rem;}
.weather-alert a{color:#fca5a5;}
/* ── Section label fix ── */
.section-label{display:inline-flex;align-items:center;gap:7px;background:var(--green-50);color:var(--green-700);border:1px solid var(--green-200);border-radius:var(--radius-full);padding:5px 14px;font-size:.78rem;font-weight:700;letter-spacing:.04em;text-transform:uppercase;margin-bottom:14px;}
/* ── Course card ── */
.course-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;transition:all var(--t-med);cursor:pointer;}
.course-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-lg);border-color:var(--green-300);}
.course-thumb{height:160px;position:relative;}
.course-badge-wrap{position:absolute;top:10px;left:10px;display:flex;gap:6px;flex-wrap:wrap;}
.course-premium-badge{background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;font-size:.68rem;font-weight:700;padding:3px 9px;border-radius:var(--radius-full);}
.course-body{padding:16px;}
.course-category{font-size:.7rem;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px;}
.course-title{font-family:var(--font-display);font-size:.95rem;font-weight:700;color:var(--text);margin-bottom:8px;line-height:1.3;}
.course-meta{display:flex;gap:10px;flex-wrap:wrap;font-size:.74rem;color:var(--text-muted);margin-bottom:12px;}
.course-meta-item{display:flex;align-items:center;gap:4px;}
.course-footer{display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border);padding-top:12px;}
.course-price{font-family:var(--font-display);font-size:1.1rem;font-weight:800;color:var(--primary);}
.course-price-free{color:var(--green-600);}
.course-instructor{display:flex;align-items:center;gap:6px;font-size:.78rem;color:var(--text-muted);}
/* ── Market card ── */
.mkt-section{background:var(--bg-2);}
.mkt-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;transition:all var(--t-med);}
.mkt-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-lg);}
.mkt-card.hidden{display:none;}
.mkt-card-thumb{height:160px;position:relative;display:flex;align-items:center;justify-content:center;}
.mkt-card-emoji{font-size:3.8rem;filter:drop-shadow(0 4px 8px rgba(0,0,0,.1));}
.mkt-wish{position:absolute;top:10px;right:10px;width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,.9);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--gray-400);transition:all .15s;}
.mkt-wish:hover,.mkt-wish.active{color:#ef4444;}
.mkt-badge{position:absolute;top:10px;left:10px;font-size:.68rem;font-weight:700;padding:3px 9px;border-radius:var(--radius-full);}
.mkt-card-body{padding:14px;}
.mkt-card-cat{font-size:.7rem;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;}
.mkt-card-name{font-family:var(--font-display);font-size:.9rem;font-weight:700;color:var(--text);margin-bottom:6px;}
.mkt-card-seller{font-size:.75rem;color:var(--text-muted);margin-bottom:12px;}
.mkt-card-footer{display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border);padding-top:10px;}
.mkt-price{font-family:var(--font-display);font-size:1.05rem;font-weight:800;color:var(--primary);}
.mkt-unit{font-size:.72rem;color:var(--text-muted);}
.mkt-add-btn{width:34px;height:34px;border-radius:50%;background:var(--primary);color:#fff;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .15s;text-decoration:none;}
.mkt-add-btn:hover{background:var(--primary-dark);transform:scale(1.1);}
/* ── Filter tabs ── */
.mkt-cats{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:28px;}
.mkt-cat{padding:7px 16px;border-radius:var(--radius-full);font-size:.83rem;font-weight:600;background:var(--bg-card);border:1.5px solid var(--border);color:var(--text-muted);cursor:pointer;transition:all .15s;font-family:var(--font-body);}
.mkt-cat:hover,.mkt-cat.active{background:var(--primary);border-color:var(--primary);color:#fff;}
/* ── Innovation card ── */
.innov-card{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:var(--radius-lg);overflow:hidden;transition:all var(--t-med);}
.innov-card:hover{transform:translateY(-4px);background:rgba(255,255,255,.1);}
.innov-card-img{height:200px;position:relative;display:flex;align-items:center;justify-content:center;font-size:4rem;}
.innov-card-body{padding:20px;}
.innov-card-category{font-size:.72rem;font-weight:700;color:var(--green-400);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px;}
.innov-card-title{font-family:var(--font-display);font-size:1rem;font-weight:700;color:#fff;margin-bottom:10px;}
.innov-card-desc{font-size:.83rem;color:rgba(255,255,255,.7);line-height:1.6;margin-bottom:14px;}
.innov-card-footer{display:flex;align-items:center;justify-content:space-between;}
.innov-farmer{display:flex;align-items:center;gap:8px;}
.vote-btn2{display:flex;align-items:center;gap:6px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);border-radius:var(--radius-full);padding:6px 14px;color:#fff;font-size:.82rem;font-weight:700;cursor:pointer;transition:all .15s;font-family:var(--font-body);}
.vote-btn2:hover,.vote-btn2.voted{background:var(--primary);border-color:var(--primary);}
/* ── Story card ── */
.story-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:32px;position:relative;transition:all var(--t-med);}
.story-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-lg);}
.story-quote-icon{font-family:Georgia,serif;font-size:5rem;color:var(--green-200);line-height:.7;margin-bottom:12px;}
.story-result{position:absolute;top:24px;right:24px;background:var(--primary);color:#fff;font-family:var(--font-display);font-size:.82rem;font-weight:800;padding:5px 12px;border-radius:var(--radius-full);}
/* ── Disease CTA Responsive ── */
.disease-cta-grid{display:grid;grid-template-columns:1fr 1fr;gap:80px;align-items:center;}
.disease-feat-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.disease-stats-row{display:flex;align-items:center;gap:20px;flex-wrap:wrap;padding-top:28px;border-top:1px solid rgba(255,255,255,.12);}
@media(max-width:900px){.disease-cta-grid{grid-template-columns:1fr;gap:40px;}.disease-cta-grid > div:last-child{display:none;}.disease-feat-grid{grid-template-columns:1fr;}}
@media(max-width:1100px){.mkt-grid{grid-template-columns:repeat(3,1fr) !important;gap:18px !important;}}
@media(max-width:900px){.mkt-grid{grid-template-columns:repeat(2,1fr) !important;gap:16px !important;}}
@media(max-width:639px){.mkt-grid{grid-template-columns:1fr !important;gap:14px !important;}.mkt-card-thumb{height:170px;}.mkt-card-body{padding:14px 16px;}.mkt-card-name{font-size:.95rem;}.mkt-add-btn{width:44px;height:44px;}.mkt-wish{width:38px;height:38px;}.mkt-price{font-size:1.05rem;}.mkt-unit{font-size:.75rem;}}
@media(max-width:600px){.disease-stats-row{gap:14px;}.disease-stats-row > div:nth-child(even){display:none;}.story-card{padding:24px 20px;}}
</style>
@endsection

@section('content')

{{-- ── WEATHER BAR ── --}}
<div class="weather-bar">
  <div class="container">
    <div class="weather-bar-inner">
      <div class="weather-items">
        <div class="weather-item"><i class="fas fa-sun"></i> Blantyre: 28°C, Sunny</div>
        <div class="weather-item"><i class="fas fa-tint"></i> Humidity: 62%</div>
        <div class="weather-item"><i class="fas fa-wind"></i> Wind: 14 km/h NE</div>
        <div class="weather-item"><i class="fas fa-cloud-rain"></i> Rain expected Friday</div>
      </div>
      @if(isset($activeAlert) && $activeAlert)
        <div class="weather-alert">
          <i class="fas fa-exclamation-triangle"></i>
          {{ Str::limit($activeAlert->title, 60) }} —
          <a href="{{ route('diseases') }}">View Alert →</a>
        </div>
      @else
        <div class="weather-alert">
          <i class="fas fa-seedling"></i> Growing season in progress — check disease alerts
          <a href="{{ route('diseases') }}">View →</a>
        </div>
      @endif
    </div>
  </div>
</div>

{{-- ── HERO ── --}}
<section class="hero" id="hero">
  <div class="hero-blob hero-blob-1"></div>
  <div class="hero-blob hero-blob-2"></div>
  <div class="hero-blob hero-blob-3"></div>
  <div class="hero-inner">
    <div class="hero-content animate-fadeInLeft">
      <div class="hero-tag">
        <div class="hero-tag-dot"></div>
        <span>🌍 Serving {{ isset($stats) ? number_format($stats['total_farmers']) : '12,000' }}+ Farmers Across Malawi</span>
      </div>
      <h1 class="hero-title">
        Empowering Farmers<br/>
        Through <span class="highlight">Smart</span><br/>
        <span class="underline-word">Technology</span>
      </h1>
      <p class="hero-desc">
        The all-in-one platform for Malawan farmers — learn modern techniques,
        buy and sell produce, track deliveries in real-time, and showcase your farming innovations.
      </p>
      <div class="hero-cta">
        @guest
          <a href="{{ route('register') }}" class="btn btn-primary btn-lg"><i class="fas fa-seedling"></i> Start Farming Smarter</a>
          <a href="{{ route('learn') }}"    class="btn btn-outline btn-lg"><i class="fas fa-play"></i> Explore Courses</a>
        @else
          <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg"><i class="fas fa-th-large"></i> Go to Dashboard</a>
          <a href="{{ route('marketplace') }}" class="btn btn-outline btn-lg"><i class="fas fa-store"></i> Browse Market</a>
        @endguest
      </div>
      <div class="hero-trust">
        <div class="hero-trust-item"><i class="fas fa-check-circle"></i> Free to join</div>
        <div class="hero-trust-divider"></div>
        <div class="hero-trust-item"><i class="fas fa-check-circle"></i> No credit card needed</div>
        <div class="hero-trust-divider"></div>
        <div class="hero-trust-item"><i class="fas fa-check-circle"></i> Works fast</div>
      </div>
    </div>
    <div class="hero-visual animate-fadeInUp delay-2">
      <div class="hero-image-wrap">
        <img src="{{ asset('assets/svg/farm-hero.svg') }}" alt="Smart Agriculture Illustration" class="hero-farm-svg"/>
      </div>
      <div class="hero-float-card hero-float-card-1 glass">
        <div class="hero-float-icon" style="background:var(--green-100);color:var(--green-700);"><i class="fas fa-users"></i></div>
        <div>
          <div class="hero-float-val" style="color:var(--green-700);">{{ isset($stats) ? number_format($stats['total_farmers']) : '12,450' }}</div>
          <div class="hero-float-lbl">Active Farmers</div>
        </div>
      </div>
      <div class="hero-float-card hero-float-card-2 glass">
        <div class="hero-float-icon" style="background:#fff7ed;color:#c2410c;"><i class="fas fa-shopping-cart"></i></div>
        <div>
          <div class="hero-float-val" style="color:#c2410c;">{{ isset($stats) ? number_format($stats['products_sold']) : '48,920' }}</div>
          <div class="hero-float-lbl">Products Sold</div>
        </div>
      </div>
      <div class="hero-float-card hero-float-card-3 glass">
        <div class="hero-float-icon" style="background:#e0f2fe;color:#0284c7;"><i class="fas fa-graduation-cap"></i></div>
        <div>
          <div class="hero-float-val" style="color:#0284c7;">{{ isset($stats) ? number_format($stats['active_courses']) : '342' }}</div>
          <div class="hero-float-lbl">Active Courses</div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ── FEATURES STRIP ── --}}
<div class="features-strip">
  <div class="container">
    <div class="features-track">
      @foreach(['fas fa-graduation-cap'=>'300+ Farming Courses','fas fa-store'=>'Agri Marketplace','fas fa-truck'=>'Real-Time Delivery','fas fa-robot'=>'AI Disease Detection','fas fa-sms'=>'Smart SMS Alerts','fas fa-lightbulb'=>'Innovation Hub','fas fa-cloud-sun'=>'Weather Forecasts','fas fa-bug'=>'Crop Disease Library','fas fa-mobile-alt'=>'Mobile Money Payments'] as $icon => $text)
        <div class="feature-item"><i class="{{ $icon }}"></i> {{ $text }}</div>
      @endforeach
      @foreach(['fas fa-graduation-cap'=>'300+ Farming Courses','fas fa-store'=>'Agri Marketplace','fas fa-truck'=>'Real-Time Delivery','fas fa-robot'=>'AI Disease Detection','fas fa-sms'=>'Smart SMS Alerts'] as $icon => $text)
        <div class="feature-item"><i class="{{ $icon }}"></i> {{ $text }}</div>
      @endforeach
    </div>
  </div>
</div>

{{-- ── STATS ── --}}
<section class="stats-section">
  <div class="container">
    <div class="stats-grid">
      @php
        $statsDisplay = [
          ['icon'=>'fas fa-users',       'color'=>'var(--green-700)', 'bg'=>'var(--green-100)', 'val'=> isset($stats) ? $stats['total_farmers']  : 12450, 'label'=>'Registered Farmers'],
          ['icon'=>'fas fa-shopping-cart','color'=>'#c2410c',         'bg'=>'#fff7ed',          'val'=> isset($stats) ? $stats['products_sold']  : 48920, 'label'=>'Products Sold'],
          ['icon'=>'fas fa-graduation-cap','color'=>'var(--sky-600)', 'bg'=>'#e0f2fe',          'val'=> isset($stats) ? $stats['active_courses'] : 342,   'label'=>'Active Courses'],
          ['icon'=>'fas fa-truck',        'color'=>'var(--green-700)', 'bg'=>'#f0fdf4',          'val'=> isset($stats) ? $stats['deliveries']     : 9800,  'label'=>'Successful Deliveries'],
        ];
      @endphp
      @foreach($statsDisplay as $i => $s)
        <div class="stat-item" data-reveal data-reveal-delay="{{ $i*100 }}">
          <div class="stat-icon-wrap" style="background:{{ $s['bg'] }};color:{{ $s['color'] }};"><i class="{{ $s['icon'] }}"></i></div>
          <div class="stat-number" data-count="{{ $s['val'] }}">0</div>
          <div class="stat-label">{{ $s['label'] }}</div>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ── FEATURED COURSES ── --}}
<section class="courses-section section" id="courses">
  <div class="container">
    <div class="section-header">
      <span class="section-label"><i class="fas fa-graduation-cap"></i> Learning Center</span>
      <h2 class="section-title">Learn From The <span>Best Experts</span></h2>
      <p class="section-desc">Modern farming techniques, agribusiness skills and technology training — taught by experienced agricultural professionals.</p>
    </div>
    <div class="courses-filter" id="coursesFilter">
      <button class="filter-btn active" data-filter="all">All Courses</button>
      @foreach(['soil_crops'=>'Soil & Crops','livestock'=>'Livestock','agri_tech'=>'Agri-Tech','agribusiness'=>'Agribusiness','organic'=>'Organic','irrigation'=>'Irrigation'] as $key => $label)
        <button class="filter-btn" data-filter="{{ $key }}">{{ $label }}</button>
      @endforeach
    </div>
    <div class="courses-grid" id="coursesGrid">
      @forelse(isset($featuredCourses) ? $featuredCourses : [] as $course)
        @php
          $emojis = ['soil_crops'=>'🌽','livestock'=>'🐄','agri_tech'=>'🚁','agribusiness'=>'📊','organic'=>'🥦','irrigation'=>'💧','post_harvest'=>'🌾'];
          $emoji  = $emojis[$course->category] ?? '🌱';
          $colors = ['soil_crops'=>'#dcfce7,#bbf7d0','livestock'=>'#fef9c3,#fef08a','agri_tech'=>'#e0f2fe,#bae6fd','agribusiness'=>'#f5f3ff,#ede9fe','organic'=>'#f0fdf4,#dcfce7','irrigation'=>'#e0f2fe,#bae6fd','post_harvest'=>'#fff7ed,#fed7aa'];
          $color  = $colors[$course->category] ?? '#dcfce7,#bbf7d0';
          $hrs    = intdiv($course->total_duration_minutes,60);
          $mins   = $course->total_duration_minutes % 60;
        @endphp
        <div class="course-card" data-reveal data-category="{{ $course->category }}">
          <div class="course-thumb" style="background:linear-gradient(135deg,{{ $color }});">
            <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:4rem;">{{ $emoji }}</div>
            <div class="course-badge-wrap">
              @if($course->access_type === 'free')     <span class="badge badge-green">FREE</span>@endif
              @if($course->access_type === 'premium')  <span class="course-premium-badge"><i class="fas fa-crown"></i> Premium</span>@endif
              @if($course->is_featured)                <span class="badge badge-earth">Bestseller</span>@endif
              @if($course->has_certificate)            <span class="badge badge-sky">🎓 Certificate</span>@endif
            </div>
          </div>
          <div class="course-body">
            <div class="course-category">{{ ucwords(str_replace('_',' ',$course->category)) }}</div>
            <div class="course-title">{{ $course->title }}</div>
            <div class="course-meta">
              <span class="course-meta-item"><i class="fas fa-clock"></i> {{ $hrs }}h {{ $mins }}m</span>
              <span class="course-meta-item"><i class="fas fa-play-circle"></i> {{ $course->total_lessons }} Lessons</span>
              <span class="course-meta-item"><i class="fas fa-star" style="color:#f59e0b;"></i> {{ number_format($course->average_rating,1) }} ({{ number_format($course->total_reviews) }})</span>
            </div>
            <div class="course-footer">
              <div>
                @if($course->access_type === 'free')
                  <div class="course-price course-price-free">FREE</div>
                @else
                  <div class="course-price">{{ $course->currency }} {{ number_format($course->price) }}</div>
                @endif
              </div>
              <div class="course-instructor">
                <div class="avatar avatar-sm" style="background:var(--green-600);color:#fff;font-size:.65rem;">
                  {{ substr($course->instructor->name ?? 'IN',0,2) }}
                </div>
                {{ $course->instructor->display_name ?? $course->instructor->name ?? 'Instructor' }}
              </div>
            </div>
          </div>
        </div>
      @empty
        {{-- Skeleton placeholders when no featured courses exist yet --}}
        @for($i=0;$i<6;$i++)
          <div class="course-card">
            <div class="course-thumb" style="background:var(--bg-2);"></div>
            <div class="course-body">
              <div style="height:12px;background:var(--bg-2);border-radius:6px;margin-bottom:10px;"></div>
              <div style="height:40px;background:var(--bg-2);border-radius:6px;margin-bottom:10px;"></div>
            </div>
          </div>
        @endfor
        <div style="grid-column:1/-1;text-align:center;color:var(--text-muted);padding:40px;">
          <p>No featured courses yet. <a href="{{ route('learn') }}" style="color:var(--primary);">Browse all courses →</a></p>
        </div>
      @endforelse
    </div>
    <div style="text-align:center;margin-top:44px;">
      <a href="{{ route('learn') }}" class="btn btn-outline btn-lg"><i class="fas fa-th-large"></i> View All Courses</a>
    </div>
  </div>
</section>

{{-- ── MARKETPLACE PREVIEW ── --}}
<section class="mkt-section section" id="marketplace">
  <div class="container">
    <div style="display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:20px;margin-bottom:28px;">
      <div>
        <span class="section-label"><i class="fas fa-store"></i> Agri Marketplace</span>
        <h2 class="section-title">Fresh From the <span>Farm</span></h2>
        <p class="section-desc">Buy directly from verified farmers — no middlemen. Seeds, fertilizers, livestock, fresh produce and machinery.</p>
      </div>
      <a href="{{ route('marketplace') }}" class="btn btn-outline btn-md">View All Products <i class="fas fa-arrow-right"></i></a>
    </div>

    {{-- Category Tabs --}}
    <div class="mkt-cats" id="mktCats">
      <button class="mkt-cat active" onclick="filterMkt(this,'all')"><i class="fas fa-th"></i> All</button>
      @foreach(['seeds'=>'🌱 Seeds','fertilizer'=>'🧪 Fertilizers','produce'=>'🍅 Fresh Produce','livestock'=>'🐐 Livestock','tools'=>'⚙️ Tools','equipment'=>'🌊 Equipment'] as $cat => $label)
        <button class="mkt-cat" onclick="filterMkt(this,'{{ $cat }}')">{{ $label }}</button>
      @endforeach
    </div>

    <div class="mkt-grid" id="mktGrid">
      @forelse(isset($featuredProducts) ? $featuredProducts : [] as $product)
        @php
          $pEmojis = ['seeds'=>'🌽','fertilizer'=>'🧪','produce'=>'🍅','livestock'=>'🐐','tools'=>'💧','equipment'=>'⚙️','chemicals'=>'⚗️','other'=>'📦'];
          $pColors = ['seeds'=>'#dcfce7,#bbf7d0','fertilizer'=>'#e0f2fe,#bae6fd','produce'=>'#fef2f2,#fecaca','livestock'=>'#fef9c3,#fef08a','tools'=>'#f0fdf4,#dcfce7','equipment'=>'#f5f3ff,#ede9fe'];
          $pEmoji  = $pEmojis[$product->category] ?? '📦';
          $pColor  = $pColors[$product->category] ?? '#dcfce7,#bbf7d0';
        @endphp
        <div class="mkt-card" data-mkt-cat="{{ $product->category }}">
          <div class="mkt-card-thumb" style="background:linear-gradient(135deg,{{ $pColor }});">
            <div class="mkt-card-emoji">{{ $pEmoji }}</div>
            @auth
              <button class="mkt-wish" onclick="toggleWishlist(this,{{ $product->id }})" title="Add to wishlist">
                <i class="{{ Auth::user()->hasWishlisted($product) ? 'fas' : 'far' }} fa-heart" style="{{ Auth::user()->hasWishlisted($product) ? 'color:#ef4444' : '' }}"></i>
              </button>
            @endauth
            @if($product->is_featured) <span class="mkt-badge" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;">🔥 Hot</span>
            @elseif($product->total_sold < 5) <span class="mkt-badge" style="background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;">New</span>
            @elseif($product->category === 'produce') <span class="mkt-badge" style="background:var(--green-50);color:var(--green-700);border:1px solid var(--green-200);">✓ Fresh</span>
            @endif
          </div>
          <div class="mkt-card-body">
            <div class="mkt-card-cat">{{ ucfirst($product->category) }}</div>
            <div class="mkt-card-name">{{ $product->name }}</div>
            <div style="display:flex;align-items:center;gap:4px;margin-bottom:4px;">
              <span style="color:#f59e0b;font-size:.82rem;">{{ str_repeat('★', round($product->average_rating)) }}{{ str_repeat('☆', 5 - round($product->average_rating)) }}</span>
              <span style="font-size:.72rem;color:var(--text-muted);">({{ $product->total_reviews }})</span>
            </div>
            <div class="mkt-card-seller"><i class="fas fa-store" style="color:var(--primary);font-size:.7rem;"></i> {{ $product->seller->full_name ?? 'Verified Seller' }} · {{ $product->district }}</div>
            <div class="mkt-card-footer">
              <div><span class="mkt-price">{{ $product->currency }} {{ number_format($product->price) }}</span><span class="mkt-unit">/{{ $product->unit }}</span></div>
              @auth
                @if($product->in_stock)
                  <form method="POST" action="{{ route('cart.add', $product) }}" style="display:inline;">
                    @csrf
                    <input type="hidden" name="quantity" value="1"/>
                    <button type="submit" class="mkt-add-btn" title="Add to cart"><i class="fas fa-cart-plus"></i></button>
                  </form>
                @else
                  <button class="mkt-add-btn" style="background:var(--gray-300);cursor:not-allowed;" disabled title="Out of stock"><i class="fas fa-times"></i></button>
                @endif
              @else
                <a href="{{ route('login') }}" class="mkt-add-btn" title="Sign in to buy"><i class="fas fa-cart-plus"></i></a>
              @endauth
            </div>
          </div>
        </div>
      @empty
        <div style="grid-column:1/-1;text-align:center;padding:60px;color:var(--text-muted);">
          <i class="fas fa-store" style="font-size:3rem;margin-bottom:16px;display:block;opacity:.3;"></i>
          No featured products yet. <a href="{{ route('marketplace') }}" style="color:var(--primary);">Browse marketplace →</a>
        </div>
      @endforelse
    </div>

    {{-- Seller CTA --}}
    <div style="background:linear-gradient(135deg,var(--green-700),var(--green-900));border-radius:var(--radius-xl);padding:28px 36px;margin-top:36px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:20px;">
      <div style="display:flex;align-items:center;gap:18px;">
        <div style="width:52px;height:52px;border-radius:14px;background:rgba(255,255,255,.12);display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0;">🛒</div>
        <div>
          <div style="font-family:var(--font-display);font-size:1.05rem;font-weight:800;color:#fff;margin-bottom:4px;">Are you a farmer or agri-business?</div>
          <div style="font-size:.86rem;color:rgba(255,255,255,.75);">List your products for free and reach {{ isset($stats) ? number_format($stats['total_farmers']) : '12,000' }}+ buyers across Malawi and Zambia.</div>
        </div>
      </div>
      <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="{{ route('register') }}" class="btn btn-primary btn-md"><i class="fas fa-plus"></i> Start Selling Free</a>
        <a href="{{ route('marketplace') }}" class="btn btn-outline btn-md" style="color:#fff;border-color:rgba(255,255,255,.4);"><i class="fas fa-shopping-basket"></i> Browse All</a>
      </div>
    </div>
  </div>
</section>

{{-- ── INNOVATION SHOWCASE ── --}}
<section class="innovation-section section">
  <div class="container innovation-inner">
    <div class="section-header center">
      <span class="section-label" style="background:rgba(250,204,21,.1);color:var(--earth-400);border-color:rgba(250,204,21,.3);"><i class="fas fa-lightbulb"></i> Innovation Hub</span>
      <h2 class="section-title">Farmer <span style="color:var(--earth-400);">Innovations</span></h2>
      <p class="section-desc">Real farmers solving real problems. Vote for your favourite innovations.</p>
    </div>
    <div class="innovation-grid" id="innovationGrid">
      @forelse(isset($topInnovations) ? $topInnovations : [] as $innovation)
        @php
          $catEmojis = ['water_management'=>'☀️','technology'=>'📱','infrastructure'=>'♻️','energy'=>'⚡','crop_solutions'=>'🏺','livestock'=>'🐄','business'=>'📊','post_harvest'=>'🌾'];
          $catColors = ['water_management'=>'#fef9c3,#fbbf24','technology'=>'#dcfce7,#4ade80','infrastructure'=>'#e0f2fe,#38bdf8','energy'=>'#f3e8ff,#a855f7','crop_solutions'=>'#fff7ed,#fb923c'];
          $iEmoji = $catEmojis[$innovation->category] ?? '💡';
          $iColor = $catColors[$innovation->category] ?? '#dcfce7,#4ade80';
        @endphp
        <div class="innov-card">
          <div class="innov-card-img" style="background:linear-gradient(135deg,{{ $iColor }});">
            {{ $iEmoji }}
            <span style="position:absolute;top:12px;right:12px;background:rgba(0,0,0,.5);color:#fff;font-size:.72rem;font-weight:700;padding:3px 9px;border-radius:20px;">
              <i class="fas fa-eye"></i> {{ number_format($innovation->view_count) }}
            </span>
          </div>
          <div class="innov-card-body">
            <div class="innov-card-category">{{ ucwords(str_replace('_',' ',$innovation->category)) }}</div>
            <div class="innov-card-title">{{ $innovation->title }}</div>
            <p class="innov-card-desc">{{ Str::limit($innovation->description, 120) }}</p>
            @if($innovation->impact_summary)
              <div style="display:flex;align-items:center;gap:6px;font-size:.78rem;color:var(--green-400);font-weight:600;margin-bottom:14px;">
                <i class="fas fa-chart-line"></i> {{ $innovation->impact_summary }}
              </div>
            @endif
            <div class="innov-card-footer">
              <div class="innov-farmer">
                <div class="avatar avatar-sm" style="background:var(--green-700);color:#fff;font-size:.65rem;">{{ $innovation->user->initials ?? 'FA' }}</div>
                <div>
                  <div style="font-weight:600;color:#fff;font-size:.82rem;">{{ $innovation->user->full_name ?? 'Farmer' }}</div>
                  <div style="font-size:.74rem;color:rgba(255,255,255,.6);">{{ $innovation->district ?? 'Malawi' }}</div>
                </div>
              </div>
              @auth
                <form method="POST" action="{{ route('innovation.vote', $innovation) }}" style="display:inline;">
                  @csrf
                  <button type="submit" class="vote-btn2">
                    <i class="fas fa-thumbs-up"></i> {{ number_format($innovation->vote_count) }}
                  </button>
                </form>
              @else
                <a href="{{ route('login') }}" class="vote-btn2">
                  <i class="fas fa-thumbs-up"></i> {{ number_format($innovation->vote_count) }}
                </a>
              @endauth
            </div>
          </div>
        </div>
      @empty
        <div style="grid-column:1/-1;text-align:center;padding:60px;color:rgba(255,255,255,.5);">
          <i class="fas fa-lightbulb" style="font-size:3rem;margin-bottom:16px;display:block;opacity:.3;"></i>
          No innovations yet. Be the first to submit!
        </div>
      @endforelse
    </div>
    <div style="text-align:center;margin-top:48px;">
      @auth
        <a href="{{ route('innovation.store') }}" class="btn btn-white btn-lg"><i class="fas fa-plus"></i> Submit Your Innovation</a>
      @else
        <a href="{{ route('register') }}" class="btn btn-white btn-lg"><i class="fas fa-plus"></i> Join & Submit Your Innovation</a>
      @endauth
    </div>
  </div>
</section>

{{-- ── SUCCESS STORIES ── --}}
<section class="section">
  <div class="container">
    <div class="section-header center">
      <span class="section-label"><i class="fas fa-star"></i> Success Stories</span>
      <h2 class="section-title">Farmers Who <span>Transformed</span> Their Lives</h2>
    </div>
    <div class="stories-grid">
      @foreach([
        ['initials'=>'CM','bg'=>'#16a34a,#15803d','name'=>'Charles Mwale','role'=>'Maize Farmer, Eastern Province','text'=>'AgriTech Pro\'s drone farming course completely changed how I manage my 20-hectare maize farm. I\'ve reduced input costs by 35% and increased yield by over 60% in just one season.','result'=>'+60% Yield','stars'=>5],
        ['initials'=>'GN','bg'=>'#f97316,#ea580c','name'=>'Grace Nkosi','role'=>'Vegetable Farmer, Southern Province','text'=>'The marketplace connected me directly to buyers in Lusaka. No more middlemen taking most of my profit. My income tripled within 4 months of joining the platform.','result'=>'3x Income','stars'=>5],
        ['initials'=>'PT','bg'=>'#0ea5e9,#0284c7','name'=>'Peter Tembo','role'=>'Irrigation Innovator, Copperbelt','text'=>'I submitted my rainwater harvesting innovation, won the K10,000 competition prize, and now three other farmers in my district are using the same system.','result'=>'K10K Won','stars'=>5],
      ] as $story)
        <div class="story-card" data-reveal>
          <div class="story-quote-icon">"</div>
          <p class="story-text">{{ $story['text'] }}</p>
          <div class="story-author">
            <div class="avatar avatar-md" style="background:linear-gradient(135deg,{{ $story['bg'] }});color:#fff;">{{ $story['initials'] }}</div>
            <div class="story-author-info">
              <strong>{{ $story['name'] }}</strong>
              <span>{{ $story['role'] }}</span>
              <div class="stars" style="margin-top:3px;color:#f59e0b;">{{ str_repeat('★',$story['stars']) }}</div>
            </div>
          </div>
          <div class="story-result">{{ $story['result'] }}</div>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ── DISEASE CTA SECTION ── --}}
<section class="section" style="background:linear-gradient(135deg,#0d2b0d 0%,#1a4a1e 40%,#0d2b0d 100%);position:relative;overflow:hidden;">
  <div style="position:absolute;inset:0;background:radial-gradient(ellipse at 20% 50%,rgba(74,222,128,.12),transparent 50%),radial-gradient(ellipse at 80% 30%,rgba(239,68,68,.1),transparent 45%);pointer-events:none;"></div>
  <div class="container" style="position:relative;z-index:1;">
    <div class="disease-cta-grid">
      {{-- Left --}}
      <div>
        <span class="section-label" style="background:rgba(239,68,68,.1);color:#ef4444;border-color:rgba(239,68,68,.25);"><i class="fas fa-microscope"></i> AI-Powered Detection</span>
        <h2 style="font-family:var(--font-display);font-size:clamp(2rem,4vw,3.2rem);font-weight:800;color:#fff;line-height:1.12;letter-spacing:-.02em;margin-bottom:20px;">
          Stop Crop Diseases<br/>
          <span style="background:linear-gradient(90deg,#f87171,#ef4444);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Before They Destroy</span><br/>
          Your Harvest
        </h2>
        <p style="font-size:1rem;color:rgba(255,255,255,.75);line-height:1.78;margin-bottom:32px;">
          Upload a photo of your sick plant and our AI identifies the disease in seconds —
          then gives you the exact treatment steps, recommended chemicals and prevention guide.
        </p>
        <div class="disease-feat-grid" style="margin-bottom:36px;">
          @foreach([
            ['bg'=>'#fef2f2','color'=>'#ef4444','icon'=>'fas fa-camera','title'=>'Photo Detection','desc'=>'Take a photo — get results in 2 seconds'],
            ['bg'=>'var(--green-50)','color'=>'var(--green-700)','icon'=>'fas fa-book-open','title'=>'200+ Disease Library','desc'=>'Full treatment & prevention guides'],
            ['bg'=>'#fff7ed','color'=>'#c2410c','icon'=>'fas fa-bell','title'=>'SMS Outbreak Alerts','desc'=>'Know before it reaches your farm'],
            ['bg'=>'#e0f2fe','color'=>'var(--sky-600)','icon'=>'fas fa-flask','title'=>'Chemical Recommendations','desc'=>'Right product, right dose, right time'],
          ] as $feat)
            <div style="display:flex;align-items:flex-start;gap:12px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:var(--radius-md);padding:14px;">
              <div style="width:38px;height:38px;border-radius:10px;background:{{ $feat['bg'] }};color:{{ $feat['color'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="{{ $feat['icon'] }}"></i>
              </div>
              <div>
                <div style="font-size:.87rem;font-weight:700;color:#fff;margin-bottom:2px;">{{ $feat['title'] }}</div>
                <div style="font-size:.76rem;color:rgba(255,255,255,.6);line-height:1.4;">{{ $feat['desc'] }}</div>
              </div>
            </div>
          @endforeach
        </div>
        <div style="display:flex;gap:14px;flex-wrap:wrap;margin-bottom:36px;">
          <a href="{{ route('diseases') }}" class="btn btn-lg" style="background:#ef4444;color:#fff;box-shadow:0 4px 20px rgba(239,68,68,.4);">
            <i class="fas fa-microscope"></i> Detect Disease Now — Free
          </a>
          <a href="{{ route('diseases') }}" class="btn btn-outline btn-lg" style="color:#fff;border-color:rgba(255,255,255,.4);">
            <i class="fas fa-book"></i> Disease Library
          </a>
        </div>
        <div class="disease-stats-row">
          @foreach(['92%'=>'Detection Accuracy','200+'=>'Diseases in Library','2 sec'=>'AI Analysis','Free'=>'Always Free'] as $val => $lbl)
            <div>
              <div style="font-family:var(--font-display);font-size:1.6rem;font-weight:800;color:#fff;">{{ $val }}</div>
              <div style="font-size:.72rem;color:rgba(255,255,255,.55);margin-top:2px;">{{ $lbl }}</div>
            </div>
            @if(!$loop->last)<div style="width:1px;height:36px;background:rgba(255,255,255,.15);"></div>@endif
          @endforeach
        </div>
      </div>
      {{-- Right --}}
      <div style="display:flex;align-items:center;justify-content:center;gap:32px;padding:40px 0;">
        {{-- Phone mockup --}}
        <div style="position:relative;">
          <div style="width:200px;background:#0a1a0a;border-radius:30px;padding:16px 10px 14px;border:2px solid rgba(74,222,128,.3);box-shadow:0 20px 60px rgba(0,0,0,.6);">
            <div style="width:60px;height:10px;background:#050e05;border-radius:6px;margin:0 auto 10px;"></div>
            <div style="background:#0d1f0d;border-radius:18px;overflow:hidden;padding:0 0 8px;">
              <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 10px 6px;background:rgba(22,163,74,.15);border-bottom:1px solid rgba(74,222,128,.15);">
                <span style="font-size:.62rem;font-weight:700;color:rgba(255,255,255,.85);">🔬 Disease Scanner</span>
                <span style="font-size:.55rem;font-weight:700;background:#22c55e;color:#fff;padding:2px 6px;border-radius:6px;">AI Active</span>
              </div>
              <div style="margin:8px;height:130px;background:#061206;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:3rem;">🌿</div>
              <div style="margin:0 8px 6px;background:rgba(239,68,68,.15);border:1px solid rgba(239,68,68,.3);border-radius:8px;padding:7px 8px;display:flex;align-items:center;justify-content:space-between;">
                <div>
                  <div style="font-size:.62rem;font-weight:700;color:#fff;">Fall Armyworm</div>
                  <div style="font-size:.55rem;color:#fca5a5;">Maize · High Risk</div>
                </div>
                <div style="text-align:center;">
                  <div style="font-family:var(--font-display);font-size:.9rem;font-weight:800;color:#4ade80;">94%</div>
                  <div style="font-size:.52rem;color:rgba(255,255,255,.5);">match</div>
                </div>
              </div>
              <div style="display:flex;gap:5px;margin:0 8px;">
                <button style="flex:1;background:#22c55e;color:#fff;border:none;border-radius:6px;font-size:.58rem;font-weight:700;padding:5px 4px;cursor:pointer;">View Treatment</button>
                <button style="flex:1;background:rgba(255,255,255,.1);color:rgba(255,255,255,.8);border:none;border-radius:6px;font-size:.58rem;font-weight:700;padding:5px 4px;cursor:pointer;">Scan Again</button>
              </div>
            </div>
          </div>
          <div style="position:absolute;top:-14px;right:-20px;background:#fff;border-radius:20px;padding:6px 12px;display:flex;align-items:center;gap:5px;font-size:.75rem;font-weight:700;color:var(--text);box-shadow:0 4px 16px rgba(0,0,0,.3);">
            <i class="fas fa-check-circle" style="color:#4ade80;"></i> 94% Accurate
          </div>
        </div>
        {{-- Chemical bottle SVG --}}
        <div style="position:relative;">
          <svg width="110" height="260" viewBox="0 0 140 280" xmlns="http://www.w3.org/2000/svg" style="filter:drop-shadow(0 12px 24px rgba(0,0,0,.5));">
            <ellipse cx="70" cy="268" rx="38" ry="8" fill="rgba(0,0,0,.2)"/>
            <path d="M28,80 Q22,90 20,110 L20,240 Q20,255 35,258 L105,258 Q120,255 120,240 L120,110 Q118,90 112,80 Z" fill="#1a4a1e"/>
            <path d="M48,40 Q44,55 28,80 L112,80 Q96,55 92,40 Z" fill="#1a4a1e"/>
            <rect x="50" y="20" width="40" height="24" rx="6" fill="#15803d"/>
            <rect x="44" y="8" width="52" height="18" rx="8" fill="#4ade80"/>
            <rect x="86" y="0" width="8" height="14" rx="3" fill="#22c55e"/>
            <rect x="26" y="95" width="88" height="140" rx="6" fill="#f0fdf4"/>
            <rect x="26" y="95" width="88" height="28" rx="6" fill="#15803d"/>
            <rect x="26" y="113" width="88" height="10" fill="#15803d"/>
            <text x="70" y="113" text-anchor="middle" font-family="sans-serif" font-size="10" font-weight="800" fill="#fff">AGRITECH PRO</text>
            <polygon points="70,132 60,148 80,148" fill="#fbbf24"/>
            <text x="70" y="146" text-anchor="middle" font-family="Arial" font-size="8" font-weight="900" fill="#92400e">!</text>
            <text x="70" y="162" text-anchor="middle" font-family="sans-serif" font-size="9" font-weight="800" fill="#14532d">ARMYWORM</text>
            <text x="70" y="174" text-anchor="middle" font-family="sans-serif" font-size="9" font-weight="800" fill="#14532d">CONTROL</text>
            <line x1="32" y1="180" x2="108" y2="180" stroke="#d1fae5" stroke-width="1"/>
            <text x="70" y="192" text-anchor="middle" font-family="Arial" font-size="6.5" fill="#166534">Chlorantraniliprole 200g/L</text>
            <rect x="32" y="198" width="76" height="20" rx="4" fill="#f0fdf4"/>
            <text x="70" y="207" text-anchor="middle" font-family="Arial" font-size="6" fill="#166534">DOSAGE: 10ml / 20L water</text>
            <text x="70" y="215" text-anchor="middle" font-family="Arial" font-size="6" fill="#166534">Apply into maize whorl</text>
            <rect x="26" y="225" width="88" height="10" rx="3" fill="#dcfce7"/>
            <text x="70" y="232" text-anchor="middle" font-family="Arial" font-size="5.5" fill="#14532d" font-weight="700">APPROVED BY MALAWI MoAF</text>
          </svg>
          <div style="position:absolute;bottom:-10px;left:-50px;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);padding:8px 12px;display:flex;align-items:center;gap:8px;box-shadow:0 4px 20px rgba(0,0,0,.3);white-space:nowrap;">
            <i class="fas fa-store" style="color:var(--green-600);"></i>
            <div>
              <div style="font-size:.75rem;font-weight:700;color:var(--text);">Available in Marketplace</div>
              <div style="font-size:.7rem;color:var(--primary);font-weight:600;">From K 185 / bottle</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@include('partials.footer')
@endsection

@section('extra_js')
<script src="{{ asset('js/home.js') }}"></script>
<script>
/* Marketplace category filter */
function filterMkt(btn, cat) {
  document.querySelectorAll('.mkt-cat').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.querySelectorAll('#mktGrid .mkt-card').forEach(card => {
    const show = cat === 'all' || card.dataset.mktCat === cat;
    card.classList.toggle('hidden', !show);
  });
}

/* Wishlist toggle via AJAX */
function toggleWishlist(btn, productId) {
  fetch(`/marketplace/products/${productId}/wishlist`, {
    method:'POST',
    headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'}
  }).then(r => r.json()).then(data => {
    const icon = btn.querySelector('i');
    if (data.added) {
      icon.className = 'fas fa-heart';
      icon.style.color = '#ef4444';
      btn.classList.add('active');
      showToast('❤️ Added to wishlist!', 'success');
    } else {
      icon.className = 'far fa-heart';
      icon.style.color = '';
      btn.classList.remove('active');
      showToast('Removed from wishlist', 'info');
    }
  });
}
</script>
@endsection
