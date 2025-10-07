<?php
require __DIR__.'/config.php';
require_login();
require_csrf();

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0){ http_response_code(400); exit('ID inválido'); }

$pdo = db();
$uid = current_user()['id'];

// só dono (ou admin)
if (is_admin()){
  $stmt = $pdo->prepare("SELECT id FROM budgets WHERE id=?");
  $stmt->execute([$id]);
} else {
  $stmt = $pdo->prepare("SELECT id FROM budgets WHERE id=? AND user_id=?");
  $stmt->execute([$id, $uid]);
}
$exists = $stmt->fetchColumn();
if (!$exists){ http_response_code(404); exit('Orçamento não encontrado.'); }

// pega arquivos para apagar depois
$files = $pdo->prepare("SELECT path FROM budget_files WHERE budget_id=?");
$files->execute([$id]);
$files = $files->fetchAll(PDO::FETCH_COLUMN);

$pdo->beginTransaction();
$pdo->prepare("DELETE FROM budget_files WHERE budget_id=?")->execute([$id]);
$pdo->prepare("DELETE FROM budgets WHERE id=?")->execute([$id]);
$pdo->commit();

// apaga fisicamente
foreach ($files as $url){
  // converte URL pública para caminho físico (ajuste se necessário)
  $rel = str_replace(UPLOAD_URL, '', $url);
  $abs = rtrim(UPLOAD_DIR,'/\\') . str_replace('/', DIRECTORY_SEPARATOR, $rel);
  if (is_file($abs)) @unlink($abs);
}

header('Location: meus-orcamentos.php');
