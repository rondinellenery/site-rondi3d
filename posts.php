<?php
require __DIR__.'/config.php';

$title = 'Artigos';
$pdo   = db();

$q     = trim($_GET['q'] ?? '');
$page  = max(1, (int)($_GET['p'] ?? 1));
$per   = 9;
$off   = ($page-1)*$per;

$where = ["published = 1"];
$args  = [];

/**
 * Tentamos usar FULLTEXT (MATCH ... AGAINST). Se der erro (ex.: índice ainda não criado),
 * fazemos fallback para LIKE simples.
 */
$useMatch = false;
if ($q !== '') {
  // 1) tenta MATCH
  $sqlTest = "SELECT COUNT(*) FROM posts WHERE published=1 AND MATCH(title,excerpt,body) AGAINST (? IN NATURAL LANGUAGE MODE)";
  try {
    $st = $pdo->prepare($sqlTest);
    $st->execute([$q]);
    $useMatch = true;
  } catch (Throwable $e) {
    $useMatch = false;
  }

  if ($useMatch) {
    $where[] = "MATCH(title,excerpt,body) AGAINST (? IN NATURAL LANGUAGE MODE)";
    $args[]  = $q;
  } else {
    $where[] = "(title LIKE ? OR IFNULL(excerpt,'') LIKE ? OR body LIKE ?)";
    $args[]  = "%$q%";
    $args[]  = "%$q%";
    $args[]  = "%$q%";
  }
}

$wsql = 'WHERE '.implode(' AND ', $where);

// total p/ paginação
$st = $pdo->prepare("SELECT COUNT(*) FROM posts $wsql");
$st->execute($args);
$total = (int)$st->fetchColumn();
$pages = max(1, (int)ceil($total/$per));

// listagem
$sql = "
  SELECT id, slug, title, thumb_url, excerpt, created_at
  FROM posts
  $wsql
  ORDER BY created_at DESC, id DESC
  LIMIT $per OFFSET $off
";
$st = $pdo->prepare($sql);
$st->execute($args);
$rows = $st->fetchAll();

include __DIR__.'/includes/header.php';
?>

<div class="post-grid-title mb-3">
  <h1 class="h3 m-0">Artigos</h1>
  <form action="<?= h(BASE_URL) ?>posts.php" method="get" class="d-flex gap-2">
    <input class="form-control form-control-sm" type="text" name="q" value="<?= h($q) ?>" placeholder="Buscar artigos...">
    <button class="btn btn-sm btn-primary">Buscar</button>
  </form>
</div>

<?php if ($q !== ''): ?>
  <div class="alert alert-secondary py-2">Resultados para: <strong><?= h($q) ?></strong></div>
<?php endif; ?>

<?php if (!$rows): ?>
  <div class="alert alert-info">Nenhuma publicação encontrada.</div>
<?php else: ?>
  <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
    <?php foreach ($rows as $p): ?>
      <div class="col">
        <a class="card h-100 text-decoration-none" href="<?= h(BASE_URL) ?>post.php?slug=<?= h($p['slug']) ?>">
          <?php if (!empty($p['thumb_url'])): ?>
            <img src="<?= h($p['thumb_url']) ?>" class="card-img-top"
                 alt="<?= h($p['title']) ?>" style="aspect-ratio:16/9;object-fit:cover;">
          <?php endif; ?>
          <div class="card-body d-flex flex-column">
            <div class="post-meta mb-1"><?= h(date('d/m/Y', strtotime($p['created_at']))) ?></div>
            <h2 class="h6 mb-2"><?= h($p['title']) ?></h2>
            <div class="text-muted small mt-auto"
                 style="display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">
              <?= h($p['excerpt'] ?? '') ?>
            </div>
          </div>
        </a>
      </div>
    <?php endforeach; ?>
  </div>

  <?php if ($pages > 1): ?>
    <nav class="mt-3">
      <ul class="pagination">
        <?php
          $base = 'posts.php?'.http_build_query(array_filter(['q'=>$q]));
        ?>
        <li class="page-item <?= $page<=1?'disabled':'' ?>">
          <a class="page-link" href="<?= $base.($base?'&':'').'p='.max(1,$page-1) ?>">&laquo;</a>
        </li>
        <?php for($i=max(1,$page-2); $i<=min($pages,$page+2); $i++): ?>
          <li class="page-item <?= $i===$page?'active':'' ?>">
            <a class="page-link" href="<?= $base.($base?'&':'').'p='.$i ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
        <li class="page-item <?= $page>=$pages?'disabled':'' ?>">
          <a class="page-link" href="<?= $base.($base?'&':'').'p='.min($pages,$page+1) ?>">&raquo;</a>
        </li>
      </ul>
    </nav>
  <?php endif; ?>
<?php endif; ?>

<?php include __DIR__.'/includes/footer.php'; ?>
