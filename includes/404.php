<?php
require __DIR__.'/config.php';
http_response_code(404);
$title = 'Página não encontrada';
include __DIR__.'/includes/header.php';
?>
<div class="container py-5 text-center">
  <h1 class="display-6 mb-3">404</h1>
  <p class="lead">Página não encontrada.</p>
  <a class="btn btn-primary" href="index.php">Voltar ao início</a>
</div>
<?php include __DIR__.'/includes/footer.php'; ?>
