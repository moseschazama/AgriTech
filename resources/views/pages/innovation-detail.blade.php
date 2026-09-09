@extends('layouts.app')
@section('title', ($innovation->title ?? 'Innovation') . ' — Innovation Hub | AgriTech Pro')
@section('extra_css')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"/>
<link rel="stylesheet" href="{{ asset('css/home.css') }}"/>
<style>
.vote-btn{display:flex;align-items:center;gap:6px;background:var(--bg-2);border:1.5px solid var(--border);border-radius:var(--radius-full);padding:8px 18px;font-size:.875rem;font-weight:700;color:var(--text);cursor:pointer;transition:all .15s;flex-shrink:0;text-decoration:none;}
.vote-btn:hover,.vote-btn.voted{background:var(--primary);border-color:var(--primary);color:#fff;}
.vote-btn:hover{transform:translateY(-1px);}
.detail-grid{display:grid;grid-template-columns:1fr 1.7fr;gap:32px;align-items:start;}
.detail-side{display:flex;flex-direction:column;gap:18px;}
.detail-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:22px;box-shadow:var(--shadow-sm);}
.detail-stat{display:flex;align-items:center;gap:12px;padding:10px 0;}
.detail-stat + .detail-stat{border-top:1px solid var(--border);}
.detail-stat-icon{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:.95rem;flex-shrink:0;}
.detail-stat-val{font-size:.95rem;font-weight:700;color:var(--text);line-height:1.2;}
.detail-stat-lbl{font-size:.75rem;color:var(--text-muted);}
.step-item{display:flex;gap:12px;padding:10px 0;}
.step-item + .step-item{border-top:1px solid var(--border);}
.step-num{width:26px;height:26px;border-radius:50%;background:var(--green-100);color:var(--green-700);font-size:.78rem;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px;}
@supports (-webkit-touch-callout:none) {.detail-grid{grid-template-columns:1fr;}}
@media(max-width:900px){.detail-grid{grid-template-columns:1fr;gap:22px;}}
.gallery-thumb{cursor:pointer;border:2px solid var(--border);border-radius:var(--radius-md);overflow:hidden;transition:all .15s;}
.gallery-thumb.active,.gallery-thumb:hover{border-color:var(--primary);}
</style>
@endsection

@section('content')

<div class="section" style="background:var(--bg-2);padding-top:48px;">
  <div class="container">

    <nav style="margin-bottom:24px;">
      <a href="{{ route('innovation') }}" style="display:inline-flex;align-items:center;gap:8px;font-size:.86rem;font-weight:700;color:var(--primary);text-decoration:none;">
        <i class="fas fa-arrow-left"></i> Back to Innovation Hub
      </a>
    </nav>

    @php
      $catImgs=['water_management'=>'irrigation.jpg','technology'=>'agritech-drone.jpg','infrastructure'=>'greenhouse.jpg','crop_solutions'=>'maize-field.jpg','energy'=>'sunflower.jpg','post_harvest'=>'harvest.jpg','livestock'=>'cows.jpg','business'=>'market-stall.jpg'];
      $cover    = asset('assets/img/agri/'.($catImgs[$innovation->category] ?? 'seedling.jpg'));
      $images   = $innovation->images ?? [];
      $mainImg  = count($images) > 0 ? asset('storage/'.$images[0]) : $cover;
      $isWinner = $innovation->winner_position;
      $medalLabels = [1=>'🏆 1st Place Winner',2=>'🥈 2nd Place Winner',3=>'🥉 3rd Place Winner'];
    @endphp

    <div class="detail-grid">
      {{-- LEFT: cover + stats --}}
      <div class="detail-side">
        <div style="position:relative;border-radius:var(--radius-xl);overflow:hidden;box-shadow:var(--shadow-lg);">
          <img id="detailMainImg" src="{{ $mainImg }}" alt="{{ $innovation->title }}" style="width:100%;height:320px;object-fit:cover;display:block;"/>
          @if($innovation->in_competition)
            <span style="position:absolute;top:14px;left:14px;background:#d97706;color:#fff;font-size:.75rem;font-weight:700;padding:4px 12px;border-radius:20px;">🏆 Competition Entry</span>
          @endif
          @if($isWinner)
            <span style="position:absolute;top:14px;right:14px;background:#b45309;color:#fff;font-size:.75rem;font-weight:800;padding:4px 12px;border-radius:20px;">{{ $medalLabels[$isWinner] ?? 'Winner' }}</span>
          @endif
        </div>

        @if(count($images) > 1)
          <div style="display:flex;gap:10px;overflow-x:auto;padding-bottom:4px;">
            @foreach($images as $i => $img)
              <div class="gallery-thumb {{ $i===0?'active':'' }}" onclick="switchImage(this,'{{ asset('storage/'.$img) }}')" style="flex-shrink:0;">
                <img src="{{ asset('storage/'.$img) }}" alt="Photo {{ $i+1 }}" style="width:72px;height:56px;object-fit:cover;display:block;"/>
              </div>
            @endforeach
          </div>
        @endif

        <div class="detail-card">
          <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);margin-bottom:6px;">Details</div>
          <div class="detail-stat">
            <div class="detail-stat-icon" style="background:var(--green-100);color:var(--green-700);"><i class="fas fa-thumbs-up"></i></div>
            <div>
              <div class="detail-stat-val" id="detailVoteCount">{{ number_format($innovation->vote_count) }}</div>
              <div class="detail-stat-lbl">Community votes</div>
            </div>
          </div>
          <div class="detail-stat">
            <div class="detail-stat-icon" style="background:#e0f2fe;color:var(--sky-600);"><i class="fas fa-eye"></i></div>
            <div>
              <div class="detail-stat-val">{{ number_format($innovation->view_count) }}</div>
              <div class="detail-stat-lbl">Views</div>
            </div>
          </div>
          <div class="detail-stat">
            <div class="detail-stat-icon" style="background:#fff7ed;color:#c2410c;"><i class="fas fa-map-marker-alt"></i></div>
            <div>
              <div class="detail-stat-val">{{ $innovation->district ?? 'Malawi' }}</div>
              <div class="detail-stat-lbl">District</div>
            </div>
          </div>
          @if($innovation->estimated_cost)
            <div class="detail-stat">
              <div class="detail-stat-icon" style="background:#fef9c3;color:#a16207;"><i class="fas fa-coins"></i></div>
              <div>
                <div class="detail-stat-val">{{ $innovation->estimated_cost }}</div>
                <div class="detail-stat-lbl">Estimated cost</div>
              </div>
            </div>
          @endif
          @if($innovation->in_competition && $innovation->competition)
            <div class="detail-stat">
              <div class="detail-stat-icon" style="background:#f5f3ff;color:#6d28d9;"><i class="fas fa-trophy"></i></div>
              <div>
                <div class="detail-stat-val" style="font-size:.86rem;">{{ $innovation->competition->title }}</div>
                <div class="detail-stat-lbl">Competition round</div>
              </div>
            </div>
          @endif
        </div>

        <div class="detail-card" style="display:flex;align-items:center;gap:14px;">
          <div class="avatar avatar-md" style="background:#15803d;color:#fff;">{{ $innovation->user->initials ?? 'FA' }}</div>
          <div style="min-width:0;">
            <div style="font-size:.95rem;font-weight:700;color:var(--text);">{{ $innovation->user->full_name ?? 'Farmer' }}</div>
            <div style="font-size:.78rem;color:var(--text-muted);">Innovator · {{ $innovation->district ?? 'Malawi' }}</div>
          </div>
        </div>
      </div>

      {{-- RIGHT: content --}}
      <div class="detail-card" style="padding:28px;">
        <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--primary);margin-bottom:10px;">
          {{ ucwords(str_replace('_',' ',$innovation->category)) }}
        </div>
        <h1 style="font-size:clamp(1.4rem,2.6vw,2rem);font-weight:800;color:var(--text);letter-spacing:-.02em;line-height:1.2;margin-bottom:16px;">{{ $innovation->title }}</h1>

        @if($innovation->impact_summary)
          <div style="display:inline-flex;align-items:center;gap:7px;background:var(--green-50);border:1px solid var(--green-200);color:var(--green-700);font-size:.82rem;font-weight:600;padding:7px 14px;border-radius:var(--radius-full);margin-bottom:18px;">
            <i class="fas fa-chart-line"></i> {{ $innovation->impact_summary }}
          </div>
        @endif

        <div style="font-size:.95rem;color:var(--text);line-height:1.8;margin-bottom:22px;white-space:pre-line;">{{ $innovation->description }}</div>

        <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;padding:18px 0;border-top:1px solid var(--border);border-bottom:1px solid var(--border);margin-bottom:22px;">
          @auth
            @if(isset($hasVoted) && $hasVoted)
              <button type="button" class="vote-btn voted" title="You already voted this round — one vote per innovation."><i class="fas fa-check"></i> Voted · <span id="detailVoteBtnCount">{{ number_format($innovation->vote_count) }}</span></button>
            @else
              <form method="POST" action="{{ route('innovation.vote',$innovation) }}" class="innov-vote-form" data-ajax="true" style="display:inline;">
                @csrf
                <button type="submit" class="vote-btn" title="Vote — one vote per innovation per round"><i class="fas fa-thumbs-up"></i> Vote · <span id="detailVoteBtnCount">{{ number_format($innovation->vote_count) }}</span></button>
              </form>
            @endif
          @else
            <a href="{{ route('login') }}" class="vote-btn" title="Sign in to vote"><i class="fas fa-thumbs-up"></i> Vote · {{ number_format($innovation->vote_count) }}</a>
          @endauth
          <span style="font-size:.8rem;color:var(--text-muted);">One vote per innovation per competition round.</span>
        </div>

        @if($innovation->implementation_steps)
          <div style="margin-bottom:10px;">
            <h2 style="font-size:1.05rem;font-weight:800;color:var(--text);margin-bottom:6px;"><i class="fas fa-list-ol" style="color:var(--primary);margin-right:8px;"></i>How to Replicate</h2>
            <div>
              @foreach(preg_split('/\r\n|\r|\n/', $innovation->implementation_steps) as $step)
                @if(trim($step))
                  <div class="step-item">
                    <div class="step-num">{{ $loop->iteration }}</div>
                    <div style="font-size:.88rem;color:var(--text);line-height:1.6;">{{ trim($step) }}</div>
                  </div>
                @endif
              @endforeach
            </div>
          </div>
        @endif
      </div>
    </div>

    <div style="display:flex;justify-content:center;gap:14px;flex-wrap:wrap;margin-top:44px;">
      <a href="{{ route('innovation') }}" class="btn btn-outline btn-lg"><i class="fas fa-lightbulb"></i> Browse All Innovations</a>
      @auth
        <a href="{{ route('innovation.store') }}" class="btn btn-primary btn-lg"><i class="fas fa-plus"></i> Submit Your Innovation</a>
      @else
        <a href="{{ route('register') }}" class="btn btn-primary btn-lg"><i class="fas fa-plus"></i> Join & Submit Your Innovation</a>
      @endauth
    </div>

  </div>
</div>

@include('partials.footer')
@endsection

@section('extra_js')
<script>
function switchImage(thumb, src) {
  document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
  thumb.classList.add('active');
  const main = document.getElementById('detailMainImg');
  if (main) main.src = src;
}

document.addEventListener('ajax:success', function (e) {
  const form = e.target;
  if (!form.classList.contains('innov-vote-form')) return;
  const data = e.detail;
  document.querySelectorAll('[id=detailVoteCount],[id=detailVoteBtnCount]').forEach(el => {
    if (typeof data.vote_count !== 'undefined') el.textContent = Number(data.vote_count).toLocaleString();
  });
  const btn = form.querySelector('[type=submit]');
  if (btn && data.voted) {
    btn.classList.add('voted');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-check"></i> Voted · <span>' + Number(data.vote_count).toLocaleString() + '</span>';
    btn.title = 'You already voted this round — one vote per innovation.';
  }
  if (data && data.toast === false) showToast(data.message || 'Vote recorded.', data.voted ? 'success' : 'info');
});
</script>
@endsection