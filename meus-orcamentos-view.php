<?php
// meus-orcamentos-view.php
require __DIR__.'/config.php';
require_login();

$pdo = db();
$id = (int)($_GET['id'] ?? 0);

// Se não for admin, limita ao dono do orçamento
$uid = is_admin() ? null : (current_user()['id'] ?? 0);

// Busca orçamento
if ($uid) {
  $stmt = $pdo->prepare("SELECT * FROM budgets WHERE id=? AND user_id=?");
  $stmt->execute([$id, $uid]);
} else {
  $stmt = $pdo->prepare("SELECT * FROM budgets WHERE id=?");
  $stmt->execute([$id]);
}
$budget = $stmt->fetch();
if (!$budget) {
  http_response_code(404);
  include __DIR__.'/includes/header.php';
  echo '<div class="container py-5"><div class="alert alert-warning">Orçamento não encontrado.</div></div>';
  include __DIR__.'/includes/footer.php';
  exit;
}

// Helpers
function db_status_to_label(string $db): string {
  $map = [
    'RECEBIDO'    => 'Recebido',
    'EM_ANALISE'  => 'Em análise',
    'APROVADO'    => 'Aprovado',
    'EM_PRODUCAO' => 'Em produção',
    'CONCLUIDO'   => 'Concluído',
    'CANCELADO'   => 'Cancelado',
  ];
  return $map[$db] ?? $db;
}
function hm_from_decimal($hoursDec): string {
  $hoursDec = (float)$hoursDec;
  $h = (int)floor($hoursDec);
  $m = (int)round(($hoursDec - $h) * 60);
  if ($m === 60) { $h += 1; $m = 0; }
  if ($h > 0 && $m > 0) return "{$h}h {$m}min";
  if ($h > 0 && $m === 0) return "{$h}h";
  return "{$m}min";
}

// Arquivos anexos
$files = $pdo->prepare("SELECT * FROM budget_files WHERE budget_id=? ORDER BY id");
$files->execute([$budget['id']]);
$files = $files->fetchAll();

// Snapshot de cálculo (material, breakdown, etc.)
$snap = [];
if (!empty($budget['calc_snapshot'])) {
  $snap = json_decode($budget['calc_snapshot'], true) ?: [];
}
$material  = $snap['material'] ?? 'Indefinido';
$breakdown = $snap['breakdown'] ?? null;

$preco     = $budget['total_price'] !== null ? number_format((float)$budget['total_price'], 2, ',', '.') : null;
$calcState = $budget['calc_state'] ?? 'PENDENTE';

include __DIR__.'/includes/header.php';
?>

<div class="container py-4">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="meus-orcamentos.php">Meus Orçamentos</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?=h($budget['title'] ?: 'Orçamento #'.$budget['id'])?></li>
    </ol>
  </nav>

  <div class="d-flex align-items-center gap-3 mb-3">
    <h1 class="h3 m-0"><?=h($budget['title'] ?: 'Orçamento #'.$budget['id'])?></h1>
    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
      <?=h(db_status_to_label($budget['status']))?>
    </span>
  </div>

  <div class="row g-4">
    <div class="col-lg-7">
      <?php if ($files): ?>
        <div class="row g-3">
          <?php foreach ($files as $f): ?>
            <div class="col-6 col-md-4">
              <a href="<?=h($f['path'])?>" target="_blank" style="text-decoration:none">
                <img src="<?=h($f['path'])?>" alt="<?=h($f['filename'])?>" class="img-fluid rounded"
                     style="aspect-ratio:1/1; object-fit:cover;">
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="alert alert-info">Nenhuma imagem foi enviada para este orçamento.</div>
      <?php endif; ?>

      <?php if (!empty($budget['description'])): ?>
        <div class="mt-4">
          <h2 class="h6 text-uppercase text-muted mb-2">Descrição do projeto</h2>
          <p class="mb-0"><?=nl2br(h($budget['description']))?></p>
        </div>
      <?php endif; ?>
    </div>

    <div class="col-lg-5">
      <div class="card shadow-sm">
        <div class="card-body">
          <h2 class="h6 text-uppercase text-muted mb-3">Resumo</h2>

          <dl class="row mb-0">
            <dt class="col-6">ID</dt>
            <dd class="col-6">#<?=h($budget['id'])?></dd>

            <dt class="col-6">Criado em</dt>
            <dd class="col-6"><?=h(date('d/m/Y H:i', strtotime($budget['created_at'] ?? 'now')))?></dd>

            <dt class="col-6">Material</dt>
            <dd class="col-6"><?=h($material)?></dd>

            <dt class="col-6">Peso</dt>
            <dd class="col-6"><?=h((int)$budget['weight_g'])?> g</dd>

            <dt class="col-6">Tempo</dt>
            <dd class="col-6"><?=h(hm_from_decimal($budget['time_hours']))?></dd>

            <dt class="col-6">Preço estimado</dt>
            <dd class="col-6">
              <?php if ($preco !== null): ?>
                R$ <?=h($preco)?>
              <?php else: ?>
                —
              <?php endif; ?>
            </dd>

            <dt class="col-6">Estado do cálculo</dt>
            <dd class="col-6">
              <?php if ($calcState === 'PENDENTE'): ?>
                <span class="badge text-bg-warning">Pendente de cálculo</span>
              <?php elseif ($calcState === 'ESTIMADO'): ?>
                <span class="badge text-bg-info">Estimado</span>
              <?php else: ?>
                <span class="badge text-bg-success">Final</span>
              <?php endif; ?>
            </dd>
          </dl>

          <div class="d-flex gap-2 mt-3">
            <a href="calculadora.php?for_budget=<?=h($budget['id'])?>&return=<?=rawurlencode('meus-orcamentos-view.php?id='.$budget['id'])?>"
               class="btn btn-outline-primary">Quero calcular</a>

            <form method="post" action="pedido-deletar.php" onsubmit="return confirm('Tem certeza que deseja excluir este orçamento?');">
              <input type="hidden" name="csrf" value="<?=h(csrf_token())?>">
              <input type="hidden" name="id" value="<?=h($budget['id'])?>">
              <button class="btn btn-danger">Excluir orçamento</button>
            </form>
          </div>

          <p class="text-muted small mt-3">
            * Valores exibidos são <strong>estimativas</strong>. O cálculo definitivo pode ser ajustado após análise técnica.
          </p>
        </div>
      </div>

      <div class="d-flex gap-2 mt-3">
        <a href="meus-orcamentos.php" class="btn btn-outline-secondary">Voltar</a>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__.'/includes/footer.php'; ?>
