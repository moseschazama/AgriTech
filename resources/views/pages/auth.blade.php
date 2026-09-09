@extends('layouts.app')
@section('title', 'Sign In / Register — AgriTech Pro')

@php
  $tab = (old('_form') === 'register' || $errors->has('first_name') || $errors->has('farm_type') || $errors->has('phone'))
      ? 'register'
      : ($activeTab ?? 'login');
@endphp

@section('extra_css')
  <link rel="stylesheet" href="{{ asset('css/home.css') }}"/>
  <style>
    body { background: var(--bg-2); min-height: 100vh; display: flex; flex-direction: column; }
    .auth-page {
      flex: 1; display: grid; grid-template-columns: 1fr 1fr;
      min-height: calc(100vh - var(--nav-h));
    }
    .auth-left {
      background: linear-gradient(145deg, #1e293b, #334155, #1e293b);
      display: flex; flex-direction: column;
      justify-content: center; padding: 60px 64px;
      position: relative; overflow: hidden;
    }
    .auth-left::before {
      content: '';
      position: absolute; inset: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .auth-blob {
      position: absolute; border-radius: 50%;
      filter: blur(60px); pointer-events: none;
    }
    .auth-blob-1 { width: 300px; height: 300px; background: rgba(74,222,128,0.25); top: -80px; right: -60px; }
    .auth-blob-2 { width: 200px; height: 200px; background: rgba(250,204,21,0.15); bottom: 60px; left: 40px; }
    .auth-left-content { position: relative; z-index: 1; }
    .auth-logo { display: flex; align-items: center; gap: 12px; margin-bottom: 48px; text-decoration: none; }
    .auth-logo-icon {
      width: 44px; height: 44px; background: rgba(255,255,255,0.2);
      border-radius: 12px; display: flex; align-items: center; justify-content: center;
      font-size: 1.3rem; color: #fff;
    }
    .auth-logo strong { font-size: 1.3rem; color: #fff; font-weight: 800; }
    .auth-hero-title {
      font-size: clamp(1.8rem, 3vw, 2.6rem);
      font-weight: 800; color: #fff; line-height: 1.2;
      margin-bottom: 18px; letter-spacing: -0.02em;
    }
    .auth-hero-desc { color: rgba(255,255,255,0.75); font-size: 1rem; line-height: 1.7; margin-bottom: 40px; max-width: 440px; }
    .auth-features { display: flex; flex-direction: column; gap: 14px; }
    .auth-feature {
      display: flex; align-items: center; gap: 12px;
      color: rgba(255,255,255,0.85); font-size: 0.92rem;
    }
    .auth-feature-icon {
      width: 36px; height: 36px; border-radius: 10px;
      background: rgba(255,255,255,0.12);
      display: flex; align-items: center; justify-content: center;
      font-size: 1rem; flex-shrink: 0;
    }
    .auth-stats-row {
      display: flex; gap: 32px; margin-top: 40px;
      padding-top: 32px; border-top: 1px solid rgba(255,255,255,0.15);
    }
    .auth-stat { text-align: left; }
    .auth-stat-val { font-size: 1.5rem; font-weight: 800; color: #fff; }
    .auth-stat-lbl { font-size: 0.75rem; color: rgba(255,255,255,0.65); }

    .auth-right {
      display: flex; align-items: center; justify-content: center;
      padding: 48px 40px; background: var(--bg);
    }
    .auth-box { width: 100%; max-width: 440px; }
    .auth-tabs {
      display: flex; background: var(--bg-2);
      border-radius: var(--radius-lg); padding: 4px;
      margin-bottom: 32px; border: 1px solid var(--border);
    }
    .auth-tab {
      flex: 1; padding: 10px; border-radius: var(--radius-md);
      font-size: 0.9rem; font-weight: 600; color: var(--text-muted);
      text-align: center; cursor: pointer; transition: all var(--t-fast);
    }
    .auth-tab.active {
      background: var(--bg-card); color: var(--primary);
      box-shadow: var(--shadow-sm);
    }
    .auth-form { display: none; flex-direction: column; gap: 20px; }
    .auth-form.active { display: flex; }
    .auth-heading { font-size: 1.5rem; font-weight: 800; color: var(--text); margin-bottom: 4px; }
    .auth-subheading { font-size: 0.88rem; color: var(--text-muted); margin-bottom: 8px; }
    .auth-divider {
      display: flex; align-items: center; gap: 12px; color: var(--text-muted); font-size: 0.8rem;
    }
    .auth-divider::before, .auth-divider::after {
      content: ''; flex: 1; height: 1px; background: var(--border);
    }
    .social-login-btns { display: flex; gap: 10px; }
    .social-login-btn {
      flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px;
      padding: 10px; border: 1.5px solid var(--border); border-radius: var(--radius-md);
      font-size: 0.85rem; font-weight: 600; color: var(--text);
      background: var(--bg-card); transition: all var(--t-fast);
    }
    .social-login-btn:hover { border-color: var(--primary); color: var(--primary); background: var(--green-50); }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .forgot-link { font-size: 0.82rem; color: var(--primary); font-weight: 600; text-align: right; display: block; }
    .auth-agree { font-size: 0.8rem; color: var(--text-muted); line-height: 1.55; }
    .auth-agree a { color: var(--primary); font-weight: 600; }
    .password-wrap { position: relative; }
    .password-toggle {
      position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
      color: var(--text-muted); font-size: 0.9rem; cursor: pointer;
      transition: color var(--t-fast);
    }
    .password-toggle:hover { color: var(--primary); }
    .input-wrap { position: relative; }
    .form-input-icon { padding-left: 40px !important; }
    .input-icon {
      position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
      color: var(--text-muted); font-size: 0.85rem; pointer-events: none;
    }
    @media (max-width: 900px) {
      .auth-page { grid-template-columns: 1fr; }
      .auth-left { display: none; }
      .auth-right { padding: 32px 20px; align-items: flex-start; }
    }
    .field-error {
      display: block; color: #ef4444; font-size: .76rem; margin-top: 4px;
    }
    .form-error-banner {
      display: flex; align-items: center; gap: 8px;
      background: #fef2f2; border: 1px solid #fecaca; color: #dc2626;
      font-size: .85rem; padding: 10px 14px; border-radius: var(--radius-md);
    }
  </style>
@endsection

@section('content')
<div class="auth-page">
  <div class="auth-left">
    <div class="auth-blob auth-blob-1"></div>
    <div class="auth-blob auth-blob-2"></div>
    <div class="auth-left-content">
      <a href="{{ route('home') }}" class="auth-logo">
        <div class="auth-logo-icon"><i class="fas fa-seedling"></i></div>
        <strong>AgriTech Pro</strong>
      </a>
      <h1 class="auth-hero-title">Grow More.<br/>Earn More.<br/>Farm Smarter.</h1>
      <p class="auth-hero-desc">Join 12,000+ farmers across Africa who are transforming their farms with modern technology, knowledge and direct market access.</p>
      <div class="auth-features">
        <div class="auth-feature">
          <div class="auth-feature-icon"><i class="fas fa-graduation-cap"></i></div>
          <span>Access 300+ farming courses — many completely free</span>
        </div>
        <div class="auth-feature">
          <div class="auth-feature-icon"><i class="fas fa-store"></i></div>
          <span>Buy and sell on the Agri Marketplace — no middlemen</span>
        </div>
        <div class="auth-feature">
          <div class="auth-feature-icon"><i class="fas fa-truck"></i></div>
          <span>Real-time delivery tracking with SMS updates</span>
        </div>
        <div class="auth-feature">
          <div class="auth-feature-icon"><i class="fas fa-robot"></i></div>
          <span>Farming assistant available 24/7</span>
        </div>
        <div class="auth-feature">
          <div class="auth-feature-icon"><i class="fas fa-cloud-sun"></i></div>
          <span>Hyper-local weather forecasts by district</span>
        </div>
      </div>
      <div class="auth-stats-row">
        <div class="auth-stat"><div class="auth-stat-val">12K+</div><div class="auth-stat-lbl">Farmers</div></div>
        <div class="auth-stat"><div class="auth-stat-val">300+</div><div class="auth-stat-lbl">Courses</div></div>
        <div class="auth-stat"><div class="auth-stat-val">K2.4M</div><div class="auth-stat-lbl">Traded</div></div>
      </div>
    </div>
  </div>

  <div class="auth-right">
    <div class="auth-box">
      <div class="auth-tabs">
        <div class="auth-tab {{ $tab === 'login' ? 'active' : '' }}" data-tab="login">Sign In</div>
        <div class="auth-tab {{ $tab === 'register' ? 'active' : '' }}" data-tab="register">Create Account</div>
      </div>

      <form class="auth-form {{ $tab === 'login' ? 'active' : '' }}" id="loginForm" data-form="login"
            action="{{ route('login.submit') }}" method="POST" novalidate>
        @csrf
        <div>
          <div class="auth-heading">Welcome back 👋</div>
          <div class="auth-subheading">Sign in to your AgriTech Pro account</div>
        </div>

        @if ($errors->has('identifier') && old('_form') === 'login')
          <div class="form-error-banner">
            <i class="fas fa-exclamation-circle"></i> {{ $errors->first('identifier') }}
          </div>
        @endif

        <input type="hidden" name="_form" value="login"/>

        <div class="social-login-btns">
          <button type="button" class="social-login-btn" onclick="showToast('Google login coming soon!','info')"><i class="fab fa-google" style="color:#ea4335;"></i> Google</button>
          <button type="button" class="social-login-btn" onclick="showToast('Facebook login coming soon!','info')"><i class="fab fa-facebook" style="color:#1877f2;"></i> Facebook</button>
        </div>
        <div class="auth-divider">or sign in with email / phone</div>

        <div class="form-group">
          <label class="form-label"><i class="fas fa-envelope"></i> Email or Phone</label>
          <div class="input-wrap">
            <i class="input-icon fas fa-envelope"></i>
            <input type="text" name="identifier" class="form-input form-input-icon"
                   placeholder="email@example.com or +265..." id="loginEmail"
                   value="{{ old('identifier') }}" required autofocus/>
          </div>
          @error('identifier') <span class="field-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
          <label class="form-label"><i class="fas fa-lock"></i> Password</label>
          <div class="input-wrap password-wrap">
            <i class="input-icon fas fa-lock"></i>
            <input type="password" name="password" class="form-input form-input-icon"
                   placeholder="Enter your password" id="loginPass" required/>
            <span class="password-toggle" onclick="togglePass('loginPass', this)"><i class="fas fa-eye"></i></span>
          </div>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;">
          <label style="display:flex;align-items:center;gap:8px;font-size:.85rem;color:var(--text-muted);cursor:pointer;">
            <input type="checkbox" name="remember" value="1" style="width:auto;accent-color:var(--primary);"/> Remember me
          </label>
          <a href="#" class="forgot-link" onclick="showForgotPassword()">Forgot password?</a>
        </div>

        <button type="submit" class="btn btn-primary btn-md" style="width:100%;justify-content:center;">
          <i class="fas fa-sign-in-alt"></i> Sign In
        </button>
        <p style="font-size:.82rem;color:var(--text-muted);text-align:center;">
          Don't have an account? <a href="#" style="color:var(--primary);font-weight:700;" onclick="switchTab('register')">Create one free →</a>
        </p>
      </form>

      {{-- ── Forgot Password Overlay ── --}}
      <div id="forgotPasswordOverlay" style="display:none;flex-direction:column;gap:16px;">
        <div>
          <div class="auth-heading">Reset Password 🔑</div>
          <div class="auth-subheading">Enter your phone number to receive a reset code via SMS</div>
        </div>
        <form method="POST" action="{{ route('password.send-otp') }}" id="forgotPhoneForm">
          @csrf
          <div class="form-group">
            <label class="form-label"><i class="fas fa-phone"></i> Phone Number</label>
            <input type="tel" name="phone" class="form-input" placeholder="+265 99 123 4567" required/>
          </div>
          <button type="submit" class="btn btn-primary btn-md" style="width:100%;justify-content:center;"><i class="fas fa-paper-plane"></i> Send Reset Code</button>
        </form>
        <form method="POST" action="{{ route('password.verify-otp') }}" id="forgotVerifyForm" style="display:none;flex-direction:column;gap:12px;margin-top:8px;">
          @csrf
          <input type="hidden" name="phone" id="forgotPhoneHidden"/>
          <div class="form-group">
            <label class="form-label"><i class="fas fa-key"></i> SMS Code</label>
            <input type="text" name="code" class="form-input" placeholder="6-digit code" maxlength="6" required/>
          </div>
          <div class="form-group">
            <label class="form-label"><i class="fas fa-lock"></i> New Password</label>
            <input type="password" name="password" class="form-input" placeholder="At least 8 characters" minlength="8" required/>
          </div>
          <div class="form-group">
            <label class="form-label"><i class="fas fa-lock"></i> Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-input" placeholder="Repeat password" minlength="8" required/>
          </div>
          <button type="submit" class="btn btn-primary btn-md" style="width:100%;justify-content:center;"><i class="fas fa-check"></i> Reset Password</button>
        </form>
        <div style="text-align:center;">
          <a href="#" style="color:var(--primary);font-size:.85rem;font-weight:600;" onclick="showLoginForm()"><i class="fas fa-arrow-left"></i> Back to Sign In</a>
        </div>
      </div>

      <form class="auth-form {{ $tab === 'register' ? 'active' : '' }}" id="registerForm" data-form="register"
            action="{{ route('register.submit') }}" method="POST" novalidate>
        @csrf
        <div>
          <div class="auth-heading">Join AgriTech Pro 🌱</div>
          <div class="auth-subheading">Free forever — no credit card required</div>
        </div>

        @if($errors->any() && old('_form') === 'register')
          <div class="form-error-banner"><i class="fas fa-exclamation-circle"></i> Please fix the errors below to continue.</div>
        @endif

        <input type="hidden" name="_form" value="register"/>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label"><i class="fas fa-user"></i> First Name</label>
            <input type="text" name="first_name" class="form-input"
                   placeholder="John" value="{{ old('first_name') }}" required/>
            @error('first_name') <span class="field-error">{{ $message }}</span> @enderror
          </div>
          <div class="form-group">
            <label class="form-label"><i class="fas fa-user"></i> Last Name</label>
            <input type="text" name="last_name" class="form-input"
                   placeholder="Mutale" value="{{ old('last_name') }}" required/>
            @error('last_name') <span class="field-error">{{ $message }}</span> @enderror
          </div>
        </div>

        <div class="form-group">
          <label class="form-label"><i class="fas fa-envelope"></i> Email Address <span style="color:var(--text-muted);font-size:.75rem;">(optional — alternative login)</span></label>
          <input type="email" name="email" class="form-input" placeholder="john@example.com"
                 value="{{ old('email') }}"/>
          @error('email') <span class="field-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
          <label class="form-label"><i class="fas fa-phone"></i> Phone Number <span style="color:var(--text-muted);font-size:.75rem;">(for SMS alerts)</span></label>
          <div class="input-wrap">
            <input type="tel" name="phone" class="form-input" placeholder="+265 99 123 4567"
                   value="{{ old('phone') }}" required/>
          </div>
          @error('phone') <span class="field-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label"><i class="fas fa-map-marker-alt"></i> District</label>
            <select name="district" class="form-input district-select form-select" required>
              <option value="">Select district</option>
              @foreach($districts as $district)
                <option value="{{ $district->name }}" {{ old('district')==$district->name ? 'selected' : '' }}>{{ $district->name }}</option>
              @endforeach
            </select>
            @error('district') <span class="field-error">{{ $message }}</span> @enderror
          </div>
          <input type="hidden" name="region" id="regionInput" value="{{ old('region') }}">
          <div class="form-group">
            <label class="form-label">Nearest Trading Centre / Town</label>
            <input type="text" name="trading_centre" value="{{ old('trading_centre') }}" class="form-input"
                   placeholder="Start typing to search..." id="tradingCentreInput" autocomplete="off">
            <div id="tcSuggestions" style="display:none;position:absolute;z-index:100;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);max-height:200px;overflow-y:auto;width:100%;"></div>
          </div>
          <div class="form-group">
            <label class="form-label"><i class="fas fa-seedling"></i> Farm Type</label>
            <select name="farm_type" class="form-input form-select" required>
              <option value="">Select type</option>
              <option value="small_scale"  @selected(old('farm_type') === 'small_scale')>Small-scale farmer</option>
              <option value="commercial"   @selected(old('farm_type') === 'commercial')>Commercial farmer</option>
              <option value="livestock"    @selected(old('farm_type') === 'livestock')>Livestock farmer</option>
              <option value="mixed"        @selected(old('farm_type') === 'mixed')>Mixed farmer</option>
              <option value="organic"      @selected(old('farm_type') === 'organic')>Organic farmer</option>
              <option value="agribusiness" @selected(old('farm_type') === 'agribusiness')>Agribusiness</option>
            </select>
            @error('farm_type') <span class="field-error">{{ $message }}</span> @enderror
          </div>
        </div>

        <div class="form-group">
          <label class="form-label"><i class="fas fa-lock"></i> Password</label>
          <div class="input-wrap password-wrap">
            <input type="password" name="password" class="form-input" placeholder="At least 8 characters" id="regPass" required/>
            <span class="password-toggle" onclick="togglePass('regPass', this)"><i class="fas fa-eye"></i></span>
          </div>
          @error('password') <span class="field-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
          <label class="form-label"><i class="fas fa-lock"></i> Confirm Password</label>
          <div class="input-wrap password-wrap">
            <input type="password" name="password_confirmation" class="form-input" placeholder="Repeat your password" id="regPass2" required/>
            <span class="password-toggle" onclick="togglePass('regPass2', this)"><i class="fas fa-eye"></i></span>
          </div>
        </div>

        <p class="auth-agree">
          By creating an account you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.
          We may send you farming tips and alerts via SMS.
        </p>

        <button type="submit" class="btn btn-primary btn-md" style="width:100%;justify-content:center;">
          <i class="fas fa-seedling"></i> Create Free Account
        </button>
        <p style="font-size:.82rem;color:var(--text-muted);text-align:center;">
          Already have an account? <a href="#" style="color:var(--primary);font-weight:700;" onclick="switchTab('login')">Sign in →</a>
        </p>
      </form>
    </div>
  </div>
</div>
@endsection

@section('extra_js')
<script>
function togglePass(id, btn) {
  const inp = document.getElementById(id) || btn.closest('.password-wrap').querySelector('input');
  if (!inp) return;
  const show = inp.type === 'password';
  inp.type = show ? 'text' : 'password';
  btn.innerHTML = show ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
}

function switchTab(tab) {
  document.querySelectorAll('.auth-tab').forEach(t => t.classList.toggle('active', t.dataset.tab === tab));
  document.querySelectorAll('.auth-form').forEach(f => f.classList.toggle('active', f.dataset.form === tab));
  document.getElementById('forgotPasswordOverlay').style.display = 'none';
  history.replaceState(null, '', tab === 'register' ? '#register' : '#');
}

function showForgotPassword() {
  document.getElementById('loginForm').style.display = 'none';
  document.getElementById('forgotPasswordOverlay').style.display = 'flex';
}

function showLoginForm() {
  document.getElementById('forgotPasswordOverlay').style.display = 'none';
  document.getElementById('loginForm').style.display = 'flex';
}

// Handle OTP send — show verify form on success
document.addEventListener('DOMContentLoaded', function() {
  @if(session('success') && str_contains(session('success'), 'code'))
    showForgotPassword();
    document.getElementById('forgotPhoneForm').style.display = 'none';
    document.getElementById('forgotVerifyForm').style.display = 'flex';
    document.getElementById('forgotPhoneHidden').value = '{{ old("phone") }}';
  @endif
});

document.getElementById('forgotPhoneForm')?.addEventListener('submit', function(e) {
  const phone = this.querySelector('[name=phone]').value.trim();
  if (phone.length < 5) { e.preventDefault(); showToast('Please enter a valid phone number','error'); return; }
  document.getElementById('forgotPhoneHidden').value = phone;
});

document.getElementById('forgotVerifyForm')?.addEventListener('submit', function(e) {
  const pass = this.querySelector('[name=password]').value;
  const confirm = this.querySelector('[name=password_confirmation]').value;
  if (pass !== confirm) { e.preventDefault(); showToast('Passwords do not match','error'); return; }
  if (pass.length < 8) { e.preventDefault(); showToast('Password must be at least 8 characters','error'); return; }
});

document.querySelectorAll('.auth-tab').forEach(tab => {
  tab.addEventListener('click', () => switchTab(tab.dataset.tab));
});

@if ($tab === 'login')
if (window.location.hash === '#register') switchTab('register');
@endif

const districtMap = @json($districts->mapWithKeys(fn($d) => [$d->name => ['region' => $d->region, 'centres' => $d->tradingCentres->pluck('name')]]));

const districtSelects = document.querySelectorAll('select[name="district"]');
const regionInput = document.getElementById('regionInput');

districtSelects.forEach(function(sel) {
    sel.addEventListener('change', function() {
        const data = districtMap[this.value];
        if (data && regionInput) {
            regionInput.value = data.region;
            updateTcSuggestions(data.centres);
        }
    });
    if (sel.value && districtMap[sel.value] && regionInput) {
        regionInput.value = districtMap[sel.value].region;
    }
});

const tcInput = document.getElementById('tradingCentreInput');
const tcBox = document.getElementById('tcSuggestions');
let allCentres = [];

function updateTcSuggestions(districtCentres) {
    allCentres = districtCentres || [];
    if (allCentres.length === 0) {
        Object.values(districtMap).forEach(d => allCentres = allCentres.concat(d.centres));
    }
}

Object.values(districtMap).forEach(d => allCentres = allCentres.concat(d.centres));

if (tcInput && tcBox) {
    tcInput.addEventListener('input', function() {
        const q = this.value.toLowerCase().trim();
        if (q.length < 1) { tcBox.style.display = 'none'; return; }
        const matches = allCentres.filter(c => c.toLowerCase().includes(q)).slice(0, 8);
        if (matches.length === 0) { tcBox.style.display = 'none'; return; }
        tcBox.innerHTML = matches.map(m => '<div style="padding:10px 14px;cursor:pointer;font-size:.88rem;border-bottom:1px solid var(--border);" onmouseover="this.style.background=\'var(--bg-2)\'" onmouseout="this.style.background=\'\'" onclick="document.getElementById(\'tradingCentreInput\').value=\'' + m.replace(/'/g, "\\'") + '\';document.getElementById(\'tcSuggestions\').style.display=\'none\'">' + m + '</div>').join('');
        tcBox.style.display = 'block';
        tcBox.style.width = tcInput.offsetWidth + 'px';
    });
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#tradingCentreInput') && !e.target.closest('#tcSuggestions')) {
            tcBox.style.display = 'none';
        }
    });
}
</script>
@endsection
