<?php
require __DIR__.'/config.php';
if (current_user()) { header('Location: index.php'); exit; }
$title = 'Entrar';
$err = '';

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $email = trim($_POST['email'] ?? '');
  $pass  = $_POST['password'] ?? '';
  if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass)<6){
    $err = 'E-mail/senha inválidos.';
  } else {
    $st = db()->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
    $st->execute([$email]);
    $u = $st->fetch();
    if ($u && password_verify($pass, $u['pass_hash'])) {
      $_SESSION['user'] = ['id'=>$u['id'],'name'=>$u['name'],'email'=>$u['email'],'is_admin'=>$u['is_admin'] ?? 0];
      $go = $_SESSION['redirect_after_login'] ?? 'meus-orcamentos.php';
      unset($_SESSION['redirect_after_login']);
      header('Location: '.$go); exit;
    } else $err = 'E-mail ou senha incorretos.';
  }
}

include __DIR__.'/includes/header.php';
?>
<div class="container py-4">
  <h1 class="h3 mb-3">Entrar</h1>
  <?php if ($err): ?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>
  <form method="post" data-form="login" class="row g-3">
    <div class="col-md-6">
      <label class="form-label">E-mail</label>
      <input type="email" class="form-control" name="email" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Senha</label>
      <input type="password" class="form-control" name="password" minlength="6" required>
    </div>
    <div class="col-12">
      <button class="btn btn-primary">Entrar</button>
      <a class="btn btn-link" href="signup.php">Criar conta</a>
    </div>
  </form>
</div>
<?php include __DIR__.'/includes/footer.php'; ?>
