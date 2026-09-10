@extends('layouts.app')
@section('title', 'AgriTech Pro Weather, courses & markets for Malawian farmers')
@section('extra_css')
<link rel="stylesheet" href="{{ asset('css/home.css') }}"/>
<style>
/* ── Innovation carousel (page-specific) ── */
.innov-carousel{position:relative;max-width:680px;margin:0 auto;}
.innov-carousel-track{position:relative;height:440px;}
.innov-carousel-card{position:absolute;inset:0;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;opacity:0;transform:translateX(60px) scale(.95);transition:all .5s cubic-bezier(.4,0,.2,1);pointer-events:none;box-shadow:var(--shadow-md);cursor:pointer;}
.innov-carousel-card:hover{box-shadow:var(--shadow-lg);border-color:var(--green-200);}
.innov-carousel-card.active{opacity:1;transform:translateX(0) scale(1);pointer-events:auto;}
.innov-carousel-card.exit{opacity:0;transform:translateX(-60px) scale(.95);}
.innov-carousel-img{height:180px;display:flex;align-items:center;justify-content:center;font-size:4rem;position:relative;}
.innov-carousel-body{padding:22px;}
.innov-carousel-cat{font-size:.68rem;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;}
.innov-carousel-title{font-size:1.15rem;font-weight:700;color:var(--text);margin-bottom:8px;line-height:1.35;}
.innov-carousel-desc{font-size:.85rem;color:var(--text-muted);line-height:1.65;margin-bottom:14px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.innov-carousel-impact{display:inline-flex;align-items:center;gap:6px;font-size:.78rem;color:var(--green-700);font-weight:600;background:var(--green-50);border:1px solid var(--green-200);padding:5px 12px;border-radius:var(--radius-sm);margin-bottom:14px;}
.innov-carousel-footer{display:flex;align-items:center;justify-content:space-between;}
.innov-carousel-farmer{display:flex;align-items:center;gap:8px;}
.innov-carousel-farmer .avatar{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.6rem;font-weight:700;flex-shrink:0;}
.innov-carousel-farmer-name{font-weight:600;color:var(--text);font-size:.82rem;}
.innov-carousel-farmer-loc{font-size:.72rem;color:var(--text-muted);}
/* Arrows */
.innov-arrow{position:absolute;top:50%;transform:translateY(-50%);width:42px;height:42px;border-radius:50%;background:var(--bg-card);border:1px solid var(--border);color:var(--text);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s;z-index:10;font-size:.85rem;box-shadow:var(--shadow-md);}
.innov-arrow:hover{background:var(--primary);border-color:var(--primary);color:#fff;}
.innov-arrow.prev{left:-16px;}
.innov-arrow.next{right:-16px;}
/* Dots */
.innov-dots{display:flex;justify-content:center;gap:8px;margin-top:20px;}
.innov-dot{width:8px;height:8px;border-radius:50%;background:var(--gray-300);cursor:pointer;transition:all .3s;}
.innov-dot.active{background:var(--primary);width:24px;border-radius:4px;}
/* View badge */
.innov-views-badge{position:absolute;top:14px;right:14px;background:rgba(0,0,0,.55);color:#fff;font-size:.72rem;font-weight:700;padding:4px 10px;border-radius:20px;display:flex;align-items:center;gap:5px;}
@media(max-width:700px){.innov-carousel-track{height:auto;min-height:420px;}.innov-carousel-card{position:relative;opacity:1;transform:none;pointer-events:auto;}.innov-carousel-card:not(.active){display:none;}.innov-arrow{display:none;}.innov-dots{display:flex;}}
/* ── Disease CTA grid (page-specific) ── */
.disease-cta-grid{display:grid;grid-template-columns:1fr 1fr;gap:64px;align-items:center;}
.disease-feat-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.disease-stats-row{display:flex;align-items:center;gap:20px;flex-wrap:wrap;padding-top:24px;border-top:1px solid rgba(255,255,255,.12);}
@media(max-width:900px){.disease-cta-grid{grid-template-columns:1fr;gap:40px;}.disease-cta-grid > div:last-child{display:none;}.disease-feat-grid{grid-template-columns:1fr;}}
/* ── Marketplace helpers (page-specific) ── */
.mkt-card.hidden{display:none;}
@media(max-width:1100px){.mkt-grid{grid-template-columns:repeat(3,1fr) !important;gap:18px !important;}}
@media(max-width:900px){.mkt-grid{grid-template-columns:repeat(2,1fr) !important;gap:16px !important;}}
@media(max-width:639px){.mkt-grid{grid-template-columns:1fr !important;gap:14px !important;}.mkt-card-thumb{height:170px;}.mkt-card-body{padding:14px 16px;}.mkt-card-name{font-size:.95rem;}.mkt-add-btn{width:44px;height:44px;}.mkt-wish{width:38px;height:38px;}.mkt-price{font-size:1.05rem;}.mkt-unit{font-size:.75rem;}}
@media(max-width:600px){.disease-stats-row{gap:14px;}.disease-stats-row > div:nth-child(even){display:none;}}
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
  <div class="hero-inner">
    <div class="hero-content animate-fadeInLeft">
      <span class="hero-tag"><span class="hero-tag-dot"></span> Made for Malawian farmers</span>
      <h1 class="hero-title">
        Better farming<br/>
        starts with <span class="highlight">better</span><br/>
        <span class="underline-word">decisions</span>
      </h1>
      <p class="hero-desc">
        District-level weather forecasts, hands-on farming courses, a marketplace
        that connects you straight to the buyer, and crop disease detection.
        One place, built for how Malawian farmers actually work.
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
        <div class="hero-trust-item"><i class="fas fa-check-circle"></i> Open to all farmers</div>
        <div class="hero-trust-divider"></div>
        <div class="hero-trust-item"><i class="fas fa-check-circle"></i> Direct farmer sales</div>
        <div class="hero-trust-divider"></div>
        <div class="hero-trust-item"><i class="fas fa-check-circle"></i> Weather by district</div>
      </div>
    </div>
    <div class="hero-visual animate-fadeInUp delay-2">
      <div class="hero-image-wrap">
        <img src="{{ asset('assets/img/agri/farm-aerial.jpg') }}" alt="Aerial view of farmland" class="hero-farm-svg"/>
      </div>
      <div class="hero-float-card hero-float-card-1">
        <div class="hero-float-icon" style="background:var(--green-100);color:var(--green-700);"><i class="fas fa-users"></i></div>
        <div>
          <div class="hero-float-val" style="color:var(--green-700);">{{ number_format($stats['total_farmers']) }}</div>
          <div class="hero-float-lbl">Registered Farmers</div>
        </div>
      </div>
      <div class="hero-float-card hero-float-card-2">
        <div class="hero-float-icon" style="background:#fff7ed;color:#c2410c;"><i class="fas fa-shopping-cart"></i></div>
        <div>
          <div class="hero-float-val" style="color:#c2410c;">{{ number_format($stats['products_sold']) }}</div>
          <div class="hero-float-lbl">Products Sold</div>
        </div>
      </div>
      <div class="hero-float-card hero-float-card-3">
        <div class="hero-float-icon" style="background:#e0f2fe;color:#0284c7;"><i class="fas fa-graduation-cap"></i></div>
        <div>
          <div class="hero-float-val" style="color:#0284c7;">{{ number_format($stats['active_courses']) }}</div>
          <div class="hero-float-lbl">Active Courses</div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ── STATS ── --}}
<section class="stats-section">
  <div class="container">
    <div class="stats-grid">
      @php
        $statsDisplay = [
          ['icon'=>'fas fa-users',       'color'=>'var(--green-700)', 'bg'=>'var(--green-100)', 'val'=> $stats['total_farmers'],  'label'=>'Registered Farmers'],
          ['icon'=>'fas fa-shopping-cart','color'=>'#c2410c',         'bg'=>'#fff7ed',          'val'=> $stats['products_sold'],  'label'=>'Products Sold'],
          ['icon'=>'fas fa-graduation-cap','color'=>'var(--sky-600)', 'bg'=>'#e0f2fe',          'val'=> $stats['active_courses'], 'label'=>'Active Courses'],
          ['icon'=>'fas fa-truck',        'color'=>'var(--green-700)', 'bg'=>'#f0fdf4',          'val'=> $stats['deliveries'],     'label'=>'Successful Deliveries'],
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
      <p class="section-desc">Hands-on courses in modern techniques, agribusiness and farm technology, taught by working agricultural professionals.</p>
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
          $courseImgs = ['soil_crops'=>'maize-field.jpg','livestock'=>'cows.jpg','agri_tech'=>'agritech-drone.jpg','agribusiness'=>'market-stall.jpg','organic'=>'veggies.jpg','irrigation'=>'irrigation.jpg','post_harvest'=>'harvest.jpg'];
          $cImg  = asset('assets/img/agri/'.($courseImgs[$course->category] ?? 'seedling.jpg'));
          $hrs    = intdiv($course->total_duration_minutes,60);
          $mins   = $course->total_duration_minutes % 60;
        @endphp
        <div class="course-card" data-reveal data-category="{{ $course->category }}">
          <div class="course-thumb" style="background:var(--bg-2);">
            <img src="{{ $cImg }}" alt="{{ $course->title }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;"/>
            <div class="course-badge-wrap">
              @if($course->access_type === 'free')     <span class="badge badge-green">Open</span>@endif
              @if($course->access_type === 'premium')  <span class="course-premium-badge"><i class="fas fa-crown"></i> Premium</span>@endif
              @if($course->is_featured)                <span class="badge badge-earth">Bestseller</span>@endif
              @if($course->has_certificate)            <span class="badge badge-sky"><i class="fas fa-graduation-cap"></i> Certificate</span>@endif
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
                  <div class="course-price course-price-free">Open</div>
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
        <p class="section-desc">Fresh produce, seeds, fertilizers, livestock and machinery, sold straight by the farmers who grow them.</p>
      </div>
      <a href="{{ route('marketplace') }}" class="btn btn-outline btn-md">View All Products <i class="fas fa-arrow-right"></i></a>
    </div>

    {{-- Category Tabs --}}
    <div class="mkt-cats" id="mktCats">
      <button class="mkt-cat active" onclick="filterMkt(this,'all')"><i class="fas fa-th"></i> All</button>
      @foreach(['seeds'=>'Seeds','fertilizer'=>'Fertilizers','produce'=>'Fresh Produce','livestock'=>'Livestock','tools'=>'Tools','equipment'=>'Equipment'] as $cat => $label)
        <button class="mkt-cat" onclick="filterMkt(this,'{{ $cat }}')"><i class="fas {{ \App\Support\CategoryIcons::product($cat) }}"></i> {{ $label }}</button>
      @endforeach
    </div>

    <div class="mkt-grid" id="mktGrid">
      @forelse(isset($featuredProducts) ? $featuredProducts : [] as $product)
        @php
          $pImgs = ['seeds'=>'seedling.jpg','fertilizer'=>'spraying2.jpg','produce'=>'tomato.jpg','livestock'=>'cows.jpg','tools'=>'irrigation.jpg','equipment'=>'agritech-drone.jpg','chemicals'=>'spraying2.jpg','other'=>'market-stall.jpg'];
          $pCover = $product->thumbnail
                      ? asset('storage/'.$product->thumbnail)
                      : asset('assets/img/agri/'.($pImgs[$product->category] ?? 'market-stall.jpg'));
        @endphp
        <div class="mkt-card" data-mkt-cat="{{ $product->category }}">
          <div class="mkt-card-thumb" style="background:var(--bg-2);">
            <img src="{{ $pCover }}" alt="{{ $product->name }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;"/>
            @auth
              <button class="mkt-wish" onclick="toggleWishlist(this,{{ $product->id }})" title="Add to wishlist">
                <i class="{{ $product->wishlistedBy->isNotEmpty() ? 'fas' : 'far' }} fa-heart" style="{{ $product->wishlistedBy->isNotEmpty() ? 'color:#ef4444' : '' }}"></i>
              </button>
            @endauth
            @if($product->is_featured) <span class="mkt-badge" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;">Hot</span>
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
          <i class="fas fa-store" style="font-size:1.75rem;margin-bottom:14px;display:block;opacity:.3;"></i>
          No featured products yet. <a href="{{ route('marketplace') }}" style="color:var(--primary);">Browse marketplace →</a>
        </div>
      @endforelse
    </div>

    {{-- Seller CTA --}}
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:28px 36px;margin-top:36px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:20px;box-shadow:var(--shadow-xs);">
      <div style="display:flex;align-items:center;gap:18px;">
        <div style="width:52px;height:52px;border-radius:14px;background:var(--green-100);color:var(--green-700);display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;"><i class="fas fa-store"></i></div>
        <div>
          <div style="font-size:1.0625rem;font-weight:800;color:var(--text);margin-bottom:4px;">Are you a farmer or agri-business?</div>
          <div style="font-size:.86rem;color:var(--text-muted);">List your products and reach {{ number_format($stats['total_farmers']) }} registered farmers across Malawi.</div>
        </div>
      </div>
      <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="{{ route('register') }}" class="btn btn-primary btn-md"><i class="fas fa-plus"></i> Start Selling</a>
        <a href="{{ route('marketplace') }}" class="btn btn-outline btn-md"><i class="fas fa-shopping-basket"></i> Browse All</a>
      </div>
    </div>
  </div>
</section>

 {{-- ── INNOVATION SHOWCASE ── --}}
<section class="section innovation-section">
  <div class="container">
    <div class="section-header center">
      <span class="section-label"><i class="fas fa-lightbulb"></i> Innovation Hub</span>
      <h2 class="section-title">Farmer <span>Innovations</span></h2>
      <p class="section-desc">Real farmers solving real problems. Click any innovation to explore it — and cast your vote for this round's champion.</p>
    </div>

    @php
      $innovations = isset($topInnovations) ? $topInnovations->values() : collect();
      $catImgs = ['water_management'=>'irrigation.jpg','technology'=>'agritech-drone.jpg','infrastructure'=>'greenhouse.jpg','energy'=>'sunflower.jpg','crop_solutions'=>'maize-field.jpg','livestock'=>'cows.jpg','business'=>'market-stall.jpg','post_harvest'=>'harvest.jpg'];
      $myVotes = auth()->check() ? auth()->user()->innovationVotes()->pluck('innovation_id')->all() : [];
    @endphp

    @if($innovations->count() > 0)
    <div class="innov-carousel" id="innovCarousel">
      <div class="innov-carousel-track">
        @foreach($innovations as $idx => $inn)
          @php
            $iCover = asset('assets/img/agri/'.($catImgs[$inn->category] ?? 'seedling.jpg'));
            $iVoted = in_array($inn->id, $myVotes);
          @endphp
          <div class="innov-carousel-card {{ $idx === 0 ? 'active' : '' }}" data-idx="{{ $idx }}" title="View details & vote" onclick="window.location='{{ route('innovation.show', $inn) }}'">
            <div class="innov-carousel-img">
              <img src="{{ $iCover }}" alt="{{ $inn->title }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;"/>
              @if($inn->images && count($inn->images) > 0)
                <img src="{{ asset('storage/'.$inn->images[0]) }}" alt="{{ $inn->title }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;"/>
              @endif
              <span class="innov-views-badge"><i class="fas fa-eye"></i> {{ number_format($inn->view_count) }}</span>
            </div>
            <div class="innov-carousel-body">
              <div class="innov-carousel-cat">{{ ucwords(str_replace('_',' ',$inn->category)) }}</div>
              <div class="innov-carousel-title">{{ $inn->title }}</div>
              <p class="innov-carousel-desc">{{ Str::limit($inn->description, 140) }}</p>
              @if($inn->impact_summary)
                <div class="innov-carousel-impact"><i class="fas fa-chart-line"></i> {{ $inn->impact_summary }}</div>
              @endif
              <div class="innov-carousel-footer">
                <div class="innov-carousel-farmer">
                  <div class="avatar" style="background:#15803d;color:#fff;">{{ $inn->user->initials ?? 'FA' }}</div>
                  <div>
                    <div class="innov-carousel-farmer-name">{{ $inn->user->full_name ?? 'Farmer' }}</div>
                    <div class="innov-carousel-farmer-loc">{{ $inn->district ?? 'Malawi' }}</div>
                  </div>
                </div>
                @auth
                  @if($iVoted)
                    <span class="vote-btn2 voted" style="cursor:default;"><i class="fas fa-check"></i> Voted <span class="vote-count">· {{ number_format($inn->vote_count) }}</span></span>
                  @else
                    <form method="POST" action="{{ route('innovation.vote', $inn) }}" class="innov-vote-form" style="display:inline;" onclick="event.stopPropagation();">
                      @csrf
                      <button type="submit" class="vote-btn2"><i class="fas fa-thumbs-up"></i> Vote · <span class="vote-count">{{ number_format($inn->vote_count) }}</span></button>
                    </form>
                  @endif
                @else
                  <a href="{{ route('login') }}" class="vote-btn2" onclick="event.stopPropagation();"><i class="fas fa-thumbs-up"></i> Vote · {{ number_format($inn->vote_count) }}</a>
                @endauth
              </div>
              <div style="margin-top:12px;font-size:.78rem;font-weight:700;color:var(--primary);"><i class="fas fa-arrow-right"></i> View details & vote in the hub</div>
            </div>
          </div>
        @endforeach
      </div>

      @if($innovations->count() > 1)
        <button class="innov-arrow prev" onclick="innovSlide(-1)"><i class="fas fa-chevron-left"></i></button>
        <button class="innov-arrow next" onclick="innovSlide(1)"><i class="fas fa-chevron-right"></i></button>
        <div class="innov-dots" id="innovDots">
          @foreach($innovations as $idx => $dot)
            <div class="innov-dot {{ $idx===0?'active':'' }}" onclick="innovGo({{ $idx }})"></div>
          @endforeach
        </div>
      @endif
    </div>
    @else
      <div style="text-align:center;padding:60px;color:var(--text-muted);">
        <i class="fas fa-lightbulb" style="font-size:1.75rem;margin-bottom:14px;display:block;opacity:.3;"></i>
        No innovations yet. Be the first to submit!
      </div>
    @endif

    <div style="text-align:center;margin-top:40px;display:flex;gap:14px;justify-content:center;flex-wrap:wrap;">
      <a href="{{ route('innovation') }}" class="btn btn-outline btn-lg"><i class="fas fa-lightbulb"></i> View the Innovation Hub</a>
      @auth
        <a href="{{ route('innovation.store') }}" class="btn btn-primary btn-lg"><i class="fas fa-plus"></i> Submit Your Innovation</a>
      @else
        <a href="{{ route('register') }}" class="btn btn-primary btn-lg"><i class="fas fa-plus"></i> Join & Submit Your Innovation</a>
      @endauth
    </div>
  </div>
</section>

<script>
(function(){
  const cards = document.querySelectorAll('.innov-carousel-card');
  const dots  = document.querySelectorAll('.innov-dot');
  if (!cards.length) return;
  let cur = 0, total = cards.length, autoTimer;

  function show(idx) {
    cards[cur].classList.remove('active');
    cards[cur].classList.add('exit');
    setTimeout(() => cards[cur].classList.remove('exit'), 500);
    dots[cur]?.classList.remove('active');
    cur = (idx + total) % total;
    cards[cur].classList.add('active');
    dots[cur]?.classList.add('active');
  }
  window.innovSlide = function(dir) { show(cur + dir); resetAuto(); };
  window.innovGo    = function(i)  { show(i); resetAuto(); };
  function resetAuto() { clearInterval(autoTimer); autoTimer = setInterval(() => show(cur + 1), 4500); }
  resetAuto();
  // Swipe support
  let sx = 0;
  const el = document.getElementById('innovCarousel');
  el.addEventListener('touchstart', e => sx = e.touches[0].clientX, {passive:true});
  el.addEventListener('touchend', e => { const dx = e.changedTouches[0].clientX - sx; if(Math.abs(dx)>40) innovSlide(dx<0?1:-1); }, {passive:true});
})();
</script>

<section class="section disease-cta-section">
  <div class="container">
    <div class="disease-cta-grid">
      {{-- Left --}}
      <div>
        <span class="section-label" style="background:#3f1010;color:#fca5a5;border-color:#7f1d1d;"><i class="fas fa-microscope"></i> Crop Health Analysis</span>
        <h2 class="disease-cta-title">
          Stop Crop Diseases<br/>
          <span class="disease-cta-highlight">Before They Destroy</span><br/>
          Your Harvest
        </h2>
        <p class="disease-cta-desc">
          Upload a photo of the affected plant. The analyzer identifies the disease in minutes
          and gives you treatment steps, recommended chemicals and a prevention guide.
        </p>
        <div class="disease-feat-grid" style="margin-bottom:32px;">
          @foreach([
            ['bg'=>'#fef2f2','color'=>'#ef4444','icon'=>'fas fa-camera','title'=>'Photo Detection','desc'=>'Upload a photo of the affected leaf'],
            ['bg'=>'var(--green-50)','color'=>'var(--green-700)','icon'=>'fas fa-book-open','title'=>'Disease Library','desc'=>'Treatment & prevention guides'],
            ['bg'=>'#fff7ed','color'=>'#c2410c','icon'=>'fas fa-bell','title'=>'Outbreak Alerts','desc'=>'Get notified before it spreads to your farm'],
            ['bg'=>'#e0f2fe','color'=>'var(--sky-600)','icon'=>'fas fa-flask','title'=>'Chemical Advice','desc'=>'Recommended products & application guidance'],
          ] as $feat)
            <div style="display:flex;align-items:flex-start;gap:12px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:var(--radius-md);padding:14px;">
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
            <i class="fas fa-microscope"></i> Detect Disease Now
          </a>
          <a href="{{ route('diseases') }}" class="btn btn-outline btn-lg" style="color:#fff;border-color:#94a3b8;">
            <i class="fas fa-book"></i> Disease Library
          </a>
        </div>
        <div class="disease-stats-row">
          <div>
            <div style="font-size:1.5rem;font-weight:800;color:#fff;">{{ number_format($diseaseCount) }}</div>
            <div style="font-size:.72rem;color:rgba(255,255,255,.55);margin-top:2px;">Diseases Covered</div>
          </div>
          <div style="width:1px;height:36px;background:rgba(255,255,255,.15);"></div>
          <div>
            <div style="font-size:1.5rem;font-weight:800;color:#fff;">Instant</div>
            <div style="font-size:.72rem;color:rgba(255,255,255,.55);margin-top:2px;">Detection & Diagnosis</div>
          </div>
        </div>
      </div>
      {{-- Right --}}
      <div style="display:flex;align-items:center;justify-content:center;gap:32px;padding:40px 0;">
        {{-- Phone mockup --}}
        <div style="position:relative;">
          <div style="width:200px;background:#0a1a0a;border-radius:30px;padding:16px 10px 14px;border:2px solid #14532d;box-shadow:0 20px 60px rgba(0,0,0,.6);">
            <div style="width:60px;height:10px;background:#050e05;border-radius:6px;margin:0 auto 10px;"></div>
            <div style="background:#0d1f0d;border-radius:18px;overflow:hidden;padding:0 0 8px;">
              <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 10px 6px;background:#0d3d22;border-bottom:1px solid #14532d;">
                <span style="font-size:.62rem;font-weight:700;color:rgba(255,255,255,.85);"><i class="fas fa-microscope" style="margin-right:3px;"></i> Disease Scanner</span>
                <span style="font-size:.55rem;font-weight:700;background:#22c55e;color:#fff;padding:2px 6px;border-radius:6px;">Ready</span>
              </div>
              <div style="margin:8px;height:130px;background:#061206;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:3rem;color:#22c55e;"><i class="fas fa-leaf"></i></div>
              <div style="margin:0 8px 6px;background:rgba(239,68,68,.15);border:1px solid rgba(239,68,68,.3);border-radius:8px;padding:7px 8px;display:flex;align-items:center;justify-content:space-between;">
                <div>
                  <div style="font-size:.62rem;font-weight:700;color:#fff;">Fall Armyworm</div>
                  <div style="font-size:.55rem;color:#fca5a5;">Maize · High Risk</div>
                </div>
                <div style="text-align:center;">
                  <div style="font-size:.7rem;font-weight:800;color:#4ade80;"><i class="fas fa-check"></i></div>
                  <div style="font-size:.52rem;color:rgba(255,255,255,.5);">identified</div>
                </div>
              </div>
              <div style="display:flex;gap:5px;margin:0 8px;">
                <button style="flex:1;background:#22c55e;color:#fff;border:none;border-radius:6px;font-size:.58rem;font-weight:700;padding:5px 4px;cursor:pointer;">View Treatment</button>
                <button style="flex:1;background:rgba(255,255,255,.1);color:rgba(255,255,255,.8);border:none;border-radius:6px;font-size:.58rem;font-weight:700;padding:5px 4px;cursor:pointer;">Scan Again</button>
              </div>
            </div>
          </div>
          <div style="position:absolute;top:-14px;right:-20px;background:#fff;border-radius:20px;padding:6px 12px;display:flex;align-items:center;gap:5px;font-size:.75rem;font-weight:700;color:var(--text);box-shadow:0 4px 16px rgba(0,0,0,.3);">
            <i class="fas fa-book-medical" style="color:#16a34a;"></i> Step-by-step treatment guide
          </div>
        </div>
        {{-- Crop protection supplies --}}
        <div style="position:relative;">
          <img src="{{ asset('assets/img/agri/spraying2.jpg') }}" alt="Crop protection inputs" width="110" height="260" style="object-fit:cover;border-radius:14px;box-shadow:0 12px 24px rgba(0,0,0,.5);display:block;"/>
          <div style="position:absolute;bottom:-10px;left:-50px;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);padding:8px 12px;display:flex;align-items:center;gap:8px;box-shadow:0 4px 20px rgba(0,0,0,.3);white-space:nowrap;">
            <i class="fas fa-store" style="color:var(--green-600);"></i>
            <div>
              <div style="font-size:.75rem;font-weight:700;color:var(--text);">Available in Marketplace</div>
              <div style="font-size:.7rem;color:var(--primary);font-weight:600;">Crop protection inputs</div>
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
/* Innovation vote — one vote per innovation per round */
document.addEventListener('submit', function (e) {
  const form = e.target;
  if (!form.classList.contains('innov-vote-form')) return;
  e.preventDefault();
  const btn = form.querySelector('button[type=submit]');
  if (btn) { btn.disabled = true; btn.classList.add('voting'); }
  fetch(form.action, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({})
  })
  .then(r => r.json())
  .then(data => {
    const countEl = form.querySelector('.vote-count');
    if (countEl && typeof data.vote_count !== 'undefined') countEl.textContent = Number(data.vote_count).toLocaleString();
    if (data.voted) {
      btn.innerHTML = '<i class="fas fa-check"></i> Voted <span class="vote-count">· ' + Number(data.vote_count).toLocaleString() + '</span>';
      btn.classList.remove('voting');
      btn.classList.add('voted');
      btn.style.pointerEvents = 'none';
    }
    showToast(data.message || 'Vote recorded.', data.voted ? 'success' : 'info');
  })
  .catch(() => {
    if (btn) { btn.disabled = false; btn.classList.remove('voting'); }
    showToast('Could not record the vote. Please try again.', 'error');
  });
});

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
      showToast('Added to wishlist!', 'success');
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
