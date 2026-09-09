@extends('layouts.app')
@section('title', 'Farm Alerts — Admin — AgriTech Pro')
@section('extra_css')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"/>
<link rel="stylesheet" href="{{ asset('css/farm-records.css') }}"/>
<style>
.faaf-layout{display:flex;min-height:calc(100vh - 70px);}
.faaf-sidebar{width:240px;background:#0f172a;position:fixed;top:70px;left:0;height:calc(100vh - 70px);overflow-y:auto;z-index:100;display:flex;flex-direction:column;}
.faaf-sidebar-logo{padding:20px;border-bottom:1px solid rgba(255,255,255,.08);}
.faaf-nav-link{display:flex;align-items:center;gap:10px;padding:10px 20px;font-size:.84rem;color:rgba(255,255,255,.65);font-weight:500;text-decoration:none;transition:all .15s;border-left:3px solid transparent;}
.faaf-nav-link:hover{background:rgba(255,255,255,.06);color:#fff;border-left-color:rgba(255,255,255,.3);}
.faaf-nav-link.active{background:rgba(22,163,74,.15);color:#4ade80;border-left-color:#4ade80;font-weight:700;}
.faaf-nav-link i{width:16px;text-align:center;}
.faaf-main{margin-left:240px;flex:1;padding:28px;background:var(--bg-2);min-height:calc(100vh - 70px);}
.faaf-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;}
.faaf-stat{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:20px;display:flex;align-items:center;gap:14px;transition:all var(--t-med);}
.faaf-stat:hover{box-shadow:var(--shadow-md);transform:translateY(-2px);}
.faaf-stat-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;}
.faaf-stat-val{font-family:var(--font-display);font-size:1.5rem;font-weight:800;color:var(--text);line-height:1;}
.faaf-stat-lbl{font-size:.8rem;color:var(--text-muted);margin-top:2px;}
.faaf-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;}
.faaf-card-header{padding:18px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;}
.faaf-card-title{font-family:var(--font-display);font-size:.95rem;font-weight:700;color:var(--text);}
.faaf-alert-row{display:flex;align-items:flex-start;gap:14px;padding:16px 22px;border-bottom:1px solid var(--border);transition:background var(--t-fast);}
.faaf-alert-row:last-child{border-bottom:none;}
.faaf-alert-row:hover{background:var(--bg-2);}
.faaf-alert-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0;margin-top:5px;}
.faaf-alert-icon{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0;}
.faaf-alert-content{flex:1;min-width:0;}
.faaf-alert-title{font-size:.88rem;font-weight:600;color:var(--text);margin-bottom:2px;}
.faaf-alert-msg{font-size:.8rem;color:var(--text-muted);line-height:1.5;}
.faaf-alert-meta{display:flex;flex-wrap:wrap;gap:10px;margin-top:6px;font-size:.73rem;color:var(--text-muted);}
.faaf-alert-meta span{display:flex;align-items:center;gap:4px;}
.faaf-alert-actions{display:flex;gap:6px;flex-shrink:0;}
.faaf-empty{text-align:center;padding:60px 20px;color:var(--text-muted);}
.faaf-empty i{font-size:3rem;margin-bottom:16px;display:block;opacity:.3;}
.faaf-intervene-form{display:none;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:20px 24px;margin-bottom:20px;}
.faaf-intervene-form.show{display:block;}
@media(max-width:1100px){.faaf-stats{grid-template-columns:repeat(2,1fr);}}
@media(max-width:768px){.faaf-sidebar{transform:translateX(-100%);z-index:300;width:260px;box-shadow:var(--shadow-xl);}.faaf-sidebar.open{transform:translateX(0);}.faaf-main{margin-left:0;padding:16px 12px;}.faaf-stats{grid-template-columns:1fr 1fr;gap:10px;}.faaf-alert-row{flex-direction:column;}.faaf-alert-actions{align-self:flex-start;}}
@media(max-width:480px){.faaf-main{padding:12px 8px;}.faaf-stats{grid-template-columns:1fr;}.faaf-stat{padding:14px;}.faaf-stat-val{font-size:1.3rem;}}
</style>
@endsection

@section('content')
<div class="faaf-layout">

  {{-- Sidebar --}}
  <aside class="faaf-sidebar" id="faafSidebar">
    <div class="faaf-sidebar-logo">
      <div style="display:flex;align-items:center;gap:10px;">
        <div style="width:36px;height:36px;border-radius:10px;background:#dc2626;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.9rem;"><i class="fas fa-exclamation-triangle"></i></div>
        <div><div style="font-size:.88rem;font-weight:700;color:#fff;">Farm Alerts</div><div style="font-size:.65rem;color:rgba(255,255,255,.4);">Admin Intervention</div></div>
      </div>
    </div>
    <nav style="flex:1;">
      <a href="{{ route('admin.index') }}" class="faaf-nav-link"><i class="fas fa-chart-bar"></i> Back to Admin</a>
      <a href="{{ route('admin.farm-alerts') }}" class="faaf-nav-link active"><i class="fas fa-exclamation-circle"></i> Active Alerts</a>
      <a href="{{ route('admin.farmers') }}" class="faaf-nav-link"><i class="fas fa-users"></i> Farmers</a>
    </nav>
    <div style="padding:16px 20px;border-top:1px solid rgba(255,255,255,.08);">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
        <div class="avatar avatar-sm" style="background:#dc2626;color:#fff;font-size:.7rem;">{{ Auth::user()->initials }}</div>
        <div><div style="font-size:.82rem;font-weight:600;color:#fff;">{{ Auth::user()->full_name }}</div><div style="font-size:.7rem;color:rgba(255,255,255,.4);">Administrator</div></div>
      </div>
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" style="width:100%;background:rgba(239,68,68,.15);border:1px solid rgba(239,68,68,.3);color:#fca5a5;padding:8px;border-radius:var(--radius-md);font-size:.8rem;cursor:pointer;font-family:var(--font-body);">
          <i class="fas fa-sign-out-alt"></i> Sign Out
        </button>
      </form>
    </div>
  </aside>

  {{-- Main Content --}}
  <main class="faaf-main">
    {{-- Mobile sidebar toggle --}}
    <button onclick="document.getElementById('faafSidebar').classList.toggle('open')" style="display:none;align-items:center;gap:8px;padding:8px 14px;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);font-size:.85rem;font-weight:600;color:var(--text);margin-bottom:12px;cursor:pointer;" id="faafSidebarToggle">
      <i class="fas fa-bars"></i> Menu
    </button>
    <style>@media(max-width:768px){#faafSidebarToggle{display:inline-flex !important;}}</style>

    {{-- Stats --}}
    <div class="faaf-stats">
      <div class="faaf-stat">
        <div class="faaf-stat-icon" style="background:#fef2f2;color:#dc2626;"><i class="fas fa-exclamation-circle"></i></div>
        <div>
          <div class="faaf-stat-val">{{ $stats['total'] }}</div>
          <div class="faaf-stat-lbl">Active Alerts</div>
        </div>
      </div>
      <div class="faaf-stat">
        <div class="faaf-stat-icon" style="background:#fef2f2;color:#dc2626;"><i class="fas fa-bolt"></i></div>
        <div>
          <div class="faaf-stat-val">{{ $stats['urgent'] }}</div>
          <div class="faaf-stat-lbl">Urgent</div>
        </div>
      </div>
      <div class="faaf-stat">
        <div class="faaf-stat-icon" style="background:#eff6ff;color:#2563eb;"><i class="fas fa-envelope"></i></div>
        <div>
          <div class="faaf-stat-val">{{ $stats['unread'] }}</div>
          <div class="faaf-stat-lbl">Unread</div>
        </div>
      </div>
      <div class="faaf-stat">
        <div class="faaf-stat-icon" style="background:var(--green-100);color:var(--green-700);"><i class="fas fa-tractor"></i></div>
        <div>
          <div class="faaf-stat-val">{{ $stats['farms'] }}</div>
          <div class="faaf-stat-lbl">Total Farms</div>
        </div>
      </div>
    </div>

    {{-- Intervention Form --}}
    <div class="faaf-intervene-form" id="interveneForm">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <div style="font-size:.95rem;font-weight:700;color:var(--text);"><i class="fas fa-user-shield" style="color:#dc2626;margin-right:8px;"></i> Send Intervention Alert</div>
        <button onclick="document.getElementById('interveneForm').classList.remove('show')" style="background:var(--bg-2);border:none;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:1rem;color:var(--text-muted);">&times;</button>
      </div>
      <form method="POST" id="interveneAlertForm" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        @csrf
        <input type="hidden" name="farm_id" id="interveneFarmId" value=""/>
        <div class="form-group" style="grid-column:1/-1;">
          <label class="form-label">Alert Title *</label>
          <input type="text" name="title" class="form-input" placeholder="e.g. Urgent: Review your fertilizer spending" required/>
        </div>
        <div class="form-group" style="grid-column:1/-1;">
          <label class="form-label">Message *</label>
          <textarea name="message" class="form-input" rows="3" placeholder="Describe the issue and what the farmer should do..." required></textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Priority *</label>
          <select name="priority" class="form-input form-select" required>
            <option value="low">Low</option>
            <option value="medium" selected>Medium</option>
            <option value="high">High</option>
            <option value="urgent">Urgent</option>
          </select>
        </div>
        <div class="form-group" style="display:flex;align-items:flex-end;">
          <button type="submit" class="btn btn-primary btn-md" style="width:100%;justify-content:center;"><i class="fas fa-paper-plane"></i> Send Intervention</button>
        </div>
      </form>
    </div>

    {{-- Alerts List --}}
    <div class="faaf-card">
      <div class="faaf-card-header">
        <div class="faaf-card-title"><i class="fas fa-bell" style="color:var(--primary);margin-right:8px;"></i> Active Farm Alerts</div>
        <div style="display:flex;gap:8px;">
          <button onclick="document.getElementById('interveneForm').classList.toggle('show');document.getElementById('interveneForm').scrollIntoView({behavior:'smooth'});" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Send Alert</button>
        </div>
      </div>

      @forelse($alerts as $alert)
        @php $alertType = \App\Models\FarmAlert::types()[$alert->type] ?? []; @endphp
        <div class="faaf-alert-row">
          <div class="faaf-alert-dot" style="background:{{ $alert->priority_color }};"></div>
          <div class="faaf-alert-icon" style="background:{{ $alertType['color'] ?? '#94a3b8' }}20;color:{{ $alertType['color'] ?? '#94a3b8' }};">
            <i class="{{ $alertType['icon'] ?? 'fas fa-info' }}"></i>
          </div>
          <div class="faaf-alert-content">
            <div class="faaf-alert-title">{{ $alert->title }}</div>
            <div class="faaf-alert-msg">{{ $alert->message }}</div>
            <div class="faaf-alert-meta">
              <span><i class="fas fa-tractor"></i> {{ $alert->farm->name ?? 'Unknown Farm' }}</span>
              @if($alert->farm && $alert->farm->user)
                <span><i class="fas fa-user"></i> {{ $alert->farm->user->full_name }}</span>
              @endif
              @if($alert->season)
                <span><i class="fas fa-seedling"></i> {{ $alert->season->name }}</span>
              @endif
              <span><i class="fas fa-tag"></i> {{ $alertType['label'] ?? $alert->type }}</span>
              <span><i class="fas fa-clock"></i> {{ $alert->created_at->diffForHumans() }}</span>
              <span style="font-weight:600;color:{{ $alert->priority_color }};text-transform:uppercase;letter-spacing:.04em;">{{ $alert->priority }}</span>
              @if($alert->is_read)
                <span style="color:var(--success);"><i class="fas fa-check-circle"></i> Read</span>
              @endif
              @if($alert->requires_action)
                <span style="color:#dc2626;font-weight:600;"><i class="fas fa-exclamation-triangle"></i Requires Action</span>
              @endif
            </div>
          </div>
          <div class="faaf-alert-actions">
            <form method="POST" action="{{ route('admin.farm-alerts.dismiss', $alert) }}" style="display:inline;" onsubmit="return confirm('Dismiss this alert?')">
              @csrf
              <button type="submit" class="btn btn-outline btn-sm" style="font-size:.72rem;padding:4px 10px;"><i class="fas fa-times"></i> Dismiss</button>
            </form>
            <button type="button" class="btn btn-primary btn-sm" style="font-size:.72rem;padding:4px 10px;" onclick="openIntervene({{ $alert->farm_id }},'{{ addslashes($alert->farm->name ?? '') }}')"><i class="fas fa-user-shield"></i> Intervene</button>
          </div>
        </div>
      @empty
        <div class="faaf-empty">
          <i class="fas fa-check-circle" style="color:var(--green-600);"></i>
          <h3 style="margin-bottom:8px;">All clear!</h3>
          <p>No active farm alerts. Farmers are doing great.</p>
        </div>
      @endforelse
    </div>

    @if($alerts->hasPages())
      <div style="margin-top:20px;">{{ $alerts->links() }}</div>
    @endif
  </main>
</div>
@endsection

@section('extra_js')
<script>
function openIntervene(farmId, farmName) {
  const form = document.getElementById('interveneForm');
  document.getElementById('interveneFarmId').value = farmId;
  form.classList.add('show');
  form.scrollIntoView({behavior:'smooth'});
  form.querySelector('[name="title"]').placeholder = 'Alert for ' + farmName + '...';
}
</script>
@endsection
