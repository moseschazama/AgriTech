@extends('layouts.app')
@section('title', 'Innovation Hub — AgriTech Pro')
@section('extra_css')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"/>
<link rel="stylesheet" href="{{ asset('css/home.css') }}"/>
<style>
.innov-hero{background:linear-gradient(135deg,#0a0f1e,#1a1f3a,#0d2b47);padding:60px 0 48px;color:#fff;position:relative;overflow:hidden;}
.innov-hero::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 30% 50%,rgba(99,102,241,.15),transparent 50%),radial-gradient(ellipse at 70% 30%,rgba(16,185,129,.1),transparent 50%);pointer-events:none;}
.innov-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px;}
.innov-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;transition:all .2s;}
.innov-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-lg);}
.innov-thumb{height:200px;position:relative;display:flex;align-items:center;justify-content:center;font-size:4rem;overflow:hidden;}
.innov-body{padding:20px;}
.innov-cat-badge{font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--primary);margin-bottom:8px;}
.innov-title{font-family:var(--font-display);font-size:1rem;font-weight:700;color:var(--text);margin-bottom:8px;line-height:1.35;word-break:break-word;}
.innov-desc{font-size:.82rem;color:var(--text-muted);line-height:1.6;margin-bottom:12px;}
.innov-impact{display:flex;align-items:center;gap:6px;font-size:.78rem;color:var(--primary);font-weight:600;margin-bottom:14px;}
.innov-footer{display:flex;align-items:center;justify-content:space-between;padding-top:14px;border-top:1px solid var(--border);gap:8px;}
.innov-farmer{display:flex;align-items:center;gap:8px;min-width:0;}
.innov-farmer > div:last-child{min-width:0;}
.innov-farmer > div:last-child div{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.vote-btn{display:flex;align-items:center;gap:6px;background:var(--bg-2);border:1.5px solid var(--border);border-radius:var(--radius-full);padding:6px 14px;font-size:.82rem;font-weight:700;color:var(--text);cursor:pointer;transition:all .15s;font-family:var(--font-body);flex-shrink:0;}
.vote-btn:hover,.vote-btn.voted{background:var(--primary);border-color:var(--primary);color:#fff;}
.comp-card{background:linear-gradient(135deg,#052e0f,#0d4a1e,#166534);border-radius:var(--radius-xl);padding:36px;color:#fff;position:relative;overflow:hidden;margin-bottom:36px;}
.comp-card::before{content:'🏆';position:absolute;right:32px;top:50%;transform:translateY(-50%);font-size:6rem;opacity:.12;}
.prize-box{background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);border-radius:var(--radius-md);padding:14px 20px;text-align:center;}
.submit-form-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:36px;margin-top:40px;}
.innov-filter-tabs{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:24px;}
.innov-tab{padding:8px 16px;border-radius:var(--radius-full);font-size:.82rem;font-weight:600;border:1.5px solid var(--border);color:var(--text-muted);background:var(--bg-card);cursor:pointer;transition:all .15s;text-decoration:none;}
.innov-tab:hover,.innov-tab.active{background:var(--primary);border-color:var(--primary);color:#fff;}
.char-counter{font-size:.74rem;color:var(--text-muted);text-align:right;margin-top:4px;}
.innov-form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
@media(max-width:1100px){.innov-grid{grid-template-columns:repeat(2,1fr);}}
@media(max-width:900px){.innov-hero{padding:40px 0 32px;}.comp-card{padding:24px 20px;}.submit-form-card{padding:24px 16px;}}
@media(max-width:768px){.innov-grid{gap:14px;}}
@media(max-width:639px){.innov-grid{grid-template-columns:1fr;gap:14px;}.innov-thumb{height:170px;}.innov-body{padding:16px;}.innov-title{font-size:.92rem;}.innov-filter-tabs{overflow-x:auto;flex-wrap:nowrap;-webkit-overflow-scrolling:touch;scrollbar-width:none;}.innov-filter-tabs::-webkit-scrollbar{display:none;}.innov-tab{white-space:nowrap;flex-shrink:0;}.submit-form-card{padding:20px 16px;}.comp-card{padding:20px 16px;}.comp-card::before{display:none;}.innov-form-grid{grid-template-columns:1fr;gap:14px;}.innov-form-grid .form-input,.innov-form-grid .form-select,.innov-form-grid select{min-height:48px;font-size:16px;}.innov-form-actions{flex-direction:column;align-items:stretch;}.innov-form-actions .btn-lg{width:100%;justify-content:center;}.innov-form-actions span{text-align:center;justify-content:center;}}
@media(max-width:600px){.innov-hero{padding:32px 0 24px;}}
</style>
@endsection

@section('content')

{{-- Hero --}}
<section class="innov-hero">
  <div class="container" style="position:relative;z-index:1;">
    <div style="text-align:center;margin-bottom:32px;">
      <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(99,102,241,.15);border:1px solid rgba(99,102,241,.3);border-radius:var(--radius-full);padding:5px 14px;font-size:.78rem;font-weight:700;color:#818cf8;margin-bottom:14px;">
        <i class="fas fa-lightbulb"></i> Innovation Hub
      </span>
      <h1 style="font-family:var(--font-display);font-size:clamp(2rem,5vw,3rem);font-weight:800;margin-bottom:12px;">
        Farmer <span style="color:#34d399;">Innovations</span> That Change Malawi
      </h1>
      <p style="opacity:.8;max-width:560px;margin:0 auto 28px;line-height:1.7;font-size:1rem;">
        Real farmers solving real problems. Vote for the best innovations, submit your own ideas, and win cash prizes.
      </p>
    </div>
    <div style="display:flex;gap:28px;justify-content:center;flex-wrap:wrap;">
      @foreach(['fas fa-lightbulb'=>['Innovations','submitted this season'],'fas fa-trophy'=>['K 50,000','in prize money this year'],'fas fa-thumbs-up'=>['8,400+','community votes cast']] as $icon=>[$val,$label])
        <div style="text-align:center;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);border-radius:var(--radius-lg);padding:18px 28px;">
          <i class="{{ $icon }}" style="color:#34d399;font-size:1.4rem;margin-bottom:8px;display:block;"></i>
          <div style="font-family:var(--font-display);font-size:1.5rem;font-weight:800;">{{ $val }}</div>
          <div style="font-size:.75rem;opacity:.7;margin-top:2px;">{{ $label }}</div>
        </div>
      @endforeach
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
          <h2 style="font-family:var(--font-display);font-size:1.5rem;font-weight:800;margin-bottom:8px;">{{ $activeCompetition->title }}</h2>
          <p style="opacity:.8;max-width:500px;line-height:1.6;font-size:.88rem;margin-bottom:20px;">{{ Str::limit($activeCompetition->description,200) }}</p>
          <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:20px;">
            <div style="display:flex;align-items:center;gap:6px;font-size:.82rem;opacity:.8;">
              <i class="fas fa-calendar"></i> Deadline: <strong>{{ $activeCompetition->ends_at->format('M j, Y') }}</strong>
            </div>
            <div style="display:flex;align-items:center;gap:6px;font-size:.82rem;opacity:.8;">
              <i class="fas fa-clock"></i> <strong style="color:#4ade80;">{{ $activeCompetition->daysRemaining() }} days left</strong>
            </div>
            <div style="display:flex;align-items:center;gap:6px;font-size:.82rem;opacity:.8;">
              <i class="fas fa-users"></i> {{ $activeCompetition->entry_count }} entries
            </div>
          </div>
          @if($activeCompetition->isOpen())
            @auth
              <a href="#submit" onclick="document.getElementById('inCompetition').checked=true;document.getElementById('compId').value='{{ $activeCompetition->id }}';" class="btn btn-white btn-md">
                <i class="fas fa-upload"></i> Enter Competition
              </a>
            @else
              <a href="{{ route('register') }}" class="btn btn-white btn-md"><i class="fas fa-upload"></i> Join & Enter Competition</a>
            @endauth
          @else
            <button class="btn btn-outline btn-md" disabled style="opacity:.6;color:#fff;border-color:rgba(255,255,255,.4);">Competition Closed</button>
          @endif
        </div>
        <div style="display:flex;gap:12px;flex-wrap:wrap;flex-shrink:0;">
          @foreach([['1st','var(--earth-500)','K '.number_format($activeCompetition->first_prize)],['2nd','var(--gray-400)','K '.number_format($activeCompetition->second_prize)],['3rd','#c2410c','K '.number_format($activeCompetition->third_prize)]] as [$place,$color,$prize])
            <div class="prize-box">
              <div style="font-size:.72rem;font-weight:700;color:{{ $color }};text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">{{ $place }} Place</div>
              <div style="font-family:var(--font-display);font-size:1.2rem;font-weight:800;">{{ $prize }}</div>
            </div>
          @endforeach
        </div>
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
      <select class="form-select" style="font-size:.82rem;padding:8px 12px;" onchange="window.location=this.value">
        @foreach(['votes'=>'Most Voted','newest'=>'Newest','views'=>'Most Viewed'] as $v=>$l)
          <option value="{{ route('innovation',array_merge(request()->query(),['sort'=>$v])) }}" @selected(request('sort','votes')===$v)>{{ $l }}</option>
        @endforeach
      </select>
    </div>

    {{-- Innovation Grid --}}
    <div class="innov-grid">
      @forelse(isset($innovations)?$innovations:[] as $innovation)
        @php
          $catEmojis=['water_management'=>'☀️','technology'=>'📱','infrastructure'=>'♻️','crop_solutions'=>'🌽','energy'=>'⚡','post_harvest'=>'🌾','livestock'=>'🐄','business'=>'📊'];
          $catBgs=['water_management'=>'#fef9c3,#fbbf24','technology'=>'#dcfce7,#4ade80','infrastructure'=>'#e0f2fe,#38bdf8','crop_solutions'=>'#dcfce7,#86efac','energy'=>'#f3e8ff,#a855f7','post_harvest'=>'#fff7ed,#fb923c','livestock'=>'#fef9c3,#f59e0b','business'=>'#eff6ff,#60a5fa'];
          $emoji=$catEmojis[$innovation->category]??'💡';
          $bg=$catBgs[$innovation->category]??'#dcfce7,#86efac';
          $userVoted=auth()->check()&&Auth::user()->hasVotedFor($innovation);
        @endphp
        <div class="innov-card">
          <div class="innov-thumb" style="background:linear-gradient(135deg,{{ $bg }});">
            {{ $emoji }}
            @if($innovation->images&&count($innovation->images)>0)
              <img src="{{ asset('storage/'.$innovation->images[0]) }}" alt="{{ $innovation->title }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;"/>
            @endif
            <span style="position:absolute;top:10px;right:10px;background:rgba(0,0,0,.55);color:#fff;font-size:.7rem;font-weight:700;padding:3px 8px;border-radius:20px;">
              <i class="fas fa-eye"></i> {{ number_format($innovation->view_count) }}
            </span>
            @if($innovation->in_competition)
              <span style="position:absolute;top:10px;left:10px;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;font-size:.67rem;font-weight:700;padding:3px 8px;border-radius:20px;">
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
                <div class="avatar avatar-sm" style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;font-size:.65rem;">{{ $innovation->user->initials??'FA' }}</div>
                <div>
                  <div style="font-size:.8rem;font-weight:600;color:var(--text);">{{ $innovation->user->full_name??'Farmer' }}</div>
                  <div style="font-size:.72rem;color:var(--text-muted);">{{ $innovation->district??'Malawi' }}</div>
                </div>
              </div>
              @auth
                <form method="POST" action="{{ route('innovation.vote',$innovation) }}" style="display:inline;">
                  @csrf
                  <button type="submit" class="vote-btn {{ $userVoted?'voted':'' }}" title="{{ $userVoted?'Remove vote':'Vote for this innovation' }}">
                    <i class="fas fa-thumbs-up"></i> {{ number_format($innovation->vote_count) }}
                  </button>
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

    {{-- Submit Innovation Form --}}
    <div class="submit-form-card" id="submit">
      <div style="display:flex;align-items:center;gap:14px;margin-bottom:24px;">
        <div style="width:52px;height:52px;border-radius:14px;background:var(--green-100);display:flex;align-items:center;justify-content:center;font-size:1.5rem;">💡</div>
        <div>
          <h2 style="font-family:var(--font-display);font-size:1.3rem;font-weight:800;color:var(--text);margin-bottom:4px;">Submit Your Innovation</h2>
          <p style="font-size:.85rem;color:var(--text-muted);">Share how you're solving farming challenges. Approved innovations are visible to 12,000+ farmers.</p>
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
              <div style="font-size:.73rem;color:var(--text-muted);margin-top:3px;">Short statement of the main benefit</div>
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
              <div style="font-size:.73rem;color:var(--text-muted);margin-top:3px;">Upload photos of your innovation in action. Max 4MB each.</div>
            </div>
            @if(isset($activeCompetition)&&$activeCompetition&&$activeCompetition->isOpen())
              <div class="form-group" style="grid-column:1/-1;background:linear-gradient(135deg,#fef9c3,#fef3c7);border:1.5px solid #fbbf24;border-radius:var(--radius-md);padding:16px;">
                <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;">
                  <input type="checkbox" id="inCompetition" name="in_competition" value="1" @checked(old('in_competition')) style="width:auto;accent-color:var(--primary);margin-top:2px;"/>
                  <input type="hidden" id="compId" name="competition_id" value="{{ $activeCompetition->id }}"/>
                  <div>
                    <div style="font-weight:700;color:var(--text);font-size:.88rem;margin-bottom:3px;">🏆 Enter this innovation into "{{ $activeCompetition->title }}"</div>
                    <div style="font-size:.78rem;color:var(--text-muted);">Win up to K {{ number_format($activeCompetition->first_prize) }} in prizes. {{ $activeCompetition->daysRemaining() }} days left to enter.</div>
                  </div>
                </label>
              </div>
            @endif
          </div>
          <div class="innov-form-actions" style="margin-top:22px;display:flex;gap:12px;flex-wrap:wrap;">
            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-paper-plane"></i> Submit Innovation</button>
            <span style="font-size:.78rem;color:var(--text-muted);display:flex;align-items:center;">Reviewed and published within 24 hours</span>
          </div>
        </form>
      @else
        <div style="text-align:center;padding:32px;background:var(--bg-2);border-radius:var(--radius-lg);">
          <div style="font-size:3rem;margin-bottom:14px;">🌱</div>
          <h3 style="font-family:var(--font-display);font-size:1.2rem;margin-bottom:8px;">Join to Submit Your Innovation</h3>
          <p style="font-size:.85rem;color:var(--text-muted);margin-bottom:20px;">Create a free account to share your farming innovations with 12,000+ farmers across Africa.</p>
          <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg"><i class="fas fa-seedling"></i> Create Free Account</a>
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
// Character counter for description
const desc=document.getElementById('innovDesc');
const counter=document.getElementById('descCount');
if(desc&&counter){
  counter.textContent=desc.value.length;
  desc.addEventListener('input',()=>{counter.textContent=desc.value.length;});
}
</script>
@endsection
