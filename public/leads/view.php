<?php
require_once dirname(dirname(__DIR__)) . '/app/bootstrap.php';
use App\Core\{Auth, Helper, Session};
use App\Core\Validator;
use App\Models\{Lead, FollowUp, ActivityLog};

Auth::require();
$admin     = Auth::admin();
$stats     = (new Lead())->stats();
$leadModel = new Lead();

$id   = (int)($_GET['id'] ?? 0);
$lead = $leadModel->findById($id);
if (!$lead) {
    Session::flash('error', 'Lead not found.');
    Helper::redirect(BASE_URL . '/leads/index.php');
}

$followUpModel = new FollowUp();
$followups     = $followUpModel->getByLead($id);
$fuErrors      = [];

// Handle add follow-up
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_followup'])) {
    $v = new Validator($_POST);
    $v->required('note', 'Note');
    if ($v->passes()) {
        $followUpModel->create([
            'lead_id'      => $id,
            'admin_id'     => $admin['id'],
            'note'         => $v->get('note'),
            'followup_date'=> $v->get('followup_date') ?: null,
        ]);
        (new ActivityLog())->log($admin['id'], 'Added follow-up note', $id);
        Session::flash('success', 'Follow-up note added.');
        Helper::redirect(BASE_URL . '/leads/view.php?id=' . $id);
    } else {
        $fuErrors = $v->errors();
    }
}

// Handle delete follow-up
if (isset($_GET['delete_fu'])) {
    $fuId = (int)$_GET['delete_fu'];
    $followUpModel->delete($fuId);
    (new ActivityLog())->log($admin['id'], 'Deleted follow-up note', $id);
    Helper::redirect(BASE_URL . '/leads/view.php?id=' . $id);
}

$today    = date('Y-m-d');
$isOverdue= $lead['next_followup_date'] && $lead['next_followup_date'] < $today
            && !in_array($lead['status'], ['Converted','Closed']);

$pageTitle    = Helper::e($lead['name']) . ' – ' . APP_NAME;
$pageSubtitle = 'Lead Detail';
$activePage   = 'leads';
ob_start();
?>

<!-- Back + Actions -->
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
  <a href="<?= BASE_URL ?>/leads/index.php"
     class="inline-flex items-center gap-2 text-slate-500 hover:text-indigo-600 text-sm font-medium transition-colors">
    <i class="bi bi-arrow-left"></i> Back to Leads
  </a>
  <div class="flex items-center gap-2">
    <a href="<?= BASE_URL ?>/leads/edit.php?id=<?= $id ?>"
       class="flex items-center gap-2 border border-slate-300 text-slate-600 hover:bg-slate-50 text-sm font-medium px-4 py-2 rounded-xl transition-colors">
      <i class="bi bi-pencil"></i> Edit
    </a>
    <a href="<?= BASE_URL ?>/leads/delete.php?id=<?= $id ?>"
       onclick="return confirm('Delete this lead permanently?')"
       class="flex items-center gap-2 border border-red-200 text-red-600 hover:bg-red-50 text-sm font-medium px-4 py-2 rounded-xl transition-colors">
      <i class="bi bi-trash"></i> Delete
    </a>
  </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

  <!-- Lead Info Card -->
  <div class="xl:col-span-2 space-y-5">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden <?= $isOverdue ? 'border-l-4 border-l-red-400' : '' ?>">
      <div class="px-6 py-5 border-b border-slate-100">
        <div class="flex items-start gap-4">
          <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl font-bold flex-shrink-0">
            <?= Helper::initials($lead['name']) ?>
          </div>
          <div class="flex-1">
            <h2 class="text-xl font-bold text-slate-800"><?= Helper::e($lead['name']) ?></h2>
            <?php if ($lead['company']): ?>
              <p class="text-slate-500 text-sm"><?= Helper::e($lead['company']) ?></p>
            <?php endif; ?>
            <div class="flex flex-wrap items-center gap-2 mt-2">
              <span class="text-xs font-semibold px-2.5 py-1 rounded-full <?= Helper::statusClass($lead['status']) ?>">
                <?= Helper::e($lead['status']) ?>
              </span>
              <span class="text-xs font-semibold px-2.5 py-1 rounded-full <?= Helper::priorityClass($lead['priority']) ?>">
                <?= Helper::e($lead['priority']) ?> Priority
              </span>
              <?php if ($isOverdue): ?>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-red-100 text-red-700">
                  ⚠ Overdue
                </span>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <?php
        $fields = [
          ['icon'=>'envelope',       'label'=>'Email',       'value'=>$lead['email'],                                'link'=>'mailto:'.$lead['email']],
          ['icon'=>'telephone',      'label'=>'Phone',       'value'=>$lead['phone'] ?: '—',                        'link'=>null],
          ['icon'=>'globe2',         'label'=>'Source',      'value'=>$lead['source'] ?: '—',                       'link'=>null],
          ['icon'=>'calendar-plus',  'label'=>'Added',       'value'=>Helper::formatDate($lead['created_at']),       'link'=>null],
          ['icon'=>'calendar-check', 'label'=>'Follow-up',   'value'=>Helper::formatDate($lead['next_followup_date']),'link'=>null],
          ['icon'=>'clock-history',  'label'=>'Last Updated','value'=>Helper::formatDate($lead['updated_at']),       'link'=>null],
        ];
        foreach ($fields as $f):
        ?>
        <div class="flex items-start gap-3">
          <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
            <i class="bi bi-<?= $f['icon'] ?> text-slate-500 text-sm"></i>
          </div>
          <div>
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider"><?= $f['label'] ?></div>
            <?php if ($f['link']): ?>
              <a href="<?= $f['link'] ?>" class="text-sm text-indigo-600 hover:underline font-medium"><?= Helper::e($f['value']) ?></a>
            <?php else: ?>
              <div class="text-sm text-slate-700 font-medium"><?= Helper::e($f['value']) ?></div>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <?php if ($lead['notes']): ?>
      <div class="px-6 pb-6">
        <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
          <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
            <i class="bi bi-sticky mr-1"></i> Notes
          </div>
          <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-wrap"><?= Helper::e($lead['notes']) ?></p>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- Follow-up History -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
      <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
        <i class="bi bi-chat-text-fill text-indigo-500"></i>
        <h3 class="font-bold text-slate-800 text-sm">Follow-up History</h3>
        <span class="bg-indigo-50 text-indigo-600 text-xs font-bold px-2 py-0.5 rounded-full"><?= count($followups) ?></span>
      </div>

      <?php if (empty($followups)): ?>
        <div class="text-center py-10 text-slate-400 text-sm">
          <i class="bi bi-chat-text text-3xl block mb-2 opacity-20"></i>
          No follow-up notes yet
        </div>
      <?php else: ?>
        <div class="divide-y divide-slate-50">
          <?php foreach ($followups as $fu): ?>
          <div class="px-6 py-4 flex items-start gap-3 hover:bg-slate-50 transition-colors">
            <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">
              <?= Helper::initials($fu['admin_name'] ?? 'A') ?>
            </div>
            <div class="flex-1">
              <div class="flex items-center justify-between gap-2 mb-1">
                <span class="text-xs font-semibold text-slate-700"><?= Helper::e($fu['admin_name'] ?? 'Admin') ?></span>
                <div class="flex items-center gap-2">
                  <?php if ($fu['followup_date']): ?>
                    <span class="text-xs text-slate-400"><?= Helper::formatDate($fu['followup_date']) ?></span>
                  <?php endif; ?>
                  <span class="text-xs text-slate-400"><?= Helper::timeAgo($fu['created_at']) ?></span>
                  <a href="?id=<?= $id ?>&delete_fu=<?= $fu['id'] ?>"
                     onclick="return confirm('Delete this note?')"
                     class="text-slate-300 hover:text-red-500 transition-colors">
                    <i class="bi bi-x-circle text-sm"></i>
                  </a>
                </div>
              </div>
              <p class="text-sm text-slate-600 leading-relaxed"><?= Helper::e($fu['note']) ?></p>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Add Follow-up Sidebar -->
  <div class="space-y-5">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
      <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
        <i class="bi bi-plus-circle-fill text-indigo-500"></i>
        <h3 class="font-bold text-slate-800 text-sm">Add Follow-up Note</h3>
      </div>
      <form method="POST" class="p-5 space-y-4">
        <input type="hidden" name="add_followup" value="1">
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Note <span class="text-red-500">*</span></label>
          <textarea name="note" rows="4"
                    class="w-full px-3 py-2.5 border <?= isset($fuErrors['note']) ? 'border-red-400' : 'border-slate-300' ?> rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition resize-none"
                    placeholder="Call summary, next steps, what was discussed…"></textarea>
          <?php if (isset($fuErrors['note'])): ?>
            <p class="text-red-500 text-xs mt-1"><?= Helper::e($fuErrors['note']) ?></p>
          <?php endif; ?>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Follow-up Date</label>
          <input type="date" name="followup_date"
                 class="w-full px-3 py-2.5 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition">
        </div>
        <button type="submit"
                class="w-full flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors shadow-sm shadow-indigo-500/20">
          <i class="bi bi-plus-lg"></i> Add Note
        </button>
      </form>
    </div>

    <!-- Quick Status Update -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
      <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
        <i class="bi bi-arrow-repeat text-indigo-500"></i>
        <h3 class="font-bold text-slate-800 text-sm">Quick Status Update</h3>
      </div>
      <div class="p-5 space-y-2">
        <?php foreach (['New','Contacted','Follow-up','Converted','Closed'] as $s): ?>
          <a href="<?= BASE_URL ?>/leads/update_status.php?id=<?= $id ?>&status=<?= urlencode($s) ?>"
             class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-sm font-medium
                    <?= $lead['status'] === $s
                        ? 'bg-indigo-600 text-white shadow-sm'
                        : 'text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
            <span class="w-2 h-2 rounded-full <?= $lead['status'] === $s ? 'bg-white' : 'bg-slate-300' ?>"></span>
            <?= $s ?>
            <?php if ($lead['status'] === $s): ?>
              <i class="bi bi-check-lg ml-auto"></i>
            <?php endif; ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/views/layouts/main.php';
