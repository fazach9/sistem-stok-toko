<?php require 'config.php';
if (!empty($_SESSION['uid'])) { header('Location: dashboard.php'); exit; }
$n = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(); // 0 = belum ada admin
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  chk(); $u = trim($_POST['u'] ?? ''); $p = $_POST['p'] ?? '';
  if (!$n) {
    if (strlen($u) < 3 || strlen($p) < 6) $err = 'Username min. 3 dan password min. 6 karakter.';
    else { $pdo->prepare('INSERT INTO users(username,password) VALUES(?,?)')->execute([$u, password_hash($p, PASSWORD_DEFAULT)]);
      flash('Akun admin dibuat. Silakan login.'); header('Location: index.php'); exit; }
  } else {
    $s = $pdo->prepare('SELECT * FROM users WHERE username=?'); $s->execute([$u]); $r = $s->fetch();
    if ($r && password_verify($p, $r['password'])) {
      session_regenerate_id(true); $_SESSION['uid'] = $r['id']; $_SESSION['u'] = $r['username'];
      header('Location: dashboard.php'); exit;
    }
    $err = 'Username atau password salah.';
  }
}
head($n ? 'Login' : 'Buat Admin'); ?>
<div class="row justify-content-center mt-5"><div class="col-md-4">
  <div class="card shadow-sm"><div class="card-body p-4">
    <h4 class="mb-1">📦 Stok Toko</h4>
    <p class="text-muted small"><?= $n ? 'Masuk untuk mengelola stok.' : 'Pertama kali dipakai: buat akun admin.' ?></p>
    <?php if ($err): ?><div class="alert alert-danger py-2"><?= e($err) ?></div><?php endif ?>
    <form method="post"><input type="hidden" name="t" value="<?= csrf() ?>">
      <input class="form-control mb-2" name="u" placeholder="Username" required autofocus>
      <input class="form-control mb-3" type="password" name="p" placeholder="Password" required>
      <button class="btn btn-dark w-100"><?= $n ? 'Masuk' : 'Buat Akun Admin' ?></button>
    </form>
  </div></div>
</div></div>
<?php foot();
