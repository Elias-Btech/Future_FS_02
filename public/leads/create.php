<?php
require_once dirname(dirname(__DIR__)) . '/app/bootstrap.php';
use App\Core\{Auth, Helper, Session};
use App\Core\Validator;
use App\Models\{Lead, ActivityLog};

Auth::require();
$admin  = Auth::admin();
$stats  = (new Lead())->stats();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $v = new Validator($_POST);
    $v->required('name',  'Full Name')
      ->required('email', 'Email')->email('email', 'Email')
      ->required('status',   'Status')->in('status',   ['New','Contacted','Follow-up','Converted','Closed'], 'Status')
      ->required('priority', 'Priority')->in('priority', ['Low','Medium','High'], 'Priority');

    if ($v->passes()) {
        $leadModel = new Lead();
        $id = $leadModel->create([
            'name'              => $v->get('name'),
            'email'             => $v->get('email'),
            'phone'             => $v->get('phone'),
            'company'           => $v->get('company'),
            'source'            => $v->get('source'),
            'status'            => $v->get('status'),
            'priority'          => $v->get('priority'),
            'notes'             => $v->get('notes'),
            'next_followup_date'=> $v->get('next_followup_date') ?: null,
            'assigned_to'       => $admin['id'],
        ]);
        (new ActivityLog())->log($admin['id'], 'Created new lead', $id);
        Session::flash('success', 'Lead "' . $v->get('name') . '" created successfully.');
        Helper::redirect(BASE_URL . '/leads/index.php');
    } else {
        $errors = $v->errors();
    }
}

$pageTitle    = 'Add Lead – ' . APP_NAME;
$pageSubtitle = 'New Lead';
$activePage   = 'lead-create';

// Helper to render a field error
function fieldErr(array $errors, string $field): string {
    return isset($errors[$field])
        ? '<p class="text-red-500 text-xs mt-1">' . htmlspecialchars($errors[$field]) . '</p>'
        : '';
}
function fieldClass(array $errors, string $field): string {
    return isset($errors[$field]) ? 'border-red-400 bg-red-50' : 'border-slate-300';
}

ob_start();
?>

<div class="max-w-3xl mx-auto">

  <!-- Back -->
  <a href="<?= BASE_URL ?>/leads/index.php"
     class="inline-flex items-center gap-2 text-slate-500 hover:text-indigo-600 text-sm font-medium mb-5 transition-colors">
    <i class="bi bi-arrow-left"></i> Back to Leads
  </a>

  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3">
      <div class="w-9 h-9 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center">
        <i class="bi bi-person-plus-fill"></i>
      </div>
      <div>
        <h2 class="font-bold text-slate-800">Add New Lead</h2>
        <p class="text-slate-500 text-xs">Fill in the lead details below</p>
      </div>
    </div>

    <form method="POST" class="p-6 space-y-5">

      <!-- Row 1: Name + Email -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
          <input type="text" name="name" value="<?= Helper::e($_POST['name'] ?? '') ?>"
                 class="w-full px-4 py-2.5 border <?= fieldClass($errors,'name') ?> rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition"
                 placeholder="Jane Smith">
          <?= fieldErr($errors,'name') ?>
        </div>
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email <span class="text-red-500">*</span></label>
          <input type="email" name="email" value="<?= Helper::e($_POST['email'] ?? '') ?>"
                 class="w-full px-4 py-2.5 border <?= fieldClass($errors,'email') ?> rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition"
                 placeholder="jane@example.com">
          <?= fieldErr($errors,'email') ?>
        </div>
      </div>

      <!-- Row 2: Phone + Company -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1.5">Phone</label>
          <input type="tel" name="phone" value="<?= Helper::e($_POST['phone'] ?? '') ?>"
                 class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition"
                 placeholder="+1 234 567 8900">
        </div>
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1.5">Company</label>
          <input type="text" name="company" value="<?= Helper::e($_POST['company'] ?? '') ?>"
                 class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition"
                 placeholder="Acme Corp">
        </div>
      </div>

      <!-- Row 3: Source + Status + Priority -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1.5">Source</label>
          <select name="source" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition bg-white">
            <option value="">Select source</option>
            <?php foreach (['Website','Google','Facebook','LinkedIn','Instagram','Referral','Cold Call','Other'] as $s): ?>
              <option value="<?= $s ?>" <?= ($_POST['source'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status <span class="text-red-500">*</span></label>
          <select name="status" class="w-full px-4 py-2.5 border <?= fieldClass($errors,'status') ?> rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition bg-white">
            <?php foreach (['New','Contacted','Follow-up','Converted','Closed'] as $s): ?>
              <option value="<?= $s ?>" <?= ($_POST['status'] ?? 'New') === $s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
          </select>
          <?= fieldErr($errors,'status') ?>
        </div>
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1.5">Priority <span class="text-red-500">*</span></label>
          <select name="priority" class="w-full px-4 py-2.5 border <?= fieldClass($errors,'priority') ?> rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition bg-white">
            <?php foreach (['Low','Medium','High'] as $p): ?>
              <option value="<?= $p ?>" <?= ($_POST['priority'] ?? 'Medium') === $p ? 'selected' : '' ?>><?= $p ?></option>
            <?php endforeach; ?>
          </select>
          <?= fieldErr($errors,'priority') ?>
        </div>
      </div>

      <!-- Row 4: Next Follow-up Date -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1.5">Next Follow-up Date</label>
          <input type="date" name="next_followup_date" value="<?= Helper::e($_POST['next_followup_date'] ?? '') ?>"
                 class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition">
        </div>
      </div>

      <!-- Notes -->
      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Notes</label>
        <textarea name="notes" rows="4"
                  class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition resize-none"
                  placeholder="Initial notes, context, how they found you…"><?= Helper::e($_POST['notes'] ?? '') ?></textarea>
      </div>

      <!-- Actions -->
      <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
        <a href="<?= BASE_URL ?>/leads/index.php"
           class="px-5 py-2.5 border border-slate-300 text-slate-600 hover:bg-slate-50 text-sm font-medium rounded-xl transition-colors">
          Cancel
        </a>
        <button type="submit"
                class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-colors shadow-sm shadow-indigo-500/20">
          <i class="bi bi-person-plus-fill"></i> Create Lead
        </button>
      </div>
    </form>
  </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/views/layouts/main.php';
