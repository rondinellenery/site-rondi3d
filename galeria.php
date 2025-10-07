<?php
require __DIR__.'/config.php';
$title = 'Galeria';
$pdo = db();

$q     = trim($_GET['q'] ?? '');
$where = ["published=1","COALESCE(type,'post')='gallery'"];
$args  = [];

if ($q!==''){
  $where[]="(title LIKE ? OR IFNULL(excerpt,'') LIKE ?)";
  $args[]="%$q%"; $args[]="%$q%";
}
$wsql='WHERE '.implode(' AND ',$where);

$rows = $pdo->prepare("
  SELECT id, slug, title, thumb_url, excerpt, unit_price
  FROM posts
  $wsql
  ORDER BY created_at DESC, id DESC
");
$rows->execute($args);
$rows = $rows->fetchAll();

include __DIR__.'/includes/header.php';
?>
<div class="d-flex align-items-center justify-content-between mb-3">
  <h1 class="h3 m-0">Galeria de projetos</h1>
  <form class="d-none d-md-flex gap-2" method="get" action="<?= h(BASE_URL) ?>galeria.php">
    <input class="form-control form-control-sm" name="q" value="<?= h($q) ?>" placeholder="Buscar projeto…">
    <button class="btn btn-sm btn-primary">Buscar</button>
  </form>
</div>

<?php if (!$rows): ?>
  <div class="alert alert-info">Nenhum projeto encontrado.</div>
<?php else: ?>
  <div class="gallery-grid">
    <?php foreach($rows as $p): ?>
      <a class="gallery-card text-decoration-none" href="<?= h(BASE_URL) ?>galeria-view.php?slug=<?= h($p['slug']) ?>">
        <?php if ($p['thumb_url']): ?>
          <img src="<?= h($p['thumb_url']) ?>" alt="<?= h($p['title']) ?>">
        <?php endif; ?>
        <div class="gallery-meta">
          <div class="small text-muted mb-1"><?= h($p['excerpt'] ?? '') ?></div>
          <div class="d-flex justify-content-between align-items-center">
            <div class="fw-semibold"><?= h($p['title']) ?></div>
            <?php if ($p['unit_price'] !== null): ?>
              <div class="price">R$ <?= number_format((float)$p['unit_price'],2,',','.') ?></div>
            <?php endif; ?>
          </div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php include __DIR__.'/includes/footer.php';
