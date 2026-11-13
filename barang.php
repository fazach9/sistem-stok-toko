<?php require 'config.php'; auth();
if (isset($_GET['csv'])) { // Ekspor stok ke CSV (bisa dibuka di Excel)
  header('Content-Type: text/csv; charset=utf-8'); header('Content-Disposition: attachment; filename=stok-' . date('Ymd') . '.csv');
  $o = fopen('php://output', 'w'); fwrite($o, "\xEF\xBB\xBF");
  fputcsv($o, ['Kode', 'Nama', 'Kategori', 'Satuan', 'Harga Beli', 'Harga Jual', 'Stok', 'Stok Min']);
  foreach ($pdo->query('SELECT kode,nama,kategori,satuan,harga_beli,harga_jual,stok,stok_min FROM barang ORDER BY nama') as $r) fputcsv($o, $r);
  exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  chk(); $a = $_POST['a'] ?? '';
  try {
    if ($a === 'del') { $pdo->prepare('DELETE FROM barang WHERE id=?')->execute([(int)$_POST['pid']]); flash('Barang dihapus.'); }
    else {
      $id = (int)($_POST['pid'] ?? 0);
      $d = [trim($_POST['kode']), trim($_POST['nama']), trim($_POST['kategori']) ?: 'Umum', trim($_POST['satuan']) ?: 'pcs',
            max(0, (int)$_POST['harga_beli']), max(0, (int)$_POST['harga_jual']), max(0, (int)$_POST['stok_min'])];
      if ($d[0] === '' || $d[1] === '') throw new DomainException('Kode dan nama wajib diisi.');
      if ($id) { $pdo->prepare('UPDATE barang SET kode=?,nama=?,kategori=?,satuan=?,harga_beli=?,harga_jual=?,stok_min=? WHERE id=?')->execute([...$d, $id]); flash('Barang diperbarui.'); }
      else {
        $pdo->beginTransaction();
        $pdo->prepare('INSERT INTO barang(kode,nama,kategori,satuan,harga_beli,harga_jual,stok_min) VALUES(?,?,?,?,?,?,?)')->execute($d);
        $bid = $pdo->lastInsertId(); $s = max(0, (int)$_POST['stok']);
        if ($s) { $pdo->prepare("INSERT INTO transaksi(barang_id,jenis,qty,harga,catatan,user_id) VALUES(?,'masuk',?,?,'Stok awal',?)")->execute([$bid, $s, $d[4], $_SESSION['uid']]);
                  $pdo->prepare('UPDATE barang SET stok=? WHERE id=?')->execute([$s, $bid]); }
        $pdo->commit(); flash('Barang ditambahkan.');
      }
    }
  } catch (DomainException $x) { flash($x->getMessage(), 'danger'); }
  catch (PDOException $x) { if ($pdo->inTransaction()) $pdo->rollBack(); flash($x->getCode() == 23000 ? 'Kode barang sudah dipakai.' : 'Gagal menyimpan.', 'danger'); }
  header('Location: barang.php'); exit;
}
$rows = $pdo->query('SELECT * FROM barang ORDER BY nama')->fetchAll();
head('Barang', 'barang'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0">Data Barang</h4>
  <div><a class="btn btn-outline-secondary btn-sm" href="?csv=1">⬇ CSV</a> <button class="btn btn-dark btn-sm" id="add">+ Tambah Barang</button></div>
</div>
<div class="card shadow-sm"><div class="card-body table-responsive">
<table class="table table-hover align-middle dt">
  <thead><tr><th>Kode</th><th>Nama</th><th>Kategori</th><th class="text-end">Harga Beli</th><th class="text-end">Harga Jual</th><th class="text-end">Stok</th><th></th></tr></thead>
  <tbody><?php foreach ($rows as $b): ?>
    <tr><td><?= e($b['kode']) ?></td><td><?= e($b['nama']) ?></td><td><?= e($b['kategori']) ?></td>
      <td class="text-end"><?= rp($b['harga_beli']) ?></td><td class="text-end"><?= rp($b['harga_jual']) ?></td>
      <td class="text-end" data-order="<?= $b['stok'] ?>"><span class="badge bg-<?= $b['stok'] <= $b['stok_min'] ? 'danger' : 'success' ?>"><?= $b['stok'] ?> <?= e($b['satuan']) ?></span></td>
      <td class="text-end text-nowrap">
        <button class="btn btn-sm btn-outline-primary edit" data-b="<?= e(json_encode($b)) ?>">Edit</button>
        <form method="post" class="d-inline" onsubmit="return confirm('Hapus barang ini beserta riwayat transaksinya?')">
          <input type="hidden" name="t" value="<?= csrf() ?>"><input type="hidden" name="a" value="del"><input type="hidden" name="pid" value="<?= $b['id'] ?>">
          <button class="btn btn-sm btn-outline-danger">Hapus</button></form></td></tr>
  <?php endforeach ?></tbody>
</table></div></div>

<div class="modal fade" id="m" tabindex="-1"><div class="modal-dialog"><form method="post" class="modal-content" id="f">
  <input type="hidden" name="t" value="<?= csrf() ?>"><input type="hidden" name="a" value="save"><input type="hidden" name="pid">
  <div class="modal-header"><h5 class="modal-title" id="mt">Tambah Barang</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body row g-2">
    <div class="col-4"><label class="form-label small">Kode*</label><input class="form-control" name="kode" required></div>
    <div class="col-8"><label class="form-label small">Nama*</label><input class="form-control" name="nama" required></div>
    <div class="col-6"><label class="form-label small">Kategori</label><input class="form-control" name="kategori" placeholder="Umum"></div>
    <div class="col-6"><label class="form-label small">Satuan</label><input class="form-control" name="satuan" placeholder="pcs"></div>
    <div class="col-6"><label class="form-label small">Harga Beli (Rp)</label><input type="number" min="0" class="form-control" name="harga_beli" value="0"></div>
    <div class="col-6"><label class="form-label small">Harga Jual (Rp)</label><input type="number" min="0" class="form-control" name="harga_jual" value="0"></div>
    <div class="col-6"><label class="form-label small">Stok Awal</label><input type="number" min="0" class="form-control" name="stok" value="0"><div class="form-text">Ubah stok lewat menu Transaksi.</div></div>
    <div class="col-6"><label class="form-label small">Stok Minimum</label><input type="number" min="0" class="form-control" name="stok_min" value="5"></div>
  </div>
  <div class="modal-footer"><button class="btn btn-dark">Simpan</button></div>
</form></div></div>
<?php foot("
const m=new bootstrap.Modal('#m'),f=document.getElementById('f');
$('#add').click(()=>{f.reset();f.pid.value='';f.stok.disabled=false;$('#mt').text('Tambah Barang');m.show()});
$(document).on('click','.edit',function(){const b=JSON.parse(this.dataset.b);f.reset();
  for(const k in b)if(f.elements[k]&&k!=='stok')f.elements[k].value=b[k];
  f.pid.value=b.id;f.stok.disabled=true;$('#mt').text('Edit Barang');m.show()});");
