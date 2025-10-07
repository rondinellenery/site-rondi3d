<?php
// /post-editar.php — editar post/galeria (admin)
require __DIR__.'/config.php';
require_login();
require_admin();

$id  = (int)($_GET['id'] ?? 0);
$pdo = db();

$st = $pdo->prepare("SELECT * FROM posts WHERE id=?");
$st->execute([$id]);
$post = $st->fetch();
if (!$post){ http_response_code(404); exit('Post não encontrado'); }

// carrega imagens já cadastradas (se for galeria)
$imgs = $pdo->prepare("SELECT id, path, sort FROM post_images WHERE post_id=? ORDER BY sort,id");
$imgs->execute([$id]);
$imgs = $imgs->fetchAll();

$err=[];
if ($_SERVER['REQUEST_METHOD']==='POST'){
  require_csrf();

  $title     = trim($_POST['title'] ?? '');
  $slug      = trim($_POST['slug'] ?? '');
  $excerpt   = trim($_POST['excerpt'] ?? '');
  $body      = trim($_POST['body'] ?? '');
  $thumb     = trim($_POST['thumb_url'] ?? '');
  $published = isset($_POST['published']) ? 1 : 0;

  $type       = ($_POST['type'] ?? 'post') === 'gallery' ? 'gallery' : 'post';
  $painter    = trim($_POST['painter'] ?? '');
  $piece_size = trim($_POST['piece_size'] ?? '');
  $unit_price = $_POST['unit_price'] !== '' ? (float)$_POST['unit_price'] : null;

  if ($title==='') $err[]='Informe o título.';
  if ($slug===''){
    $slug = strtolower(preg_replace('/[^a-z0-9\-]+/','-', iconv('UTF-8','ASCII//TRANSLIT',$title)));
    $slug = trim($slug,'-');
  }

  if (!$err){
    try{
      $st = $pdo->prepare("
        UPDATE posts
           SET slug=?, title=?, excerpt=?, body=?, thumb_url=?, published=?,
               type=?, painter=?, piece_size=?, unit_price=?
         WHERE id=?
      ");
      $st->execute([$slug,$title,$excerpt,$body,$thumb,$published,$type,$painter,$piece_size,$unit_price,$id]);

      // Remover imagens marcadas
      if (!empty($_POST['delimg']) && is_array($_POST['delimg'])) {
        $idsDel = array_map('intval', array_keys($_POST['delimg']));
        if ($idsDel){
          // (opcional) apagar arquivos físicos é possível se o caminho for local
          $q = 'DELETE FROM post_images WHERE post_id=? AND id IN ('.implode(',', $idsDel).')';
          $pdo->prepare($q)->execute([$id]);
        }
      }

      // Adicionar novas imagens (máx 10 no total; aqui vamos apenas anexar)
      if (!empty($_FILES['images']['name'][0])) {
        $fi   = new finfo(FILEINFO_MIME_TYPE);
        $ok   = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
        $sub  = 'gallery/'.date('Y').'/'.date('m');
        $baseDir = rtrim(UPLOAD_DIR,'/\\').DIRECTORY_SEPARATOR.$sub;
        if (!is_dir($baseDir)) @mkdir($baseDir, 0775, true);

        // pega sort atual máximo
        $nextSort = (int)$pdo->query("SELECT COALESCE(MAX(sort),0)+1 FROM post_images WHERE post_id=".$id)->fetchColumn();

        for ($i=0; $i<count($_FILES['images']['name']); $i++){
          if ($_FILES['images']['error'][$i]!==UPLOAD_ERR_OK || $_FILES['images']['name'][$i]==='') continue;
          if ($_FILES['images']['size'][$i] > UPLOAD_MAX_BYTES) continue;

          $mime = $fi->file($_FILES['images']['tmp_name'][$i]) ?: '';
          if (!isset($ok[$mime])) continue;

          $ext  = $ok[$mime];
          $name = pathinfo($_FILES['images']['name'][$i], PATHINFO_FILENAME);
          $name = preg_replace('/[^a-zA-Z0-9_\-]/','_', $name) ?: 'img';
          $dest = $baseDir.DIRECTORY_SEPARATOR.$name.'_'.bin2hex(random_bytes(5)).'.'.$ext;

          if (!move_uploaded_file($_FILES['images']['tmp_name'][$i], $dest)) continue;

          $url = rtrim(UPLOAD_URL,'/').'/'.$sub.'/'.basename($dest);
          $pdo->prepare("INSERT INTO post_images (post_id, path, sort, created_at) VALUES (?,?,?,NOW())")
              ->execute([$id, $url, $nextSort++]);
        }
      }

      header('Location: '.BASE_URL.'admin_posts.php');
      exit;
    }catch(Throwable $e){
      $err[]='Falha ao atualizar (slug pode estar em uso).';
    }
  }
}

$title = 'Editar post';
include __DIR__.'/includes/header.php';
?>
<h1 class="h3 mb-3">Editar post</h1>

<?php if ($err): ?>
  <div class="alert alert-danger"><ul class="mb-0"><?php foreach($err as $e) echo '<li>'.h($e).'</li>';?></ul></div>
<?php endif; ?>

<form method="post" class="row g-3" enctype="multipart/form-data">
  <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">

  <div class="col-md-4">
    <label class="form-label">Tipo</label>
    <select name="type" class="form-select">
      <option value="post"    <?= $post['type']==='post'?'selected':'' ?>>Artigo</option>
      <option value="gallery" <?= $post['type']==='gallery'?'selected':'' ?>>Galeria</option>
    </select>
  </div>

  <div class="col-md-8">
    <label class="form-label">Título</label>
    <input name="title" class="form-control" required value="<?= h($post['title']) ?>">
  </div>

  <div class="col-md-4">
    <label class="form-label">Slug (URL)</label>
    <input name="slug" class="form-control" value="<?= h($post['slug']) ?>">
  </div>

  <div class="col-md-8">
    <label class="form-label">Imagem de destaque (URL)</label>
    <input name="thumb_url" class="form-control" value="<?= h($post['thumb_url'] ?? '') ?>">
  </div>

  <div class="col-12">
    <label class="form-label">Resumo/Excerpt (opcional)</label>
    <textarea name="excerpt" class="form-control" rows="3"><?= h($post['excerpt']) ?></textarea>
  </div>

  <div class="col-12">
    <label class="form-label">Conteúdo</label>
    <textarea name="body" class="form-control" rows="12" required><?= h($post['body']) ?></textarea>
  </div>

  <!-- Ficha técnica -->
  <div class="col-md-4">
    <label class="form-label">Quem pintou</label>
    <input name="painter" class="form-control" value="<?= h($post['painter']) ?>">
  </div>
  <div class="col-md-4">
    <label class="form-label">Tamanho da peça</label>
    <input name="piece_size" class="form-control" value="<?= h($post['piece_size']) ?>">
  </div>
  <div class="col-md-4">
    <label class="form-label">Valor cobrado (unidade)</label>
    <input name="unit_price" class="form-control" type="number" step="0.01" value="<?= h($post['unit_price']) ?>">
  </div>

  <div class="col-12">
    <label class="form-label">Adicionar novas imagens (até 10 por vez)</label>
    <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
    <div class="form-text">JPG/PNG/WEBP, até <?= (int)(UPLOAD_MAX_BYTES/1048576) ?>MB por arquivo.</div>
  </div>

  <?php if ($imgs): ?>
    <div class="col-12">
      <label class="form-label">Imagens atuais (marque para remover)</label>
      <div class="row g-2">
        <?php foreach ($imgs as $im): ?>
          <div class="col-6 col-md-3">
            <div class="border rounded p-1 text-center">
              <img src="<?= h($im['path']) ?>" alt="" class="img-fluid mb-1" style="aspect-ratio:1/1;object-fit:cover;">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="delimg[<?= (int)$im['id'] ?>]" id="del<?= (int)$im['id'] ?>">
                <label class="form-check-label small" for="del<?= (int)$im['id'] ?>">Remover</label>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <div class="col-12 d-flex align-items-center gap-3">
    <div class="form-check">
      <input class="form-check-input" type="checkbox" id="pub" name="published" <?= $post['published']?'checked':'' ?>>
      <label for="pub" class="form-check-label">Publicado</label>
    </div>
    <button class="btn btn-success ms-auto">Salvar</button>
    <a class="btn btn-outline-secondary" href="<?= h(BASE_URL) ?>admin_posts.php">Cancelar</a>
  </div>
</form>

<?php include __DIR__.'/includes/footer.php';
