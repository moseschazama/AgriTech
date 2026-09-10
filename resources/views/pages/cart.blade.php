@extends('layouts.app')
@section('title', 'Your Cart — AgriTech Pro')
@section('extra_css')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"/>
<style>
.cart-layout{display:grid;grid-template-columns:1fr 380px;gap:24px;align-items:start;}
.cart-item-row{display:flex;gap:16px;padding:20px;border-bottom:1px solid var(--border);align-items:flex-start;}
.cart-item-thumb{width:80px;height:80px;border-radius:var(--radius-md);background:var(--bg-2);display:flex;align-items:center;justify-content:center;font-size:2.5rem;flex-shrink:0;}
.cart-qty-ctrl{display:flex;align-items:center;gap:8px;margin-top:10px;background:var(--bg-2);padding:6px 10px;border-radius:var(--radius-full);border:1.5px solid var(--border);width:fit-content;}
.cart-qty-ctrl:focus-within{border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-glow);}
.cart-qty-btn{width:36px;height:36px;border-radius:50%;border:1.5px solid var(--border);background:var(--bg-card);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:1.1rem;font-weight:800;color:var(--text);transition:all .15s;box-shadow:var(--shadow-xs);}
.cart-qty-btn:hover{border-color:var(--primary);background:var(--primary);color:#fff;box-shadow:var(--shadow-green);transform:scale(1.08);}
.cart-qty-btn:active{transform:scale(0.95);}
.cart-qty-btn:disabled{opacity:.4;cursor:not-allowed;transform:none;box-shadow:none;}
.order-summary{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:24px;position:sticky;top:90px;}
.tc-suggestions{position:absolute;top:100%;left:0;right:0;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);box-shadow:var(--shadow-lg);max-height:200px;overflow-y:auto;z-index:100;display:none;}
.tc-suggestions div{padding:10px 14px;font-size:.8125rem;cursor:pointer;border-bottom:1px solid var(--border);transition:background .1s;}
.tc-suggestions div:hover{background:var(--green-50);color:var(--primary);}
.summary-row{display:flex;justify-content:space-between;padding:10px 0;font-size:.8125rem;}
.summary-row.total{border-top:2px solid var(--border);margin-top:6px;padding-top:14px;font-size:1.0625rem;font-weight:800;}
.payment-method-btn{border:2px solid var(--border);border-radius:var(--radius-md);padding:14px;cursor:pointer;transition:all .15s;display:flex;align-items:center;gap:10px;width:100%;background:var(--bg-2);margin-bottom:10px;}
.payment-method-btn:hover,.payment-method-btn.selected{border-color:var(--primary);background:var(--green-50);}
.payment-method-btn input[type=radio]{accent-color:var(--primary);}
@media(max-width:900px){.cart-layout{grid-template-columns:1fr;}}
@media(max-width:768px){.order-summary{padding:18px;}}
@media(max-width:600px){.order-summary{position:static;}.cart-item-row{flex-wrap:wrap;gap:12px;}.cart-item-thumb{width:60px;height:60px;font-size:2rem;}}
@media(max-width:480px){.cart-item-row{padding:14px;}.cart-item-thumb{width:50px;height:50px;font-size:1.6rem;}.cart-qty-btn{width:34px;height:34px;}.order-summary{padding:14px;}.summary-row{font-size:.8rem;}.cart-empty-state{padding:32px 20px !important;}}
</style>
@endsection

@section('content')
<div class="section" style="background:var(--bg-2);">
  <div class="container">
    <div style="margin-bottom:24px;">
      <h1 class="heading-lg" style="font-size:clamp(1.3rem,4vw,1.6rem);color:var(--text);word-break:break-word;"><i class="fas fa-cart-shopping" style="color:var(--primary);"></i> Your Cart</h1>
      <p style="color:var(--text-muted);">Review your items before checkout</p>
    </div>

    @if(count($items)===0)
      <div class="cart-empty-state" style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);padding:60px;text-align:center;">
        <i class="fas fa-shopping-cart" style="font-size:1.75rem;color:var(--text-muted);margin-bottom:14px;display:block;opacity:.3;"></i>
        <h3 class="heading-sm" style="margin-bottom:8px;">Your cart is empty</h3>
        <p style="color:var(--text-muted);margin-bottom:20px;">Browse our marketplace to find quality farm products.</p>
        <a href="{{ route('marketplace') }}" class="btn btn-primary btn-lg"><i class="fas fa-store"></i> Browse Marketplace</a>
      </div>
    @else
    <div class="cart-layout">
      {{-- Cart Items --}}
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-xl);overflow:hidden;">
        <div style="padding:16px 20px;border-bottom:1px solid var(--border);">
          <strong>{{ count($items) }} item(s) in your cart</strong>
        </div>
        @foreach($items as $item)
          @if($item['product'])
          @php
            $p=$item['product'];
          @endphp
          <div class="cart-item-row">
            <div class="cart-item-thumb"><i class="fas {{ \App\Support\CategoryIcons::product($p->category) }}"></i></div>
            <div style="flex:1;">
              <div class="body-base font-700" style="color:var(--text);margin-bottom:3px;">{{ $p->name }}</div>
              <div class="body-xs" style="color:var(--text-muted);">{{ ucfirst($p->category) }} · Sold by {{ $p->seller->full_name??'Verified Seller' }}</div>
              <div class="body-sm" style="color:var(--text-muted);">{{ $p->currency }} {{ number_format($p->price) }}/{{ $p->unit }}</div>
              <div class="cart-qty-ctrl">
                <form method="POST" action="{{ route('cart.update',$p) }}" style="display:inline;">
                  @csrf @method('PATCH')
                  <input type="hidden" name="quantity" value="{{ max(0,$item['quantity']-1) }}"/>
                  <button type="submit" class="cart-qty-btn">−</button>
                </form>
                <span class="body-base font-700" style="min-width:24px;text-align:center;">{{ $item['quantity'] }}</span>
                <form method="POST" action="{{ route('cart.update',$p) }}" style="display:inline;">
                  @csrf @method('PATCH')
                  <input type="hidden" name="quantity" value="{{ $item['quantity']+1 }}"/>
                  <button type="submit" class="cart-qty-btn" {{ $item['quantity']>=$p->stock_quantity?'disabled':'' }}>+</button>
                </form>
                <form method="POST" action="{{ route('cart.update',$p) }}" style="display:inline;margin-left:8px;">
                  @csrf @method('PATCH')
                  <input type="hidden" name="quantity" value="0"/>
                  <button type="submit" style="background:none;border:none;color:#ef4444;cursor:pointer;" class="body-xs">Remove</button>
                </form>
              </div>
            </div>
            <div style="text-align:right;">
              <div class="text-base font-800" style="color:var(--primary);">{{ $p->currency }} {{ number_format($item['subtotal']) }}</div>
              @if($p->stock_quantity<=$item['quantity']&&$p->stock_quantity>0)
                <div class="body-xs" style="color:#f59e0b;margin-top:4px;"><i class="fas fa-triangle-exclamation" style="margin-right:3px;"></i> Limited stock</div>
              @endif
            </div>
          </div>
          @endif
        @endforeach
        <div style="padding:14px 20px;display:flex;justify-content:space-between;align-items:center;">
          <a class="body-sm font-600" href="{{ route('marketplace') }}" style="color:var(--primary);"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
          <span class="body-sm" style="color:var(--text-muted);">Prices in MWK · Delivery fee calculated at checkout</span>
        </div>
      </div>

      {{-- Order Summary + Checkout --}}
      <div class="order-summary">
        <div class="heading-sm font-800" style="margin-bottom:18px;">Order Summary</div>
        <div class="summary-row"><span>Subtotal ({{ count($items) }} items)</span><span>MWK {{ number_format($total) }}</span></div>
        <div class="summary-row" id="deliveryFeeRow"><span>Delivery fee</span><span id="deliveryFeeDisplay" style="font-weight:600;">{{ $deliveryFee > 0 ? 'MWK '.number_format($deliveryFee) : 'Included' }}</span></div>
        <div class="summary-row total"><span>Total</span><span id="grandTotalDisplay" style="color:var(--primary);">MWK {{ number_format($grandTotal) }}</span></div>

        <form method="POST" action="{{ route('checkout') }}" id="checkoutForm" style="margin-top:20px;">
          @csrf
          <div class="body-base font-700" style="margin-bottom:12px;">Delivery Address</div>
          <div class="form-group">
            <label class="form-label">Delivery District *</label>
            <select name="delivery_district" id="deliveryDistrict" class="form-input form-select" required onchange="updateTradingCentres()">
              <option value="">Select District</option>
              @foreach($districts as $district)
                <option value="{{ $district->name }}" {{ old('delivery_district', Auth::user()->district)==$district->name ? 'selected' : '' }}>{{ $district->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group" style="position:relative;">
            <label class="form-label">Trading Centre / Town *</label>
            <input type="text" name="delivery_town" id="deliveryTown" class="form-input" placeholder="Start typing to search..." value="{{ old('delivery_town', Auth::user()->trading_centre) }}" autocomplete="off" required/>
            <div class="tc-suggestions" id="townSuggestions"></div>
          </div>
          <div class="form-group">
            <label class="form-label">Delivery Address *</label>
            <input type="text" name="delivery_address" class="form-input" placeholder="e.g. Area 47, Behind Shoprite" required/>
          </div>

          <div class="body-base font-700" style="margin:16px 0 12px;">Payment Method</div>
          @foreach(['airtel_money'=>['fa-mobile-screen-button','#ea580c','Airtel Money','Pay with your Airtel Money account'],'mtn_momo'=>['fa-mobile-screen-button','#ca8a04','MTN MoMo','Pay with MTN Mobile Money'],'tnm_mpamba'=>['fa-mobile-screen-button','#1d4ed8','TNM Mpamba','Pay with TNM Mpamba']] as $val=>[$pIcon,$pColor,$name,$desc])
            <label class="payment-method-btn" onclick="selectPayment('{{ $val }}',this)">
              <input type="radio" name="payment_method" value="{{ $val }}" {{ $val==='airtel_money'?'checked':'' }} style="margin:0;"/>
              <i class="fas {{ $pIcon }}" style="color:{{ $pColor }};font-size:1.15rem;flex-shrink:0;"></i>
              <div><div class="body-base font-700">{{ $name }}</div><div class="body-xs" style="color:var(--text-muted);">{{ $desc }}</div></div>
            </label>
          @endforeach
          <div class="form-group" id="phoneField">
            <label class="form-label">Mobile Money Phone Number</label>
            <input type="tel" name="phone" class="form-input" placeholder="+265 99 123 4567" value="{{ Auth::user()->phone }}"/>
          </div>
          @if($errors->any())
            <div class="body-sm" style="background:#fef2f2;border:1px solid #fecaca;border-radius:var(--radius-md);padding:12px;margin-bottom:14px;color:#dc2626;">
              @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
            </div>
          @endif
          <button type="submit" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;margin-top:6px;">
            <i class="fas fa-lock"></i> Place Order — MWK {{ number_format($grandTotal) }}
          </button>
          <div class="body-xs" style="text-align:center;color:var(--text-muted);margin-top:10px;">
            <i class="fas fa-lock" style="color:var(--text-muted);"></i> Secure checkout · SMS confirmation sent after order is placed
          </div>
        </form>
      </div>
    </div>
    @endif
  </div>
</div>
@include('partials.footer')
@endsection
@section('extra_js')
<script>
// ── Payment method selection ──
function selectPayment(val, el) {
  document.querySelectorAll('.payment-method-btn').forEach(b => b.classList.remove('selected'));
  el.classList.add('selected');
  document.getElementById('phoneField').style.display = 'block';
}
document.addEventListener('DOMContentLoaded', () => {
  const selected = document.querySelector('input[name=payment_method]:checked');
  if (selected) selected.closest('.payment-method-btn').classList.add('selected');
});

// ── Trading centre autocomplete ──
const districtMap = @json($districts->mapWithKeys(fn($d) => [$d->name => $d->tradingCentres->pluck('name')]));
const townInput = document.getElementById('deliveryTown');
const townBox = document.getElementById('townSuggestions');

const subtotal = {{ $total }};
const feeSchedule = { lilongwe: 50, blantyre: 50, mzuzu: 50 };
const defaultFee = 120;

function calcDeliveryFee(district) {
  if (subtotal >= 5000) return 0;
  const key = district?.toLowerCase().trim();
  return feeSchedule[key] ?? defaultFee;
}

function updateDeliveryFee() {
  const district = document.getElementById('deliveryDistrict').value;
  const fee = calcDeliveryFee(district);
  const total = subtotal + fee;
  const feeEl = document.getElementById('deliveryFeeDisplay');
  const totalEl = document.getElementById('grandTotalDisplay');
  const btn = document.querySelector('.btn-primary.btn-lg');
  feeEl.textContent = fee > 0 ? 'MWK ' + fee.toLocaleString() : 'Included';
  totalEl.textContent = 'MWK ' + total.toLocaleString();
  if (btn) btn.innerHTML = '<i class="fas fa-lock"></i> Place Order — MWK ' + total.toLocaleString();
}

function updateTradingCentres() {
  const dist = document.getElementById('deliveryDistrict').value;
  townInput.value = '';
  townBox.style.display = 'none';
  updateDeliveryFee();
}

// Recalculate fee on district change
document.getElementById('deliveryDistrict').addEventListener('change', updateDeliveryFee);

townInput?.addEventListener('input', function() {
  const dist = document.getElementById('deliveryDistrict').value;
  if (!dist) { showToast('Please select a district first','warning'); return; }
  const centres = districtMap[dist] || [];
  const val = this.value.toLowerCase().trim();
  if (!val) { townBox.style.display = 'none'; return; }
  const matches = centres.filter(c => c.toLowerCase().includes(val));
  if (matches.length) {
    townBox.innerHTML = matches.map(m =>
      '<div onclick="selectTown(\'' + m.replace(/'/g,"\\'") + '\')">' + m + '</div>'
    ).join('');
    townBox.style.display = 'block';
  } else {
    townBox.innerHTML = '<div style="color:var(--text-muted);font-style:italic;cursor:default;">No matching town found</div>';
    townBox.style.display = 'block';
  }
});

function selectTown(name) {
  townInput.value = name;
  townBox.style.display = 'none';
}

document.addEventListener('click', e => {
  if (!townInput?.contains(e.target) && !townBox?.contains(e.target)) {
    townBox.style.display = 'none';
  }
});

// ── Keyboard shortcut: Enter confirms town suggestion ──
townInput?.addEventListener('keydown', e => {
  if (e.key === 'Enter' && townBox.style.display === 'block') {
    const first = townBox.querySelector('div');
    if (first) { first.click(); e.preventDefault(); }
  }
});
</script>
@endsection
