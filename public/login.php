<?php
require_once dirname(__DIR__) . '/app/bootstrap.php';
use App\Core\{Auth, Helper, Session};
use App\Core\Validator;
use App\Models\Admin;

// Already logged in → go to dashboard
if (Auth::check()) { Helper::redirect(BASE_URL . '/dashboard.php'); }

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $v = new Validator($_POST);
    $v->required('username', 'Username')
      ->required('password', 'Password');

    if ($v->fails()) {
        $errors = $v->errors();
    } else {
        $adminModel = new Admin();
        $user = $adminModel->findByUsername($v->get('username'));

        if ($user && $adminModel->verifyPassword($v->get('password'), $user['password'])) {
            Auth::login($user);
            (new \App\Models\ActivityLog())->log($user['id'], 'Logged in');
            Helper::redirect(BASE_URL . '/dashboard.php');
        } else {
            $errors['general'] = 'Invalid username or password.';
        }
    }
}

$pageTitle = 'Login – ' . APP_NAME;
ob_start();
?>
<div class="w-full max-w-md">
  <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-8">

    <!-- Back to Home -->
    <div class="mb-6">
      <a href="<?= BASE_URL ?>/home.php"
         class="inline-flex items-center gap-2 text-slate-400 hover:text-indigo-400 text-sm font-medium transition-colors">
        <i class="bi bi-arrow-left text-xs"></i> Back to Home
      </a>
    </div>

    <!-- Logo -->
    <div class="text-center mb-8">
      <div class="inline-flex items-center justify-center w-14 h-14 bg-indigo-600 rounded-2xl shadow-lg shadow-indigo-500/30 mb-4">
        <i class="bi bi-lightning-charge-fill text-white text-2xl"></i>
      </div>
      <h1 class="text-2xl font-extrabold text-slate-800">CRM <span class="text-indigo-600">Pulse</span></h1>
      <p class="text-slate-500 text-sm mt-1">Sign in to your admin panel</p>
    </div>

    <!-- Error -->
    <?php if (!empty($errors['general'])): ?>
      <div class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
        <i class="bi bi-exclamation-circle-fill"></i>
        <?= Helper::e($errors['general']) ?>
      </div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div class="mb-4">
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Username</label>
        <div class="relative">
          <i class="bi bi-person absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
          <input type="text" name="username"
                 value="<?= Helper::e($_POST['username'] ?? '') ?>"
                 class="w-full pl-9 pr-4 py-2.5 border <?= isset($errors['username']) ? 'border-red-400' : 'border-slate-300' ?> rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition"
                 placeholder="Enter username">
        </div>
        <?php if (isset($errors['username'])): ?>
          <p class="text-red-500 text-xs mt-1"><?= Helper::e($errors['username']) ?></p>
        <?php endif; ?>
      </div>

      <div class="mb-6">
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
        <div class="relative">
          <i class="bi bi-lock absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
          <input type="password" name="password"
                 class="w-full pl-9 pr-4 py-2.5 border <?= isset($errors['password']) ? 'border-red-400' : 'border-slate-300' ?> rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition"
                 placeholder="Enter password">
        </div>
        <?php if (isset($errors['password'])): ?>
          <p class="text-red-500 text-xs mt-1"><?= Helper::e($errors['password']) ?></p>
        <?php endif; ?>
      </div>

      <button type="submit"
              class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-xl transition-colors shadow-sm shadow-indigo-500/20 flex items-center justify-center gap-2">
        <i class="bi bi-box-arrow-in-right"></i> Sign In
      </button>
    </form>

    <p class="text-center text-sm text-slate-500 mt-6">
      Don't have an account?
      <a href="<?= BASE_URL ?>/register.php" class="text-indigo-600 font-semibold hover:underline">Register</a>
    </p>
  </div>
</div>
<?php
$content = ob_get_clean();
require ROOT_PATH . '/views/layouts/auth.php';
