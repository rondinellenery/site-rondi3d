<?php
require __DIR__.'/config.php';
$title = "Entrar";
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $email = trim($_POST['email'] ?? '');
  $pass  = $_POST['password'] ?? '';
  $stmt  = db()->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
  $stmt->execute([$email]);
  $u = $stmt->fetch();
  if ($u && password_verify($pass, $u['pass_hash'])) {
    $_SESSION['user'] = $u;
    $dest = $_SESSION['redirect_after_login'] ?? 'index.php';
    unset($_SESSION['redirect_after_login']);
    header('Location: '.$dest); exit;
  } else $error = "E-mail ou senha inválidos.";
}
include __DIR__.'/includes/header.php';
?>
<h1 class="h3 mb-3">Entrar</h1>
<?php if (!empty($_SESSION['flash_ok'])): ?><div class="alert alert-success"><?= h($_SESSION['flash_ok']); unset($_SESSION['flash_ok']);?></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
<form method="post" class="row g-3" autocomplete="off">
  <div class="col-md-6">
    <label class="form-label">E-mail</label>
    <input name="email" type="email" class="form-control" required value="<?= h($_POST['email'] ?? '') ?>">
  </div>
  <div class="col-md-6">
    <label class="form-label">Senha</label>
    <input name="password" type="password" class="form-control" required>
  </div>
  <div class="col-12">
    <button class="btn btn-primary">Entrar</button>
    <a class="btn btn-link" href="signup.php">Criar conta</a>
  </div>
</form>
<?php include __DIR__.'/includes/footer.php'; ?>
