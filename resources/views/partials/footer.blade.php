<footer class="footer" style="position:relative;overflow:hidden;">
  {{-- Subtle top glow --}}
  <div style="position:absolute;top:-1px;left:50%;transform:translateX(-50%);width:400px;height:2px;background:linear-gradient(90deg,transparent,var(--primary),transparent);opacity:.4;"></div>

  <div class="container" style="padding:48px 20px 28px;">
    {{-- Main grid --}}
    <div class="footer-grid" style="display:grid;grid-template-columns:1.4fr 1fr 1fr auto;gap:40px;align-items:start;margin-bottom:40px;">

      {{-- Brand --}}
      <div>
        <a href="{{ route('home') }}" style="display:inline-flex;align-items:center;gap:10px;text-decoration:none;margin-bottom:14px;">
          <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,var(--green-500),var(--green-700));color:#fff;display:flex;align-items:center;justify-content:center;font-size:.95rem;box-shadow:0 4px 12px rgba(22,163,74,.3);">
            <i class="fas fa-seedling"></i>
          </div>
          <div>
            <span style="font-weight:800;font-size:1.05rem;color:#fff;letter-spacing:-.01em;">AgriTech Pro</span>
            <span style="display:block;font-size:.68rem;color:var(--gray-400);font-weight:500;letter-spacing:.04em;text-transform:uppercase;">Smart Farming Platform</span>
          </div>
        </a>
        <p style="font-size:.82rem;color:var(--gray-400);line-height:1.6;max-width:280px;margin-bottom:18px;">Empowering Malawian farmers with practical tools, real-time markets, and hands-on learning.</p>
        <div style="display:flex;gap:6px;">
          <a href="#" title="WhatsApp" style="width:34px;height:34px;border-radius:8px;border:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;color:var(--gray-400);text-decoration:none;font-size:.82rem;transition:all .2s;" onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)';this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='rgba(255,255,255,.08)';this.style.color='var(--gray-400)';this.style.transform='none'"><i class="fab fa-whatsapp"></i></a>
          <a href="#" title="Facebook" style="width:34px;height:34px;border-radius:8px;border:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;color:var(--gray-400);text-decoration:none;font-size:.82rem;transition:all .2s;" onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)';this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='rgba(255,255,255,.08)';this.style.color='var(--gray-400)';this.style.transform='none'"><i class="fab fa-facebook-f"></i></a>
          <a href="#" title="X" style="width:34px;height:34px;border-radius:8px;border:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;color:var(--gray-400);text-decoration:none;font-size:.82rem;transition:all .2s;" onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)';this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='rgba(255,255,255,.08)';this.style.color='var(--gray-400)';this.style.transform='none'"><i class="fab fa-x-twitter"></i></a>
          <a href="#" title="YouTube" style="width:34px;height:34px;border-radius:8px;border:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;color:var(--gray-400);text-decoration:none;font-size:.82rem;transition:all .2s;" onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)';this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='rgba(255,255,255,.08)';this.style.color='var(--gray-400)';this.style.transform='none'"><i class="fab fa-youtube"></i></a>
        </div>
      </div>

      {{-- Platform --}}
      <div>
        <h4 style="font-size:.68rem;font-weight:700;color:var(--gray-400);text-transform:uppercase;letter-spacing:.08em;margin-bottom:16px;">Platform</h4>
        <div style="display:flex;flex-direction:column;gap:10px;">
          <a href="{{ route('learn') }}" style="color:var(--gray-300);text-decoration:none;font-size:.82rem;font-weight:500;transition:color .2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--gray-300)'">Courses</a>
          <a href="{{ route('marketplace') }}" style="color:var(--gray-300);text-decoration:none;font-size:.82rem;font-weight:500;transition:color .2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--gray-300)'">Marketplace</a>
          <a href="{{ route('innovation') }}" style="color:var(--gray-300);text-decoration:none;font-size:.82rem;font-weight:500;transition:color .2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--gray-300)'">Innovation Hub</a>
          <a href="{{ route('diseases') }}" style="color:var(--gray-300);text-decoration:none;font-size:.82rem;font-weight:500;transition:color .2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--gray-300)'">Disease Detection</a>
          <a href="{{ route('delivery') }}" style="color:var(--gray-300);text-decoration:none;font-size:.82rem;font-weight:500;transition:color .2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--gray-300)'">Delivery Tracking</a>
        </div>
      </div>

      {{-- Account --}}
      <div>
        <h4 style="font-size:.68rem;font-weight:700;color:var(--gray-400);text-transform:uppercase;letter-spacing:.08em;margin-bottom:16px;">Account</h4>
        <div style="display:flex;flex-direction:column;gap:10px;">
          @auth
            <a href="{{ route('dashboard') }}" style="color:var(--gray-300);text-decoration:none;font-size:.82rem;font-weight:500;transition:color .2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--gray-300)'">Dashboard</a>
            @if(Auth::user()->isAdmin())
              <a href="{{ route('admin.index') }}" style="color:var(--gray-300);text-decoration:none;font-size:.82rem;font-weight:500;transition:color .2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--gray-300)'">Admin Panel</a>
            @endif
          @else
            <a href="{{ route('login') }}" style="color:var(--gray-300);text-decoration:none;font-size:.82rem;font-weight:500;transition:color .2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--gray-300)'">Sign In</a>
            <a href="{{ route('register') }}" style="display:inline-flex;align-items:center;gap:5px;padding:7px 16px;border-radius:8px;background:var(--primary);color:#fff;text-decoration:none;font-size:.78rem;font-weight:600;width:fit-content;transition:all .2s;" onmouseover="this.style.opacity='.9';this.style.transform='translateY(-1px)'" onmouseout="this.style.opacity='1';this.style.transform='none'"><i class="fas fa-arrow-right" style="font-size:.65rem;"></i> Get Started</a>
          @endauth
        </div>
      </div>

      {{-- Contact --}}
      <div>
        <h4 style="font-size:.68rem;font-weight:700;color:var(--gray-400);text-transform:uppercase;letter-spacing:.08em;margin-bottom:16px;">Contact</h4>
        <div style="display:flex;flex-direction:column;gap:10px;">
          <a href="tel:+26599450275" style="color:var(--gray-300);text-decoration:none;font-size:.82rem;font-weight:500;display:flex;align-items:center;gap:6px;transition:color .2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--gray-300)'"><i class="fas fa-phone" style="font-size:.7rem;color:var(--green-500);"></i> +265 999 450 275</a>
          <a href="mailto:hello@agritechpro.mw" style="color:var(--gray-300);text-decoration:none;font-size:.82rem;font-weight:500;display:flex;align-items:center;gap:6px;transition:color .2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--gray-300)'"><i class="fas fa-envelope" style="font-size:.7rem;color:var(--green-500);"></i> hello@agritechpro.mw</a>
          <span style="color:var(--gray-300);font-size:.82rem;font-weight:500;display:flex;align-items:center;gap:6px;"><i class="fas fa-location-dot" style="font-size:.7rem;color:var(--green-500);"></i> Dowa, Malawi</span>
        </div>
      </div>

    </div>

    {{-- Bottom --}}
    <div style="border-top:1px solid rgba(255,255,255,.06);padding-top:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
      <span style="font-size:.72rem;color:var(--gray-500);">&copy; {{ date('Y') }} AgriTech Pro. Built for Malawian farmers.</span>
      <div style="display:flex;gap:16px;">
        <a href="#" style="font-size:.72rem;color:var(--gray-500);text-decoration:none;transition:color .2s;" onmouseover="this.style.color='var(--gray-300)'" onmouseout="this.style.color='var(--gray-500)'">Privacy</a>
        <a href="#" style="font-size:.72rem;color:var(--gray-500);text-decoration:none;transition:color .2s;" onmouseover="this.style.color='var(--gray-300)'" onmouseout="this.style.color='var(--gray-500)'">Terms</a>
      </div>
    </div>
  </div>
</footer>

<style>
@media(max-width:768px){
  .footer-grid{grid-template-columns:1fr 1fr!important;gap:28px!important;}
}
@media(max-width:520px){
  .footer-grid{grid-template-columns:1fr!important;gap:24px!important;}
}
</style>
