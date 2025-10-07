<?php
// save-calc.php
require __DIR__.'/config.php';
require_login();

header('Content-Type: application/json; charset=utf-8');

// Lê JSON
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) { echo json_encode(['ok'=>false,'error'=>'Payload inválido']); exit; }

// CSRF
if (empty($data['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', $data['csrf'])) {
  echo json_encode(['ok'=>false,'error'=>'CSRF inválido']); exit;
}

$budgetId  = (int)($data['budget_id'] ?? 0);
$peso      = (float)($data['peso'] ?? 0);
$tempoH    = (float)($data['tempo_h'] ?? 0);
$material  = trim($data['material'] ?? 'Indefinido');
$final     = (float)($data['final_price'] ?? 0);
$breakdown = $data['breakdown'] ?? null;

if ($budgetId<=0 || $peso<=0 || $tempoH<=0 || $final<=0) {
  echo json_encode(['ok'=>false,'error'=>'Dados incompletos']); exit;
}

$pdo = db();

// Busca orçamento
$stmt = $pdo->prepare("SELECT id, user_id FROM budgets WHERE id=?");
$stmt->execute([$budgetId]);
$bud = $stmt->fetch();
if (!$bud) { echo json_encode(['ok'=>false,'error'=>'Orçamento não existe']); exit; }

// Permissão: dono ou admin
if (!is_admin() && (int)$bud['user_id'] !== (int)current_user()['id']) {
  echo json_encode(['ok'=>false,'error'=>'Sem permissão']); exit;
}

// Monta snapshot p/ histórico
$snapshot = [
  'material'  => $material,
  'breakdown' => $breakdown,
  'defaults'  => (defined('CALC_DEFAULTS') && is_array(CALC_DEFAULTS)) ? CALC_DEFAULTS : null,
  'ts'        => (int)($data['ts'] ?? time()*1000)
];

$pdo->beginTransaction();
$up = $pdo->prepare("
  UPDATE budgets
     SET weight_g = ?,
         time_hours = ?,
         total_price = ?,
         calc_snapshot = ?,
         calc_state = 'ESTIMADO'
   WHERE id = ?
");
$up->execute([
  (int)$peso,
  $tempoH,
  $final,
  json_encode($snapshot, JSON_UNESCAPED_UNICODE),
  $budgetId
]);
$pdo->commit();

echo json_encode(['ok'=>true]);
