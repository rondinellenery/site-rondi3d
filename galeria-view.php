<?php
require __DIR__.'/config.php';
$pdo = db();

$slug = trim($_GET['slug'] ?? '');
$st = $pdo->prepare("SELECT * FROM posts WHERE slug=? AND published=1 AND COALESCE(type,'post')='gallery'");
$st->execute([$slug]);
$p = $st->fetch();
if (!$p){ http_response_code(404); exit('Projeto não encontrado'); }

$imgs = $pdo->prepare("SELECT path FROM post_images WHERE post_id=? ORDER BY sort,id");
$imgs->execute([$p['id']]);
$imgs = $imgs->fetchAll();

$title = $p['title'].' · Galeria';
include __DIR__.'/includes/header.php';
?>
<div class="gallery-view">
  <div class="row g-4">
    <div class="col-lg-7">
      <?php if ($p['thumb_url']): ?>
        <img src="<?= h($p['thumb_url']) ?>" class="img-fluid rounded border mb-2" alt="<?= h($p['title']) ?>">
      <?php endif; ?>

      <?php if ($imgs): ?>
        <div class="thumbs">
          <?php foreach($imgs as $im): ?>
            <img src="<?= h($im['path']) ?>" data-viewer data-src="<?= h($im['path']) ?>" alt="">
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="col-lg-5">
      <h1 class="h4"><?= h($p['title']) ?></h1>
      <div class="text-muted mb-3"><?= nl2br(h($p['excerpt'] ?? '')) ?></div>

      <div class="border rounded p-3 mb-3">
        <div class="fw-semibold mb-2">Ficha técnica</div>
        <dl class="row mb-0">
          <dt class="col-4 col-md-3">Pintor(a)</dt><dd class="col-8 col-md-9"><?= h($p['painter'] ?? '—') ?></dd>
          <dt class="col-4 col-md-3">Tamanho</dt><dd class="col-8 col-md-9"><?= h($p['piece_size'] ?? '—') ?></dd>
          <dt class="col-4 col-md-3">Preço</dt><dd class="col-8 col-md-9">
            <?= $p['unit_price']!==null ? 'R$ '.number_format((float)$p['unit_price'],2,',','.') : '—' ?>
          </dd>
        </dl>
      </div>

      <a class="btn btn-primary" href="<?= h(BASE_URL) ?>novo-orcamento.php?ref=<?= urlencode($p['title']) ?>&tipo=galeria">Peça o seu</a>
    </div>
  </div>
</div>

<!-- Modal simples de preview -->
<div class="modal fade" id="imgModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <img src="" alt="" class="w-100 rounded">
    </div>
  </div>
</div>

<?php include __DIR__.'/includes/footer.php';
