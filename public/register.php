<?php
require_once dirname(__DIR__) . '/app/bootstrap.php';
use App\Core\{Auth, Helper};
use App\Core\Validator;
use App\Models\Admin;

if (Auth::check()) { Helper::redirect(BASE_URL . '/dashboard.php'); }

// ── Secret invite code ─────────────────────────────────────
// Change this to anything you want — share it only with trusted people
define('INVITE_CODE', 'FUTURECRM2026');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Check invite code FIRST before anything else
    $enteredCode = trim($_POST['invite_code'] ?? '');
    if ($enteredCode !== INVITE_CODE) {
        $errors['invite_code'] = 'Invalid invite code. You are not authorized to register.';
    } else {
        // 2. Validate all other fields
        $v = new Validator($_POST);
        $v->required('full_name', 'Full Name')
          ->required('username',  'Username')->min('username', 3, 'Username')
          ->required('email',     'Email')->email('email', 'Email')
          ->required('password',  'Password')->min('password', 6, 'Password')
          ->required('confirm',   'Confirm Password');

        if ($v->passes() && $v->get('password') !== $v->get('confirm')) {
            $errors['confirm'] = 'Passwords do not match.';
        }

        if (empty($errors) && $v->passes()) {
            $adminModel = new Admin();
            if ($adminModel->exists($v->get('username'), $v->get('email'))) {
                $errors['general'] = 'Username or email already exists.';
            } else {
                $adminModel->create($v->all());
                \App\Core\Session::flash('success', 'Account created successfully! Please sign in.');
                Helper::redirect(BASE_URL . '/login.php');
            }
        } else {
            $errors = array_merge($v->errors(), $errors);
        }
    }
}

$pageTitle = 'Register – ' . APP_NAME;
ob_start();
?>
<div class="w-full max-w-md">
  <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-8">

    <!-- Back to Home -->
    <div class="mb-5">
      <a href="<?= BASE_URL ?>/home.php"
         class="inline-flex items-center gap-2 text-slate-400 hover:text-indigo-500 text-sm font-medium transition-colors">
        <i class="bi bi-arrow-left text-xs"></i> Back to Home
      </a>
    </div>

    <!-- Logo -->
    <div class="text-center mb-7">
      <div class="inline-flex items-center justify-center w-14 h-14 bg-indigo-600 rounded-2xl shadow-lg shadow-indigo-500/30 mb-4"
           style="box-shadow:0 8px 24px rgba(99,102,241,.4);">
        <i class="bi bi-lightning-charge-fill text-white text-2xl"></i>
      </div>
      <h1 class="text-2xl font-extrabold text-slate-800">CRM <span class="text-indigo-600">Pulse</span></h1>
      <p class="text-slate-500 text-sm mt-1">Create your admin account</p>
    </div>

    <!-- General error -->
    <?php if (!empty($errors['general'])): ?>
      <div class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
        <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
        <?= Helper::e($errors['general']) ?>
      </div>
    <?php endif; ?>

    <form method="POST" novalidate class="space-y-4">

      <!-- ── INVITE CODE (first field — most important) ── -->
      <div class="p-4 rounded-xl border-2 <?= isset($errors['invite_code']) ? 'border-red-300 bg-red-50' : 'border-indigo-200 bg-indigo-50' ?>">
        <label class="block text-sm font-bold text-indigo-700 mb-1.5 flex items-center gap-2">
          <i class="bi bi-shield-lock-fill"></i>
          Invite Code <span class="text-red-500">*</span>
        </label>
        <input type="text" name="invite_code"
               value="<?= Helper::e($_POST['invite_code'] ?? '') ?>"
               class="w-full px-4 py-2.5 border <?= isset($errors['invite_code']) ? 'border-red-400 bg-white' : 'border-indigo-300 bg-white' ?> rounded-xl text-sm font-mono tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition uppercase"
               placeholder="Enter your invite code"
               autocomplete="off"
               style="letter-spacing:.15em;">
        <?php if (isset($errors['invite_code'])): ?>
          <p class="text-red-600 text-xs mt-1.5 font-semibold flex items-center gap-1">
            <i class="bi bi-x-circle-fill"></i>
            <?= Helper::e($errors['invite_code']) ?>
          </p>
        <?php else: ?>
          <p class="text-indigo-500 text-xs mt-1.5 flex items-center gap-1">
            <i class="bi bi-info-circle"></i>
            You need an invite code to create an admin account.
          </p>
        <?php endif; ?>
      </div>

      <!-- Full Name -->
      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
        <div class="relative">
          <i class="bi bi-person-badge absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
          <input type="text" name="full_name" value="<?= Helper::e($_POST['full_name'] ?? '') ?>"
                 class="w-full pl-9 pr-4 py-2.5 border <?= isset($errors['full_name']) ? 'border-red-400 bg-red-50' : 'border-slate-300' ?> rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition"
                 placeholder="John Doe">
        </div>
        <?php if (isset($errors['full_name'])): ?>
          <p class="text-red-500 text-xs mt-1"><?= Helper::e($errors['full_name']) ?></p>
        <?php endif; ?>
      </div>

      <!-- Username -->
      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Username <span class="text-red-500">*</span></label>
        <div class="relative">
          <i class="bi bi-at absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
          <input type="text" name="username" value="<?= Helper::e($_POST['username'] ?? '') ?>"
                 class="w-full pl-9 pr-4 py-2.5 border <?= isset($errors['username']) ? 'border-red-400 bg-red-50' : 'border-slate-300' ?> rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition"
                 placeholder="johndoe">
        </div>
        <?php if (isset($errors['username'])): ?>
          <p class="text-red-500 text-xs mt-1"><?= Helper::e($errors['username']) ?></p>
        <?php endif; ?>
      </div>

      <!-- Email -->
      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email <span class="text-red-500">*</span></label>
        <div class="relative">
          <i class="bi bi-envelope absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
          <input type="email" name="email" value="<?= Helper::e($_POST['email'] ?? '') ?>"
                 class="w-full pl-9 pr-4 py-2.5 border <?= isset($errors['email']) ? 'border-red-400 bg-red-50' : 'border-slate-300' ?> rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition"
                 placeholder="john@example.com">
        </div>
        <?php if (isset($errors['email'])): ?>
          <p class="text-red-500 text-xs mt-1"><?= Helper::e($errors['email']) ?></p>
        <?php endif; ?>
      </div>

      <!-- Password -->
      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password <span class="text-red-500">*</span></label>
        <div class="relative">
          <i class="bi bi-lock absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
          <input type="password" name="password"
                 class="w-full pl-9 pr-4 py-2.5 border <?= isset($errors['password']) ? 'border-red-400 bg-red-50' : 'border-slate-300' ?> rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition"
                 placeholder="Min. 6 characters">
        </div>
        <?php if (isset($errors['password'])): ?>
          <p class="text-red-500 text-xs mt-1"><?= Helper::e($errors['password']) ?></p>
        <?php endif; ?>
      </div>

      <!-- Confirm Password -->
      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Confirm Password <span class="text-red-500">*</span></label>
        <div class="relative">
          <i class="bi bi-lock-fill absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
          <input type="password" name="confirm"
                 class="w-full pl-9 pr-4 py-2.5 border <?= isset($errors['confirm']) ? 'border-red-400 bg-red-50' : 'border-slate-300' ?> rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition"
                 placeholder="Repeat password">
        </div>
        <?php if (isset($errors['confirm'])): ?>
          <p class="text-red-500 text-xs mt-1"><?= Helper::e($errors['confirm']) ?></p>
        <?php endif; ?>
      </div>

      <button type="submit"
              class="w-full text-white font-semibold py-3 rounded-xl transition-all flex items-center justify-center gap-2 mt-2"
              style="background:linear-gradient(135deg,#6366f1,#8b5cf6);box-shadow:0 4px 14px rgba(99,102,241,.35);">
        <i class="bi bi-person-plus-fill"></i> Create Account
      </button>
    </form>

    <p class="text-center text-sm text-slate-500 mt-6">
      Already have an account?
      <a href="<?= BASE_URL ?>/login.php" class="text-indigo-600 font-semibold hover:underline">Sign in</a>
    </p>
  </div>
</div>
<?php
$content = ob_get_clean();
require ROOT_PATH . '/views/layouts/auth.php';
