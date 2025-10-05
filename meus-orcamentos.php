<?php
require __DIR__.'/config.php'; require_login();
$title = "Meus Orçamentos";
$rows = db()->prepare("SELECT e.*, (SELECT path FROM files f WHERE f.estimate_id=e.id ORDER BY id DESC LIMIT 1) AS thumb
                       FROM estimates e WHERE user_id=? ORDER BY id DESC");
$rows->execute([ current_user()['id'] ]);
$rows = $rows->fetchAll();
include __DIR__.'/includes/header.php';
?>
<h1 class="h3 mb-3">Meus Orçamentos</h1>

<?php if (!$rows): ?>
  <div class="alert alert-info">Você ainda não enviou nenhum orçamento. Comece pela <a href="calculadora.php">Calculadora 3D</a>.</div>
<?php else: ?>
  <div class="row g-3">
    <?php foreach($rows as $r): ?>
      <div class="col-md-6">
        <div class="border rounded p-3 h-100 d-flex gap-3">
          <?php if ($r['thumb']): ?>
            <img src="<?= h($r['thumb']) ?>" alt="" style="width:96px;height:96px;object-fit:cover" class="rounded">
          <?php else: ?>
            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:96px;height:96px">—</div>
          <?php endif; ?>
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between">
              <strong><?= h($r['title'] ?: 'Orçamento #'.$r['id']) ?></strong>
              <span class="badge bg-secondary"><?= h($r['status']) ?></span>
            </div>
            <div class="small text-muted mb-1"><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></div>
            <div class="small">Peso: <?= h($r['weight_g']) ?> g · Tempo: <?= h($r['time_h_decimal']) ?> h</div>
            <div class="fw-bold mt-1">Total: R$ <?= number_format((float)$r['price_final'],2,',','.') ?></div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php include __DIR__.'/includes/footer.php'; ?>
