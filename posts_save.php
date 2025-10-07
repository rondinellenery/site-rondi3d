<?php
require __DIR__.'/config.php';
require_login();
if (!is_admin()){ http_response_code(403); exit('Acesso restrito.'); }

$pdo = db();

// campos
$title   = trim($_POST['title'] ?? '');
$slug    = trim($_POST['slug'] ?? '');
$excerpt = trim($_POST['excerpt'] ?? '');
$body    = trim($_POST['body'] ?? '');
$pub     = isset($_POST['published']) ? 1 : 0;

$type     = $_POST['type'] ?? 'post';
$painter  = trim($_POST['painter'] ?? '');
$piece_sz = trim($_POST['piece_size'] ?? '');
$unit_pr  = $_POST['unit_price'] !== '' ? (float)$_POST['unit_price'] : null;

if ($title==='' || $body===''){
  http_response_code(400); exit('Título e conteúdo são obrigatórios.');
}

// slug automático
if ($slug===''){
  $slug = mb_strtolower($title);
  $slug = iconv('UTF-8','ASCII//TRANSLIT',$slug);
  $slug = preg_replace('/[^a-z0-9]+/','-', $slug);
  $slug = trim($slug,'-');
}
if ($slug===''){ $slug = 'post-'.bin2hex(random_bytes(3)); }

// capa opcional (igual ao seu)
$coverUrl = null;
if (!empty($_FILES['cover']['name']) && $_FILES['cover']['error']===UPLOAD_ERR_OK){
  if ($_FILES['cover']['size'] > UPLOAD_MAX_BYTES){ http_response_code(400); exit('Capa acima de 20MB.'); }
  $fi   = new finfo(FILEINFO_MIME_TYPE);
  $mime = $fi->file($_FILES['cover']['tmp_name']) ?: '';
  $ext  = match($mime){ 'image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp', default=>null };
  if (!$ext){ http_response_code(400); exit('Capa precisa ser JPG/PNG/WEBP.'); }

  $subdir  = 'posts/'.date('Y').'/'.date('m');
  $destDir = rtrim(UPLOAD_DIR,'/\\').DIRECTORY_SEPARATOR.$subdir;
  if (!is_dir($destDir)) mkdir($destDir, 0775, true);
  $base = preg_replace('/[^a-zA-Z0-9_\-]/','_', pathinfo($_FILES['cover']['name'], PATHINFO_FILENAME)) ?: 'capa';
  $name = $base.'_'.bin2hex(random_bytes(5)).'.'.$ext;
  $dest = $destDir.DIRECTORY_SEPARATOR.$name;
  if (!move_uploaded_file($_FILES['cover']['tmp_name'], $dest)){ http_response_code(500); exit('Falha ao salvar capa.'); }
  $coverUrl = rtrim(UPLOAD_URL,'/').'/'.$subdir.'/'.$name;
}

// insere
$st = $pdo->prepare("
  INSERT INTO posts (slug, title, thumb_url, excerpt, body, published, type, painter, piece_size, unit_price, created_at)
  VALUES (?,?,?,?,?,?,?,?,?,?, NOW())
");
$st->execute([$slug,$title,$coverUrl,$excerpt,$body,$pub,$type,$painter,$piece_sz,$unit_pr]);

// uploads múltiplos (se for galeria)
$postId = (int)$pdo->lastInsertId();
if ($type==='gallery' && !empty($_FILES['gallery']['name'][0])) {
  $fi = new finfo(FILEINFO_MIME_TYPE);
  $subdir  = 'gallery/'.date('Y').'/'.date('m');
  $destDir = rtrim(UPLOAD_DIR,'/\\').DIRECTORY_SEPARATOR.$subdir;
  if (!is_dir($destDir)) mkdir($destDir, 0775, true);

  $count = min(10, count($_FILES['gallery']['name']));
  $ins = $pdo->prepare("INSERT INTO post_images (post_id,path,sort,created_at) VALUES (?,?,?,NOW())");

  for ($i=0; $i<$count; $i++) {
    if ($_FILES['gallery']['error'][$i] !== UPLOAD_ERR_OK) continue;
    if ($_FILES['gallery']['size'][$i] > UPLOAD_MAX_BYTES) continue;

    $mime = $fi->file($_FILES['gallery']['tmp_name'][$i]) ?: '';
    $ext  = ($mime==='image/jpeg')?'jpg':(($mime==='image/png')?'png':(($mime==='image/webp')?'webp':null));
    if (!$ext) continue;

    $base = pathinfo($_FILES['gallery']['name'][$i], PATHINFO_FILENAME);
    $base = preg_replace('/[^a-zA-Z0-9_\-]/','_', $base) ?: 'img';
    $name = $base.'_'.bin2hex(random_bytes(5)).'.'.$ext;
    $dest = $destDir.DIRECTORY_SEPARATOR.$name;

    if (move_uploaded_file($_FILES['gallery']['tmp_name'][$i], $dest)) {
      $url  = rtrim(UPLOAD_URL,'/').'/'.$subdir.'/'.$name;
      $ins->execute([$postId,$url,$i+1]);
    }
  }
}

header('Location: '.BASE_URL.'admin_posts.php');
