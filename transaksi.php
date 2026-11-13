<?php require 'config.php'; auth();
if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Catat barang masuk / keluar
  chk();
  $bid = (int)$_POST['barang_id']; $j = ($_POST['jenis'] ?? '') === 'masuk' ? 'masuk' : 'keluar';
  $q = (int)$_POST['qty']; $h = max(0, (int)$_POST['harga']); $c = mb_substr(trim($_POST['catatan'] ?? ''), 0, 200);
  try {
    if ($q < 1) throw new DomainException('Jumlah minimal 1.');
    $pdo->beginTransaction();
    $s = $pdo->prepare('SELECT stok FROM barang WHERE id=? FOR UPDATE'); $s->execute([$bid]); $st = $s->fetchColumn();
    if ($st === false) throw new DomainException('Barang tidak ditemukan.');
    if ($j === 'keluar' && $q > $st) throw new DomainException("Stok tidak cukup (tersisa $st).");
    $pdo->prepare('UPDATE barang SET stok=stok+? WHERE id=?')->execute([$j === 'masuk' ? $q : -$q, $bid]);
    $pdo->prepare('INSERT INTO transaksi(barang_id,jenis,qty,harga,catatan,user_id) VALUES(?,?,?,?,?,?)')->execute([$bid, $j, $q, $h, $c, $_SESSION['uid']]);
    $pdo->commit(); flash('Transaksi dicatat.');
  } catch (Exception $x) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    flash($x instanceof DomainException ? $x->getMessage() : 'Gagal menyimpan transaksi.', 'danger');
  }
  header('Location: transaksi.php'); exit;
}
$dari = $_GET['dari'] ?? date('Y-m-01'); $sampai = $_GET['sampai'] ?? date('Y-m-d'); $jn = $_GET['jenis'] ?? '';
$sql = 'SELECT t.*,b.kode,b.nama,b.satuan,u.username FROM transaksi t JOIN barang b ON b.id=t.barang_id LEFT JOIN users u ON u.id=t.user_id WHERE DATE(t.created_at) BETWEEN ? AND ?';
$par = [$dari, $sampai]; if (in_array($jn, ['masuk', 'keluar'])) { $sql .= ' AND t.jenis=?'; $par[] = $jn; }
$st = $pdo->prepare($sql . ' ORDER BY t.id DESC LIMIT 2000'); $st->execute($par); $rows = $st->fetchAll();
if (isset($_GET['csv'])) {
  header('Content-Type: text/csv; charset=utf-8'); header('Content-Disposition: attachment; filename=transaksi-' . date('Ymd') . '.csv');
  $o = fopen('php://output', 'w'); fwrite($o, "\xEF\xBB\xBF"); fputcsv($o, ['Waktu', 'Kode', 'Barang', 'Jenis', 'Qty', 'Harga', 'Total', 'Catatan', 'User']);
  foreach ($rows as $r) fputcsv($o, [$r['created_at'], $r['kode'], $r['nama'], $r['jenis'], $r['qty'], $r['harga'], $r['qty'] * $r['harga'], $r['catatan'], $r['username']]);
  exit;
}
$brg = $pdo->query('SELECT id,kode,nama,satuan,stok,harga_beli,harga_jual FROM barang ORDER BY nama')->fetchAll();
$in = $out = 0; foreach ($rows as $r) { if ($r['jenis'] === 'masuk') $in += $r['qty'] * $r['harga']; else $out += $r['qty'] * $r['harga']; }
head('Transaksi', 'transaksi'); ?>
<h4 class="mb-3">Transaksi Stok</h4>
<div class="card shadow-sm mb-4"><div class="card-body">
<form method="post" class="row g-2 align-items-end"><input type="hidden" name="t" value="<?= csrf() ?>">
  <div class="col-md-4"><label class="form-label small">Barang</label><select class="form-select" name="barang_id" id="b" required>
    <option value="">— pilih barang —</option>
    <?php foreach ($brg as $b): ?><option value="<?= $b['id'] ?>" data-beli="<?= $b['harga_beli'] ?>" data-jual="<?= $b['harga_jual'] ?>"><?= e($b['kode'] . ' · ' . $b['nama'] . ' (stok ' . $b['stok'] . ' ' . $b['satuan'] . ')') ?></option><?php endforeach ?></select></div>
  <div class="col-md-2"><label class="form-label small">Jenis</label><select class="form-select" name="jenis" id="j"><option value="keluar">Keluar / Terjual</option><option value="masuk">Masuk / Restock</option></select></div>
  <div class="col-4 col-md-1"><label class="form-label small">Qty</label><input type="number" min="1" class="form-control" name="qty" value="1" required></div>
  <div class="col-8 col-md-2"><label class="form-label small">Harga satuan (Rp)</label><input type="number" min="0" class="form-control" name="harga" id="h" value="0"></div>
  <div class="col-md-2"><label class="form-label small">Catatan</label><input class="form-control" name="catatan" maxlength="200"></div>
  <div class="col-md-1"><button class="btn btn-dark w-100">Simpan</button></div>
</form></div></div>

<form class="row g-2 mb-3 align-items-end">
  <div class="col-auto"><label class="form-label small mb-0">Dari</label><input type="date" class="form-control form-control-sm" name="dari" value="<?= e($dari) ?>"></div>
  <div class="col-auto"><label class="form-label small mb-0">Sampai</label><input type="date" class="form-control form-control-sm" name="sampai" value="<?= e($sampai) ?>"></div>
  <div class="col-auto"><select class="form-select form-select-sm" name="jenis"><option value="">Semua</option><option value="masuk" <?= $jn === 'masuk' ? 'selected' : '' ?>>Masuk</option><option value="keluar" <?= $jn === 'keluar' ? 'selected' : '' ?>>Keluar</option></select></div>
  <div class="col-auto"><button class="btn btn-sm btn-outline-dark">Filter</button> <button class="btn btn-sm btn-outline-secondary" name="csv" value="1">⬇ CSV</button></div>
  <div class="col text-md-end small text-muted">Pembelian: <b><?= rp($in) ?></b> · Penjualan: <b class="text-success"><?= rp($out) ?></b></div>
</form>
<div class="card shadow-sm"><div class="card-body table-responsive">
<table class="table table-sm align-middle dt"><thead><tr><th>Waktu</th><th>Barang</th><th>Jenis</th><th class="text-end">Qty</th><th class="text-end">Harga</th><th class="text-end">Total</th><th>Catatan</th><th>User</th></tr></thead>
<tbody><?php foreach ($rows as $r): ?>
  <tr><td class="text-nowrap"><?= e(date('d/m/Y H:i', strtotime($r['created_at']))) ?></td><td><?= e($r['nama']) ?></td>
    <td><span class="badge bg-<?= $r['jenis'] === 'masuk' ? 'success' : 'danger' ?>"><?= $r['jenis'] ?></span></td>
    <td class="text-end"><?= $r['qty'] ?> <?= e($r['satuan']) ?></td><td class="text-end"><?= rp($r['harga']) ?></td><td class="text-end"><?= rp($r['qty'] * $r['harga']) ?></td>
    <td><?= e($r['catatan']) ?></td><td><?= e($r['username']) ?></td></tr>
<?php endforeach ?></tbody></table></div></div>
<?php foot("
const fill=()=>{const o=$('#b option:selected');$('#h').val(o.data($('#j').val()==='masuk'?'beli':'jual')||0)};
$('#b,#j').on('change',fill);");
