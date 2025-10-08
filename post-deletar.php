<?php
// /post-deletar.php — exclui post/galeria (somente admin, via POST)
require __DIR__ . '/config.php';
require_login();
require_admin();

// Aceita apenas POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Método não permitido');
}

// CSRF obrigatório
require_csrf();

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
  http_response_code(400);
  exit('ID inválido');
}

// Redirecionamento opcional (somente paths locais)
$redirect = trim($_POST['redirect'] ?? '');
if ($redirect === '' || str_contains($redirect, '://') || !str_starts_with($redirect, '/')) {
  $redirect = BASE_URL . 'admin_posts.php';
}

$pdo = db();

// Carrega post alvo
$st = $pdo->prepare("SELECT id, thumb_url, COALESCE(type,'post') AS type FROM posts WHERE id = ?");
$st->execute([$id]);
$post = $st->fetch();
if (!$post) {
  http_response_code(404);
  exit('Post não encontrado');
}

/**
 * Helper: converte URL pública (UPLOAD_URL) em caminho físico (UPLOAD_DIR).
 * Útil caso você opte por excluir arquivos do disco.
 */
function url_to_disk(string $url): ?string {
  $prefix = rtrim(UPLOAD_URL, '/').'/';
  if (str_starts_with($url, $prefix)) {
    $rel = substr($url, strlen($prefix)); // ex.: covers/2025/10/arquivo.jpg
    $rel = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $rel);
    return rtrim(UPLOAD_DIR, '/\\') . DIRECTORY_SEPARATOR . $rel;
  }
  return null;
}

try {
  $pdo->beginTransaction();

  // (Opcional) Apagar arquivo da CAPA do post
  // Por segurança, deixo comentado. Descomente se quiser remover o arquivo físico.
  /*
  if (!empty($post['thumb_url'])) {
    $f = url_to_disk($post['thumb_url']);
    if ($f && is_file($f)) {
      @unlink($f);
    }
  }
  */

  // Busca imagens da galeria (se houver)
  $imgsStmt = $pdo->prepare("SELECT id, path FROM post_images WHERE post_id = ?");
  $imgsStmt->execute([$id]);
  $images = $imgsStmt->fetchAll();

  // (Opcional) Apagar arquivos físicos das imagens da galeria
  // Também deixo comentado por segurança.
  /*
  foreach ($images as $im) {
    $f = url_to_disk((string)$im['path']);
    if ($f && is_file($f)) {
      @unlink($f);
    }
  }
  */

  // Remove registros da galeria
  $pdo->prepare("DELETE FROM post_images WHERE post_id = ?")->execute([$id]);

  // Remove o post
  $pdo->prepare("DELETE FROM posts WHERE id = ?")->execute([$id]);

  $pdo->commit();

  // Mensagem simples (query string) + redireciona
  // Ex.: admin_posts.php?msg=post_excluido
  $sep = (str_contains($redirect, '?') ? '&' : '?');
  header('Location: ' . $redirect . $sep . 'msg=post_excluido');
  exit;

} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  http_response_code(500);
  // Você pode logar $e->getMessage() se quiser
  exit('Falha ao excluir o post.');
}
