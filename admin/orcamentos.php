<?php
require __DIR__.'/../config.php';
if (!is_admin()) { http_response_code(403); exit('Acesso restrito.'); }
$title = "Admin — Orçamentos";

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['id'], $_POST['status'])) {
  $id = (int)$_POST['id']; $st = $_POST['status'];
  if (in_array($st, ESTIMATE_STATUSES, true)) {
    db()->prepare("UPDATE estimates SET status=? WHERE id=?")->execute([$st,$id]);
  }
  header('Location: orcamentos.php'); exit;
}

$rows = db()->query("SELECT e.*, u.name,u.email,
       (SELECT path FROM files f WHERE f.estimate_id=e.id ORDER BY id DESC LIMIT 1) AS thumb
       FROM estimates e JOIN users u ON u.id=e.user_id ORDER BY e.id DESC")->fetchAll();

include __DIR__.'/../includes/header.php';
?>
<h1 class="h4 mb-3">Admin — Orçamentos</h1>
<div class="table-responsive">
<table class="table table-sm align-middle">
  <thead><tr>
    <th>#</th><th>Cliente</th><th>Título</th><th>Total</th><th>Status</th><th>Data</th><th>Imagem</th><th>Ação</th>
  </tr></thead>
  <tbody>
    <?php foreach($rows as $r): ?>
      <tr>
        <td><?= (int)$r['id'] ?></td>
        <td><?= h($r['name']) ?><br><small class="text-muted"><?= h($r['email']) ?></small></td>
        <td style="max-width:240px"><?= h($r['title'] ?: substr($r['description'],0,60).'…') ?></td>
        <td>R$ <?= number_format((float)$r['price_final'],2,',','.') ?></td>
        <td><span class="badge bg-secondary"><?= h($r['status']) ?></span></td>
        <td><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></td>
        <td><?php if($r['thumb']): ?><img src="../<?= h($r['thumb']) ?>" style="width:56px;height:56px;object-fit:cover" class="rounded"><?php endif; ?></td>
        <td>
          <form method="post" class="d-flex gap-2">
            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
            <select name="status" class="form-select form-select-sm">
              <?php foreach(ESTIMATE_STATUSES as $st) echo '<option'.($st===$r['status']?' selected':'').'>'.$st.'</option>'; ?>
            </select>
            <button class="btn btn-sm btn-primary">Atualizar</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php include __DIR__.'/../includes/footer.php'; ?>
