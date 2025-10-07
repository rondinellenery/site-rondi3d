<?php
require __DIR__.'/config.php';
require_login();
if (!is_admin()){ http_response_code(403); exit('Acesso restrito.'); }

$title = 'Novo artigo';
include __DIR__.'/includes/header.php';
?>
<div class="site-container site-container--wide">
  <h1 class="h4 mb-3">Novo artigo</h1>

  <form class="card shadow-sm" method="post" action="<?= h(BASE_URL) ?>posts_save.php" enctype="multipart/form-data">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-8">
          <label class="form-label">Título *</label>
          <input required class="form-control" name="title" id="inTitle" placeholder="Ex.: Como escolher o material ideal">
        </div>
        <div class="col-md-4">
          <label class="form-label">Slug (URL)</label>
          <input class="form-control" name="slug" id="inSlug" placeholder="como-escolher-o-material">
          <div class="form-text">Se vazio, será gerado automaticamente.</div>
        </div>

        <div class="col-12">
          <label class="form-label">Capa (imagem)</label>
          <input type="file" class="form-control" name="cover" accept="image/*">
          <div class="form-text">JPG/PNG/WEBP até 20MB.</div>
        </div>

        <div class="col-12">
          <label class="form-label">Resumo (opcional)</label>
          <textarea class="form-control" name="excerpt" rows="2" placeholder="Uma breve descrição que aparece na listagem."></textarea>
        </div>

        <div class="col-12">
          <label class="form-label">Conteúdo</label>
          <textarea class="form-control" name="body" id="postBody" rows="12"></textarea>
        </div>

        <div class="col-md-3">
          <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" name="published" id="inPub" checked>
            <label class="form-check-label" for="inPub">Publicado</label>
          </div>
        </div>
      </div>
    </div>
    <div class="card-footer d-flex justify-content-end gap-2">
      <a class="btn btn-outline-secondary" href="<?= h(BASE_URL) ?>posts.php">Cancelar</a>
      <button class="btn btn-primary">Salvar</button>
    </div>
  </form>
</div>

<!-- TinyMCE (CDN) -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
  selector: '#postBody',
  height: 520,
  menubar: false,
  plugins: 'link lists image table code media autoresize',
  toolbar: 'undo redo | styles | bold italic underline | bullist numlist | link image media table | alignleft aligncenter alignright | removeformat | code',
  branding: false,
  convert_urls: false,
  relative_urls: false,
  image_caption: true
});

// gera slug básico a partir do título
document.getElementById('inTitle').addEventListener('blur', ()=>{
  const t = document.getElementById('inTitle').value.trim();
  const s = document.getElementById('inSlug');
  if (!s.value.trim() && t){
    s.value = t.toLowerCase()
      .normalize('NFD').replace(/[\u0300-\u036f]/g,'')
      .replace(/[^a-z0-9]+/g,'-').replace(/(^-|-$)/g,'');
  }
});
</script>

<?php include __DIR__.'/includes/footer.php'; ?>
