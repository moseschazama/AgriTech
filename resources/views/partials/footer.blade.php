<footer class="footer">
  <div class="container">
    <div class="footer-top">

      {{-- Brand --}}
      <div>
        <div class="footer-logo-wrap">
          <a href="{{ route('home') }}" class="nav-logo footer-logo-text">
            <div class="nav-logo-icon"><i class="fas fa-seedling"></i></div>
            <div class="nav-logo-text">
              <strong>AgriTech Pro</strong>
              <span>Smart Farming Platform</span>
            </div>
          </a>
        </div>
        <p class="footer-tagline">Empowering Malawian farmers through education, technology, trade, and innovation. Your growth is our mission.</p>
        <div class="footer-social">
          <a href="#" class="footer-social-btn" title="Facebook"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="footer-social-btn" title="Twitter/X"><i class="fab fa-twitter"></i></a>
          <a href="#" class="footer-social-btn" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
          <a href="#" class="footer-social-btn" title="YouTube"><i class="fab fa-youtube"></i></a>
          <a href="#" class="footer-social-btn" title="Instagram"><i class="fab fa-instagram"></i></a>
        </div>
      </div>

      {{-- Platform --}}
      <div class="footer-col">
        <h4>Platform</h4>
        <div class="footer-links">
          <a href="{{ route('learn') }}"       class="footer-link"><i class="fas fa-chevron-right"></i> Learning Center</a>
          <a href="{{ route('marketplace') }}" class="footer-link"><i class="fas fa-chevron-right"></i> Marketplace</a>
          <a href="{{ route('innovation') }}"  class="footer-link"><i class="fas fa-chevron-right"></i> Innovation Hub</a>
          <a href="{{ route('delivery') }}"    class="footer-link"><i class="fas fa-chevron-right"></i> Delivery Tracking</a>
          <a href="{{ route('diseases') }}"    class="footer-link"><i class="fas fa-chevron-right"></i> Disease Detection</a>
          @auth
            <a href="{{ route('dashboard') }}" class="footer-link"><i class="fas fa-chevron-right"></i> Farmer Dashboard</a>
            @if(Auth::user()->isAdmin())
              <a href="{{ route('admin.index') }}" class="footer-link"><i class="fas fa-chevron-right"></i> Admin Panel</a>
            @endif
          @else
            <a href="{{ route('login') }}"    class="footer-link"><i class="fas fa-chevron-right"></i> Sign In</a>
            <a href="{{ route('register') }}" class="footer-link"><i class="fas fa-chevron-right"></i> Create Account</a>
          @endauth
        </div>
      </div>

      {{-- Resources --}}
      <div class="footer-col">
        <h4>Resources</h4>
        <div class="footer-links">
          <a href="{{ route('diseases') }}"   class="footer-link"><i class="fas fa-chevron-right"></i> Crop Calendar</a>
          <a href="{{ route('diseases') }}"   class="footer-link"><i class="fas fa-chevron-right"></i> Weather Forecasts</a>
          <a href="{{ route('diseases') }}"   class="footer-link"><i class="fas fa-chevron-right"></i> Disease Library</a>
          <a href="{{ route('marketplace') }}" class="footer-link"><i class="fas fa-chevron-right"></i> Market Prices</a>
          <a href="{{ route('innovation') }}" class="footer-link"><i class="fas fa-chevron-right"></i> Farmer Community</a>
          <a href="{{ route('learn') }}"      class="footer-link"><i class="fas fa-chevron-right"></i> Blog & News</a>
        </div>
      </div>

      {{-- Company --}}
      <div class="footer-col">
        <h4>Company</h4>
        <div class="footer-links">
          <a href="#" class="footer-link"><i class="fas fa-chevron-right"></i> About Us</a>
          <a href="#" class="footer-link"><i class="fas fa-chevron-right"></i> Careers</a>
          <a href="#" class="footer-link"><i class="fas fa-chevron-right"></i> Partners</a>
          <a href="#" class="footer-link"><i class="fas fa-chevron-right"></i> Press Kit</a>
          <a href="#" class="footer-link"><i class="fas fa-chevron-right"></i> Privacy Policy</a>
          <a href="#" class="footer-link"><i class="fas fa-chevron-right"></i> Terms of Service</a>
        </div>
      </div>

      {{-- Newsletter --}}
      <div class="footer-col footer-newsletter">
        <h4>Stay Updated</h4>
        <p>Get farming tips, alerts and market prices via SMS & email.</p>
        <form method="POST" action="{{ route('diseases.subscribe') }}" id="footerNewsletterForm">
          @csrf
          <div class="footer-newsletter-form">
            <input type="email" name="email" placeholder="your@email.com" id="newsletterEmail" required/>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-paper-plane"></i></button>
          </div>
        </form>
        <div class="footer-sms-note">
          <i class="fas fa-sms"></i>
          <span>Text <strong>JOIN</strong> to <strong>1212</strong> to receive SMS farming alerts</span>
        </div>
        <div style="margin-top:20px;">
          <h4 style="margin-bottom:10px;">Contact</h4>
          <div class="footer-links">
            <a href="tel:+26599450275" class="footer-link"><i class="fas fa-phone"></i> +265 999 450 275</a>
            <a href="mailto:hello@agritechpro.mw" class="footer-link"><i class="fas fa-envelope"></i> hello@agritechpro.mw</a>
            <span class="footer-link"><i class="fas fa-map-marker-alt"></i> Dowa, Malawi</span>
          </div>
        </div>
      </div>

    </div>
    <div class="footer-bottom">
      <span>© {{ date('Y') }} AgriTech Pro. All rights reserved. Built for Malawian farmers.</span>
      <div class="footer-bottom-links">
        <a href="#">Privacy</a>
        <a href="#">Terms</a>
        <a href="#">Cookies</a>
        <a href="#">Accessibility</a>
      </div>
    </div>
  </div>
</footer>

<script>
document.getElementById('footerNewsletterForm')?.addEventListener('submit', function(e) {
  e.preventDefault();
  const email = document.getElementById('newsletterEmail').value.trim();
  if (!email) return;
  showToast('✅ You\'re subscribed! Check your email.', 'success');
  this.reset();
});
</script>
