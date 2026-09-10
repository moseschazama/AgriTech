@extends('layouts.app')
@section('title', 'My Listings — AgriTech Pro')
@section('extra_css')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"/>
<style>
.approval-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;}
@media(max-width:768px){.approval-grid{grid-template-columns:1fr;}}
</style>
@endsection

@section('content')
<div class="section" style="background:var(--bg-2);">
  <div class="container">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
      <div>
        <h1 style="font-size:1.5rem;font-weight:700;color:var(--text);letter-spacing:-0.02em;"><i class="fas fa-shop" style="color:var(--primary);"></i> My Listings</h1>
        <p style="color:var(--text-muted);">Manage your products — admin approves before they go live to buyers</p>
      </div>
      <a href="{{ route('marketplace') }}#sell" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add New Listing</a>
    </div>

    @if(session('success'))
      <div style="background:var(--green-50);border:1.5px solid var(--green-200);border-radius:var(--radius-md);padding:12px 18px;margin-bottom:20px;color:var(--green-700);font-weight:600;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
      </div>
    @endif

    {{-- Status guide --}}
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:16px 20px;margin-bottom:22px;display:flex;gap:20px;flex-wrap:wrap;">
      <div style="font-size:.8125rem;font-weight:700;color:var(--text-muted);align-self:center;">Status guide:</div>
      @foreach(['pending_review'=>['badge-earth','fa-hourglass-half','Pending Review','Waiting for admin approval'],'active'=>['badge-green','fa-circle-check','Active','Live — visible to all buyers'],'inactive'=>['badge-gray','fa-circle-xmark','Inactive','Hidden from buyers'],'sold_out'=>['badge-coral','fa-box-open','Sold Out','Out of stock']] as $status=>[$badge,$icon,$label,$desc])
        <div style="display:flex;align-items:center;gap:6px;">
          <span class="badge {{ $badge }}" style="font-size:.75rem;"><i class="fas {{ $icon }}"></i> {{ $label }}</span>
          <span style="font-size:.75rem;color:var(--text-muted);">{{ $desc }}</span>
        </div>
      @endforeach
    </div>

    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;">
      <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
          <thead>
            <tr style="border-bottom:2px solid var(--border);background:var(--bg-2);">
              <th style="padding:12px 16px;text-align:left;font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">Product</th>
              <th style="padding:12px 16px;text-align:left;font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">Price</th>
              <th style="padding:12px 16px;text-align:center;font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">Stock</th>
              <th style="padding:12px 16px;text-align:center;font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">Orders</th>
              <th style="padding:12px 16px;text-align:left;font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">Status</th>
              <th style="padding:12px 16px;text-align:left;font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse(isset($products) ? $products : [] as $product)
              <tr style="border-bottom:1px solid var(--border);" onmouseover="this.style.background='var(--bg-2)'" onmouseout="this.style.background=''">
                <td style="padding:14px 16px;">
                  <div style="font-weight:700;font-size:.8125rem;color:var(--text);">{{ $product->name }}</div>
                  <div style="font-size:.75rem;color:var(--text-muted);">{{ ucfirst($product->category) }} · {{ $product->district }}</div>
                  @if($product->status === 'pending_review')
                    <div style="font-size:.75rem;color:#f59e0b;margin-top:3px;"><i class="fas fa-info-circle"></i> Under review — visible after admin approval</div>
                  @elseif($product->rejection_reason)
                    <div style="font-size:.75rem;color:#dc2626;margin-top:3px;"><i class="fas fa-times-circle"></i> Rejected: {{ $product->rejection_reason }}</div>
                  @endif
                </td>
                <td style="padding:14px 16px;font-weight:700;color:var(--primary);">{{ $product->currency }} {{ number_format($product->price) }}/{{ $product->unit }}</td>
                <td style="padding:14px 16px;text-align:center;">
                  @if($product->stock_quantity <= 0)
                    <span style="color:#ef4444;font-weight:700;">0 <i class="fas fa-circle-xmark"></i></span>
                  @elseif($product->stock_quantity <= 5)
                    <span style="color:#f59e0b;font-weight:700;">{{ $product->stock_quantity }} <i class="fas fa-triangle-exclamation"></i></span>
                  @else
                    <span style="font-weight:600;">{{ $product->stock_quantity }}</span>
                  @endif
                </td>
                <td style="padding:14px 16px;text-align:center;font-weight:700;color:var(--primary);">{{ $product->order_items_count ?? 0 }}</td>
                <td style="padding:14px 16px;">
                  <span class="badge {{ $product->status==='active'?'badge-green':($product->status==='pending_review'?'badge-earth':($product->status==='sold_out'?'badge-coral':'badge-gray')) }}" style="font-size:.75rem;">
                    {{ ucwords(str_replace('_',' ',$product->status)) }}
                  </span>
                </td>
                <td style="padding:14px 16px;">
                  <div style="display:flex;gap:6px;flex-wrap:wrap;">
                    @if($product->status === 'active')
                      <a href="{{ route('marketplace.show',$product) }}" class="btn btn-outline btn-sm" style="font-size:.75rem;padding:4px 10px;" target="_blank"><i class="fas fa-eye"></i> View</a>
                    @endif
                    <form method="POST" action="{{ route('marketplace.destroy',$product) }}" onsubmit="return confirm('Remove this listing permanently?')">
                      @csrf @method('DELETE')
                      <button type="submit" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;border-radius:var(--radius-md);font-size:.75rem;padding:4px 10px;cursor:pointer;">
                        <i class="fas fa-trash"></i> Remove
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" style="text-align:center;padding:60px;color:var(--text-muted);">
                  <i class="fas fa-tag" style="font-size:1.4rem;margin-bottom:12px;display:block;opacity:.3;"></i>
                  <div style="font-size:1.25rem;font-weight:600;letter-spacing:-0.01em;margin-bottom:8px;">No listings yet</div>
                  <p style="margin-bottom:18px;">List your farm products and reach 12,000+ buyers across Malawi.</p>
                  <a href="{{ route('marketplace') }}#sell" class="btn btn-primary btn-md"><i class="fas fa-plus"></i> List Your First Product</a>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    @if(isset($products) && method_exists($products,'hasPages') && $products->hasPages())
      <div style="margin-top:24px;">{{ $products->links() }}</div>
    @endif

    {{-- How listing approval works --}}
    <div style="background:var(--green-50);border:1.5px solid var(--green-200);border-radius:var(--radius-lg);padding:22px;margin-top:24px;">
      <h3 style="font-size:1.0625rem;font-weight:600;color:var(--green-700);letter-spacing:-0.01em;margin-bottom:10px;">
        <i class="fas fa-info-circle"></i> How Listing Approval Works
      </h3>
      <div class="approval-grid">
        @foreach([['1','fas fa-upload','#e0f2fe','var(--sky-600)','You Submit','Fill the form and submit your product listing'],['2','fas fa-search','#fff7ed','#c2410c','Admin Reviews','Our team checks your listing within 24 hours'],['3','fas fa-check-circle','var(--green-100)','var(--green-700)','Goes Live','Approved listings are visible to all buyers instantly']] as [$num,$icon,$bg,$color,$title,$desc])
          <div style="display:flex;gap:10px;align-items:flex-start;">
            <div style="width:32px;height:32px;border-radius:50%;background:{{ $bg }};color:{{ $color }};display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.9rem;"><i class="{{ $icon }}"></i></div>
            <div><div style="font-weight:700;font-size:.8125rem;color:var(--text);">{{ $num }}. {{ $title }}</div><div style="font-size:.75rem;color:var(--text-muted);line-height:1.5;">{{ $desc }}</div></div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@include('partials.footer')
@endsection
