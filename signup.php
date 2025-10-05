<?php
require __DIR__.'/config.php';
$title = "Cadastre-se";
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $name  = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $pass  = $_POST['password'] ?? '';
  $errors = [];
  if ($name==='') $errors[]='Nome é obrigatório';
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[]='E-mail inválido';
  if (strlen($pass) < 6) $errors[]='Senha mínima de 6 caracteres';
  if (!$errors) {
    $stmt = db()->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
    $stmt->execute([$email]);
    if ($stmt->fetch()) $errors[]='E-mail já cadastrado';
  }
  if (!$errors) {
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    db()->prepare("INSERT INTO users(name,email,pass_hash) VALUES(?,?,?)")->execute([$name,$email,$hash]);
    $_SESSION['flash_ok'] = "Cadastro efetuado. Faça login.";
    header('Location: login.php'); exit;
  }
}
include __DIR__.'/includes/header.php';
?>
<h1 class="h3 mb-3">Criar conta</h1>
<?php if (!empty($errors)): ?>
  <div class="alert alert-danger"><ul class="mb-0"><?php foreach($errors as $e) echo '<li>'.h($e).'</li>';?></ul></div>
<?php endif; ?>
<form method="post" class="row g-3" autocomplete="off">
  <div class="col-md-6">
    <label class="form-label">Nome</label>
    <input name="name" class="form-control" required value="<?= h($_POST['name'] ?? '') ?>">
  </div>
  <div class="col-md-6">
    <label class="form-label">E-mail</label>
    <input name="email" type="email" class="form-control" required value="<?= h($_POST['email'] ?? '') ?>">
  </div>
  <div class="col-md-6">
    <label class="form-label">Senha</label>
    <input name="password" type="password" class="form-control" required>
    <div class="form-text">Mínimo 6 caracteres.</div>
  </div>
  <div class="col-12">
    <button class="btn btn-primary">Criar conta</button>
    <a class="btn btn-link" href="login.php">Já tenho conta</a>
  </div>
</form>
<?php include __DIR__.'/includes/footer.php'; ?>
