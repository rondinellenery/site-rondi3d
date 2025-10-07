<?php
// /admin_post_edit.php — cria/edita Artigo e Galeria (com upload de capa)
require __DIR__.'/config.php';
require_login();
require_admin();

$pdo = db();
$id  = (int)($_GET['id'] ?? 0);

// Carrega (edição) ou define defaults (novo)
if ($id) {
  $st = $pdo->prepare("SELECT * FROM posts WHERE id=?");
  $st->execute([$id]);
  $post = $st->fetch();
  if (!$post) { http_response_code(404); exit('Post não encontrado'); }
  $isEdit = true;
  $type   = $post['type'] ?? 'post';
} else {
  $post = [
    'title'      => '',
    'slug'       => '',
    'thumb_url'  => '',
    'excerpt'    => '',
    'body'       => '',
    'published'  => 1,
    'type'       => 'post',
    'painter'    => '',
    'piece_size' => '',
    'unit_price' => null,
  ];
  $isEdit = false;
  $type   = 'post';
}

// imagens atuais (galeria)
$imgs = [];
if ($isEdit) {
  $st = $pdo->prepare("SELECT id, path, sort FROM post_images WHERE post_id=? ORDER BY sort,id");
  $st->execute([$id]);
  $imgs = $st->fetchAll();
}

$err = [];

if ($_SERVER['REQUEST_METHOD']==='POST') {
  require_csrf();

  $title     = trim($_POST['title'] ?? '');
  $slug      = trim($_POST['slug'] ?? '');
  $excerpt   = trim($_POST['excerpt'] ?? '');
  $body      = trim($_POST['body'] ?? '');
  $published = isset($_POST['published']) ? 1 : 0;

  $type       = ($_POST['type'] ?? 'post') === 'gallery' ? 'gallery' : 'post';
  $painter    = trim($_POST['painter'] ?? '');
  $piece_size = trim($_POST['piece_size'] ?? '');
  $unit_price = $_POST['unit_price'] !== '' ? (float)$_POST['unit_price'] : null;

  if ($title==='') $err[]='Informe o título.';

  // slug automático
  if ($slug==='') {
    $slug = strtolower(preg_replace('/[^a-z0-9\-]+/','-', iconv('UTF-8','ASCII//TRANSLIT',$title)));
    $slug = trim($slug,'-');
  }
  if ($slug==='') $slug = 'post-'.bin2hex(random_bytes(3));

  // ——— Upload de CAPA ———
  $thumbUrl   = $post['thumb_url'] ?? null;         // default: mantém atual
  $removeCover = !empty($_POST['remove_cover']);    // checkbox “Remover capa”

  if ($removeCover) {
    // opcional: apagar arquivo físico se estiver dentro de UPLOAD_URL (descomentando bloco)
    /*
    $prefix = rtrim(UPLOAD_URL,'/').'/';
    if ($thumbUrl && str_starts_with($thumbUrl, $prefix)) {
      $rel  = substr($thumbUrl, strlen($prefix)); // ex: covers/2025/10/arquivo.jpg
      $file = rtrim(UPLOAD_DIR,'/\\').DIRECTORY_SEPARATOR.str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $rel);
      if (is_file($file)) @unlink($file);
    }
    */
    $thumbUrl = null;
  }

  if (!empty($_FILES['cover']['name']) && $_FILES['cover']['error']===UPLOAD_ERR_OK) {
    if ($_FILES['cover']['size'] > UPLOAD_MAX_BYTES) {
      $err[] = 'Capa acima do limite.';
    } else {
      $fi = new finfo(FILEINFO_MIME_TYPE);
      $mime = $fi->file($_FILES['cover']['tmp_name']) ?: '';
      $ok = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
      if (!isset($ok[$mime])) {
        $err[] = 'Capa precisa ser JPG, PNG ou WEBP.';
      } else {
        $subdir  = 'covers/'.date('Y').'/'.date('m');
        $destDir = rtrim(UPLOAD_DIR,'/\\').DIRECTORY_SEPARATOR.$subdir;
        if (!is_dir($destDir)) @mkdir($destDir, 0775, true);
        $base = pathinfo($_FILES['cover']['name'], PATHINFO_FILENAME);
        $base = preg_replace('/[^a-zA-Z0-9_\-]/','_', $base) ?: 'capa';
        $name = $base.'_'.bin2hex(random_bytes(5)).'.'.$ok[$mime];
        $dest = $destDir.DIRECTORY_SEPARATOR.$name;

        if (!move_uploaded_file($_FILES['cover']['tmp_name'], $dest)) {
          $err[] = 'Falha ao salvar a capa.';
        } else {
          $thumbUrl = rtrim(UPLOAD_URL,'/').'/'.$subdir.'/'.$name;
        }
      }
    }
  }

  // limite de upload múltiplo (galeria)
  if (!empty($_FILES['images']['name'][0])) {
    $countSel = 0;
    foreach ((array)$_FILES['images']['name'] as $n) if ($n!=='') $countSel++;
    if ($countSel > 10) $err[] = 'Envie no máximo 10 imagens por vez.';
  }

  if (!$err) {
    try {
      if ($isEdit) {
        // update
        $st = $pdo->prepare("
          UPDATE posts
             SET slug=?, title=?, excerpt=?, body=?, thumb_url=?, published=?,
                 type=?, painter=?, piece_size=?, unit_price=?
           WHERE id=?
        ");
        $st->execute([$slug,$title,$excerpt,$body,$thumbUrl,$published,$type,$painter,$piece_size,$unit_price,$id]);
      } else {
        // insert
        $st = $pdo->prepare("
          INSERT INTO posts (slug, title, thumb_url, excerpt, body, published, type, painter, piece_size, unit_price, created_at)
          VALUES (?,?,?,?,?,?,?,?,?,?, NOW())
        ");
        $st->execute([$slug,$title,$thumbUrl,$excerpt,$body,$published,$type,$painter,$piece_size,$unit_price]);
        $id = (int)$pdo->lastInsertId();
        $isEdit = true;
      }

      // Remover imagens marcadas (edição)
      if ($type==='gallery' && !empty($_POST['delimg']) && is_array($_POST['delimg'])) {
        $idsDel = array_map('intval', array_keys($_POST['delimg']));
        if ($idsDel) {
          $pdo->query("DELETE FROM post_images WHERE post_id={$id} AND id IN (".implode(',',$idsDel).")");
        }
      }

      // Upload múltiplo (apenas quando type = gallery)
      if ($type==='gallery' && !empty($_FILES['images']['name'][0])) {
        $fi = new finfo(FILEINFO_MIME_TYPE);
        $ok = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];

        $subdir  = 'gallery/'.date('Y').'/'.date('m');
        $destDir = rtrim(UPLOAD_DIR,'/\\').DIRECTORY_SEPARATOR.$subdir;
        if (!is_dir($destDir)) @mkdir($destDir, 0775, true);

        $nextSort = (int)$pdo->query("SELECT COALESCE(MAX(sort),0)+1 FROM post_images WHERE post_id=".$id)->fetchColumn();

        $n = count($_FILES['images']['name']);
        for ($i=0; $i<$n; $i++) {
          if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK || $_FILES['images']['name'][$i]==='') continue;
          if ($_FILES['images']['size'][$i] > UPLOAD_MAX_BYTES) continue;

          $mime = $fi->file($_FILES['images']['tmp_name'][$i]) ?: '';
          if (!isset($ok[$mime])) continue;

          $ext  = $ok[$mime];
          $base = pathinfo($_FILES['images']['name'][$i], PATHINFO_FILENAME);
          $base = preg_replace('/[^a-zA-Z0-9_\-]/','_', $base) ?: 'img';
          $name = $base.'_'.bin2hex(random_bytes(5)).'.'.$ext;
          $dest = $destDir.DIRECTORY_SEPARATOR.$name;

          if (!move_uploaded_file($_FILES['images']['tmp_name'][$i], $dest)) continue;

          $url = rtrim(UPLOAD_URL,'/').'/'.$subdir.'/'.$name;
          $pdo->prepare("INSERT INTO post_images (post_id, path, sort, created_at) VALUES (?,?,?,NOW())")
              ->execute([$id, $url, $nextSort++]);
        }
      }

      header('Location: '.BASE_URL.'admin_posts.php');
      exit;
    } catch (Throwable $e) {
      $err[] = 'Erro ao salvar: '.$e->getMessage();
    }
  }

  // atualiza $post para re-renderizar após submit
  $post = array_merge($post ?? [], [
    'title'      => $title ?? $post['title'],
    'slug'       => $slug  ?? $post['slug'],
    'thumb_url'  => $thumbUrl,
    'excerpt'    => $excerpt ?? $post['excerpt'],
    'body'       => $body ?? $post['body'],
    'published'  => $published,
    'type'       => $type,
    'painter'    => $painter ?? '',
    'piece_size' => $piece_size ?? '',
    'unit_price' => $unit_price,
  ]);

  // recarrega imagens após submit
  $st = $pdo->prepare("SELECT id, path, sort FROM post_images WHERE post_id=? ORDER BY sort,id");
  $st->execute([$id]);
  $imgs = $st->fetchAll();
}

$title = $isEdit ? 'Editar post' : 'Novo post';
include __DIR__.'/includes/header.php';
?>
<h1 class="h3 mb-3"><?= $isEdit ? 'Editar post' : 'Novo post' ?></h1>

<?php if ($err): ?>
  <div class="alert alert-danger"><ul class="mb-0"><?php foreach($err as $e) echo '<li>'.h($e).'</li>'; ?></ul></div>
<?php endif; ?>

<form method="post" class="row g-3" enctype="multipart/form-data">
  <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">

  <div class="col-md-4">
    <label class="form-label">Tipo</label>
    <select name="type" class="form-select" id="typeSelect">
      <option value="post"    <?= ($post['type']??'post')==='post'?'selected':'' ?>>Artigo</option>
      <option value="gallery" <?= ($post['type']??'post')==='gallery'?'selected':'' ?>>Galeria</option>
    </select>
  </div>

  <div class="col-md-8">
    <label class="form-label">Título</label>
    <input name="title" class="form-control" required value="<?= h($post['title']) ?>">
  </div>

  <div class="col-md-4">
    <label class="form-label">Slug (URL)</label>
    <input name="slug" class="form-control" value="<?= h($post['slug']) ?>" placeholder="vazio para gerar">
  </div>

  <!-- CAPA (upload) -->
  <div class="col-md-8">
    <label class="form-label">Imagem de destaque (capa)</label>
    <input type="file" name="cover" class="form-control" accept="image/*">
    <div class="form-text">JPG/PNG/WEBP — limite <?= (int)(UPLOAD_MAX_BYTES/1048576) ?>MB.</div>

    <?php if (!empty($post['thumb_url'])): ?>
      <div class="mt-2 d-flex align-items-center gap-3">
        <img src="<?= h($post['thumb_url']) ?>" alt="Capa atual" class="rounded border" style="max-height:80px">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="rmcover" name="remove_cover">
          <label for="rmcover" class="form-check-label">Remover capa atual</label>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <div class="col-12">
    <label class="form-label">Resumo/Excerpt (opcional)</label>
    <textarea name="excerpt" class="form-control" rows="3"><?= h($post['excerpt']) ?></textarea>
  </div>

  <div class="col-12">
    <label class="form-label">Conteúdo</label>
    <textarea name="body" class="form-control" rows="12" required><?= h($post['body']) ?></textarea>
  </div>

  <!-- Ficha técnica (apenas quando Galeria) -->
  <div class="col-12 border rounded p-3" id="galleryBox" style="<?= ($post['type']??'post')==='gallery'?'':'display:none' ?>">
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Quem pintou</label>
        <input name="painter" class="form-control" value="<?= h($post['painter'] ?? '') ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Tamanho da peça</label>
        <input name="piece_size" class="form-control" value="<?= h($post['piece_size'] ?? '') ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Valor cobrado (unidade)</label>
        <input name="unit_price" type="number" step="0.01" min="0" class="form-control" value="<?= h($post['unit_price'] ?? '') ?>">
      </div>

      <div class="col-12">
        <label class="form-label">Adicionar imagens (até 10 por vez)</label>
        <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
        <div class="form-text">JPG/PNG/WEBP — limite <?= (int)(UPLOAD_MAX_BYTES/1048576) ?>MB por arquivo.</div>
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
    </div>
  </div>

  <div class="col-12 d-flex align-items-center gap-3">
    <div class="form-check">
      <input class="form-check-input" type="checkbox" id="pub" name="published" <?= !empty($post['published'])?'checked':'' ?>>
      <label for="pub" class="form-check-label">Publicado</label>
    </div>
    <button class="btn btn-success ms-auto"><?= $isEdit ? 'Salvar' : 'Criar' ?></button>
    <a class="btn btn-outline-secondary" href="<?= h(BASE_URL) ?>admin_posts.php">Cancelar</a>
  </div>
</form>

<script>
  (function(){
    const sel = document.getElementById('typeSelect');
    const box = document.getElementById('galleryBox');
    function toggle(){ box.style.display = sel.value === 'gallery' ? '' : 'none'; }
    sel.addEventListener('change', toggle); toggle();
  })();
</script>

<?php include __DIR__.'/includes/footer.php';
