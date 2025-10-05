<?php
require __DIR__.'/config.php';
$title = "Quem Somos — Rondi3D";
include __DIR__.'/includes/header.php';
?>

<!-- HERO + INTRO -->
<section class="row align-items-center g-4 mb-4">
  <div class="col-lg-6">
    <h1 class="mb-3">Quem Somos</h1>
    <p class="lead">
      Na <strong>Rondi3D</strong> transformamos ideias em peças reais, com <strong>acabamento de loja</strong> e atenção a cada detalhe.
      Somos apaixonados por <strong>impressão 3D</strong>, <strong>modelagem</strong> e pelo universo <strong>geek</strong> e de <strong>boardgames</strong> — dos inserts que agilizam o setup às miniaturas que viram destaque na mesa.
    </p>
  </div>
  <div class="col-lg-6 text-center">
    <img src="assets/img/sobre.png" alt="Rondi3D — impressão 3D e boardgames"
         class="img-fluid rounded-14 shadow-soft" style="max-height:340px;object-fit:cover;">
  </div>
</section>

<!-- O QUE FAZEMOS -->
<section class="mb-4">
  <h2 class="h4 mb-3">O que fazemos</h2>
  <div class="row g-3">
    <div class="col-md-6">
      <div class="p-3 border rounded-14 h-100">
        <h3 class="h6 mb-2">Peças sob medida e personalizadas</h3>
        <p class="mb-0">Prototipagem, peças funcionais, acessórios e soluções para pequenos reparos — do conceito à peça final.</p>
      </div>
    </div>
    <div class="col-md-6">
      <div class="p-3 border rounded-14 h-100">
        <h3 class="h6 mb-2">Inserts e acessórios para jogos de tabuleiro</h3>
        <p class="mb-0">Organização inteligente, proteção dos componentes e <strong>setup turbo</strong> para jogar mais e arrumar menos.</p>
      </div>
    </div>
    <div class="col-md-6">
      <div class="p-3 border rounded-14 h-100">
        <h3 class="h6 mb-2">Miniaturas, props e colecionáveis</h3>
        <p class="mb-0">Produção em <strong>resina</strong> com alto nível de detalhe e acabamento premium (lixa, primer e pintura opcional).</p>
      </div>
    </div>
    <div class="col-md-6">
      <div class="p-3 border rounded-14 h-100">
        <h3 class="h6 mb-2">Prototipagem rápida</h3>
        <p class="mb-0">Validação de forma, tolerâncias e encaixes, com ajustes rápidos até chegar ao resultado ideal.</p>
      </div>
    </div>
  </div>
</section>

<!-- COMO TRABALHAMOS -->
<section class="mb-4">
  <h2 class="h4 mb-3">Como trabalhamos</h2>
  <ol class="ps-3">
    <li class="mb-2"><strong>Briefing rápido</strong> — você conta a ideia e, se quiser, anexa uma imagem de referência.</li>
    <li class="mb-2"><strong>Estimativa transparente</strong> — calculamos material, tempo de impressão e mostramos o valor com clareza.</li>
    <li class="mb-2"><strong>Modelagem/ajustes</strong> — refinamos o 3D (quando necessário) e validamos com você.</li>
    <li class="mb-2"><strong>Produção e acabamento</strong> — impressões em <strong>FDM</strong> (força/custo-benefício) ou <strong>Resina</strong> (detalhe/suavidade), com acabamento profissional.</li>
    <li class="mb-2"><strong>Entrega</strong> — combinamos retirada, envio ou motoboy (conforme sua região).</li>
  </ol>
</section>

<!-- DIFERENCIAIS -->
<section class="mb-4">
  <h2 class="h4 mb-3">Por que a Rondi3D?</h2>
  <ul class="ps-3">
    <li class="mb-1"><strong>Acabamento superior</strong>: peças limpas, tolerâncias revisadas e aspecto profissional.</li>
    <li class="mb-1"><strong>Tecnologia certa</strong>: FDM para resistência e escala; <strong>resina</strong> para detalhes finos.</li>
    <li class="mb-1"><strong>Prazos realistas</strong>: comunicação direta, sem promessas vazias.</li>
    <li class="mb-1"><strong>Experiência em boardgames</strong>: inserts que realmente <em>organizam</em> e <em>protegem</em> (não só “encaixam”).</li>
    <li class="mb-1"><strong>Atendimento próximo</strong>: você fala direto com quem imprime — sem intermediários.</li>
  </ul>
</section>

<!-- MATERIAIS -->
<section class="mb-4">
  <h2 class="h4 mb-3">Materiais & Qualidade</h2>
  <p class="mb-2">
    Trabalhamos com <strong>PLA, PETG e Resina</strong>. Indicamos o material certo para cada uso, avaliando resistência, temperatura, acabamento e custo.
  </p>
  <p class="mb-0">
    Ajustamos <em>infill</em>, paredes, camadas e orientações para maximizar a performance da sua peça em uso real.
  </p>
</section>

<!-- CHAMADAS -->
<section class="mb-5">
  <div class="d-flex flex-wrap gap-2">
    <a class="btn btn-success" href="https://wa.me/5585981636032?text=Oi%20Rondi%2C%20quero%20um%20or%C3%A7amento%20via%20site." target="_blank" rel="noopener">
      Falar no WhatsApp
    </a>
    <a class="btn btn-primary" href="calculadora.php">Calcular meu projeto</a>
    <?php if (current_user()): ?>
      <a class="btn btn-outline-dark" href="meus-orcamentos.php">Acompanhar meus orçamentos</a>
    <?php else: ?>
      <a class="btn btn-outline-dark" href="signup.php">Criar minha conta</a>
    <?php endif; ?>
  </div>
  <p class="text-muted small mt-2 mb-0">
    Missão: levar a impressão 3D além do protótipo, com peças prontas para uso — bonitas, funcionais e feitas sob medida.
  </p>
</section>

<?php include __DIR__.'/includes/footer.php'; ?>
