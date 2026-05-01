<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? APP_NAME ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@600;700;800;900&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }

    /* ── Heading font ── */
    h1, h2, h3, h4, h5, h6 {
      font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    }
    .btn-glow {
      background: linear-gradient(135deg, #6366f1, #8b5cf6);
      box-shadow: 0 4px 14px rgba(99,102,241,.35);
      transition: all .2s ease;
    }
    .btn-glow:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(99,102,241,.45); }
  </style>
</head>
<body class="min-h-screen font-sans" style="background:linear-gradient(135deg,#0f172a 0%,#1e1b4b 60%,#0f172a 100%);">

  <!-- ── Top Navbar ── -->
  <nav class="fixed top-0 left-0 right-0 z-50 flex items-center justify-between px-4 sm:px-8"
       style="height:60px;background:rgba(15,23,42,.8);backdrop-filter:blur(16px);border-bottom:1px solid rgba(255,255,255,.06);">

    <!-- Logo / Back to Home -->
    <a href="<?= BASE_URL ?>/home.php"
       class="flex items-center gap-2.5 group">
      <div class="w-8 h-8 rounded-xl flex items-center justify-center btn-glow flex-shrink-0">
        <i class="bi bi-lightning-charge-fill text-white text-sm"></i>
      </div>
      <span class="font-extrabold text-base text-white tracking-tight">
        CRM <span style="color:#818cf8;">Pulse</span>
      </span>
    </a>

    <!-- Right nav links -->
    <div class="flex items-center gap-2 sm:gap-3">
      <a href="<?= BASE_URL ?>/home.php"
         class="flex items-center gap-1.5 text-white/60 hover:text-white text-sm font-medium transition-colors px-3 py-1.5 rounded-lg hover:bg-white/5">
        <i class="bi bi-house-fill text-xs"></i>
        <span class="hidden sm:inline">Home</span>
      </a>
      <a href="<?= BASE_URL ?>/login.php"
         class="flex items-center gap-1.5 text-white/60 hover:text-white text-sm font-medium transition-colors px-3 py-1.5 rounded-lg hover:bg-white/5
                <?= str_contains($pageTitle ?? '', 'Login') ? 'text-white bg-white/8' : '' ?>">
        <i class="bi bi-box-arrow-in-right text-xs"></i>
        <span class="hidden sm:inline">Sign In</span>
      </a>
      <a href="<?= BASE_URL ?>/register.php"
         class="btn-glow flex items-center gap-1.5 text-white text-sm font-semibold px-4 py-2 rounded-xl">
        <i class="bi bi-person-plus-fill text-xs"></i>
        <span class="hidden sm:inline">Register</span>
        <span class="sm:hidden">Join</span>
      </a>
    </div>
  </nav>

  <!-- ── Page Content ── -->
  <div class="min-h-screen flex items-center justify-center px-4 py-8" style="padding-top:80px;">
    <?= $content ?>
  </div>

</body>
</html>
