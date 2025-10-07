<?php
// /post-deletar.php
require __DIR__.'/config.php';
require_login();
require_admin();

if ($_SERVER['REQUEST_METHOD']!=='POST'){ http_response_code(405); exit('Método não permitido'); }
require_csrf();

$id = (int)($_POST['id'] ?? 0);
if ($id<=0){ http_response_code(400); exit('ID inválido'); }

$st = db()->prepare("DELETE FROM posts WHERE id=?");
$st->execute([$id]);

header('Location: '.BASE_URL.'admin_posts.php');
exit;
