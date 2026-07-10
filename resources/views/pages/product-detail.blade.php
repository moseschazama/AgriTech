@extends('layouts.app')
@section('title', $product->name.' — AgriTech Pro')
@section('extra_css')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"/>
<link rel="stylesheet" href="{{ asset('css/home.css') }}"/>
<style>
.pd-layout{display:grid;grid-template-columns:1fr 1fr;gap:36px;align-items:start;}
.pd-gallery-main{height:380px;border-radius:var(--radius-xl);background:var(--bg-2);display:flex;align-items:center;justify-content:center;font-size:6rem;overflow:hidden;margin-bottom:12px;}
.pd-thumb-row{display:flex;gap:8px;}
.pd-thumb{width:64px;height:64px;border-radius:var(--radius-md);background:var(--bg-2);border:2px solid transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:1.6rem;overflow:hidden;}
.pd-thumb.active{border-color:var(--primary);}
.pd-price-box{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:26px;position:sticky;top:90px;}
.pd-qty-stepper{display:flex;align-items:center;gap:10px;margin:16px 0;}
.qty-btn{width:36px;height:36px;border-radius:var(--radius-md);border:1.5px solid var(--border);background:var(--bg-2);cursor:pointer;font-size:1rem;font-weight:700;}
.seller-card{background:var(--bg-2);border-radius:var(--radius-md);padding:16px;margin-top:18px;}
.review-card{padding:16px 0;border-bottom:1px solid var(--border);}
.related-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;transition:all .2s;}
.related-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-md);}
@media(max-width:900px){.pd-layout{grid-template-columns:1fr;}.pd-price-box{position:static;}}
</style>
@endsection
@section('content')
<div class="section" style="background:var(--bg-2);">
  <div class="container">
    <div style="margin-bottom:18px;font-size:.82rem;color:var(--text-muted);">
      <a href="{{ route('marketplace') }}" style="color:var(--text-muted);">Marketplace</a> /
      <a href="{{ route('marketplace',['category'=>$product->category]) }}" style="color:var(--text-muted);">{{ ucfirst($product->category) }}</a> /
      <span style="color:var(--text);">{{ $product->name }}</span>
    </div>

    <div class="pd-layout">
      {{-- Gallery --}}
      <div>
        @php $pEmojis=['seeds'=>'🌽','fertilizer'=>'🧪','produce'=>'🍅','livestock'=>'🐐','tools'=>'💧','equipment'=>'⚙️','chemicals'=>'⚗️','other'=>'📦'];
              $pColors=['seeds'=>'#dcfce7,#bbf7d0','fertilizer'=>'#e0f2fe,#bae6fd','produce'=>'#fef2f2,#fecaca','livestock'=>'#fef9c3,#fef08a','tools'=>'#f0fdf4,#dcfce7','equipment'=>'#f5f3ff,#ede9fe']; @endphp
        <div class="pd-gallery-main" id="pdMain" style="background:linear-gradient(135deg,{{ $pColors[$product->category]??'#dcfce7,#bbf7d0' }});">
          @if($product->thumbnail)<img src="{{ asset('storage/'.$product->thumbnail) }}" style="width:100%;height:100%;object-fit:cover;"/>
          @else {{ $pEmojis[$product->category]??'📦' }} @endif
        </div>
        @if($product->images&&count($product->images)>1)
          <div class="pd-thumb-row">
            @foreach($product->images as $img)
              <div class="pd-thumb" onclick="document.getElementById('pdMain').innerHTML='<img src=\'{{ asset('storage/'.$img) }}\' style=\'width:100%;height:100%;object-fit:cover;\'/>'">
                <img src="{{ asset('storage/'.$img) }}" style="width:100%;height:100%;object-fit:cover;"/>
              </div>
            @endforeach
          </div>
        @endif

        {{-- Description --}}
        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:22px;margin-top:22px;">
          <h3 style="font-family:var(--font-display);font-size:1rem;font-weight:700;margin-bottom:12px;">Description</h3>
          <p style="font-size:.88rem;color:var(--text);line-height:1.8;">{{ $product->description }}</p>
        </div>

        {{-- Reviews --}}
        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:22px;margin-top:22px;">
          <h3 style="font-family:var(--font-display);font-size:1rem;font-weight:700;margin-bottom:6px;">⭐ Reviews ({{ $product->total_reviews }})</h3>
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;">
            <span style="color:#f59e0b;font-size:1.1rem;">{{ str_repeat('★',round($product->average_rating)) }}{{ str_repeat('☆',5-round($product->average_rating)) }}</span>
            <span style="font-weight:700;">{{ number_format($product->average_rating,1) }}</span>
          </div>
          @forelse($product->reviews->take(5) as $review)
            <div class="review-card">
              <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                <div class="avatar avatar-sm" style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;font-size:.65rem;">{{ $review->user->initials??'FA' }}</div>
                <div><div style="font-weight:700;font-size:.84rem;">{{ $review->user->full_name??'Buyer' }}</div><div style="color:#f59e0b;font-size:.78rem;">{{ str_repeat('★',$review->rating) }}</div></div>
                @if($review->is_verified_purchase)<span class="badge badge-green" style="margin-left:auto;font-size:.65rem;">Verified Purchase</span>@endif
              </div>
              @if($review->review)<p style="font-size:.84rem;color:var(--text-muted);line-height:1.6;">{{ $review->review }}</p>@endif
            </div>
          @empty
            <p style="color:var(--text-muted);font-size:.85rem;text-align:center;padding:16px;">No reviews yet. Be the first buyer to review!</p>
          @endforelse
        </div>
      </div>

      {{-- Price box --}}
      <div class="pd-price-box">
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px;">
          <span class="badge badge-sky">{{ ucfirst($product->category) }}</span>
          @if($product->is_verified)<span class="badge badge-green"><i class="fas fa-check-circle"></i> Verified</span>@endif
          @if($product->price_negotiable)<span class="badge badge-earth">Negotiable</span>@endif
        </div>
        <h1 style="font-family:var(--font-display);font-size:1.5rem;font-weight:800;color:var(--text);margin-bottom:8px;line-height:1.3;">{{ $product->name }}</h1>
        <div style="display:flex;align-items:center;gap:6px;margin-bottom:16px;">
          <span style="color:#f59e0b;">{{ str_repeat('★',round($product->average_rating)) }}{{ str_repeat('☆',5-round($product->average_rating)) }}</span>
          <span style="font-size:.82rem;color:var(--text-muted);">({{ $product->total_reviews }} reviews) · {{ $product->total_sold }} sold</span>
        </div>
        <div style="font-family:var(--font-display);font-size:2.2rem;font-weight:800;color:var(--primary);margin-bottom:4px;">{{ $product->currency }} {{ number_format($product->price) }}</div>
        <div style="font-size:.82rem;color:var(--text-muted);margin-bottom:16px;">per {{ $product->unit }} · Min order: {{ $product->minimum_order }}</div>

        @if($product->in_stock)
          <div style="color:var(--green-700);font-size:.85rem;font-weight:600;margin-bottom:6px;"><i class="fas fa-check-circle"></i> In Stock ({{ $product->stock_quantity }} available)</div>
        @else
          <div style="color:#ef4444;font-size:.85rem;font-weight:600;margin-bottom:6px;"><i class="fas fa-times-circle"></i> Out of Stock</div>
        @endif

        @auth
          @if($product->in_stock)
          <form method="POST" action="{{ route('cart.add',$product) }}">
            @csrf
            <div class="pd-qty-stepper">
              <button type="button" class="qty-btn" onclick="document.getElementById('pdQty').stepDown()">−</button>
              <input type="number" name="quantity" id="pdQty" value="{{ $product->minimum_order }}" min="{{ $product->minimum_order }}" max="{{ $product->stock_quantity }}" style="width:60px;text-align:center;padding:8px;border:1.5px solid var(--border);border-radius:var(--radius-md);"/>
              <button type="button" class="qty-btn" onclick="document.getElementById('pdQty').stepUp()">+</button>
            </div>
            <button type="submit" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;margin-bottom:10px;"><i class="fas fa-cart-plus"></i> Add to Cart</button>
          </form>
          @else
            <button disabled class="btn btn-outline btn-lg" style="width:100%;justify-content:center;opacity:.5;">Out of Stock</button>
          @endif
          <form method="POST" action="{{ route('marketplace.wishlist',$product) }}">
            @csrf
            <button type="submit" class="btn btn-outline btn-md" style="width:100%;justify-content:center;">
              <i class="{{ Auth::user()->hasWishlisted($product)?'fas':'far' }} fa-heart"></i> {{ Auth::user()->hasWishlisted($product)?'Remove from':'Add to' }} Wishlist
            </button>
          </form>
        @else
          <a href="{{ route('login') }}" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;"><i class="fas fa-sign-in-alt"></i> Sign In to Buy</a>
        @endauth

        <div class="seller-card">
          <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;color:var(--text-muted);margin-bottom:10px;">Sold By</div>
          <div style="display:flex;align-items:center;gap:10px;">
            <div class="avatar avatar-md" style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;font-size:.75rem;">{{ $product->seller->initials??'SE' }}</div>
            <div><div style="font-weight:700;font-size:.86rem;">{{ $product->seller->full_name??'Verified Seller' }}</div><div style="font-size:.76rem;color:var(--text-muted);">{{ $product->district }}{{ $product->district?', '.$product->district:'' }}</div></div>
          </div>
        </div>

        @if($product->delivery_available)
          <div style="display:flex;align-items:center;gap:8px;margin-top:14px;font-size:.82rem;color:var(--text-muted);"><i class="fas fa-truck" style="color:var(--primary);"></i> Delivery available to your area</div>
        @endif
      </div>
    </div>

    {{-- Related products --}}
    @if(isset($related)&&$related->count()>0)
      <div style="margin-top:48px;">
        <h2 style="font-family:var(--font-display);font-size:1.2rem;font-weight:800;margin-bottom:18px;">Related Products</h2>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;">
          @foreach($related as $rp)
            <a href="{{ route('marketplace.show',$rp) }}" class="related-card" style="text-decoration:none;display:block;">
              <div style="height:120px;background:linear-gradient(135deg,#dcfce7,#bbf7d0);display:flex;align-items:center;justify-content:center;font-size:2.5rem;">{{ $pEmojis[$rp->category]??'📦' }}</div>
              <div style="padding:12px;">
                <div style="font-size:.84rem;font-weight:700;color:var(--text);margin-bottom:4px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $rp->name }}</div>
                <div style="color:var(--primary);font-weight:700;font-size:.82rem;">{{ $rp->currency }} {{ number_format($rp->price) }}</div>
              </div>
            </a>
          @endforeach
        </div>
      </div>
    @endif
  </div>
</div>
@include('partials.footer')
@endsection
