<?php
// admins_orcamento_status.php — altera status e/ou calc_state (somente admin)
require __DIR__ . '/config.php';
require_login();
require_admin();
require_csrf();

$id = (int)($_POST['id'] ?? 0);
$onlyCalc = isset($_POST['only_calc']);

if ($id <= 0){ http_response_code(400); exit('ID inválido'); }

$pdo = db();
$st  = $pdo->prepare("SELECT id FROM budgets WHERE id=?");
$st->execute([$id]);
if (!$st->fetch()){ http_response_code(404); exit('Orçamento não encontrado'); }

if ($onlyCalc){
  $calc = trim($_POST['calc_state'] ?? '');
  if (!in_array($calc, ['PENDENTE','ESTIMADO','FINAL'], true)){
    http_response_code(400); exit('calc_state inválido');
  }
  $st = $pdo->prepare("UPDATE budgets SET calc_state=? WHERE id=?");
  $st->execute([$calc,$id]);
} else {
  $status = trim($_POST['status'] ?? '');
  if (!in_array($status, ['RECEBIDO','EM_ANALISE','APROVADO','EM_PRODUCAO','CONCLUIDO','CANCELADO'], true)){
    http_response_code(400); exit('status inválido');
  }
  $st = $pdo->prepare("UPDATE budgets SET status=? WHERE id=?");
  $st->execute([$status,$id]);
}

$back = $_SERVER['HTTP_REFERER'] ?? (BASE_URL.'admins_orcamentos.php');
header('Location: '.$back);
