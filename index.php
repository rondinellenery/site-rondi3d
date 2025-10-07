<?php
require __DIR__.'/config.php';
$title = 'Home';

// carrega últimos posts (somente ARTIGOS)
$pdo = db();
$posts = $pdo->query("
  SELECT id, slug, title, thumb_url, excerpt, created_at
  FROM posts
  WHERE published = 1 AND COALESCE(type,'post') = 'post'
  ORDER BY created_at DESC, id DESC
  LIMIT 6
")->fetchAll();

include __DIR__.'/includes/header.php';
?>

<!-- TÓPICOS FIXOS -->
<section class="mb-4">
  <div class="row row-cols-1 row-cols-md-3 g-3">
    <div class="col">
      <div class="card h-100 shadow-sm">
        <img src="<?= h(BASE_URL) ?>assets/img/posts/post1.png" class="card-img-top"
             alt="Peça sob medida" style="aspect-ratio:16/9;object-fit:cover;">
        <div class="card-body">
          <h2 class="h5">Peça sob medida e personalizada</h2>
          <p class="text-muted mb-0">Produzimos modelos únicos com alta precisão e ótimo acabamento.</p>
        </div>
      </div>
    </div>

    <div class="col">
      <div class="card h-100 shadow-sm">
        <img src="<?= h(BASE_URL) ?>assets/img/posts/post2.png" class="card-img-top"
             alt="Protótipos e peças funcionais" style="aspect-ratio:16/9;object-fit:cover;">
        <div class="card-body">
          <h2 class="h5">Protótipos e peças funcionais</h2>
          <p class="text-muted mb-0">Valide ideias rapidamente com peças robustas para testes.</p>
        </div>
      </div>
    </div>

    <div class="col">
      <div class="card h-100 shadow-sm">
        <img src="<?= h(BASE_URL) ?>assets/img/posts/post3.png" class="card-img-top"
             alt="Artesanato e escultura" style="aspect-ratio:16/9;object-fit:cover;">
        <div class="card-body">
          <h2 class="h5">Artesanato e escultura</h2>
          <p class="text-muted mb-0">Detalhe e acabamento para projetos artísticos e decorativos.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CARROSSEL DE FEEDBACKS -->
<section class="mb-5">
  <div class="d-flex align-items-center justify-content-between mb-2">
    <div class="h4 m-0">Feedbacks de clientes</div>
  </div>

  <div id="feedbackCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">

      <!-- Item 1 -->
      <div class="carousel-item active">
        <div class="testimonial-card d-flex">
          <div class="flex-grow-1">
            <div class="testimonial-text">
              “Excelente trabalho. Proatividade total do Rondi. Não tinha o arquivo e ele achou e fez em pouco tempo o que queria. Recomendo demais e em breve voltarei com outros projetos.”
            </div>
            <div class="testimonial-meta">
              <img src="<?= h(BASE_URL) ?>assets/img/avatars/cli1.png" alt="Cliente 1">
              <div>
                <div class="fw-semibold">Lucas Romcy</div>
                <div class="small text-muted">Fortaleza — CE</div>
              </div>
            </div>
          </div>
          <div class="actions-right">
            <a href="<?= h(BASE_URL) ?>galeria.php" class="btn btn-sm btn-outline-primary">Ver projeto</a>
          </div>
        </div>
      </div>

      <!-- Item 2 -->
      <div class="carousel-item">
        <div class="testimonial-card d-flex">
          <div class="flex-grow-1">
            <div class="testimonial-text">
              “Indico demais!!! Me deu atenção necessária desde o início da compra, até a retirada,
              tirou as dúvidas e fez exatamente do jeitinho que eu queria. Parabéns , superou minhas expectativas. 👏🏻👏🏻👏🏻”
            </div>
            <div class="testimonial-meta">
              <img src="<?= h(BASE_URL) ?>assets/img/avatars/cli2.jpg" alt="Cliente 2">
              <div>
                <div class="fw-semibold">Kaylane Soares</div>
                <div class="small text-muted">Fortaleza — CE</div>
              </div>
            </div>
          </div>
          <div class="actions-right">
            <a href="<?= h(BASE_URL) ?>galeria.php" class="btn btn-sm btn-outline-primary">Ver projeto</a>
          </div>
        </div>
      </div>

      <!-- Item 3 -->
      <div class="carousel-item">
        <div class="testimonial-card d-flex">
          <div class="flex-grow-1">
            <div class="testimonial-text">
              “Pedi acabamento premium e ficou com cara de produto de prateleira. Recomendo!”
            </div>
            <div class="testimonial-meta">
              <img src="<?= h(BASE_URL) ?>assets/img/avatars/cli3.jpg" alt="Cliente 3">
              <div>
                <div class="fw-semibold">Roberto C.</div>
                <div class="small text-muted">Eusébio — CE</div>
              </div>
            </div>
          </div>
          <div class="actions-right">
            <a href="<?= h(BASE_URL) ?>galeria.php" class="btn btn-sm btn-outline-primary">Ver projeto</a>
          </div>
        </div>
      </div>

    </div>

    <!-- Controles -->
    <button class="carousel-control-prev" type="button" data-bs-target="#feedbackCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Anterior</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#feedbackCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Próximo</span>
    </button>

    <!-- Indicadores -->
    <div class="carousel-indicators mt-2">
      <button type="button" data-bs-target="#feedbackCarousel" data-bs-slide-to="0" class="active" aria-current="true"></button>
      <button type="button" data-bs-target="#feedbackCarousel" data-bs-slide-to="1"></button>
      <button type="button" data-bs-target="#feedbackCarousel" data-bs-slide-to="2"></button>
    </div>
  </div>
</section>

<!-- ARTIGOS / BLOG -->
<section class="mb-5">
  <div class="d-flex align-items-center justify-content-between mb-2">
    <h2 class="h4 m-0">Artigos recentes</h2>
    <a class="btn btn-outline-primary btn-sm" href="<?= h(BASE_URL) ?>posts.php">Ver todos</a>
  </div>

  <?php if (!$posts): ?>
    <div class="alert alert-info">Ainda não há publicações.</div>
  <?php else: ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
      <?php foreach ($posts as $p): ?>
        <div class="col">
          <a class="card h-100 text-decoration-none" href="<?= h(BASE_URL) ?>post.php?slug=<?= h($p['slug']) ?>">
            <?php if (!empty($p['thumb_url'])): ?>
              <img src="<?= h($p['thumb_url']) ?>" class="card-img-top"
                   alt="<?= h($p['title']) ?>" style="aspect-ratio:16/9;object-fit:cover;">
            <?php endif; ?>
            <div class="card-body d-flex flex-column">
              <div class="small text-muted mb-1"><?= h(date('d/m/Y', strtotime($p['created_at']))) ?></div>
              <h3 class="h6 mb-2"><?= h($p['title']) ?></h3>
              <div class="text-muted small mt-auto"
                   style="display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">
                <?= h($p['excerpt'] ?? '') ?>
              </div>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<?php include __DIR__.'/includes/footer.php';
