@extends('layouts.app')
@section('title', 'Innovation Hub — AgriTech Pro')
@section('extra_css')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"/>
<link rel="stylesheet" href="{{ asset('css/home.css') }}"/>
<style>
.innov-hero{--hero-glow-1:rgba(99,102,241,0.07);--hero-glow-2:rgba(22,163,74,0.04);--hero-orb:rgba(99,102,241,0.06);--hero-badge-bg:rgba(99,102,241,0.1);--hero-badge-fg:#6366f1;--hero-badge-border:rgba(99,102,241,0.2);--hero-accent-color:#6366f1;--hero-overlay-start:rgba(15,12,35,0.78);--hero-overlay-mid:rgba(20,15,45,0.55);--hero-overlay-end:rgba(10,8,30,0.72);--hero-overlay-accent:rgba(129,140,248,0.15);}
.innov-hero.page-hero-image{background-image:url('https://images.unsplash.com/photo-1530836369250-ef72a3f5cda8?auto=format&fit=crop&w=1920&q=80');}
.innov-hero.page-hero-image .page-hero-badge{background:rgba(129,140,248,0.2);border-color:rgba(129,140,248,0.35);color:#c7d2fe;}
.innov-hero.page-hero-image .page-hero-title .accent{color:#a5b4fc;}
.innov-hero.page-hero-image .page-hero-pill i,.innov-hero.page-hero-image .page-hero-stat i{color:#a5b4fc;}
.innov-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px;}
.innov-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;transition:all .2s;}
.innov-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-lg);}
.innov-thumb{height:200px;position:relative;display:flex;align-items:center;justify-content:center;font-size:4rem;overflow:hidden;}
.innov-body{padding:20px;}
.innov-cat-badge{font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--primary);margin-bottom:8px;}
.innov-title{font-size:1.0625rem;font-weight:600;color:var(--text);margin-bottom:8px;line-height:1.35;letter-spacing:-0.01em;word-break:break-word;}
.innov-desc{font-size:.8125rem;color:var(--text-muted);line-height:1.6;margin-bottom:12px;}
.innov-impact{display:flex;align-items:center;gap:6px;font-size:.8125rem;color:var(--primary);font-weight:600;margin-bottom:14px;}
.innov-footer{display:flex;align-items:center;justify-content:space-between;padding-top:14px;border-top:1px solid var(--border);gap:8px;}
.innov-farmer{display:flex;align-items:center;gap:8px;min-width:0;}
.innov-farmer > div:last-child{min-width:0;}
.innov-farmer > div:last-child div{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.vote-btn{display:flex;align-items:center;gap:6px;background:var(--bg-2);border:1.5px solid var(--border);border-radius:var(--radius-full);padding:6px 14px;font-size:.8125rem;font-weight:700;color:var(--text);cursor:pointer;transition:all .15s;flex-shrink:0;}
.vote-btn:hover,.vote-btn.voted{background:var(--primary);border-color:var(--primary);color:#fff;}
.comp-card{background:var(--green-50);border:1px solid var(--green-200);border-radius:var(--radius-xl);padding:36px;color:var(--text);position:relative;overflow:hidden;margin-bottom:36px;}
.comp-card::before{content:'🏆';position:absolute;right:32px;top:50%;transform:translateY(-50%);font-size:6rem;opacity:.12;}
.prize-box{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);padding:14px 20px;text-align:center;}
.submit-form-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:36px;margin-top:40px;}
.innov-filter-tabs{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:24px;}
.innov-tab{padding:8px 16px;border-radius:var(--radius-full);font-size:.8125rem;font-weight:600;border:1.5px solid var(--border);color:var(--text-muted);background:var(--bg-card);cursor:pointer;transition:all .15s;text-decoration:none;}
.innov-tab:hover,.innov-tab.active{background:var(--primary);border-color:var(--primary);color:#fff;}
.char-counter{font-size:.75rem;color:var(--text-muted);text-align:right;margin-top:4px;}
.innov-form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
@media(max-width:1100px){.innov-grid{grid-template-columns:repeat(2,1fr);}}
@media(max-width:900px){.comp-card{padding:24px 20px;}.submit-form-card{padding:24px 16px;}}
@media(max-width:768px){.innov-grid{gap:14px;}}
@media(max-width:639px){.innov-grid{grid-template-columns:1fr;gap:14px;}.innov-thumb{height:170px;}.innov-body{padding:16px;}.innov-title{font-size:.9375rem;}.innov-filter-tabs{overflow-x:auto;flex-wrap:nowrap;-webkit-overflow-scrolling:touch;scrollbar-width:none;}.innov-filter-tabs::-webkit-scrollbar{display:none;}.innov-tab{white-space:nowrap;flex-shrink:0;}.submit-form-card{padding:20px 16px;}.comp-card{padding:20px 16px;}.comp-card::before{display:none;}.innov-form-grid{grid-template-columns:1fr;gap:14px;}.innov-form-grid .form-input,.innov-form-grid .form-select,.innov-form-grid select{min-height:48px;font-size:16px;}.innov-form-actions{flex-direction:column;align-items:stretch;}.innov-form-actions .btn-lg{width:100%;justify-content:center;}.innov-form-actions span{text-align:center;justify-content:center;}}
</style>
@endsection

@section('content')

{{-- Hero --}}
<section class="page-hero innov-hero page-hero-image">
  <div class="container">
    <div class="page-hero-content">
      <span class="page-hero-badge">
        <i class="fas fa-lightbulb"></i> Innovation Hub
      </span>
      <h1 class="page-hero-title">Farmer <span class="accent">Innovations</span> That Change Malawi</h1>
      <p class="page-hero-desc">Real farmers solving real problems. Vote for the best innovations, submit your own ideas, and win cash prizes.</p>
      <div class="page-hero-stats">
        @foreach(['fas fa-lightbulb'=>['Innovations','submitted this season'],'fas fa-trophy'=>['K 50,000','in prize money this year'],'fas fa-thumbs-up'=>['8,400+','community votes cast']] as $icon=>[$val,$label])
          <div class="page-hero-stat" style="flex-direction:column;text-align:center;padding:16px 24px;">
            <i class="{{ $icon }}" style="font-size:1.3rem;margin-bottom:6px;"></i>
            <div style="font-size:1.3rem;font-weight:800;letter-spacing:-0.025em;line-height:1;">{{ $val }}</div>
            <div style="font-size:.72rem;color:var(--text-muted);margin-top:4px;font-weight:500;">{{ $label }}</div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</section>

<div class="section" style="background:var(--bg-2);">
  <div class="container">

    {{-- Active Competition --}}
    @if(isset($activeCompetition)&&$activeCompetition)
    <div class="comp-card">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:20px;">
        <div style="flex:1;">
          <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(250,204,21,.2);border:1px solid rgba(250,204,21,.4);border-radius:var(--radius-full);padding:4px 12px;font-size:.75rem;font-weight:700;color:#fbbf24;margin-bottom:12px;">
            <i class="fas fa-trophy"></i> ACTIVE COMPETITION
          </span>
          <h2 style="font-size:1.5rem;font-weight:700;letter-spacing:-0.02em;margin-bottom:8px;">{{ $activeCompetition->title }}</h2>
          <p style="color:var(--text-muted);max-width:500px;line-height:1.6;font-size:.88rem;margin-bottom:20px;">{{ Str::limit($activeCompetition->description,200) }}</p>
          <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:20px;">
            <div style="display:flex;align-items:center;gap:6px;font-size:.8125rem;color:var(--text-muted);">
              <i class="fas fa-calendar"></i> Deadline: <strong>{{ $activeCompetition->ends_at->format('M j, Y') }}</strong>
            </div>
            <div style="display:flex;align-items:center;gap:6px;font-size:.8125rem;color:var(--text-muted);">
              <i class="fas fa-clock"></i> <strong style="color:var(--green-600);">{{ $activeCompetition->daysRemaining() }} days left</strong>
            </div>
            <div style="display:flex;align-items:center;gap:6px;font-size:.8125rem;color:var(--text-muted);">
              <i class="fas fa-users"></i> {{ $activeCompetition->entry_count }} entries
            </div>
          </div>
          @if($activeCompetition->isOpen())
            @auth
              <a href="#submit" onclick="document.getElementById('inCompetition').checked=true;document.getElementById('compId').value='{{ $activeCompetition->id }}';" class="btn btn-primary btn-md">
                <i class="fas fa-upload"></i> Enter Competition
              </a>
            @else
              <a href="{{ route('register') }}" class="btn btn-primary btn-md"><i class="fas fa-upload"></i> Join & Enter Competition</a>
            @endauth
          @else
            <button class="btn btn-outline btn-md" disabled style="opacity:.6;">Competition Closed</button>
          @endif
        </div>
        <div style="display:flex;gap:12px;flex-wrap:wrap;flex-shrink:0;">
          @foreach([['1st','var(--earth-500)','K '.number_format($activeCompetition->first_prize)],['2nd','var(--gray-400)','K '.number_format($activeCompetition->second_prize)],['3rd','#c2410c','K '.number_format($activeCompetition->third_prize)]] as [$place,$color,$prize])
            <div class="prize-box">
              <div style="font-size:.75rem;font-weight:700;color:{{ $color }};text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">{{ $place }} Place</div>
              <div style="font-size:1.25rem;font-weight:700;letter-spacing:-0.01em;">{{ $prize }}</div>
            </div>
          @endforeach
        </div>
        <div style="flex-basis:100%;display:flex;flex-direction:column;gap:10px;margin-top:4px;">
          <div style="display:flex;align-items:center;gap:8px;font-size:.8rem;color:var(--text);background:rgba(255,255,255,.5);border:1px solid var(--green-200);border-radius:var(--radius-md);padding:10px 14px;">
            <i class="fas fa-gift" style="color:var(--green-600);"></i>
            <span><strong>Prize pool: K {{ number_format($activeCompetition->first_prize + $activeCompetition->second_prize + $activeCompetition->third_prize) }}</strong> split across the top 3 innovations. Community votes decide who wins.</span>
          </div>
          <div style="display:flex;gap:8px;flex-wrap:wrap;">
            @foreach(['Submit your innovation to enter','Farmers vote for their favourites','Top 3 voted win cash prizes'] as $step)
              <span style="display:inline-flex;align-items:center;gap:6px;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-full);padding:5px 12px;font-size:.74rem;font-weight:600;color:var(--text-muted);">
                <i class="fas fa-check-circle" style="color:var(--green-600);"></i> {{ $step }}
              </span>
            @endforeach
          </div>
        </div>
      </div>
    </div>
    @endif

    {{-- Community Leaderboard (voting showcase) --}}
    @if(isset($leaders) && $leaders->count() > 0)
    <div style="margin-bottom:32px;">
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;">
        <div style="width:38px;height:38px;border-radius:12px;background:#fefce8;display:flex;align-items:center;justify-content:center;font-size:1.05rem;">🏆</div>
        <div>
          <h3 style="font-size:1.1rem;font-weight:700;letter-spacing:-0.01em;color:var(--text);margin-bottom:2px;">Community Leaderboard</h3>
          <p style="font-size:.8rem;color:var(--text-muted);">The people leading right now — cast your vote to help crown the next champion.</p>
        </div>
      </div>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;align-items:start;">
        @foreach($leaders as $ld)
          @php
            $pos=$loop->iteration;
            $gold=$pos===1;
            $rankBorders=['#f59e0b','#9ca3af','#d97706'];
            $rankBgs=['#fff7ed','#f9fafb','#fffbeb'];
            $lImgs=['water_management'=>'irrigation.jpg','technology'=>'agritech-drone.jpg','infrastructure'=>'greenhouse.jpg','crop_solutions'=>'maize-field.jpg','energy'=>'sunflower.jpg','post_harvest'=>'harvest.jpg','livestock'=>'cows.jpg','business'=>'market-stall.jpg'];
            $lCover=asset('assets/img/agri/'.($lImgs[$ld->category]??'seedling.jpg'));
            $lVoted=auth()->check()&&Auth::user()->hasVotedFor($ld);
          @endphp
          <div style="background:var(--bg-card);border:2px solid {{ $gold?$rankBorders[0] : 'var(--border)' }};border-radius:var(--radius-xl);overflow:hidden;{{ $gold?'box-shadow:0 18px 40px -18px rgba(245,158,11,.45);':'' }} transform:{{ $gold?'translateY(-8px)':'translateY(0)' }};">
            <div style="position:relative;height:140px;background:{{ $gold?$rankBgs[0]:'var(--bg-2)' }};display:flex;align-items:center;justify-content:center;">
              <img src="{{ $lCover }}" alt="{{ $ld->title }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;"/>
              @if($ld->images&&count($ld->images)>0)
                <img src="{{ asset('storage/'.$ld->images[0]) }}" alt="{{ $ld->title }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;"/>
              @endif
              <span style="position:absolute;top:10px;left:10px;background:{{ $rankBorders[$pos-1] }};color:#fff;font-size:.78rem;font-weight:800;padding:4px 10px;border-radius:20px;">#{{ $pos }}</span>
              @if($ld->in_competition)
                <span style="position:absolute;top:10px;right:10px;background:#d97706;color:#fff;font-size:.68rem;font-weight:700;padding:3px 8px;border-radius:20px;">🏆 Competition</span>
              @endif
            </div>
            <div style="padding:16px;">
              <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--primary);margin-bottom:6px;">{{ ucwords(str_replace('_',' ',$ld->category)) }}</div>
              <a href="{{ route('innovation.show',$ld) }}" style="font-size:.95rem;font-weight:700;color:var(--text);display:block;margin-bottom:6px;line-height:1.35;text-decoration:none;">{{ $ld->title }}</a>
              @if($ld->impact_summary)
                <div style="display:flex;align-items:center;gap:6px;font-size:.76rem;color:var(--primary);font-weight:600;margin-bottom:12px;"><i class="fas fa-chart-line"></i> {{ $ld->impact_summary }}</div>
              @endif
              <div style="display:flex;align-items:center;justify-content:space-between;padding-top:12px;border-top:1px solid var(--border);gap:8px;">
                <div style="display:flex;align-items:center;gap:8px;min-width:0;">
                  <div class="avatar avatar-sm" style="background:#16a34a;color:#fff;font-size:.62rem;flex-shrink:0;">{{ $ld->user->initials??'FA' }}</div>
                  <div style="min-width:0;">
                    <div style="font-size:.78rem;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $ld->user->full_name??'Farmer' }}</div>
                    <div style="font-size:.72rem;color:var(--text-muted);">{{ $ld->district??'Malawi' }}</div>
                  </div>
                </div>
                @auth
                  <form method="POST" action="{{ route('innovation.vote',$ld) }}" style="display:inline;flex-shrink:0;" class="innov-vote-form" data-ajax="true">
                    @csrf
                    @if($lVoted)
                      <button type="button" class="vote-btn voted" title="You already voted this round — one vote per innovation."><i class="fas fa-check"></i> Voted · <span class="vote-count">{{ number_format($ld->vote_count) }}</span></button>
                    @else
                      <button type="submit" class="vote-btn" title="Vote — one vote per innovation per round"><i class="fas fa-thumbs-up"></i> <span class="vote-count">{{ number_format($ld->vote_count) }}</span></button>
                    @endif
                  </form>
                @else
                  <a href="{{ route('login') }}" class="vote-btn" style="flex-shrink:0;text-decoration:none;" title="Sign in to vote"><i class="fas fa-thumbs-up"></i> {{ number_format($ld->vote_count) }}</a>
                @endauth
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
    @endif

    {{-- Filter Tabs --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;">
      <div class="innov-filter-tabs">
        <a href="{{ route('innovation') }}" class="innov-tab {{ !request('category') ? 'active' : '' }}">All</a>
        @foreach(['water_management'=>'💧 Water','technology'=>'📱 Technology','infrastructure'=>'🏗️ Infrastructure','crop_solutions'=>'🌽 Crops','energy'=>'⚡ Energy','post_harvest'=>'🌾 Post-Harvest','livestock'=>'🐄 Livestock','business'=>'📊 Business'] as $key=>$label)
          <a href="{{ route('innovation',['category'=>$key]) }}" class="innov-tab {{ request('category')===$key ? 'active' : '' }}">{{ $label }}</a>
        @endforeach
      </div>
      <select class="form-select" style="font-size:.8125rem;padding:8px 12px;" onchange="window.location=this.value">
        @foreach(['votes'=>'Most Voted','newest'=>'Newest','views'=>'Most Viewed'] as $v=>$l)
          <option value="{{ route('innovation',array_merge(request()->query(),['sort'=>$v])) }}" @selected(request('sort','votes')===$v)>{{ $l }}</option>
        @endforeach
      </select>
    </div>

    {{-- Innovation Grid --}}
    <div class="innov-grid">
      @forelse(isset($innovations)?$innovations:[] as $innovation)
        @php
          $catBgs=['water_management'=>'#fef9c3,#fbbf24','technology'=>'#dcfce7,#4ade80','infrastructure'=>'#e0f2fe,#38bdf8','crop_solutions'=>'#dcfce7,#86efac','energy'=>'#f3e8ff,#a855f7','post_harvest'=>'#fff7ed,#fb923c','livestock'=>'#fef9c3,#f59e0b','business'=>'#eff6ff,#60a5fa'];
          $catImgs=['water_management'=>'irrigation.jpg','technology'=>'agritech-drone.jpg','infrastructure'=>'greenhouse.jpg','crop_solutions'=>'maize-field.jpg','energy'=>'sunflower.jpg','post_harvest'=>'harvest.jpg','livestock'=>'cows.jpg','business'=>'market-stall.jpg'];
          $bg=$catBgs[$innovation->category]??'#dcfce7,#86efac';
          $cover=asset('assets/img/agri/'.($catImgs[$innovation->category]??'seedling.jpg'));
          $userVoted=auth()->check()&&Auth::user()->hasVotedFor($innovation);
        @endphp
        <div class="innov-card">
          <div class="innov-thumb" style="background:{{ explode(',', $bg)[0] }};">
            <img src="{{ $cover }}" alt="{{ $innovation->title }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;"/>
            @if($innovation->images&&count($innovation->images)>0)
              <img src="{{ asset('storage/'.$innovation->images[0]) }}" alt="{{ $innovation->title }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;"/>
            @endif
            <span style="position:absolute;top:10px;right:10px;background:rgba(0,0,0,.55);color:#fff;font-size:.75rem;font-weight:700;padding:3px 8px;border-radius:20px;">
              <i class="fas fa-eye"></i> {{ number_format($innovation->view_count) }}
            </span>
            @if($innovation->in_competition)
              <span style="position:absolute;top:10px;left:10px;background:#d97706;color:#fff;font-size:.75rem;font-weight:700;padding:3px 8px;border-radius:20px;">
                🏆 Competition
              </span>
            @endif
          </div>
          <div class="innov-body">
            <div class="innov-cat-badge">{{ ucwords(str_replace('_',' ',$innovation->category)) }}</div>
            <div class="innov-title">{{ $innovation->title }}</div>
            <p class="innov-desc">{{ Str::limit($innovation->description,120) }}</p>
            @if($innovation->impact_summary)
              <div class="innov-impact"><i class="fas fa-chart-line"></i> {{ $innovation->impact_summary }}</div>
            @endif
            @if($innovation->estimated_cost)
              <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:10px;">
                <i class="fas fa-coins" style="color:var(--earth-500);"></i> Estimated cost: {{ $innovation->estimated_cost }}
              </div>
            @endif
            <div class="innov-footer">
              <div class="innov-farmer">
                <div class="avatar avatar-sm" style="background:#16a34a;color:#fff;font-size:.65rem;">{{ $innovation->user->initials??'FA' }}</div>
                <div>
                  <div style="font-size:.8125rem;font-weight:600;color:var(--text);">{{ $innovation->user->full_name??'Farmer' }}</div>
                  <div style="font-size:.75rem;color:var(--text-muted);">{{ $innovation->district??'Malawi' }}</div>
                </div>
              </div>
              @auth
                <form method="POST" action="{{ route('innovation.vote',$innovation) }}" style="display:inline;" class="innov-vote-form" data-ajax="true">
                  @csrf
                  @if($userVoted)
                    <button type="button" class="vote-btn voted" title="You already voted this round — one vote per innovation."><i class="fas fa-check"></i> Voted · <span class="vote-count">{{ number_format($innovation->vote_count) }}</span></button>
                  @else
                    <button type="submit" class="vote-btn" title="Vote — one vote per innovation per round">
                      <i class="fas fa-thumbs-up"></i> <span class="vote-count">{{ number_format($innovation->vote_count) }}</span>
                    </button>
                  @endif
                </form>
              @else
                <a href="{{ route('login') }}" class="vote-btn" title="Sign in to vote">
                  <i class="fas fa-thumbs-up"></i> {{ number_format($innovation->vote_count) }}
                </a>
              @endauth
            </div>
          </div>
        </div>
      @empty
        <div style="grid-column:1/-1;text-align:center;padding:60px;color:var(--text-muted);">
          <i class="fas fa-lightbulb" style="font-size:3rem;margin-bottom:16px;display:block;opacity:.3;"></i>
          <h3 style="margin-bottom:8px;">No innovations yet</h3>
          <p>Be the first to submit your farming innovation!</p>
        </div>
      @endforelse
    </div>

    @if(isset($innovations)&&$innovations->hasPages())
      <div style="margin-top:32px;">{{ $innovations->withQueryString()->links() }}</div>
    @endif

    {{-- Previous Winners (until the next round opens) --}}
    @if(isset($pastWinners) && $pastWinners->count() > 0)
    <div style="margin-top:44px;">
      <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:18px;">
        <div style="display:flex;align-items:center;gap:12px;">
          <div style="width:38px;height:38px;border-radius:12px;background:#fefce8;display:flex;align-items:center;justify-content:center;font-size:1.05rem;">🥇</div>
          <div>
            <h3 style="font-size:1.1rem;font-weight:700;letter-spacing:-0.01em;color:var(--text);margin-bottom:2px;">Previous Winners</h3>
            <p style="font-size:.8rem;color:var(--text-muted);">Champions from the last competition round — new entries open soon.</p>
          </div>
        </div>
        @php $champComp = $pastWinners->firstWhere('winner_position', 1)?->competition_id; @endphp
        <a href="{{ route('innovation.results', $champComp) }}" class="btn btn-outline btn-md" style="font-size:.8rem;">
          <i class="fas fa-download"></i> Download Results List
        </a>
      </div>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
        @foreach($pastWinners as $w)
          @php
            $wImgs=['water_management'=>'irrigation.jpg','technology'=>'agritech-drone.jpg','infrastructure'=>'greenhouse.jpg','crop_solutions'=>'maize-field.jpg','energy'=>'sunflower.jpg','post_harvest'=>'harvest.jpg','livestock'=>'cows.jpg','business'=>'market-stall.jpg'];
            $wCover=asset('assets/img/agri/'.($wImgs[$w->category]??'seedling.jpg'));
            $medals=['#f59e0b','#9ca3af','#d97706'];
            $medalLabel=['1st Place','2nd Place','3rd Place'];
            $posBg=['#fef3c7','#f3f4f6','#ffedd5'];
          @endphp
          <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);overflow:hidden;">
            <div style="position:relative;height:150px;background:{{ $posBg[$w->winner_position-1] }};">
              <img src="{{ $wCover }}" alt="{{ $w->title }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;"/>
              @if($w->images&&count($w->images)>0)
                <img src="{{ asset('storage/'.$w->images[0]) }}" alt="{{ $w->title }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;"/>
              @endif
              <span style="position:absolute;top:10px;left:10px;background:{{ $medals[$w->winner_position-1] }};color:#fff;font-size:.72rem;font-weight:800;padding:4px 10px;border-radius:20px;">🏆 {{ $medalLabel[$w->winner_position-1] }}</span>
              @if($w->winner_prize)
                <span style="position:absolute;bottom:10px;right:10px;background:rgba(0,0,0,.6);color:#fde68a;font-size:.72rem;font-weight:700;padding:4px 10px;border-radius:20px;">K {{ number_format($w->winner_prize) }}</span>
              @endif
            </div>
            <div style="padding:16px;">
              <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--primary);margin-bottom:6px;">{{ ucwords(str_replace('_',' ',$w->category)) }}</div>
              <div style="font-size:.95rem;font-weight:700;color:var(--text);line-height:1.35;margin-bottom:10px;">{{ $w->title }}</div>
              <div style="display:flex;align-items:center;justify-content:space-between;padding-top:12px;border-top:1px solid var(--border);">
                <div style="display:flex;align-items:center;gap:8px;min-width:0;">
                  <div class="avatar avatar-sm" style="background:#b45309;color:#fff;font-size:.62rem;flex-shrink:0;">{{ $w->user->initials??'FA' }}</div>
                  <div style="min-width:0;">
                    <div style="font-size:.78rem;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $w->user->full_name??'Farmer' }}</div>
                    <div style="font-size:.72rem;color:var(--text-muted);">{{ $w->district??'Malawi' }}</div>
                  </div>
                </div>
                <div style="text-align:right;flex-shrink:0;">
                  <div style="font-size:.8rem;font-weight:700;color:var(--primary);"><i class="fas fa-thumbs-up"></i> {{ number_format($w->vote_count) }}</div>
                  <div style="font-size:.68rem;color:var(--text-muted);">{{ number_format($w->view_count) }} views</div>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
    @endif

    {{-- Submit Innovation Form --}}
    <div class="submit-form-card" id="submit">
      <div style="display:flex;align-items:center;gap:14px;margin-bottom:24px;">
        <div style="width:52px;height:52px;border-radius:14px;background:var(--green-100);display:flex;align-items:center;justify-content:center;font-size:1.5rem;">💡</div>
        <div>
          <h2 style="font-size:1.25rem;font-weight:700;color:var(--text);letter-spacing:-0.01em;margin-bottom:4px;">Submit Your Innovation</h2>
          <p style="font-size:.8125rem;color:var(--text-muted);">Share how you're solving farming challenges. Approved innovations are visible to 12,000+ farmers.</p>
        </div>
      </div>
      @auth
        @if(session('success'))
          <div style="background:var(--green-50);border:1.5px solid var(--green-200);border-radius:var(--radius-md);padding:14px 18px;margin-bottom:20px;color:var(--green-700);font-weight:600;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
          </div>
        @endif
        <form method="POST" action="{{ route('innovation.store') }}" enctype="multipart/form-data">
          @csrf
          <div class="innov-form-grid">
            <div class="form-group" style="grid-column:1/-1;">
              <label class="form-label">Innovation Title *</label>
              <input type="text" name="title" class="form-input" placeholder="e.g. Solar-Powered Drip Irrigation Controller" value="{{ old('title') }}" required maxlength="150"/>
              @error('title')<span style="color:#ef4444;font-size:.75rem;">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
              <label class="form-label">Category *</label>
              <select name="category" class="form-input form-select" required>
                <option value="">Select category</option>
                @foreach(['water_management'=>'💧 Water Management','technology'=>'📱 Technology','infrastructure'=>'🏗️ Infrastructure','crop_solutions'=>'🌽 Crop Solutions','energy'=>'⚡ Energy','post_harvest'=>'🌾 Post Harvest','livestock'=>'🐄 Livestock','business'=>'📊 Agribusiness'] as $v=>$l)
                  <option value="{{ $v }}" @selected(old('category')===$v)>{{ $l }}</option>
                @endforeach
              </select>
              @error('category')<span style="color:#ef4444;font-size:.75rem;">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
              <label class="form-label">District</label>
              <select name="district" class="form-input form-select">
                <option value="">Select district</option>
                @foreach($districts as $district)
                  <option value="{{ $district->name }}" @selected(old('district',$district->name===Auth::user()->district?$district->name:null)===$district->name)>{{ $district->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group" style="grid-column:1/-1;">
              <label class="form-label">Description *</label>
              <textarea name="description" id="innovDesc" class="form-input" rows="4" placeholder="Describe your innovation in detail — what problem it solves, how it works, and results you've seen..." required maxlength="3000" style="resize:vertical;">{{ old('description') }}</textarea>
              <div class="char-counter"><span id="descCount">0</span>/3000 characters</div>
              @error('description')<span style="color:#ef4444;font-size:.75rem;">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
              <label class="form-label">Impact Summary</label>
              <input type="text" name="impact_summary" class="form-input" placeholder="e.g. 45% less water used" value="{{ old('impact_summary') }}" maxlength="200"/>
              <div style="font-size:.75rem;color:var(--text-muted);margin-top:3px;">Short statement of the main benefit</div>
            </div>
            <div class="form-group">
              <label class="form-label">Estimated Cost</label>
              <input type="text" name="estimated_cost" class="form-input" placeholder="e.g. K 500 for 1 hectare" value="{{ old('estimated_cost') }}" maxlength="100"/>
            </div>
            <div class="form-group" style="grid-column:1/-1;">
              <label class="form-label">Implementation Steps</label>
              <textarea name="implementation_steps" class="form-input" rows="3" placeholder="Step-by-step guide for other farmers to replicate this innovation..." style="resize:vertical;">{{ old('implementation_steps') }}</textarea>
            </div>
            <div class="form-group" style="grid-column:1/-1;">
              <label class="form-label">Photos (max 5)</label>
              <input type="file" name="images[]" class="form-input" multiple accept="image/jpeg,image/png,image/webp" style="padding:8px;"/>
              <div style="font-size:.75rem;color:var(--text-muted);margin-top:3px;">Upload photos of your innovation in action. Max 4MB each.</div>
            </div>
            @if(isset($activeCompetition)&&$activeCompetition&&$activeCompetition->isOpen())
              <div class="form-group" style="grid-column:1/-1;background:#fefce8;border:1.5px solid #fbbf24;border-radius:var(--radius-md);padding:16px;">
                <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;">
                  <input type="checkbox" id="inCompetition" name="in_competition" value="1" @checked(old('in_competition')) style="width:auto;accent-color:var(--primary);margin-top:2px;"/>
                  <input type="hidden" id="compId" name="competition_id" value="{{ $activeCompetition->id }}"/>
                  <div>
                    <div style="font-weight:700;color:var(--text);font-size:.8125rem;margin-bottom:3px;">🏆 Enter this innovation into "{{ $activeCompetition->title }}"</div>
                    <div style="font-size:.8125rem;color:var(--text-muted);">Win up to K {{ number_format($activeCompetition->first_prize) }} in prizes. {{ $activeCompetition->daysRemaining() }} days left to enter.</div>
                  </div>
                </label>
              </div>
            @endif
          </div>
          <div class="innov-form-actions" style="margin-top:22px;display:flex;gap:12px;flex-wrap:wrap;">
            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-paper-plane"></i> Submit Innovation</button>
            <span style="font-size:.8125rem;color:var(--text-muted);display:flex;align-items:center;">Reviewed and published within 24 hours</span>
          </div>
        </form>
      @else
        <div style="text-align:center;padding:32px;background:var(--bg-2);border-radius:var(--radius-lg);">
          <div style="font-size:3rem;margin-bottom:14px;">🌱</div>
          <h3 style="font-size:1.25rem;font-weight:600;letter-spacing:-0.01em;margin-bottom:8px;">Join to Submit Your Innovation</h3>
          <p style="font-size:.8125rem;color:var(--text-muted);margin-bottom:20px;">Create an account to share your farming innovations with 12,000+ farmers across Africa.</p>
          <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg"><i class="fas fa-seedling"></i> Create Account</a>
            <a href="{{ route('login') }}" class="btn btn-outline btn-lg">Sign In</a>
          </div>
        </div>
      @endauth
    </div>

  </div>
</div>

@include('partials.footer')
@endsection

@section('extra_js')
<script>
document.addEventListener('ajax:success', function (e) {
  const form = e.target;
  if (!form.classList.contains('innov-vote-form')) return;
  const data = e.detail;
  const count = form.querySelector('.vote-count');
  if (count && typeof data.vote_count !== 'undefined') count.textContent = Number(data.vote_count).toLocaleString();
  const btn = form.querySelector('[type=submit]');
  if (btn && data.voted) {
    btn.classList.remove('voting');
    btn.classList.add('voted');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-check"></i> Voted · <span class="vote-count">' + Number(data.vote_count).toLocaleString() + '</span>';
    btn.title = 'You already voted this round — one vote per innovation.';
  }
  if (data && data.toast === false) showToast(data.message || 'Vote recorded.', data.voted ? 'success' : 'info');
});

/* Character counter for description */
const desc=document.getElementById('innovDesc');
const counter=document.getElementById('descCount');
if(desc&&counter){
  counter.textContent=desc.value.length;
  desc.addEventListener('input',()=>{counter.textContent=desc.value.length;});
}
</script>
@endsection
