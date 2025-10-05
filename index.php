<?php
require __DIR__.'/config.php';
$title = "Rondi3D — Impressão 3D sob medida";
include __DIR__.'/includes/header.php';
?>

<!-- HERO -->
<section class="row align-items-center g-4 hero">
  <div class="col-lg-7">
    <h1 class="display-5 fw-bold mb-3">Peças 3D com acabamento de loja</h1>
    <p class="lead text-muted mb-4">
      Sua ideia, pronta para usar: FDM e resina + modelagem para
      <strong>colecionáveis</strong>, <strong>prototótipos</strong>,
      <strong>artesanato</strong>, <strong>esculturas</strong> e
      <strong>projetos acadêmicos</strong>.
    </p>
    <div class="d-flex gap-2">
      <a href="calculadora.php" class="btn btn-primary">Calcular meu projeto</a>
      <a href="quem-somos.php" class="btn btn-outline-secondary">Quem somos</a>
    </div>
  </div>
  <div class="col-lg-5 text-center">
    <img src="assets/img/sobre.png" alt="Rondi3D — impressão 3D sob medida"
         class="img-fluid rounded-14 shadow-soft" style="max-height:360px;object-fit:cover;">
  </div>
</section>

<!-- DIFERENCIAIS RÁPIDOS -->
<section class="my-4">
  <div class="row g-3">
    <div class="col-md-4">
      <div class="p-3 border rounded-14 h-100">
        <h3 class="h6 mb-1">Acabamento premium</h3>
        <p class="mb-0 text-muted">Peças limpas, tolerâncias revisadas e, quando necessário, lixa/primer/pintura.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="p-3 border rounded-14 h-100">
        <h3 class="h6 mb-1">FDM & Resina</h3>
        <p class="mb-0 text-muted">Força e custo-benefício na FDM; detalhe fino e suavidade na resina.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="p-3 border rounded-14 h-100">
        <h3 class="h6 mb-1">Orçamento transparente</h3>
        <p class="mb-0 text-muted">Estimativa clara baseada em material e tempo — do jeito que combinamos.</p>
      </div>
    </div>
  </div>
</section>

<!-- SERVIÇOS / VITRINE (sem boardgame) -->
<section class="my-4">
  <h2 class="h4 mb-3">Serviços em destaque</h2>
  <div class="row g-3">
    <div class="col-md-4">
      <article class="post-card">
        <img class="post-thumb" src="assets/img/post1.png" alt="Peças sob medida e personalizadas">
        <div class="p-3 d-flex flex-column gap-1">
          <h3 class="h6 mb-1">Peças sob medida</h3>
          <p class="text-muted mb-2">Personalização, adaptações e soluções para uso real — do conceito à peça final.</p>
          <div class="mt-auto d-flex gap-2">
            <a class="btn btn-sm btn-primary" href="calculadora.php">Orçar minha peça</a>
            <a class="btn btn-sm btn-outline-secondary" href="quem-somos.php">Saiba mais</a>
          </div>
        </div>
      </article>
    </div>

    <div class="col-md-4">
      <article class="post-card">
        <img class="post-thumb" src="assets/img/post2.png" alt="Prototipagem e peças funcionais">
        <div class="p-3 d-flex flex-column gap-1">
          <h3 class="h6 mb-1">Prototipagem & Funcionais</h3>
          <p class="text-muted mb-2">Teste de forma, tolerâncias e encaixes com prazos honestos.</p>
          <div class="mt-auto d-flex gap-2">
            <a class="btn btn-sm btn-primary" href="calculadora.php">Orçar protótipo</a>
            <a class="btn btn-sm btn-outline-secondary" href="quem-somos.php">Processo</a>
          </div>
        </div>
      </article>
    </div>

    <div class="col-md-4">
      <article class="post-card">
        <img class="post-thumb" src="assets/img/post3.png" alt="Miniaturas, Esculturas e Actionfigures">
        <div class="p-3 d-flex flex-column gap-1">
          <h3 class="h6 mb-1">Miniaturas & Esculturas (Resina)</h3>
          <p class="text-muted mb-2">Detalhe fino, superfícies suaves e acabamento de vitrine.</p>
          <div class="mt-auto d-flex gap-2">
            <a class="btn btn-sm btn-primary" href="calculadora.php">Orçar agora</a>
            <a class="btn btn-sm btn-outline-secondary" href="quem-somos.php">Materiais</a>
          </div>
        </div>
      </article>
    </div>
  </div>
</section>

<!-- CHAMADA FINAL -->
<section class="my-5">
  <div class="p-4 rounded-14 shadow-soft" style="background:#f6faf9;border:1px solid #e5e7eb">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
      <div>
        <h2 class="h5 mb-1">Pronto pra tirar sua ideia do papel?</h2>
        <p class="mb-0 text-muted">Faça uma estimativa agora e acompanhe seu pedido em <strong>Meus Orçamentos</strong>.</p>
      </div>
      <div class="d-flex gap-2">
        <a href="calculadora.php" class="btn btn-primary">Calcular meu projeto</a>
        <?php if (current_user()): ?>
          <a href="meus-orcamentos.php" class="btn btn-outline-dark">Meus Orçamentos</a>
        <?php else: ?>
          <a href="signup.php" class="btn btn-outline-dark">Criar minha conta</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__.'/includes/footer.php'; ?>
