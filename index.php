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
    <!-- CARD 1: guia de orçamento -->
    <div class="col">
      <a class="card h-100 shadow-sm text-decoration-none"
         href="<?= h(BASE_URL) ?>guia-orcamento-3d.php">
        <img src="<?= h(BASE_URL) ?>assets/img/posts/post1.png" class="card-img-top"
             alt="Peça sob medida" style="aspect-ratio:16/9;object-fit:cover;">
        <div class="card-body">
          <h2 class="h5 mb-1 text-dark text-center">Como funciona o Orçamento?</h2>
          <p class="text-muted mb-0 text-center">Sem complicação e direto ao ponto esclarecendo como funciona o nosso processo de orçamento.</p>
        </div>
      </a>
    </div>

    <!-- CARD 2: redes sociais -->
    <div class="col">
      <a class="card h-100 shadow-sm text-decoration-none"
         href="<?= h(BASE_URL) ?>redes-sociais.php">
        <img src="<?= h(BASE_URL) ?>assets/img/posts/post2.png" class="card-img-top"
             alt="Nossas Redes Sociais" style="aspect-ratio:16/9;object-fit:cover;">
        <div class="card-body">
          <h2 class="h5 text-center">Nossas Redes Sociais</h2>
          <p class="text-muted mb-0 text-center">Acompanhe nossas Redes Sociais e fique por dentro em tempo real do que há de novo.</p>
        </div>
      </a>
    </div>

    <!-- CARD 3: minha história -->
    <div class="col">
      <a class="card h-100 shadow-sm text-decoration-none"
         href="<?= h(BASE_URL) ?>minha-historia.php">
        <img src="<?= h(BASE_URL) ?>assets/img/posts/post3.png" class="card-img-top"
             alt="Quando o Hobby se Torna Profissão" style="aspect-ratio:16/9;object-fit:cover;">
        <div class="card-body">
          <h2 class="h5 text-center">Quando o Hobby se Torna Profissão</h2>
          <p class="text-muted mb-0 text-center">Vou compartilhar como transformei meu hobby em uma carreira de sucesso.</p>
        </div>
      </a>
    </div>
  </div>
</section>



<!-- CARROSSEL DE FEEDBACKS (com visual de confiança) -->
<section class="mb-5 section-trust">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h2 class="h4 trust-title m-0">
      <span class="dot"></span> Feedbacks de clientes
    </h2>

    <!-- Selo opcional de “avaliações verificadas” -->
    <span class="trust-badge">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M20 7l-9 9-5-5" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      Avaliações verificadas
    </span>
  </div>

  <div id="feedbackCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">

      <!-- Item 1 -->
      <div class="carousel-item active">
        <div class="testimonial-card d-flex">
          <div class="flex-grow-1">
            <div class="testimonial-quote">
              Excelente trabalho. Proatividade total do Rondi. Não tinha o arquivo e ele achou e fez em pouco
              tempo o que queria. Recomendo demais e em breve voltarei com outros projetos.
            </div>
            <div class="testimonial-meta">
              <img src="<?= h(BASE_URL) ?>assets/img/avatars/cli1.png" alt="Lucas Romcy">
              <div class="testimonial-person">
                <span class="testimonial-name">Lucas Romcy</span>
                <span class="testimonial-loc">Fortaleza — CE</span>
              </div>
            </div>
          </div>
          <div class="actions-right ms-3">
            <a href="<?= h(BASE_URL) ?>galeria.php" class="btn btn-sm btn-outline-primary">Ver projeto</a>
          </div>
        </div>
      </div>

      <!-- Item 2 -->
      <div class="carousel-item">
        <div class="testimonial-card d-flex">
          <div class="flex-grow-1">
            <div class="testimonial-quote">
              Indico demais! Me deu atenção desde o início da compra até a retirada, tirou as dúvidas e fez
              exatamente do jeitinho que eu queria. Parabéns, superou minhas expectativas.
            </div>
            <div class="testimonial-meta">
              <img src="<?= h(BASE_URL) ?>assets/img/avatars/cli2.jpg" alt="Kaylane Soares">
              <div class="testimonial-person">
                <span class="testimonial-name">Kaylane Soares</span>
                <span class="testimonial-loc">Fortaleza — CE</span>
              </div>
            </div>
          </div>
          <div class="actions-right ms-3">
            <a href="<?= h(BASE_URL) ?>galeria.php" class="btn btn-sm btn-outline-primary">Ver projeto</a>
          </div>
        </div>
      </div>

      <!-- Item 3 -->
      <div class="carousel-item">
        <div class="testimonial-card d-flex">
          <div class="flex-grow-1">
            <div class="testimonial-quote">
              Muito boa a experiência, ótimo preço e atendimento. Excelente profissional!
              Fiz a máscara do Líder do Round 6 e ficou perfeita.
            </div>
            <div class="testimonial-meta">
              <img src="<?= h(BASE_URL) ?>assets/img/avatars/cli3.png" alt="Walmir Queiroz">
              <div class="testimonial-person">
                <span class="testimonial-name">Walmir Queiroz</span>
                <span class="testimonial-loc">Eusébio — CE</span>
              </div>
            </div>
          </div>
          <div class="actions-right ms-3">
            <a href="<?= h(BASE_URL) ?>galeria.php" class="btn btn-sm btn-outline-primary">Ver projeto</a>
          </div>
        </div>
      </div>

    </div>

    <!-- Controles -->
    <button class="carousel-control-prev" type="button" data-bs-target="#feedbackCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Anterior</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#feedbackCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Próximo</span>
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
