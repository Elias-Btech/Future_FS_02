<?php
require_once dirname(__DIR__) . '/app/bootstrap.php';
use App\Core\{Auth, Helper};
use App\Models\{Lead, ActivityLog};

Auth::require();
$admin       = Auth::admin();
$leadModel   = new Lead();
$stats       = $leadModel->stats();
$recentLeads = $leadModel->recent(8);
$bySource    = $leadModel->bySource();
$activity    = (new ActivityLog())->recent(10);

// â”€â”€ Chart 1: Leads over last 7 days â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
$db = \App\Core\Database::getInstance()->getConnection();
$dailyRows = $db->query("
    SELECT DATE(created_at) AS day, COUNT(*) AS total
    FROM leads
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
    GROUP BY DATE(created_at)
    ORDER BY day ASC
")->fetchAll();

// Fill missing days with 0
$dailyMap = [];
foreach ($dailyRows as $r) $dailyMap[$r['day']] = (int)$r['total'];
$dailyLabels = [];
$dailyData   = [];
for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    // Use internationally accepted date format: DD/MM
    $dailyLabels[] = date('d/m', strtotime($d));
    $dailyData[]   = $dailyMap[$d] ?? 0;
}

// â”€â”€ Chart 2: Status doughnut data â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
$statusLabels = ['New','Contacted','Follow-up','Converted','Closed'];
$statusData   = [
    (int)$stats['new_leads'],
    (int)$stats['contacted'],
    (int)$stats['followups'],
    (int)$stats['converted'],
    (int)$stats['closed'],
];

// â”€â”€ Chart 3: Source bar data â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
$sourceLabels = array_column($bySource, 'source');
$sourceData   = array_map('intval', array_column($bySource, 'total'));

$pageTitle    = 'Dashboard';
$pageSubtitle = date('l, F j, Y');
$activePage   = 'dashboard';

ob_start();
?>

<!-- â”€â”€ Welcome Banner â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
<div class="flex flex-wrap items-center justify-between gap-4 mb-8">
  <div>
    <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">
      Welcome back, <?= Helper::e($admin['name']) ?> 👋
    </h2>
    <p class="text-slate-500 text-sm mt-1">
      <?php if ($stats['overdue'] > 0): ?>
        <span class="inline-flex items-center gap-1.5 text-red-600 font-semibold">
          <i class="bi bi-exclamation-triangle-fill"></i>
          <?= $stats['overdue'] ?> overdue action<?= $stats['overdue'] > 1 ? 's' : '' ?> need attention
        </span>
      <?php else: ?>
        <span class="inline-flex items-center gap-1.5 text-emerald-600 font-semibold">
          <i class="bi bi-check-circle-fill"></i> All actions are on track
        </span>
      <?php endif; ?>
    </p>
  </div>
  <a href="<?= BASE_URL ?>/leads/create.php"
     class="hidden sm:inline-flex items-center gap-2 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all"
     style="background:#6366f1;box-shadow:0 4px 14px rgba(99,102,241,.35);"
     onmouseover="this.style.background='#4f46e5'" onmouseout="this.style.background='#6366f1'">
    <i class="bi bi-plus-lg"></i> Add New Lead
  </a>
</div>

<!-- â”€â”€ Stat Cards â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5 mb-8">

  <!-- Total Leads -->
  <div class="stat-card bg-white rounded-2xl p-6 border border-slate-200"
       style="box-shadow:0 1px 3px rgba(0,0,0,.06);"
       data-href="<?= BASE_URL ?>/leads/index.php">
    <div class="flex items-center justify-between mb-5">
      <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Total Leads</p>
      <div class="w-11 h-11 rounded-2xl flex items-center justify-center" style="background:rgba(99,102,241,.1);">
        <i class="bi bi-people-fill text-lg" style="color:#6366f1;"></i>
      </div>
    </div>
    <p class="text-4xl font-extrabold text-slate-800 counter" data-target="<?= $stats['total'] ?>">0</p>
    <p class="text-xs text-slate-400 mt-2 flex items-center gap-1">
      <i class="bi bi-database text-slate-300"></i> All time records
    </p>
  </div>

  <!-- New Leads -->
  <div class="stat-card bg-white rounded-2xl p-6 border border-slate-200"
       style="box-shadow:0 1px 3px rgba(0,0,0,.06);"
       data-href="<?= BASE_URL ?>/leads/index.php?status=New">
    <div class="flex items-center justify-between mb-5">
      <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">New Leads</p>
      <div class="w-11 h-11 rounded-2xl flex items-center justify-center" style="background:rgba(245,158,11,.1);">
        <i class="bi bi-star-fill text-lg" style="color:#f59e0b;"></i>
      </div>
    </div>
    <p class="text-4xl font-extrabold text-slate-800 counter" data-target="<?= $stats['new_leads'] ?>">0</p>
    <p class="text-xs text-slate-400 mt-2 flex items-center gap-1">
      <i class="bi bi-clock text-slate-300"></i> Awaiting contact
    </p>
  </div>

  <!-- Follow-ups -->
  <div class="stat-card bg-white rounded-2xl p-6 border border-slate-200"
       style="box-shadow:0 1px 3px rgba(0,0,0,.06);"
       data-href="<?= BASE_URL ?>/leads/index.php?status=Follow-up">
    <div class="flex items-center justify-between mb-5">
      <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Follow-ups</p>
      <div class="w-11 h-11 rounded-2xl flex items-center justify-center" style="background:rgba(59,130,246,.1);">
        <i class="bi bi-calendar-check-fill text-lg" style="color:#3b82f6;"></i>
      </div>
    </div>
    <p class="text-4xl font-extrabold text-slate-800 counter" data-target="<?= $stats['followups'] ?>">0</p>
    <p class="text-xs text-slate-400 mt-2 flex items-center gap-1">
      <i class="bi bi-arrow-right text-slate-300"></i> In progress
    </p>
  </div>

  <!-- Converted -->
  <div class="stat-card bg-white rounded-2xl p-6 border border-slate-200"
       style="box-shadow:0 1px 3px rgba(0,0,0,.06);"
       data-href="<?= BASE_URL ?>/leads/index.php?status=Converted">
    <div class="flex items-center justify-between mb-5">
      <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Converted</p>
      <div class="w-11 h-11 rounded-2xl flex items-center justify-center" style="background:rgba(34,197,94,.1);">
        <i class="bi bi-trophy-fill text-lg" style="color:#22c55e;"></i>
      </div>
    </div>
    <p class="text-4xl font-extrabold text-slate-800 counter" data-target="<?= $stats['converted'] ?>">0</p>
    <p class="text-xs text-slate-400 mt-2 flex items-center gap-1">
      <i class="bi bi-graph-up-arrow" style="color:#22c55e;"></i>
      <span style="color:#22c55e;font-weight:600;"><?= $stats['conversion_rate'] ?>%</span> conversion rate
    </p>
  </div>

</div>

<!-- â”€â”€ CHARTS ROW â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-5 mb-6 sm:mb-8">

  <!-- Chart 1: Leads Over Time (Line) -->
  <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 overflow-hidden" style="box-shadow:0 1px 3px rgba(0,0,0,.06);">
    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center">
          <i class="bi bi-graph-up text-indigo-500 text-sm"></i>
        </div>
        <div>
          <h3 class="font-bold text-slate-800 text-sm">Leads Over Time</h3>
          <p class="text-slate-400 text-xs">Last 7 days</p>
        </div>
      </div>
      <span class="text-xs font-bold px-2.5 py-1 rounded-full" style="background:rgba(99,102,241,.1);color:#6366f1;">
        <?= array_sum($dailyData) ?> this week
      </span>
    </div>
    <div class="p-5" style="height:220px;">
      <canvas id="lineChart"></canvas>
    </div>
  </div>

  <!-- Chart 2: Status Doughnut -->
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden" style="box-shadow:0 1px 3px rgba(0,0,0,.06);">
    <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3">
      <div class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center">
        <i class="bi bi-pie-chart-fill text-indigo-500 text-sm"></i>
      </div>
      <div>
        <h3 class="font-bold text-slate-800 text-sm">Lead Status</h3>
        <p class="text-slate-400 text-xs">Distribution</p>
      </div>
    </div>
    <div class="p-5 flex items-center justify-center" style="height:220px;">
      <?php if ($stats['total'] > 0): ?>
        <canvas id="doughnutChart"></canvas>
      <?php else: ?>
        <div class="text-center text-slate-400">
          <i class="bi bi-pie-chart text-4xl block mb-2 opacity-20"></i>
          <p class="text-sm">No data yet</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

</div>

<!-- Chart 3: Lead Sources (Bar) â€” full width -->
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden mb-6 sm:mb-8" style="box-shadow:0 1px 3px rgba(0,0,0,.06);">
  <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <div class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center">
        <i class="bi bi-bar-chart-fill text-indigo-500 text-sm"></i>
      </div>
      <div>
        <h3 class="font-bold text-slate-800 text-sm">Leads by Source</h3>
        <p class="text-slate-400 text-xs">Where your leads come from</p>
      </div>
    </div>
  </div>
  <div class="p-5" style="height:200px;">
    <?php if (!empty($sourceLabels)): ?>
      <canvas id="barChart"></canvas>
    <?php else: ?>
      <div class="flex items-center justify-center h-full text-slate-400">
        <div class="text-center">
          <i class="bi bi-bar-chart text-4xl block mb-2 opacity-20"></i>
          <p class="text-sm">No source data yet</p>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- â”€â”€ Pipeline + Sources â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-5 mb-6 sm:mb-8">

  <!-- Pipeline -->
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden" style="box-shadow:0 1px 3px rgba(0,0,0,.06);">
    <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3">
      <div class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center">
        <i class="bi bi-bar-chart-steps text-indigo-500 text-sm"></i>
      </div>
      <h3 class="font-bold text-slate-800">Pipeline Breakdown</h3>
    </div>
    <div class="p-6 space-y-5">
      <?php
      $pipeline = [
        ['label'=>'New',       'count'=>$stats['new_leads'], 'color'=>'#6366f1', 'bg'=>'rgba(99,102,241,.12)',  'text'=>'#6366f1'],
        ['label'=>'Contacted', 'count'=>$stats['contacted'], 'color'=>'#f59e0b', 'bg'=>'rgba(245,158,11,.12)', 'text'=>'#d97706'],
        ['label'=>'Follow-up', 'count'=>$stats['followups'], 'color'=>'#3b82f6', 'bg'=>'rgba(59,130,246,.12)', 'text'=>'#2563eb'],
        ['label'=>'Converted', 'count'=>$stats['converted'], 'color'=>'#22c55e', 'bg'=>'rgba(34,197,94,.12)',  'text'=>'#16a34a'],
        ['label'=>'Closed',    'count'=>$stats['closed'],    'color'=>'#94a3b8', 'bg'=>'rgba(148,163,184,.12)','text'=>'#64748b'],
      ];
      foreach ($pipeline as $p):
        $pct = $stats['total'] > 0 ? round($p['count'] / $stats['total'] * 100) : 0;
      ?>
      <div>
        <div class="flex items-center justify-between mb-1.5">
          <span class="text-xs font-semibold px-2.5 py-1 rounded-full" style="background:<?= $p['bg'] ?>;color:<?= $p['text'] ?>;">
            <?= $p['label'] ?>
          </span>
          <div class="flex items-center gap-2">
            <span class="text-sm font-bold text-slate-700"><?= $p['count'] ?></span>
            <span class="text-xs text-slate-400"><?= $pct ?>%</span>
          </div>
        </div>
        <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
          <div class="h-full rounded-full transition-all duration-700"
               style="width:<?= $pct ?>%;background:<?= $p['color'] ?>;"></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Lead Sources -->
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden" style="box-shadow:0 1px 3px rgba(0,0,0,.06);">
    <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3">
      <div class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center">
        <i class="bi bi-pie-chart-fill text-indigo-500 text-sm"></i>
      </div>
      <h3 class="font-bold text-slate-800">Lead Sources</h3>
    </div>
    <div class="p-6 space-y-5">
      <?php if (empty($bySource)): ?>
        <div class="text-center py-8">
          <i class="bi bi-inbox text-4xl text-slate-200 block mb-2"></i>
          <p class="text-slate-400 text-sm">No source data yet</p>
        </div>
      <?php else: ?>
        <?php
        $srcColors = ['#6366f1','#f59e0b','#22c55e','#3b82f6','#ef4444','#8b5cf6','#ec4899','#14b8a6'];
        foreach ($bySource as $i => $src):
          $pct   = $stats['total'] > 0 ? round($src['total'] / $stats['total'] * 100) : 0;
          $color = $srcColors[$i % count($srcColors)];
        ?>
        <div>
          <div class="flex items-center justify-between mb-1.5">
            <span class="text-sm font-medium text-slate-600 flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:<?= $color ?>;"></span>
              <?= Helper::e($src['source']) ?>
            </span>
            <div class="flex items-center gap-2">
              <span class="text-sm font-bold text-slate-700"><?= $src['total'] ?></span>
              <span class="text-xs text-slate-400"><?= $pct ?>%</span>
            </div>
          </div>
          <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
            <div class="h-full rounded-full" style="width:<?= $pct ?>%;background:<?= $color ?>;"></div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- â”€â”€ Recent Leads + Activity â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-4 sm:gap-5">

  <!-- Recent Leads -->
  <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-200 overflow-hidden" style="box-shadow:0 1px 3px rgba(0,0,0,.06);">
    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center">
          <i class="bi bi-table text-indigo-500 text-sm"></i>
        </div>
        <h3 class="font-bold text-slate-800">Recent Leads</h3>
        <span class="text-xs font-bold px-2.5 py-1 rounded-full" style="background:rgba(99,102,241,.1);color:#6366f1;">
          <?= count($recentLeads) ?>
        </span>
      </div>
      <a href="<?= BASE_URL ?>/leads/index.php"
         class="text-sm font-semibold hover:underline" style="color:#6366f1;">
        View all â†’
      </a>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead>
          <tr style="background:#f8fafc;border-bottom:1px solid #f1f5f9;">
            <th class="text-left px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Lead</th>
            <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Source</th>
            <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
            <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Priority</th>
            <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Date</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($recentLeads)): ?>
            <tr><td colspan="5" class="text-center py-16 text-slate-400">
              <i class="bi bi-inbox text-5xl block mb-3 opacity-20"></i>
              <p class="font-medium text-sm">No leads yet</p>
              <a href="<?= BASE_URL ?>/leads/create.php" class="text-sm font-semibold mt-1 inline-block" style="color:#6366f1;">
                Add your first lead â†’
              </a>
            </td></tr>
          <?php else: ?>
            <?php foreach ($recentLeads as $lead): ?>
            <tr style="border-bottom:1px solid #f8fafc;transition:background .15s;"
                onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 text-white"
                       style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">
                    <?= Helper::initials($lead['name']) ?>
                  </div>
                  <div>
                    <a href="<?= BASE_URL ?>/leads/view.php?id=<?= $lead['id'] ?>"
                       class="font-semibold text-slate-800 text-sm hover:text-indigo-600 transition-colors">
                      <?= Helper::e($lead['name']) ?>
                    </a>
                    <p class="text-xs text-slate-400 mt-0.5"><?= Helper::e($lead['email']) ?></p>
                  </div>
                </div>
              </td>
              <td class="px-4 py-4">
                <span class="text-xs font-medium px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600">
                  <?= Helper::e($lead['source'] ?: 'â€”') ?>
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
              <td class="px-4 py-4 text-xs text-slate-400 whitespace-nowrap">
                <?= Helper::formatDate($lead['created_at']) ?>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Activity Feed -->
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden" style="box-shadow:0 1px 3px rgba(0,0,0,.06);">
    <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3">
      <div class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center">
        <i class="bi bi-activity text-indigo-500 text-sm"></i>
      </div>
      <h3 class="font-bold text-slate-800">Activity Feed</h3>
    </div>
    <div class="overflow-y-auto" style="max-height:420px;">
      <?php if (empty($activity)): ?>
        <div class="text-center py-12 text-slate-400">
          <i class="bi bi-clock-history text-4xl block mb-2 opacity-20"></i>
          <p class="text-sm">No activity yet</p>
        </div>
      <?php else: ?>
        <?php foreach ($activity as $act): ?>
        <div class="flex items-start gap-3 px-5 py-4 border-b border-slate-50 hover:bg-slate-50 transition-colors">
          <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5"
               style="background:rgba(99,102,241,.1);">
            <i class="bi bi-person-check text-xs" style="color:#6366f1;"></i>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-xs text-slate-700 leading-relaxed">
              <span class="font-semibold"><?= Helper::e($act['admin_name'] ?? 'Admin') ?></span>
              <?= Helper::e($act['action']) ?>
              <?php if ($act['lead_name']): ?>
                â€” <span class="font-semibold" style="color:#6366f1;"><?= Helper::e($act['lead_name']) ?></span>
              <?php endif; ?>
            </p>
            <p class="text-xs text-slate-400 mt-1"><?= Helper::timeAgo($act['created_at']) ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

</div>

<?php
$content = ob_get_clean();

// Pass chart data to JS via inline script
$chartData = json_encode([
    'daily'   => ['labels' => $dailyLabels, 'data' => $dailyData],
    'status'  => ['labels' => $statusLabels, 'data' => $statusData],
    'sources' => ['labels' => $sourceLabels, 'data' => $sourceData],
]);

// Append chart scripts to content
$content .= <<<HTML
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
  const d = {$chartData};

  Chart.defaults.font.family = "'Plus Jakarta Sans', 'Inter', sans-serif";
  Chart.defaults.font.size   = 12;
  Chart.defaults.color       = '#94a3b8';

  const isDark     = document.body.classList.contains('dark-mode');
  const gridColor  = isDark ? 'rgba(255,255,255,.05)' : 'rgba(0,0,0,.04)';
  const labelColor = isDark ? '#64748b' : '#94a3b8';
  const surfaceBg  = isDark ? '#1e293b' : '#ffffff';

  const tooltip = {
    backgroundColor: '#0f172a',
    titleColor: '#f1f5f9',
    bodyColor: '#94a3b8',
    padding: 14,
    cornerRadius: 12,
    borderColor: 'rgba(255,255,255,.08)',
    borderWidth: 1,
    displayColors: false,
  };

  // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
  // OPTION A â€” GRADIENT FILL LINE
  // Style: Smooth curve + deep gradient fill underneath
  // Used by: Stripe, Shopify, HubSpot
  // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

  // Chart 1: Gradient Line â€” Leads Over Time
  const lineCtx = document.getElementById('lineChart');
  if (lineCtx) {
    const ctx2d = lineCtx.getContext('2d');

    // Build vertical gradient
    const grad = ctx2d.createLinearGradient(0, 0, 0, 180);
    grad.addColorStop(0,   'rgba(99,102,241,.35)');
    grad.addColorStop(0.5, 'rgba(99,102,241,.12)');
    grad.addColorStop(1,   'rgba(99,102,241,.0)');

    new Chart(lineCtx, {
      type: 'line',
      data: {
        labels: d.daily.labels,
        datasets: [{
          label: 'Leads',
          data: d.daily.data,
          borderColor: '#6366f1',
          backgroundColor: grad,
          borderWidth: 3,
          pointBackgroundColor: '#6366f1',
          pointBorderColor: surfaceBg,
          pointBorderWidth: 3,
          pointRadius: 6,
          pointHoverRadius: 9,
          pointHoverBackgroundColor: '#6366f1',
          pointHoverBorderColor: '#fff',
          pointHoverBorderWidth: 3,
          fill: true,
          tension: 0.45,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: { display: false },
          tooltip: {
            ...tooltip,
            callbacks: {
              title: ctx => ctx[0].label,
              label: ctx => '  ' + ctx.parsed.y + ' new lead' + (ctx.parsed.y !== 1 ? 's' : ''),
            }
          }
        },
        scales: {
          x: {
            grid: { color: gridColor, drawBorder: false },
            border: { display: false },
            ticks: { color: labelColor, font: { size: 11, weight: '500' }, padding: 8 }
          },
          y: {
            beginAtZero: true,
            grid: { color: gridColor, drawBorder: false },
            border: { display: false },
            ticks: { color: labelColor, font: { size: 11 }, stepSize: 1, precision: 0, padding: 8 }
          }
        }
      }
    });
  }

  // Chart 2: Doughnut â€” Status (thick ring, glowing segments)
  const doughnutCtx = document.getElementById('doughnutChart');
  if (doughnutCtx) {
    new Chart(doughnutCtx, {
      type: 'doughnut',
      data: {
        labels: d.status.labels,
        datasets: [{
          data: d.status.data,
          backgroundColor: ['#6366f1','#f59e0b','#3b82f6','#22c55e','#94a3b8'],
          borderColor: surfaceBg,
          borderWidth: 4,
          hoverOffset: 10,
          hoverBorderWidth: 0,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '72%',
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              color: labelColor,
              padding: 14,
              font: { size: 11, weight: '600' },
              usePointStyle: true,
              pointStyleWidth: 9,
            }
          },
          tooltip: {
            ...tooltip,
            callbacks: {
              label: ctx => '  ' + ctx.label + ': ' + ctx.parsed + ' leads'
            }
          }
        }
      }
    });
  }

  // Chart 3: Gradient Bars â€” Lead Sources
  const barCtx = document.getElementById('barChart');
  if (barCtx) {
    const bCtx = barCtx.getContext('2d');

    // One gradient per bar using a plugin
    const barColors = ['#6366f1','#f59e0b','#22c55e','#3b82f6','#ef4444','#8b5cf6','#ec4899','#14b8a6'];

    new Chart(barCtx, {
      type: 'bar',
      data: {
        labels: d.sources.labels,
        datasets: [{
          label: 'Leads',
          data: d.sources.data,
          backgroundColor: d.sources.labels.map((_, i) => {
            const g = bCtx.createLinearGradient(0, 0, 0, 160);
            const c = barColors[i % barColors.length];
            g.addColorStop(0,   c);
            g.addColorStop(1,   c + '55');
            return g;
          }),
          borderColor: 'transparent',
          borderWidth: 0,
          borderRadius: 10,
          borderSkipped: false,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            ...tooltip,
            callbacks: {
              label: ctx => '  ' + ctx.parsed.y + ' lead' + (ctx.parsed.y !== 1 ? 's' : '')
            }
          }
        },
        scales: {
          x: {
            grid: { display: false },
            border: { display: false },
            ticks: { color: labelColor, font: { size: 11, weight: '500' }, padding: 6 }
          },
          y: {
            beginAtZero: true,
            grid: { color: gridColor, drawBorder: false },
            border: { display: false },
            ticks: { color: labelColor, font: { size: 11 }, stepSize: 1, precision: 0, padding: 8 }
          }
        }
      }
    });
  }

})();
</script>
HTML;

require ROOT_PATH . '/views/layouts/main.php';
