<?php
require __DIR__.'/config.php';
require_login();

// Aceita JSON POST
$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

// CSRF
if (!isset($data['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', $data['csrf'])){
  http_response_code(400);
  echo json_encode(['ok'=>false, 'error'=>'CSRF inválido']); exit;
}

$id          = (int)($data['id'] ?? 0);
$weight_g    = (float)($data['weight_g'] ?? 0);
$time_hours  = (float)($data['time_hours'] ?? 0);
$total_price = (float)($data['total_price'] ?? 0);
$material    = trim($data['material'] ?? 'Indefinido');
$breakdown   = $data['breakdown'] ?? null;

if ($id<=0 || $weight_g<=0 || $time_hours<=0 || $total_price<=0){
  http_response_code(400);
  echo json_encode(['ok'=>false, 'error'=>'Dados incompletos']); exit;
}

$pdo = db();

// dono ou admin
$st = $pdo->prepare("SELECT id, user_id FROM budgets WHERE id=?");
$st->execute([$id]);
$b = $st->fetch();
if (!$b){
  http_response_code(404);
  echo json_encode(['ok'=>false, 'error'=>'Orçamento não encontrado']); exit;
}
if (!is_admin() && (int)$b['user_id'] !== (int)(current_user()['id'] ?? 0)){
  http_response_code(403);
  echo json_encode(['ok'=>false, 'error'=>'Sem permissão']); exit;
}

// snapshot
$snap = [
  'material'  => $material,
  'breakdown' => $breakdown,
  'saved_at'  => date('c'),
  'by_admin'  => is_admin() ? 1 : 0,
];

$pdo->prepare("
  UPDATE budgets
     SET weight_g      = ?,
         time_hours    = ?,
         total_price   = ?,
         calc_snapshot = ?,
         calc_state    = 'ESTIMADO'
   WHERE id = ?
")->execute([$weight_g, $time_hours, $total_price, json_encode($snap, JSON_UNESCAPED_UNICODE), $id]);

echo json_encode(['ok'=>true]);
