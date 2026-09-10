@extends('layouts.app')
@section('title', 'Agri Marketplace — AgriTech Pro')
@section('extra_css')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"/>
<link rel="stylesheet" href="{{ asset('css/home.css') }}"/>
<style>
.market-hero{--hero-glow-1:rgba(22,163,74,0.07);--hero-glow-2:rgba(22,163,74,0.04);--hero-orb:rgba(22,163,74,0.06);--hero-badge-bg:var(--green-100);--hero-badge-fg:var(--green-700);--hero-badge-border:var(--green-200);--hero-accent-color:var(--green-600);--hero-overlay-start:rgba(12,22,8,0.78);--hero-overlay-mid:rgba(15,28,10,0.58);--hero-overlay-end:rgba(8,18,5,0.72);--hero-overlay-accent:rgba(74,222,128,0.15);}
.market-hero.page-hero-image{background-image:url('https://images.unsplash.com/photo-1488459716781-31db52582fe9?auto=format&fit=crop&w=1920&q=80');}
.market-hero.page-hero-image .page-hero-cta input,.market-hero.page-hero-image .page-hero-cta select{background:rgba(255,255,255,.95)!important;color:#333!important;border-color:rgba(255,255,255,.3)!important;}
.market-hero .page-hero-cta form{flex-wrap:wrap;}
.market-layout{display:grid;grid-template-columns:260px 1fr;gap:28px;align-items:start;}
.market-sidebar{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:22px;position:sticky;top:90px;}
.market-sidebar h4{font-size:.875rem;font-weight:700;color:var(--text);margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid var(--border);}
.filter-check-group{display:flex;flex-direction:column;gap:8px;margin-bottom:20px;}
.filter-check{display:flex;align-items:center;gap:8px;font-size:.82rem;color:var(--text-muted);cursor:pointer;}
.filter-check input{accent-color:var(--primary);width:15px;height:15px;}
.filter-check:hover{color:var(--text);}
.filter-check.selected{color:var(--primary);font-weight:600;}
.price-range{display:flex;gap:8px;margin-bottom:20px;}
.price-range input{flex:1;padding:7px 10px;border:1.5px solid var(--border);border-radius:var(--radius-md);font-size:.8rem;background:var(--bg-2);color:var(--text);}
.star-filter{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:20px;}
.star-btn{padding:5px 10px;border:1.5px solid var(--border);border-radius:var(--radius-full);font-size:.78rem;cursor:pointer;transition:all .15s;background:var(--bg-2);color:var(--text-muted);}
.star-btn.active,.star-btn:hover{border-color:var(--primary);background:var(--green-50);color:var(--primary);}
.product-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;}
.product-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;transition:all .2s;}
.product-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-lg);border-color:var(--green-300);}
.product-thumb{height:170px;position:relative;display:flex;align-items:center;justify-content:center;overflow:hidden;}
.product-wishlist{position:absolute;top:10px;right:10px;width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,.9);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--gray-400);transition:all .15s;z-index:2;}
.product-wishlist:hover,.product-wishlist.active{color:#ef4444;transform:scale(1.1);}
.product-badge{position:absolute;top:10px;left:10px;font-size:.67rem;font-weight:700;padding:3px 8px;border-radius:var(--radius-full);z-index:2;}
.product-body{padding:14px;}
.product-cat{font-size:.69rem;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;}
.product-name{font-size:.9375rem;font-weight:700;color:var(--text);margin-bottom:4px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;word-break:break-word;}
.product-stars{color:#f59e0b;font-size:.78rem;letter-spacing:1px;}
.product-seller{font-size:.73rem;color:var(--text-muted);margin-bottom:10px;display:flex;align-items:center;gap:4px;flex-wrap:wrap;}
.product-footer{display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border);padding-top:10px;gap:8px;}
.product-price{font-size:1rem;font-weight:800;color:var(--primary);}
.product-unit{font-size:.71rem;color:var(--text-muted);}
.add-cart-btn{width:34px;height:34px;border-radius:50%;background:var(--primary);color:#fff;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .15s;font-size:.85rem;text-decoration:none;flex-shrink:0;}
.add-cart-btn:hover{background:var(--primary-dark);transform:scale(1.1);}
.add-cart-btn:disabled{background:var(--gray-300);cursor:not-allowed;transform:none;}
.toolbar{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:20px;flex-wrap:wrap;}
.toolbar-left{font-size:.83rem;color:var(--text-muted);}
.cart-drawer{position:fixed;right:0;top:0;width:360px;height:100vh;background:var(--bg-card);border-left:1px solid var(--border);z-index:2000;transform:translateX(100%);transition:transform .3s ease;display:flex;flex-direction:column;box-shadow:-4px 0 24px rgba(0,0,0,.12);}
.cart-drawer.open{transform:translateX(0);}
.cart-drawer-header{padding:20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.cart-drawer-body{flex:1;overflow-y:auto;padding:16px;}
.cart-item{display:flex;gap:12px;padding:12px 0;border-bottom:1px solid var(--border);}
.cart-qty-btn{width:28px;height:28px;border-radius:50%;border:1px solid var(--border);background:var(--bg-2);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:.8rem;}
.cart-drawer-footer{padding:16px;border-top:1px solid var(--border);}
.overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1999;display:none;}
.overlay.active{display:block;}
.sell-form-section{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:28px;margin-top:28px;}
@media(max-width:1100px){.product-grid{grid-template-columns:repeat(3,1fr);}}
@media(max-width:900px){.market-layout{grid-template-columns:1fr;}.market-sidebar{position:static;}.product-grid{grid-template-columns:repeat(2,1fr);gap:14px;}}
@media(max-width:768px){.market-sidebar{display:none;position:fixed;top:var(--nav-h);left:0;right:0;bottom:0;z-index:500;overflow-y:auto;padding:20px;background:var(--bg-card);}.market-sidebar.open{display:block;}.product-grid{grid-template-columns:repeat(2,1fr);gap:12px;}.cart-drawer{width:100%;}.product-body{padding:12px;}.product-name{font-size:.85rem;}.add-cart-btn{width:38px;height:38px;}}
@media(max-width:639px){.product-grid{grid-template-columns:1fr;gap:14px;}.product-thumb{height:180px;}.product-body{padding:14px 16px;}.product-name{font-size:.95rem;}.product-cat{font-size:.73rem;}.product-price{font-size:1.05rem;}.product-seller{font-size:.8rem;}.product-stars{font-size:.85rem;}.add-cart-btn{width:44px;height:44px;font-size:1.05rem;}.product-wishlist{width:36px;height:36px;font-size:.95rem;}.toolbar{flex-direction:column;align-items:stretch;gap:10px;}.toolbar-left{font-size:.82rem;text-align:center;}.toolbar .btn{width:100%;justify-content:center;}.toolbar select{width:100%;}.sell-form-section{padding:20px 16px;}}
@media(max-width:380px){.product-thumb{height:160px;}}
.sell-form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
@media(max-width:640px){.sell-form-grid{grid-template-columns:1fr;gap:14px;}.sell-form-grid .form-input,.sell-form-grid .form-select,.sell-form-grid select{min-height:48px;font-size:16px;}.sell-form-section .btn-lg{width:100%;justify-content:center;}}
</style>
@endsection

@section('content')

{{-- Hero --}}
<section class="page-hero market-hero page-hero-image">
  <div class="container">
    <div class="page-hero-content">
      <span class="page-hero-badge">
        <i class="fas fa-store"></i> Agri Marketplace
      </span>
      <h1 class="page-hero-title">Fresh From the <span class="accent">Farm</span></h1>
      <p class="page-hero-desc">Buy directly from verified farmers — no middlemen. Paid via Airtel Money, TNM Mpamba or MTN MoMo.</p>
      <div class="page-hero-cta">
        <form method="GET" action="{{ route('marketplace') }}" style="display:flex;gap:0;max-width:560px;margin:0 auto;">
          <input type="text" name="q" value="{{ request('q') }}" placeholder="Search seeds, fertilizer, livestock, produce..."
                 style="flex:1;padding:14px 20px;border:none;border-radius:var(--radius-md) 0 0 var(--radius-md);font-size:.9375rem;background:var(--bg-card);color:var(--text);border:1px solid var(--border);border-right:none;"/>
          <select name="category" style="padding:14px;border:none;border-left:1px solid var(--border);border-right:1px solid var(--border);font-size:.85rem;background:var(--bg-card);color:var(--text);">
            <option value="">All Categories</option>
            @foreach(['seeds'=>'Seeds','fertilizer'=>'Fertilizer','produce'=>'Fresh Produce','livestock'=>'Livestock','tools'=>'Tools','equipment'=>'Equipment','chemicals'=>'Chemicals'] as $v=>$l)
              <option value="{{ $v }}" @selected(request('category')===$v)>{{ $l }}</option>
            @endforeach
          </select>
          <button type="submit" class="btn btn-primary" style="border-radius:0 var(--radius-md) var(--radius-md) 0;padding:14px 24px;">
            <i class="fas fa-search"></i>
          </button>
        </form>
      </div>
      <div class="page-hero-pills">
        @foreach(['fas fa-shield-alt'=>'Verified Sellers','fas fa-truck'=>'Fast Delivery','fas fa-mobile-alt'=>'Mobile Money','fas fa-undo'=>'Easy Returns'] as $icon=>$label)
          <span class="page-hero-pill"><i class="{{ $icon }}"></i> {{ $label }}</span>
        @endforeach
      </div>
    </div>
  </div>
</section>

<div class="section" style="background:var(--bg-2);">
  <div class="container">

    {{-- Cart count bar --}}
    @php $cartCount = session('cart') ? count(session('cart')) : 0; @endphp
    @if($cartCount > 0)
    <div style="background:var(--green-50);border:1.5px solid var(--green-200);border-radius:var(--radius-md);padding:12px 18px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;">
      <span style="font-size:.88rem;color:var(--green-700);font-weight:600;"><i class="fas fa-shopping-cart"></i> {{ $cartCount }} item(s) in your cart</span>
      <button onclick="openCart()" class="btn btn-primary btn-sm"><i class="fas fa-shopping-bag"></i> View Cart & Checkout</button>
    </div>
    @endif

    <div class="market-layout">

      {{-- Mobile filter toggle --}}
      <button onclick="document.querySelector('.market-sidebar').classList.toggle('open')" style="display:none;align-items:center;gap:8px;padding:10px 16px;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);font-size:.85rem;font-weight:600;color:var(--text);cursor:pointer;width:100%;justify-content:center;margin-bottom:12px;" id="mktFilterToggle">
        <i class="fas fa-filter"></i> Filters
      </button>
      <style>@media(max-width:768px){#mktFilterToggle{display:flex !important;}}</style>

      {{-- Sidebar Filters --}}
      <aside class="market-sidebar">
        <div class="sidebar-close-mobile" onclick="document.querySelector('.market-sidebar').classList.remove('open')" style="display:none;align-items:center;justify-content:space-between;margin-bottom:14px;padding-bottom:12px;border-bottom:1px solid var(--border);">
          <span style="font-size:.9rem;font-weight:700;color:var(--text);"><i class="fas fa-filter" style="color:var(--primary);margin-right:6px;"></i> Filters</span>
          <button style="background:var(--bg-2);border:1px solid var(--border);width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:1rem;color:var(--text-muted);display:flex;align-items:center;justify-content:center;">&times;</button>
        </div>
        <style>@media(max-width:768px){.sidebar-close-mobile{display:flex !important;}}</style>
        <form method="GET" action="{{ route('marketplace') }}" id="filterForm">
          @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}"/>@endif

          <h4><i class="fas fa-filter" style="color:var(--primary);"></i> Filter Products</h4>

          <div style="margin-bottom:20px;">
            <div style="font-size:.78rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px;">Category</div>
            <div class="filter-check-group">
              <label class="filter-check {{ !request('category') ? 'selected' : '' }}">
                <input type="radio" name="category" value="" @checked(!request('category'))/> All Categories
              </label>
              @foreach(['seeds'=>'Seeds','fertilizer'=>'Fertilizer','produce'=>'Fresh Produce','livestock'=>'Livestock','tools'=>'Tools','equipment'=>'Equipment','chemicals'=>'Chemicals'] as $v=>$l)
                <label class="filter-check {{ request('category')===$v ? 'selected' : '' }}">
                  <input type="radio" name="category" value="{{ $v }}" @checked(request('category')===$v)/> <i class="fas {{ \App\Support\CategoryIcons::product($v) }}"></i> {{ $l }}
                </label>
              @endforeach
            </div>
          </div>

          <div style="margin-bottom:20px;">
            <div style="font-size:.78rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px;">Price Range (MWK)</div>
            <div class="price-range">
              <input type="number" name="price_min" value="{{ request('price_min') }}" placeholder="Min"/>
              <input type="number" name="price_max" value="{{ request('price_max') }}" placeholder="Max"/>
            </div>
          </div>

          <div style="margin-bottom:20px;">
            <div style="font-size:.78rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px;">District</div>
            <select name="district" class="form-input form-select" style="font-size:.82rem;padding:8px 12px;">
              <option value="">All Districts</option>
              @foreach($districts as $district)
                <option value="{{ $district->name }}" @selected(request('district')==$district->name)>{{ $district->name }}</option>
              @endforeach
            </select>
          </div>

          <div style="margin-bottom:20px;">
            <div style="font-size:.78rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px;">Min Rating</div>
            <div class="star-filter">
              @foreach([4=>'4★+',3=>'3★+',2=>'2★+',1=>'Any'] as $r=>$l)
                <a href="{{ route('marketplace', array_merge(request()->query(), ['min_rating'=>$r])) }}"
                   class="star-btn {{ request('min_rating')==$r ? 'active' : '' }}">{{ $l }}</a>
              @endforeach
            </div>
          </div>

          <div style="margin-bottom:20px;">
            <div style="font-size:.78rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px;">Availability</div>
            <label class="filter-check"><input type="checkbox" name="in_stock" value="1" @checked(request('in_stock'))/> In Stock Only</label>
            <label class="filter-check" style="margin-top:6px;"><input type="checkbox" name="delivery" value="1" @checked(request('delivery'))/> Delivery Available</label>
          </div>

          <button type="submit" class="btn btn-primary btn-md" style="width:100%;justify-content:center;"><i class="fas fa-filter"></i> Apply Filters</button>
          <a href="{{ route('marketplace') }}" class="btn btn-outline btn-sm" style="width:100%;justify-content:center;margin-top:8px;">Clear All</a>
        </form>
      </aside>

      {{-- Product Grid --}}
      <div>
        <div class="toolbar">
          <div class="toolbar-left">
            Showing <strong>{{ isset($products) ? $products->total() : 0 }}</strong> products
            @if(request('q')) for "<strong>{{ request('q') }}</strong>" @endif
          </div>
          <div style="display:flex;align-items:center;gap:10px;">
            @auth
              <button onclick="openCart()" class="btn btn-outline btn-sm">
                <i class="fas fa-shopping-cart"></i> Cart ({{ $cartCount }})
              </button>
            @endauth
            <select class="form-select" style="font-size:.82rem;padding:7px 12px;" onchange="window.location=this.value">
              @foreach(['popular'=>'Most Popular','newest'=>'Newest','price_low'=>'Price: Low–High','price_high'=>'Price: High–Low','rating'=>'Highest Rated'] as $v=>$l)
                <option value="{{ route('marketplace', array_merge(request()->query(), ['sort'=>$v])) }}" @selected(request('sort',$v==='popular'?'popular':null)===$v)>{{ $l }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="product-grid">
          @forelse(isset($products) ? $products : [] as $product)
            @php
              $pColors=['seeds'=>'#dcfce7,#bbf7d0','fertilizer'=>'#e0f2fe,#bae6fd','produce'=>'#fef2f2,#fecaca','livestock'=>'#fef9c3,#fef08a','tools'=>'#f0fdf4,#dcfce7','equipment'=>'#f5f3ff,#ede9fe','chemicals'=>'#fff7ed,#fed7aa'];
              $pImgs=['seeds'=>'seedling.jpg','fertilizer'=>'spraying2.jpg','produce'=>'tomato.jpg','livestock'=>'cows.jpg','tools'=>'irrigation.jpg','equipment'=>'agritech-drone.jpg','chemicals'=>'spraying2.jpg','other'=>'market-stall.jpg'];
              $pColor=$pColors[$product->category]??'#dcfce7,#bbf7d0';
              $pCover=asset('assets/img/agri/'.($pImgs[$product->category]??'leaf-healthy.jpg'));
              $wishlisted=auth()->check()&&$product->wishlistedBy->isNotEmpty();
            @endphp
            <div class="product-card">
              <div class="product-thumb" style="background:{{ explode(',', $pColor)[0] }};">
                @if($product->thumbnail)
                  <img src="{{ asset('storage/'.$product->thumbnail) }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;"/>
                @else
                  <img src="{{ $pCover }}" alt="{{ $product->name }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;"/>
                @endif
                @auth
                  <button class="product-wishlist {{ $wishlisted?'active':'' }}" onclick="toggleWishlist(this,{{ $product->id }})" title="Wishlist">
                    <i class="{{ $wishlisted?'fas':'far' }} fa-heart" style="{{ $wishlisted?'color:#ef4444':'' }}"></i>
                  </button>
                @endauth
                @if($product->is_featured)
                  <span class="product-badge" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;">Hot</span>
                @elseif($product->total_sold<5)
                  <span class="product-badge" style="background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;">New</span>
                @elseif($product->category==='produce')
                  <span class="product-badge" style="background:var(--green-50);color:var(--green-700);border:1px solid var(--green-200);">✓ Fresh</span>
                @endif
              </div>
              <div class="product-body">
                <div class="product-cat">{{ ucfirst($product->category) }}</div>
                <div class="product-name">{{ $product->name }}</div>
                <div style="display:flex;align-items:center;gap:4px;margin-bottom:3px;">
                  <span class="product-stars">{{ str_repeat('★',round($product->average_rating)) }}{{ str_repeat('☆',5-round($product->average_rating)) }}</span>
                  <span style="font-size:.7rem;color:var(--text-muted);">({{ $product->total_reviews }})</span>
                </div>
                <div class="product-seller">
                  <i class="fas fa-store" style="color:var(--primary);font-size:.68rem;"></i>
                  {{ $product->seller->full_name??'Verified Seller' }} · {{ $product->district }}
                </div>
                @if(!$product->in_stock)
                  <div style="font-size:.72rem;color:#ef4444;font-weight:600;margin-bottom:6px;"><i class="fas fa-triangle-exclamation" style="margin-right:3px;"></i> Out of Stock</div>
                @elseif($product->stock_quantity<=5)
                  <div style="font-size:.72rem;color:#f59e0b;font-weight:600;margin-bottom:6px;"><i class="fas fa-bolt" style="margin-right:3px;"></i> Only {{ $product->stock_quantity }} left!</div>
                @endif
                <div class="product-footer">
                  <div>
                    <span class="product-price">{{ $product->currency }} {{ number_format($product->price) }}</span>
                    <span class="product-unit">/{{ $product->unit }}</span>
                  </div>
                  @auth
                    @if($product->in_stock)
                      <form method="POST" action="{{ route('cart.add',$product) }}" style="display:inline;">
                        @csrf
                        <input type="hidden" name="quantity" value="1"/>
                        <button type="submit" class="add-cart-btn" title="Add to cart"><i class="fas fa-cart-plus"></i></button>
                      </form>
                    @else
                      <button class="add-cart-btn" disabled style="background:var(--gray-300);cursor:not-allowed;" title="Out of stock">
                        <i class="fas fa-times"></i>
                      </button>
                    @endif
                  @else
                    <a href="{{ route('login') }}" class="add-cart-btn" title="Sign in to buy"><i class="fas fa-cart-plus"></i></a>
                  @endauth
                </div>
              </div>
            </div>
          @empty
            <div style="grid-column:1/-1;text-align:center;padding:60px;color:var(--text-muted);">
              <i class="fas fa-search" style="font-size:3rem;margin-bottom:16px;display:block;opacity:.3;"></i>
              <h3 style="margin-bottom:8px;">No products found</h3>
              <p>Try adjusting your search or filters.</p>
              <a href="{{ route('marketplace') }}" class="btn btn-primary btn-sm" style="margin-top:16px;">Clear filters</a>
            </div>
          @endforelse
        </div>

        @if(isset($products)&&$products->hasPages())
          <div style="margin-top:32px;">{{ $products->withQueryString()->links() }}</div>
        @endif

        {{-- Sell a Product Form --}}
        @auth
        <div class="sell-form-section content-end" id="sell">
<h3 style="font-size:clamp(1rem,4vw,1.2rem);font-weight:800;color:var(--text);margin-bottom:6px;word-break:break-word;"><i class="fas fa-wheat-awn" style="color:var(--primary);"></i> List Your Product</h3>
          <p style="font-size:.85rem;color:var(--text-muted);margin-bottom:22px;">Reach 12,000+ buyers across Malawi. Your listing goes live once approved (usually within 24 hours).</p>
          <form method="POST" action="{{ route('marketplace.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="sell-form-grid">
              <div class="form-group">
                <label class="form-label">Product Name *</label>
                <input type="text" name="name" class="form-input" placeholder="e.g. Hybrid Maize Seed DK8031" value="{{ old('name') }}" required/>
                @error('name')<span style="color:#ef4444;font-size:.75rem;">{{ $message }}</span>@enderror
              </div>
              <div class="form-group">
                <label class="form-label">Category *</label>
                <select name="category" class="form-input form-select" required>
                  <option value="">Select category</option>
                  @foreach(['seeds'=>'Seeds','fertilizer'=>'Fertilizer','produce'=>'Fresh Produce','livestock'=>'Livestock','tools'=>'Tools','equipment'=>'Equipment','chemicals'=>'Chemicals','other'=>'Other'] as $v=>$l)
                    <option value="{{ $v }}" @selected(old('category')===$v)>{{ $l }}</option>
                  @endforeach
                </select>
                @error('category')<span style="color:#ef4444;font-size:.75rem;">{{ $message }}</span>@enderror
              </div>
              <div class="form-group" style="grid-column:1/-1;">
                <label class="form-label">Description *</label>
                <textarea name="description" class="form-input" rows="3" placeholder="Describe your product — variety, quality, how grown..." required style="resize:vertical;">{{ old('description') }}</textarea>
                @error('description')<span style="color:#ef4444;font-size:.75rem;">{{ $message }}</span>@enderror
              </div>
              <div class="form-group">
                <label class="form-label">Price (MWK) *</label>
                <input type="number" name="price" class="form-input" placeholder="e.g. 180" value="{{ old('price') }}" step="0.01" min="0" required/>
                @error('price')<span style="color:#ef4444;font-size:.75rem;">{{ $message }}</span>@enderror
              </div>
              <div class="form-group">
                <label class="form-label">Unit *</label>
                <select name="unit" class="form-input form-select" required>
                  @foreach(['kg','bag','box','head','litre','bundle','piece','tray','crate','unit'] as $u)
                    <option value="{{ $u }}" @selected(old('unit')===$u)>{{ ucfirst($u) }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Stock Quantity *</label>
                <input type="number" name="stock_quantity" class="form-input" placeholder="e.g. 100" value="{{ old('stock_quantity') }}" min="1" required/>
              </div>
              <div class="form-group">
                <label class="form-label">Min Order</label>
                <input type="number" name="minimum_order" class="form-input" placeholder="e.g. 1" value="{{ old('minimum_order',1) }}" min="1"/>
              </div>
              <div class="form-group">
                <label class="form-label">District *</label>
                <select name="district" class="form-input form-select" required>
                  <option value="">Select District</option>
                  @foreach($districts as $district)
                    <option value="{{ $district->name }}" @selected(old('district')==$district->name)>{{ $district->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Trading Centre</label>
                <input type="text" name="trading_centre" class="form-input" placeholder="e.g. Limbe" value="{{ old('trading_centre') }}"/>
              </div>
              <div class="form-group" style="grid-column:1/-1;">
                <label class="form-label">Product Photos (max 5)</label>
                <input type="file" name="images[]" class="form-input" multiple accept="image/jpeg,image/png,image/webp" style="padding:8px;"/>
                <div style="font-size:.74rem;color:var(--text-muted);margin-top:4px;">JPG, PNG, WebP · Max 4MB each · First photo becomes the thumbnail</div>
              </div>
              <div class="form-group" style="display:flex;align-items:center;gap:8px;">
                <input type="checkbox" name="price_negotiable" value="1" id="negotiable" @checked(old('price_negotiable')) style="width:auto;accent-color:var(--primary);"/>
                <label for="negotiable" style="font-size:.85rem;color:var(--text-muted);cursor:pointer;">Price is negotiable</label>
              </div>
              <div class="form-group" style="display:flex;align-items:center;gap:8px;">
                <input type="checkbox" name="delivery_available" value="1" id="delivery" @checked(old('delivery_available',true)) style="width:auto;accent-color:var(--primary);"/>
                <label for="delivery" style="font-size:.85rem;color:var(--text-muted);cursor:pointer;">Delivery available</label>
              </div>
            </div>
            <div style="margin-top:20px;">
              <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-plus"></i> List Product — Goes Live After Review</button>
            </div>
          </form>
        </div>
        @else
        <div style="background:var(--green-50);border:1.5px solid var(--green-200);border-radius:var(--radius-lg);padding:32px;text-align:center;margin-top:28px;">
          <i class="fas fa-store" style="font-size:2.5rem;color:var(--primary);margin-bottom:14px;display:block;"></i>
          <h3 style="font-size:1.2rem;margin-bottom:8px;">Start Selling Your Farm Products</h3>
          <p style="font-size:.85rem;color:var(--text-muted);margin-bottom:20px;">Join 12,000+ farmers already selling on AgriTech Pro.</p>
          <a href="{{ route('register') }}" class="btn btn-primary btn-lg"><i class="fas fa-seedling"></i> Create Account</a>
        </div>
        @endauth
      </div>
    </div>
  </div>
</div>

{{-- Cart Drawer --}}
<div class="overlay" id="cartOverlay" onclick="closeCart()"></div>
<div class="cart-drawer" id="cartDrawer">
  <div class="cart-drawer-header">
    <h3 style="font-size:1rem;font-weight:700;"><i class="fas fa-cart-shopping"></i> Your Cart ({{ $cartCount }})</h3>
    <button onclick="closeCart()" style="background:none;border:none;font-size:1.1rem;cursor:pointer;color:var(--text-muted);">✕</button>
  </div>
  <div class="cart-drawer-body">
    @php $cartItems=session('cart',[]); $cartTotal=0; @endphp
    @if(count($cartItems)>0)
      @foreach($cartItems as $productId=>$qty)
        @php $p=\App\Models\Product::find($productId); if(!$p) continue; $lineTotal=$p->price*$qty; $cartTotal+=$lineTotal; @endphp
        <div class="cart-item">
          <div style="width:50px;height:50px;background:var(--bg-2);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:var(--green-600);flex-shrink:0;">
            <i class="fas {{ \App\Support\CategoryIcons::product($p->category) }}"></i>
          </div>
          <div style="flex:1;min-width:0;">
            <div style="font-size:.83rem;font-weight:600;color:var(--text);margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $p->name }}</div>
            <div style="font-size:.75rem;color:var(--text-muted);">{{ $p->currency }} {{ number_format($p->price) }}/{{ $p->unit }}</div>
            <div style="display:flex;align-items:center;gap:8px;margin-top:6px;">
              <form method="POST" action="{{ route('cart.update',$p) }}" style="display:inline;">
                @csrf @method('PATCH')
                <input type="hidden" name="quantity" value="{{ max(0,$qty-1) }}"/>
                <button type="submit" class="cart-qty-btn">−</button>
              </form>
              <span style="font-size:.85rem;font-weight:700;color:var(--text);min-width:20px;text-align:center;">{{ $qty }}</span>
              <form method="POST" action="{{ route('cart.update',$p) }}" style="display:inline;">
                @csrf @method('PATCH')
                <input type="hidden" name="quantity" value="{{ $qty+1 }}"/>
                <button type="submit" class="cart-qty-btn">+</button>
              </form>
            </div>
          </div>
          <div style="text-align:right;">
            <div style="font-size:.88rem;font-weight:700;color:var(--primary);">{{ $p->currency }} {{ number_format($lineTotal) }}</div>
            <form method="POST" action="{{ route('cart.update',$p) }}" style="display:inline;">
              @csrf @method('PATCH')
              <input type="hidden" name="quantity" value="0"/>
              <button type="submit" style="background:none;border:none;color:#ef4444;font-size:.72rem;cursor:pointer;margin-top:6px;">Remove</button>
            </form>
          </div>
        </div>
      @endforeach
    @else
      <div style="text-align:center;padding:40px;color:var(--text-muted);">
        <i class="fas fa-shopping-cart" style="font-size:2.5rem;margin-bottom:14px;display:block;opacity:.3;"></i>
        Your cart is empty.<br>
        <button onclick="closeCart()" class="btn btn-primary btn-sm" style="margin-top:16px;">Browse Products</button>
      </div>
    @endif
  </div>
  @if($cartCount>0)
  <div class="cart-drawer-footer">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
      <span style="font-size:.9rem;font-weight:600;color:var(--text);">Subtotal</span>
      <span style="font-size:1.2rem;font-weight:800;color:var(--primary);">MWK {{ number_format($cartTotal) }}</span>
    </div>
    <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:14px;">Delivery fee calculated at checkout based on your location.</div>
    <a href="{{ route('cart') }}" class="btn btn-primary btn-md" style="width:100%;justify-content:center;margin-bottom:8px;">
      <i class="fas fa-credit-card"></i> Proceed to Checkout
    </a>
    <button onclick="closeCart()" class="btn btn-outline btn-sm" style="width:100%;justify-content:center;">Continue Shopping</button>
  </div>
  @endif
</div>

@include('partials.footer')
@endsection

@section('extra_js')
<script>
function openCart(){document.getElementById('cartDrawer').classList.add('open');document.getElementById('cartOverlay').classList.add('active');document.body.style.overflow='hidden';}
function closeCart(){document.getElementById('cartDrawer').classList.remove('open');document.getElementById('cartOverlay').classList.remove('active');document.body.style.overflow='';}

function toggleWishlist(btn,productId){
  fetch(`/marketplace/products/${productId}/wishlist`,{
    method:'POST',
    headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'}
  }).then(r=>r.json()).then(data=>{
    const icon=btn.querySelector('i');
    if(data.added){icon.className='fas fa-heart';icon.style.color='#ef4444';btn.classList.add('active');showToast('Added to wishlist!','success');}
    else{icon.className='far fa-heart';icon.style.color='';btn.classList.remove('active');showToast('Removed from wishlist','info');}
  });
}

// Auto-select unit when category changes
const categoryUnitMap = {
  seeds: 'kg',
  fertilizer: 'bag',
  produce: 'kg',
  livestock: 'head',
  tools: 'piece',
  equipment: 'unit',
  chemicals: 'litre',
  other: 'unit'
};

const categorySelect = document.querySelector('select[name="category"]');
const unitSelect = document.querySelector('select[name="unit"]');

if (categorySelect && unitSelect) {
  categorySelect.addEventListener('change', function() {
    const unit = categoryUnitMap[this.value];
    if (unit) {
      unitSelect.value = unit;
      // Brief highlight so user notices the change
      unitSelect.style.transition = 'box-shadow .2s';
      unitSelect.style.boxShadow = '0 0 0 2px var(--primary)';
      setTimeout(() => { unitSelect.style.boxShadow = ''; }, 800);
    }
  });
}

// Auto-submit filter form on change
document.querySelectorAll('#filterForm input[type=radio], #filterForm input[type=checkbox]').forEach(el=>{
  el.addEventListener('change',()=>document.getElementById('filterForm').submit());
});
</script>
@endsection
