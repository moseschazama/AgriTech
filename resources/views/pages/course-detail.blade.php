@extends('layouts.app')
@section('title', $course->title.' — AgriTech Pro')
@section('extra_css')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"/>
<link rel="stylesheet" href="{{ asset('css/home.css') }}"/>
<style>
.course-hero{background:linear-gradient(135deg,#052e0f,#0d4a1e,#166534);padding:52px 0 40px;color:#fff;}
.cd-layout{display:grid;grid-template-columns:1fr 360px;gap:28px;align-items:start;}
.cd-sidebar{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);overflow:hidden;position:sticky;top:90px;}
.cd-sidebar-thumb{height:200px;display:flex;align-items:center;justify-content:center;font-size:6rem;}
.cd-sidebar-body{padding:22px;}
.cd-price{font-family:var(--font-display);font-size:2.2rem;font-weight:800;color:var(--primary);margin-bottom:16px;}
.lesson-row{display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid var(--border);}
.lesson-row:last-child{border-bottom:none;}
.lesson-num{width:30px;height:30px;border-radius:50%;background:var(--bg-2);border:1.5px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:.72rem;font-weight:700;color:var(--text-muted);flex-shrink:0;}
.lesson-num.done{background:var(--primary);border-color:var(--primary);color:#fff;}
.lesson-num.free-preview{background:var(--green-50);border-color:var(--green-200);color:var(--green-700);}
.lesson-title{font-size:.84rem;font-weight:500;color:var(--text);flex:1;line-height:1.3;}
.play-btn{display:flex;align-items:center;gap:5px;padding:5px 12px;background:var(--primary);color:#fff;border:none;border-radius:var(--radius-full);cursor:pointer;font-size:.74rem;font-weight:700;font-family:var(--font-body);transition:all .15s;}
.play-btn:hover{background:var(--primary-dark);}
.pdf-btn{display:flex;align-items:center;gap:5px;padding:5px 12px;background:#fef2f2;color:#dc2626;border:1px solid #fecaca;border-radius:var(--radius-full);cursor:pointer;font-size:.74rem;font-weight:700;font-family:var(--font-body);transition:all .15s;text-decoration:none;}
.pdf-btn:hover{background:#dc2626;color:#fff;}
.progress-bar-outer{background:var(--bg-2);border-radius:20px;height:8px;overflow:hidden;margin:8px 0;}
.progress-bar-inner{height:8px;background:linear-gradient(90deg,var(--primary),#4ade80);border-radius:20px;transition:width .5s ease;}
.what-learn-item{display:flex;gap:10px;margin-bottom:10px;font-size:.88rem;color:var(--text);}
.review-card{padding:16px 0;border-bottom:1px solid var(--border);}
.review-card:last-child{border-bottom:none;}

/* ── Video / Lesson Modal ── */
.lesson-modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.88);z-index:9999;display:none;align-items:center;justify-content:center;}
.lesson-modal-overlay.open{display:flex;}
.lesson-modal-box{background:#0f172a;border-radius:var(--radius-xl);width:94%;max-width:820px;overflow:hidden;display:flex;flex-direction:column;max-height:92vh;}
.lesson-modal-header{display:flex;align-items:center;justify-content:space-between;padding:14px 20px;background:#1e293b;flex-shrink:0;}
.lesson-player{flex:1;background:#000;min-height:420px;position:relative;display:flex;align-items:center;justify-content:center;}
.lesson-player iframe{width:100%;height:420px;border:none;}
.lesson-modal-footer{padding:14px 20px;background:#1e293b;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;flex-shrink:0;}

@media(max-width:900px){.cd-layout{grid-template-columns:1fr;}.cd-sidebar{position:static;}.lesson-player{min-height:280px;}.lesson-player iframe{height:280px;}}
@media(max-width:600px){.course-hero{padding:32px 0 24px;}.cd-sidebar-thumb{height:150px;font-size:4rem;}.cd-sidebar-body{padding:16px;}.lesson-player{min-height:220px;}.lesson-player iframe{height:220px;}.lesson-modal-box{width:100%;max-width:100%;border-radius:0;height:100vh;max-height:100vh;}}
</style>
@endsection

@section('content')

{{-- ── HERO ── --}}
<section class="course-hero">
  <div class="container">
    <div style="max-width:720px;">
      <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px;font-size:.82rem;">
        <a href="{{ route('learn') }}" style="color:rgba(255,255,255,.6);text-decoration:none;"><i class="fas fa-arrow-left"></i> Learning Center</a>
        <span style="color:rgba(255,255,255,.3);">/</span>
        <span style="color:rgba(255,255,255,.6);">{{ ucwords(str_replace('_',' ',$course->category)) }}</span>
      </div>
      <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px;">
        @if($course->access_type==='free')   <span class="badge badge-green">FREE</span> @endif
        @if($course->is_featured)            <span class="badge badge-earth">Bestseller</span> @endif
        @if($course->has_certificate)        <span class="badge badge-sky">🎓 Certificate</span> @endif
        <span class="badge" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.2);">{{ ucfirst($course->level) }}</span>
      </div>
      <h1 style="font-family:var(--font-display);font-size:clamp(1.6rem,4vw,2.4rem);font-weight:800;margin-bottom:12px;line-height:1.2;">{{ $course->title }}</h1>
      <p style="opacity:.85;font-size:1rem;line-height:1.7;margin-bottom:20px;max-width:600px;">{{ $course->description }}</p>

      {{-- Use loaded lessons for accurate counts --}}
      @php
        $lessonCount = $course->lessons->count();
        $totalMins   = $course->lessons->sum('duration_minutes');
        $hrs  = intdiv($totalMins, 60);
        $mins = $totalMins % 60;
      @endphp
      <div style="display:flex;gap:20px;flex-wrap:wrap;font-size:.82rem;opacity:.8;">
        <span><i class="fas fa-star" style="color:#fbbf24;"></i> {{ number_format($course->average_rating,1) }} ({{ $course->total_reviews }} reviews)</span>
        <span><i class="fas fa-users"></i> {{ number_format($course->total_enrolled) }} enrolled</span>
        <span><i class="fas fa-play-circle"></i> {{ $lessonCount }} lessons</span>
        <span><i class="fas fa-clock"></i> {{ $hrs }}h {{ $mins }}m total</span>
        <span><i class="fas fa-chalkboard-teacher"></i> {{ $course->instructor->name ?? 'Instructor' }}</span>
      </div>
    </div>
  </div>
</section>

<div class="section" style="background:var(--bg-2);">
  <div class="container">
    <div class="cd-layout">

      {{-- ── MAIN CONTENT ── --}}
      <div>

        {{-- Progress bar (enrolled active users only) --}}
        @if($enrollment && $enrollment->status === 'active')
          @php $pct = $enrollment->progressPercentage(); @endphp
          <div style="background:var(--green-50);border:1.5px solid var(--green-200);border-radius:var(--radius-lg);padding:18px 22px;margin-bottom:22px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
              <span style="font-weight:700;color:var(--green-700);font-size:.9rem;">📚 Your Progress</span>
              <span style="font-family:var(--font-display);font-weight:800;color:var(--primary);">{{ $pct }}%</span>
            </div>
            <div class="progress-bar-outer"><div class="progress-bar-inner" style="width:{{ $pct }}%;"></div></div>
            <div style="font-size:.78rem;color:var(--text-muted);margin-top:5px;">
              {{ round($lessonCount * $pct / 100) }}/{{ $lessonCount }} lessons completed
            </div>
          </div>
        @elseif($enrollment && $enrollment->status === 'completed')
          <div style="background:var(--green-50);border:1.5px solid var(--green-200);border-radius:var(--radius-lg);padding:16px 22px;margin-bottom:22px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <span style="font-weight:700;color:var(--green-700);">🎉 Course Completed — {{ $enrollment->completed_at?->format('M j, Y') }}</span>
            @if($enrollment->certificate_number)
              <a href="{{ route('learn.certificate', $enrollment) }}" class="btn btn-primary btn-sm"><i class="fas fa-certificate"></i> Download Certificate</a>
            @endif
          </div>
        @endif

        {{-- What You'll Learn --}}
        @if($course->what_you_learn)
          <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:24px;margin-bottom:22px;">
            <h2 style="font-family:var(--font-display);font-size:1.05rem;font-weight:800;color:var(--text);margin-bottom:16px;">✅ What You'll Learn</h2>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
              @foreach(explode(',',$course->what_you_learn) as $item)
                @if(trim($item))
                  <div class="what-learn-item">
                    <i class="fas fa-check-circle" style="color:var(--primary);margin-top:2px;flex-shrink:0;"></i>
                    {{ trim($item) }}
                  </div>
                @endif
              @endforeach
            </div>
          </div>
        @endif

        {{-- ── LESSON LIST ── --}}
        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:24px;margin-bottom:22px;">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:8px;">
            <h2 style="font-family:var(--font-display);font-size:1.05rem;font-weight:800;color:var(--text);">
              📋 Course Lessons ({{ $lessonCount }})
            </h2>
            <div style="font-size:.8rem;color:var(--text-muted);">
              <i class="fas fa-clock"></i> {{ $hrs }}h {{ $mins }}m ·
              <i class="fas fa-play-circle"></i> {{ $lessonCount }} lessons
            </div>
          </div>

          @if($course->lessons->count() > 0)
            @foreach($course->lessons->sortBy('sort_order') as $i => $lesson)
              @php
                // Uses pre-loaded $completedLessonIds from show() — no DB query in loop
                $isCompleted = in_array($lesson->id, $completedLessonIds ?? []);
                $canAccess   = $lesson->is_free_preview || ($enrollment && $enrollment->status === 'active') || ($enrollment && $enrollment->status === 'completed');
                $lessonMins  = $lesson->duration_minutes ?? 0;
                $lessonHrs   = intdiv($lessonMins, 60);
                $lessonRem   = $lessonMins % 60;
                $durationStr = $lessonHrs > 0 ? $lessonHrs.'h '.$lessonRem.'m' : $lessonMins.'m';
              @endphp
              <div class="lesson-row">
                {{-- Number / checkmark --}}
                <div class="lesson-num {{ $isCompleted ? 'done' : ($lesson->is_free_preview ? 'free-preview' : '') }}">
                  @if($isCompleted)
                    <i class="fas fa-check" style="font-size:.65rem;"></i>
                  @else
                    {{ $i + 1 }}
                  @endif
                </div>

                {{-- Icon by type --}}
                <i class="fas {{ $lesson->type === 'video' ? 'fa-play-circle' : ($lesson->type === 'pdf' ? 'fa-file-pdf' : ($lesson->type === 'quiz' ? 'fa-question-circle' : 'fa-file-alt')) }}"
                   style="color:{{ $lesson->type === 'pdf' ? '#ef4444' : 'var(--primary)' }};font-size:.9rem;flex-shrink:0;"></i>

                {{-- Title --}}
                <div style="flex:1;">
                  <div class="lesson-title">{{ $lesson->title }}</div>
                  @if($lesson->is_free_preview && !$enrollment)
                    <span style="font-size:.7rem;color:var(--green-600);font-weight:700;">FREE PREVIEW</span>
                  @endif
                </div>

                {{-- Duration --}}
                <span style="font-size:.75rem;color:var(--text-muted);white-space:nowrap;">{{ $durationStr }}</span>

                {{-- Action button --}}
                @if($canAccess)
                  @if($lesson->type === 'pdf' && $lesson->pdf_path)
                    <a href="{{ asset('storage/'.$lesson->pdf_path) }}" target="_blank" class="pdf-btn">
                      <i class="fas fa-file-pdf"></i> Open PDF
                    </a>
                  @elseif($lesson->type === 'video' && $lesson->video_url)
                    <button class="play-btn"
                      onclick="openLessonModal(
                        {{ $lesson->id }},
                        '{{ addslashes($lesson->title) }}',
                        'video',
                        '{{ $lesson->video_url }}',
                        '',
                        {{ $lessonMins }}
                      )">
                      <i class="fas fa-play"></i> {{ $isCompleted ? 'Replay' : 'Play' }}
                    </button>
                  @else
                    <button class="play-btn"
                      onclick="openLessonModal({{ $lesson->id }},'{{ addslashes($lesson->title) }}','{{ $lesson->type }}','','',{{ $lessonMins }})">
                      <i class="fas fa-book-open"></i> Open
                    </button>
                  @endif
                @else
                  <span style="font-size:.74rem;color:var(--text-muted);"><i class="fas fa-lock"></i> Enroll to unlock</span>
                @endif
              </div>
            @endforeach
          @else
            <div style="text-align:center;padding:32px;color:var(--text-muted);">
              <i class="fas fa-hourglass-half" style="font-size:2rem;margin-bottom:12px;display:block;opacity:.3;"></i>
              Lessons are being added — check back soon!
            </div>
          @endif
        </div>

        {{-- ── INSTRUCTOR ── --}}
        @if($course->instructor)
          <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:24px;margin-bottom:22px;">
            <h2 style="font-family:var(--font-display);font-size:1.05rem;font-weight:800;color:var(--text);margin-bottom:16px;">👨‍🏫 About the Instructor</h2>
            <div style="display:flex;gap:14px;align-items:flex-start;">
              <div class="avatar avatar-lg" style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;font-size:1rem;flex-shrink:0;">
                {{ strtoupper(substr($course->instructor->name, 0, 2)) }}
              </div>
              <div>
                <div style="font-family:var(--font-display);font-size:1rem;font-weight:700;color:var(--text);">{{ $course->instructor->name }}</div>
                <div style="font-size:.82rem;color:var(--primary);font-weight:600;margin-bottom:6px;">{{ $course->instructor->specialization ?? $course->instructor->title ?? 'Agricultural Expert' }}</div>
                <p style="font-size:.84rem;color:var(--text-muted);line-height:1.6;">{{ $course->instructor->bio ?? 'Expert agricultural trainer with extensive field experience across Malawi and Zambia.' }}</p>
              </div>
            </div>
          </div>
        @endif

        {{-- ── REVIEWS ── --}}
        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:24px;margin-bottom:22px;">
          <h2 style="font-family:var(--font-display);font-size:1.05rem;font-weight:800;color:var(--text);margin-bottom:6px;">⭐ Student Reviews</h2>
          <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;flex-wrap:wrap;">
            <div style="text-align:center;">
              <div style="font-family:var(--font-display);font-size:3rem;font-weight:800;color:var(--primary);line-height:1;">{{ number_format($course->average_rating, 1) }}</div>
              <div style="color:#f59e0b;font-size:1.1rem;">{{ str_repeat('★', round($course->average_rating)) }}{{ str_repeat('☆', 5 - round($course->average_rating)) }}</div>
              <div style="font-size:.75rem;color:var(--text-muted);">{{ $course->total_reviews }} reviews</div>
            </div>
            <div style="flex:1;min-width:180px;">
              @foreach([5,4,3,2,1] as $star)
                @php $pctStar = $star === 5 ? 68 : ($star === 4 ? 20 : ($star === 3 ? 8 : ($star === 2 ? 3 : 1))); @endphp
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:5px;">
                  <span style="font-size:.74rem;color:var(--text-muted);width:10px;">{{ $star }}</span>
                  <i class="fas fa-star" style="color:#f59e0b;font-size:.72rem;"></i>
                  <div style="flex:1;background:var(--bg-2);border-radius:20px;height:6px;overflow:hidden;">
                    <div style="width:{{ $pctStar }}%;height:6px;background:#f59e0b;border-radius:20px;"></div>
                  </div>
                  <span style="font-size:.72rem;color:var(--text-muted);width:28px;">{{ $pctStar }}%</span>
                </div>
              @endforeach
            </div>
          </div>

          @if($course->reviews->count() > 0)
            @foreach($course->reviews->take(4) as $review)
              <div class="review-card">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                  <div class="avatar avatar-sm" style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;font-size:.65rem;flex-shrink:0;">
                    {{ $review->user->initials ?? 'FA' }}
                  </div>
                  <div>
                    <div style="font-weight:700;font-size:.85rem;color:var(--text);">{{ $review->user->full_name ?? 'Farmer' }}</div>
                    <div style="color:#f59e0b;font-size:.8rem;">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</div>
                  </div>
                  <div style="margin-left:auto;font-size:.74rem;color:var(--text-muted);">{{ $review->created_at->format('M Y') }}</div>
                </div>
                @if($review->review)
                  <p style="font-size:.84rem;color:var(--text-muted);line-height:1.6;">{{ $review->review }}</p>
                @endif
              </div>
            @endforeach
          @else
            <div style="text-align:center;padding:20px;color:var(--text-muted);">No reviews yet. Be the first!</div>
          @endif

          {{-- Submit review form (completed enrollment only) --}}
          @if($enrollment && $enrollment->status === 'completed')
            <div style="margin-top:20px;padding-top:20px;border-top:1px solid var(--border);">
              <div style="font-weight:700;font-size:.9rem;margin-bottom:14px;">Leave a Review</div>
              <form method="POST" action="{{ route('learn.review', $course) }}">
                @csrf
                <div style="display:flex;gap:6px;margin-bottom:12px;" id="starRow">
                  @for($i = 1; $i <= 5; $i++)
                    <label style="cursor:pointer;font-size:1.6rem;color:var(--border);transition:color .15s;" id="star-label-{{ $i }}">
                      <input type="radio" name="rating" value="{{ $i }}" style="display:none;" onchange="setRating({{ $i }})"/>
                      ★
                    </label>
                  @endfor
                </div>
                <textarea name="review" class="form-input" rows="3" placeholder="Share your experience with this course..." style="resize:vertical;margin-bottom:12px;"></textarea>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-paper-plane"></i> Submit Review</button>
              </form>
            </div>
          @endif
        </div>

      </div>

      {{-- ── SIDEBAR ── --}}
      <div class="cd-sidebar">
        @php
          $catEmojis = ['soil_crops'=>'🌽','livestock'=>'🐄','agri_tech'=>'🚁','agribusiness'=>'📊','organic'=>'🥦','irrigation'=>'💧','post_harvest'=>'🌾'];
          $catColors = ['soil_crops'=>'#dcfce7,#bbf7d0','livestock'=>'#fef9c3,#fef08a','agri_tech'=>'#e0f2fe,#bae6fd','agribusiness'=>'#f5f3ff,#ede9fe','organic'=>'#f0fdf4,#dcfce7','irrigation'=>'#e0f2fe,#bae6fd','post_harvest'=>'#fff7ed,#fed7aa'];
          $emoji = $catEmojis[$course->category] ?? '🌱';
          $bg    = $catColors[$course->category] ?? '#dcfce7,#bbf7d0';
        @endphp
        <div class="cd-sidebar-thumb" style="background:linear-gradient(135deg,{{ $bg }});">{{ $emoji }}</div>
        <div class="cd-sidebar-body">
          {{-- Price --}}
          @if($course->access_type === 'free')
            <div class="cd-price" style="color:var(--green-600);">FREE</div>
          @else
            <div class="cd-price">{{ $course->currency }} {{ number_format($course->price) }}</div>
          @endif

          {{-- CTA Button --}}
          @if($enrollment)
            @if($enrollment->status === 'completed')
              <a href="{{ route('learn.certificate', $enrollment) }}" class="btn btn-primary btn-md" style="width:100%;justify-content:center;margin-bottom:10px;">
                <i class="fas fa-certificate"></i> Download Certificate
              </a>
            @else
              {{-- Open first available lesson --}}
              @php $firstLesson = $course->lessons->sortBy('sort_order')->first(); @endphp
              @if($firstLesson)
                <button onclick="openLessonModal({{ $firstLesson->id }},'{{ addslashes($firstLesson->title) }}','{{ $firstLesson->type }}','{{ $firstLesson->video_url }}','{{ $firstLesson->pdf_path }}',{{ $firstLesson->duration_minutes }})"
                        class="btn btn-primary btn-md" style="width:100%;justify-content:center;margin-bottom:10px;">
                  <i class="fas fa-play"></i> Continue Learning
                </button>
              @else
                <button class="btn btn-primary btn-md" style="width:100%;justify-content:center;margin-bottom:10px;" disabled>
                  <i class="fas fa-hourglass-half"></i> Lessons Coming Soon
                </button>
              @endif
              <div style="text-align:center;font-size:.8rem;color:var(--primary);font-weight:600;margin-bottom:10px;">
                <i class="fas fa-graduation-cap"></i> You are enrolled
              </div>
            @endif
          @elseif($course->access_type === 'free')
            @auth
              <form method="POST" action="{{ route('learn.enroll', $course) }}">
                @csrf
                <button type="submit" class="btn btn-primary btn-md" style="width:100%;justify-content:center;margin-bottom:10px;">
                  <i class="fas fa-plus"></i> Enroll Free — Start Now
                </button>
              </form>
            @else
              <a href="{{ route('register') }}" class="btn btn-primary btn-md" style="width:100%;justify-content:center;margin-bottom:10px;">
                <i class="fas fa-seedling"></i> Create Free Account
              </a>
            @endauth
          @else
            @auth
              <button class="btn btn-primary btn-md" style="width:100%;justify-content:center;margin-bottom:10px;"
                      onclick="showToast('💳 Paid enrollment — contact us to enroll','info')">
                <i class="fas fa-lock-open"></i> Enroll for {{ $course->currency }} {{ number_format($course->price) }}
              </button>
            @else
              <a href="{{ route('register') }}" class="btn btn-primary btn-md" style="width:100%;justify-content:center;margin-bottom:10px;">
                <i class="fas fa-seedling"></i> Create Free Account
              </a>
            @endauth
          @endif

          {{-- Course includes --}}
          <div style="background:var(--bg-2);border-radius:var(--radius-md);padding:14px;margin-top:6px;">
            <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:10px;">Course Includes</div>
            @foreach([
              ['fas fa-play-circle', $lessonCount.' lessons'],
              ['fas fa-clock',       $hrs.'h '.$mins.'m of content'],
              ['fas fa-mobile-alt',  'Mobile & desktop access'],
              ['fas fa-infinity',    'Lifetime access'],
            ] as [$icon, $label])
              <div style="display:flex;align-items:center;gap:8px;margin-bottom:7px;font-size:.82rem;color:var(--text);">
                <i class="{{ $icon }}" style="color:var(--primary);width:16px;text-align:center;"></i> {{ $label }}
              </div>
            @endforeach
            @if($course->has_certificate)
              <div style="display:flex;align-items:center;gap:8px;font-size:.82rem;color:var(--text);">
                <i class="fas fa-certificate" style="color:var(--primary);width:16px;text-align:center;"></i> Certificate of completion
              </div>
            @endif
          </div>

          <div style="display:flex;gap:8px;margin-top:14px;">
            <button onclick="showToast('🔗 Link copied!','success')" class="btn btn-outline btn-sm" style="flex:1;justify-content:center;"><i class="fas fa-share-alt"></i> Share</button>
            <a href="{{ route('learn') }}" class="btn btn-outline btn-sm" style="flex:1;justify-content:center;"><i class="fas fa-book"></i> More Courses</a>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════
     LESSON MODAL — plays YouTube, shows PDF download, or text
═══════════════════════════════════════════════════════════════════ --}}
<div class="lesson-modal-overlay" id="lessonModal">
  <div class="lesson-modal-box">
    {{-- Header --}}
    <div class="lesson-modal-header">
      <div>
        <div id="modalLessonTitle" style="font-family:var(--font-display);font-weight:700;color:#fff;font-size:.95rem;">Lesson</div>
        <div id="modalLessonMeta" style="font-size:.74rem;color:rgba(255,255,255,.55);margin-top:2px;"></div>
      </div>
      <button onclick="closeLessonModal()" style="background:rgba(255,255,255,.1);border:none;width:32px;height:32px;border-radius:50%;cursor:pointer;color:#fff;font-size:1rem;">✕</button>
    </div>

    {{-- Player area --}}
    <div class="lesson-player" id="lessonPlayerArea">
      {{-- Content injected by JS --}}
      <div style="color:rgba(255,255,255,.4);font-size:.9rem;">Select a lesson to start</div>
    </div>

    {{-- Footer --}}
    <div class="lesson-modal-footer">
      <div id="modalMarkComplete" style="display:none;">
        @auth
          @if($enrollment && $enrollment->status === 'active')
            <button onclick="markLessonComplete()" class="btn btn-primary btn-sm">
              <i class="fas fa-check"></i> Mark as Complete
            </button>
          @endif
        @endauth
      </div>
      <div style="display:flex;gap:8px;">
        <button onclick="navigateLesson(-1)" class="btn btn-outline btn-sm" style="color:#fff;border-color:rgba(255,255,255,.2);">
          <i class="fas fa-chevron-left"></i> Prev
        </button>
        <button onclick="navigateLesson(1)" class="btn btn-outline btn-sm" style="color:#fff;border-color:rgba(255,255,255,.2);">
          Next <i class="fas fa-chevron-right"></i>
        </button>
      </div>
    </div>
  </div>
</div>

@include('partials.footer')
@endsection

@section('extra_js')
<script>
// ── Lesson data passed from PHP ──────────────────────────────────────
const COMPLETED_IDS = {!! json_encode($completedLessonIds ?? []) !!};
const LESSONS = {!! json_encode(
    $course->lessons->sortBy('sort_order')->values()->map(function($l) {
        return [
            'id'        => $l->id,
            'title'     => $l->title,
            'type'      => $l->type,
            'video_url' => $l->video_url ?? '',
            'pdf_path'  => $l->pdf_path ?? '',
            'duration'  => $l->duration_minutes,
        ];
    })
) !!};

let currentLessonIndex = 0;
let currentLessonId    = null;

// ── YouTube ID extractor ─────────────────────────────────────────────
function getYouTubeId(url) {
  if (!url) return null;
  // Handles all YouTube URL formats
  const patterns = [
    /[?&]v=([a-zA-Z0-9_-]{11})/,           // ?v=ID or &v=ID
    /youtu\.be\/([a-zA-Z0-9_-]{11})/,      // youtu.be/ID
    /embed\/([a-zA-Z0-9_-]{11})/,           // embed/ID
    /shorts\/([a-zA-Z0-9_-]{11})/,          // shorts/ID
    /^([a-zA-Z0-9_-]{11})$/,                 // bare ID
  ];
  for (const re of patterns) {
    const m = url.match(re);
    if (m && m[1]) return m[1];
  }
  return null;
}

function isYouTubeUrl(url) {
  return url && (url.includes('youtube.com') || url.includes('youtu.be'));
}

// ── Open lesson modal ────────────────────────────────────────────────
function openLessonModal(lessonId, title, type, videoUrl, pdfPath, durationMins) {
  currentLessonId    = lessonId;
  currentLessonIndex = LESSONS.findIndex(l => l.id === lessonId);

  document.getElementById('modalLessonTitle').textContent = title;
  document.getElementById('modalLessonMeta').textContent  =
    (durationMins > 0 ? durationMins + ' min · ' : '') + type.charAt(0).toUpperCase() + type.slice(1) + ' lesson';

  const player = document.getElementById('lessonPlayerArea');
  const markEl = document.getElementById('modalMarkComplete');

  // Clear previous content (stops any playing video)
  player.innerHTML = '';

  if (type === 'video' && videoUrl) {
    const ytId = getYouTubeId(videoUrl);
    const openBtn = `
      <div style="margin-top:16px;">
        <a href="${videoUrl}" target="_blank"
           style="display:inline-flex;align-items:center;gap:8px;padding:10px 22px;background:#ff0000;color:#fff;border-radius:var(--radius-md);font-weight:700;text-decoration:none;font-size:.9rem;">
          <svg width="18" height="13" viewBox="0 0 18 13" fill="white"><path d="M17.6 2s-.2-1.4-.8-2c-.7-.8-1.6-.8-2-.8C12.8 0 9 0 9 0S5.2 0 3.2.2c-.4.1-1.3.1-2 .8C.6.6.4 2 .4 2S.2 3.6.2 5.2v1.5c0 1.6.2 3.2.2 3.2s.2 1.4.8 2c.7.8 1.7.8 2.1.8C4.8 13 9 13 9 13s3.8 0 5.8-.2c.4-.1 1.3-.1 2-.8.6-.6.8-2 .8-2s.2-1.6.2-3.2V5.2C17.8 3.6 17.6 2 17.6 2zM7.2 9V3.6l5.4 2.7L7.2 9z"/></svg>
          Watch on YouTube
        </a>
      </div>`;

    if (ytId) {
      // Embed YouTube video
      player.innerHTML = `
        <div style="width:100%;height:100%;display:flex;flex-direction:column;align-items:center;background:#000;padding-top:10px;">
          <iframe
            src="https://www.youtube.com/embed/${ytId}?autoplay=1&rel=0&modestbranding=1"
            width="100%" height="380" frameborder="0"
            allow="autoplay; encrypted-media; fullscreen; picture-in-picture"
            allowfullscreen>
          </iframe>
          ${openBtn}
        </div>`;
    } else if (videoUrl.match(/\.(mp4|webm|ogg)$/i)) {
      player.innerHTML = `
        <video controls autoplay width="100%" height="420" style="background:#000;">
          <source src="${videoUrl}" type="video/mp4">
          Your browser does not support video.
        </video>`;
    } else if (isYouTubeUrl(videoUrl)) {
      // YouTube URL but couldn't extract ID — open directly
      player.innerHTML = `
        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;gap:16px;background:#1e293b;padding:40px;text-align:center;">
          <svg width="60" height="42" viewBox="0 0 18 13" fill="#ff0000"><path d="M17.6 2s-.2-1.4-.8-2c-.7-.8-1.6-.8-2-.8C12.8 0 9 0 9 0S5.2 0 3.2.2c-.4.1-1.3.1-2 .8C.6.6.4 2 .4 2S.2 3.6.2 5.2v1.5c0 1.6.2 3.2.2 3.2s.2 1.4.8 2c.7.8 1.7.8 2.1.8C4.8 13 9 13 9 13s3.8 0 5.8-.2c.4-.1 1.3-.1 2-.8.6-.6.8-2 .8-2s.2-1.6.2-3.2V5.2C17.8 3.6 17.6 2 17.6 2zM7.2 9V3.6l5.4 2.7L7.2 9z"/></svg>
          <div style="color:#fff;font-size:1rem;font-weight:700;">${title}</div>
          <div style="color:rgba(255,255,255,.6);font-size:.85rem;">Click below to watch this video</div>
          ${openBtn}
        </div>`;
    } else {
      player.innerHTML = `
        <iframe src="${videoUrl}" width="100%" height="420" frameborder="0" allowfullscreen></iframe>`;
    }
    if (markEl) markEl.style.display = 'flex';

  } else if (type === 'pdf' && pdfPath) {
    // PDF lesson — use /lesson-pdf/ route to avoid storage permission issues
    const pdfUrl = '/lesson-pdf/' + encodeURIComponent(pdfPath);
    player.innerHTML = `
      <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;gap:20px;background:#1e293b;padding:40px;text-align:center;">
        <i class="fas fa-file-pdf" style="font-size:4rem;color:#ef4444;"></i>
        <div style="color:#fff;font-family:var(--font-display);font-size:1.1rem;font-weight:700;">${title}</div>
        <div style="display:flex;gap:12px;flex-wrap:wrap;justify-content:center;">
          <a href="${pdfUrl}" target="_blank"
             style="display:inline-flex;align-items:center;gap:8px;padding:12px 24px;background:#ef4444;color:#fff;border-radius:var(--radius-md);font-weight:700;text-decoration:none;font-size:.9rem;">
            <i class="fas fa-eye"></i> Open PDF in Browser
          </a>
          <a href="${pdfUrl}" download
             style="display:inline-flex;align-items:center;gap:8px;padding:12px 24px;background:var(--primary);color:#fff;border-radius:var(--radius-md);font-weight:700;text-decoration:none;font-size:.9rem;">
            <i class="fas fa-download"></i> Download PDF
          </a>
        </div>
        <div style="color:rgba(255,255,255,.5);font-size:.8rem;">Opens in a new tab</div>
      </div>`;
    if (markEl) markEl.style.display = 'flex';

  } else if (type === 'quiz') {
    player.innerHTML = `
      <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;gap:16px;background:#1e293b;color:#fff;padding:40px;text-align:center;">
        <i class="fas fa-question-circle" style="font-size:4rem;color:#f59e0b;"></i>
        <div style="font-family:var(--font-display);font-size:1.1rem;font-weight:700;">Quiz: ${title}</div>
        <div style="color:rgba(255,255,255,.6);font-size:.85rem;">Quiz functionality is coming soon. Stay tuned!</div>
      </div>`;
    if (markEl) markEl.style.display = 'flex';

  } else {
    player.innerHTML = `
      <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;gap:16px;background:#1e293b;color:#fff;padding:40px;text-align:center;">
        <i class="fas fa-file-alt" style="font-size:4rem;color:#94a3b8;"></i>
        <div style="font-family:var(--font-display);font-size:1.1rem;font-weight:700;">${title}</div>
        <div style="color:rgba(255,255,255,.6);font-size:.85rem;">Text lesson content. Read the material below.</div>
      </div>`;
    if (markEl) markEl.style.display = 'flex';
  }

  document.getElementById('lessonModal').classList.add('open');
  document.body.style.overflow = 'hidden';
}

// ── Close modal (also stops the video) ──────────────────────────────
function closeLessonModal() {
  document.getElementById('lessonPlayerArea').innerHTML = ''; // stops iframe video
  document.getElementById('lessonModal').classList.remove('open');
  document.body.style.overflow = '';
  currentLessonId = null;
}

// ── Navigate prev/next lesson ────────────────────────────────────────
function navigateLesson(dir) {
  const next = currentLessonIndex + dir;
  if (next < 0 || next >= LESSONS.length) {
    showToast(dir > 0 ? 'You are on the last lesson' : 'This is the first lesson', 'info');
    return;
  }
  const l = LESSONS[next];
  openLessonModal(l.id, l.title, l.type, l.video_url, l.pdf_path, l.duration);
}

// ── Mark lesson complete (AJAX) ──────────────────────────────────────
async function markLessonComplete() {
  if (!currentLessonId) return;
  try {
    const res = await fetch(`/learn/lessons/${currentLessonId}/complete`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
        'Accept': 'application/json',
      },
    });
    const data = await res.json();
    showToast('✅ Lesson marked as complete!', 'success');
    // Update the lesson number in the list to show checkmark
    const lessonRows = document.querySelectorAll('.lesson-row');
    lessonRows.forEach(row => {
      const numEl = row.querySelector('.lesson-num');
      if (numEl && row.querySelector(`[onclick*="openLessonModal(${currentLessonId}"]`)) {
        numEl.classList.add('done');
        numEl.innerHTML = '<i class="fas fa-check" style="font-size:.65rem;"></i>';
      }
    });
    // Auto-advance to next lesson
    setTimeout(() => navigateLesson(1), 1500);
  } catch(e) {
    showToast('Could not save progress. Try again.', 'error');
  }
}

// ── Close on backdrop click or ESC ──────────────────────────────────
document.getElementById('lessonModal')?.addEventListener('click', e => {
  if (e.target === document.getElementById('lessonModal')) closeLessonModal();
});
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') closeLessonModal();
  if (e.key === 'ArrowRight') navigateLesson(1);
  if (e.key === 'ArrowLeft')  navigateLesson(-1);
});

// ── Star rating for review ───────────────────────────────────────────
let selectedRating = 0;
function setRating(n) {
  selectedRating = n;
  for (let i = 1; i <= 5; i++) {
    const el = document.getElementById('star-label-' + i);
    if (el) el.style.color = i <= n ? '#f59e0b' : 'var(--border)';
  }
}
// Hover effects
document.querySelectorAll('#starRow label').forEach((label, idx) => {
  label.addEventListener('mouseover', () => {
    for (let i = 0; i < 5; i++) {
      const el = document.querySelectorAll('#starRow label')[i];
      if (el) el.style.color = i <= idx ? '#f59e0b' : 'var(--border)';
    }
  });
  label.addEventListener('mouseout', () => setRating(selectedRating));
});
</script>
@endsection
