<?php
// /post-novo.php — criar post/galeria (admin)
require __DIR__.'/config.php';
require_login();
require_admin();

$err=[];

if ($_SERVER['REQUEST_METHOD']==='POST'){
  require_csrf();

  $title     = trim($_POST['title'] ?? '');
  $slug      = trim($_POST['slug'] ?? '');
  $excerpt   = trim($_POST['excerpt'] ?? '');
  $body      = trim($_POST['body'] ?? '');
  $thumb     = trim($_POST['thumb_url'] ?? '');
  $published = isset($_POST['published']) ? 1 : 0;

  // NOVO: tipo e ficha técnica (para Galeria)
  $type       = ($_POST['type'] ?? 'post') === 'gallery' ? 'gallery' : 'post';
  $painter    = trim($_POST['painter'] ?? '');
  $piece_size = trim($_POST['piece_size'] ?? '');
  $unit_price = $_POST['unit_price'] !== '' ? (float)$_POST['unit_price'] : null;

  if ($title==='') $err[]='Informe o título.';

  // slug automático
  if ($slug===''){
    $slug = strtolower(preg_replace('/[^a-z0-9\-]+/','-', iconv('UTF-8','ASCII//TRANSLIT',$title)));
    $slug = trim($slug,'-');
  }

  // Validações da galeria
  if ($type==='gallery') {
    // Nenhum campo é estritamente obrigatório, mas vamos limitar a 10 imagens
    if (!empty($_FILES['images']['name'][0])) {
      $qt = 0;
      foreach ((array)$_FILES['images']['name'] as $n) if ($n!=='') $qt++;
      if ($qt > 10) $err[] = 'Envie no máximo 10 imagens.';
    }
  }

  if (!$err){
    $pdo = db();

    // Insere o post com os novos campos
    $st = $pdo->prepare("
      INSERT INTO posts (slug, title, thumb_url, excerpt, body, published, type, painter, piece_size, unit_price, created_at)
      VALUES (?,?,?,?,?,?,?,?,?,?, NOW())
    ");

    try{
      $st->execute([$slug,$title,$thumb,$excerpt,$body,$published,$type,$painter,$piece_size,$unit_price]);
      $postId = (int)$pdo->lastInsertId();

      // Upload MÚLTIPLO (apenas se for galeria)
      if ($type==='gallery' && !empty($_FILES['images']['name'][0])) {
        $fi   = new finfo(FILEINFO_MIME_TYPE);
        $ok   = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
        $sub  = 'gallery/'.date('Y').'/'.date('m');
        $baseDir = rtrim(UPLOAD_DIR,'/\\').DIRECTORY_SEPARATOR.$sub;
        if (!is_dir($baseDir)) @mkdir($baseDir, 0775, true);

        $sort = 1;
        for ($i=0; $i<count($_FILES['images']['name']); $i++){
          if ($_FILES['images']['error'][$i]!==UPLOAD_ERR_OK || $_FILES['images']['name'][$i]==='') continue;

          // tamanho
          if ($_FILES['images']['size'][$i] > UPLOAD_MAX_BYTES){
            $err[] = 'Uma das imagens excede o limite configurado.';
            continue;
          }

          // mime
          $mime = $fi->file($_FILES['images']['tmp_name'][$i]) ?: '';
          if (!isset($ok[$mime])) { $err[]='Uma das imagens não é JPG/PNG/WEBP.'; continue; }

          $ext  = $ok[$mime];
          $name = pathinfo($_FILES['images']['name'][$i], PATHINFO_FILENAME);
          $name = preg_replace('/[^a-zA-Z0-9_\-]/','_', $name) ?: 'img';
          $dest = $baseDir.DIRECTORY_SEPARATOR.$name.'_'.bin2hex(random_bytes(5)).'.'.$ext;

          if (!move_uploaded_file($_FILES['images']['tmp_name'][$i], $dest)){
            $err[]='Falha ao salvar uma das imagens.';
            continue;
          }
          $url = rtrim(UPLOAD_URL,'/').'/'.$sub.'/'.basename($dest);

          $pdo->prepare("INSERT INTO post_images (post_id, path, sort, created_at) VALUES (?,?,?,NOW())")
              ->execute([$postId, $url, $sort++]);
        }
      }

      if (!$err){
        header('Location: '.BASE_URL.'admin_posts.php');
        exit;
      }
    }catch(Throwable $e){
      $err[] = 'Falha ao salvar (slug pode já existir).';
    }
  }
}

$title = 'Novo post';
include __DIR__.'/includes/header.php';
?>
<h1 class="h3 mb-3">Novo post</h1>

<?php if ($err): ?>
  <div class="alert alert-danger"><ul class="mb-0"><?php foreach($err as $e) echo '<li>'.h($e).'</li>';?></ul></div>
<?php endif; ?>

<form method="post" class="row g-3" enctype="multipart/form-data">
  <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">

  <div class="col-md-4">
    <label class="form-label">Tipo</label>
    <select name="type" class="form-select" id="typeSelect">
      <option value="post">Artigo</option>
      <option value="gallery">Galeria</option>
    </select>
  </div>

  <div class="col-md-8">
    <label class="form-label">Título</label>
    <input name="title" class="form-control" required>
  </div>

  <div class="col-md-4">
    <label class="form-label">Slug (URL)</label>
    <input name="slug" class="form-control" placeholder="deixe vazio para gerar">
  </div>

  <div class="col-md-8">
    <label class="form-label">Imagem de destaque (URL)</label>
    <input name="thumb_url" class="form-control" placeholder="https://… (opcional)">
  </div>

  <div class="col-12">
    <label class="form-label">Resumo/Excerpt (opcional)</label>
    <textarea name="excerpt" class="form-control" rows="3"></textarea>
  </div>

  <div class="col-12">
    <label class="form-label">Conteúdo</label>
    <textarea name="body" class="form-control" rows="12" required></textarea>
  </div>

  <!-- Ficha técnica (mostre sempre; o front decide usar só quando type=gallery) -->
  <div class="col-md-4">
    <label class="form-label">Quem pintou</label>
    <input name="painter" class="form-control" placeholder="ex.: Kaylane Soares">
  </div>
  <div class="col-md-4">
    <label class="form-label">Tamanho da peça</label>
    <input name="piece_size" class="form-control" placeholder="ex.: 14 cm (altura)">
  </div>
  <div class="col-md-4">
    <label class="form-label">Valor cobrado (unidade)</label>
    <input name="unit_price" class="form-control" type="number" step="0.01" placeholder="ex.: 79.90">
  </div>

  <div class="col-12">
    <label class="form-label">Imagens do projeto (até 10)</label>
    <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
    <div class="form-text">JPG/PNG/WEBP, até <?= (int)(UPLOAD_MAX_BYTES/1048576) ?>MB por arquivo.</div>
  </div>

  <div class="col-12 d-flex align-items-center gap-3">
    <div class="form-check">
      <input class="form-check-input" type="checkbox" id="pub" name="published" checked>
      <label for="pub" class="form-check-label">Publicar</label>
    </div>
    <button class="btn btn-success ms-auto">Salvar</button>
    <a class="btn btn-outline-secondary" href="<?= h(BASE_URL) ?>admin_posts.php">Cancelar</a>
  </div>
</form>

<?php include __DIR__.'/includes/footer.php';
