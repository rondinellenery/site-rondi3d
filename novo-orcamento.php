<?php
require __DIR__.'/config.php';
require_login();

$title = "Novo Orçamento";
$err = []; $ok=false;

// ===== POST =====
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $desc      = trim($_POST['description'] ?? '');
  $material  = trim($_POST['material'] ?? 'Indefinido');

  // dados vindos da calculadora (podem estar ausentes)
  $weight_g  = isset($_POST['weight_g'])       && $_POST['weight_g']       !== '' ? (float)$_POST['weight_g']       : 0;
  $time_h    = isset($_POST['time_h_decimal']) && $_POST['time_h_decimal'] !== '' ? (float)$_POST['time_h_decimal'] : 0;
  $price     = isset($_POST['price_final'])    && $_POST['price_final']    !== '' ? (float)$_POST['price_final']    : 0.0;
  $breakdown = $_POST['breakdown_json'] ?? null;

  // Se vier cálculo válido, marcamos como ESTIMADO; senão, PENDENTE
  $hasCalc    = ($weight_g > 0 && $time_h > 0 && $price > 0);
  $calc_state = $hasCalc ? 'ESTIMADO' : 'PENDENTE';

  if (mb_strlen($desc) < 10) $err[]='Descreva melhor seu projeto (mín. 10 caracteres).';

  // snapshot (só salvamos breakdown/material se existir algo)
  $snapshot = [
    'material'  => $material,
    'breakdown' => $breakdown ? json_decode($breakdown, true) : null,
    'saved_at'  => date('c'),
  ];
  $snapshot_json = $hasCalc ? json_encode($snapshot, JSON_UNESCAPED_UNICODE) : null;

  // upload opcional
  $publicPath = null; $mime = null; $origName = null; $sizeBytes = 0;
  if (!empty($_FILES['photo']['name'])) {
    $f = $_FILES['photo'];
    if ($f['error'] !== UPLOAD_ERR_OK) {
      $err[] = 'Erro no upload (código '.$f['error'].').';
    } elseif ($f['size'] > UPLOAD_MAX_BYTES) {
      $err[] = 'Imagem acima de 20MB.';
    } else {
      $fi   = new finfo(FILEINFO_MIME_TYPE);
      $mime = $fi->file($f['tmp_name']) ?: '';
      $ext  = match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        default      => null,
      };
      if (!$ext) {
        $err[] = 'Somente JPG, PNG ou WEBP.';
      } else {
        $subdir   = date('Y').'/'.date('m');
        $destDir  = rtrim(UPLOAD_DIR, '/\\').DIRECTORY_SEPARATOR.$subdir;
        if (!is_dir($destDir)) mkdir($destDir, 0775, true);
        $safeBase = preg_replace('/[^a-zA-Z0-9_\-\.]/','_', pathinfo($f['name'], PATHINFO_FILENAME)) ?: 'upload';
        $newName  = $safeBase.'_'.bin2hex(random_bytes(5)).'.'.$ext;
        $destPath = $destDir.DIRECTORY_SEPARATOR.$newName;
        if (!move_uploaded_file($f['tmp_name'], $destPath)) {
          $err[] = 'Falha ao salvar a imagem.';
        } else {
          $publicPath = rtrim(UPLOAD_URL,'/').'/'.$subdir.'/'.$newName;
          $origName   = $f['name'];
          $sizeBytes  = (int)$f['size'];
        }
      }
    }
  }

  if (!$err) {
    $pdo = db(); 
    $pdo->beginTransaction();

    // IMPORTANTE: usar os campos corretos e created_at
    $stmt = $pdo->prepare("
      INSERT INTO budgets
        (user_id, title, description, weight_g, time_hours, status, total_price, calc_snapshot, calc_state, created_at)
      VALUES
        (?,       ?,     ?,           ?,        ?,         'RECEBIDO', ?,           ?,             ?,          NOW())
    ");
    $titleEst = trim($_POST['title'] ?? ''); 
    if ($titleEst==='') $titleEst = 'Orçamento';

    $stmt->execute([
      current_user()['id'],
      $titleEst,
      $desc,
      $hasCalc ? $weight_g : 0,                 // se não tem cálculo, zera
      $hasCalc ? $time_h   : 0,
      $hasCalc ? $price    : null,              // sem cálculo, deixa NULL
      $snapshot_json,                           // só vem se $hasCalc
      $calc_state
    ]);
    $budgetId = (int)$pdo->lastInsertId();

    if ($publicPath) {
      $stmtF = $pdo->prepare("
        INSERT INTO budget_files (budget_id, path, filename, mime, size_bytes)
        VALUES (?, ?, ?, ?, ?)
      ");
      $stmtF->execute([$budgetId, $publicPath, $origName, $mime, $sizeBytes]);
    }

    $pdo->commit();

    if ($hasCalc) {
      // já tem cálculo -> vai para o detalhe
      header('Location: '.BASE_URL.'meus-orcamentos-view.php?id='.$budgetId);
      exit;
    } else {
      // sem cálculo -> leva para a calculadora vinculada com opção de cancelar
      $return = BASE_URL.'meus-orcamentos-view.php?id='.$budgetId;
      header('Location: '.BASE_URL.'calculadora.php?for_budget='.$budgetId.'&return='.rawurlencode($return));
      exit;
    }
  }
}

include __DIR__.'/includes/header.php';
?>
<h1 class="h3 mb-3">Novo Orçamento</h1>

<?php if ($err): ?>
  <div class="alert alert-danger" role="alert">
    <ul class="mb-0"><?php foreach($err as $e) echo '<li>'.h($e).'</li>';?></ul>
  </div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="row g-3" id="formQuote" style="display:grid; gap:12px 16px; grid-template-columns: 1fr 1fr;">
  <div>
    <label class="form-label">Título (opcional)</label>
    <input class="form-control" name="title" placeholder="Ex.: Peça sob medida">
  </div>
  <div>
    <label class="form-label">Material desejado</label>
    <select class="form-select" name="material">
      <?php foreach (['Indefinido','PLA','PETG','Resina'] as $m) echo '<option>'.h($m).'</option>'; ?>
    </select>
  </div>

  <div style="grid-column:1/-1">
    <label class="form-label">Descrição do projeto</label>
    <textarea class="form-control" name="description" rows="4" placeholder="Dimensões, cores, acabamento, prazo… mínimo 10 caracteres"></textarea>
  </div>

  <div>
    <label class="form-label">Imagem (JPG/PNG/WEBP até 20MB)</label>
    <input type="file" class="form-control" name="photo" accept="image/jpeg,image/png,image/webp">
  </div>

  <!-- hidden vindos da calculadora (podem não existir) -->
  <input type="hidden" name="weight_g" id="h_weight">
  <input type="hidden" name="time_h_decimal" id="h_time">
  <input type="hidden" name="price_final" id="h_price">
  <input type="hidden" name="breakdown_json" id="h_breakdown">

  <div style="grid-column:1/-1">
    <button class="btn btn-success">Enviar orçamento</button>
    <a class="btn btn-link" href="<?= h(BASE_URL) ?>calculadora.php?usecalc=1">Usar a Calculadora</a>
  </div>
</form>

<script>
(function(){
  // Se vier da calculadora, carregamos o cálculo do sessionStorage
  const params  = new URLSearchParams(location.search);
  const usecalc = params.get('usecalc') === '1';

  const MAX_AGE_MS = 30 * 60 * 1000; // 30min

  const H = {
    w: document.getElementById('h_weight'),
    t: document.getElementById('h_time'),
    p: document.getElementById('h_price'),
    b: document.getElementById('h_breakdown'),
  };
  H.w.value = H.t.value = H.p.value = '';
  H.b.value = '';

  if (usecalc) {
    const raw = sessionStorage.getItem('calc_result');
    if (raw) {
      try {
        const r = JSON.parse(raw);
        const fresh = r && r.ts && (Date.now() - r.ts) <= MAX_AGE_MS;

        if (fresh && r.peso > 0 && r.t > 0 && r.finalPrice > 0) {
          H.w.value = r.peso;
          H.t.value = r.t;
          H.p.value = r.finalPrice;
          H.b.value = JSON.stringify(r.breakdown || {});
        }
      } catch(e){}
    }
    // limpa para não vazar para próximos orçamentos
    sessionStorage.removeItem('calc_result');
  }
})();
</script>

<?php include __DIR__.'/includes/footer.php'; ?>
