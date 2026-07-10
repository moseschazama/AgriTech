<div class="chatbot-widget" id="chatbotWidget" role="complementary" aria-label="AI Farming Assistant">
  <button class="chatbot-toggle" id="chatbotToggle" onclick="toggleChatbot()" title="AgriTech AI Assistant" aria-label="Toggle AI assistant chat">
    <i class="fas fa-robot"></i>
    <span class="chatbot-toggle-badge" id="chatBadge" style="display:none;">1</span>
  </button>
  <div class="chatbot-panel" id="chatbotPanel" style="display:none;" role="dialog" aria-label="AgriBot chat">
    <div class="chatbot-head">
      <div class="chatbot-head-info">
        <h4>AgriBot Assistant</h4>
        <p>AI-Powered · Always Here 🌱</p>
      </div>
      <div class="chatbot-online" aria-label="Online"></div>
      <button onclick="toggleChatbot()" aria-label="Close chat" style="background:none;border:none;color:rgba(255,255,255,.7);cursor:pointer;font-size:1.1rem;margin-left:auto;padding:4px;">✕</button>
    </div>
    <div class="chatbot-messages" id="chatMessages" role="log" aria-live="polite">
      <div class="chat-msg bot">
        <div class="avatar avatar-sm" style="background:var(--green-100);color:var(--green-700);font-size:.8rem;flex-shrink:0;" aria-hidden="true"><i class="fas fa-robot"></i></div>
        <div class="chat-bubble">
          👋 Hello{{ auth()->check() ? ', '.Auth::user()->first_name : '' }}! I'm AgriBot.
          Ask me anything about farming, crop diseases, weather, market prices, or your courses!
        </div>
      </div>
      <div style="display:flex;flex-wrap:wrap;gap:6px;padding:4px 8px;" role="group" aria-label="Quick suggestions">
        @foreach(['Maize diseases','Market prices','Irrigation tips','New courses'] as $chip)
          <button onclick="sendQuickMessage('{{ $chip }}')"
                  style="background:var(--green-50);color:var(--green-700);border:1px solid var(--green-200);border-radius:20px;padding:4px 10px;font-size:.72rem;font-weight:600;cursor:pointer;transition:all .15s;"
                  onmouseover="this.style.background='var(--green-100)'" onmouseout="this.style.background='var(--green-50)'">
            {{ $chip }}
          </button>
        @endforeach
      </div>
    </div>
    <div class="chatbot-input-row">
      <input type="text" id="chatInput" placeholder="Ask about farming..." onkeydown="if(event.key==='Enter')sendChat()" aria-label="Type your question"/>
      <button class="chatbot-send" id="chatSend" onclick="sendChat()" aria-label="Send message"><i class="fas fa-paper-plane"></i></button>
    </div>
  </div>
</div>

<script>
const agribotResponses = {
  maize:       { r: "🌽 <strong>Maize Farming Tips:</strong><br>• Plant with NPK 23:21:0+4S basal fertilizer<br>• Top dress with CAN at knee height<br>• Scout for Fall Armyworm weekly<br>• Harvest at 12-14% moisture content<br><a href='/diseases#fall-armyworm' style='color:var(--primary)'>→ View disease guides</a>", delay: 1200 },
  disease:     { r: "🔬 <strong>Crop Disease Help:</strong><br>• Upload a photo for instant AI diagnosis<br>• Get treatment steps in seconds<br>• Check active outbreak alerts<br><a href='/diseases' style='color:var(--primary)'>→ Open Disease Scanner</a>", delay: 1000 },
  price:       { r: "💰 <strong>Current Market Prices (Malawi):</strong><br>• Maize: K 350-420/50kg bag<br>• Soybeans: K 850-950/50kg<br>• Tomatoes: K 80-120/10kg box<br>• Groundnuts: K 500-600/50kg<br><a href='/marketplace' style='color:var(--primary)'>→ View marketplace</a>", delay: 900 },
  weather:     { r: "🌤️ <strong>Weather Update:</strong><br>• Lusaka: 28°C, Sunny today<br>• Blantyre: 26°C, Partly cloudy<br>• Lilongwe: 24°C, Light rain expected<br>• Best planting window: this week!<br><small>Source: Malawi Met Services</small>", delay: 800 },
  irrigation:  { r: "💧 <strong>Irrigation Tips:</strong><br>• Drip irrigation saves 40-60% water<br>• Water early morning or evening<br>• Check soil moisture before watering<br>• Consider borehole for dry season<br><a href='/learn?category=irrigation' style='color:var(--primary)'>→ Irrigation courses</a>", delay: 1000 },
  course:      { r: "🎓 <strong>Featured Courses:</strong><br>• Modern Maize Farming — FREE<br>• Drip Irrigation Setup — K 180<br>• Organic Vegetable Production — FREE<br>• Profitable Dairy Farming — K 280<br><a href='/learn' style='color:var(--primary)'>→ Browse all courses</a>", delay: 900 },
  fertilizer:  { r: "🌿 <strong>Fertilizer Guide:</strong><br>• Basal: NPK 23:21:0 — 1 bag/acre<br>• Top dressing: CAN 27%N — 1 bag/acre<br>• Organic: Compost reduces need by 30%<br>• Apply basal at planting, CAN at 4-6 weeks<br><a href='/marketplace?category=fertilizer' style='color:var(--primary)'>→ Buy fertilizer</a>", delay: 1100 },
  delivery:    { r: "🚚 <strong>Delivery Tracking:</strong><br>• Track your order in real-time on GPS<br>• SMS updates at every status change<br>• Cash on delivery available<br>• Average delivery: 1-3 business days<br><a href='/delivery' style='color:var(--primary)'>→ Track your orders</a>", delay: 800 },
  sell:        { r: "🛒 <strong>Sell on AgriTech Pro:</strong><br>• List products for FREE — no commission<br>• Reach 12,000+ buyers in Malawi & Zambia<br>• Accept Airtel Money / TNM Mpamba<br>• Delivery arranged automatically<br><a href='/marketplace' style='color:var(--primary)'>→ Start selling today</a>", delay: 900 },
  armyworm:    { r: "🐛 <strong>Fall Armyworm:</strong><br>• Look for: holes in maize leaves, frass (droppings)<br>• Treatment: Coragen, Ampligo or Orthene<br>• Apply early morning or evening<br>• 1 spray cycle = 2-3 weeks protection<br><a href='/diseases' style='color:var(--primary)'>→ Full treatment guide</a>", delay: 1100 },
  default:     { r: "🌱 I can help you with: <strong>crop diseases, market prices, weather, irrigation, fertilizers, courses, delivery tracking</strong> and more.<br><br>Try asking: <em>\"What's the price of maize?\"</em> or <em>\"How do I treat Fall Armyworm?\"</em>", delay: 700 },
};

function toggleChatbot() {
  const panel = document.getElementById('chatbotPanel');
  const badge = document.getElementById('chatBadge');
  const isOpen = panel.style.display !== 'none' && panel.classList.contains('open');
  if (isOpen) {
    panel.classList.remove('open');
    setTimeout(() => { panel.style.display = 'none'; }, 200);
  } else {
    panel.style.display = 'flex';
    panel.style.flexDirection = 'column';
    setTimeout(() => {
      panel.classList.add('open');
      document.getElementById('chatInput')?.focus();
    }, 10);
    badge.style.display = 'none';
    scrollChatBottom();
  }
}

function scrollChatBottom() {
  const msgs = document.getElementById('chatMessages');
  setTimeout(() => msgs.scrollTop = msgs.scrollHeight, 50);
}

function addMessage(text, type) {
  const msgs = document.getElementById('chatMessages');
  const div = document.createElement('div');
  div.className = `chat-msg ${type}`;
  if (type === 'bot') {
    div.innerHTML = `<div class="avatar avatar-sm" style="background:var(--green-100);color:var(--green-700);font-size:.8rem;flex-shrink:0;"><i class="fas fa-robot"></i></div><div class="chat-bubble">${text}</div>`;
  } else {
    div.innerHTML = `<div class="chat-bubble">${text}</div>`;
  }
  msgs.appendChild(div);
  scrollChatBottom();
}

function showTyping() {
  const msgs = document.getElementById('chatMessages');
  const div = document.createElement('div');
  div.className = 'chat-msg bot'; div.id = 'typingIndicator';
  div.innerHTML = `<div class="avatar avatar-sm" style="background:var(--green-100);color:var(--green-700);font-size:.8rem;flex-shrink:0;"><i class="fas fa-robot"></i></div><div class="chat-bubble"><span style="display:flex;gap:4px;align-items:center;"><span style="width:7px;height:7px;border-radius:50%;background:var(--text-muted);animation:pulse 1.2s infinite;"></span><span style="width:7px;height:7px;border-radius:50%;background:var(--text-muted);animation:pulse 1.2s .3s infinite;"></span><span style="width:7px;height:7px;border-radius:50%;background:var(--text-muted);animation:pulse 1.2s .6s infinite;"></span></span></div>`;
  msgs.appendChild(div); scrollChatBottom();
}

function removeTyping() {
  document.getElementById('typingIndicator')?.remove();
}

function getBotResponse(input) {
  const lower = input.toLowerCase();
  if (lower.includes('maize') || lower.includes('corn')) return agribotResponses.maize;
  if (lower.includes('disease') || lower.includes('sick') || lower.includes('pest') || lower.includes('detect')) return agribotResponses.disease;
  if (lower.includes('price') || lower.includes('market') || (lower.includes('sell') && lower.includes('price'))) return agribotResponses.price;
  if (lower.includes('weather') || lower.includes('rain') || lower.includes('forecast')) return agribotResponses.weather;
  if (lower.includes('irrig') || lower.includes('water') || lower.includes('drip')) return agribotResponses.irrigation;
  if (lower.includes('course') || lower.includes('learn') || lower.includes('train')) return agribotResponses.course;
  if (lower.includes('fertiliz') || lower.includes('npk') || lower.includes('compost')) return agribotResponses.fertilizer;
  if (lower.includes('deliver') || lower.includes('track') || lower.includes('order')) return agribotResponses.delivery;
  if (lower.includes('sell') || lower.includes('list') || lower.includes('marketplace')) return agribotResponses.sell;
  if (lower.includes('armyworm') || lower.includes('worm') || lower.includes('caterpillar')) return agribotResponses.armyworm;
  return agribotResponses.default;
}

function sendChat() {
  const input = document.getElementById('chatInput');
  const text = input.value.trim();
  if (!text) return;
  addMessage(text, 'user');
  input.value = '';
  const response = getBotResponse(text);
  showTyping();
  setTimeout(() => { removeTyping(); addMessage(response.r, 'bot'); }, response.delay);
}

function sendQuickMessage(text) {
  document.getElementById('chatInput').value = text;
  sendChat();
}

setTimeout(() => {
  const panel = document.getElementById('chatbotPanel');
  if (!panel.classList.contains('open')) {
    const badge = document.getElementById('chatBadge');
    badge.style.display = 'flex';
    badge.style.alignItems = 'center';
    badge.style.justifyContent = 'center';
  }
}, 8000);

document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    const panel = document.getElementById('chatbotPanel');
    if (panel.classList.contains('open')) toggleChatbot();
  }
});
</script>
