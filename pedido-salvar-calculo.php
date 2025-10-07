<?php
// pedido-salvar-calculo.php
require __DIR__.'/config.php';
require_login();
require_csrf();

$pdo = db();

$id  = (int)($_POST['id'] ?? 0);
$w   = (int)($_POST['weight_g'] ?? 0);
$th  = (float)($_POST['time_h'] ?? 0);
$pr  = isset($_POST['price']) ? (float)$_POST['price'] : null;
$snap= trim($_POST['snapshot'] ?? '');

// valida dono (a não ser admin)
if (!is_admin()){
  $stmt = $pdo->prepare("SELECT user_id FROM budgets WHERE id=?");
  $stmt->execute([$id]);
  $own = $stmt->fetchColumn();
  if (!$own || (int)$own !== (int)current_user()['id']){
    http_response_code(403); exit('Forbidden');
  }
}

$stmt = $pdo->prepare("
  UPDATE budgets
     SET weight_g = ?,
         time_hours = ?,
         total_price = ?,
         calc_snapshot = ?,
         calc_state = 'ESTIMADO'
   WHERE id = ?
  LIMIT 1
");
$stmt->execute([$w, $th, $pr, $snap, $id]);

$_SESSION['flash_ok'] = 'Cálculo salvo neste orçamento.';

$redir = $_POST['return'] ?? ('meus-orcamentos-view.php?id='.$id);
header('Location: '.$redir);
