<?php
// meus-orcamentos.php — listagem dos orçamentos do usuário (ou todos, se admin)
require __DIR__.'/config.php';
require_login();

$pdo  = db();
$meId = current_user()['id'];
$isAd = is_admin();

// --------- Helpers ---------
function label_to_enum($label){
  $map = [
    'Recebido'    => 'RECEBIDO',
    'Em análise'  => 'EM_ANALISE',
    'Aprovado'    => 'APROVADO',
    'Em produção' => 'EM_PRODUCAO',
    'Concluído'   => 'CONCLUIDO',
    'Cancelado'   => 'CANCELADO',
  ];
  return $map[$label] ?? '';
}
function enum_to_label($enum){
  $map = [
    'RECEBIDO'    => 'Recebido',
    'EM_ANALISE'  => 'Em análise',
    'APROVADO'    => 'Aprovado',
    'EM_PRODUCAO' => 'Em produção',
    'CONCLUIDO'   => 'Concluído',
    'CANCELADO'   => 'Cancelado',
  ];
  return $map[$enum] ?? $enum;
}
function status_badge_class($enum){
  return match($enum){
    'RECEBIDO'    => 'bg-secondary',
    'EM_ANALISE'  => 'bg-info',
    'APROVADO'    => 'bg-primary',
    'EM_PRODUCAO' => 'bg-warning text-dark',
    'CONCLUIDO'   => 'bg-success',
    'CANCELADO'   => 'bg-danger',
    default       => 'bg-secondary',
  };
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

// --------- Filtros ---------
$q      = trim($_GET['q'] ?? '');
$st     = trim($_GET['status'] ?? '');
$page   = max(1, (int)($_GET['p'] ?? 1));
$per    = 9;
$offset = ($page - 1) * $per;

// --------- WHERE dinâmico ---------
$where = [];
$args  = [];

if (!$isAd) {
  $where[] = 'user_id = ?';
  $args[]  = $meId;
}
if ($q !== '') {
  $where[] = '(title LIKE ? OR description LIKE ?)';
  $args[]  = '%'.$q.'%';
  $args[]  = '%'.$q.'%';
}
if ($st !== '') {
  $enum = label_to_enum($st);
  if ($enum !== '') {
    $where[] = 'status = ?';
    $args[]  = $enum;
  }
}

$whereSql = $where ? ('WHERE '.implode(' AND ', $where)) : '';

// --------- Total + paginação ---------
$stmt = $pdo->prepare("SELECT COUNT(*) AS c FROM budgets $whereSql");
$stmt->execute($args);
$total = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("
  SELECT id, user_id, title, description, weight_g, time_hours, status, total_price, created_at
  FROM budgets
  $whereSql
  ORDER BY created_at DESC, id DESC
  LIMIT $per OFFSET $offset
");
$stmt->execute($args);
$rows = $stmt->fetchAll();

// Thumb por orçamento
$ids = array_column($rows, 'id');
$thumbs = [];
if ($ids) {
  $in  = implode(',', array_fill(0, count($ids), '?'));
  $stT = $pdo->prepare("
    SELECT bf.budget_id, bf.path
    FROM budget_files bf
    JOIN (
      SELECT budget_id, MIN(id) AS min_id
      FROM budget_files
      WHERE budget_id IN ($in)
      GROUP BY budget_id
    ) x ON x.budget_id = bf.budget_id AND x.min_id = bf.id
  ");
  $stT->execute($ids);
  foreach ($stT as $t) $thumbs[(int)$t['budget_id']] = $t['path'];
}

$pages = (int)ceil(max(1, $total) / $per);

include __DIR__.'/includes/header.php';
?>

<div class="container py-4">
  <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
    <h1 class="h3 m-0">Meus Orçamentos</h1>
    <a class="btn btn-success ms-auto" href="novo-orcamento.php">Pedir orçamento</a>
  </div>

  <form class="row g-2 mb-3" method="get" action="">
    <div class="col-md-6">
      <input type="text" name="q" value="<?=h($q)?>" class="form-control" placeholder="Buscar por título ou descrição…">
    </div>
    <div class="col-md-3">
      <select name="status" class="form-select">
        <option value="">— Todos os status —</option>
        <?php foreach (['Recebido','Em análise','Aprovado','Em produção','Concluído','Cancelado'] as $opt): ?>
          <option value="<?=h($opt)?>" <?=($opt===$st?'selected':'')?>><?=h($opt)?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3 d-grid d-md-block">
      <button class="btn btn-primary">Filtrar</button>
      <?php if ($q!=='' || $st!==''): ?>
        <a class="btn btn-outline-secondary ms-1" href="meus-orcamentos.php">Limpar</a>
      <?php endif; ?>
    </div>
  </form>

  <?php if (!$rows): ?>
    <div class="alert alert-info">
      Nenhum orçamento encontrado.
      <a href="novo-orcamento.php" class="alert-link">Criar novo orçamento</a>.
    </div>
  <?php else: ?>
    <div class="row g-3">
      <?php foreach ($rows as $r): ?>
        <?php
          $thumb = $thumbs[(int)$r['id']] ?? null;
          $label = enum_to_label($r['status']);
          $badge = status_badge_class($r['status']);
          $price = $r['total_price'] !== null ? 'R$ '.number_format((float)$r['total_price'], 2, ',', '.') : '—';
        ?>
        <div class="col-12 col-sm-6 col-lg-4">
          <div class="card h-100 shadow-sm">
            <?php if ($thumb): ?>
              <a href="meus-orcamentos-view.php?id=<?=h($r['id'])?>">
                <img src="<?=h($thumb)?>" class="card-img-top" alt="thumb" style="aspect-ratio:16/9; object-fit:cover;">
              </a>
            <?php endif; ?>
            <div class="card-body d-flex flex-column">
              <h2 class="h6 card-title mb-1">
                <a class="stretched-link text-decoration-none" href="meus-orcamentos-view.php?id=<?=h($r['id'])?>">
                  <?=h($r['title'] ?: 'Orçamento #'.$r['id'])?>
                </a>
              </h2>
              <div class="mb-2">
                <span class="badge <?=$badge?>"><?=h($label)?></span>
              </div>
              <div class="small text-muted mb-2">
                Criado em <?=h(date('d/m/Y H:i', strtotime($r['created_at'] ?? 'now')));?>
              </div>
              <div class="mt-auto d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><?=$price?></span>
                <span class="text-muted small">
                  <?=h((int)$r['weight_g'])?> g · <?=h(hm_from_decimal($r['time_hours']))?>
                </span>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <?php if ($pages > 1): ?>
      <nav class="mt-4">
        <ul class="pagination">
          <?php
            $base = 'meus-orcamentos.php?'.http_build_query(array_filter(['q'=>$q,'status'=>$st]));
          ?>
          <li class="page-item <?=($page<=1?'disabled':'')?>">
            <a class="page-link" href="<?=$base.($base?'&':'').'p='.max(1,$page-1)?>" aria-label="Anterior">&laquo;</a>
          </li>
          <?php for ($i=max(1,$page-2); $i<=min($pages,$page+2); $i++): ?>
            <li class="page-item <?=($i===$page?'active':'')?>"><a class="page-link" href="<?=$base.($base?'&':'').'p='.$i?>"><?=$i?></a></li>
          <?php endfor; ?>
          <li class="page-item <?=($page>=$pages?'disabled':'')?>">
            <a class="page-link" href="<?=$base.($base?'&':'').'p='.min($pages,$page+1)?>" aria-label="Próximo">&raquo;</a>
          </li>
        </ul>
      </nav>
    <?php endif; ?>
  <?php endif; ?>
</div>

<?php include __DIR__.'/includes/footer.php'; ?>
