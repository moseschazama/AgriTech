@extends('layouts.app')
@section('title', 'Learning Center — AgriTech Pro')
@section('extra_css')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"/>
<link rel="stylesheet" href="{{ asset('css/home.css') }}"/>
<style>
.learn-hero{background:linear-gradient(135deg,#052e0f,#0d4a1e,#166534);padding:60px 0 48px;color:#fff;}
.learn-search{display:flex;gap:0;max-width:540px;margin:0 auto 24px;}
.learn-search input{flex:1;padding:14px 18px;border:none;border-radius:var(--radius-md) 0 0 var(--radius-md);font-size:.95rem;font-family:var(--font-body);background:rgba(255,255,255,.95);color:var(--text);}
.learn-search input:focus{outline:none;}
.learn-search button{padding:14px 22px;background:var(--primary);color:#fff;border:none;border-radius:0 var(--radius-md) var(--radius-md) 0;cursor:pointer;font-size:.95rem;}
.learn-stats-row{display:flex;gap:32px;justify-content:center;flex-wrap:wrap;margin-top:28px;}
.learn-stat-pill{display:flex;align-items:center;gap:8px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);border-radius:var(--radius-full);padding:8px 18px;font-size:.82rem;font-weight:600;}
.filter-toolbar{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:28px;}
.filter-tabs{display:flex;gap:8px;flex-wrap:wrap;}
.filter-tab{padding:8px 16px;border-radius:var(--radius-full);font-size:.82rem;font-weight:600;border:1.5px solid var(--border);color:var(--text-muted);background:var(--bg-card);cursor:pointer;transition:all .15s;text-decoration:none;}
.filter-tab:hover,.filter-tab.active{background:var(--primary);border-color:var(--primary);color:#fff;}
.sort-select{padding:8px 14px;border:1.5px solid var(--border);border-radius:var(--radius-md);font-size:.82rem;font-family:var(--font-body);background:var(--bg-card);color:var(--text);cursor:pointer;}
.course-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px;}
.course-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;transition:all .2s;}
.course-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-lg);border-color:var(--green-300);}
.course-thumb{height:170px;position:relative;}
.course-badge-wrap{position:absolute;top:10px;left:10px;display:flex;gap:5px;flex-wrap:wrap;}
.course-premium-badge{background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;font-size:.67rem;font-weight:700;padding:3px 8px;border-radius:var(--radius-full);}
.course-body{padding:16px;}
.course-category{font-size:.7rem;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;}
.course-title{font-family:var(--font-display);font-size:.93rem;font-weight:700;color:var(--text);margin-bottom:8px;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.course-meta{display:flex;gap:10px;flex-wrap:wrap;font-size:.73rem;color:var(--text-muted);margin-bottom:12px;}
.course-footer{display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border);padding-top:12px;}
.course-price{font-family:var(--font-display);font-size:1.05rem;font-weight:800;color:var(--primary);}
.course-price-free{color:var(--green-600);}
.progress-bar-outer{background:var(--bg-2);border-radius:20px;height:5px;margin:6px 0;overflow:hidden;}
.progress-bar-inner{height:5px;background:linear-gradient(90deg,var(--primary),#4ade80);border-radius:20px;}
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
@media(max-width:600px){.learn-hero{padding:40px 0 32px;}.learn-stats-row{gap:12px;}.learn-stat-pill{padding:6px 12px;font-size:.75rem;}.video-player{height:260px;}.video-modal-box{width:95%;}}
@media(max-width:480px){.course-grid{grid-template-columns:1fr;gap:14px;}.filter-toolbar{flex-direction:column;align-items:stretch;}.filter-toolbar .btn,.filter-toolbar select{width:100%;justify-content:center;}.course-body{padding:12px;}.course-title{font-size:.85rem;}.continue-grid{grid-template-columns:1fr !important;}.guides-grid{grid-template-columns:1fr !important;}.learn-hero{padding:32px 0 24px;}}
</style>
@endsection

@section('content')

{{-- ── HERO ── --}}
<section class="learn-hero">
  <div class="container">
    <div style="text-align:center;margin-bottom:28px;">
      <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(74,222,128,.15);border:1px solid rgba(74,222,128,.3);border-radius:var(--radius-full);padding:5px 14px;font-size:.78rem;font-weight:700;color:#4ade80;margin-bottom:16px;">
        <i class="fas fa-graduation-cap"></i> Learning Center
      </span>
      <h1 style="font-family:var(--font-display);font-size:clamp(2rem,5vw,3rem);font-weight:800;margin-bottom:12px;">Learn Modern Farming — <span style="color:#4ade80;">Free</span></h1>
      <p style="font-size:1rem;opacity:.8;max-width:540px;margin:0 auto 28px;line-height:1.7;">Expert-led courses on maize farming, irrigation, livestock, agribusiness and disease management. Earn certificates and grow your farm.</p>
    </div>
    <form method="GET" action="{{ route('learn') }}" class="learn-search">
      <input type="text" name="q" value="{{ request('q') }}" placeholder="Search 300+ farming courses..."/>
      <button type="submit"><i class="fas fa-search"></i></button>
    </form>
    <div class="learn-stats-row">
      @foreach(['fas fa-play-circle'=>'300+ Courses','fas fa-users'=>'12,000+ Students','fas fa-chalkboard-teacher'=>'50+ Expert Instructors','fas fa-certificate'=>'Free Certificates'] as $icon=>$stat)
        <div class="learn-stat-pill"><i class="{{ $icon }}" style="color:#4ade80;"></i> {{ $stat }}</div>
      @endforeach
    </div>
  </div>
</section>

<div class="section" style="background:var(--bg-2);">
  <div class="container">

    {{-- ── CONTINUE LEARNING (auth only) ── --}}
    @auth
    @if(isset($continueWatching) && $continueWatching->count() > 0)
    <div style="margin-bottom:40px;">
      <h2 style="font-family:var(--font-display);font-size:1.25rem;font-weight:800;color:var(--text);margin-bottom:16px;">▶️ Continue Learning</h2>
      <div class="continue-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
        @foreach($continueWatching as $enrollment)
          @php $pct = $enrollment->progressPercentage(); @endphp
          <div class="continue-card">
            <div style="width:48px;height:48px;border-radius:12px;background:var(--green-100);display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0;">🌱</div>
            <div style="flex:1;min-width:0;">
              <div style="font-size:.85rem;font-weight:700;color:var(--text);margin-bottom:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $enrollment->course->title }}</div>
              <div class="progress-bar-outer"><div class="progress-bar-inner" style="width:{{ $pct }}%;"></div></div>
              <div style="font-size:.72rem;color:var(--text-muted);">{{ $pct }}% complete</div>
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
        @foreach(['soil_crops'=>'🌽 Soil & Crops','livestock'=>'🐄 Livestock','agri_tech'=>'🚁 Agri-Tech','agribusiness'=>'📊 Agribusiness','organic'=>'🥦 Organic','irrigation'=>'💧 Irrigation','post_harvest'=>'🌾 Post-Harvest'] as $key=>$label)
          <a href="{{ route('learn', array_merge(request()->query(), ['category'=>$key])) }}"
             class="filter-tab {{ request('category') === $key ? 'active' : '' }}">{{ $label }}</a>
        @endforeach
      </div>
      <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        <span style="font-size:.82rem;color:var(--text-muted);">{{ isset($courses) ? $courses->total() : 0 }} courses found</span>
        <select class="sort-select" onchange="window.location=this.value">
          @foreach(['popular'=>'Most Popular','newest'=>'Newest','rating'=>'Highest Rated','price'=>'Free First'] as $val=>$label)
            <option value="{{ route('learn', array_merge(request()->query(), ['sort'=>$val])) }}" @selected(request('sort',$val==='popular'?'popular':null)===$val)>{{ $label }}</option>
          @endforeach
        </select>
      </div>
    </div>

    {{-- ── COURSES GRID ── --}}
    <div class="course-grid" id="courseGrid">
      @forelse(isset($courses) ? $courses : [] as $course)
        @php
          $catEmojis=['soil_crops'=>'🌽','livestock'=>'🐄','agri_tech'=>'🚁','agribusiness'=>'📊','organic'=>'🥦','irrigation'=>'💧','post_harvest'=>'🌾'];
          $catColors=['soil_crops'=>'#dcfce7,#bbf7d0','livestock'=>'#fef9c3,#fef08a','agri_tech'=>'#e0f2fe,#bae6fd','agribusiness'=>'#f5f3ff,#ede9fe','organic'=>'#f0fdf4,#dcfce7','irrigation'=>'#e0f2fe,#bae6fd','post_harvest'=>'#fff7ed,#fed7aa'];
          $emoji=$catEmojis[$course->category]??'🌱';
          $color=$catColors[$course->category]??'#dcfce7,#bbf7d0';
          $hrs=intdiv($course->total_duration_minutes,60);
          $mins=$course->total_duration_minutes%60;
          // Check if user is enrolled
          $isEnrolled=auth()->check()&&$course->enrollments()->where('user_id',Auth::id())->exists();
          $enrollment=auth()->check()?$course->enrollments()->where('user_id',Auth::id())->first():null;
        @endphp
        <div class="course-card">
          <div class="course-thumb" style="background:linear-gradient(135deg,{{ $color }});">
            <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:4rem;">{{ $emoji }}</div>
            <div class="course-badge-wrap">
              @if($course->access_type==='free')    <span class="badge badge-green">FREE</span>@endif
              @if($course->access_type==='premium') <span class="course-premium-badge"><i class="fas fa-crown"></i> Premium</span>@endif
              @if($course->is_featured)             <span class="badge badge-earth">Bestseller</span>@endif
              @if($isEnrolled&&$enrollment?->status==='completed') <span class="badge badge-green"><i class="fas fa-check"></i> Completed</span>@endif
              @if($isEnrolled&&$enrollment?->status==='active')    <span class="badge badge-sky"><i class="fas fa-play"></i> Enrolled</span>@endif
              @if($course->has_certificate)         <span class="badge badge-gray">🎓 Certificate</span>@endif
            </div>
          </div>
          <div class="course-body">
            <div class="course-category">{{ ucwords(str_replace('_',' ',$course->category)) }}</div>
            <div class="course-title">{{ $course->title }}</div>
            <div style="font-size:.76rem;color:var(--text-muted);margin-bottom:8px;">by {{ $course->instructor->display_name??$course->instructor->name??'Instructor' }}</div>
            <div class="course-meta">
              <span><i class="fas fa-clock"></i> {{ $hrs }}h {{ $mins }}m</span>
              <span><i class="fas fa-play-circle"></i> {{ $course->total_lessons }} Lessons</span>
              <span><i class="fas fa-star" style="color:#f59e0b;"></i> {{ number_format($course->average_rating,1) }} ({{ $course->total_reviews }})</span>
              <span><i class="fas fa-users"></i> {{ number_format($course->total_enrolled) }}</span>
            </div>
            @if($isEnrolled&&$enrollment&&$enrollment->status==='active')
              @php $pct=$enrollment->progressPercentage(); @endphp
              <div class="progress-bar-outer"><div class="progress-bar-inner" style="width:{{ $pct }}%;"></div></div>
              <div style="font-size:.72rem;color:var(--text-muted);margin-bottom:10px;">{{ $pct }}% complete</div>
            @endif
            <div class="course-footer">
              <div>
                @if($course->access_type==='free') <div class="course-price course-price-free">FREE</div>
                @else <div class="course-price">{{ $course->currency }} {{ number_format($course->price) }}</div>@endif
              </div>
              @if($isEnrolled)
                @if($enrollment?->status==='completed')
                  <a href="{{ route('learn.certificate',$enrollment) }}" class="btn btn-outline btn-sm"><i class="fas fa-certificate"></i> Certificate</a>
                @else
                  <a href="{{ route('learn.show',$course) }}" class="btn btn-primary btn-sm"><i class="fas fa-play"></i> Continue</a>
                @endif
              @elseif(auth()->check())
                @if($course->access_type==='free')
                  <form method="POST" action="{{ route('learn.enroll',$course) }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Enroll Free</button>
                  </form>
                @else
                  <a href="{{ route('learn.show',$course) }}" class="btn btn-outline btn-sm"><i class="fas fa-eye"></i> Preview</a>
                @endif
              @else
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Enroll Free</a>
              @endif
            </div>
          </div>
        </div>
      @empty
        <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:var(--text-muted);">
          <i class="fas fa-graduation-cap" style="font-size:3rem;margin-bottom:16px;display:block;opacity:.3;"></i>
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
        <h2 class="section-title">Download Free <span>Field Guides</span></h2>
        <p class="section-desc">Practical farming guides uploaded by our agricultural experts — print and use in the field, no internet required.</p>
      </div>

      {{-- Topic filter chips --}}
      <div style="display:flex;gap:8px;flex-wrap:wrap;justify-content:center;margin-bottom:28px;">
        <a href="{{ route('learn') }}" class="filter-tab {{ !request('guide_topic') ? 'active' : '' }}">All Guides</a>
        @foreach(['soil_crops'=>'🌽 Soil & Crops','livestock'=>'🐄 Livestock','agri_tech'=>'🚁 Agri-Tech','agribusiness'=>'📊 Agribusiness','organic'=>'🥦 Organic','irrigation'=>'💧 Irrigation','post_harvest'=>'🌾 Post-Harvest','disease_control'=>'🔬 Disease Control'] as $key=>$label)
          <a href="{{ route('learn',['guide_topic'=>$key]) }}" class="filter-tab {{ request('guide_topic')===$key ? 'active' : '' }}">{{ $label }}</a>
        @endforeach
      </div>

      <div class="guides-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;">
        @forelse(isset($guides) ? $guides : [] as $guide)
          <div class="guide-card">
            <div class="guide-icon" style="background:var(--green-50);font-size:2rem;">{{ $guide->topic_emoji }}</div>
            <div style="font-family:var(--font-display);font-size:.88rem;font-weight:700;color:var(--text);margin-bottom:6px;line-height:1.3;">{{ $guide->title }}</div>
            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:10px;">
              {{ $guide->page_count ? $guide->page_count.' pages · ' : '' }}{{ $guide->file_size_formatted }}
            </div>
            @if($guide->description)
              <div style="font-size:.74rem;color:var(--text-muted);margin-bottom:10px;line-height:1.5;">{{ Str::limit($guide->description, 70) }}</div>
            @endif
            <span class="badge badge-green" style="margin-bottom:14px;display:inline-block;">Free Download</span>
            <div style="font-size:.72rem;color:var(--text-muted);margin-bottom:10px;">
              <i class="fas fa-download"></i> {{ number_format($guide->download_count) }} downloads
            </div>
            <a href="{{ route('guides.download', $guide) }}" class="btn btn-outline btn-sm" style="width:100%;justify-content:center;">
              <i class="fas fa-download"></i> Download PDF
            </a>
          </div>
        @empty
          <div style="grid-column:1/-1;text-align:center;padding:48px 20px;color:var(--text-muted);">
            <i class="fas fa-file-pdf" style="font-size:2.5rem;margin-bottom:14px;display:block;opacity:.3;"></i>
            <p>No guides uploaded yet{{ request('guide_topic') ? ' for this topic' : '' }}.</p>
            @if(request('guide_topic'))
              <a href="{{ route('learn') }}" style="color:var(--primary);font-size:.85rem;">View all guides →</a>
            @endif
          </div>
        @endforelse
      </div>
    </div>

    {{-- ── BECOME AN INSTRUCTOR CTA ── --}}
    <div style="background:linear-gradient(135deg,#052e0f,#166534);border-radius:var(--radius-xl);padding:40px;margin-top:48px;text-align:center;color:#fff;">
      <div style="font-size:2.5rem;margin-bottom:14px;">👨‍🏫</div>
      <h2 style="font-family:var(--font-display);font-size:1.5rem;font-weight:800;margin-bottom:10px;">Are You an Agricultural Expert?</h2>
      <p style="opacity:.8;max-width:480px;margin:0 auto 24px;font-size:.9rem;line-height:1.6;">Share your knowledge with 12,000+ farmers across Malawi and Zambia. Create courses and earn from your expertise.</p>
      <a href="{{ route('register') }}" class="btn btn-white btn-lg"><i class="fas fa-chalkboard-teacher"></i> Become an Instructor</a>
    </div>

  </div>
</div>

{{-- Video Modal --}}
<div class="video-modal-backdrop" id="videoModal">
  <div class="video-modal-box">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border);">
      <div id="videoModalTitle" style="font-family:var(--font-display);font-weight:700;color:var(--text);">Course Video</div>
      <button onclick="closeVideoModal()" style="background:none;border:none;font-size:1.2rem;cursor:pointer;color:var(--text-muted);">✕</button>
    </div>
    <div class="video-player">
      <div class="video-play-btn" onclick="showToast('▶️ Video playing...','info')">
        <i class="fas fa-play" style="margin-left:4px;"></i>
      </div>
    </div>
    <div style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;">
      <div id="videoLessonName" style="font-size:.85rem;color:var(--text-muted);">Lesson 1</div>
      <div style="display:flex;gap:8px;">
        <button class="btn btn-outline btn-sm" onclick="showToast('← Previous lesson','info')"><i class="fas fa-chevron-left"></i> Prev</button>
        <button class="btn btn-primary btn-sm" onclick="showToast('✅ Lesson marked complete!','success')"><i class="fas fa-check"></i> Mark Complete</button>
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
