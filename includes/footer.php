<?php
// /includes/footer.php
?>
</main>

<footer class="site-footer">
  <div class="site-container d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
    <div>© <?= date('Y') ?> Rondi3D — Impressão 3D sob medida.</div>
    <nav class="d-flex gap-3">
      <a href="<?= h(BASE_URL) ?>quem-somos.php">Sobre</a>
      <a href="<?= h(BASE_URL) ?>novo-orcamento.php">Orçamento</a>
    </nav>
  </div>
</footer>

<!-- Lightbox (galeria) -->
<div class="lb" id="lightbox" aria-hidden="true">
  <button class="lb-close" id="lbClose" aria-label="Fechar">×</button>
  <img id="lbImg" alt="">
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= h(BASE_URL) ?>assets/js/app.js"></script>
</body>
</html>
