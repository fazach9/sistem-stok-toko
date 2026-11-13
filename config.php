<?php
// Pengaturan database (sesuaikan jika perlu)
const DB_HOST = '127.0.0.1;port=8889', DB_NAME = 'stok_toko', DB_USER = 'root', DB_PASS = '';
date_default_timezone_set('Asia/Jakarta');
session_start();
try {
  $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
  $pdo->exec("SET time_zone='+07:00'");
} catch (PDOException $x) { exit('Database belum siap. Import database.sql lalu cek pengaturan di config.php.'); }

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function rp($n) { return 'Rp ' . number_format((float)$n, 0, ',', '.'); }
function auth() { if (empty($_SESSION['uid'])) { header('Location: index.php'); exit; } }
function csrf() { return $_SESSION['t'] ??= bin2hex(random_bytes(16)); }
function chk() { if (!hash_equals($_SESSION['t'] ?? '', $_POST['t'] ?? '')) { http_response_code(400); exit('Token tidak valid.'); } }
function flash($m = null, $t = 'success') {
  if ($m) { $_SESSION['f'] = [$m, $t]; return; }
  $f = $_SESSION['f'] ?? null; unset($_SESSION['f']); return $f;
}
function head($title, $active = '') { $f = flash(); ?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?> · Stok Toko</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/datatables.net-bs5@1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head><body class="bg-light">
<?php if (!empty($_SESSION['uid'])): ?>
<nav class="navbar navbar-expand-md navbar-dark bg-dark mb-4"><div class="container">
  <a class="navbar-brand" href="dashboard.php">📦 Stok Toko</a>
  <div class="navbar-nav me-auto">
    <?php foreach (['dashboard' => 'Dashboard', 'barang' => 'Barang', 'transaksi' => 'Transaksi'] as $k => $v): ?>
      <a class="nav-link <?= $active === $k ? 'active' : '' ?>" href="<?= $k ?>.php"><?= $v ?></a>
    <?php endforeach ?>
  </div>
  <span class="navbar-text me-3"><?= e($_SESSION['u']) ?></span>
  <a class="btn btn-outline-light btn-sm" href="logout.php">Keluar</a>
</div></nav>
<?php endif ?>
<div class="container pb-5">
<?php if ($f): ?><div class="alert alert-<?= $f[1] ?> alert-dismissible"><?= e($f[0]) ?><button class="btn-close" data-bs-dismiss="alert"></button></div><?php endif;
}
function foot($js = '') { ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net@1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net-bs5@1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script>
$('.dt').DataTable({order:[],language:{search:'Cari:',lengthMenu:'Tampilkan _MENU_',info:'_START_–_END_ dari _TOTAL_',infoEmpty:'Tidak ada data',emptyTable:'Belum ada data',zeroRecords:'Tidak ditemukan',paginate:{previous:'‹',next:'›'}}});
<?= $js ?>
</script></body></html>
<?php }
