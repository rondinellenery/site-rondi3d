<?php
require __DIR__.'/config.php';
$pdo = db();

$slug = trim($_GET['slug'] ?? '');
$id   = (int)($_GET['id'] ?? 0);

if ($slug!=='') {
  $st=$pdo->prepare("SELECT * FROM posts WHERE slug=? AND published=1");
  $st->execute([$slug]);
} else {
  $st=$pdo->prepare("SELECT * FROM posts WHERE id=? AND published=1");
  $st->execute([$id]);
}
$post = $st->fetch();
if (!$post){ http_response_code(404); include __DIR__.'/includes/header.php'; echo '<div class="alert alert-warning">Artigo não encontrado.</div>'; include __DIR__.'/includes/footer.php'; exit; }

$title = $post['title'];
include __DIR__.'/includes/header.php';
?>
<article class="mx-auto" style="max-width: 880px;">
  <header class="mb-3">
    <h1 class="h2 mb-1"><?=h($post['title'])?></h1>
    <div class="text-muted small"><?=h(date('d/m/Y H:i', strtotime($post['created_at'])))?></div>
    <?php if ($post['thumb_url']): ?>
      <img class="rounded mt-3 w-100" src="<?=h($post['thumb_url'])?>" alt="<?=h($post['title'])?>" style="aspect-ratio:16/9;object-fit:cover;">
    <?php endif; ?>
  </header>

  <div class="content">
    <?php
      // Corpo aceita HTML básico que você inseriu no admin
      echo $post['body'];
    ?>
  </div>
</article>

<?php include __DIR__.'/includes/footer.php';
