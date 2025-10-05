<?php
// /pedido-deletar.php
require __DIR__ . '/config.php';
require_login();

// Aceita apenas POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Método não permitido');
}

// CSRF
require_csrf();

// ID do orçamento
$budgetId = (int)($_POST['id'] ?? 0);
if ($budgetId <= 0) {
  http_response_code(400);
  exit('ID inválido');
}

$pdo = db();

// Busca orçamento
$stmt = $pdo->prepare("SELECT id, user_id FROM budgets WHERE id=?");
$stmt->execute([$budgetId]);
$budget = $stmt->fetch();
if (!$budget) {
  http_response_code(404);
  exit('Orçamento não encontrado');
}

// Permissão: dono ou admin
if (!is_admin() && (int)$budget['user_id'] !== (int)(current_user()['id'] ?? 0)) {
  http_response_code(403);
  exit('Sem permissão para excluir este orçamento');
}

// Helper: converte URL pública (UPLOAD_URL) em caminho físico (UPLOAD_DIR)
function url_to_disk_path(string $urlPath): ?string {
  // Normaliza separadores
  $urlPath = str_replace('\\', '/', $urlPath);

  $uBase = rtrim(UPLOAD_URL, '/');   // ex.: /site-rondi3d/storage/uploads
  $dBase = rtrim(UPLOAD_DIR, '/\\'); // ex.: C:\xampp\htdocs\site-rondi3d\storage\uploads

  if ($uBase !== '' && str_starts_with($urlPath, $uBase)) {
    // remove o prefixo da URL pública
    $suffix = substr($urlPath, strlen($uBase)); // começa com /YYYY/MM/arquivo.png
    $suffix = str_replace('/', DIRECTORY_SEPARATOR, $suffix);
    return $dBase . $suffix;
  }

  // Compat: se vier como 'storage/uploads/...' (sem BASE_URL)
  if (str_starts_with($urlPath, 'storage/uploads')) {
    $suffix = substr($urlPath, strlen('storage/uploads')); // /YYYY/MM/arquivo.png
    $suffix = str_replace('/', DIRECTORY_SEPARATOR, $suffix);
    return $dBase . $suffix;
  }

  // Último caso: se vier um caminho absoluto já no disco
  if (preg_match('~^[a-zA-Z]:\\\\~', $urlPath) || str_starts_with($urlPath, '/')) {
    return $urlPath; // já é caminho físico
  }

  // Não reconhecido
  return null;
}

try {
  $pdo->beginTransaction();

  // Busca arquivos anexos
  $stF = $pdo->prepare("SELECT id, path FROM budget_files WHERE budget_id=?");
  $stF->execute([$budgetId]);
  $files = $stF->fetchAll();

  // Apaga arquivos físicos
  foreach ($files as $f) {
    $disk = url_to_disk_path((string)$f['path']);
    if ($disk && file_exists($disk)) {
      @unlink($disk); // silencia caso não consiga; DB ainda será limpo
    }
  }

  // Remove registros de arquivos
  $pdo->prepare("DELETE FROM budget_files WHERE budget_id=?")->execute([$budgetId]);

  // Remove o orçamento
  $pdo->prepare("DELETE FROM budgets WHERE id=?")->execute([$budgetId]);

  $pdo->commit();

  // ---- REDIRECT: se veio "return" (ex.: painel do admin), respeita;
  // caso contrário, mantém seu comportamento atual (Meus Orçamentos).
  $goBack = trim((string)($_POST['return'] ?? ''));
  if ($goBack === '') {
    $goBack = BASE_URL.'meus-orcamentos.php?msg=orcamento_excluido';
  }
  header('Location: '.$goBack);
  exit;

} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  http_response_code(500);
  exit('Falha ao excluir o orçamento. Tente novamente.');
}
