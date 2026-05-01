<?php
require_once dirname(__DIR__) . '/app/bootstrap.php';
use App\Core\Auth;
if (Auth::check()) { header('Location: ' . BASE_URL . '/dashboard.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title>CRM Pulse – Smart Lead Management for Modern Businesses</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@600;700;800;900&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }
    body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; overflow-x: hidden; background: #f8fafc; }

    /* ── Heading font ── */
    h1, h2, h3, h4, h5, h6,
    .heading-font {
      font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    }

    /* ── Gradient text ── */
    .g-text {
      background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%);
      -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
    }

    /* ── Hero background ── */
    .hero-bg {
      background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
      position: relative; overflow: hidden;
    }
    .hero-bg::before {
      content: ''; position: absolute; inset: 0;
      background: radial-gradient(ellipse 80% 60% at 60% 30%, rgba(99,102,241,.2), transparent),
                  radial-gradient(ellipse 50% 70% at 10% 70%, rgba(139,92,246,.15), transparent);
    }
    .hero-bg::after {
      content: ''; position: absolute; inset: 0;
      background-image: radial-gradient(rgba(255,255,255,.035) 1px, transparent 1px);
      background-size: 28px 28px;
    }

    /* ── Orbs ── */
    .orb { position: absolute; border-radius: 50%; filter: blur(70px); animation: floatOrb 9s ease-in-out infinite; pointer-events: none; }
    .orb1 { width: min(400px, 60vw); height: min(400px, 60vw); background: rgba(99,102,241,.18); top: -80px; right: -80px; }
    .orb2 { width: min(280px, 50vw); height: min(280px, 50vw); background: rgba(139,92,246,.14); bottom: -40px; left: -40px; animation-delay: 3s; }
    .orb3 { width: min(180px, 40vw); height: min(180px, 40vw); background: rgba(236,72,153,.1); top: 45%; left: 35%; animation-delay: 6s; }
    @keyframes floatOrb { 0%,100% { transform: translateY(0) scale(1); } 50% { transform: translateY(-25px) scale(1.04); } }

    /* ── Navbar ── */
    .navbar { transition: background .3s ease, box-shadow .3s ease; }
    .navbar.scrolled { background: rgba(255,255,255,.96) !important; backdrop-filter: blur(20px); box-shadow: 0 1px 24px rgba(0,0,0,.08); }
    .navbar.scrolled .nav-logo { color: #0f172a !important; }
    .navbar.scrolled .nav-link { color: #475569 !important; }
    .navbar.scrolled .nav-link:hover { color: #6366f1 !important; }
    .navbar.scrolled .mobile-menu-btn { color: #0f172a !important; }

    /* ── Mobile menu ── */
    .mobile-menu { display: none; }
    .mobile-menu.open { display: block; }

    /* ── Buttons ── */
    .btn-glow {
      background: linear-gradient(135deg, #6366f1, #8b5cf6);
      box-shadow: 0 4px 18px rgba(99,102,241,.4);
      transition: all .22s ease;
    }
    .btn-glow:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(99,102,241,.5); }
    .btn-glow:active { transform: translateY(0); }

    .btn-outline {
      border: 2px solid rgba(255,255,255,.2);
      transition: all .22s ease;
    }
    .btn-outline:hover { border-color: rgba(255,255,255,.45); background: rgba(255,255,255,.07); }

    /* ── Section backgrounds ── */
    .section-light  { background: #ffffff; }
    .section-tinted { background: linear-gradient(160deg, #f0f4ff 0%, #faf5ff 50%, #f0fdf4 100%); }
    .section-slate  { background: linear-gradient(160deg, #f8fafc 0%, #f1f5f9 100%); }

    /* ── Feature cards ── */
    .feature-card { transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease; }
    .feature-card:hover { transform: translateY(-6px); box-shadow: 0 24px 50px rgba(99,102,241,.13); border-color: #c7d2fe; }
    .feature-card .card-icon-wrap { transition: transform .22s ease; }
    .feature-card:hover .card-icon-wrap { transform: scale(1.1) rotate(-4deg); }

    .step-card { transition: transform .22s ease, box-shadow .22s ease; }
    .step-card:hover { transform: translateY(-5px); box-shadow: 0 20px 44px rgba(99,102,241,.1); }

    .testimonial-card { transition: transform .22s ease, box-shadow .22s ease; }
    .testimonial-card:hover { transform: translateY(-5px); box-shadow: 0 20px 44px rgba(0,0,0,.1); }

    /* ── Ticker ── */
    .ticker-wrap { overflow: hidden; white-space: nowrap; }
    .ticker { display: inline-block; animation: ticker 22s linear infinite; }
    @keyframes ticker { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }

    /* ── Scroll reveal ── */
    .reveal { opacity: 0; transform: translateY(28px); transition: opacity .65s ease, transform .65s ease; }
    .reveal.visible { opacity: 1; transform: translateY(0); }

    /* ── Mockup shadow ── */
    .mockup { box-shadow: 0 40px 80px rgba(0,0,0,.45), 0 0 0 1px rgba(255,255,255,.06); }

    /* ── Responsive helpers ── */
    @media (max-width: 640px) {
      .hero-headline { font-size: 2.4rem !important; line-height: 1.15 !important; }
      .hero-sub { font-size: 1rem !important; }
      .section-headline { font-size: 2rem !important; }
      .orb1, .orb2, .orb3 { opacity: .5; }
    }
    @media (max-width: 768px) {
      .stat-divider::after { display: none !important; }
    }
  </style>
</head>
<body>

<!-- ══════════════════════════════════════════════════════
     NAVBAR
══════════════════════════════════════════════════════ -->
<nav class="navbar fixed top-0 left-0 right-0 z-50" id="navbar">
  <div class="max-w-7xl mx-auto px-4 sm:px-6" style="height:68px;display:flex;align-items:center;justify-content:space-between;">

    <!-- Logo -->
    <a href="#" class="flex items-center gap-2.5 flex-shrink-0">
      <div class="w-9 h-9 rounded-xl flex items-center justify-center btn-glow flex-shrink-0">
        <i class="bi bi-lightning-charge-fill text-white"></i>
      </div>
      <span class="nav-logo font-extrabold text-lg text-white tracking-tight">
        CRM <span style="color:#818cf8;">Pulse</span>
      </span>
    </a>

    <!-- Desktop Nav Links -->
    <div class="hidden md:flex items-center gap-7">
      <a href="#features"     class="nav-link text-sm font-medium text-white/70 hover:text-white transition-colors">Features</a>
      <a href="#how-it-works" class="nav-link text-sm font-medium text-white/70 hover:text-white transition-colors">How It Works</a>
      <a href="#testimonials" class="nav-link text-sm font-medium text-white/70 hover:text-white transition-colors">Reviews</a>
      <a href="#pricing"      class="nav-link text-sm font-medium text-white/70 hover:text-white transition-colors">Pricing</a>
    </div>

    <!-- Desktop CTA -->
    <div class="hidden md:flex items-center gap-3">
      <a href="<?= BASE_URL ?>/login.php"
         class="text-sm font-semibold text-white/75 hover:text-white transition-colors px-3 py-2">
        Sign In
      </a>
      <a href="<?= BASE_URL ?>/register.php"
         class="btn-glow text-white text-sm font-bold px-5 py-2.5 rounded-xl flex items-center gap-2">
        <i class="bi bi-rocket-takeoff-fill"></i> Get Started Free
      </a>
    </div>

    <!-- Mobile: CTA + Hamburger -->
    <div class="flex md:hidden items-center gap-2">
      <a href="<?= BASE_URL ?>/register.php"
         class="btn-glow text-white text-xs font-bold px-4 py-2 rounded-xl">
        Start Free
      </a>
      <button id="mobileMenuBtn" class="mobile-menu-btn w-9 h-9 flex items-center justify-center rounded-xl text-white/80 hover:text-white transition-colors">
        <i class="bi bi-list text-2xl" id="menuIcon"></i>
      </button>
    </div>
  </div>

  <!-- Mobile Menu Dropdown -->
  <div class="mobile-menu" id="mobileMenu"
       style="background:rgba(15,23,42,.97);backdrop-filter:blur(20px);border-top:1px solid rgba(255,255,255,.06);">
    <div class="px-4 py-4 space-y-1">
      <a href="#features"     class="block px-4 py-3 text-sm font-medium text-white/70 hover:text-white hover:bg-white/5 rounded-xl transition-colors">Features</a>
      <a href="#how-it-works" class="block px-4 py-3 text-sm font-medium text-white/70 hover:text-white hover:bg-white/5 rounded-xl transition-colors">How It Works</a>
      <a href="#testimonials" class="block px-4 py-3 text-sm font-medium text-white/70 hover:text-white hover:bg-white/5 rounded-xl transition-colors">Reviews</a>
      <a href="#pricing"      class="block px-4 py-3 text-sm font-medium text-white/70 hover:text-white hover:bg-white/5 rounded-xl transition-colors">Pricing</a>
      <div class="border-t border-white/5 pt-3 mt-3 flex flex-col gap-2">
        <a href="<?= BASE_URL ?>/login.php"
           class="block text-center px-4 py-3 text-sm font-semibold text-white/80 border border-white/15 rounded-xl hover:border-white/30 transition-colors">
          Sign In
        </a>
        <a href="<?= BASE_URL ?>/register.php"
           class="btn-glow block text-center px-4 py-3 text-sm font-bold text-white rounded-xl">
          Create Free Account
        </a>
      </div>
    </div>
  </div>
</nav>

<!-- ══════════════════════════════════════════════════════
     HERO
══════════════════════════════════════════════════════ -->
<section class="hero-bg" style="padding-top:68px;">
  <div class="orb orb1"></div>
  <div class="orb orb2"></div>
  <div class="orb orb3"></div>

  <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 py-16 sm:py-20 lg:py-28">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">

      <!-- Left: Text -->
      <div class="text-center lg:text-left">
        <!-- Headline -->
        <h1 class="hero-headline font-black text-white leading-tight mb-5 tracking-tight"
            style="font-size:clamp(2.2rem,5vw,3.75rem);line-height:1.12;">
          Turn Every Lead Into
          <span class="block g-text">A Paying Client</span>
        </h1>

        <!-- Sub -->
        <p class="hero-sub text-slate-400 leading-relaxed mb-8 mx-auto lg:mx-0 max-w-lg"
           style="font-size:clamp(.95rem,2vw,1.15rem);">
          CRM Pulse is a powerful, secure, and beautifully designed lead management system.
          Track every contact, follow up on time, and grow your business — all from one dashboard.
        </p>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3 mb-10">
          <a href="<?= BASE_URL ?>/register.php"
             class="btn-glow w-full sm:w-auto text-white font-bold px-7 py-4 rounded-2xl text-base flex items-center justify-center gap-2">
            <i class="bi bi-rocket-takeoff-fill"></i> Start for Free
          </a>
          <a href="<?= BASE_URL ?>/login.php"
             class="btn-outline w-full sm:w-auto text-white font-bold px-7 py-4 rounded-2xl text-base flex items-center justify-center gap-2">
            <i class="bi bi-box-arrow-in-right"></i> Sign In
          </a>
        </div>

        <!-- Social proof -->
        <div class="flex items-center justify-center lg:justify-start gap-4">
          <div class="flex -space-x-2">
            <?php
            $avColors = ['#6366f1','#8b5cf6','#ec4899','#f59e0b','#22c55e'];
            $avNames  = ['JD','AM','SK','RB','TL'];
            for ($i = 0; $i < 5; $i++): ?>
            <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                 style="background:<?= $avColors[$i] ?>;border-color:#0f172a;">
              <?= $avNames[$i] ?>
            </div>
            <?php endfor; ?>
          </div>
          <div>
            <div class="flex items-center gap-0.5 mb-0.5">
              <?php for ($i = 0; $i < 5; $i++): ?>
                <i class="bi bi-star-fill text-amber-400 text-xs"></i>
              <?php endfor; ?>
            </div>
            <p class="text-slate-400 text-xs">Trusted by <span class="text-white font-semibold">500+</span> businesses</p>
          </div>
        </div>
      </div>

      <!-- Right: Dashboard Mockup (hidden on small, shown on lg) -->
      <div class="relative hidden lg:block">
        <div class="absolute inset-0 rounded-3xl blur-3xl opacity-25"
             style="background:linear-gradient(135deg,#6366f1,#8b5cf6);transform:scale(.88) translateY(24px);"></div>

        <div class="relative mockup rounded-2xl overflow-hidden border border-white/10">
          <!-- Browser bar -->
          <div class="px-4 py-3 flex items-center gap-2 border-b border-white/5" style="background:#1e293b;">
            <div class="flex gap-1.5">
              <div class="w-2.5 h-2.5 rounded-full bg-red-500/70"></div>
              <div class="w-2.5 h-2.5 rounded-full bg-amber-500/70"></div>
              <div class="w-2.5 h-2.5 rounded-full bg-emerald-500/70"></div>
            </div>
            <div class="flex-1 rounded-lg px-3 py-1.5 text-xs text-slate-500 ml-2 flex items-center gap-1.5" style="background:#0f172a;">
              <i class="bi bi-lock-fill text-emerald-500" style="font-size:.55rem;"></i>
              localhost/FUTURE_FS_02/public/dashboard.php
            </div>
          </div>
          <!-- Dashboard UI -->
          <div class="p-4" style="background:#f1f5f9;">
            <div class="bg-white rounded-xl px-4 py-2.5 flex items-center justify-between mb-3 border border-slate-200">
              <div>
                <p class="font-bold text-slate-800 text-xs">Dashboard</p>
                <p class="text-slate-400" style="font-size:.58rem;">Friday, May 1, 2026</p>
              </div>
              <div class="rounded-lg text-white text-xs font-bold px-2.5 py-1.5 flex items-center gap-1 btn-glow" style="font-size:.65rem;">
                <i class="bi bi-plus-lg"></i> Add Lead
              </div>
            </div>
            <div class="grid grid-cols-4 gap-2 mb-3">
              <?php
              $mc = [
                ['l'=>'Total','v'=>'24','i'=>'people-fill',         'c'=>'#6366f1','b'=>'rgba(99,102,241,.1)'],
                ['l'=>'New',  'v'=>'8', 'i'=>'star-fill',           'c'=>'#f59e0b','b'=>'rgba(245,158,11,.1)'],
                ['l'=>'Followup','v'=>'6','i'=>'calendar-check-fill','c'=>'#3b82f6','b'=>'rgba(59,130,246,.1)'],
                ['l'=>'Done', 'v'=>'4', 'i'=>'trophy-fill',         'c'=>'#22c55e','b'=>'rgba(34,197,94,.1)'],
              ];
              foreach ($mc as $c): ?>
              <div class="bg-white rounded-xl p-2.5 border border-slate-200">
                <div class="flex items-center justify-between mb-1.5">
                  <span class="text-slate-400 font-bold uppercase" style="font-size:.48rem;"><?= $c['l'] ?></span>
                  <div class="w-5 h-5 rounded-lg flex items-center justify-center" style="background:<?= $c['b'] ?>;">
                    <i class="bi bi-<?= $c['i'] ?>" style="color:<?= $c['c'] ?>;font-size:.48rem;"></i>
                  </div>
                </div>
                <p class="font-extrabold text-slate-800 text-lg leading-none"><?= $c['v'] ?></p>
              </div>
              <?php endforeach; ?>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
              <div class="px-3 py-2 border-b border-slate-100 flex items-center justify-between">
                <span class="text-xs font-bold text-slate-700">Recent Leads</span>
                <span style="color:#6366f1;font-size:.62rem;font-weight:700;">View all →</span>
              </div>
              <?php
              $rows = [
                ['n'=>'Alice Johnson','s'=>'New',      'sc'=>'rgba(99,102,241,.1)','tc'=>'#6366f1'],
                ['n'=>'Bob Martinez', 's'=>'Contacted','sc'=>'rgba(245,158,11,.1)','tc'=>'#d97706'],
                ['n'=>'Carol White',  's'=>'Converted','sc'=>'rgba(34,197,94,.1)', 'tc'=>'#16a34a'],
              ];
              foreach ($rows as $r): ?>
              <div class="flex items-center gap-2 px-3 py-2 border-b border-slate-50 last:border-0">
                <div class="w-6 h-6 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0 btn-glow" style="font-size:.5rem;">
                  <?= strtoupper($r['n'][0]) ?>
                </div>
                <p class="flex-1 font-semibold text-slate-700 truncate" style="font-size:.62rem;"><?= $r['n'] ?></p>
                <span class="font-semibold px-2 py-0.5 rounded-full" style="background:<?= $r['sc'] ?>;color:<?= $r['tc'] ?>;font-size:.52rem;">
                  <?= $r['s'] ?>
                </span>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <!-- Floating badges -->
        <div class="absolute -left-5 top-1/4 bg-white rounded-2xl px-3 py-2.5 border border-slate-200 flex items-center gap-2.5"
             style="box-shadow:0 8px 24px rgba(0,0,0,.12);">
          <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(34,197,94,.1);">
            <i class="bi bi-graph-up-arrow" style="color:#22c55e;"></i>
          </div>
          <div>
            <p class="text-xs font-bold text-slate-800">+32% Conversion</p>
            <p class="text-xs text-slate-400">This month</p>
          </div>
        </div>

        <div class="absolute -right-5 bottom-1/4 bg-white rounded-2xl px-3 py-2.5 border border-slate-200 flex items-center gap-2.5"
             style="box-shadow:0 8px 24px rgba(0,0,0,.12);">
          <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(99,102,241,.1);">
            <i class="bi bi-bell-fill" style="color:#6366f1;"></i>
          </div>
          <div>
            <p class="text-xs font-bold text-slate-800">3 Follow-ups Due</p>
            <p class="text-xs text-slate-400">Today</p>
          </div>
        </div>
      </div>

      <!-- Mobile mockup (simplified) -->
      <div class="lg:hidden">
        <div class="mockup rounded-2xl overflow-hidden border border-white/10 max-w-sm mx-auto">
          <div class="px-3 py-2.5 flex items-center gap-2 border-b border-white/5" style="background:#1e293b;">
            <div class="flex gap-1"><div class="w-2 h-2 rounded-full bg-red-500/70"></div><div class="w-2 h-2 rounded-full bg-amber-500/70"></div><div class="w-2 h-2 rounded-full bg-emerald-500/70"></div></div>
            <div class="flex-1 rounded-md px-2 py-1 text-xs text-slate-600 ml-1" style="background:#0f172a;font-size:.6rem;">dashboard.php</div>
          </div>
          <div class="p-3" style="background:#f1f5f9;">
            <div class="grid grid-cols-2 gap-2 mb-2">
              <?php foreach ([['Total Leads','24','#6366f1'],['Converted','4','#22c55e']] as $c): ?>
              <div class="bg-white rounded-xl p-3 border border-slate-200 text-center">
                <p class="text-slate-400 font-bold uppercase mb-1" style="font-size:.55rem;"><?= $c[0] ?></p>
                <p class="font-extrabold text-slate-800 text-2xl" style="color:<?= $c[2] ?>;"><?= $c[1] ?></p>
              </div>
              <?php endforeach; ?>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-3">
              <p class="text-xs font-bold text-slate-700 mb-2">Recent Leads</p>
              <?php foreach ([['Alice J.','New','#6366f1'],['Bob M.','Converted','#22c55e']] as $r): ?>
              <div class="flex items-center justify-between py-1.5 border-b border-slate-50 last:border-0">
                <span class="text-xs font-semibold text-slate-700"><?= $r[0] ?></span>
                <span class="text-xs font-bold px-2 py-0.5 rounded-full" style="background:<?= $r[2] ?>15;color:<?= $r[2] ?>;"><?= $r[1] ?></span>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Wave -->
  <div style="margin-top:-1px;line-height:0;">
    <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="width:100%;display:block;height:60px;">
      <path d="M0 60L1440 60L1440 15C1200 55 960 0 720 30C480 60 240 5 0 40Z" fill="#f8fafc"/>
    </svg>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════
     TICKER
══════════════════════════════════════════════════════ -->
<div class="py-3 overflow-hidden" style="background:#6366f1;">
  <div class="ticker-wrap">
    <div class="ticker">
      <?php
      $items = ['Track Your Leads','Never Miss a Follow-up','Convert More Clients','Manage Your Pipeline','Grow Your Business','Stay Organized','Close More Deals','Know Your Best Sources','Follow Up on Time','See Your Conversion Rate','Add Notes Instantly','Export Your Data'];
      $all = array_merge($items,$items,$items,$items);
      foreach ($all as $item): ?>
        <span class="inline-flex items-center gap-2 text-white/85 text-sm font-medium mx-6 sm:mx-8">
          <i class="bi bi-lightning-charge-fill text-white/40 text-xs"></i><?= $item ?>
        </span>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════
     STATS
══════════════════════════════════════════════════════ -->
<section class="py-16 sm:py-20" style="background:linear-gradient(160deg,#f0f4ff 0%,#faf5ff 50%,#f0fdf4 100%);">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-indigo-100 overflow-hidden reveal"
         style="box-shadow:0 8px 40px rgba(99,102,241,.1);">
      <div class="grid grid-cols-2 md:grid-cols-4 divide-y md:divide-y-0 divide-x-0 md:divide-x divide-slate-100">
        <?php
        $stats = [
          ['num'=>'500+','label'=>'Businesses Trust Us',  'icon'=>'building',           'color'=>'#6366f1'],
          ['num'=>'10K+','label'=>'Leads Managed',        'icon'=>'people-fill',        'color'=>'#8b5cf6'],
          ['num'=>'98%', 'label'=>'Satisfaction Rate',    'icon'=>'emoji-smile-fill',   'color'=>'#ec4899'],
          ['num'=>'24/7','label'=>'System Availability',  'icon'=>'clock-fill',         'color'=>'#22c55e'],
        ];
        foreach ($stats as $s): ?>
        <div class="text-center py-8 sm:py-10 px-4 sm:px-6">
          <div class="w-11 h-11 rounded-2xl flex items-center justify-center mx-auto mb-3"
               style="background:<?= $s['color'] ?>15;">
            <i class="bi bi-<?= $s['icon'] ?> text-lg" style="color:<?= $s['color'] ?>;"></i>
          </div>
          <p class="text-3xl sm:text-4xl font-black mb-1" style="color:<?= $s['color'] ?>;"><?= $s['num'] ?></p>
          <p class="text-slate-500 text-xs sm:text-sm font-medium"><?= $s['label'] ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════
     FEATURES
══════════════════════════════════════════════════════ -->
<section id="features" class="py-16 sm:py-24" style="background:#ffffff;">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">

    <div class="text-center mb-12 sm:mb-16 reveal">
      <div class="inline-flex items-center gap-2 text-xs font-bold px-4 py-2 rounded-full mb-5"
           style="background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.2);color:#6366f1;">
        <i class="bi bi-grid-3x3-gap-fill"></i> Powerful Features
      </div>
      <h2 class="section-headline font-extrabold text-slate-900 mb-4 tracking-tight"
          style="font-size:clamp(1.8rem,4vw,3rem);">
        Built for Real Business <span class="g-text">Growth</span>
      </h2>
      <p class="text-slate-500 max-w-xl mx-auto leading-relaxed" style="font-size:clamp(.9rem,2vw,1.1rem);">
        Every feature is designed to help you capture more leads, follow up faster, and close more deals.
      </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
      <?php
      $features = [
        ['icon'=>'shield-lock-fill',             'color'=>'#6366f1','tag'=>'Security',
         'title'=>'Secure Authentication',
         'desc'=>'Session-based login with bcrypt password hashing. Protected routes ensure only admins access the dashboard.'],
        ['icon'=>'people-fill',                  'color'=>'#3b82f6','tag'=>'Core',
         'title'=>'Complete Lead Management',
         'desc'=>'Add, edit, view, and delete leads. Track name, email, phone, company, source, status, and priority.'],
        ['icon'=>'funnel-fill',                  'color'=>'#f59e0b','tag'=>'Productivity',
         'title'=>'Advanced Search & Filter',
         'desc'=>'Filter by status, priority, source, and date range. Search by name, email, or company instantly.'],
        ['icon'=>'calendar-check-fill',          'color'=>'#22c55e','tag'=>'Automation',
         'title'=>'Follow-up Reminders',
         'desc'=>'Set follow-up dates per lead. Get overdue alerts on the dashboard so nothing slips through.'],
        ['icon'=>'chat-text-fill',               'color'=>'#8b5cf6','tag'=>'Tracking',
         'title'=>'Notes & History',
         'desc'=>'Add detailed follow-up notes to every lead. View the full interaction history with timestamps.'],
        ['icon'=>'file-earmark-spreadsheet-fill','color'=>'#ec4899','tag'=>'Analytics',
         'title'=>'Reports & CSV Export',
         'desc'=>'View pipeline analytics, source breakdown, and conversion rates. Export all data to CSV in one click.'],
      ];
      foreach ($features as $f): ?>
      <div class="feature-card bg-white rounded-2xl p-6 sm:p-7 reveal"
           style="box-shadow:0 2px 12px rgba(0,0,0,.06);border:1px solid #e2e8f0;border-top:3px solid <?= $f['color'] ?>;">
        <div class="flex items-start justify-between mb-5">
          <div class="card-icon-wrap w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0"
               style="background:<?= $f['color'] ?>18;">
            <i class="bi bi-<?= $f['icon'] ?> text-2xl" style="color:<?= $f['color'] ?>;"></i>
          </div>
          <span class="text-xs font-bold px-3 py-1 rounded-full flex-shrink-0"
                style="background:<?= $f['color'] ?>12;color:<?= $f['color'] ?>;">
            <?= $f['tag'] ?>
          </span>
        </div>
        <h3 class="font-bold text-slate-800 text-base sm:text-lg mb-2"><?= $f['title'] ?></h3>
        <p class="text-slate-500 text-sm leading-relaxed"><?= $f['desc'] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════
     HOW IT WORKS
══════════════════════════════════════════════════════ -->
<section id="how-it-works" class="py-16 sm:py-24" style="background:linear-gradient(160deg,#f0f4ff 0%,#faf5ff 50%,#f0fdf4 100%);">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">

    <div class="text-center mb-12 sm:mb-16 reveal">
      <div class="inline-flex items-center gap-2 text-xs font-bold px-4 py-2 rounded-full mb-5"
           style="background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.2);color:#6366f1;">
        <i class="bi bi-arrow-right-circle-fill"></i> Simple Process
      </div>
      <h2 class="font-extrabold text-slate-900 mb-4 tracking-tight"
          style="font-size:clamp(1.8rem,4vw,3rem);">
        From First Contact to <span class="g-text">Closed Deal</span>
      </h2>
      <p class="text-slate-500 max-w-lg mx-auto" style="font-size:clamp(.9rem,2vw,1.05rem);">
        Four simple steps to turn strangers into paying clients.
      </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 sm:gap-6">
      <?php
      $steps = [
        ['num'=>'01','icon'=>'envelope-arrow-down-fill','color'=>'#6366f1',
         'title'=>'Lead Submits Form',
         'desc'=>'A visitor fills your contact form. Their info is saved automatically to your CRM.'],
        ['num'=>'02','icon'=>'eye-fill','color'=>'#8b5cf6',
         'title'=>'You Review Instantly',
         'desc'=>'Log in and see the new lead on your dashboard with all their details.'],
        ['num'=>'03','icon'=>'chat-dots-fill','color'=>'#ec4899',
         'title'=>'Follow Up & Track',
         'desc'=>'Update status, add notes, and set follow-up dates to stay on top of every lead.'],
        ['num'=>'04','icon'=>'trophy-fill','color'=>'#22c55e',
         'title'=>'Convert to Client',
         'desc'=>'Mark the lead as Converted and watch your business grow with real analytics.'],
      ];
      foreach ($steps as $s): ?>
      <div class="step-card bg-white rounded-2xl p-6 border border-slate-200 reveal"
           style="box-shadow:0 2px 12px rgba(0,0,0,.06);border-left:4px solid <?= $s['color'] ?>;">
        <div class="flex items-center gap-3 mb-5">
          <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0"
               style="background:<?= $s['color'] ?>15;">
            <i class="bi bi-<?= $s['icon'] ?> text-2xl" style="color:<?= $s['color'] ?>;"></i>
          </div>
          <span class="text-4xl font-black" style="color:<?= $s['color'] ?>25;"><?= $s['num'] ?></span>
        </div>
        <h3 class="font-bold text-slate-800 text-base mb-2"><?= $s['title'] ?></h3>
        <p class="text-slate-500 text-sm leading-relaxed"><?= $s['desc'] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════
     TESTIMONIALS
══════════════════════════════════════════════════════ -->
<section id="testimonials" class="py-16 sm:py-24" style="background:#ffffff;">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">

    <div class="text-center mb-12 sm:mb-16 reveal">
      <div class="inline-flex items-center gap-2 text-xs font-bold px-4 py-2 rounded-full mb-5"
           style="background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.2);color:#6366f1;">
        <i class="bi bi-chat-quote-fill"></i> Testimonials
      </div>
      <h2 class="font-extrabold text-slate-900 mb-4 tracking-tight"
          style="font-size:clamp(1.8rem,4vw,3rem);">
        What People Are <span class="g-text">Saying</span>
      </h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
      <?php
      $testimonials = [
        ['name'=>'Sarah Mitchell','role'=>'Freelance Designer','av'=>'SM','color'=>'#6366f1',
         'text'=>'CRM Pulse completely changed how I manage client inquiries. I never miss a follow-up anymore. The dashboard is clean and intuitive — exactly what I needed.'],
        ['name'=>'James Okonkwo','role'=>'Agency Owner','av'=>'JO','color'=>'#8b5cf6',
         'text'=>'We went from losing leads in spreadsheets to having a proper system. The CSV export and pipeline view are game changers for our weekly team reviews.'],
        ['name'=>'Priya Sharma','role'=>'Startup Founder','av'=>'PS','color'=>'#ec4899',
         'text'=>'The overdue alerts alone saved us 3 deals last month. Simple, fast, and professional. This is exactly what a small business needs to compete.'],
      ];
      foreach ($testimonials as $t): ?>
      <div class="testimonial-card bg-white rounded-2xl p-6 sm:p-7 border border-slate-200 reveal"
           style="box-shadow:0 2px 12px rgba(0,0,0,.06);border-top:3px solid <?= $t['color'] ?>;">
        <div class="flex items-center justify-between mb-5">
          <div class="flex gap-1">
            <?php for ($i = 0; $i < 5; $i++): ?>
              <i class="bi bi-star-fill text-amber-400 text-sm"></i>
            <?php endfor; ?>
          </div>
          <i class="bi bi-quote text-3xl" style="color:<?= $t['color'] ?>25;"></i>
        </div>
        <p class="text-slate-600 text-sm leading-relaxed mb-6 italic">"<?= $t['text'] ?>"</p>
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
               style="background:linear-gradient(135deg,<?= $t['color'] ?>,<?= $t['color'] ?>99);">
            <?= $t['av'] ?>
          </div>
          <div>
            <p class="font-bold text-slate-800 text-sm"><?= $t['name'] ?></p>
            <p class="text-slate-400 text-xs"><?= $t['role'] ?></p>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════
     PRICING
══════════════════════════════════════════════════════ -->
<section id="pricing" class="py-16 sm:py-24" style="background:#f8fafc;">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">

    <div class="text-center mb-12 sm:mb-16 reveal">
      <div class="inline-flex items-center gap-2 text-xs font-bold px-4 py-2 rounded-full mb-5"
           style="background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.2);color:#6366f1;">
        <i class="bi bi-tag-fill"></i> Pricing
      </div>
      <h2 class="font-extrabold text-slate-900 mb-4 tracking-tight"
          style="font-size:clamp(1.8rem,4vw,3rem);">
        Simple, Transparent <span class="g-text">Pricing</span>
      </h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 sm:gap-6 max-w-5xl mx-auto">
      <?php
      $plans = [
        ['name'=>'Starter','price'=>'Free','period'=>'forever','color'=>'#64748b','popular'=>false,
         'features'=>['Up to 50 leads','Basic dashboard','CSV export','Email support'],
         'cta'=>'Get Started'],
        ['name'=>'Pro','price'=>'$29','period'=>'/month','color'=>'#6366f1','popular'=>true,
         'features'=>['Unlimited leads','Advanced analytics','Priority support','Activity logs','Team access'],
         'cta'=>'Start Free Trial'],
        ['name'=>'Agency','price'=>'$79','period'=>'/month','color'=>'#8b5cf6','popular'=>false,
         'features'=>['Everything in Pro','Multiple workspaces','API access','Custom branding','Dedicated support'],
         'cta'=>'Contact Sales'],
      ];
      foreach ($plans as $p): ?>
      <div class="reveal relative <?= $p['popular'] ? 'sm:scale-105 sm:z-10' : '' ?>">
        <?php if ($p['popular']): ?>
          <div class="absolute -top-4 left-1/2 -translate-x-1/2 text-white text-xs font-bold px-4 py-1.5 rounded-full z-10 btn-glow whitespace-nowrap">
            ⭐ Most Popular
          </div>
        <?php endif; ?>
        <div class="bg-white rounded-2xl p-7 border h-full flex flex-col
                    <?= $p['popular'] ? 'border-indigo-300' : 'border-slate-200' ?>"
             style="box-shadow:<?= $p['popular'] ? '0 16px 40px rgba(99,102,241,.15)' : '0 1px 3px rgba(0,0,0,.06)' ?>;">
          <div class="mb-6">
            <p class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-2"><?= $p['name'] ?></p>
            <div class="flex items-end gap-1">
              <span class="font-black" style="font-size:2.8rem;color:<?= $p['color'] ?>;line-height:1;"><?= $p['price'] ?></span>
              <span class="text-slate-400 text-sm mb-1"><?= $p['period'] ?></span>
            </div>
          </div>
          <ul class="space-y-3 mb-8 flex-1">
            <?php foreach ($p['features'] as $feat): ?>
            <li class="flex items-center gap-3 text-sm text-slate-600">
              <div class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0"
                   style="background:<?= $p['color'] ?>15;">
                <i class="bi bi-check-lg" style="color:<?= $p['color'] ?>;font-size:.65rem;"></i>
              </div>
              <?= $feat ?>
            </li>
            <?php endforeach; ?>
          </ul>
          <a href="<?= BASE_URL ?>/register.php"
             class="block text-center font-bold py-3 rounded-xl transition-all text-sm
                    <?= $p['popular'] ? 'btn-glow text-white' : 'border-2 border-slate-200 text-slate-700 hover:border-indigo-300 hover:text-indigo-600' ?>">
            <?= $p['cta'] ?>
          </a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════
     FINAL CTA
══════════════════════════════════════════════════════ -->
<section class="py-20 sm:py-28 relative overflow-hidden"
         style="background:linear-gradient(135deg,#0f172a 0%,#1e1b4b 100%);">
  <div class="orb orb1" style="opacity:.4;"></div>
  <div class="orb orb2" style="opacity:.4;"></div>

  <div class="relative z-10 max-w-3xl mx-auto px-4 sm:px-6 text-center reveal">
    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl sm:rounded-3xl flex items-center justify-center mx-auto mb-6 btn-glow">
      <i class="bi bi-lightning-charge-fill text-white text-2xl sm:text-3xl"></i>
    </div>
    <h2 class="font-black text-white mb-5 tracking-tight leading-tight"
        style="font-size:clamp(2rem,5vw,3.5rem);">
      Ready to Grow<br><span class="g-text">Your Business?</span>
    </h2>
    <p class="text-slate-400 mb-8 max-w-xl mx-auto leading-relaxed"
       style="font-size:clamp(.95rem,2vw,1.2rem);">
      Join hundreds of businesses already using CRM Pulse to manage leads, follow up faster, and close more deals.
    </p>
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4">
      <a href="<?= BASE_URL ?>/register.php"
         class="btn-glow w-full sm:w-auto text-white font-bold px-8 py-4 rounded-2xl text-base flex items-center justify-center gap-2">
        <i class="bi bi-person-plus-fill"></i> Create Free Account
      </a>
      <a href="<?= BASE_URL ?>/login.php"
         class="btn-outline w-full sm:w-auto text-white font-bold px-8 py-4 rounded-2xl text-base flex items-center justify-center gap-2">
        <i class="bi bi-box-arrow-in-right"></i> Sign In
      </a>
    </div>
    <p class="text-slate-600 text-sm mt-6">No credit card required · Free forever plan available</p>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════
     FOOTER
══════════════════════════════════════════════════════ -->
<footer style="background:#020617;border-top:1px solid rgba(255,255,255,.05);">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10 sm:py-14">
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-8 mb-10">

      <div class="col-span-2 sm:col-span-2">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-9 h-9 rounded-xl flex items-center justify-center btn-glow flex-shrink-0">
            <i class="bi bi-lightning-charge-fill text-white"></i>
          </div>
          <span class="font-extrabold text-lg text-white">CRM <span style="color:#818cf8;">Pulse</span></span>
        </div>
        <p class="text-slate-500 text-sm leading-relaxed max-w-xs mb-3">
          A powerful Mini CRM system built for businesses, freelancers, and agencies to manage leads and grow faster.
        </p>
        <p class="text-slate-600 text-xs">
          Built for <span style="color:#818cf8;font-weight:600;">Future Interns</span> · Full Stack Task 2 · 2026
        </p>
      </div>

      <div>
        <p class="text-white font-bold text-sm mb-4">Product</p>
        <ul class="space-y-2.5">
          <?php foreach (['Features','How It Works','Pricing','Reports'] as $l): ?>
          <li><a href="#" class="text-slate-500 hover:text-white text-sm transition-colors"><?= $l ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div>
        <p class="text-white font-bold text-sm mb-4">Account</p>
        <ul class="space-y-2.5">
          <li><a href="<?= BASE_URL ?>/login.php"     class="text-slate-500 hover:text-white text-sm transition-colors">Sign In</a></li>
          <li><a href="<?= BASE_URL ?>/register.php"  class="text-slate-500 hover:text-white text-sm transition-colors">Register</a></li>
          <li><a href="<?= BASE_URL ?>/dashboard.php" class="text-slate-500 hover:text-white text-sm transition-colors">Dashboard</a></li>
        </ul>
      </div>
    </div>

    <div class="border-t border-white/5 pt-8 flex flex-col sm:flex-row items-center justify-between gap-3">
      <p class="text-slate-600 text-sm text-center sm:text-left">© 2026 CRM Pulse. All rights reserved.</p>
      <p class="text-slate-700 text-xs text-center">PHP 8 · MySQL · Tailwind CSS · OOP Architecture</p>
    </div>
  </div>
</footer>

<script>
// ── Navbar scroll ─────────────────────────────────────────
window.addEventListener('scroll', () => {
    document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 30);
});

// ── Mobile menu toggle ────────────────────────────────────
const mobileMenuBtn = document.getElementById('mobileMenuBtn');
const mobileMenu    = document.getElementById('mobileMenu');
const menuIcon      = document.getElementById('menuIcon');

mobileMenuBtn?.addEventListener('click', () => {
    const isOpen = mobileMenu.classList.toggle('open');
    menuIcon.className = isOpen ? 'bi bi-x-lg text-2xl' : 'bi bi-list text-2xl';
});

// Close mobile menu when a link is clicked
mobileMenu?.querySelectorAll('a').forEach(a => {
    a.addEventListener('click', () => {
        mobileMenu.classList.remove('open');
        menuIcon.className = 'bi bi-list text-2xl';
    });
});

// ── Smooth scroll ─────────────────────────────────────────
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const target = document.querySelector(a.getAttribute('href'));
        if (target) {
            e.preventDefault();
            const offset = 68; // navbar height
            const top = target.getBoundingClientRect().top + window.scrollY - offset;
            window.scrollTo({ top, behavior: 'smooth' });
        }
    });
});

// ── Scroll reveal ─────────────────────────────────────────
const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry, i) => {
        if (entry.isIntersecting) {
            setTimeout(() => entry.target.classList.add('visible'), i * 70);
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.08 });

document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>
</body>
</html>
