<?php
// /admin_posts.php — Gerenciador de artigos/galerias (somente admin)
require __DIR__ . '/config.php';
require_login();
if (!is_admin()) { http_response_code(403); exit('Somente admin.'); }

$pdo = db();

$q    = trim($_GET['q'] ?? '');
$pub  = trim($_GET['published'] ?? '');
$page = max(1, (int)($_GET['p'] ?? 1));
$per  = 12;
$off  = ($page - 1) * $per;

$where = [];
$args  = [];

// filtro de busca (título, slug, resumo)
if ($q !== '') {
  $where[] = "(title LIKE ? OR slug LIKE ? OR excerpt LIKE ?)";
  $args[] = "%$q%"; $args[] = "%$q%"; $args[] = "%$q%";
}

// filtro publicado/rascunho
if ($pub !== '') {
  $where[] = "published = ?";
  $args[]  = (int)$pub;
}

$w = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

// total para paginação
$st = $pdo->prepare("SELECT COUNT(*) FROM posts $w");
$st->execute($args);
$total = (int)$st->fetchColumn();
$pages = max(1, (int)ceil($total / $per));

// busca
$sql = "
  SELECT id, slug, title, thumb_url, excerpt, published,
         COALESCE(type,'post') AS type, created_at
    FROM posts
    $w
   ORDER BY created_at DESC, id DESC
   LIMIT $per OFFSET $off
";
$st = $pdo->prepare($sql);
$st->execute($args);
$rows = $st->fetchAll();

$title = 'Admin — Posts';
include __DIR__ . '/includes/header.php';
?>
<div class="d-flex align-items-center gap-2 mb-3">
  <h1 class="h3 m-0">Admin — Posts</h1>
  <a class="btn btn-primary ms-auto" href="<?= h(BASE_URL) ?>admin_post_edit.php">Novo post</a>
</div>

<form class="row g-2 mb-3" method="get">
  <div class="col-md-6">
    <input class="form-control" name="q" value="<?= h($q) ?>" placeholder="Buscar por título, slug, resumo...">
  </div>
  <div class="col-md-3">
    <select name="published" class="form-select">
      <option value="">— Todos —</option>
      <option value="1" <?= $pub==='1'?'selected':'' ?>>Publicados</option>
      <option value="0" <?= $pub==='0'?'selected':'' ?>>Rascunhos</option>
    </select>
  </div>
  <div class="col-md-3 d-grid">
    <button class="btn btn-secondary">Filtrar</button>
  </div>
</form>

<?php if (!$rows): ?>
  <div class="alert alert-info">Nenhum post encontrado.</div>
<?php else: ?>
  <div class="row g-3">
    <?php foreach ($rows as $r): ?>
      <div class="col-12 col-md-6 col-lg-4">
        <div class="card h-100">
          <?php if (!empty($r['thumb_url'])): ?>
            <img src="<?= h($r['thumb_url']) ?>" class="card-img-top" style="aspect-ratio:16/9; object-fit:cover" alt="">
          <?php endif; ?>

          <div class="card-body d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center">
              <div class="small text-muted"><?= h(date('d/m/Y H:i', strtotime($r['created_at']))) ?></div>
              <span class="badge bg-secondary"><?= $r['type']==='gallery' ? 'Galeria' : 'Artigo' ?></span>
            </div>

            <h2 class="h6 mt-2 mb-2"><?= h($r['title']) ?></h2>

            <?php if (!empty($r['excerpt'])): ?>
              <div class="text-muted small mb-3"
                   style="display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">
                <?= h($r['excerpt']) ?>
              </div>
            <?php endif; ?>

            <div class="mt-auto d-flex justify-content-between align-items-center">
              <span class="badge <?= $r['published'] ? 'text-bg-success' : 'text-bg-secondary' ?>">
                <?= $r['published'] ? 'Publicado' : 'Rascunho' ?>
              </span>

              <div class="d-flex align-items-center gap-2">
                <!-- Editar -->
                <a class="btn btn-sm btn-outline-primary"
                   href="<?= h(BASE_URL) ?>admin_post_edit.php?id=<?= (int)$r['id'] ?>">
                  Editar
                </a>

                <!-- Ver (sempre fora de <form>, abre em nova aba) -->
                <?php if ($r['type'] === 'gallery'): ?>
                  <a class="btn btn-sm btn-outline-dark"
                     target="_blank" rel="noopener"
                     href="<?= h(BASE_URL) ?>galeria-view.php?slug=<?= h($r['slug']) ?>">
                    Ver
                  </a>
                <?php else: ?>
                  <a class="btn btn-sm btn-outline-dark"
                     target="_blank" rel="noopener"
                     href="<?= h(BASE_URL) ?>post.php?slug=<?= h($r['slug']) ?>">
                    Ver
                  </a>
                <?php endif; ?>

                <!-- Excluir (form separado com redirect para a URL atual) -->
                <form method="post"
                      action="<?= h(BASE_URL) ?>post-deletar.php"
                      class="d-inline"
                      onsubmit="return confirm('Tem certeza que deseja excluir este post?');">
                  <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
                  <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                  <!-- Redirect: volta exatamente para a mesma listagem/página/filtro -->
                  <input type="hidden" name="redirect" value="<?= h($_SERVER['REQUEST_URI'] ?? (BASE_URL.'admin_posts.php')) ?>">
                  <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                </form>
              </div>
            </div>

          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <?php if ($pages > 1): ?>
    <nav class="mt-3">
      <ul class="pagination">
        <?php
          $base = 'admin_posts.php?' . http_build_query(array_filter([
            'q' => $q,
            'published' => $pub
          ]));
        ?>
        <li class="page-item <?= $page<=1?'disabled':'' ?>">
          <a class="page-link" href="<?= $base . ($base ? '&' : '') . 'p=' . max(1, $page-1) ?>">&laquo;</a>
        </li>
        <?php for ($i=max(1,$page-2); $i<=min($pages,$page+2); $i++): ?>
          <li class="page-item <?= $i===$page?'active':'' ?>">
            <a class="page-link" href="<?= $base . ($base ? '&' : '') . 'p=' . $i ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
        <li class="page-item <?= $page>=$pages?'disabled':'' ?>">
          <a class="page-link" href="<?= $base . ($base ? '&' : '') . 'p=' . min($pages, $page+1) ?>">&raquo;</a>
        </li>
      </ul>
    </nav>
  <?php endif; ?>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php';
