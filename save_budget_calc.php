<?php
// /save_budget_calc.php
require __DIR__ . '/config.php';
require_login();
require_admin(); // só admin pode “gravar cálculo” em orçamento

// Aceita apenas JSON
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  header('Allow: POST');
  exit('Método não permitido.');
}

$raw = file_get_contents('php://input');
$payload = json_decode($raw, true);

// CSRF básico
if (!isset($payload['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', $payload['csrf'])) {
  http_response_code(400);
  exit('CSRF inválido.');
}

// Campos obrigatórios
$budgetId   = (int)($payload['id'] ?? 0);
$weight_g   = (float)($payload['weight_g'] ?? 0);
$time_hours = (float)($payload['time_hours'] ?? 0);
$price      = (float)($payload['price_final'] ?? 0);
$material   = trim($payload['material'] ?? 'Indefinido');

// snapshot_json deve trazer params_used + breakdown
$snapshot_json = $payload['snapshot_json'] ?? null;
if ($budgetId <= 0 || $weight_g <= 0 || $time_hours <= 0 || $price <= 0 || !$snapshot_json) {
  http_response_code(400);
  exit('Payload incompleto.');
}

// Valida orçamento existente
$pdo = db();
$st  = $pdo->prepare("SELECT id FROM budgets WHERE id = ? LIMIT 1");
$st->execute([$budgetId]);
if (!$st->fetch()) {
  http_response_code(404);
  exit('Orçamento não encontrado.');
}

// Monta snapshot final (garantindo material dentro do JSON)
$ss = json_decode($snapshot_json, true);
if (!is_array($ss)) $ss = [];
$ss['material'] = $material;
$final_snapshot = json_encode($ss, JSON_UNESCAPED_UNICODE);

// Atualiza somente este orçamento
$upd = $pdo->prepare("
  UPDATE budgets
     SET weight_g     = ?,
         time_hours   = ?,
         total_price  = ?,
         calc_state   = 'ESTIMADO',
         calc_snapshot= ?,
         updated_at   = NOW()
   WHERE id = ?
");
$upd->execute([$weight_g, $time_hours, $price, $final_snapshot, $budgetId]);

header('Content-Type: application/json');
echo json_encode(['ok' => true, 'id' => $budgetId], JSON_UNESCAPED_UNICODE);
