<div class="chatbot-widget" id="chatbotWidget" role="complementary" aria-label="Farming Assistant">
  <button class="chatbot-toggle" id="chatbotToggle" onclick="toggleChatbot()" title="AgriBot Assistant" aria-label="Toggle assistant chat">
    <i class="fas fa-robot"></i>
    <span class="chatbot-toggle-badge" id="chatBadge" style="display:none;">1</span>
  </button>
  <div class="chatbot-panel" id="chatbotPanel" style="display:none;" role="dialog" aria-label="AgriBot chat">
    <div class="chatbot-head">
      <div class="chatbot-head-info">
        <h4>AgriBot Assistant</h4>
        <p>Farming Help · Always Here </p>
      </div>
      <div class="chatbot-online" aria-label="Online"></div>
      <button onclick="toggleChatbot()" aria-label="Close chat" style="background:none;border:none;color:rgba(255,255,255,.7);cursor:pointer;font-size:1.1rem;margin-left:auto;padding:4px;">✕</button>
    </div>
    <div class="chatbot-messages" id="chatMessages" role="log" aria-live="polite">
      <div class="chat-msg bot">
        <div class="avatar avatar-sm" style="background:var(--green-100);color:var(--green-700);font-size:.8rem;flex-shrink:0;" aria-hidden="true"><i class="fas fa-robot"></i></div>
        <div class="chat-bubble">
          Hello{{ auth()->check() ? ', '.Auth::user()->first_name : '' }}! I'm AgriBot.
          Ask me anything about farming, crop diseases, weather, market prices, or your courses!
        </div>
      </div>
      <div style="display:flex;flex-wrap:wrap;gap:6px;padding:4px 8px;" role="group" aria-label="Quick suggestions">
        @foreach(['Maize prices','Farming calendar','Fall armyworm','Training courses'] as $chip)
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
/* ═══ AgriBot Offline Knowledge Base ═══════════════════════════════
   Everything runs on-device — no internet or API needed. It knows
   Malawi crops, their farming calendars, prices, diseases, fertilizers,
   districts and key features, then sends farmers to the right pages. */

// ── Crops: calendar, prices, diseases, fertilizer, page links ─────
const AGRI_CROPS = {
  maize: {
    kw: ['maize','corn','chimanga','ufa','umphodza'],
    name: 'Maize',
    overview: "<strong>Maize (Chimanga):</strong> Malawi's staple crop and the heart of village food security. Opt for certified hybrids (e.g. DK8031, SC637) and follow the NPK + CAN programme for the best yield.",
    calendar: "<strong>Maize Calendar (Central Region):</strong><br>• Plant with first reliable rains (Oct–Dec)<br>• Basal NPK 23:21:0 at planting<br>• Top dress CAN at knee height (4–6 weeks)<br>• Second weeding + banking by early Feb<br>• Ready to harvest Mar–May (12–14% moisture)",
    price: "<strong>Maize price:</strong> Typically <strong>K 35,000–50,000 per 50kg bag</strong> at rural markets; higher (K 55,000+) during lean season. Dried grain sells better than fresh cobs.<br><a href='/marketplace' style='color:var(--primary)'>→ See live listings</a>",
    disease: "<strong>Common maize problems:</strong><br>• Fall Armyworm — holes in leaves, frass; spray Coragen/Ampligo<br>• Maize Lethal Necrosis — yellow streaking leaves; remove & burn<br>• Grey Leaf Spot — small grey dots; use resistant hybrids<br>• Stalk borer & termites in dry spells<br><a href='/diseases' style='color:var(--primary)'>→ Full disease guides & photo scan</a>",
    fertil: "<strong>Maize fertilizer programme:</strong><br>• Basal: NPK 23:21:0+4S — 1 bag/acre at planting<br>• Top dress: CAN 27%N — 1 bag/acre at knee height<br>• Add compost/manure to build acidic soils<br>• Lime if pH below 5.5<br><a href='/marketplace?category=fertilizer' style='color:var(--primary)'>→ Buy fertilizer</a>"
  },
  soybean: {
    kw: ['soybean','soyabean','soya','soy','nandolo','sawawa'],
    name: 'Soybean',
    overview: "<strong>Soybean (Soya):</strong> A cash crop that fixes its own nitrogen — no basal fertilizer needed. Great for rotation after maize and for village-level processing into flour and oil.",
    calendar: "<strong>Soybean Calendar:</strong><br>• Plant late Dec–mid Jan after maize planting<br>• Use inoculant on seed for better nodulation<br>• Weed twice in the first 6 weeks<br>• Harvest Mar–May when pods rattle",
    price: "<strong>Soybean price:</strong> Roughly <strong>K 60,000–80,000 per 50kg bag</strong>. Processed soya flour and milk fetch more than raw beans.<br><a href='/marketplace' style='color:var(--primary)'>→ Find buyers</a>",
    disease: "<strong>Soybean problems:</strong><br>• Frogeye leaf spot, rust and pod blight in wet years<br>• Use clean seed and rotate away from beans for 2 seasons<br>• Red spider mites during dry spells<br><a href='/diseases' style='color:var(--primary)'>→ Disease guides</a>",
    fertil: "<strong>Soybean nutrition:</strong><br>• No nitrogen needed if seed is inoculated<br>• Low dose of SSP/D compound if soils are depleted<br>• Keep lime application small — soya likes pH 6.0–6.5"
  },
  groundnut: {
    kw: ['groundnut','groundnuts','peanut','peanuts','mtedza','ntchunga'],
    name: 'Groundnut',
    overview: "<strong>Groundnut (Mtedza):</strong> A must-have rotation crop for smallholders — fixes nitrogen, improves maize yields that follow it, and earns good income from confectionery grades.",
    calendar: "<strong>Groundnut Calendar:</strong><br>• Plant Nov–Dec, hilled ridges<br>• Do not top dress with nitrogen<br>• Ridge/till soil and harvest promptly at 110–130 days<br>• Dry well and store kadango shells-away from rats",
    price: "<strong>Groundnut price:</strong> Around <strong>K 60,000–90,000 per bag</strong> (confectionery grade). Roasted groundnuts fetch more at roadside stands.<br><a href='/marketplace' style='color:var(--primary)'>→ Sell / buy</a>",
    disease: "<strong>Groundnut diseases:</strong><br>• Rosette virus — spread by aphids; rogued plants immediately<br>• Leaf spots (early/late) in wet years<br>• Aflatoxin — dry quickly and store in clean bags<br><a href='/diseases' style='color:var(--primary)'>→ Disease guides</a>",
    fertil: "<strong>Groundnut nutrition:</strong><br>• No nitrogen needed — do NOT apply NPK<br>• Small amount of gypsum/lime at pegging helps fill pods"
  },
  tomato: {
    kw: ['tomato','tomatoes','matimati','nyanya'],
    name: 'Tomato',
    overview: "<strong>Tomato (Matimati):</strong> One of the best-margin vegetables in Malawi — but demands care: staking, pruning, watering and strict pest scouting for a profitable crop.",
    calendar: "<strong>Tomato Calendar:</strong><br>• Nursery early (6–8 weeks before transplanting)<br>• Transplant 1–2 weeks after rains establish or with irrigation<br>• Stake & prune to 2 stems<br>• Harvest 60–75 days after transplanting, over 6–8 weeks",
    price: "<strong>Tomato price:</strong> Typically <strong>K 8,000–15,000 per 10kg box</strong>; prices peak in lean season. Grade A Roma fetches the best price.<br><a href='/marketplace' style='color:var(--primary)'>→ Live market prices</a>",
    disease: "<strong>Tomato diseases:</strong><br>• Late blight — brown spots, white mold on leaves; spray copper/mancozeb early<br>• Bacterial wilt — sudden collapse; rotate, avoid over-watering<br>• Blossom-end rot — add calcium lime & steady watering<br><a href='/diseases' style='color:var(--primary)'>→ Disease guides</a>",
    fertil: "<strong>Tomato nutrition:</strong><br>• Apply compost at transplanting<br>• CAN top dress 2 weeks later, repeated every 3 weeks<br>• Calcium + steady water prevent blossom-end rot"
  },
  potato: {
    kw: ['potato','potatoes','irish potato','mbatata','batata'],
    name: 'Irish Potato',
    overview: "<strong>Irish Potato (Mbatata):</strong> Grows best in cool areas like Dedza, Ntcheu and Mzimba. Certified seed matters — recycled seed carries disease season after season.",
    calendar: "<strong>Potato Calendar:</strong><br>• Plant Oct–Dec and again Mar–Apr (two seasons)<br>• Dahlias every 25cm on ridges<br>• Earth-up / hill plants at flowering<br>• Harvest 90–120 days; cure for 2 weeks before storage",
    price: "<strong>Potato price:</strong> Around <strong>K 30,000–45,000 per 50kg bag</strong>, higher in lean months.<br><a href='/marketplace' style='color:var(--primary)'>→ Marketplace</a>",
    disease: "<strong>Potato diseases:</strong><br>• Late blight — dark patches, white ring under leaves; it can wipe a field in a week<br>• Bacterial wilt — wilted + tubers rot; rotate crops<br>• Spiral nematodes & potato tuber moth<br>• Use certified seed & spray mancozeb early<br><a href='/diseases' style='color:var(--primary)'>→ Disease guides</a>",
    fertil: "<strong>Potato nutrition:</strong><br>• NPK 23:21:0 basal at planting<br>• CAN top dress at flowering<br>• Avoid fresh manure near planting (burns tubers)"
  },
  rice: {
    kw: ['rice','mpunga'],
    name: 'Rice',
    overview: "<strong>Rice (Mpunga):</strong> Grown on lake-shore plains (Karonga, Salima, Machinga, Nkhata Bay). Needs standing water through to flowering — planting time follows the rains.</p>",
    calendar: "<strong>Rice Calendar:</strong><br>• Nursery Dec, transplant late Dec–Jan<br>• Flood field 5–10cm at tillering<br>• Drain 2 weeks before harvest<br>• Harvest stalks 2–3 weeks after flowering; threshold March–May",
    price: "<strong>Rice price:</strong> Roughly <strong>K 70,000–95,000 per 50kg bag</strong> depending on polish/grade.<br><a href='/marketplace' style='color:var(--primary)'>→ Marketplace</a>",
    disease: "<strong>Rice problems:</strong><br>• Rice blast — diamond lesions; plant resistant varieties<br>• Stem borers in flooded fields<br>• Rats during dry season storage<br><a href='/diseases' style='color:var(--primary)'>→ Disease guides</a>",
    fertil: "<strong>Rice nutrition:</strong><br>• Basal NPK at transplanting<br>• Urea top dress split at tillering & panicle stage<br>• Keep water OFF if you apply weeds-free"
  },
  cabbage: {
    kw: ['cabbage','kabichi'],
    name: 'Cabbage',
    overview: "<strong>Cabbage (Kabichi):</strong> A reliable cool-season vegetable with high yields per square metre — popular for market gardens and school feeding programmes.",
    calendar: "<strong>Cabbage Calendar:</strong><br>• Nursery 4–6 weeks before transplanting<br>• Transplant on ridges Nov–Jan (or with irrigation any month)<br>• Water every 3–4 days; harvest 90–120 days later<br>• Cut with a knife, leaving outer leaves to protect the head",
    price: "<strong>Cabbage price:</strong> Around <strong>K 1,000–2,500 per head</strong> at local markets; bulk crates go cheaper.<br><a href='/marketplace' style='color:var(--primary)'>→ Marketplace</a>",
    disease: "<strong>Cabbage problems:</strong><br>• Diamondback moth / caterpillars — check under leaves weekly<br>• Aphids & black rot in humid weeks<br><a href='/diseases' style='color:var(--primary)'>→ Disease guides</a>",
    fertil: "<strong>Cabbage nutrition:</strong><br>• Compost + D compound at planting<br>• CAN top dress 3 weeks after transplanting<br>• Keep soil moist — bitter heads come from stress"
  },
  casava: { kw: ['cassava','kasava','chinangwa'],
    name: 'Cassava',
    overview: "<strong>Cassava (Chinangwa):</strong> A drought-hardy food and cash crop for lakeshore and flood-plain areas; tolerant of poor soils and gives you food security even in lean years.",
    calendar: "<strong>Cassava Calendar:</strong><br>• Plant stem cuttings Nov–Dec or with early rains<br>• No nitrogen needed — keep weeding early<br>• Harvest after 9–12 months, or grab-and-pull as food reserve",
    price: "<strong>Cassava price:</strong> Fresh tubers sell for <strong>K 15,000–30,000 per 50kg bag</strong>; chips & flour extend the value.",
    disease: "<strong>Cassava problems:</strong><br>• Mosaic virus — curl mottled leaves; use clean cuttings<br>• Cassava brown streak — necrotic roots; plant tolerant varieties<br><a href='/diseases' style='color:var(--primary)'>→ Disease guides</a>",
    fertil: "<strong>Cassava nutrition:</strong><br>• Little or no fertilizer on virgin land<br>• Small potash helps in sandy soils"
  },
  tobacco: { kw: ['tobacco','fodya'],
    name: 'Tobacco',
    overview: "<strong>Tobacco (Fodya):</strong> The leading export crop in Malawi. Very management-intensive — nursery, planting, primings, then a long grading/selling cycle through authorized floors.",
    calendar: "<strong>Burley/Flu-cured Calendar:</strong><br>• Seedbed mid-year (Jul–Sep), transplant Oct–Dec<br>• Top (remove flowers) & de-sucker regularly<br>• Prime leaves from Jan; cure in barns<br>• Sell at licensed auction floors",
    price: "<strong>Tobacco price:</strong> Varies by grade — roughly <strong>K 1,800–3,500 per kg</strong> on the auction floor in recent seasons.",
    disease: "<strong>Tobacco problems:</strong><br>• Leaf curl & mosaic — control aphids, use clean hands<br>• Angular leaf spot (rainy weather)<br>• Avoid heavy soils that waterlog roots<br><a href='/diseases' style='color:var(--primary)'>→ Disease guides</a>",
    fertil: "<strong>Tobacco nutrition:</strong><br>• NPK 23:21:0 basal in the seedbed row<br>• Sidedress CAN 3–4 weeks after transplanting<br>• Never over-fertilize — it burns leaves"
  }
};

// ── Districts & regions ────────────────────────────────────────────
const AGRI_REGIONS = [
  { kw: ['lilongwe'], name: 'Lilongwe', text: "<strong>Lilongwe (Central Region):</strong><br>• Red-black loams, rains Nov–Apr (~900mm)<br>• Top crops: maize, soya, groundnuts, vegetables<br>• Plant maize with first rains; top dress CAN at knee height<br>• Use ridges across the slope to reduce run-off<br><a href='/learn?category=crop-production' style='color:var(--primary)'>→ Crop courses</a>" },
  { kw: ['blantyre','blantyre city'], name: 'Blantyre', text: "<strong>Blantyre (Southern Region):</strong><br>• Hot and humid, moderate rain<br>• Top crops: vegetables, tomatoes, maize, pigeon peas<br>• Cover nursery beds on hot days; watch whiteflies<br><a href='/learn?category=vegetables' style='color:var(--primary)'>→ Vegetable courses</a>" },
  { kw: ['mzimba','mzuzu'], name: 'Mzimba / Mzuzu', text: "<strong>Mzimba / Mzuzu (Northern Region):</strong><br>• Good rainfall, cool highlands<br>• Top crops: maize, tobacco, groundnuts; Mzuzu adds coffee & macadamia<br>• Full ridges help on the steeper hillsides" },
  { kw: ['kasungu'], name: 'Kasungu', text: "<strong>Kasungu:</strong><br>• Sandy loams — lime acidic soils (pH below 5.5)<br>• Top crops: maize, tobacco, groundnuts<br>• Watch for early-season armyworm on young maize<br><a href='/diseases' style='color:var(--primary)'>→ Disease guides</a>" },
  { kw: ['dedza'], name: 'Dedza', text: "<strong>Dedza (cool highlands):</strong><br>• One of Malawi's best areas for Irish potatoes<br>• Also maize, beans and peas<br>• Late blight is the #1 risk — spray early and use certified seed<br><a href='/diseases' style='color:var(--primary)'>→ Late blight guide</a>" },
  { kw: ['salima'], name: 'Salima', text: "<strong>Salima (Lakeshore):</strong><br>• Hot lowlands with lake influence<br>• Top crops: rice, maize, and tomato/onion under irrigation<br>• Water management is key — treadle pumps work well here<br><a href='/innovation' style='color:var(--primary)'>→ Irrigation innovations</a>" },
  { kw: ['zomba'], name: 'Zomba', text: "<strong>Zomba:</strong><br>• Good rainfall at the plateau<br>• Top crops: tobacco, maize, vegetables<br>• Plan for two seasons using valley water where available" },
  { kw: ['ntcheu'], name: 'Ntcheu', text: "<strong>Ntcheu:</strong><br>• Famous for groundnuts — confectionery grade fetches top prices<br>• Also maize and beans<br>• Dry and store groundnuts away from moisture to avoid aflatoxin<br><a href='/marketplace' style='color:var(--primary)'>→ Sell groundnuts</a>" },
  { kw: ['machinga'], name: 'Machinga', text: "<strong>Machinga:</strong><br>• Lakeshore plains — rice country<br>• Also cassava and maize<br>• Time the rice nursery to the rains, flood at tillering" },
  { kw: ['nkhata bay','nkhata'], name: 'Nkhata Bay', text: "<strong>Nkhata Bay:</strong><br>• Tropical lakeshore — cassava, rice, macadamia<br>• Use crab-resistant seedbeds and guard chickens in rice season" },
  { kw: ['karonga'], name: 'Karonga', text: "<strong>Karonga:</strong><br>• Hot northern lakeshore — rice, cassava, and short-season maize<br>• High heat: plant early and mulch to save moisture" },
  { kw: ['thyolo'], name: 'Thyolo', text: "<strong>Thyolo:</strong><br>• Estate belt — tea, macadamia, bananas<br>• Smallholders grow maize + vegetables between estates<br>• Frequent rain; keep drainage channels clear" },
  { kw: ['mulanje'], name: 'Mulanje', text: "<strong>Mulanje:</strong><br>• Cool tea/coffee slopes below the mountain<br>• Vegetables, cassava and rice on the plains<br>• Watch cold-season frost on vegetables" }
];

// ── Generic topic answers ───────────────────────────────────────────
const GENERIC = {
  price: { r: "<strong>Typical market prices (Malawi):</strong><br>• Maize: K 35,000–50,000/50kg bag<br>• Soybeans: K 60,000–80,000/50kg<br>• Groundnuts: K 60,000–90,000/bag<br>• Tomatoes: K 8,000–15,000/10kg box<br>• Potatoes: K 30,000–45,000/50kg<br>• Rice: K 70,000–95,000/50kg<br>These are typical ranges — always check live listings.<br><a href='/marketplace' style='color:var(--primary)'>→ View marketplace</a>", delay: 1000 },
  disease: { r: "<strong>Crop Disease Help:</strong><br>• Upload a leaf photo for an instant diagnosis<br>• Get treatment steps for any disease<br>• Check active outbreak alerts in your district<br><a href='/diseases' style='color:var(--primary)'>→ Open Disease Scanner</a>", delay: 900 },
  calendar: { r: "<strong>Malawi farming calendar (main season):</strong><br>• Oct–Dec: Land prep & plant maize, soya, groundnuts<br>• Jan–Feb: Weed, top dress, control pests<br>• Mar–May: Harvest maize, soya, groundnuts<br>• Jun–Aug: Second season — tomatoes, cabbage, onions with irrigation<br>• Sep: Land prep for the next round<br>Tell me a crop name (e.g. \"maize calendar\") or <a href='/learn' style='color:var(--primary)'>→ open courses</a>", delay: 1000 },
  weather: { r: "<strong>Weather & Planting Window:</strong><br>• Blantyre: 26°C, partly cloudy<br>• Lilongwe: 24°C, light rain expected<br>• Mzuzu: 21°C, cloudy<br>• Best window: plant as soon as rains establish (mid Nov–Dec)<br><small>Advice from Malawi's seasonal forecast.</small>", delay: 800 },
  irrigation: { r: "<strong>Irrigation Tips:</strong><br>• Drip saves 40–60% water vs surface irrigation<br>• Water early morning or evening to cut evaporation<br>• Check soil moisture before watering<br>• Treadle pumps work well for 1–2 acre gardens<br><a href='/learn?category=irrigation' style='color:var(--primary)'>→ Irrigation course</a> · <a href='/innovation' style='color:var(--primary)'>→ Water innovations</a>", delay: 1000 },
  course: { r: "<strong>Featured Training Courses:</strong><br>• Modern Maize Farming<br>• Drip Irrigation Setup<br>• Organic Vegetable Production<br>• Profitable Dairy Farming<br>• Drone Farming & Mapping<br>Complete lessons, earn certificates, learn at your pace.<br><a href='/learn' style='color:var(--primary)'>→ Browse all courses</a>", delay: 900 },
  fertilizer: { r: "<strong>Fertilizer Guide:</strong><br>• Basal: NPK 23:21:0+4S — 1 bag/acre at planting<br>• Top dressing: CAN 27%N — 1 bag/acre at 4–6 weeks<br>• Legumes (soya, groundnut) need little or no nitrogen<br>• Compost/manure cut fertilizer bills by ~30%<br>• Lime acidic soils below pH 5.5<br><a href='/marketplace?category=fertilizer' style='color:var(--primary)'>→ Buy fertilizer</a>", delay: 1100 },
  delivery: { r: "<strong>Delivery Tracking:</strong><br>• Track your order live on GPS from dispatch to door<br>• SMS updates at every status change<br>• Average delivery: 1–3 business days<br>• Instant mobile money payments (Airtel Money / Mpamba)<br><a href='/delivery' style='color:var(--primary)'>→ Track your orders</a>", delay: 800 },
  sell: { r: "<strong>Sell on AgriTech Pro:</strong><br>• List your products and reach buyers across Malawi<br>• Accept Airtel Money / TNM Mpamba<br>• Delivery is arranged automatically<br><a href='/marketplace' style='color:var(--primary)'>→ Start selling</a>", delay: 900 },
  competition: { r: "<strong>Farmer Innovation Challenge:</strong><br>• Submit your farming innovation<br>• Community votes pick the top 3<br>• 1st, 2nd & 3rd win cash prizes (up to K 25,000)<br>• The winners list is published and downloadable<br><a href='/innovation' style='color:var(--primary)'>→ View the Innovation Hub</a>", delay: 900 },
  funding: { r: "<strong>Grants & Support:</strong><br>• Look for seasonal input subsidies and NGO farmer programmes announced through extension workers<br>• AgriTech Pro links you to buyers, courses and suppliers to improve margins<br>• Follow the SMS alerts for funding windows in your district<br><a href='/register' style='color:var(--primary)'>→ Create an account</a>", delay: 1000 },
  account: { r: "<strong>Your account:</strong><br>• Register — takes under a minute<br>• Track orders, follow courses, save farm records<br>• Receive disease & market alerts in your district<br><a href='/register' style='color:var(--primary)'>→ Register</a> · <a href='/login' style='color:var(--primary)'>→ Sign in</a>", delay: 900 },
  help: { r: "I can help you with: <strong>crop farming calendars, market prices, diseases, fertilizers, districts/regions, weather, irrigation, courses, delivery, and the innovation challenge.</strong><br><br>Try asking:<br>• \"What's the price of maize?\"<br>• \"Give me the maize farming calendar\"<br>• \"Fall armyworm\" · \"Tomato diseases\"<br>• \"Best crops for Lilongwe?\"", delay: 700 }
};

function cropFromText(t) {
  for (const key in AGRI_CROPS) {
    if (AGRI_CROPS[key].kw.some(k => t.includes(k))) return key;
  }
  return null;
}

function regionFromText(t) {
  for (const r of AGRI_REGIONS) {
    if (r.kw.some(k => t.includes(k))) return r;
  }
  return null;
}

function linkify(r, delay) { return { r, delay }; }

function getBotResponse(input) {
  const lower = input.toLowerCase();
  const crop = cropFromText(lower);
  const region = regionFromText(lower);
  const has = (...w) => w.some(x => lower.includes(x));

  // Greetings
  if (has('hello','hi ','hey','muli bwanji','moni','zikomo','thanks','thank you'))
    return linkify("<strong>Hello!</strong> I'm AgriBot, your farming assistant. Ask me about any crop for its <em>calendar, price or diseases</em>, or about prices, districts, courses, delivery and the innovation challenge. What would you like to know?", 700);
  if (has('who are you','what can you do','help'))
    return linkify(GENERIC.help.r, 800);

  // Price + crop → that crop's price
  if (crop && has('price','how much','cost','sell','market','thandizo','mtengo')) {
    const c = AGRI_CROPS[crop];
    return linkify(c.price || GENERIC.price.r, 1000);
  }
  // Price (general)
  if (has('price','prices','market','cost','how much','mtengo')) return linkify(GENERIC.price.r, 1000);

  // Calendar / season + crop → that crop's calendar
  if (crop && has('calendar','plant','planting','sow','grow','season','when to','harvest','kalendala')) {
    const c = AGRI_CROPS[crop];
    return linkify((c.calendar || GENERIC.calendar.r) + "<br><a href='/learn' style='color:var(--primary)'>→ Open courses</a>", 1000);
  }
  // General calendar
  if (has('calend','planting time','when to plant','farming season','crop calendar','kalendala')) return linkify(GENERIC.calendar.r, 1000);

  // Disease/pest + crop → that crop's disease guide
  if (crop && has('disease','sick','pest','worm','fungus','blight','rot','leaf','spray','treat','mites')) {
    const c = AGRI_CROPS[crop];
    return linkify(c.disease || GENERIC.disease.r, 1000);
  }
  if (has('fall armyworm','armyworm','worm','caterpillar')) {
    return linkify("<strong>Fall Armyworm:</strong><br>• Look for leaf holes, piles of frass and ragged whorl feeding<br>• Spray Coragen, Ampligo or Orthene early morning/evening<br>• Scout maize weekly from emergence to tassel<br>• 1 spray cycle protects 2–3 weeks<br><a href='/diseases' style='color:var(--primary)'>→ Full treatment guide</a>", 1100);
  }
  if (has('disease','diseases','pest','sick','detect','scan')) return linkify(GENERIC.disease.r, 900);

  // Fertilizer + crop → that crop's fertilizer programme
  if (crop && has('fertil','npk','can','manure','compost','topdress','top dress','lime')) {
    const c = AGRI_CROPS[crop];
    return linkify(c.fertil || GENERIC.fertilizer.r, 1100);
  }
  if (has('fertil','npk','can ','manure','compost')) return linkify(GENERIC.fertilizer.r, 1100);

  // District / region
  if (region) return linkify(region.text, 1000);
  if (has('region','district','where is','best place','best crops in','agro-ecology')) {
    return linkify("<strong>Malawi regions at a glance:</strong><br>• North — more rain: maize, tobacco, coffee, macadamia<br>• Centre — red soils: maize, soya, groundnuts<br>• South — hotter: vegetables, rice, cassava, tobacco<br>Tell me a district name (e.g. \"crops for Dedza\") for specific advice, or <a href='/learn' style='color:var(--primary)'>→ see courses</a>", 1000);
  }

  // Other system features
  if (has('irrig','water','drip','treadle')) return linkify(GENERIC.irrigation.r, 1000);
  if (has('weather','rain','forecast','sunny')) return linkify(GENERIC.weather.r, 800);
  if (has('course','courses','learn','train','certificate','lessons')) return linkify(GENERIC.course.r, 900);
  if (has('deliver','track','order','parcel')) return linkify(GENERIC.delivery.r, 800);
  if (has('sell','list','marketplace','buy ','advertise')) return linkify(GENERIC.sell.r, 900);
  if (has('competition','challenge','prize','winner','win')) return linkify(GENERIC.competition.r, 900);
  if (has('grant','fund','subsidy','support','loan')) return linkify(GENERIC.funding.r, 1000);
  if (has('account','register','sign up','create','login')) return linkify(GENERIC.account.r, 900);

  // Any crop name alone → full crop guide
  if (crop) {
    const c = AGRI_CROPS[crop];
    return linkify(
      (c.overview || '') + "<br><br>" + (c.calendar || '') + "<br><br>" +
      (c.price || '') + "<br><br>" + (c.disease || '') +
      "<br><br><a href='/learn' style='color:var(--primary)'>→ Courses</a> · <a href='/marketplace' style='color:var(--primary)'>→ Marketplace</a> · <a href='/diseases' style='color:var(--primary)'>→ Disease guides</a>",
      1200
    );
  }

  return linkify(GENERIC.help.r, 700);
}

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
