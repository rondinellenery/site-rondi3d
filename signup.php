<?php
require __DIR__.'/config.php';
if (current_user()) { header('Location: index.php'); exit; }
$title = 'Cadastre-se';
$err = ''; $ok=false;

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $name  = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $pass  = $_POST['password'] ?? '';

  if (mb_strlen($name)<2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($pass)<6) {
    $err = 'Preencha os campos corretamente.';
  } else {
    $e = db()->prepare("SELECT 1 FROM users WHERE email=?");
    $e->execute([$email]);
    if ($e->fetch()) { $err='E-mail já cadastrado.'; }
    else {
      db()->prepare("INSERT INTO users (name,email,pass_hash) VALUES (?,?,?)")
         ->execute([$name,$email,password_hash($pass, PASSWORD_DEFAULT)]);
      $uId = db()->lastInsertId();
      $_SESSION['user'] = ['id'=>$uId,'name'=>$name,'email'=>$email,'is_admin'=>0];
      header('Location: meus-orcamentos.php'); exit;
    }
  }
}

include __DIR__.'/includes/header.php';
?>
<div class="container py-4">
  <h1 class="h3 mb-3">Criar conta</h1>
  <?php if ($err): ?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>
  <form method="post" data-form="signup" class="row g-3">
    <div class="col-md-4">
      <label class="form-label">Nome</label>
      <input class="form-control" name="name" required>
    </div>
    <div class="col-md-4">
      <label class="form-label">E-mail</label>
      <input type="email" class="form-control" name="email" required>
    </div>
    <div class="col-md-4">
      <label class="form-label">Senha</label>
      <input type="password" class="form-control" name="password" minlength="6" required>
    </div>
    <div class="col-12">
      <button class="btn btn-success">Criar conta</button>
      <a class="btn btn-link" href="login.php">Já tenho conta</a>
    </div>
  </form>
</div>
<?php include __DIR__.'/includes/footer.php'; ?>
