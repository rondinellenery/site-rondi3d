<?php
// /buscar.php — busca em posts publicados
require __DIR__.'/config.php';

$q = trim($_GET['q'] ?? '');
$rows = [];
if ($q !== '') {
  $st = db()->prepare("
    SELECT id,slug,title,excerpt,thumb_url,created_at
    FROM posts
    WHERE published=1 AND (title LIKE ? OR excerpt LIKE ? OR body LIKE ?)
    ORDER BY created_at DESC, id DESC
    LIMIT 50
  ");
  $like = "%$q%";
  $st->execute([$like,$like,$like]);
  $rows = $st->fetchAll();
}

$title = 'Buscar: '.($q?:'');
include __DIR__.'/includes/header.php';
?>
<h1 class="h3 mb-3">Resultados da busca</h1>

<form class="row g-2 mb-3" method="get">
  <div class="col-md-8">
    <input class="form-control" name="q" value="<?= h($q) ?>" placeholder="Buscar artigos…">
  </div>
  <div class="col-md-4 d-grid d-md-block"><button class="btn btn-primary">Buscar</button></div>
</form>

<?php if ($q===''): ?>
  <div class="alert alert-info">Digite um termo para buscar.</div>
<?php elseif (!$rows): ?>
  <div class="alert alert-warning">Nenhum artigo encontrado para “<?= h($q) ?>”.</div>
<?php else: ?>
  <div class="row g-3">
    <?php foreach ($rows as $r): ?>
      <div class="col-12 col-sm-6 col-lg-4">
        <div class="card h-100 post-card">
          <?php if (!empty($r['thumb_url'])): ?>
            <a href="<?= h(BASE_URL.'post.php?slug='.$r['slug']) ?>">
              <img class="post-thumb" src="<?= h($r['thumb_url']) ?>" alt="<?= h($r['title']) ?>">
            </a>
          <?php endif; ?>
          <div class="card-body d-flex flex-column">
            <div class="small text-muted mb-1"><?= h(date('d/m/Y', strtotime($r['created_at']))) ?></div>
            <h2 class="h6 mb-1">
              <a class="text-decoration-none" href="<?= h(BASE_URL.'post.php?slug='.$r['slug']) ?>">
                <?= h($r['title']) ?>
              </a>
            </h2>
            <?php if (!empty($r['excerpt'])): ?>
              <p class="small text-muted mb-3"><?= h(mb_strimwidth($r['excerpt'],0,140,'…','UTF-8')) ?></p>
            <?php endif; ?>
            <a class="mt-auto btn btn-sm btn-outline-primary" href="<?= h(BASE_URL.'post.php?slug='.$r['slug']) ?>">Ler mais</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php include __DIR__.'/includes/footer.php';
