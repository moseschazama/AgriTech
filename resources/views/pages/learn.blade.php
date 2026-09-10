@extends('layouts.app')
@section('title', 'Learning Center — AgriTech Pro')
@section('extra_css')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"/>
<link rel="stylesheet" href="{{ asset('css/home.css') }}"/>
<style>
.learn-hero{--hero-glow-1:rgba(22,163,74,0.07);--hero-glow-2:rgba(22,163,74,0.04);--hero-orb:rgba(22,163,74,0.06);--hero-badge-bg:var(--green-100);--hero-badge-fg:var(--green-700);--hero-badge-border:var(--green-200);--hero-accent-color:var(--green-600);--hero-overlay-start:rgba(10,25,12,0.78);--hero-overlay-mid:rgba(10,30,15,0.58);--hero-overlay-end:rgba(5,20,10,0.72);--hero-overlay-accent:rgba(34,197,94,0.15);}
.learn-hero.page-hero-image{background-image:url('https://images.unsplash.com/photo-1574943320219-553eb213f72d?auto=format&fit=crop&w=1920&q=80');}
.learn-search{display:flex;gap:0;max-width:540px;margin:0 auto;}
.learn-search input{flex:1;padding:14px 18px;border:none;border-radius:var(--radius-md) 0 0 var(--radius-md);font-size:.9375rem;background:rgba(255,255,255,.95);color:var(--text);}
.learn-search input:focus{outline:none;}
.learn-search button{padding:14px 22px;background:var(--primary);color:#fff;border:none;border-radius:0 var(--radius-md) var(--radius-md) 0;cursor:pointer;font-size:.9375rem;}
.learn-stats-row{display:flex;gap:32px;justify-content:center;flex-wrap:wrap;margin-top:28px;}
.learn-stat-pill{display:flex;align-items:center;gap:8px;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-full);padding:8px 18px;font-size:.8125rem;font-weight:600;}
.filter-toolbar{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:28px;}
.filter-tabs{display:flex;gap:8px;flex-wrap:wrap;}
.filter-tab{padding:8px 16px;border-radius:var(--radius-full);font-size:.8125rem;font-weight:600;border:1.5px solid var(--border);color:var(--text-muted);background:var(--bg-card);cursor:pointer;transition:all .15s;text-decoration:none;}
.filter-tab:hover,.filter-tab.active{background:var(--primary);border-color:var(--primary);color:#fff;}
.sort-select{padding:8px 14px;border:1.5px solid var(--border);border-radius:var(--radius-md);font-size:.8125rem;background:var(--bg-card);color:var(--text);cursor:pointer;}
.course-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px;}
.course-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;transition:all .2s;}
.course-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-lg);border-color:var(--green-300);}
.course-thumb{height:170px;position:relative;}
.course-badge-wrap{position:absolute;top:10px;left:10px;display:flex;gap:5px;flex-wrap:wrap;}
.course-premium-badge{background:#f59e0b;color:#fff;font-size:.75rem;font-weight:700;padding:3px 8px;border-radius:var(--radius-full);}
.course-body{padding:16px;}
.course-category{font-size:.75rem;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;}
.course-title{font-size:.9375rem;font-weight:700;color:var(--text);margin-bottom:8px;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.course-meta{display:flex;gap:10px;flex-wrap:wrap;font-size:.75rem;color:var(--text-muted);margin-bottom:12px;}
.course-footer{display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border);padding-top:12px;}
.course-price{font-size:1.0625rem;font-weight:800;color:var(--primary);}
.course-price-free{color:var(--green-600);}
.progress-bar-outer{background:var(--bg-2);border-radius:20px;height:5px;margin:6px 0;overflow:hidden;}
.progress-bar-inner{height:5px;background:var(--primary);border-radius:20px;}
.guide-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:24px;text-align:center;transition:all .2s;}
.guide-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-md);}
.guide-icon{width:64px;height:64px;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:2rem;margin:0 auto 14px;}
.continue-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:18px 20px;display:flex;align-items:center;gap:16px;transition:all .2s;}
.continue-card:hover{border-color:var(--primary);box-shadow:var(--shadow-md);}
.video-modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.85);z-index:10000;display:none;align-items:center;justify-content:center;}
.video-modal-backdrop.open{display:flex;}
.video-modal-box{background:var(--bg-card);border-radius:var(--radius-xl);width:90%;max-width:700px;overflow:hidden;}
.video-player{background:#000;height:380px;display:flex;align-items:center;justify-content:center;font-size:4rem;cursor:pointer;position:relative;}
.video-play-btn{width:72px;height:72px;border-radius:50%;background:rgba(255,255,255,.2);border:3px solid #fff;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.8rem;cursor:pointer;transition:all .2s;}
.video-play-btn:hover{background:var(--primary);border-color:var(--primary);}
@media(max-width:900px){.course-grid{grid-template-columns:repeat(2,1fr);}.continue-grid{grid-template-columns:1fr 1fr !important;}.guides-grid{grid-template-columns:repeat(2,1fr) !important;}}
@media(max-width:768px){.course-grid{gap:14px;}.filter-toolbar{gap:12px;}}
@media(max-width:600px){.learn-search{flex-direction:column;gap:8px;}.learn-search input,.learn-search button{border-radius:var(--radius-md);width:100%;}.learn-stats-row{gap:12px;}.learn-stat-pill{padding:6px 12px;font-size:.75rem;}.video-player{height:260px;}.video-modal-box{width:95%;}}
@media(max-width:480px){.course-grid{grid-template-columns:1fr;gap:14px;}.filter-toolbar{flex-direction:column;align-items:stretch;}.filter-toolbar .btn,.filter-toolbar select{width:100%;justify-content:center;}.course-body{padding:12px;}.course-title{font-size:.85rem;}.continue-grid{grid-template-columns:1fr !important;}.guides-grid{grid-template-columns:1fr !important;}}
</style>
@endsection

@section('content')

{{-- ── HERO ── --}}
<section class="page-hero learn-hero page-hero-image">
  <div class="container">
    <div class="page-hero-content">
      <span class="page-hero-badge">
        <i class="fas fa-graduation-cap"></i> Learning Center
      </span>
      <h1 class="page-hero-title">Learn Modern Farming</h1>
      <p class="page-hero-desc">Expert-led courses on maize farming, irrigation, livestock, agribusiness and disease management. Earn certificates and grow your farm.</p>
      <div class="page-hero-cta">
        <form method="GET" action="{{ route('learn') }}" class="learn-search">
          <input type="text" name="q" value="{{ request('q') }}" placeholder="Search 300+ farming courses..."/>
          <button type="submit"><i class="fas fa-search"></i></button>
        </form>
      </div>
      <div class="page-hero-stats">
        @foreach(['fas fa-play-circle'=>'300+ Courses','fas fa-users'=>'12,000+ Students','fas fa-chalkboard-teacher'=>'50+ Expert Instructors','fas fa-certificate'=>'Certified Learning'] as $icon=>$stat)
          <div class="page-hero-stat"><i class="{{ $icon }}"></i> {{ $stat }}</div>
        @endforeach
      </div>
    </div>
  </div>
</section>

<div class="section" style="background:var(--bg-2);">
  <div class="container">

    {{-- ── CONTINUE LEARNING (auth only) ── --}}
    @auth
    @if(isset($continueWatching) && $continueWatching->count() > 0)
    <div style="margin-bottom:40px;">
      <h2 class="heading-sm font-800" style="color:var(--text);margin-bottom:16px;"><i class="fas fa-play-circle" style="color:var(--primary);"></i> Continue Learning</h2>
      <div class="continue-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
        @foreach($continueWatching as $enrollment)
          @php $pct = $enrollment->progressPercentage(); @endphp
          <div class="continue-card">
            <div style="width:48px;height:48px;border-radius:12px;background:var(--green-100);display:flex;align-items:center;justify-content:center;font-size:1.15rem;color:var(--green-700);flex-shrink:0;"><i class="fas fa-seedling"></i></div>
            <div style="flex:1;min-width:0;">
              <div class="body-base font-700" style="color:var(--text);margin-bottom:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $enrollment->course->title }}</div>
              <div class="progress-bar-outer"><div class="progress-bar-inner" style="width:{{ $pct }}%;"></div></div>
              <div class="body-xs" style="color:var(--text-muted);">{{ $pct }}% complete</div>
            </div>
            <a href="{{ route('learn.show', $enrollment->course) }}" class="btn btn-primary btn-sm" style="flex-shrink:0;"><i class="fas fa-play"></i></a>
          </div>
        @endforeach
      </div>
    </div>
    @endif
    @endauth

    {{-- ── FILTER TOOLBAR ── --}}
    <div class="filter-toolbar">
      <div class="filter-tabs">
        <a href="{{ route('learn', array_merge(request()->query(), ['category'=>null])) }}"
           class="filter-tab {{ !request('category') ? 'active' : '' }}">All</a>
        @foreach(['soil_crops'=>'Soil & Crops','livestock'=>'Livestock','agri_tech'=>'Agri-Tech','agribusiness'=>'Agribusiness','organic'=>'Organic','irrigation'=>'Irrigation','post_harvest'=>'Post-Harvest'] as $key=>$label)
          <a href="{{ route('learn', array_merge(request()->query(), ['category'=>$key])) }}"
             class="filter-tab {{ request('category') === $key ? 'active' : '' }}"><i class="fas {{ \App\Support\CategoryIcons::course($key) }}"></i> {{ $label }}</a>
        @endforeach
      </div>
      <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        <span class="body-sm" style="color:var(--text-muted);">{{ isset($courses) ? $courses->total() : 0 }} courses found</span>
        <select class="sort-select" onchange="window.location=this.value">
          @foreach(['popular'=>'Most Popular','newest'=>'Newest','rating'=>'Highest Rated','price'=>'Open First'] as $val=>$label)
            <option value="{{ route('learn', array_merge(request()->query(), ['sort'=>$val])) }}" @selected(request('sort',$val==='popular'?'popular':null)===$val)>{{ $label }}</option>
          @endforeach
        </select>
      </div>
    </div>

    {{-- ── COURSES GRID ── --}}
    <div class="course-grid" id="courseGrid">
      @forelse(isset($courses) ? $courses : [] as $course)
        @php
          $catColors=['soil_crops'=>'#dcfce7,#bbf7d0','livestock'=>'#fef9c3,#fef08a','agri_tech'=>'#e0f2fe,#bae6fd','agribusiness'=>'#f5f3ff,#ede9fe','organic'=>'#f0fdf4,#dcfce7','irrigation'=>'#e0f2fe,#bae6fd','post_harvest'=>'#fff7ed,#fed7aa'];
          $catImgs=['soil_crops'=>'maize-field.jpg','livestock'=>'cows.jpg','agri_tech'=>'agritech-drone.jpg','agribusiness'=>'market-stall.jpg','organic'=>'seedling.jpg','irrigation'=>'irrigation.jpg','post_harvest'=>'harvest.jpg'];
          $color=$catColors[$course->category]??'#dcfce7,#bbf7d0';
          $cover=$course->thumbnail_url ?? asset('assets/img/agri/'.($catImgs[$course->category]??'seedling.jpg'));
          $hrs=intdiv($course->total_duration_minutes,60);
          $mins=$course->total_duration_minutes%60;
          // Check if user is enrolled
          $isEnrolled=auth()->check()&&$course->enrollments()->where('user_id',Auth::id())->exists();
          $enrollment=auth()->check()?$course->enrollments()->where('user_id',Auth::id())->first():null;
        @endphp
        <div class="course-card">
          <div class="course-thumb" style="background:{{ explode(',', $color)[0] }};">
            <img src="{{ $cover }}" alt="{{ $course->title }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;" loading="lazy"/>
            <div class="course-badge-wrap">
              @if($course->access_type==='free')    <span class="badge badge-green">Open</span>@endif
              @if($course->access_type==='premium') <span class="course-premium-badge"><i class="fas fa-crown"></i> Premium</span>@endif
              @if($course->is_featured)             <span class="badge badge-earth">Bestseller</span>@endif
              @if($isEnrolled&&$enrollment?->status==='completed') <span class="badge badge-green"><i class="fas fa-check"></i> Completed</span>@endif
              @if($isEnrolled&&$enrollment?->status==='active')    <span class="badge badge-sky"><i class="fas fa-play"></i> Enrolled</span>@endif
              @if($course->has_certificate)         <span class="badge badge-gray"><i class="fas fa-graduation-cap"></i> Certificate</span>@endif
            </div>
          </div>
          <div class="course-body">
            <div class="course-category">{{ ucwords(str_replace('_',' ',$course->category)) }}</div>
            <div class="course-title">{{ $course->title }}</div>
            <div class="body-xs" style="color:var(--text-muted);margin-bottom:8px;">by {{ $course->instructor->display_name??$course->instructor->name??'Instructor' }}</div>
            <div class="course-meta">
              <span><i class="fas fa-clock"></i> {{ $hrs }}h {{ $mins }}m</span>
              <span><i class="fas fa-play-circle"></i> {{ $course->total_lessons }} Lessons</span>
              <span><i class="fas fa-star" style="color:#f59e0b;"></i> {{ number_format($course->average_rating,1) }} ({{ $course->total_reviews }})</span>
              <span><i class="fas fa-users"></i> {{ number_format($course->total_enrolled) }}</span>
            </div>
            @if($isEnrolled&&$enrollment&&$enrollment->status==='active')
              @php $pct=$enrollment->progressPercentage(); @endphp
              <div class="progress-bar-outer"><div class="progress-bar-inner" style="width:{{ $pct }}%;"></div></div>
              <div class="body-xs" style="color:var(--text-muted);margin-bottom:10px;">{{ $pct }}% complete</div>
            @endif
            <div class="course-footer">
              <div>
                @if($course->access_type==='free') <div class="course-price course-price-free">Open</div>
                @else <div class="course-price">{{ $course->currency }} {{ number_format($course->price) }}</div>@endif
              </div>
              @if($isEnrolled)
                @if($enrollment?->status==='completed')
                  <a href="{{ route('learn.certificate.pdf',$enrollment) }}" class="btn btn-outline btn-sm"><i class="fas fa-download"></i> Certificate PDF</a>
                @else
                  <a href="{{ route('learn.show',$course) }}" class="btn btn-primary btn-sm"><i class="fas fa-play"></i> Continue</a>
                @endif
              @elseif(auth()->check())
                @if($course->access_type==='free')
                  <form method="POST" action="{{ route('learn.enroll',$course) }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Enroll Now</button>
                  </form>
                @else
                  <a href="{{ route('learn.show',$course) }}" class="btn btn-outline btn-sm"><i class="fas fa-eye"></i> Preview</a>
                @endif
              @else
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Enroll Now</a>
              @endif
            </div>
          </div>
        </div>
      @empty
        <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:var(--text-muted);">
          <i class="fas fa-graduation-cap" style="font-size:1.75rem;margin-bottom:14px;display:block;opacity:.3;"></i>
          <h3 style="margin-bottom:8px;">No courses found</h3>
          <p>Try a different search or category filter.</p>
          <a href="{{ route('learn') }}" class="btn btn-primary btn-sm" style="margin-top:16px;">Clear filters</a>
        </div>
      @endforelse
    </div>

    {{-- Pagination --}}
    @if(isset($courses)&&$courses->hasPages())
      <div style="margin-top:36px;">{{ $courses->withQueryString()->links() }}</div>
    @endif

    {{-- ── PDF GUIDES ── --}}
    <div style="margin-top:60px;">
      <div style="text-align:center;margin-bottom:32px;">
        <span class="section-label"><i class="fas fa-file-pdf"></i> PDF Guides</span>
        <h2 class="section-title">Download <span>Field Guides</span></h2>
        <p class="section-desc">Practical farming guides uploaded by our agricultural experts — print and use in the field, no internet required.</p>
      </div>

      {{-- Topic filter chips --}}
      <div style="display:flex;gap:8px;flex-wrap:wrap;justify-content:center;margin-bottom:28px;">
        <a href="{{ route('learn') }}" class="filter-tab {{ !request('guide_topic') ? 'active' : '' }}">All Guides</a>
        @foreach(['soil_crops'=>'Soil & Crops','livestock'=>'Livestock','agri_tech'=>'Agri-Tech','agribusiness'=>'Agribusiness','organic'=>'Organic','irrigation'=>'Irrigation','post_harvest'=>'Post-Harvest','disease_control'=>'Disease Control'] as $key=>$label)
          <a href="{{ route('learn',['guide_topic'=>$key]) }}" class="filter-tab {{ request('guide_topic')===$key ? 'active' : '' }}">{{ $label }}</a>
        @endforeach
      </div>

      <div class="guides-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;">
        @forelse(isset($guides) ? $guides : [] as $guide)
          <div class="guide-card">
            <div class="guide-icon" style="background:var(--green-50);font-size:1.4rem;color:var(--green-700);"><i class="fas {{ \App\Support\CategoryIcons::course($guide->topic) }}"></i></div>
            <div class="body-base font-700" style="color:var(--text);margin-bottom:6px;line-height:1.3;">{{ $guide->title }}</div>
            <div class="body-xs" style="color:var(--text-muted);margin-bottom:10px;">
              {{ $guide->page_count ? $guide->page_count.' pages · ' : '' }}{{ $guide->file_size_formatted }}
            </div>
            @if($guide->description)
              <div class="body-xs" style="color:var(--text-muted);margin-bottom:10px;">{{ Str::limit($guide->description, 70) }}</div>
            @endif
            <span class="badge badge-green" style="margin-bottom:14px;display:inline-block;">Field Guide</span>
            <div class="body-xs" style="color:var(--text-muted);margin-bottom:10px;">
              <i class="fas fa-download"></i> {{ number_format($guide->download_count) }} downloads
            </div>
            <a href="{{ route('guides.download', $guide) }}" class="btn btn-outline btn-sm" style="width:100%;justify-content:center;">
              <i class="fas fa-download"></i> Download PDF
            </a>
          </div>
        @empty
          <div style="grid-column:1/-1;text-align:center;padding:48px 20px;color:var(--text-muted);">
            <i class="fas fa-file-pdf" style="font-size:1.4rem;margin-bottom:12px;display:block;opacity:.3;"></i>
            <p>No guides uploaded yet{{ request('guide_topic') ? ' for this topic' : '' }}.</p>
            @if(request('guide_topic'))
              <a href="{{ route('learn') }}" style="color:var(--primary);font-size:.85rem;">View all guides →</a>
            @endif
          </div>
        @endforelse
      </div>
    </div>

    {{-- ── BECOME AN INSTRUCTOR CTA ── --}}
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:40px;margin-top:48px;text-align:center;color:var(--text);box-shadow:var(--shadow-md);">
      <div style="font-size:1.4rem;margin-bottom:12px;color:var(--primary);"><i class="fas fa-chalkboard-user"></i></div>
      <h2 class="heading-md" style="margin-bottom:10px;">Are You an Agricultural Expert?</h2>
      <p class="body-base" style="color:var(--text-muted);max-width:480px;margin:0 auto 24px;">Share your knowledge with 12,000+ farmers across Malawi. Create courses and earn from your expertise.</p>
      <a href="{{ route('register') }}" class="btn btn-primary btn-lg"><i class="fas fa-chalkboard-teacher"></i> Become an Instructor</a>
    </div>

  </div>
</div>

{{-- Video Modal --}}
<div class="video-modal-backdrop" id="videoModal">
  <div class="video-modal-box">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border);">
      <div id="videoModalTitle" class="body-base font-700" style="color:var(--text);">Course Video</div>
      <button onclick="closeVideoModal()" style="background:none;border:none;font-size:1.2rem;cursor:pointer;color:var(--text-muted);">✕</button>
    </div>
    <div class="video-player">
      <div class="video-play-btn" onclick="showToast('Video playing...','info')">
        <i class="fas fa-play" style="margin-left:4px;"></i>
      </div>
    </div>
    <div style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;">
      <div id="videoLessonName" class="body-sm" style="color:var(--text-muted);">Lesson 1</div>
      <div style="display:flex;gap:8px;">
        <button class="btn btn-outline btn-sm" onclick="showToast('← Previous lesson','info')"><i class="fas fa-chevron-left"></i> Prev</button>
        <button class="btn btn-primary btn-sm" onclick="showToast('Lesson marked complete!','success')"><i class="fas fa-check"></i> Mark Complete</button>
        <button class="btn btn-outline btn-sm" onclick="showToast('Next lesson →','info')">Next <i class="fas fa-chevron-right"></i></button>
      </div>
    </div>
  </div>
</div>

@include('partials.footer')
@endsection

@section('extra_js')
<script>
function closeVideoModal(){document.getElementById('videoModal').classList.remove('open');}
function openVideoModal(title,lesson){
  document.getElementById('videoModalTitle').textContent=title;
  document.getElementById('videoLessonName').textContent=lesson;
  document.getElementById('videoModal').classList.add('open');
}
document.getElementById('videoModal')?.addEventListener('click',function(e){if(e.target===this)closeVideoModal();});
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeVideoModal();});
</script>
@endsection
