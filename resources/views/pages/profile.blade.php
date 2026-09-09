@extends('layouts.app')
@section('title', 'My Profile — AgriTech Pro')
@section('extra_css')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"/>
<link rel="stylesheet" href="{{ asset('css/home.css') }}"/>
<style>
.profile-hero{background:#f6faf5;padding:48px 0 80px;color:var(--text);position:relative;}
.profile-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:32px;margin-top:-60px;position:relative;z-index:2;margin-bottom:24px;}
.profile-hero-inner{display:flex;align-items:flex-end;gap:24px;flex-wrap:wrap;}
.profile-avatar-wrap{position:relative;flex-shrink:0;}
.profile-avatar-edit{position:absolute;bottom:4px;right:4px;width:28px;height:28px;border-radius:50%;background:var(--primary);color:#fff;border:2px solid #fff;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:.7rem;}
.profile-stats-row{display:flex;gap:24px;flex-wrap:wrap;margin-top:16px;}
.profile-stat{text-align:center;}
.profile-stat-val{font-size:1.4rem;font-weight:800;color:var(--text);}
.profile-stat-lbl{font-size:.72rem;color:var(--text-muted);}
.profile-tabs{display:flex;gap:4px;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:5px;margin-bottom:24px;flex-wrap:wrap;}
.profile-tab{padding:9px 18px;border-radius:var(--radius-md);font-size:.84rem;font-weight:600;color:var(--text-muted);cursor:pointer;border:none;background:transparent;transition:all .15s;}
.profile-tab.active{background:var(--primary);color:#fff;}
.profile-panel{display:none;}.profile-panel.active{display:block;}
.form-section{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:24px;margin-bottom:20px;}
.form-section-title{font-size:.9375rem;font-weight:700;color:var(--text);margin-bottom:18px;display:flex;align-items:center;gap:8px;}
.cert-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:24px;color:var(--text);position:relative;overflow:hidden;}
.cert-card::before{content:'🎓';position:absolute;right:16px;top:50%;transform:translateY(-50%);font-size:4rem;opacity:.15;}
.cert-number{font-size:.8125rem;background:rgba(255,255,255,.15);border-radius:var(--radius-full);padding:3px 10px;display:inline-block;margin-bottom:10px;}
.production-table{width:100%;border-collapse:collapse;}
.production-table th{font-size:.73rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);padding:8px 12px;text-align:left;border-bottom:2px solid var(--border);}
.production-table td{padding:10px 12px;font-size:.82rem;border-bottom:1px solid var(--border);color:var(--text);}
.listing-table{width:100%;border-collapse:collapse;}
.listing-table th{font-size:.73rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);padding:8px 12px;text-align:left;border-bottom:2px solid var(--border);}
.listing-table td{padding:10px 12px;font-size:.82rem;border-bottom:1px solid var(--border);}
.toggle-row{display:flex;align-items:center;justify-content:space-between;padding:14px 0;border-bottom:1px solid var(--border);}
.toggle-row:last-child{border-bottom:none;}
.toggle-label{font-size:.87rem;font-weight:600;color:var(--text);}
.toggle-desc{font-size:.76rem;color:var(--text-muted);margin-top:2px;}
.toggle-switch{position:relative;display:inline-block;width:44px;height:24px;flex-shrink:0;}
.toggle-switch input{opacity:0;width:0;height:0;}
.toggle-slider{position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background:var(--gray-300);border-radius:24px;transition:.3s;}
.toggle-slider:before{position:absolute;content:'';height:18px;width:18px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:.3s;}
.toggle-switch input:checked+.toggle-slider{background:var(--primary);}
.toggle-switch input:checked+.toggle-slider:before{transform:translateX(20px);}
.security-info-row{display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);font-size:.83rem;}
.security-info-row:last-child{border-bottom:none;}
@media(max-width:768px){.profile-tabs{overflow-x:auto;flex-wrap:nowrap;-webkit-overflow-scrolling:touch;scrollbar-width:none;}.profile-tabs::-webkit-scrollbar{display:none;}.profile-tab{white-space:nowrap;flex-shrink:0;padding:8px 14px;font-size:.8rem;}.profile-card{padding:20px 16px;margin-top:-40px;}.profile-hero{padding:32px 0 60px;}.profile-hero-inner{justify-content:center;text-align:center;}.profile-stats-row{justify-content:center;}.form-grid-2{grid-template-columns:1fr !important;}}
@media(max-width:480px){.profile-stat-val{font-size:1.1rem;}.form-section{padding:16px;}}
</style>
@endsection

@section('content')

{{-- Profile Hero --}}
<div class="profile-hero">
  <div class="container">
    <div class="profile-hero-inner">
      <div class="profile-avatar-wrap">
        @if(isset($user)&&$user->avatar)
          <img src="{{ asset('storage/'.$user->avatar) }}" alt="{{ $user->full_name }}" class="avatar avatar-xl" style="object-fit:cover;"/>
        @else
          <div class="avatar avatar-xl" style="background:var(--green-50);color:var(--green-700);font-size:1.8rem;font-weight:800;">{{ Auth::user()->initials }}</div>
        @endif
        <div class="profile-avatar-edit" onclick="document.getElementById('avatarInput').click()" title="Change photo">
          <i class="fas fa-camera"></i>
        </div>
        <form id="avatarForm" method="POST" action="{{ route('profile.personal-info') }}" enctype="multipart/form-data" style="display:none;">
          @csrf
          <input type="hidden" name="first_name" value="{{ Auth::user()->first_name }}"/>
          <input type="hidden" name="last_name"  value="{{ Auth::user()->last_name }}"/>
          <input type="hidden" name="email"      value="{{ Auth::user()->email }}"/>
          <input type="hidden" name="district"   value="{{ Auth::user()->district }}"/>
          <input type="file" id="avatarInput" name="avatar" accept="image/*" onchange="this.form.submit()"/>
        </form>
      </div>
      <div style="flex:1;">
        <div style="font-size:1.8rem;font-weight:800;margin-bottom:6px;">{{ Auth::user()->full_name }}</div>
        <div style="color:var(--text-muted);margin-bottom:12px;display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
          <i class="fas fa-map-marker-alt"></i> {{ Auth::user()->district??'Malawi' }}
          <span style="opacity:.4;">·</span>
          {{ ucwords(str_replace('_','-', isset($farm)&&$farm ? $farm->farm_type : 'Farmer')) }}
          @if(Auth::user()->created_at)
            <span style="opacity:.4;">·</span> Member since {{ Auth::user()->created_at->format('M Y') }}
          @endif
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
          @if(isset($farm)&&$farm?->is_verified)
            <span class="badge badge-green"><i class="fas fa-check-circle"></i> Verified Farmer</span>
          @endif
          <span class="badge" style="background:var(--bg-card);color:var(--text);border:1px solid var(--border);">{{ ucfirst(Auth::user()->role) }}</span>
          @if(Auth::user()->farm?->is_organic_certified)
            <span class="badge" style="background:rgba(74,222,128,.2);color:#4ade80;border:1px solid rgba(74,222,128,.3);"><i class="fas fa-leaf"></i> Organic Certified</span>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<div style="background:var(--bg-2);min-height:60vh;padding-bottom:60px;">
  <div class="container">

    {{-- Stats card --}}
    <div class="profile-card">
      <div class="profile-stats-row">
        @foreach([
          [Auth::user()->farm?->size_hectares ? Auth::user()->farm->size_hectares.' ha' : '—', 'Farm Size'],
          [number_format(Auth::user()->total_orders), 'Total Orders'],
          [number_format(Auth::user()->total_courses_enrolled), 'Courses Enrolled'],
          [number_format(Auth::user()->total_innovations), 'Innovations'],
          [isset($certificates) ? $certificates->count() : 0, 'Certificates'],
          [Auth::user()->enrollments()->where('status','completed')->count(), 'Completed'],
        ] as [$val, $lbl])
          <div class="profile-stat">
            <div class="profile-stat-val">{{ $val }}</div>
            <div class="profile-stat-lbl">{{ $lbl }}</div>
          </div>
        @endforeach
        <div style="margin-left:auto;display:flex;gap:8px;flex-wrap:wrap;">
          <a href="{{ route('dashboard') }}" class="btn btn-outline btn-sm"><i class="fas fa-th-large"></i> Dashboard</a>
          <a href="{{ route('marketplace.my-listings') }}" class="btn btn-primary btn-sm"><i class="fas fa-store"></i> My Listings</a>
        </div>
      </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
      <div style="background:var(--green-50);border:1.5px solid var(--green-200);border-radius:var(--radius-md);padding:14px 18px;margin-bottom:16px;color:var(--green-700);font-weight:600;display:flex;align-items:center;gap:8px;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
      </div>
    @endif

    {{-- Profile Tabs --}}
    <div class="profile-tabs">
      @foreach([['tab-info','👤 Personal Info'],['tab-farm','🌾 Farm Details'],['tab-certs','🎓 Certificates'],['tab-listings','🛒 My Listings'],['tab-security','🔐 Security']] as [$id,$label])
        <button class="profile-tab {{ $id==='tab-info'?'active':'' }}" onclick="switchTab('{{ $id }}',this)">{{ $label }}</button>
      @endforeach
    </div>

    {{-- ══ TAB 1: PERSONAL INFO ══ --}}
    <div class="profile-panel active" id="tab-info">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

        <div class="form-section">
          <div class="form-section-title"><i class="fas fa-user" style="color:var(--primary);"></i> Personal Information</div>
          <form method="POST" action="{{ route('profile.personal-info') }}" enctype="multipart/form-data">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
              <div class="form-group">
                <label class="form-label">First Name *</label>
                <input type="text" name="first_name" class="form-input" value="{{ old('first_name', Auth::user()->first_name) }}" required/>
                @error('first_name')<span style="color:#ef4444;font-size:.74rem;">{{ $message }}</span>@enderror
              </div>
              <div class="form-group">
                <label class="form-label">Last Name *</label>
                <input type="text" name="last_name" class="form-input" value="{{ old('last_name', Auth::user()->last_name) }}" required/>
                @error('last_name')<span style="color:#ef4444;font-size:.74rem;">{{ $message }}</span>@enderror
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Email Address *</label>
              <input type="email" name="email" class="form-input" value="{{ old('email', Auth::user()->email) }}" required/>
              @error('email')<span style="color:#ef4444;font-size:.74rem;">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
              <label class="form-label">Phone Number</label>
              <input type="tel" name="phone" class="form-input" value="{{ old('phone', Auth::user()->phone) }}" placeholder="+265 99 123 4567"/>
              @error('phone')<span style="color:#ef4444;font-size:.74rem;">{{ $message }}</span>@enderror
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
              <div class="form-group">
                <label class="form-label">District</label>
                <select name="district" class="form-input form-select">
                  <option value="">Select District</option>
                  @foreach($districts as $district)
                    <option value="{{ $district->name }}" {{ old('district',Auth::user()->district)==$district->name ? 'selected' : '' }}>{{ $district->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Trading Centre</label>
                <input type="text" name="trading_centre" class="form-input" value="{{ old('trading_centre', Auth::user()->trading_centre) }}" placeholder="e.g. Limbe"/>
              </div>
            </div>
            <input type="hidden" name="region" value="{{ Auth::user()->region }}"/>
            <button type="submit" class="btn btn-primary btn-md"><i class="fas fa-save"></i> Save Changes</button>
          </form>
        </div>

        <div class="form-section">
          <div class="form-section-title"><i class="fas fa-bell" style="color:var(--primary);"></i> Notification Preferences</div>
          <form method="POST" action="{{ route('profile.notifications') }}">
            @csrf
            <div class="toggle-row">
              <div><div class="toggle-label">SMS Disease Alerts</div><div class="toggle-desc">Outbreak alerts for your district</div></div>
              <label class="toggle-switch"><input type="checkbox" name="sms_alerts" value="1" @checked(Auth::user()->sms_alerts)/><span class="toggle-slider"></span></label>
            </div>
            <div class="toggle-row">
              <div><div class="toggle-label">Email Notifications</div><div class="toggle-desc">Order updates, new lessons, market prices</div></div>
              <label class="toggle-switch"><input type="checkbox" name="email_alerts" value="1" @checked(Auth::user()->email_alerts)/><span class="toggle-slider"></span></label>
            </div>
            @foreach([['Order Updates','SMS when your order status changes'],['Market Prices','Daily crop price updates'],['New Courses','When new courses are available'],['Innovation Votes','When someone votes your innovation']] as [$label,$desc])
              <div class="toggle-row">
                <div><div class="toggle-label">{{ $label }}</div><div class="toggle-desc">{{ $desc }}</div></div>
                <label class="toggle-switch"><input type="checkbox" value="1" checked/><span class="toggle-slider"></span></label>
              </div>
            @endforeach
            <div style="margin-top:18px;">
              <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Save Preferences</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- ══ TAB 2: FARM DETAILS ══ --}}
    <div class="profile-panel" id="tab-farm">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

        <div class="form-section">
          <div class="form-section-title"><i class="fas fa-seedling" style="color:var(--primary);"></i> Farm Information</div>
          <form method="POST" action="{{ route('profile.farm') }}">
            @csrf
            @php $farmName = isset($farm)&&$farm ? $farm->name : Auth::user()->first_name."'s Farm"; @endphp
            <div class="form-group">
              <label class="form-label">Farm Name *</label>
              <input type="text" name="name" class="form-input" value="{{ old('name', $farmName) }}" required/>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
              <div class="form-group">
                <label class="form-label">Size (hectares)</label>
                <input type="number" name="size_hectares" class="form-input" step="0.1" min="0" value="{{ old('size_hectares', isset($farm)?$farm->size_hectares:'') }}" placeholder="e.g. 4.5"/>
              </div>
              <div class="form-group">
                <label class="form-label">Farm Type</label>
                <select name="farm_type" class="form-input form-select">
                  @foreach(['small_scale'=>'Small-scale','commercial'=>'Commercial','livestock'=>'Livestock','mixed'=>'Mixed','organic'=>'Organic','agribusiness'=>'Agribusiness'] as $v=>$l)
                    <option value="{{ $v }}" @selected((isset($farm)&&$farm?->farm_type)===$v)>{{ $l }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Primary Crops</label>
              <input type="text" name="primary_crops_text" class="form-input" placeholder="e.g. Maize, Soybeans, Tomatoes"
                     value="{{ isset($farm)&&$farm?->primary_crops ? implode(', ',$farm->primary_crops) : '' }}"/>
              <div style="font-size:.73rem;color:var(--text-muted);margin-top:3px;">Separate crops with commas</div>
            </div>
            <div class="form-group">
              <label class="form-label">Irrigation Type</label>
              <select name="irrigation_type" class="form-input form-select">
                @foreach(['rain_fed'=>'Rain-fed','drip'=>'Drip Irrigation','sprinkler'=>'Sprinkler','flood'=>'Flood Irrigation','borehole'=>'Borehole'] as $v=>$l)
                  <option value="{{ $v }}" @selected((isset($farm)&&$farm?->irrigation_type)===$v)>{{ $l }}</option>
                @endforeach
              </select>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:16px;">
              @foreach([['has_storage','Has Storage Facility'],['has_greenhouse','Has Greenhouse'],['is_organic_certified','Organic Certified']] as [$field,$label])
                <label style="display:flex;align-items:center;gap:6px;font-size:.82rem;cursor:pointer;">
                  <input type="checkbox" name="{{ $field }}" value="1" @checked(isset($farm)&&$farm?->{$field}) style="accent-color:var(--primary);width:auto;"/>
                  {{ $label }}
                </label>
              @endforeach
            </div>
            <button type="submit" class="btn btn-primary btn-md"><i class="fas fa-save"></i> Save Farm Details</button>
          </form>
        </div>

        <div class="form-section">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:10px;">
            <div class="form-section-title" style="margin-bottom:0;"><i class="fas fa-chart-bar" style="color:var(--primary);"></i> Production Records</div>
            <a href="{{ route('farm-records') }}" class="btn btn-outline btn-sm"><i class="fas fa-tractor"></i> View Farm Records</a>
          </div>
          @if(isset($farm)&&$farm&&$farm->productions->count()>0)
            <div style="overflow-x:auto;margin-bottom:18px;">
              <table class="production-table">
                <thead><tr><th>Season</th><th>Crop</th><th>Yield/ha</th><th>Revenue</th></tr></thead>
                <tbody>
                  @foreach($farm->productions->take(5) as $prod)
                    <tr>
                      <td class="code">{{ $prod->season }}</td>
                      <td style="font-weight:600;">{{ $prod->crop }}</td>
                      <td>{{ $prod->yield_per_hectare ? number_format($prod->yield_per_hectare,1).' t/ha' : '—' }}</td>
                      <td style="color:var(--primary);font-weight:700;">{{ $prod->revenue ? 'K '.number_format($prod->revenue) : '—' }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div style="text-align:center;padding:20px;color:var(--text-muted);margin-bottom:16px;">
              <i class="fas fa-chart-line" style="font-size:2rem;margin-bottom:8px;display:block;opacity:.3;"></i>
              No production records yet
            </div>
          @endif
          <div style="font-size:.82rem;font-weight:700;color:var(--text);margin-bottom:12px;">+ Add Production Record</div>
          <form method="POST" action="{{ route('profile.farm.production') }}">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
              <div class="form-group">
                <label class="form-label">Season</label>
                <input type="text" name="season" class="form-input" placeholder="e.g. 2025/26" required/>
              </div>
              <div class="form-group">
                <label class="form-label">Crop</label>
                <input type="text" name="crop" class="form-input" placeholder="e.g. Maize" required/>
              </div>
              <div class="form-group">
                <label class="form-label">Yield/ha (tonnes)</label>
                <input type="number" name="yield_per_hectare" class="form-input" step="0.01" placeholder="e.g. 3.5"/>
              </div>
              <div class="form-group">
                <label class="form-label">Revenue (MWK)</label>
                <input type="number" name="revenue" class="form-input" step="0.01" placeholder="e.g. 45000"/>
              </div>
              <div class="form-group">
                <label class="form-label">Planted Date</label>
                <input type="date" name="planted_at" class="form-input"/>
              </div>
              <div class="form-group">
                <label class="form-label">Harvested Date</label>
                <input type="date" name="harvested_at" class="form-input"/>
              </div>
            </div>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Record</button>
          </form>
        </div>
      </div>
    </div>

    {{-- ══ TAB 3: CERTIFICATES ══ --}}
    <div class="profile-panel" id="tab-certs">
      @if(isset($certificates)&&$certificates->count()>0)
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;">
          @foreach($certificates as $enrollment)
            <div class="cert-card">
              <div class="cert-number">{{ $enrollment->certificate_number }}</div>
              <div style="font-size:1rem;font-weight:800;margin-bottom:6px;line-height:1.3;">{{ $enrollment->course->title }}</div>
              <div style="font-size:.78rem;opacity:.75;margin-bottom:4px;">{{ ucwords(str_replace('_',' ',$enrollment->course->category)) }}</div>
              <div style="font-size:.75rem;opacity:.65;margin-bottom:18px;">
                Completed: {{ $enrollment->completed_at?->format('M j, Y') }}<br>
                Issued: {{ $enrollment->certificate_issued_at?->format('M j, Y') }}
              </div>
              <div style="display:flex;gap:8px;">
                <a href="{{ route('learn.certificate.pdf',$enrollment) }}" class="btn btn-white btn-sm"><i class="fas fa-download"></i> Download PDF</a>
                <button onclick="shareToLinkedIn('{{ $enrollment->certificate_number }}','{{ addslashes($enrollment->course->title) }}')" class="btn btn-sm" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);">
                  <i class="fab fa-linkedin"></i> Share
                </button>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="form-section" style="text-align:center;padding:60px;">
          <i class="fas fa-certificate" style="font-size:3rem;color:var(--text-muted);margin-bottom:16px;display:block;opacity:.3;"></i>
          <h3 style="font-size:1.2rem;margin-bottom:8px;">No Certificates Yet</h3>
          <p style="color:var(--text-muted);margin-bottom:20px;">Complete a course with a certificate to earn your first credential.</p>
          <a href="{{ route('learn') }}" class="btn btn-primary btn-lg"><i class="fas fa-graduation-cap"></i> Browse Courses</a>
        </div>
      @endif
    </div>

    {{-- ══ TAB 4: MY LISTINGS ══ --}}
    <div class="profile-panel" id="tab-listings">
      <div class="form-section" style="padding:0;overflow:hidden;">
        <div style="padding:18px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
          <div style="font-size:.9375rem;font-weight:700;">My Product Listings</div>
          <a href="{{ route('marketplace') }}#sell" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add New Listing</a>
        </div>
        <div style="overflow-x:auto;">
          <table class="listing-table">
            <thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Orders</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
              @forelse(isset($listings)?$listings:Auth::user()->products()->withCount('orderItems')->latest()->get() as $product)
                <tr>
                  <td style="font-weight:600;font-size:.83rem;max-width:180px;">{{ Str::limit($product->name,35) }}</td>
                  <td><span class="badge badge-gray" style="font-size:.67rem;">{{ ucfirst($product->category) }}</span></td>
                  <td style="font-weight:700;color:var(--primary);">{{ $product->currency }} {{ number_format($product->price) }}/{{ $product->unit }}</td>
                  <td style="text-align:center;">
                    @if($product->stock_quantity<=5&&$product->stock_quantity>0)
                      <span style="color:#f59e0b;font-weight:700;">{{ $product->stock_quantity }} ⚠️</span>
                    @else
                      {{ $product->stock_quantity }}
                    @endif
                  </td>
                  <td style="text-align:center;font-weight:600;color:var(--primary);">{{ $product->order_items_count }}</td>
                  <td>
                    <span class="badge {{ $product->status==='active'?'badge-green':($product->status==='pending_review'?'badge-earth':($product->status==='sold_out'?'badge-coral':'badge-gray')) }}" style="font-size:.67rem;">
                      {{ ucwords(str_replace('_',' ',$product->status)) }}
                    </span>
                  </td>
                  <td>
                    <div style="display:flex;gap:5px;flex-wrap:wrap;">
                      <button onclick="showToast('✏️ Edit feature coming soon!','info')" class="btn btn-outline btn-sm" style="font-size:.72rem;padding:4px 10px;">Edit</button>
                      <form method="POST" action="{{ route('marketplace.destroy',$product) }}" onsubmit="return confirm('Remove this listing?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;font-size:.72rem;padding:4px 10px;">Remove</button>
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">
                    <i class="fas fa-tag" style="font-size:2rem;margin-bottom:10px;display:block;opacity:.3;"></i>
                    No listings yet. <a href="{{ route('marketplace') }}#sell" style="color:var(--primary);">List your first product →</a>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- ══ TAB 5: SECURITY ══ --}}
    <div class="profile-panel" id="tab-security">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

        <div class="form-section">
          <div class="form-section-title"><i class="fas fa-lock" style="color:var(--primary);"></i> Change Password</div>
          <form method="POST" action="{{ route('profile.password') }}">
            @csrf
            <div class="form-group">
              <label class="form-label">Current Password *</label>
              <input type="password" name="current_password" class="form-input" required/>
              @error('current_password')<span style="color:#ef4444;font-size:.74rem;">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
              <label class="form-label">New Password *</label>
              <input type="password" name="password" class="form-input" required minlength="8"/>
              @error('password')<span style="color:#ef4444;font-size:.74rem;">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
              <label class="form-label">Confirm New Password *</label>
              <input type="password" name="password_confirmation" class="form-input" required minlength="8"/>
            </div>
            <button type="submit" class="btn btn-primary btn-md"><i class="fas fa-key"></i> Update Password</button>
          </form>
        </div>

        <div>
          <div class="form-section" style="margin-bottom:16px;">
            <div class="form-section-title"><i class="fas fa-shield-alt" style="color:var(--primary);"></i> Two-Factor Authentication</div>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
              <div>
                <div style="font-size:.88rem;font-weight:600;color:var(--text);">{{ Auth::user()->two_factor_enabled ? '✅ 2FA Enabled' : '❌ 2FA Disabled' }}</div>
                <div style="font-size:.76rem;color:var(--text-muted);">OTP code sent via SMS on login</div>
              </div>
              <form method="POST" action="{{ route('profile.2fa') }}">
                @csrf
                <button type="submit" class="btn {{ Auth::user()->two_factor_enabled ? 'btn-outline' : 'btn-primary' }} btn-sm">
                  {{ Auth::user()->two_factor_enabled ? 'Disable 2FA' : 'Enable 2FA' }}
                </button>
              </form>
            </div>
          </div>

          <div class="form-section">
            <div class="form-section-title"><i class="fas fa-history" style="color:var(--primary);"></i> Account Activity</div>
            <div class="security-info-row">
              <span style="color:var(--text-muted);">Last login</span>
              <span style="font-weight:600;">{{ Auth::user()->last_login_at?->format('M j, Y g:i A')??'Never' }}</span>
            </div>
            <div class="security-info-row">
              <span style="color:var(--text-muted);">Login IP</span>
              <span class="code">{{ Auth::user()->last_login_ip??'—' }}</span>
            </div>
            <div class="security-info-row">
              <span style="color:var(--text-muted);">Account created</span>
              <span style="font-weight:600;">{{ Auth::user()->created_at->format('M j, Y') }}</span>
            </div>
            <div class="security-info-row">
              <span style="color:var(--text-muted);">Account status</span>
              <span class="badge badge-green" style="font-size:.7rem;">{{ ucfirst(Auth::user()->status) }}</span>
            </div>
            <div style="margin-top:16px;display:flex;gap:8px;flex-wrap:wrap;">
              <button onclick="showToast('⚠️ All other sessions have been signed out.','warning')" class="btn btn-outline btn-sm">
                <i class="fas fa-sign-out-alt"></i> Sign Out Other Devices
              </button>
              <a href="{{ route('notifications') }}" class="btn btn-outline btn-sm">
                <i class="fas fa-bell"></i> View Notifications
              </a>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
</div>

@include('partials.footer')
@endsection

@section('extra_js')
<script>
function switchTab(tabId, btn) {
  document.querySelectorAll('.profile-panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.profile-tab').forEach(b => b.classList.remove('active'));
  document.getElementById(tabId)?.classList.add('active');
  if (btn) btn.classList.add('active');
}

function shareToLinkedIn(certNumber, courseTitle) {
  const url = `https://www.linkedin.com/profile/add?startTask=CERTIFICATION_NAME&name=${encodeURIComponent(courseTitle)}&organizationName=AgriTech+Pro&certUrl=${encodeURIComponent(window.location.origin)}&certId=${encodeURIComponent(certNumber)}`;
  window.open(url, '_blank', 'width=600,height=500');
}

// Open correct tab from URL hash
const hash = window.location.hash.replace('#', '');
if (['tab-info','tab-farm','tab-certs','tab-listings','tab-security'].includes(hash)) {
  const btn = document.querySelector(`.profile-tab[onclick*="${hash}"]`);
  switchTab(hash, btn);
}
</script>
@endsection
