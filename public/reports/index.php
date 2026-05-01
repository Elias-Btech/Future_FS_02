<?php
require_once dirname(dirname(__DIR__)) . '/app/bootstrap.php';
use App\Core\{Auth, Helper};
use App\Models\Lead;

Auth::require();
$admin     = Auth::admin();
$leadModel = new Lead();
$stats     = $leadModel->stats();

// ── CSV Export ────────────────────────────────────────────
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $leads = $leadModel->exportAll();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="leads_' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID','Name','Email','Phone','Company','Source','Status','Priority','Notes','Follow-up Date','Created','Updated']);
    foreach ($leads as $row) {
        fputcsv($out, [
            $row['id'], $row['name'], $row['email'], $row['phone'] ?? '',
            $row['company'] ?? '', $row['source'] ?? '', $row['status'],
            $row['priority'], $row['notes'] ?? '',
            $row['next_followup_date'] ?? '', $row['created_at'], $row['updated_at'] ?? '',
        ]);
    }
    fclose($out);
    exit;
}

$bySource  = $leadModel->bySource();
$allLeads  = $leadModel->exportAll();

$pageTitle    = 'Reports – ' . APP_NAME;
$pageSubtitle = 'Analytics & Export';
$activePage   = 'reports';
ob_start();
?>

<!-- Header -->
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
  <div>
    <h2 class="text-xl font-bold text-slate-800">Reports & Analytics</h2>
    <p class="text-slate-500 text-sm mt-0.5">Business overview and data export</p>
  </div>
  <a href="?export=csv"
     class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors shadow-sm shadow-emerald-500/20">
    <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
  </a>
</div>

<!-- ── Summary Cards ──────────────────────────────────── -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
  <?php $cards = [
    ['label'=>'Total Leads',     'value'=>$stats['total'],              'icon'=>'people-fill',              'color'=>'indigo'],
    ['label'=>'Converted',       'value'=>$stats['converted'],          'icon'=>'trophy-fill',              'color'=>'emerald'],
    ['label'=>'Conversion Rate', 'value'=>$stats['conversion_rate'].'%','icon'=>'graph-up-arrow',           'color'=>'blue'],
    ['label'=>'High Priority',   'value'=>$stats['high_priority'],      'icon'=>'exclamation-triangle-fill','color'=>'red'],
  ];
  $cm = [
    'indigo'  => 'bg-indigo-100 text-indigo-600',
    'emerald' => 'bg-emerald-100 text-emerald-600',
    'blue'    => 'bg-blue-100 text-blue-600',
    'red'     => 'bg-red-100 text-red-600',
  ];
  foreach ($cards as $card):
  ?>
  <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <span class="text-xs font-bold text-slate-500 uppercase tracking-wider"><?= $card['label'] ?></span>
      <div class="w-9 h-9 <?= $cm[$card['color']] ?> rounded-xl flex items-center justify-center">
        <i class="bi bi-<?= $card['icon'] ?> text-base"></i>
      </div>
    </div>
    <div class="text-3xl font-extrabold text-slate-800"><?= $card['value'] ?></div>
  </div>
  <?php endforeach; ?>
</div>

<!-- ── Two-column: Status + Source ────────────────────── -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">

  <!-- Status Breakdown -->
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
      <i class="bi bi-pie-chart-fill text-indigo-500"></i>
      <h3 class="font-bold text-slate-800 text-sm">Status Breakdown</h3>
    </div>
    <div class="p-5 space-y-3">
      <?php
      $statuses = [
        ['label'=>'New',       'count'=>$stats['new_leads'], 'color'=>'bg-indigo-500',  'badge'=>'bg-indigo-100 text-indigo-700'],
        ['label'=>'Contacted', 'count'=>$stats['contacted'], 'color'=>'bg-amber-500',   'badge'=>'bg-amber-100 text-amber-700'],
        ['label'=>'Follow-up', 'count'=>$stats['followups'], 'color'=>'bg-blue-500',    'badge'=>'bg-blue-100 text-blue-700'],
        ['label'=>'Converted', 'count'=>$stats['converted'], 'color'=>'bg-emerald-500', 'badge'=>'bg-emerald-100 text-emerald-700'],
        ['label'=>'Closed',    'count'=>$stats['closed'],    'color'=>'bg-slate-400',   'badge'=>'bg-slate-100 text-slate-600'],
      ];
      foreach ($statuses as $s):
        $pct = $stats['total'] > 0 ? round($s['count'] / $stats['total'] * 100) : 0;
      ?>
      <div class="flex items-center gap-3">
        <span class="text-xs font-semibold px-2.5 py-1 rounded-full <?= $s['badge'] ?> w-24 text-center flex-shrink-0">
          <?= $s['label'] ?>
        </span>
        <div class="flex-1 h-2.5 bg-slate-100 rounded-full overflow-hidden">
          <div class="h-full <?= $s['color'] ?> rounded-full" style="width:<?= $pct ?>%"></div>
        </div>
        <span class="text-xs font-bold text-slate-700 w-6 text-right"><?= $s['count'] ?></span>
        <span class="text-xs text-slate-400 w-10"><?= $pct ?>%</span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Source Breakdown -->
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
      <i class="bi bi-bar-chart-fill text-indigo-500"></i>
      <h3 class="font-bold text-slate-800 text-sm">Source Breakdown</h3>
    </div>
    <div class="p-5 space-y-3">
      <?php if (empty($bySource)): ?>
        <p class="text-slate-400 text-sm text-center py-4">No source data yet</p>
      <?php else: ?>
        <?php foreach ($bySource as $src):
          $pct = $stats['total'] > 0 ? round($src['total'] / $stats['total'] * 100) : 0;
        ?>
        <div class="flex items-center gap-3">
          <span class="text-xs font-medium text-slate-600 w-24 flex-shrink-0 truncate"><?= Helper::e($src['source']) ?></span>
          <div class="flex-1 h-2.5 bg-slate-100 rounded-full overflow-hidden">
            <div class="h-full bg-indigo-400 rounded-full" style="width:<?= $pct ?>%"></div>
          </div>
          <span class="text-xs font-bold text-slate-700 w-6 text-right"><?= $src['total'] ?></span>
          <span class="text-xs text-slate-400 w-10"><?= $pct ?>%</span>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- ── Full Leads Table ────────────────────────────────── -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
  <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
    <div class="flex items-center gap-2">
      <i class="bi bi-table text-indigo-500"></i>
      <h3 class="font-bold text-slate-800 text-sm">All Leads Data</h3>
      <span class="bg-indigo-50 text-indigo-600 text-xs font-bold px-2 py-0.5 rounded-full"><?= count($allLeads) ?></span>
    </div>
    <a href="?export=csv"
       class="flex items-center gap-1.5 text-emerald-600 hover:text-emerald-700 text-xs font-semibold transition-colors">
      <i class="bi bi-download"></i> Download CSV
    </a>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="bg-slate-50 border-b border-slate-200">
          <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Name</th>
          <th class="text-left px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Email</th>
          <th class="text-left px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Company</th>
          <th class="text-left px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Source</th>
          <th class="text-left px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
          <th class="text-left px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Priority</th>
          <th class="text-left px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Created</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        <?php foreach ($allLeads as $lead): ?>
        <tr class="hover:bg-slate-50 transition-colors">
          <td class="px-5 py-3.5">
            <a href="<?= BASE_URL ?>/leads/view.php?id=<?= $lead['id'] ?>"
               class="font-semibold text-slate-800 hover:text-indigo-600 transition-colors">
              <?= Helper::e($lead['name']) ?>
            </a>
          </td>
          <td class="px-4 py-3.5 text-slate-500 text-xs"><?= Helper::e($lead['email']) ?></td>
          <td class="px-4 py-3.5 text-slate-500 text-xs"><?= Helper::e($lead['company'] ?: '—') ?></td>
          <td class="px-4 py-3.5">
            <span class="bg-slate-100 text-slate-600 text-xs font-medium px-2 py-0.5 rounded-lg">
              <?= Helper::e($lead['source'] ?: '—') ?>
            </span>
          </td>
          <td class="px-4 py-3.5">
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full <?= Helper::statusClass($lead['status']) ?>">
              <?= Helper::e($lead['status']) ?>
            </span>
          </td>
          <td class="px-4 py-3.5">
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full <?= Helper::priorityClass($lead['priority']) ?>">
              <?= Helper::e($lead['priority']) ?>
            </span>
          </td>
          <td class="px-4 py-3.5 text-xs text-slate-400"><?= Helper::formatDate($lead['created_at']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/views/layouts/main.php';
