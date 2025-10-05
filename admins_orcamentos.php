<?php
// admins_orcamentos.php — Painel do administrador
require __DIR__ . '/config.php';
require_login();

// === garante que só admin acesse ===
if (!is_admin()) {
  http_response_code(403);
  exit('Acesso restrito.');
}

$pdo = db();

// ---------- Filtros ----------
$q       = trim($_GET['q'] ?? '');
$status  = trim($_GET['status'] ?? '');
$calcst  = trim($_GET['calc_state'] ?? '');
$df      = trim($_GET['df'] ?? '');
$dt      = trim($_GET['dt'] ?? '');
$page    = max(1, (int)($_GET['p'] ?? 1));
$per     = 15;
$off     = ($page - 1) * $per;

$where = []; $args = [];
if ($q !== ''){
  $where[] = '(u.name LIKE ? OR u.email LIKE ? OR b.title LIKE ? OR b.description LIKE ?)';
  $args[] = "%$q%"; $args[] = "%$q%"; $args[] = "%$q%"; $args[] = "%$q%";
}
if ($status !== ''){
  $where[] = 'b.status = ?'; $args[] = $status;
}
if ($calcst !== ''){
  $where[] = 'b.calc_state = ?'; $args[] = $calcst;
}
if ($df !== ''){
  $where[] = 'DATE(b.created_at) >= ?'; $args[] = $df;
}
if ($dt !== ''){
  $where[] = 'DATE(b.created_at) <= ?'; $args[] = $dt;
}
$wsql = $where ? ('WHERE '.implode(' AND ', $where)) : '';

// ---------- Total / Paginação ----------
$st = $pdo->prepare("SELECT COUNT(*) FROM budgets b JOIN users u ON u.id=b.user_id $wsql");
$st->execute($args);
$total = (int)$st->fetchColumn();
$pages = max(1, (int)ceil($total/$per));

// ---------- Listagem ----------
$sql = "
  SELECT b.id,b.user_id,b.title,b.description,b.weight_g,b.time_hours,b.total_price,
         b.status,b.calc_state,b.created_at,
         u.name AS user_name, u.email AS user_email
  FROM budgets b
  JOIN users u ON u.id=b.user_id
  $wsql
  ORDER BY b.created_at DESC, b.id DESC
  LIMIT $per OFFSET $off
";
$st = $pdo->prepare($sql);
$st->execute($args);
$rows = $st->fetchAll();

// ---------- Helpers ----------
function enum_to_label($e){
  return [
    'RECEBIDO'=>'Recebido','EM_ANALISE'=>'Em análise','APROVADO'=>'Aprovado',
    'EM_PRODUCAO'=>'Em produção','CONCLUIDO'=>'Concluído','CANCELADO'=>'Cancelado'
  ][$e] ?? $e;
}
function calc_label($c){
  return ['PENDENTE'=>'Pendente','ESTIMADO'=>'Estimado','FINAL'=>'Final'][$c] ?? $c;
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

$title = 'Painel — Orçamentos';
include __DIR__ . '/includes/header.php';
?>
<div class="d-flex align-items-center gap-2 mb-3">
  <h1 class="h3 m-0">Painel — Orçamentos</h1>
  <span class="badge bg-dark">Admin</span>
</div>

<form class="row g-2 mb-3" method="get">
  <div class="col-md-3">
    <input class="form-control" name="q" value="<?=h($q)?>" placeholder="Cliente, e-mail, título ou descrição">
  </div>
  <div class="col-md-2">
    <select name="status" class="form-select">
      <option value="">— Todos status —</option>
      <?php foreach (['RECEBIDO','EM_ANALISE','APROVADO','EM_PRODUCAO','CONCLUIDO','CANCELADO'] as $opt): ?>
        <option value="<?=$opt?>" <?=$status===$opt?'selected':''?>><?=h(enum_to_label($opt))?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-2">
    <select name="calc_state" class="form-select">
      <option value="">— Cálculo —</option>
      <?php foreach (['PENDENTE','ESTIMADO','FINAL'] as $opt): ?>
        <option value="<?=$opt?>" <?=$calcst===$opt?'selected':''?>><?=h(calc_label($opt))?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-2"><input type="date" class="form-control" name="df" value="<?=h($df)?>"></div>
  <div class="col-md-2"><input type="date" class="form-control" name="dt" value="<?=h($dt)?>"></div>
  <div class="col-md-1 d-grid"><button class="btn btn-primary">Filtrar</button></div>
</form>

<?php if (!$rows): ?>
  <div class="alert alert-info">Nenhum orçamento encontrado para os filtros aplicados.</div>
<?php else: ?>
  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>Quando</th>
          <th>Cliente</th>
          <th>Título</th>
          <th>Peso / Tempo</th>
          <th>Status</th>
          <th>Cálculo</th>
          <th class="text-end">Preço</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td>#<?=h($r['id'])?></td>
          <td><?=h(date('d/m/Y H:i', strtotime($r['created_at'])))?></td>
          <td>
            <div class="fw-semibold"><?=h($r['user_name'])?></div>
            <div class="small text-muted"><?=h($r['user_email'])?></div>
          </td>
          <td>
            <div class="fw-semibold"><?=h($r['title'] ?: 'Orçamento #'.$r['id'])?></div>
            <?php if (!empty($r['description'])): ?>
              <div class="text-muted small" style="max-width:420px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                <?=h($r['description'])?>
              </div>
            <?php endif; ?>
          </td>

          <td>
            <div><strong><?= (int)$r['weight_g'] ?> g</strong></div>
            <div class="text-muted small"><?= h(hm_from_decimal($r['time_hours'])) ?></div>
          </td>

          <td style="min-width:210px">
            <form class="d-flex gap-2 align-items-center" method="post" action="<?= h(BASE_URL) ?>admins_orcamento_status.php">
              <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
              <input type="hidden" name="id" value="<?=h($r['id'])?>">
              <select name="status" class="form-select form-select-sm">
                <?php foreach (['RECEBIDO','EM_ANALISE','APROVADO','EM_PRODUCAO','CONCLUIDO','CANCELADO'] as $opt): ?>
                  <option value="<?=$opt?>" <?=$r['status']===$opt?'selected':''?>><?=h(enum_to_label($opt))?></option>
                <?php endforeach; ?>
              </select>
              <button class="btn btn-sm btn-outline-primary">Salvar</button>
            </form>
          </td>

          <td style="min-width:180px">
            <form class="d-flex gap-2 align-items-center" method="post" action="<?= h(BASE_URL) ?>admins_orcamento_status.php">
              <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
              <input type="hidden" name="id" value="<?=h($r['id'])?>">
              <input type="hidden" name="only_calc" value="1">
              <select name="calc_state" class="form-select form-select-sm">
                <?php foreach (['PENDENTE','ESTIMADO','FINAL'] as $opt): ?>
                  <option value="<?=$opt?>" <?=$r['calc_state']===$opt?'selected':''?>><?=h(calc_label($opt))?></option>
                <?php endforeach; ?>
              </select>
              <button class="btn btn-sm btn-outline-secondary">OK</button>
            </form>
          </td>

          <td class="text-end">
            <?= $r['total_price']!==null ? 'R$ '.number_format((float)$r['total_price'],2,',','.') : '—' ?>
          </td>

          <td>
            <div class="btn-group btn-group-sm">
              <a class="btn btn-outline-dark" target="_blank" href="<?= h(BASE_URL) ?>meus-orcamentos-view.php?id=<?=h($r['id'])?>">Ver</a>
              <a class="btn btn-outline-success" href="<?= h(BASE_URL) ?>calculadora.php?for_budget=<?=h($r['id'])?>&return=<?= urlencode('admins_orcamentos.php?'.http_build_query($_GET)) ?>">Calcular</a>
              <form method="post" action="<?= h(BASE_URL) ?>pedido-deletar.php"
                    onsubmit="return confirm('Excluir orçamento #<?=h($r['id'])?>? Esta ação não pode ser desfeita.');">
                <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
                <input type="hidden" name="id" value="<?= h($r['id']) ?>">
                <button class="btn btn-outline-danger">Excluir</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <?php if ($pages>1): ?>
    <nav>
      <ul class="pagination">
        <?php $base = 'admins_orcamentos.php?'.http_build_query(array_filter([
          'q'=>$q,'status'=>$status,'calc_state'=>$calcst,'df'=>$df,'dt'=>$dt
        ])); ?>
        <li class="page-item <?=$page<=1?'disabled':''?>">
          <a class="page-link" href="<?=$base.($base?'&':'').'p='.max(1,$page-1)?>">&laquo;</a>
        </li>
        <?php for($i=max(1,$page-2);$i<=min($pages,$page+2);$i++): ?>
          <li class="page-item <?=$i===$page?'active':''?>">
            <a class="page-link" href="<?=$base.($base?'&':'').'p='.$i?>"><?=$i?></a>
          </li>
        <?php endfor; ?>
        <li class="page-item <?=$page>=$pages?'disabled':''?>">
          <a class="page-link" href="<?=$base.($base?'&':'').'p='.min($pages,$page+1)?>">&raquo;</a>
        </li>
      </ul>
    </nav>
  <?php endif; ?>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php';
