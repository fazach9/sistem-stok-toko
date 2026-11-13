<?php require 'config.php'; auth();
$s = $pdo->query("SELECT COUNT(*) n, COALESCE(SUM(stok),0) u, COALESCE(SUM(stok*harga_beli),0) v, COALESCE(SUM(stok<=stok_min),0) l FROM barang")->fetch();
$h = $pdo->query("SELECT COALESCE(SUM(CASE WHEN jenis='keluar' THEN qty*harga END),0) jual FROM transaksi WHERE DATE(created_at)=CURDATE()")->fetch();
$low = $pdo->query('SELECT kode,nama,stok,stok_min,satuan FROM barang WHERE stok<=stok_min ORDER BY stok LIMIT 10')->fetchAll();
$days = []; for ($i = 6; $i >= 0; $i--) $days[date('Y-m-d', strtotime("-$i day"))] = ['masuk' => 0, 'keluar' => 0];
foreach ($pdo->query("SELECT DATE(created_at) d, jenis, SUM(qty) q FROM transaksi WHERE created_at>=CURDATE()-INTERVAL 6 DAY GROUP BY d,jenis") as $r)
  if (isset($days[$r['d']])) $days[$r['d']][$r['jenis']] = (int)$r['q'];
head('Dashboard', 'dashboard'); ?>
<h4 class="mb-3">Dashboard</h4>
<div class="row g-3 mb-4">
<?php foreach ([['Jenis Barang', $s['n'], ''], ['Total Unit', number_format($s['u'], 0, ',', '.'), ''], ['Nilai Stok (modal)', rp($s['v']), ''],
  ['Stok Menipis', $s['l'], $s['l'] ? 'text-danger' : ''], ['Penjualan Hari Ini', rp($h['jual']), 'text-success']] as [$k, $v, $c]): ?>
  <div class="col-6 col-lg"><div class="card shadow-sm h-100"><div class="card-body"><div class="small text-muted"><?= $k ?></div><div class="fs-5 fw-bold <?= $c ?>"><?= e($v) ?></div></div></div></div>
<?php endforeach ?>
</div>
<div class="row g-3">
  <div class="col-lg-7"><div class="card shadow-sm"><div class="card-body"><h6>Barang masuk vs keluar (7 hari)</h6><canvas id="c" height="140"></canvas></div></div></div>
  <div class="col-lg-5"><div class="card shadow-sm"><div class="card-body"><h6>⚠️ Perlu restock</h6>
    <?php if (!$low): ?><p class="text-muted mb-0">Semua stok aman.</p><?php else: ?>
    <table class="table table-sm mb-0"><?php foreach ($low as $b): ?>
      <tr><td><?= e($b['nama']) ?><div class="small text-muted"><?= e($b['kode']) ?></div></td>
      <td class="text-end"><span class="badge bg-danger"><?= $b['stok'] ?> <?= e($b['satuan']) ?></span><div class="small text-muted">min. <?= $b['stok_min'] ?></div></td></tr>
    <?php endforeach ?></table><?php endif ?>
  </div></div></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<?php foot("new Chart(document.getElementById('c'),{type:'bar',data:{labels:" . json_encode(array_map(fn($d) => date('d/m', strtotime($d)), array_keys($days))) .
  ",datasets:[{label:'Masuk',data:" . json_encode(array_column($days, 'masuk')) . ",backgroundColor:'#198754'},{label:'Keluar',data:" . json_encode(array_column($days, 'keluar')) .
  ",backgroundColor:'#dc3545'}]},options:{plugins:{legend:{position:'bottom'}},scales:{y:{beginAtZero:true,ticks:{precision:0}}}}});");
