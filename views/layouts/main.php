<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? APP_NAME ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/../assets/css/custom.css">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
          colors: {
            primary: '#6366f1',
            sidebar: '#0f172a'
          },
          boxShadow: {
            'card': '0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04)',
            'card-hover': '0 8px 25px rgba(0,0,0,.09)',
            'indigo': '0 4px 14px rgba(99,102,241,.35)',
          }
        }
      }
    }
  </script>
  <style>
    body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }

    /* ── Heading font ── */
    h1, h2, h3, h4, h5, h6 {
      font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    }

    /* ── Sidebar nav links ── */
    .nav-link {
      display: flex; align-items: center; gap: 10px;
      padding: 9px 12px; border-radius: 8px;
      color: #94a3b8; font-size: .825rem; font-weight: 500;
      text-decoration: none; transition: all .15s ease;
      margin-bottom: 2px; border: 1px solid transparent;
      white-space: nowrap;
    }
    .nav-link:hover { background: rgba(255,255,255,.06); color: #e2e8f0; }
    .nav-link.active {
      background: #6366f1; color: #fff;
      box-shadow: 0 4px 14px rgba(99,102,241,.35);
    }
    .nav-link i { width: 18px; text-align: center; font-size: .95rem; flex-shrink: 0; }
    .nav-link span { flex: 1; }

    /* ── Scrollbar ── */
    ::-webkit-scrollbar { width: 5px; height: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }

    /* ── Stat cards ── */
    .stat-card { transition: transform .2s ease, box-shadow .2s ease; cursor: pointer; }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,.1); }

    /* ── Counter ── */
    .counter { display: inline-block; }

    /* ── Flash messages ── */
    .flash-msg { transition: opacity .6s ease, transform .6s ease; }

    /* ══════════════════════════════════════════
       DARK MODE
    ══════════════════════════════════════════ */
    body.dark-mode { background: #0f172a !important; color: #e2e8f0; }

    /* Topbar */
    body.dark-mode header {
      background: #1e293b !important;
      border-color: #334155 !important;
    }
    body.dark-mode header h1 { color: #f1f5f9 !important; }
    body.dark-mode header p  { color: #94a3b8 !important; }
    body.dark-mode #darkToggle {
      background: #334155 !important;
      border-color: #475569 !important;
      color: #94a3b8 !important;
    }

    /* Main content bg */
    body.dark-mode main { background: #0f172a !important; }

    /* White cards → dark surface */
    body.dark-mode .bg-white,
    body.dark-mode [class*="bg-white"] {
      background: #1e293b !important;
      border-color: #334155 !important;
    }

    /* Stat cards */
    body.dark-mode .stat-card { background: #1e293b !important; border-color: #334155 !important; }
    body.dark-mode .stat-card p.text-slate-800,
    body.dark-mode .stat-card .text-slate-800 { color: #f1f5f9 !important; }
    body.dark-mode .stat-card .text-slate-500,
    body.dark-mode .stat-card .text-slate-400 { color: #94a3b8 !important; }

    /* Table */
    body.dark-mode table thead tr { background: #0f172a !important; border-color: #334155 !important; }
    body.dark-mode table thead th { color: #64748b !important; }
    body.dark-mode table tbody tr { border-color: #1e293b !important; }
    body.dark-mode table tbody tr:hover { background: #0f172a !important; }
    body.dark-mode table td { color: #cbd5e1 !important; }
    body.dark-mode table td a { color: #a5b4fc !important; }

    /* Panel headers */
    body.dark-mode .border-slate-100 { border-color: #334155 !important; }
    body.dark-mode .border-slate-200 { border-color: #334155 !important; }
    body.dark-mode .bg-slate-100 { background: #334155 !important; }
    body.dark-mode .bg-slate-50  { background: #0f172a !important; }

    /* Text */
    body.dark-mode .text-slate-800 { color: #f1f5f9 !important; }
    body.dark-mode .text-slate-700 { color: #e2e8f0 !important; }
    body.dark-mode .text-slate-600 { color: #cbd5e1 !important; }
    body.dark-mode .text-slate-500 { color: #94a3b8 !important; }
    body.dark-mode .text-slate-400 { color: #64748b !important; }

    /* Progress bars track */
    body.dark-mode .bg-slate-100.rounded-full { background: #334155 !important; }

    /* Activity feed */
    body.dark-mode .border-slate-50 { border-color: #1e293b !important; }
    body.dark-mode .hover\:bg-slate-50:hover { background: #0f172a !important; }

    /* Forms */
    body.dark-mode input,
    body.dark-mode select,
    body.dark-mode textarea {
      background: #0f172a !important;
      border-color: #334155 !important;
      color: #e2e8f0 !important;
    }
    body.dark-mode input::placeholder,
    body.dark-mode textarea::placeholder { color: #475569 !important; }

    /* Welcome banner */
    body.dark-mode h2.text-slate-800 { color: #f1f5f9 !important; }
    body.dark-mode p.text-slate-500  { color: #94a3b8 !important; }
  </style>
</head>
<body class="bg-[#f1f5f9]" id="app">

<!-- ════════════════════════════════════════
     SIDEBAR
════════════════════════════════════════ -->
<aside id="sidebar"
       class="fixed top-0 left-0 h-screen w-64 bg-[#0f172a] flex flex-col z-50">

  <!-- Brand -->
  <div class="flex items-center gap-3 px-5 py-6 border-b border-white/5 flex-shrink-0">
    <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center flex-shrink-0"
         style="box-shadow:0 4px 14px rgba(99,102,241,.4);">
      <i class="bi bi-lightning-charge-fill text-white text-lg"></i>
    </div>
    <div>
      <div class="text-white font-extrabold text-base tracking-tight leading-tight">
        CRM <span class="text-indigo-400">Pulse</span>
      </div>
      <div class="text-slate-500 text-xs mt-0.5">Mini CRM System</div>
    </div>
  </div>

  <!-- Nav -->
  <nav class="flex-1 px-3 py-5 overflow-y-auto space-y-0.5">

    <p class="text-slate-600 text-xs font-bold uppercase tracking-widest px-3 mb-3">Main Menu</p>

    <a href="<?= BASE_URL ?>/dashboard.php" class="nav-link <?= ($activePage??'')==='dashboard'?'active':'' ?>">
      <i class="bi bi-speedometer2"></i>
      <span>Dashboard</span>
    </a>

    <a href="<?= BASE_URL ?>/leads/index.php" class="nav-link <?= ($activePage??'')==='leads'?'active':'' ?>">
      <i class="bi bi-people-fill"></i>
      <span>All Leads</span>
      <?php if (!empty($stats['new_leads']) && $stats['new_leads'] > 0): ?>
        <span class="ml-auto text-xs font-bold px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300">
          <?= $stats['new_leads'] ?>
        </span>
      <?php endif; ?>
    </a>

    <a href="<?= BASE_URL ?>/leads/create.php" class="nav-link <?= ($activePage??'')==='lead-create'?'active':'' ?>">
      <i class="bi bi-person-plus-fill"></i>
      <span>Add Lead</span>
    </a>

    <a href="<?= BASE_URL ?>/leads/index.php?status=Follow-up" class="nav-link <?= ($activePage??'')==='followups'?'active':'' ?>">
      <i class="bi bi-calendar-check-fill"></i>
      <span>Follow-ups</span>
      <?php if (!empty($stats['overdue']) && $stats['overdue'] > 0): ?>
        <span class="ml-auto text-xs font-bold px-2 py-0.5 rounded-full bg-red-500/20 text-red-400">
          <?= $stats['overdue'] ?>
        </span>
      <?php endif; ?>
    </a>

    <a href="<?= BASE_URL ?>/reports/index.php" class="nav-link <?= ($activePage??'')==='reports'?'active':'' ?>">
      <i class="bi bi-bar-chart-line-fill"></i>
      <span>Reports</span>
    </a>

    <div class="border-t border-white/5 my-4"></div>
    <p class="text-slate-600 text-xs font-bold uppercase tracking-widest px-3 mb-3">Tools</p>

    <a href="<?= BASE_URL ?>/leads/index.php" class="nav-link">
      <i class="bi bi-search"></i>
      <span>Search Leads</span>
    </a>

    <div class="border-t border-white/5 my-4"></div>
    <a href="<?= BASE_URL ?>/home.php" class="nav-link" target="_blank">
      <i class="bi bi-house-fill"></i>
      <span>Public Homepage</span>
      <i class="bi bi-arrow-up-right ml-auto text-xs opacity-40"></i>
    </a>

  </nav>

  <!-- User Footer -->
  <div class="p-4 border-t border-white/5 flex-shrink-0">
    <div class="flex items-center gap-3 px-3 py-3 rounded-xl bg-white/5 border border-white/8">
      <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
        <?= \App\Core\Helper::initials($admin['name'] ?? 'Admin') ?>
      </div>
      <div class="flex-1 min-w-0">
        <div class="text-white text-sm font-semibold truncate"><?= \App\Core\Helper::e($admin['name'] ?? '') ?></div>
        <div class="text-slate-500 text-xs">Administrator</div>
      </div>
      <a href="<?= BASE_URL ?>/logout.php" title="Logout"
         class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 hover:bg-red-500/15 hover:text-red-400 transition-colors flex-shrink-0">
        <i class="bi bi-box-arrow-right"></i>
      </a>
    </div>
  </div>
</aside>

<!-- ════════════════════════════════════════
     TOPBAR
════════════════════════════════════════ -->
<header class="fixed top-0 left-0 right-0 md:left-64 z-40 bg-white border-b border-slate-200/80 flex items-center justify-between px-4 sm:px-6"
        id="topbar"
        style="height:64px; box-shadow:0 1px 3px rgba(0,0,0,.05);">

  <div class="flex items-center gap-3 min-w-0">
    <button id="sidebarToggle"
            class="md:hidden w-9 h-9 flex items-center justify-center rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors flex-shrink-0">
      <i class="bi bi-list text-xl"></i>
    </button>
    <div class="min-w-0">
      <h1 class="font-bold text-slate-800 text-sm sm:text-base leading-tight truncate"><?= $pageTitle ?? 'Dashboard' ?></h1>
      <?php if (!empty($pageSubtitle)): ?>
        <p class="text-slate-400 text-xs hidden sm:block"><?= \App\Core\Helper::e($pageSubtitle) ?></p>
      <?php endif; ?>
    </div>
  </div>

  <div class="flex items-center gap-2 flex-shrink-0">
    <button id="darkToggle"
            class="w-9 h-9 flex items-center justify-center rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors"
            title="Toggle dark mode">
      <i class="bi bi-moon-fill text-sm"></i>
    </button>
    <a href="<?= BASE_URL ?>/leads/create.php"
       class="flex items-center gap-1.5 text-white text-xs sm:text-sm font-semibold px-3 sm:px-4 py-2 rounded-xl transition-all whitespace-nowrap"
       style="background:#6366f1; box-shadow:0 4px 14px rgba(99,102,241,.35);"
       onmouseover="this.style.background='#4f46e5'" onmouseout="this.style.background='#6366f1'">
      <i class="bi bi-plus-lg"></i>
      <span class="hidden sm:inline">Add Lead</span>
      <span class="sm:hidden">Add</span>
    </a>
  </div>
</header>

<!-- ════════════════════════════════════════
     MAIN CONTENT
════════════════════════════════════════ -->
<main class="min-h-screen md:ml-64" style="padding-top:64px;">
  <div class="p-4 sm:p-6 lg:p-7">

    <!-- Flash Messages -->
    <?php
    $flashSuccess = \App\Core\Session::getFlash('success');
    $flashError   = \App\Core\Session::getFlash('error');
    ?>
    <?php if ($flashSuccess): ?>
      <div class="flash-msg mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3.5 rounded-2xl text-sm font-medium">
        <div class="w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center flex-shrink-0">
          <i class="bi bi-check-lg text-white text-xs"></i>
        </div>
        <?= \App\Core\Helper::e($flashSuccess) ?>
      </div>
    <?php endif; ?>
    <?php if ($flashError): ?>
      <div class="flash-msg mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3.5 rounded-2xl text-sm font-medium">
        <div class="w-6 h-6 bg-red-500 rounded-full flex items-center justify-center flex-shrink-0">
          <i class="bi bi-exclamation-lg text-white text-xs"></i>
        </div>
        <?= \App\Core\Helper::e($flashError) ?>
      </div>
    <?php endif; ?>

    <?= $content ?>

  </div>
</main>

<!-- Mobile overlay -->
<div id="sidebarOverlay"></div>

<script src="<?= BASE_URL ?>/../assets/js/app.js"></script>
</body>
</html>
