@extends('layouts.app')
@section('title', 'My Courses — AgriTech Pro')
@section('extra_css')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"/>
<style>
.progress-bar-outer{background:var(--bg-2);border-radius:20px;height:7px;overflow:hidden;margin:8px 0;}
.progress-bar-inner{height:7px;background:var(--primary);border-radius:20px;}
.course-enroll-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;transition:all .2s;}
.course-enroll-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-lg);}
.my-courses-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px;}
@media(max-width:900px){.my-courses-grid{grid-template-columns:repeat(2,1fr);}}
@media(max-width:560px){.my-courses-grid{grid-template-columns:1fr;}}
</style>
@endsection

@section('content')
<div class="section" style="background:var(--bg-2);">
  <div class="container">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
      <div>
        <h1 style="font-size:1.5rem;font-weight:700;color:var(--text);letter-spacing:-0.02em;">🎓 My Courses</h1>
        <p style="color:var(--text-muted);">{{ isset($enrollments) ? $enrollments->total() : 0 }} course(s) enrolled</p>
      </div>
      <div style="display:flex;gap:10px;">
        <a href="{{ route('learn') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Browse More Courses</a>
        <a href="{{ route('dashboard') }}" class="btn btn-outline btn-sm"><i class="fas fa-th-large"></i> Dashboard</a>
      </div>
    </div>

    @if(session('success'))
      <div style="background:var(--green-50);border:1.5px solid var(--green-200);border-radius:var(--radius-md);padding:12px 18px;margin-bottom:20px;color:var(--green-700);font-weight:600;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
      </div>
    @endif

    <div class="my-courses-grid">
      @forelse(isset($enrollments) ? $enrollments : [] as $enrollment)
        @php
          $course = $enrollment->course;
          $pct    = $enrollment->progressPercentage();
          // Use loaded lessons count — more accurate than cached DB column
          $lessonCount = $course->lessons->count();
          $totalMins   = $course->lessons->sum('duration_minutes');
          $hrs  = intdiv($totalMins, 60);
          $mins = $totalMins % 60;
          $catEmojis = ['soil_crops'=>'🌽','livestock'=>'🐄','agri_tech'=>'🚁','agribusiness'=>'📊','organic'=>'🥦','irrigation'=>'💧','post_harvest'=>'🌾'];
          $catColors = ['soil_crops'=>'#dcfce7,#bbf7d0','livestock'=>'#fef9c3,#fef08a','agri_tech'=>'#e0f2fe,#bae6fd','agribusiness'=>'#f5f3ff,#ede9fe','organic'=>'#f0fdf4,#dcfce7','irrigation'=>'#e0f2fe,#bae6fd','post_harvest'=>'#fff7ed,#fed7aa'];
          $emoji = $catEmojis[$course->category] ?? '🌱';
          $bg    = $catColors[$course->category] ?? '#dcfce7,#bbf7d0';
        @endphp
        <div class="course-enroll-card">
          {{-- Thumb --}}
          <div style="height:140px;background:{{ explode(',', $bg)[0] }};display:flex;align-items:center;justify-content:center;font-size:3.5rem;position:relative;">
            {{ $emoji }}
            @if($enrollment->status === 'completed')
              <span style="position:absolute;top:10px;right:10px;background:var(--primary);color:#fff;font-size:.75rem;font-weight:700;padding:3px 10px;border-radius:20px;">✓ Completed</span>
            @endif
          </div>
          {{-- Body --}}
          <div style="padding:18px;">
            <div style="font-size:.75rem;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">
              {{ ucwords(str_replace('_',' ',$course->category)) }}
            </div>
            <div style="font-size:1.0625rem;font-weight:600;color:var(--text);letter-spacing:-0.01em;margin-bottom:6px;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
              {{ $course->title }}
            </div>
            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:10px;">
              by {{ $course->instructor->name ?? 'Instructor' }} ·
              {{ $lessonCount }} lessons ·
              {{ $hrs }}h {{ $mins }}m
            </div>

            {{-- Progress bar --}}
            <div class="progress-bar-outer"><div class="progress-bar-inner" style="width:{{ $pct }}%;"></div></div>
            <div style="display:flex;justify-content:space-between;font-size:.75rem;color:var(--text-muted);margin-bottom:14px;">
              <span>{{ $pct }}% complete</span>
              <span>{{ round($lessonCount * $pct / 100) }}/{{ $lessonCount }} lessons</span>
            </div>

            {{-- Actions --}}
            <div style="display:flex;gap:8px;">
              @if($enrollment->status === 'completed' && $enrollment->certificate_number)
                <a href="{{ route('learn.certificate.pdf', $enrollment) }}" class="btn btn-outline btn-sm" style="flex:1;justify-content:center;font-size:.8125rem;">
                  <i class="fas fa-download"></i> Certificate PDF
                </a>
              @endif
              <a href="{{ route('learn.show', $course) }}" class="btn btn-primary btn-sm" style="flex:1;justify-content:center;font-size:.8125rem;">
                <i class="fas fa-play"></i> {{ $enrollment->status === 'completed' ? 'Review Course' : 'Continue' }}
              </a>
            </div>
          </div>
        </div>
      @empty
        <div style="grid-column:1/-1;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:60px;text-align:center;">
          <i class="fas fa-graduation-cap" style="font-size:3.5rem;color:var(--text-muted);margin-bottom:18px;display:block;opacity:.25;"></i>
          <h3 style="font-size:1.25rem;font-weight:600;letter-spacing:-0.01em;margin-bottom:8px;">No courses enrolled yet</h3>
          <p style="color:var(--text-muted);margin-bottom:24px;">Enroll in courses and start growing your farming skills today.</p>
          <a href="{{ route('learn') }}" class="btn btn-primary btn-lg"><i class="fas fa-graduation-cap"></i> Browse Courses</a>
        </div>
      @endforelse
    </div>

    @if(isset($enrollments) && $enrollments->hasPages())
      <div style="margin-top:28px;">{{ $enrollments->links() }}</div>
    @endif
  </div>
</div>
@include('partials.footer')
@endsection
