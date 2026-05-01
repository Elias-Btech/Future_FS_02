<?php
require_once dirname(dirname(__DIR__)) . '/app/bootstrap.php';
use App\Core\{Auth, Helper};
use App\Models\Lead;

Auth::require();
$admin     = Auth::admin();
$leadModel = new Lead();
$stats     = $leadModel->stats();

// ── Filters from GET ──────────────────────────────────────
$filters = [
    'search'   => trim($_GET['search']   ?? ''),
    'status'   => trim($_GET['status']   ?? ''),
    'priority' => trim($_GET['priority'] ?? ''),
    'source'   => trim($_GET['source']   ?? ''),
    'date_from'=> trim($_GET['date_from']?? ''),
    'date_to'  => trim($_GET['date_to']  ?? ''),
];

// ── Pagination ────────────────────────────────────────────
$perPage = 15;
$page    = max(1, (int)($_GET['page'] ?? 1));
$total   = $leadModel->count($filters);
$pag     = Helper::paginate($total, $perPage, $page);
$leads   = $leadModel->getAll($filters, $perPage, $pag['offset']);

$pageTitle    = 'All Leads – ' . APP_NAME;
$pageSubtitle = 'Manage Leads';
$activePage   = 'leads';

ob_start();
?>

<!-- Header -->
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
  <div>
    <h2 class="text-xl font-bold text-slate-800">All Leads</h2>
    <p class="text-slate-500 text-sm mt-0.5"><?= $total ?> total leads found</p>
  </div>
  <div class="flex items-center gap-2">
    <a href="<?= BASE_URL ?>/reports/index.php?export=csv"
       class="flex items-center gap-2 border border-slate-300 text-slate-600 hover:bg-slate-50 text-sm font-medium px-4 py-2 rounded-xl transition-colors">
      <i class="bi bi-download"></i> Export CSV
    </a>
    <a href="<?= BASE_URL ?>/leads/create.php"
       class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors shadow-sm shadow-indigo-500/20">
      <i class="bi bi-plus-lg"></i> Add Lead
    </a>
  </div>
</div>

<!-- ── Filter Bar ─────────────────────────────────────── -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-4">
  <form method="GET" class="flex flex-wrap gap-3 items-end">
    <!-- Search -->
    <div class="flex-1 min-w-48">
      <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wider">Search</label>
      <div class="relative">
        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
        <input type="text" name="search" value="<?= Helper::e($filters['search']) ?>"
               class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition"
               placeholder="Name, email, company…">
      </div>
    </div>

    <!-- Status -->
    <div class="min-w-36">
      <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wider">Status</label>
      <select name="status" class="w-full py-2 px-3 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition bg-white">
        <option value="">All Status</option>
        <?php foreach (['New','Contacted','Follow-up','Converted','Closed'] as $s): ?>
          <option value="<?= $s ?>" <?= $filters['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Priority -->
    <div class="min-w-32">
      <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wider">Priority</label>
      <select name="priority" class="w-full py-2 px-3 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition bg-white">
        <option value="">All Priority</option>
        <?php foreach (['High','Medium','Low'] as $p): ?>
          <option value="<?= $p ?>" <?= $filters['priority'] === $p ? 'selected' : '' ?>><?= $p ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Source -->
    <div class="min-w-32">
      <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wider">Source</label>
      <select name="source" class="w-full py-2 px-3 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition bg-white">
        <option value="">All Sources</option>
        <?php foreach (['Website','Google','Facebook','LinkedIn','Instagram','Referral','Cold Call','Other'] as $src): ?>
          <option value="<?= $src ?>" <?= $filters['source'] === $src ? 'selected' : '' ?>><?= $src ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Date From -->
    <div class="min-w-36">
      <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wider">From</label>
      <input type="date" name="date_from" value="<?= Helper::e($filters['date_from']) ?>"
             class="w-full py-2 px-3 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition">
    </div>

    <!-- Date To -->
    <div class="min-w-36">
      <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wider">To</label>
      <input type="date" name="date_to" value="<?= Helper::e($filters['date_to']) ?>"
             class="w-full py-2 px-3 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition">
    </div>

    <div class="flex gap-2">
      <button type="submit"
              class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">
        <i class="bi bi-funnel-fill"></i> Filter
      </button>
      <a href="<?= BASE_URL ?>/leads/index.php"
         class="flex items-center gap-2 border border-slate-300 text-slate-600 hover:bg-slate-50 text-sm font-medium px-4 py-2 rounded-xl transition-colors">
        <i class="bi bi-x-lg"></i> Clear
      </a>
    </div>
  </form>
</div>

<!-- ── Leads Table ────────────────────────────────────── -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="bg-slate-50 border-b border-slate-200">
          <th class="text-left px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Lead</th>
          <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Company</th>
          <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Source</th>
          <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
          <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Priority</th>
          <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Follow-up</th>
          <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Added</th>
          <th class="text-right px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        <?php if (empty($leads)): ?>
          <tr><td colspan="8" class="text-center py-16 text-slate-400">
            <i class="bi bi-inbox text-4xl block mb-3 opacity-20"></i>
            <p class="font-medium">No leads found</p>
            <p class="text-xs mt-1">Try adjusting your filters or <a href="<?= BASE_URL ?>/leads/create.php" class="text-indigo-600 font-semibold">add a new lead</a></p>
          </td></tr>
        <?php else: ?>
          <?php foreach ($leads as $lead):
            $today    = date('Y-m-d');
            $isOverdue= $lead['next_followup_date'] && $lead['next_followup_date'] < $today
                        && !in_array($lead['status'], ['Converted','Closed']);
          ?>
          <tr class="hover:bg-slate-50 transition-colors <?= $isOverdue ? 'border-l-2 border-l-red-400' : '' ?>">
            <td class="px-5 py-4">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold flex-shrink-0">
                  <?= Helper::initials($lead['name']) ?>
                </div>
                <div>
                  <a href="<?= BASE_URL ?>/leads/view.php?id=<?= $lead['id'] ?>"
                     class="font-semibold text-slate-800 hover:text-indigo-600 transition-colors">
                    <?= Helper::e($lead['name']) ?>
                  </a>
                  <div class="text-xs text-slate-400"><?= Helper::e($lead['email']) ?></div>
                  <?php if ($lead['phone']): ?>
                    <div class="text-xs text-slate-400"><?= Helper::e($lead['phone']) ?></div>
                  <?php endif; ?>
                </div>
              </div>
            </td>
            <td class="px-4 py-4 text-sm text-slate-600"><?= Helper::e($lead['company'] ?: '—') ?></td>
            <td class="px-4 py-4">
              <span class="bg-slate-100 text-slate-600 text-xs font-medium px-2.5 py-1 rounded-lg">
                <?= Helper::e($lead['source'] ?: '—') ?>
              </span>
            </td>
            <td class="px-4 py-4">
              <span class="text-xs font-semibold px-2.5 py-1 rounded-full <?= Helper::statusClass($lead['status']) ?>">
                <?= Helper::e($lead['status']) ?>
              </span>
            </td>
            <td class="px-4 py-4">
              <span class="text-xs font-semibold px-2.5 py-1 rounded-full <?= Helper::priorityClass($lead['priority']) ?>">
                <?= Helper::e($lead['priority']) ?>
              </span>
            </td>
            <td class="px-4 py-4">
              <?php if ($lead['next_followup_date']): ?>
                <span class="text-xs font-medium <?= $isOverdue ? 'text-red-600 font-bold' : 'text-slate-600' ?>">
                  <?= $isOverdue ? '⚠ ' : '' ?><?= Helper::formatDate($lead['next_followup_date'], 'M j, Y') ?>
                </span>
              <?php else: ?>
                <span class="text-slate-300 text-xs">—</span>
              <?php endif; ?>
            </td>
            <td class="px-4 py-4 text-xs text-slate-400 whitespace-nowrap">
              <?= Helper::formatDate($lead['created_at']) ?>
            </td>
            <td class="px-5 py-4">
              <div class="flex items-center justify-end gap-1">
                <a href="<?= BASE_URL ?>/leads/view.php?id=<?= $lead['id'] ?>"
                   class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 transition-colors" title="View">
                  <i class="bi bi-eye text-sm"></i>
                </a>
                <a href="<?= BASE_URL ?>/leads/edit.php?id=<?= $lead['id'] ?>"
                   class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:bg-amber-50 hover:text-amber-600 transition-colors" title="Edit">
                  <i class="bi bi-pencil text-sm"></i>
                </a>
                <a href="<?= BASE_URL ?>/leads/delete.php?id=<?= $lead['id'] ?>"
                   onclick="return confirm('Delete this lead? This cannot be undone.')"
                   class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors" title="Delete">
                  <i class="bi bi-trash text-sm"></i>
                </a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <?php if ($pag['total_pages'] > 1): ?>
  <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between">
    <p class="text-xs text-slate-500">
      Showing <?= $pag['offset'] + 1 ?>–<?= min($pag['offset'] + $perPage, $total) ?> of <?= $total ?> leads
    </p>
    <div class="flex items-center gap-1">
      <?php if ($pag['has_prev']): ?>
        <a href="?<?= http_build_query(array_merge($filters, ['page' => $page - 1])) ?>"
           class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 text-sm transition-colors">
          <i class="bi bi-chevron-left"></i>
        </a>
      <?php endif; ?>
      <?php for ($i = max(1, $page-2); $i <= min($pag['total_pages'], $page+2); $i++): ?>
        <a href="?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>"
           class="w-8 h-8 flex items-center justify-center rounded-lg text-xs font-semibold transition-colors
                  <?= $i === $page ? 'bg-indigo-600 text-white shadow-sm' : 'border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>
      <?php if ($pag['has_next']): ?>
        <a href="?<?= http_build_query(array_merge($filters, ['page' => $page + 1])) ?>"
           class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 text-sm transition-colors">
          <i class="bi bi-chevron-right"></i>
        </a>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/views/layouts/main.php';
