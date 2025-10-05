<?php
require __DIR__.'/config.php';
require_login();
$title = "Novo Orçamento";
$err = []; $ok=false;

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $desc = trim($_POST['description'] ?? '');
  $material = $_POST['material'] ?? 'Indefinido';
  $weight_g = (float)($_POST['weight_g'] ?? 0);
  $time_h   = (float)($_POST['time_h_decimal'] ?? 0);
  $price    = (float)($_POST['price_final'] ?? 0);
  $breakdown= $_POST['breakdown_json'] ?? null;

  if ($weight_g<=0 || $time_h<=0 || $price<=0) $err[]='Recalcule na Calculadora antes de enviar.';
  if (mb_strlen($desc) < 10) $err[]='Descreva melhor seu projeto (mín. 10 caracteres).';

  $pathSaved = null; $mime = null;
  if (!empty($_FILES['photo']['name'])) {
    $f = $_FILES['photo'];
    if ($f['error']===UPLOAD_ERR_OK) {
      if ($f['size'] > 20*1024*1024) $err[]='Imagem acima de 20MB.';
      $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
      $mime = @mime_content_type($f['tmp_name']);
      if (!isset($allowed[$mime])) $err[]='Somente JPG, PNG ou WEBP.';
      if (!$err) {
        if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0775, true);
        $new = uniqid('img_').'.'.$allowed[$mime];
        $dest = UPLOAD_DIR.'/'.$new;
        if (!move_uploaded_file($f['tmp_name'], $dest)) $err[]='Falha ao salvar imagem.';
        else $pathSaved = 'storage/uploads/'.$new;
      }
    } else $err[]='Erro no upload (código '.$f['error'].').';
  }

  if (!$err) {
    $stmt = db()->prepare("INSERT INTO estimates (user_id,title,description,material,weight_g,time_h_decimal,price_final,breakdown_json,status)
                           VALUES (?,?,?,?,?,?,?,?, 'Recebido')");
    $titleEst = $_POST['title'] ?? null;
    $stmt->execute([ current_user()['id'], $titleEst, $desc, $material, $weight_g, $time_h, $price, $breakdown ]);
    $estId = db()->lastInsertId();

    if ($pathSaved) {
      db()->prepare("INSERT INTO files (estimate_id, path, original_name, mime, size_bytes) VALUES (?,?,?,?,?)")
        ->execute([$estId, $pathSaved, $_FILES['photo']['name'], $mime, (int)$_FILES['photo']['size']]);
    }
    $ok = true;
  }
}

include __DIR__.'/includes/header.php';
?>
<h1 class="h3 mb-3">Novo Orçamento</h1>

<?php if ($ok): ?>
  <div class="alert alert-success">Recebemos seu orçamento! Acompanhe em <a href="meus-orcamentos.php">Meus Orçamentos</a>.</div>
<?php endif; ?>

<?php if ($err): ?>
  <div class="alert alert-danger"><ul class="mb-0"><?php foreach($err as $e) echo '<li>'.h($e).'</li>';?></ul></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="row g-3" id="formQuote">
  <div class="col-md-6">
    <label class="form-label">Título (opcional)</label>
    <input class="form-control" name="title" placeholder="Ex.: Insert para Catan">
  </div>
  <div class="col-md-6">
    <label class="form-label">Material desejado</label>
    <select class="form-select" name="material">
      <?php foreach (['Indefinido','PLA','PETG','Resina'] as $m) echo '<option>'.$m.'</option>'; ?>
    </select>
  </div>
  <div class="col-12">
    <label class="form-label">Descrição do projeto</label>
    <textarea class="form-control" name="description" rows="4" placeholder="Dimensões aproximadas, cores, acabamento, prazo…"></textarea>
  </div>
  <div class="col-md-6">
    <label class="form-label">Imagem (JPG/PNG/WEBP até 20MB)</label>
    <input type="file" class="form-control" name="photo" accept="image/jpeg,image/png,image/webp">
  </div>

  <!-- hidden com resultado da calculadora -->
  <input type="hidden" name="weight_g" id="h_weight">
  <input type="hidden" name="time_h_decimal" id="h_time">
  <input type="hidden" name="price_final" id="h_price">
  <input type="hidden" name="breakdown_json" id="h_breakdown">

  <div class="col-12">
    <button class="btn btn-success">Enviar orçamento</button>
    <a class="btn btn-link" href="calculadora.php">Voltar à Calculadora</a>
  </div>
</form>

<script>
// Recupera resultado salvo na calculadora
const r = JSON.parse(sessionStorage.getItem('calc_result') || 'null');
if (r){
  document.getElementById('h_weight').value = r.peso ?? 0;
  document.getElementById('h_time').value   = r.t ?? 0;
  document.getElementById('h_price').value  = r.finalPrice ?? 0;
  document.getElementById('h_breakdown').value = JSON.stringify(r);
} else {
  document.getElementById('formQuote').addEventListener('submit', e=>{
    e.preventDefault(); alert("Calcule na página Calculadora 3D antes de enviar.");
  });
}
</script>

<?php include __DIR__.'/includes/footer.php'; ?>
