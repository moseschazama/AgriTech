@extends('layouts.app')
@section('title', 'Admin Panel — AgriTech Pro')
@section('extra_css')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"/>
<link rel="stylesheet" href="{{ asset('css/home.css') }}"/>
<style>
.admin-layout{display:flex;min-height:calc(100vh - 70px);}
.admin-sidebar{width:240px;background:#0f172a;position:fixed;top:70px;left:0;height:calc(100vh - 70px);overflow-y:auto;z-index:100;display:flex;flex-direction:column;}
.admin-sidebar-logo{padding:20px;border-bottom:1px solid rgba(255,255,255,.08);}
.admin-nav-label{font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.3);padding:14px 20px 6px;}
.admin-nav-link{display:flex;align-items:center;gap:10px;padding:10px 20px;font-size:.84rem;color:rgba(255,255,255,.65);font-weight:500;text-decoration:none;transition:all .15s;border-left:3px solid transparent;}
.admin-nav-link:hover{background:rgba(255,255,255,.06);color:#fff;border-left-color:rgba(255,255,255,.3);}
.admin-nav-link.active{background:rgba(22,163,74,.15);color:#4ade80;border-left-color:#4ade80;font-weight:700;}
.admin-nav-link i{width:16px;text-align:center;}
.admin-main{margin-left:240px;flex:1;padding:28px;background:var(--bg-2);min-height:calc(100vh - 70px);}
.admin-tabs{display:flex;gap:4px;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:6px;margin-bottom:24px;flex-wrap:wrap;}
.admin-tab{padding:9px 16px;border-radius:var(--radius-md);font-size:.83rem;font-weight:600;color:var(--text-muted);cursor:pointer;border:none;background:transparent;transition:all .15s;font-family:var(--font-body);}
.admin-tab.active{background:var(--primary);color:#fff;box-shadow:var(--shadow-green);}
.admin-panel{display:none;}.admin-panel.active{display:block;}
.admin-stat-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:20px;transition:all .2s;}
.admin-stat-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-md);}
.admin-stat-val{font-family:var(--font-display);font-size:2rem;font-weight:800;color:var(--text);}
.admin-stat-label{font-size:.78rem;color:var(--text-muted);margin-top:4px;}
.data-table{width:100%;border-collapse:collapse;}
.data-table th{font-size:.73rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);padding:10px 14px;text-align:left;border-bottom:2px solid var(--border);white-space:nowrap;}
.data-table td{padding:11px 14px;font-size:.82rem;border-bottom:1px solid var(--border);color:var(--text);vertical-align:middle;}
.data-table tbody tr:hover td{background:var(--bg-2);}
.data-table td form{display:inline;}
.district-bar{display:flex;align-items:center;gap:10px;margin-bottom:10px;}
.district-bar-fill{height:8px;background:linear-gradient(90deg,var(--primary),#4ade80);border-radius:20px;transition:width .8s ease;}
.sms-template{background:var(--bg-2);border:1.5px solid var(--border);border-radius:var(--radius-md);padding:14px;cursor:pointer;transition:all .15s;margin-bottom:10px;}
.sms-template:hover{border-color:var(--primary);background:var(--green-50);}
.sms-template-title{font-size:.82rem;font-weight:700;color:var(--text);margin-bottom:4px;}
.sms-template-preview{font-size:.76rem;color:var(--text-muted);line-height:1.4;}
.toggle-switch{position:relative;display:inline-block;width:44px;height:24px;}
.toggle-switch input{opacity:0;width:0;height:0;}
.toggle-slider{position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background:var(--gray-300);border-radius:24px;transition:.3s;}
.toggle-slider:before{position:absolute;content:'';height:18px;width:18px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:.3s;}
.toggle-switch input:checked+.toggle-slider{background:var(--primary);}
.toggle-switch input:checked+.toggle-slider:before{transform:translateX(20px);}
@media(max-width:768px){.admin-sidebar{transform:translateX(-100%);z-index:300;width:260px;box-shadow:var(--shadow-xl);}.admin-sidebar.open{transform:translateX(0);}.admin-main{margin-left:0;padding:16px 12px;}.admin-tabs{overflow-x:auto;flex-wrap:nowrap;-webkit-overflow-scrolling:touch;scrollbar-width:none;}.admin-tabs::-webkit-scrollbar{display:none;}.admin-tab{white-space:nowrap;flex-shrink:0;padding:8px 12px;font-size:.78rem;}.admin-stat-card{padding:14px;}.admin-stat-val{font-size:1.5rem;}.admin-stats-grid{grid-template-columns:repeat(2,1fr) !important;}}
@media(max-width:480px){.admin-main{padding:12px 8px;}.admin-tabs{gap:2px;padding:4px;}.admin-tab{padding:7px 10px;font-size:.75rem;}.admin-stats-grid{grid-template-columns:1fr 1fr !important;gap:10px !important;}}
</style>
@endsection

@section('content')
<div class="admin-layout">

{{-- ══ SIDEBAR ══ --}}
<aside class="admin-sidebar" id="adminSidebar">
  <div class="admin-sidebar-logo">
    <div style="display:flex;align-items:center;gap:10px;">
      <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#16a34a,#15803d);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.9rem;"><i class="fas fa-seedling"></i></div>
      <div><div style="font-family:var(--font-display);font-size:.88rem;font-weight:700;color:#fff;">AgriTech Pro</div><div style="font-size:.65rem;color:rgba(255,255,255,.4);">Admin Panel</div></div>
    </div>
  </div>
  <nav style="flex:1;">
    <div class="admin-nav-label">Management</div>
    {{-- NOTE: tab-reviews is included here alongside the others --}}
    @foreach([
      ['tab-analytics',  'fas fa-chart-bar',       'Analytics'],
      ['tab-farmers',    'fas fa-users',            'Farmers'],
      ['tab-products',   'fas fa-box',              'Products'],
      ['tab-reviews',    'fas fa-star',             'Reviews'],
      ['tab-orders',     'fas fa-shopping-bag',     'Orders'],
      ['tab-courses',    'fas fa-graduation-cap',   'Courses'],
      ['tab-innovations','fas fa-lightbulb',        'Innovations'],
      ['tab-sms',        'fas fa-sms',              'SMS Broadcast'],
      ['tab-settings',   'fas fa-cog',              'Settings'],
    ] as [$tab,$icon,$label])
      <a href="#" class="admin-nav-link {{ $tab==='tab-analytics'?'active':'' }}" onclick="switchAdminTab('{{ $tab }}',this); return false;">
        <i class="{{ $icon }}"></i> {{ $label }}
      </a>
    @endforeach
    <div class="admin-nav-label">Other</div>
    <a href="{{ route('home') }}"      class="admin-nav-link"><i class="fas fa-globe"></i> View Site</a>
    <a href="{{ route('dashboard') }}" class="admin-nav-link"><i class="fas fa-th-large"></i> Dashboard</a>
  </nav>
  <div style="padding:16px 20px;border-top:1px solid rgba(255,255,255,.08);">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
      <div class="avatar avatar-sm" style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;font-size:.7rem;">{{ Auth::user()->initials }}</div>
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

{{-- ══ MAIN ══ --}}
<main class="admin-main">

  {{-- Flash success message --}}
  @if(session('success'))
    <div style="background:var(--green-50);border:1.5px solid var(--green-200);border-radius:var(--radius-md);padding:12px 18px;margin-bottom:18px;color:var(--green-700);font-weight:600;display:flex;align-items:center;gap:8px;">
      <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
  @endif
  @if($errors->any())
    <div style="background:#fef2f2;border:1.5px solid #fecaca;border-radius:var(--radius-md);padding:12px 18px;margin-bottom:18px;color:#dc2626;">
      @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
  @endif

  {{-- Mobile sidebar toggle --}}
  <button onclick="document.getElementById('adminSidebar').classList.toggle('open')" style="display:none;align-items:center;gap:8px;padding:8px 14px;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);font-size:.85rem;font-weight:600;color:var(--text);margin-bottom:12px;cursor:pointer;" id="adminSidebarToggle">
    <i class="fas fa-bars"></i> Menu
  </button>
  <style>@media(max-width:768px){#adminSidebarToggle{display:inline-flex !important;}}</style>

  {{-- Tab buttons --}}
  <div class="admin-tabs">
    @foreach([
      ['tab-analytics',  '📊 Analytics'],
      ['tab-farmers',    '👥 Farmers'],
      ['tab-products',   '📦 Products'],
      ['tab-reviews',    '⭐ Reviews'],
      ['tab-orders',     '🛒 Orders'],
      ['tab-courses',    '🎓 Courses'],
      ['tab-innovations','💡 Innovations'],
      ['tab-sms',        '📱 SMS'],
      ['tab-settings',   '⚙️ Settings'],
    ] as [$tab,$label])
      <button class="admin-tab {{ $tab==='tab-analytics'?'active':'' }}" onclick="switchAdminTab('{{ $tab }}',null)">{{ $label }}</button>
    @endforeach
  </div>

  {{-- ══════════════════════════════════════════════════════
       ANALYTICS TAB
  ══════════════════════════════════════════════════════ --}}
  <div class="admin-panel active" id="tab-analytics">
    <div class="admin-stats-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
      @php
        $adminStats=[
          ['fas fa-users',       'var(--green-100)', 'var(--green-700)', $stats['total_farmers']??0,                   'Total Farmers'],
          ['fas fa-shopping-bag','#fff7ed',           '#c2410c',          $stats['total_products_sold']??0,             'Products Sold'],
          ['fas fa-coins',       '#e0f2fe',           'var(--sky-600)',   'K '.number_format($stats['total_revenue']??0),'Total Revenue'],
          ['fas fa-truck',       '#f0fdf4',           'var(--green-700)', ($stats['delivery_success_rate']??0).'%',     'Delivery Rate'],
        ];
      @endphp
      @foreach($adminStats as [$icon,$bg,$color,$val,$label])
        <div class="admin-stat-card">
          <div style="width:44px;height:44px;border-radius:12px;background:{{ $bg }};color:{{ $color }};display:flex;align-items:center;justify-content:center;margin-bottom:12px;font-size:1.1rem;"><i class="{{ $icon }}"></i></div>
          <div class="admin-stat-val">{{ is_numeric($val)&&!str_contains((string)$val,'%')&&!str_contains((string)$val,'K') ? number_format($val) : $val }}</div>
          <div class="admin-stat-label">{{ $label }}</div>
        </div>
      @endforeach
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:22px;">
        <div style="font-family:var(--font-display);font-size:.95rem;font-weight:700;color:var(--text);margin-bottom:18px;">📍 Farmers by District</div>
        @php $maxDistrict=isset($districtBreakdown)&&$districtBreakdown->count()>0?$districtBreakdown->max('total'):1; @endphp
        @forelse(isset($districtBreakdown)?$districtBreakdown->take(8):[] as $row)
          <div class="district-bar">
            <div style="width:100px;font-size:.78rem;color:var(--text-muted);flex-shrink:0;">{{ $row->district }}</div>
            <div style="flex:1;background:var(--bg-2);border-radius:20px;height:8px;overflow:hidden;"><div class="district-bar-fill" style="width:{{ ($row->total/$maxDistrict)*100 }}%;"></div></div>
            <div style="width:40px;text-align:right;font-size:.78rem;font-weight:700;">{{ $row->total }}</div>
          </div>
        @empty
          <div style="color:var(--text-muted);font-size:.85rem;text-align:center;padding:20px;">No data yet</div>
        @endforelse
      </div>
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:22px;">
        <div style="font-family:var(--font-display);font-size:.95rem;font-weight:700;color:var(--text);margin-bottom:18px;">📋 Recent Activity</div>
        <div style="overflow-y:auto;max-height:300px;">
          @forelse(isset($recentActivity)?$recentActivity:[] as $activity)
            <div style="display:flex;gap:12px;padding:10px 0;border-bottom:1px solid var(--border);">
              <div style="width:8px;height:8px;border-radius:50%;background:var(--primary);flex-shrink:0;margin-top:5px;"></div>
              <div style="flex:1;">
                <div style="font-size:.82rem;color:var(--text);">{{ $activity['event'] }}</div>
                <div style="font-size:.74rem;color:var(--text-muted);">{{ $activity['user'] }} · {{ \Carbon\Carbon::parse($activity['time'])->diffForHumans() }}</div>
              </div>
              <span class="badge badge-green" style="font-size:.62rem;flex-shrink:0;">{{ $activity['module'] }}</span>
            </div>
          @empty
            <div style="color:var(--text-muted);font-size:.85rem;text-align:center;padding:20px;">No recent activity</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>

  {{-- ══════════════════════════════════════════════════════
       FARMERS TAB
  ══════════════════════════════════════════════════════ --}}
  <div class="admin-panel" id="tab-farmers">
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;">
      <div style="padding:18px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div style="font-family:var(--font-display);font-size:.95rem;font-weight:700;">Registered Farmers</div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
          <form method="GET" action="{{ route('admin.farmers') }}" style="display:flex;gap:6px;flex-wrap:wrap;">
            <input type="text" name="q" value="{{ request('q') }}" class="form-input" placeholder="Search name, phone..." style="font-size:.8rem;padding:7px 12px;"/>
            <select name="district" class="form-input form-select" style="font-size:.8rem;padding:7px 12px;">
              <option value="">All Districts</option>
              @foreach($districts as $district)
                <option value="{{ $district->name }}" @selected(request('district')===$district->name)>{{ $district->name }}</option>
              @endforeach
            </select>
            <select name="status" class="form-input form-select" style="font-size:.8rem;padding:7px 12px;">
              <option value="">All Status</option>
              <option value="active"    @selected(request('status')==='active')>Active</option>
              <option value="suspended" @selected(request('status')==='suspended')>Suspended</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
          </form>
          <button onclick="showToast('📊 CSV export coming soon','info')" class="btn btn-outline btn-sm"><i class="fas fa-download"></i> Export CSV</button>
        </div>
      </div>
      <div style="overflow-x:auto;">
        @php
          $farmers = \App\Models\User::farmers()
            ->withCount(['orders','enrollments'])
            ->when(request('q'),       fn($q,$t) => $q->where(fn($w) => $w->where('first_name','like',"%$t%")->orWhere('last_name','like',"%$t%")->orWhere('phone','like',"%$t%")))
            ->when(request('district'),fn($q,$p) => $q->where('district',$p))
            ->when(request('status'),  fn($q,$s) => $q->where('status',$s))
            ->latest()->paginate(12);
        @endphp
        <table class="data-table">
          <thead><tr><th>Farmer</th><th>Phone</th><th>Province</th><th>Farm Type</th><th>Orders</th><th>Courses</th><th>Joined</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody>
            @forelse($farmers as $farmer)
              <tr>
                <td>
                  <div style="display:flex;align-items:center;gap:8px;">
                    <div class="avatar avatar-sm" style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;font-size:.65rem;flex-shrink:0;">{{ $farmer->initials }}</div>
                    <div><div style="font-weight:600;">{{ $farmer->full_name }}</div><div style="font-size:.73rem;color:var(--text-muted);">{{ $farmer->email }}</div></div>
                  </div>
                </td>
                <td style="font-family:var(--font-mono);font-size:.78rem;">{{ $farmer->phone??'—' }}</td>
                <td>{{ $farmer->district??'—' }}</td>
                <td>{{ ucwords(str_replace('_',' ',$farmer->farm?->farm_type??'—')) }}</td>
                <td style="text-align:center;"><span class="badge badge-gray">{{ $farmer->orders_count }}</span></td>
                <td style="text-align:center;"><span class="badge badge-sky">{{ $farmer->enrollments_count }}</span></td>
                <td style="font-size:.78rem;color:var(--text-muted);">{{ $farmer->created_at->format('M j, Y') }}</td>
                <td><span class="badge {{ $farmer->status==='active'?'badge-green':'badge-coral' }}" style="font-size:.68rem;">{{ ucfirst($farmer->status) }}</span></td>
                <td>
                  @if($farmer->status==='active')
                    <form method="POST" action="{{ route('admin.farmers.suspend',$farmer) }}" style="display:inline;">
                      @csrf
                      <button type="submit" onclick="return confirm('Suspend {{ $farmer->first_name }}?')" class="btn btn-sm" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;font-size:.72rem;padding:4px 10px;">Suspend</button>
                    </form>
                  @else
                    <form method="POST" action="{{ route('admin.farmers.activate',$farmer) }}" style="display:inline;">
                      @csrf
                      <button type="submit" class="btn btn-primary btn-sm" style="font-size:.72rem;padding:4px 10px;">Activate</button>
                    </form>
                  @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="9" style="text-align:center;padding:40px;color:var(--text-muted);">No farmers found</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if($farmers->hasPages())
        <div style="padding:14px 20px;border-top:1px solid var(--border);">{{ $farmers->withQueryString()->links() }}</div>
      @endif
    </div>
  </div>

  {{-- ══════════════════════════════════════════════════════
       PRODUCTS TAB
  ══════════════════════════════════════════════════════ --}}
  <div class="admin-panel" id="tab-products">
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;">
      <div style="padding:18px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div style="display:flex;align-items:center;gap:10px;">
          <div style="font-family:var(--font-display);font-size:.95rem;font-weight:700;">Product Listings</div>
          @php $pendingCount = \App\Models\Product::where('status','pending_review')->count(); @endphp
          @if($pendingCount > 0)
            <span class="badge badge-earth" style="font-size:.72rem;animation:pulse 2s infinite;">{{ $pendingCount }} Pending Review</span>
          @endif
        </div>
        <form method="GET" action="{{ route('admin.products') }}" style="display:flex;gap:6px;">
          <select name="status" class="form-input form-select" style="font-size:.8rem;padding:7px 12px;" onchange="this.form.submit()">
            <option value="">All Status</option>
            @foreach(['pending_review'=>'Pending Review','active'=>'Active','inactive'=>'Inactive','sold_out'=>'Sold Out'] as $v=>$l)
              <option value="{{ $v }}" @selected(request('status')===$v)>{{ $l }}</option>
            @endforeach
          </select>
        </form>
        <a href="{{ route('admin.products', ['status'=>'pending_review']) }}" class="btn btn-primary btn-sm">
          <i class="fas fa-search"></i> View Pending ({{ \App\Models\Product::where('status','pending_review')->count() }})
        </a>
      </div>
      <div style="overflow-x:auto;">
        @php
          $products = \App\Models\Product::with('seller')
            ->when(request('status'), fn($q,$s) => $q->where('status',$s))
            ->latest()->paginate(12);
        @endphp
        <table class="data-table">
          <thead><tr><th>Product</th><th>Seller</th><th>Category</th><th>Price</th><th>Stock</th><th>Sold</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody>
            @forelse($products as $product)
              <tr>
                <td style="font-weight:600;max-width:200px;">{{ Str::limit($product->name,40) }}</td>
                <td style="font-size:.8rem;">{{ $product->seller->full_name??'—' }}<br><span style="color:var(--text-muted);">{{ $product->district }}</span></td>
                <td><span class="badge badge-gray" style="font-size:.68rem;">{{ ucfirst($product->category) }}</span></td>
                <td style="font-weight:700;color:var(--primary);">{{ $product->currency }} {{ number_format($product->price) }}/{{ $product->unit }}</td>
                <td style="text-align:center;">{{ $product->stock_quantity }}</td>
                <td style="text-align:center;">{{ $product->total_sold }}</td>
                <td><span class="badge {{ $product->status==='active'?'badge-green':($product->status==='pending_review'?'badge-earth':'badge-gray') }}" style="font-size:.68rem;">{{ ucwords(str_replace('_',' ',$product->status)) }}</span></td>
                <td>
                  <div style="display:flex;gap:5px;flex-wrap:wrap;">
                    @if($product->status==='pending_review')
                      <form method="POST" action="{{ route('admin.products.approve',$product) }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm" style="font-size:.72rem;padding:4px 10px;">Approve</button>
                      </form>
                      <form method="POST" action="{{ route('admin.products.reject',$product) }}" style="display:inline;" onsubmit="return setProductRejectReason(this)">
                        @csrf
                        <input type="hidden" name="reason" class="product-reject-reason-input" value="Does not meet listing guidelines"/>
                        <button type="submit" class="btn btn-sm" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;font-size:.72rem;padding:4px 10px;">Reject</button>
                      </form>
                    @elseif($product->status==='active')
                      <span class="badge badge-green" style="font-size:.68rem;">Live</span>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">No products</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if($products->hasPages())<div style="padding:14px 20px;border-top:1px solid var(--border);">{{ $products->withQueryString()->links() }}</div>@endif
    </div>
  </div>

  {{-- ══════════════════════════════════════════════════════
       REVIEWS TAB (NEW — moderation queue for course reviews)
  ══════════════════════════════════════════════════════ --}}
  <div class="admin-panel" id="tab-reviews">
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;">
      <div style="padding:18px 20px;border-bottom:1px solid var(--border);">
        <div style="font-family:var(--font-display);font-size:.95rem;font-weight:700;">⭐ Pending Course Reviews</div>
        <div style="font-size:.78rem;color:var(--text-muted);margin-top:2px;">Reviews appear on course pages only after you approve them here.</div>
      </div>
      <div style="overflow-x:auto;">
        <table class="data-table">
          <thead><tr><th>Course</th><th>Reviewer</th><th>Rating</th><th>Review Text</th><th>Submitted</th><th>Actions</th></tr></thead>
          <tbody>
            @forelse(\App\Models\CourseReview::with(['user','course'])->where('is_approved',false)->latest()->paginate(10) as $review)
              <tr>
                <td style="font-weight:600;font-size:.82rem;max-width:160px;">{{ Str::limit($review->course->title??'—',30) }}</td>
                <td style="font-size:.8rem;">{{ $review->user->full_name??'—' }}</td>
                <td><span style="color:#f59e0b;">{{ str_repeat('★',$review->rating) }}{{ str_repeat('☆',5-$review->rating) }}</span></td>
                <td style="font-size:.8rem;color:var(--text-muted);max-width:240px;">{{ Str::limit($review->review??'—',80) }}</td>
                <td style="font-size:.78rem;color:var(--text-muted);">{{ $review->created_at->diffForHumans() }}</td>
                <td>
                  <div style="display:flex;gap:5px;">
                    <form method="POST" action="{{ route('admin.reviews.approve',$review) }}" style="display:inline;">
                      @csrf
                      <button type="submit" class="btn btn-primary btn-sm" style="font-size:.72rem;padding:4px 10px;">Approve</button>
                    </form>
                    <form method="POST" action="{{ route('admin.reviews.reject',$review) }}" style="display:inline;" onsubmit="return confirm('Reject and permanently delete this review?')">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn btn-sm" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;font-size:.72rem;padding:4px 10px;">Reject</button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">
                <i class="fas fa-check-circle" style="font-size:2rem;color:var(--green-600);margin-bottom:10px;display:block;"></i>
                No pending reviews — you're all caught up!
              </td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- ══════════════════════════════════════════════════════
       ORDERS TAB
  ══════════════════════════════════════════════════════ --}}
  <div class="admin-panel" id="tab-orders">
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;">
      <div style="padding:18px 20px;border-bottom:1px solid var(--border);">
        <div style="font-family:var(--font-display);font-size:.95rem;font-weight:700;">All Orders</div>
      </div>
      <div style="overflow-x:auto;">
        @php
          $orders = \App\Models\Order::with(['buyer','seller'])
            ->when(request('order_status'), fn($q,$s) => $q->where('status',$s))
            ->latest()->paginate(12);
        @endphp
        <table class="data-table">
          <thead><tr><th>Order #</th><th>Buyer</th><th>Seller</th><th>Total</th><th>Payment</th><th>Status</th><th>Date</th><th>Update Status</th></tr></thead>
          <tbody>
            @forelse($orders as $order)
              @php $sc=['pending'=>'badge-gray','confirmed'=>'badge-sky','processing'=>'badge-earth','dispatched'=>'badge-sky','in_transit'=>'badge-earth','delivered'=>'badge-green','cancelled'=>'badge-coral','refunded'=>'badge-gray']; @endphp
              <tr>
                <td style="font-family:var(--font-mono);font-size:.78rem;font-weight:700;color:var(--primary);">{{ $order->order_number }}</td>
                <td style="font-size:.8rem;">{{ $order->buyer->full_name??'—' }}</td>
                <td style="font-size:.8rem;">{{ $order->seller->full_name??'—' }}</td>
                <td style="font-weight:700;">{{ $order->currency }} {{ number_format($order->total) }}</td>
                <td><span class="badge {{ $order->payment_status==='paid'?'badge-green':'badge-gray' }}" style="font-size:.67rem;">{{ ucfirst($order->payment_status) }}</span></td>
                <td><span class="badge {{ $sc[$order->status]??'badge-gray' }}" style="font-size:.67rem;">{{ ucfirst($order->status) }}</span></td>
                <td style="font-size:.78rem;color:var(--text-muted);">{{ $order->created_at->format('M j, Y') }}</td>
                <td>
                  <form method="POST" action="{{ route('admin.orders.status',$order) }}" style="display:flex;gap:6px;">
                    @csrf
                    <select name="status" class="form-input form-select" style="font-size:.76rem;padding:5px 8px;">
                      @foreach(['confirmed','processing','dispatched','delivered','cancelled'] as $s)
                        <option value="{{ $s }}" @selected($order->status===$s)>{{ ucfirst($s) }}</option>
                      @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm" style="font-size:.72rem;padding:4px 10px;flex-shrink:0;">Update</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">No orders yet</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if($orders->hasPages())<div style="padding:14px 20px;border-top:1px solid var(--border);">{{ $orders->links() }}</div>@endif
    </div>
  </div>

  {{-- ══════════════════════════════════════════════════════
       COURSES TAB — with Lesson Manager
  ══════════════════════════════════════════════════════ --}}
  <div class="admin-panel" id="tab-courses">
    <div style="display:grid;grid-template-columns:1fr 1.4fr;gap:20px;">
      {{-- Create Course form --}}
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:24px;">
        <div style="font-family:var(--font-display);font-size:.95rem;font-weight:700;margin-bottom:18px;">+ Create New Course</div>
        <form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data">
          @csrf
          <div class="form-group"><label class="form-label">Course Title *</label><input type="text" name="title" class="form-input" placeholder="e.g. Modern Maize Farming" required/></div>
          <div class="form-group"><label class="form-label">Category *</label>
            <select name="category" class="form-input form-select" required>
              @foreach(['soil_crops'=>'Soil & Crops','livestock'=>'Livestock','agri_tech'=>'Agri-Tech','agribusiness'=>'Agribusiness','organic'=>'Organic','irrigation'=>'Irrigation','post_harvest'=>'Post-Harvest'] as $v=>$l)
                <option value="{{ $v }}">{{ $l }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Instructor Name *</label>
            <input type="text" name="instructor_name" class="form-input" placeholder="e.g. Dr. James Mwale" required/>
            <div style="font-size:.73rem;color:var(--text-muted);margin-top:4px;">Created automatically if not yet in the system.</div>
          </div>
          <div class="form-group"><label class="form-label">Price (MWK) — 0 = Free</label><input type="number" name="price" class="form-input" placeholder="0" min="0" value="0"/></div>
          <div class="form-group"><label class="form-label">Description *</label><textarea name="description" class="form-input" rows="3" required placeholder="What will farmers learn?"></textarea></div>
          <div class="form-group"><label class="form-label">Thumbnail</label><input type="file" name="thumbnail" class="form-input" accept="image/*" style="padding:8px;"/></div>
          <button type="submit" class="btn btn-primary btn-md" style="width:100%;justify-content:center;"><i class="fas fa-plus"></i> Create Course (Draft)</button>
        </form>
      </div>

      {{-- Course list + lesson manager --}}
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;">
        <div style="padding:16px 20px;border-bottom:1px solid var(--border);font-family:var(--font-display);font-size:.9rem;font-weight:700;">All Courses</div>
        <div style="overflow-x:auto;">
          @php $allCourses = \App\Models\Course::with(['instructor','lessons'])->latest()->paginate(8); @endphp
          <table class="data-table">
            <thead><tr><th>Title</th><th>Lessons</th><th>Enrolled</th><th>Rating</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
              @forelse($allCourses as $c)
                <tr>
                  <td style="max-width:160px;">
                    <div style="font-weight:600;font-size:.82rem;">{{ Str::limit($c->title,30) }}</div>
                    <div style="font-size:.72rem;color:var(--text-muted);">{{ $c->instructor->name??'—' }}</div>
                  </td>
                  <td style="text-align:center;">
                    <span class="badge {{ $c->lessons->count()>0?'badge-green':'badge-coral' }}" style="font-size:.68rem;">{{ $c->lessons->count() }} lesson{{ $c->lessons->count()===1?'':'s' }}</span>
                  </td>
                  <td style="text-align:center;font-weight:600;">{{ number_format($c->total_enrolled) }}</td>
                  <td style="text-align:center;">{{ $c->average_rating ? '⭐ '.number_format($c->average_rating,1) : '—' }}</td>
                  <td><span class="badge {{ $c->status==='published'?'badge-green':($c->status==='draft'?'badge-gray':'badge-earth') }}" style="font-size:.67rem;">{{ ucfirst($c->status) }}</span></td>
                  <td>
                    <div style="display:flex;gap:5px;flex-wrap:wrap;">
                      <button type="button" onclick="toggleLessonManager({{ $c->id }})" class="btn btn-outline btn-sm" style="font-size:.7rem;padding:4px 8px;">
                        <i class="fas fa-list"></i> Lessons
                      </button>
                      @if($c->status==='draft')
                        <form method="POST" action="{{ route('admin.courses.publish',$c) }}" style="display:inline;">
                          @csrf
                          <button type="submit" class="btn btn-primary btn-sm" style="font-size:.72rem;padding:4px 10px;"
                            @if($c->lessons->count()===0) onclick="alert('Add at least one lesson before publishing.'); return false;" @endif>
                            Publish
                          </button>
                        </form>
                      @else
                        <span style="font-size:.75rem;color:var(--text-muted);align-self:center;">Live</span>
                      @endif
                    </div>
                  </td>
                </tr>
                {{-- Lesson Manager (hidden by default, toggled by JS) --}}
                <tr id="lesson-manager-{{ $c->id }}" style="display:none;">
                  <td colspan="6" style="background:var(--bg-2);padding:18px 20px;">
                    <div style="font-weight:700;font-size:.82rem;color:var(--text);margin-bottom:12px;">📚 Lessons — {{ $c->title }}</div>
                    @if($c->lessons->count() > 0)
                      <div style="margin-bottom:14px;">
                        @foreach($c->lessons->sortBy('sort_order') as $lesson)
                          <div style="display:flex;align-items:center;gap:10px;padding:8px 12px;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);margin-bottom:6px;">
                            <span style="width:24px;height:24px;border-radius:50%;background:var(--bg-2);display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;color:var(--text-muted);flex-shrink:0;">{{ $lesson->sort_order }}</span>
                            <i class="fas {{ $lesson->type==='video'?'fa-play-circle':($lesson->type==='pdf'?'fa-file-pdf':'fa-file-alt') }}" style="color:var(--primary);width:16px;"></i>
                            <span style="flex:1;font-size:.82rem;font-weight:500;">{{ $lesson->title }}</span>
                            <span style="font-size:.74rem;color:var(--text-muted);">{{ $lesson->duration_minutes }}m</span>
                            @if($lesson->is_free_preview)<span class="badge badge-green" style="font-size:.62rem;">Preview</span>@endif
                            <span class="badge {{ $lesson->is_published?'badge-green':'badge-gray' }}" style="font-size:.62rem;">{{ $lesson->is_published?'Live':'Hidden' }}</span>
                            <form method="POST" action="{{ route('admin.lessons.destroy',$lesson) }}" style="display:inline;" onsubmit="return confirm('Delete this lesson?')">
                              @csrf @method('DELETE')
                              <button type="submit" style="background:none;border:none;color:#ef4444;cursor:pointer;font-size:.85rem;"><i class="fas fa-trash"></i></button>
                            </form>
                          </div>
                        @endforeach
                      </div>
                    @else
                      <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:var(--radius-md);padding:10px 14px;margin-bottom:14px;font-size:.8rem;color:#dc2626;">
                        <i class="fas fa-exclamation-circle"></i> No lessons yet — add one below before publishing.
                      </div>
                    @endif
                    <form method="POST" action="{{ route('admin.courses.lessons.store',$c) }}" enctype="multipart/form-data" style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);padding:14px;">
                      @csrf
                      <div style="font-size:.78rem;font-weight:700;color:var(--text-muted);margin-bottom:10px;">+ Add New Lesson</div>
                      <div style="display:grid;grid-template-columns:2fr 1fr 1fr;gap:10px;margin-bottom:10px;">
                        <input type="text" name="title" class="form-input" placeholder="Lesson title" style="font-size:.82rem;" required/>
                        <select name="type" class="form-input form-select" style="font-size:.82rem;" onchange="toggleLessonTypeFields(this)" required>
                          <option value="video">Video</option>
                          <option value="pdf">PDF</option>
                          <option value="text">Text</option>
                          <option value="quiz">Quiz</option>
                        </select>
                        <input type="number" name="duration_minutes" class="form-input" placeholder="Minutes" style="font-size:.82rem;" min="1" required/>
                      </div>
                      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                        <input type="url" name="video_url" class="form-input lesson-video-field" placeholder="YouTube / Vimeo URL" style="font-size:.82rem;"/>
                        <input type="file" name="pdf_file" accept="application/pdf" class="form-input lesson-pdf-field" style="font-size:.78rem;padding:6px;display:none;"/>
                      </div>
                      <div style="display:flex;align-items:center;gap:16px;margin-bottom:10px;">
                        <label style="display:flex;align-items:center;gap:6px;font-size:.78rem;cursor:pointer;"><input type="checkbox" name="is_free_preview" value="1" style="accent-color:var(--primary);width:auto;"/> Free preview</label>
                        <label style="display:flex;align-items:center;gap:6px;font-size:.78rem;cursor:pointer;"><input type="checkbox" name="is_published" value="1" checked style="accent-color:var(--primary);width:auto;"/> Published immediately</label>
                      </div>
                      <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Lesson</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr><td colspan="6" style="text-align:center;padding:30px;color:var(--text-muted);">No courses yet — create one on the left.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($allCourses->hasPages())<div style="padding:14px 20px;border-top:1px solid var(--border);">{{ $allCourses->links() }}</div>@endif
      </div>
    </div>
  </div>

  {{-- PDF FIELD GUIDES UPLOAD -- inside Courses tab --}}
  <div style="margin-top:28px;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:24px;">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
      <div style="width:44px;height:44px;border-radius:12px;background:#fef2f2;color:#dc2626;display:flex;align-items:center;justify-content:center;font-size:1.2rem;"><i class="fas fa-file-pdf"></i></div>
      <div>
        <div style="font-family:var(--font-display);font-size:.95rem;font-weight:700;">Upload PDF Field Guides</div>
        <div style="font-size:.78rem;color:var(--text-muted);">Farmers see these on the Learning Center page, filtered by topic</div>
      </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1.2fr;gap:20px;">
      <div>
        <div style="font-weight:700;font-size:.85rem;margin-bottom:14px;">+ Upload New Guide</div>
        <form method="POST" action="{{ route('admin.guides.store') }}" enctype="multipart/form-data">
          @csrf
          <div class="form-group"><label class="form-label">Guide Title *</label><input type="text" name="title" class="form-input" placeholder="e.g. Fall Armyworm Control Guide 2026" required maxlength="150"/></div>
          <div class="form-group"><label class="form-label">Topic *</label>
            <select name="topic" class="form-input form-select" required>
              <option value="general">📄 General Farming</option>
              <option value="soil_crops">🌽 Soil & Crops</option>
              <option value="livestock">🐄 Livestock</option>
              <option value="agri_tech">🚁 Agri-Tech</option>
              <option value="agribusiness">📊 Agribusiness</option>
              <option value="organic">🥦 Organic Farming</option>
              <option value="irrigation">💧 Irrigation</option>
              <option value="post_harvest">🌾 Post-Harvest</option>
              <option value="disease_control">🔬 Disease Control</option>
            </select>
          </div>
          <div class="form-group"><label class="form-label">Description</label><textarea name="description" class="form-input" rows="2" maxlength="500" placeholder="What this guide covers and who it's for..."></textarea></div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <div class="form-group"><label class="form-label">Pages</label><input type="number" name="page_count" class="form-input" placeholder="e.g. 28" min="1"/></div>
            <div class="form-group"><label class="form-label">Link to Course</label>
              <select name="course_id" class="form-input form-select">
                <option value="">Not linked</option>
                @foreach(\App\Models\Course::orderBy('title')->get() as $lc)
                  <option value="{{ $lc->id }}">{{ Str::limit($lc->title,30) }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group"><label class="form-label">PDF File * <span style="color:var(--text-muted);font-weight:400;">(max 20MB)</span></label><input type="file" name="file" class="form-input" accept="application/pdf" required style="padding:8px;"/></div>
          <button type="submit" class="btn btn-primary btn-md" style="width:100%;justify-content:center;"><i class="fas fa-upload"></i> Upload — Goes Live Immediately</button>
        </form>
      </div>
      <div>
        <div style="font-weight:700;font-size:.85rem;margin-bottom:12px;">All Guides ({{ \App\Models\CourseGuide::count() }})</div>
        <div style="overflow-y:auto;max-height:440px;">
          <table class="data-table">
            <thead><tr><th>Title</th><th>Topic</th><th>Size</th><th>DL</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
              @forelse(\App\Models\CourseGuide::latest()->get() as $guide)
                <tr>
                  <td style="font-weight:600;font-size:.8rem;max-width:160px;">{{ Str::limit($guide->title,30) }}</td>
                  <td><span class="badge badge-gray" style="font-size:.65rem;">{{ $guide->topic_emoji }}</span></td>
                  <td style="font-size:.76rem;">{{ $guide->file_size_formatted }}</td>
                  <td style="text-align:center;font-weight:700;color:var(--primary);font-size:.82rem;">{{ $guide->download_count }}</td>
                  <td><span class="badge {{ $guide->is_published?'badge-green':'badge-gray' }}" style="font-size:.65rem;">{{ $guide->is_published?'Live':'Hidden' }}</span></td>
                  <td>
                    <div style="display:flex;gap:4px;">
                      <form method="POST" action="{{ route('admin.guides.toggle',$guide) }}" style="display:inline;">@csrf<button type="submit" class="btn btn-outline btn-sm" style="font-size:.68rem;padding:3px 7px;">{{ $guide->is_published?'Hide':'Show' }}</button></form>
                      <a href="{{ route('guides.download',$guide) }}" class="btn btn-outline btn-sm" style="font-size:.68rem;padding:3px 7px;"><i class="fas fa-eye"></i></a>
                      <form method="POST" action="{{ route('admin.guides.destroy',$guide) }}" style="display:inline;" onsubmit="return confirm('Delete this guide permanently?')">@csrf @method('DELETE')<button type="submit" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;border-radius:var(--radius-md);font-size:.68rem;padding:3px 7px;cursor:pointer;"><i class="fas fa-trash"></i></button></form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr><td colspan="6" style="text-align:center;padding:24px;color:var(--text-muted);">No guides yet — upload your first one!</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- ══════════════════════════════════════════════════════
       INNOVATIONS TAB
  ══════════════════════════════════════════════════════ --}}
  <div class="admin-panel" id="tab-innovations">
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;">
      <div style="padding:18px 20px;border-bottom:1px solid var(--border);font-family:var(--font-display);font-size:.95rem;font-weight:700;">Innovation Submissions</div>
      <div style="overflow-x:auto;">
        @php $allInnovations = \App\Models\Innovation::with('user')->latest()->paginate(10); @endphp
        <table class="data-table">
          <thead><tr><th>Title</th><th>Farmer</th><th>Category</th><th>Votes</th><th>Status</th><th>Submitted</th><th>Actions</th></tr></thead>
          <tbody>
            @forelse($allInnovations as $innov)
              <tr>
                <td style="max-width:200px;font-weight:600;font-size:.82rem;">{{ Str::limit($innov->title,40) }}</td>
                <td style="font-size:.8rem;">{{ $innov->user->full_name??'—' }}<br><span style="color:var(--text-muted);font-size:.73rem;">{{ $innov->district??'' }}</span></td>
                <td><span class="badge badge-gray" style="font-size:.67rem;">{{ ucwords(str_replace('_',' ',$innov->category)) }}</span></td>
                <td style="text-align:center;font-weight:700;color:var(--primary);">{{ $innov->vote_count }}</td>
                <td><span class="badge {{ in_array($innov->status,['approved','featured'])?'badge-green':($innov->status==='pending_review'?'badge-earth':'badge-coral') }}" style="font-size:.67rem;">{{ ucfirst($innov->status) }}</span></td>
                <td style="font-size:.78rem;color:var(--text-muted);">{{ $innov->created_at->format('M j, Y') }}</td>
                <td>
                  @if($innov->status==='pending_review')
                    <div style="display:flex;gap:5px;flex-wrap:wrap;">
                      <form method="POST" action="{{ route('admin.innovations.approve',$innov) }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm" style="font-size:.72rem;padding:4px 10px;">Approve</button>
                      </form>
                      <form method="POST" action="{{ route('admin.innovations.reject',$innov) }}" style="display:inline;" onsubmit="return setRejectReason(this)">
                        @csrf
                        <input type="hidden" name="reason" class="reject-reason-input" value="Does not meet submission guidelines"/>
                        <button type="submit" class="btn btn-sm" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;font-size:.72rem;padding:4px 10px;">Reject</button>
                      </form>
                    </div>
                  @else
                    <span style="font-size:.75rem;color:var(--text-muted);">{{ ucfirst($innov->status) }}</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">No innovations submitted yet</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if($allInnovations->hasPages())<div style="padding:14px 20px;border-top:1px solid var(--border);">{{ $allInnovations->links() }}</div>@endif
    </div>
  </div>

  {{-- ══════════════════════════════════════════════════════
       SMS TAB
  ══════════════════════════════════════════════════════ --}}
  <div class="admin-panel" id="tab-sms">
    <div style="display:grid;grid-template-columns:1fr 1.2fr;gap:20px;margin-bottom:20px;">
      <div style="display:flex;flex-direction:column;gap:14px;">
        @php
          $smsSentMonth = \App\Models\SmsLog::whereMonth('created_at',now()->month)->count();
          $smsDelivered = \App\Models\SmsLog::where('status','delivered')->whereMonth('created_at',now()->month)->count();
          $smsRate      = $smsSentMonth > 0 ? round(($smsDelivered/$smsSentMonth)*100,1) : 0;
          $smsCost      = \App\Models\SmsLog::whereMonth('created_at',now()->month)->sum('cost');
        @endphp
        @foreach([
          ['fas fa-paper-plane','var(--green-100)','var(--green-700)', $smsSentMonth,                   'SMS Sent This Month'],
          ['fas fa-check-double','#e0f2fe',         'var(--sky-600)',   $smsRate.'%',                    'Delivery Rate'],
          ['fas fa-dollar-sign', '#fff7ed',          '#c2410c',         '$'.number_format($smsCost,2),   'Total Cost (USD)'],
        ] as [$icon,$bg,$color,$val,$label])
          <div class="admin-stat-card" style="display:flex;align-items:center;gap:14px;padding:16px;">
            <div style="width:40px;height:40px;border-radius:10px;background:{{ $bg }};color:{{ $color }};display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="{{ $icon }}"></i></div>
            <div><div style="font-family:var(--font-display);font-size:1.3rem;font-weight:800;color:var(--text);">{{ $val }}</div><div style="font-size:.75rem;color:var(--text-muted);">{{ $label }}</div></div>
          </div>
        @endforeach
        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:18px;">
          <div style="font-size:.82rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px;">Quick Templates</div>
          @foreach([
            ['Disease Alert','⚠️ DISEASE ALERT: {disease} detected in {district}. Action: {action}. agritechpro.mw/diseases'],
            ['New Course',   '🌱 New free course: "{title}" now available at agritechpro.mw/learn'],
            ['Weather',      '🌧️ Weather Warning: Heavy rain expected in {district}. Protect your crops.'],
            ['Market Price', '💰 Maize price K{price}/50kg in {district} today. agritechpro.mw/marketplace'],
          ] as [$name,$tpl])
            <div class="sms-template" onclick="useTemplate(this)">
              <div class="sms-template-title">{{ $name }}</div>
              <div class="sms-template-preview" data-template="{{ $tpl }}">{{ $tpl }}</div>
            </div>
          @endforeach
        </div>
      </div>
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:24px;">
        <div style="font-family:var(--font-display);font-size:.95rem;font-weight:700;margin-bottom:18px;">📤 Send SMS Broadcast</div>
        <form method="POST" action="{{ route('admin.sms.broadcast') }}">
          @csrf
          <div class="form-group"><label class="form-label">Campaign Name *</label><input type="text" name="name" class="form-input" placeholder="e.g. Fall Armyworm Alert - July 2026" required/></div>
          <div class="form-group">
            <label class="form-label">Message * <span id="smsCharCount" style="float:right;font-size:.75rem;color:var(--text-muted);">0/480</span></label>
            <textarea name="message" id="smsMessage" class="form-input" rows="4" maxlength="480" placeholder="Type or click a template above..." required style="resize:vertical;"></textarea>
          </div>
          <div class="form-group"><label class="form-label">Type</label>
            <select name="type" class="form-input form-select">
              @foreach(['disease_alert'=>'Disease Alert','lesson_alert'=>'New Course','weather'=>'Weather Warning','market_price'=>'Market Price','farming_tip'=>'Farming Tip','custom'=>'Custom'] as $v=>$l)
                <option value="{{ $v }}">{{ $l }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Target Districts <span style="color:var(--text-muted);font-weight:400;">(empty = all)</span></label>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:6px;background:var(--bg-2);border-radius:var(--radius-md);padding:12px;">
              @foreach($districts as $district)
                <label style="display:flex;align-items:center;gap:5px;font-size:.77rem;cursor:pointer;">
                  <input type="checkbox" name="target_districts[]" value="{{ $district->name }}" style="accent-color:var(--primary);width:auto;"/> {{ $district->name }}
                </label>
              @endforeach
            </div>
          </div>
          <div class="form-group"><label class="form-label">Schedule For <span style="color:var(--text-muted);font-weight:400;">(blank = send now)</span></label><input type="datetime-local" name="schedule_for" class="form-input"/></div>
          <div style="display:flex;gap:8px;">
            <button type="submit" class="btn btn-primary btn-md"><i class="fas fa-paper-plane"></i> Send Now</button>
            <button type="submit" name="scheduled" value="1" class="btn btn-outline btn-md"><i class="fas fa-clock"></i> Schedule</button>
          </div>
        </form>
      </div>
    </div>
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;">
      <div style="padding:16px 20px;border-bottom:1px solid var(--border);font-family:var(--font-display);font-size:.9rem;font-weight:700;">Recent Campaigns</div>
      @php $campaigns = \App\Models\SmsCampaign::with('creator')->latest()->limit(5)->get(); @endphp
      @forelse($campaigns as $c)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid var(--border);flex-wrap:wrap;gap:8px;">
          <div><div style="font-weight:600;font-size:.85rem;">{{ $c->name }}</div><div style="font-size:.75rem;color:var(--text-muted);">{{ $c->sent_at?->format('M j, Y H:i')??'Scheduled' }} · {{ $c->creator->full_name??'Admin' }}</div></div>
          <div style="display:flex;gap:16px;align-items:center;flex-wrap:wrap;">
            <div style="text-align:center;"><div style="font-family:var(--font-display);font-weight:800;">{{ number_format($c->total_sent) }}</div><div style="font-size:.72rem;color:var(--text-muted);">Sent</div></div>
            <div style="text-align:center;"><div style="font-family:var(--font-display);font-weight:800;color:var(--primary);">{{ number_format($c->total_delivered) }}</div><div style="font-size:.72rem;color:var(--text-muted);">Delivered</div></div>
            <span class="badge {{ $c->status==='sent'?'badge-green':($c->status==='scheduled'?'badge-sky':'badge-gray') }}" style="font-size:.68rem;">{{ ucfirst($c->status) }}</span>
          </div>
        </div>
      @empty
        <div style="text-align:center;padding:32px;color:var(--text-muted);">No campaigns sent yet</div>
      @endforelse
    </div>
  </div>

  {{-- ══════════════════════════════════════════════════════
       SETTINGS TAB
       FIX: array structure changed so [$title,$desc] destructuring
       works correctly. Key is now the outer array key, not nested.
  ══════════════════════════════════════════════════════ --}}
  <div class="admin-panel" id="tab-settings">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:24px;">
        <div style="font-family:var(--font-display);font-size:.95rem;font-weight:700;margin-bottom:18px;">⚙️ Platform Settings</div>
        <form method="POST" action="{{ route('admin.settings.update') }}">
          @csrf
          @php
            $settingItems = [
              'maintenance_mode'      => ['Maintenance Mode',        'Temporarily disable the platform for maintenance'],
              'allow_registrations'   => ['Allow New Registrations', 'Enable or disable new farmer sign-ups'],
              'sms_notifications'     => ['SMS Notifications',       'Enable SMS alerts for all farmers'],
              'disease_alerts_active' => ['Disease Alert System',    'Enable disease outbreak alerts and broadcasts'],
              'marketplace_escrow'    => ['Marketplace Escrow',      'Hold payments until delivery is confirmed'],
            ];
          @endphp
          @foreach($settingItems as $settingKey => [$title, $desc])
            <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 0;border-bottom:1px solid var(--border);">
              <div>
                <div style="font-weight:600;font-size:.88rem;color:var(--text);">{{ $title }}</div>
                <div style="font-size:.76rem;color:var(--text-muted);margin-top:2px;">{{ $desc }}</div>
              </div>
              <label class="toggle-switch">
                <input type="checkbox" name="{{ $settingKey }}" value="1" @checked($settings[$settingKey] ?? true)/>
                <span class="toggle-slider"></span>
              </label>
            </div>
          @endforeach
          <button type="submit" class="btn btn-primary btn-md" style="margin-top:20px;"><i class="fas fa-save"></i> Save Settings</button>
        </form>
      </div>
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:24px;">
        <div style="font-family:var(--font-display);font-size:.95rem;font-weight:700;margin-bottom:18px;">🔐 Security Settings</div>
        <form method="POST" action="{{ route('profile.password') }}">
          @csrf
          <div class="form-group"><label class="form-label">Current Password</label><input type="password" name="current_password" class="form-input" required/></div>
          <div class="form-group"><label class="form-label">New Password</label><input type="password" name="password" class="form-input" required minlength="8"/></div>
          <div class="form-group"><label class="form-label">Confirm Password</label><input type="password" name="password_confirmation" class="form-input" required minlength="8"/></div>
          @error('current_password')<div style="color:#ef4444;font-size:.78rem;margin-bottom:12px;">{{ $message }}</div>@enderror
          <button type="submit" class="btn btn-primary btn-md"><i class="fas fa-key"></i> Update Password</button>
        </form>
        <div style="margin-top:24px;padding-top:20px;border-top:1px solid var(--border);">
          <div style="font-weight:700;color:var(--text);margin-bottom:8px;">Platform Info</div>
          <div style="font-size:.8rem;color:var(--text-muted);line-height:2;">
            Laravel {{ app()->version() }}<br>
            PHP {{ PHP_VERSION }}<br>
            Database: {{ config('database.default') }}<br>
            Environment: <strong style="color:{{ app()->isProduction()?'var(--green-600)':'#f59e0b' }};">{{ ucfirst(app()->environment()) }}</strong><br>
            Total Users: {{ \App\Models\User::count() }}<br>
            PDF Guides: {{ \App\Models\CourseGuide::count() }}<br>
            Storage: {{ round(disk_total_space('/')/1073741824 - disk_free_space('/')/1073741824,1) }} GB used
          </div>
        </div>
      </div>
    </div>
  </div>

</main>
</div>
@endsection

@section('extra_js')
<script>
// ── Tab switching ──────────────────────────────────────────────────
function switchAdminTab(tabId, linkEl) {
  document.querySelectorAll('.admin-panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.admin-tab').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.admin-nav-link').forEach(l => l.classList.remove('active'));
  document.getElementById(tabId)?.classList.add('active');
  if (linkEl) linkEl.classList.add('active');
  document.querySelector(`.admin-tab[onclick*="${tabId}"]`)?.classList.add('active');
}

// ── SMS ────────────────────────────────────────────────────────────
document.getElementById('smsMessage')?.addEventListener('input', function() {
  document.getElementById('smsCharCount').textContent = this.value.length + '/480';
});
function useTemplate(el) {
  const tpl = el.querySelector('.sms-template-preview').dataset.template;
  const msg = document.getElementById('smsMessage');
  if (msg) { msg.value = tpl; document.getElementById('smsCharCount').textContent = tpl.length + '/480'; msg.focus(); }
}

// ── Innovation rejection ───────────────────────────────────────────
function setRejectReason(form) {
  const reason = prompt('Reason for rejection:', 'Does not meet submission guidelines');
  if (!reason) return false;
  form.querySelector('.reject-reason-input').value = reason;
  return true;
}

// ── Product rejection (with reason) ───────────────────────────────
function setProductRejectReason(form) {
  const reason = prompt('Reason for rejecting this product:', 'Does not meet listing guidelines');
  if (!reason) return false;
  form.querySelector('.product-reject-reason-input').value = reason;
  return true;
}

// ── Lesson Manager ─────────────────────────────────────────────────
function toggleLessonManager(courseId) {
  const row = document.getElementById('lesson-manager-' + courseId);
  if (row) row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}
function toggleLessonTypeFields(select) {
  const form       = select.closest('form');
  const videoField = form.querySelector('.lesson-video-field');
  const pdfField   = form.querySelector('.lesson-pdf-field');
  if (!videoField || !pdfField) return;
  videoField.style.display = select.value === 'video' ? 'block' : 'none';
  pdfField.style.display   = select.value === 'pdf'   ? 'block' : 'none';
}
</script>
@endsection
