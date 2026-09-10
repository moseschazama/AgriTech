@extends('layouts.app')
@section('title', 'Farm Records & Decision Support — AgriTech Pro')
@section('extra_css')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"/>
<link rel="stylesheet" href="{{ asset('css/farm-records.css') }}"/>
@endsection

@section('content')

{{-- Hero --}}
<section class="page-hero farm-records-hero page-hero-image">
  <div class="container">
    <div class="page-hero-content">
      <span class="page-hero-badge"><i class="fas fa-tractor"></i> Farm Records</span>
      <h1 class="page-hero-title">Decision Support <span class="accent">System</span></h1>
      <p class="page-hero-desc">Track seasons, analyze costs & revenue, get intelligent insights, and make data-driven farming decisions.</p>
      <div class="page-hero-pills">
        <span class="page-hero-pill"><i class="fas fa-chart-line"></i> Analytics</span>
        <span class="page-hero-pill"><i class="fas fa-lightbulb"></i> Insights</span>
        <span class="page-hero-pill"><i class="fas fa-bell"></i> Alerts</span>
        <span class="page-hero-pill"><i class="fas fa-calendar-alt"></i> Season Tracking</span>
      </div>
    </div>
  </div>
</section>

<div class="section" style="background:var(--bg-2);">
  <div class="container">

    {{-- Alerts --}}
    @if($alerts->count() > 0)
    <div class="fr-alerts">
      @foreach($alerts as $alert)
        @php $alertType = \App\Models\FarmAlert::types()[$alert->type] ?? []; @endphp
        <div class="fr-alert" style="border-left:4px solid {{ $alert->priority_color }};">
          <div class="fr-alert-icon" style="background:{{ $alertType['color'] ?? '#94a3b8' }}20;color:{{ $alertType['color'] ?? '#94a3b8' }};">
            <i class="{{ $alertType['icon'] ?? 'fas fa-info' }}"></i>
          </div>
          <div class="fr-alert-content">
            <div class="fr-alert-title">{{ $alert->title }}</div>
            <div class="fr-alert-msg">{{ $alert->message }}</div>
            <div class="fr-alert-time">{{ $alert->created_at->diffForHumans() }}</div>
          </div>
          <div class="fr-alert-actions">
            <form method="POST" action="{{ route('farm-records.alert.dismiss', $alert) }}" style="display:inline;">
              @csrf
              <button type="submit" class="btn btn-sm btn-outline" style="font-size:.75rem;padding:5px 10px;">Dismiss</button>
            </form>
          </div>
        </div>
      @endforeach
    </div>
    @endif

    {{-- Stats Row --}}
    <div class="fr-stats">
      <div class="fr-stat">
        <div class="fr-stat-icon" style="background:var(--green-100);color:var(--green-700);"><i class="fas fa-coins"></i></div>
        <div>
          <div class="fr-stat-val">MWK {{ number_format($analytics['total_revenue']) }}</div>
          <div class="fr-stat-lbl">Total Revenue</div>
          @if($analytics['net_profit'] > 0)
            <div class="fr-stat-trend up"><i class="fas fa-arrow-up"></i> {{ number_format($analytics['roi'], 1) }}% ROI</div>
          @endif
        </div>
      </div>
      <div class="fr-stat">
        <div class="fr-stat-icon" style="background:#fef2f2;color:#dc2626;"><i class="fas fa-receipt"></i></div>
        <div>
          <div class="fr-stat-val">MWK {{ number_format($analytics['total_costs']) }}</div>
          <div class="fr-stat-lbl">Total Costs</div>
        </div>
      </div>
      <div class="fr-stat">
        <div class="fr-stat-icon" style="background:#eff6ff;color:#2563eb;"><i class="fas fa-seedling"></i></div>
        <div>
          <div class="fr-stat-val">{{ $analytics['season_count'] }}</div>
          <div class="fr-stat-lbl">Seasons Tracked</div>
          @if($analytics['active_seasons'] > 0)
            <div class="fr-stat-trend up"><i class="fas fa-circle" style="font-size:.5rem;"></i> {{ $analytics['active_seasons'] }} active</div>
          @endif
        </div>
      </div>
      <div class="fr-stat">
        <div class="fr-stat-icon" style="background:#fef9c3;color:#a16207;"><i class="fas fa-balance-scale"></i></div>
        <div>
          <div class="fr-stat-val" style="color:{{ $analytics['net_profit'] >= 0 ? 'var(--success)' : 'var(--danger)' }}">
            MWK {{ number_format($analytics['net_profit']) }}
          </div>
          <div class="fr-stat-lbl">Net Profit</div>
        </div>
      </div>
    </div>

    {{-- Tabs --}}
    <div class="fr-tabs" id="frTabs">
      <button class="fr-tab active" data-tab="seasons"><i class="fas fa-calendar-alt"></i> Seasons</button>
      <button class="fr-tab" data-tab="analytics"><i class="fas fa-chart-bar"></i> Analytics</button>
      <button class="fr-tab" data-tab="insights"><i class="fas fa-lightbulb"></i> Insights</button>
      <button class="fr-tab" data-tab="events"><i class="fas fa-clipboard-list"></i> Events</button>
    </div>

    {{-- Seasons Tab --}}
    <div id="tab-seasons" class="fr-tab-content" style="display:block;">
      {{-- Add Season Button --}}
      <div style="margin-bottom:20px;display:flex;justify-content:flex-end;">
        <button onclick="document.getElementById('addSeasonModal').style.display='flex'" class="btn btn-primary btn-md">
          <i class="fas fa-plus"></i> New Season
        </button>
      </div>

      <div class="fr-seasons-grid">
        @forelse($seasons as $season)
          <div class="fr-season-card">
            <div class="fr-season-header">
              <div>
                <div class="fr-season-name">{{ $season->name }}</div>
                <div style="font-size:.78rem;color:var(--text-muted);margin-top:2px;">{{ $season->crop }}{{ $season->field_name ? ' · '.$season->field_name : '' }}</div>
              </div>
              <span class="fr-season-status {{ $season->status }}">{{ ucfirst($season->status) }}</span>
            </div>
            <div class="fr-season-body">
              <div class="fr-season-meta">
                @if($season->field_size_hectares)
                  <span class="fr-season-meta-item"><i class="fas fa-ruler-combined"></i> {{ $season->field_size_hectares }} ha</span>
                @endif
                @if($season->planted_at)
                  <span class="fr-season-meta-item"><i class="fas fa-calendar"></i> Planted {{ $season->planted_at->format('M d') }}</span>
                @endif
                @if($season->expected_harvest_at)
                  <span class="fr-season-meta-item"><i class="fas fa-clock"></i> Harvest {{ $season->expected_harvest_at->format('M d') }}</span>
                @endif
              </div>

              <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:12px;">
                <div style="background:var(--bg-2);border-radius:var(--radius-md);padding:10px 12px;">
                  <div style="font-size:.72rem;color:var(--text-muted);margin-bottom:2px;">Costs</div>
                  <div style="font-size:.9rem;font-weight:700;color:var(--text);">MWK {{ number_format($season->total_costs) }}</div>
                </div>
                <div style="background:var(--bg-2);border-radius:var(--radius-md);padding:10px 12px;">
                  <div style="font-size:.72rem;color:var(--text-muted);margin-bottom:2px;">Revenue</div>
                  <div style="font-size:.9rem;font-weight:700;color:var(--primary);">MWK {{ number_format($season->actual_revenue) }}</div>
                </div>
              </div>

              @if($season->status === 'active' && $season->planted_at && $season->expected_harvest_at)
                @php $progress = $season->progress_percentage; @endphp
                <div class="fr-season-progress">
                  <div class="fr-season-progress-bar">
                    <div class="fr-season-progress-fill" style="width:{{ $progress }}%;"></div>
                  </div>
                  <div class="fr-season-progress-label">
                    <span>{{ $progress }}% complete</span>
                    <span>{{ $season->days_until_harvest }} days to harvest</span>
                  </div>
                </div>
              @endif

              @if($season->roi_percentage !== null)
                <div style="margin-top:10px;display:flex;align-items:center;gap:6px;font-size:.8rem;">
                  <span style="color:var(--text-muted);">ROI:</span>
                  <span style="font-weight:700;color:{{ $season->roi_percentage >= 0 ? 'var(--success)' : 'var(--danger)' }}">
                    {{ $season->roi_percentage }}%
                  </span>
                  @if($season->net_profit > 0)
                    <span style="color:var(--text-muted);">· Net: MWK {{ number_format($season->net_profit) }}</span>
                  @endif
                </div>
              @endif
            </div>
            <div class="fr-season-footer">
              <button onclick="openCostModal({{ $season->id }})" class="btn btn-outline btn-sm"><i class="fas fa-plus"></i> Cost</button>
              <button onclick="openSaleModal({{ $season->id }})" class="btn btn-outline btn-sm"><i class="fas fa-plus"></i> Sale</button>
              <button onclick="openEventModal({{ $season->id }})" class="btn btn-outline btn-sm"><i class="fas fa-plus"></i> Event</button>
              @if($season->status === 'planning')
                <form method="POST" action="{{ route('farm-records.season.update', $season) }}" style="display:inline;">
                  @csrf @method('PUT')
                  <input type="hidden" name="status" value="active"/>
                  <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-play"></i> Start</button>
                </form>
              @endif
            </div>
          </div>
        @empty
          <div style="grid-column:1/-1;text-align:center;padding:60px;color:var(--text-muted);">
            <i class="fas fa-seedling" style="font-size:1.75rem;margin-bottom:14px;display:block;opacity:.3;"></i>
            <h3 style="margin-bottom:8px;">No seasons yet</h3>
            <p>Start tracking your farming seasons to see analytics and insights.</p>
            <button onclick="document.getElementById('addSeasonModal').style.display='flex'" class="btn btn-primary btn-sm" style="margin-top:16px;">
              <i class="fas fa-plus"></i> Create First Season
            </button>
          </div>
        @endforelse
      </div>
    </div>

    {{-- Analytics Tab --}}
    <div id="tab-analytics" class="fr-tab-content" style="display:none;">
      <div class="fr-analytics">
        <div class="fr-chart-card">
          <div class="fr-chart-header">
            <div class="fr-chart-title"><i class="fas fa-chart-bar" style="color:var(--primary);margin-right:8px;"></i> Cost Breakdown by Category</div>
          </div>
          <div class="fr-chart-body">
            <canvas id="costChart" class="fr-chart-canvas"></canvas>
          </div>
        </div>
        <div class="fr-chart-card">
          <div class="fr-chart-header">
            <div class="fr-chart-title"><i class="fas fa-chart-pie" style="color:var(--earth-500);margin-right:8px;"></i> Cost Distribution</div>
          </div>
          <div class="fr-chart-body">
            <canvas id="costPieChart" class="fr-chart-canvas"></canvas>
          </div>
        </div>
      </div>
      <div class="fr-chart-card" style="margin-bottom:24px;">
        <div class="fr-chart-header">
          <div class="fr-chart-title"><i class="fas fa-chart-line" style="color:var(--green-600);margin-right:8px;"></i> Revenue Over Time</div>
        </div>
        <div class="fr-chart-body">
          <canvas id="revenueChart" style="width:100%;height:260px;"></canvas>
        </div>
      </div>
      @if($seasons->count() > 1)
      <div class="fr-chart-card">
        <div class="fr-chart-header">
          <div class="fr-chart-title"><i class="fas fa-balance-scale" style="color:#8b5cf6;margin-right:8px;"></i> Season Comparison</div>
        </div>
        <div class="fr-chart-body">
          <canvas id="seasonCompareChart" style="width:100%;height:260px;"></canvas>
        </div>
      </div>
      @endif
    </div>

    {{-- Insights Tab --}}
    <div id="tab-insights" class="fr-tab-content" style="display:none;">
      <div class="fr-insights">
        @forelse($insights as $insight)
          <div class="fr-insight">
            <div class="fr-insight-icon" style="background:{{ $insight['color'] }}20;color:{{ $insight['color'] }};">
              <i class="{{ $insight['icon'] }}"></i>
            </div>
            <div>
              <div class="fr-insight-title">{{ $insight['title'] }}</div>
              <div class="fr-insight-msg">{{ $insight['message'] }}</div>
            </div>
          </div>
        @empty
          <div style="text-align:center;padding:60px;color:var(--text-muted);">
            <i class="fas fa-lightbulb" style="font-size:1.75rem;margin-bottom:14px;display:block;opacity:.3;"></i>
            <h3 style="margin-bottom:8px;">No insights yet</h3>
            <p>Add costs, sales, and events to your seasons to get intelligent recommendations.</p>
          </div>
        @endforelse
      </div>
    </div>

    {{-- Events Tab --}}
    <div id="tab-events" class="fr-tab-content" style="display:none;">
      <div class="fr-events">
        @forelse($recentEvents as $event)
          @php $eventType = \App\Models\FarmEvent::types()[$event->type] ?? []; @endphp
          <div class="fr-event">
            <div class="fr-event-icon" style="background:{{ $eventType['color'] ?? '#94a3b8' }}20;color:{{ $eventType['color'] ?? '#94a3b8' }};">
              <i class="{{ $eventType['icon'] ?? 'fas fa-info' }}"></i>
            </div>
            <div class="fr-event-content">
              <div class="fr-event-title">{{ $event->title }}</div>
              @if($event->description)
                <div class="fr-event-desc">{{ $event->description }}</div>
              @endif
              <div class="fr-event-date">{{ $event->occurred_at->format('M d, Y') }} · {{ $eventType['label'] ?? $event->type }}</div>
              @if($event->requires_follow_up && !$event->is_resolved)
                <div style="margin-top:6px;"><span class="badge badge-earth" style="font-size:.68rem;"><i class="fas fa-clock"></i> Follow-up {{ $event->follow_up_date ? 'by '.$event->follow_up_date->format('M d') : 'needed' }}</span></div>
              @endif
            </div>
          </div>
        @empty
          <div style="text-align:center;padding:60px;color:var(--text-muted);">
            <i class="fas fa-clipboard-list" style="font-size:1.75rem;margin-bottom:14px;display:block;opacity:.3;"></i>
            <h3 style="margin-bottom:8px;">No events logged</h3>
            <p>Start logging farm activities, weather, and treatments.</p>
          </div>
        @endforelse
      </div>
    </div>

  </div>
</div>

{{-- Add Season Modal --}}
<div id="addSeasonModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;padding:20px;" onclick="if(event.target===this)this.style.display='none'">
  <div style="background:var(--bg-card);border-radius:var(--radius-xl);max-width:560px;width:100%;max-height:90vh;overflow-y:auto;box-shadow:var(--shadow-xl);">
    <div style="display:flex;justify-content:space-between;align-items:center;padding:20px 24px;border-bottom:1px solid var(--border);">
      <h3 style="font-size:1.1rem;font-weight:800;color:var(--text);"><i class="fas fa-seedling" style="color:var(--primary);margin-right:8px;"></i> New Season</h3>
      <button onclick="document.getElementById('addSeasonModal').style.display='none'" style="background:var(--bg-2);border:none;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:1rem;color:var(--text-muted);">&times;</button>
    </div>
    <form method="POST" action="{{ route('farm-records.season.store') }}" style="padding:20px 24px;">
      @csrf
      <div class="fr-form-grid">
        <div class="form-group">
          <label class="form-label">Season Name *</label>
          <input type="text" name="name" class="form-input" placeholder="e.g. 2025/26 Main Season" required/>
        </div>
        <div class="form-group">
          <label class="form-label">Crop *</label>
          <input type="text" name="crop" class="form-input" placeholder="e.g. Maize, Beans, Tomatoes" required/>
        </div>
        <div class="form-group">
          <label class="form-label">Field Name</label>
          <input type="text" name="field_name" class="form-input" placeholder="e.g. North Field, Plot A"/>
        </div>
        <div class="form-group">
          <label class="form-label">Field Size (hectares)</label>
          <input type="number" name="field_size_hectares" class="form-input" step="0.01" min="0" placeholder="e.g. 2.5"/>
        </div>
        <div class="form-group">
          <label class="form-label">Planting Date</label>
          <input type="date" name="planted_at" class="form-input"/>
        </div>
        <div class="form-group">
          <label class="form-label">Expected Harvest Date</label>
          <input type="date" name="expected_harvest_at" class="form-input"/>
        </div>
        <div class="form-group">
          <label class="form-label">Expected Yield (kg)</label>
          <input type="number" name="expected_yield_kg" class="form-input" min="0" placeholder="e.g. 5000"/>
        </div>
        <div class="form-group">
          <label class="form-label">Expected Revenue (MWK)</label>
          <input type="number" name="expected_revenue" class="form-input" min="0" placeholder="e.g. 750000"/>
        </div>
        <div class="form-group fr-form-full">
          <label class="form-label">Notes</label>
          <textarea name="notes" class="form-input" rows="2" placeholder="Any additional notes about this season..."></textarea>
        </div>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:18px;">
        <button type="button" onclick="document.getElementById('addSeasonModal').style.display='none'" class="btn btn-outline btn-md">Cancel</button>
        <button type="submit" class="btn btn-primary btn-md"><i class="fas fa-plus"></i> Create Season</button>
      </div>
    </form>
  </div>
</div>

{{-- Add Cost Modal --}}
<div id="addCostModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;padding:20px;" onclick="if(event.target===this)this.style.display='none'">
  <div style="background:var(--bg-card);border-radius:var(--radius-xl);max-width:500px;width:100%;max-height:90vh;overflow-y:auto;box-shadow:var(--shadow-xl);">
    <div style="display:flex;justify-content:space-between;align-items:center;padding:20px 24px;border-bottom:1px solid var(--border);">
      <h3 style="font-size:1.1rem;font-weight:800;color:var(--text);"><i class="fas fa-receipt" style="color:#ef4444;margin-right:8px;"></i> Add Cost</h3>
      <button onclick="document.getElementById('addCostModal').style.display='none'" style="background:var(--bg-2);border:none;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:1rem;color:var(--text-muted);">&times;</button>
    </div>
    <form id="costForm" method="POST" style="padding:20px 24px;">
      @csrf
      <div class="fr-form-grid">
        <div class="form-group fr-form-full">
          <label class="form-label">Category *</label>
          <select name="category" class="form-input form-select" required>
            <option value="">Select category</option>
            @foreach(\App\Models\FarmCost::categories() as $key=>$cat)
              <option value="{{ $key }}">{{ $cat['label'] }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group fr-form-full">
          <label class="form-label">Item Name *</label>
          <input type="text" name="item_name" class="form-input" placeholder="e.g. NPK Fertilizer 50kg" required/>
        </div>
        <div class="form-group">
          <label class="form-label">Quantity</label>
          <input type="number" name="quantity" class="form-input" step="0.01" min="0" placeholder="e.g. 10"/>
        </div>
        <div class="form-group">
          <label class="form-label">Unit</label>
          <input type="text" name="unit" class="form-input" placeholder="e.g. kg, bags, hours"/>
        </div>
        <div class="form-group">
          <label class="form-label">Unit Cost (MWK) *</label>
          <input type="number" name="unit_cost" class="form-input" step="0.01" min="0" required placeholder="e.g. 5000"/>
        </div>
        <div class="form-group">
          <label class="form-label">Total Cost (MWK) *</label>
          <input type="number" name="total_cost" class="form-input" step="0.01" min="0" required placeholder="e.g. 50000"/>
        </div>
        <div class="form-group">
          <label class="form-label">Purchase Date</label>
          <input type="date" name="purchased_at" class="form-input"/>
        </div>
        <div class="form-group">
          <label class="form-label">Supplier</label>
          <input type="text" name="supplier" class="form-input" placeholder="e.g. Agro Dealers Ltd"/>
        </div>
        <div class="form-group fr-form-full">
          <label class="form-label">Notes</label>
          <textarea name="notes" class="form-input" rows="2" placeholder="Additional notes..."></textarea>
        </div>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:18px;">
        <button type="button" onclick="document.getElementById('addCostModal').style.display='none'" class="btn btn-outline btn-md">Cancel</button>
        <button type="submit" class="btn btn-primary btn-md"><i class="fas fa-plus"></i> Add Cost</button>
      </div>
    </form>
  </div>
</div>

{{-- Add Sale Modal --}}
<div id="addSaleModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;padding:20px;" onclick="if(event.target===this)this.style.display='none'">
  <div style="background:var(--bg-card);border-radius:var(--radius-xl);max-width:500px;width:100%;max-height:90vh;overflow-y:auto;box-shadow:var(--shadow-xl);">
    <div style="display:flex;justify-content:space-between;align-items:center;padding:20px 24px;border-bottom:1px solid var(--border);">
      <h3 style="font-size:1.1rem;font-weight:800;color:var(--text);"><i class="fas fa-dollar-sign" style="color:var(--green-600);margin-right:8px;"></i> Record Sale</h3>
      <button onclick="document.getElementById('addSaleModal').style.display='none'" style="background:var(--bg-2);border:none;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:1rem;color:var(--text-muted);">&times;</button>
    </div>
    <form id="saleForm" method="POST" style="padding:20px 24px;">
      @csrf
      <div class="fr-form-grid">
        <div class="form-group">
          <label class="form-label">Crop *</label>
          <input type="text" name="crop" class="form-input" placeholder="e.g. Maize" required/>
        </div>
        <div class="form-group">
          <label class="form-label">Quantity (kg) *</label>
          <input type="number" name="quantity_kg" class="form-input" step="0.01" min="0.01" required placeholder="e.g. 500"/>
        </div>
        <div class="form-group">
          <label class="form-label">Price per kg (MWK) *</label>
          <input type="number" name="price_per_kg" class="form-input" step="0.01" min="0" required placeholder="e.g. 280"/>
        </div>
        <div class="form-group">
          <label class="form-label">Total Revenue (MWK) *</label>
          <input type="number" name="total_revenue" class="form-input" step="0.01" min="0" required placeholder="e.g. 140000"/>
        </div>
        <div class="form-group">
          <label class="form-label">Buyer Name</label>
          <input type="text" name="buyer_name" class="form-input" placeholder="e.g. John Moyo"/>
        </div>
        <div class="form-group">
          <label class="form-label">Buyer Type</label>
          <select name="buyer_type" class="form-input form-select">
            <option value="">Select type</option>
            @foreach(\App\Models\FarmSale::buyerTypes() as $key=>$label)
              <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Market / Location</label>
          <input type="text" name="market" class="form-input" placeholder="e.g. Lilongwe Market"/>
        </div>
        <div class="form-group">
          <label class="form-label">Sale Date *</label>
          <input type="date" name="sold_at" class="form-input" required/>
        </div>
        <div class="form-group fr-form-full">
          <label class="form-label">Notes</label>
          <textarea name="notes" class="form-input" rows="2" placeholder="Additional notes..."></textarea>
        </div>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:18px;">
        <button type="button" onclick="document.getElementById('addSaleModal').style.display='none'" class="btn btn-outline btn-md">Cancel</button>
        <button type="submit" class="btn btn-primary btn-md"><i class="fas fa-plus"></i> Record Sale</button>
      </div>
    </form>
  </div>
</div>

{{-- Add Event Modal --}}
<div id="addEventModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;padding:20px;" onclick="if(event.target===this)this.style.display='none'">
  <div style="background:var(--bg-card);border-radius:var(--radius-xl);max-width:500px;width:100%;max-height:90vh;overflow-y:auto;box-shadow:var(--shadow-xl);">
    <div style="display:flex;justify-content:space-between;align-items:center;padding:20px 24px;border-bottom:1px solid var(--border);">
      <h3 style="font-size:1.1rem;font-weight:800;color:var(--text);"><i class="fas fa-clipboard" style="color:#8b5cf6;margin-right:8px;"></i> Log Event</h3>
      <button onclick="document.getElementById('addEventModal').style.display='none'" style="background:var(--bg-2);border:none;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:1rem;color:var(--text-muted);">&times;</button>
    </div>
    <form id="eventForm" method="POST" style="padding:20px 24px;">
      @csrf
      <div class="fr-form-grid">
        <div class="form-group">
          <label class="form-label">Event Type *</label>
          <select name="type" class="form-input form-select" required>
            <option value="">Select type</option>
            @foreach(\App\Models\FarmEvent::types() as $key=>$type)
              <option value="{{ $key }}">{{ $type['label'] }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Date *</label>
          <input type="date" name="occurred_at" class="form-input" required/>
        </div>
        <div class="form-group fr-form-full">
          <label class="form-label">Title *</label>
          <input type="text" name="title" class="form-input" placeholder="e.g. Applied NPK fertilizer" required/>
        </div>
        <div class="form-group fr-form-full">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-input" rows="2" placeholder="Details about this event..."></textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Severity</label>
          <select name="severity" class="form-input form-select">
            <option value="">Select severity</option>
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
            <option value="critical">Critical</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Requires Follow-up?</label>
          <select name="requires_follow_up" class="form-input form-select">
            <option value="0">No</option>
            <option value="1">Yes</option>
          </select>
        </div>
        <div class="form-group fr-form-full" id="followUpDateField" style="display:none;">
          <label class="form-label">Follow-up Date</label>
          <input type="date" name="follow_up_date" class="form-input"/>
        </div>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:18px;">
        <button type="button" onclick="document.getElementById('addEventModal').style.display='none'" class="btn btn-outline btn-md">Cancel</button>
        <button type="submit" class="btn btn-primary btn-md"><i class="fas fa-plus"></i> Log Event</button>
      </div>
    </form>
  </div>
</div>

@include('partials.footer')
@endsection

@section('extra_js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ── Tab Switching ──
document.querySelectorAll('#frTabs .fr-tab').forEach(tab => {
  tab.addEventListener('click', () => {
    document.querySelectorAll('#frTabs .fr-tab').forEach(t => t.classList.remove('active'));
    tab.classList.add('active');
    document.querySelectorAll('.fr-tab-content').forEach(c => c.style.display = 'none');
    document.getElementById('tab-' + tab.dataset.tab).style.display = 'block';
    if (tab.dataset.tab === 'analytics') loadCharts();
  });
});

// ── Modal Helpers ──
function openCostModal(seasonId) {
  const modal = document.getElementById('addCostModal');
  const form = document.getElementById('costForm');
  form.action = '/farm-records/seasons/' + seasonId + '/costs';
  modal.style.display = 'flex';
}
function openSaleModal(seasonId) {
  const modal = document.getElementById('addSaleModal');
  const form = document.getElementById('saleForm');
  form.action = '/farm-records/seasons/' + seasonId + '/sales';
  modal.style.display = 'flex';
}
function openEventModal(seasonId) {
  const modal = document.getElementById('addEventModal');
  const form = document.getElementById('eventForm');
  form.action = '/farm-records/seasons/' + seasonId + '/events';
  modal.style.display = 'flex';
}

// Show follow-up date when required
document.querySelector('#addEventModal [name="requires_follow_up"]')?.addEventListener('change', function() {
  document.getElementById('followUpDateField').style.display = this.value === '1' ? 'block' : 'none';
});

// ── Charts ──
let chartsLoaded = false;
function loadCharts() {
  if (chartsLoaded) return;
  chartsLoaded = true;

  const farmId = {{ $farm->id }};
  fetch('/farm-records/' + farmId + '/analytics')
    .then(r => r.json())
    .then(data => {
      const categoryColors = {
        seed: '#22c55e', fertilizer: '#3b82f6', chemicals: '#ef4444',
        labour: '#f59e0b', transport: '#8b5cf6', irrigation: '#0ea5e9',
        equipment: '#64748b', rent: '#ec4899', other: '#94a3b8'
      };

      // Cost Bar Chart
      if (data.costByCategory.length > 0) {
        const labels = data.costByCategory.map(c => {
          const cats = @json(\App\Models\FarmCost::categories());
          return cats[c.category]?.label || c.category;
        });
        const values = data.costByCategory.map(c => parseFloat(c.total));
        const colors = data.costByCategory.map(c => categoryColors[c.category] || '#94a3b8');

        new Chart(document.getElementById('costChart'), {
          type: 'bar',
          data: { labels, datasets: [{ label: 'Cost (MWK)', data: values, backgroundColor: colors, borderRadius: 8, barThickness: 36 }] },
          options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { callback: v => 'MWK ' + v.toLocaleString() } } } }
        });

        new Chart(document.getElementById('costPieChart'), {
          type: 'doughnut',
          data: { labels, datasets: [{ data: values, backgroundColor: colors, borderWidth: 2, borderColor: 'var(--bg-card)' }] },
          options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 10, font: { size: 11 } } } } }
        });
      }

      // Revenue Line Chart
      if (data.revenueOverTime.length > 0) {
        new Chart(document.getElementById('revenueChart'), {
          type: 'line',
          data: {
            labels: data.revenueOverTime.map(r => r.month),
            datasets: [{ label: 'Revenue (MWK)', data: data.revenueOverTime.map(r => parseFloat(r.total)), borderColor: '#22c55e', backgroundColor: 'rgba(34,197,94,0.1)', fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#22c55e' }]
          },
          options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { callback: v => 'MWK ' + v.toLocaleString() } } } }
        });
      }

      // Season Comparison
      if (data.seasonComparison.length > 1) {
        new Chart(document.getElementById('seasonCompareChart'), {
          type: 'bar',
          data: {
            labels: data.seasonComparison.map(s => s.name),
            datasets: [
              { label: 'Costs', data: data.seasonComparison.map(s => s.costs), backgroundColor: '#ef4444', borderRadius: 6 },
              { label: 'Revenue', data: data.seasonComparison.map(s => s.revenue), backgroundColor: '#22c55e', borderRadius: 6 },
              { label: 'Profit', data: data.seasonComparison.map(s => s.profit), backgroundColor: '#3b82f6', borderRadius: 6 }
            ]
          },
          options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top', labels: { boxWidth: 12 } } }, scales: { y: { beginAtZero: true, ticks: { callback: v => 'MWK ' + v.toLocaleString() } } } }
        });
      }
    });
}
</script>
@endsection
