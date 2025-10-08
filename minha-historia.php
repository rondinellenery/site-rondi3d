<?php
// minha-historia.php — Página fixa “Minha História”
require __DIR__ . '/config.php';
$title = 'Minha história — Do papel ao 3D';

include __DIR__ . '/includes/header.php';
?>

<style>
/* ===== Estilos escopados só desta página ===== */
.story-hero{
  background:linear-gradient(135deg,#0ea5e916 0%, #0ea5e908 40%, transparent 100%);
  border:1px solid #e5e7eb; border-radius:16px; padding:22px 22px;
}
@media (min-width:768px){ .story-hero{padding:28px 34px} }

.story-lead{font-size:1.15rem; color:#334155}
.story-section h2{font-weight:800; letter-spacing:.2px}
.story-quote{
  border-left:4px solid #0ea5e9; padding-left:14px; color:#0f172a; font-style:italic;
  background:#f8fafc; border-radius:6px; padding-top:8px; padding-bottom:8px;
}
.story-meta{color:#64748b}

.story-figure{margin:0}
.story-figure img{
  width:100%; aspect-ratio:16/9; object-fit:cover; border-radius:12px;
  box-shadow:0 8px 28px rgba(2,6,23,.08);
}
.story-cap{font-size:.9rem; color:#64748b; margin-top:6px}

/* Cards de timeline */
.timeline .card{border-radius:14px; border:1px solid #e5e7eb}
.timeline .card-header{
  background:#f1f5f9; border-bottom:1px solid #e5e7eb; font-weight:700;
}

/* Destaques */
.badge-era{background:#0ea5e91a; color:#0369a1; border:1px solid #7dd3fc; font-weight:600}
.callout{
  background:#ecfeff; border:1px solid #a5f3fc; border-radius:12px; padding:14px 16px;
}
</style>

<div class="site-container py-4">

  <!-- HERO -->
  <section class="story-hero mb-4">
    <div class="row g-4 align-items-center">
      <div class="col-md-6">
        <!-- FOTO 01 (troque quando quiser) -->
        <figure class="story-figure">
          <img src="<?= h(BASE_URL) ?>assets/img/historia/hero-01.jpg" alt="Rondi3D — infância analógica e curiosidade" />
          <figcaption class="story-cap">Espaço para uma foto da infância/juventude ou algo que represente tua origem.</figcaption>
        </figure>
      </div>
      <div class="col-md-6">
        <h1 class="display-6 mb-2">Do papel ao 3D: uma história de resiliência e criação</h1>
        <p class="story-lead">
          Nasci em 1985, no meio do mundo analógico que, quinze anos depois, ia se tornar digital. Cresci sem shopping, sem videogame em casa, mas com a TV ligada nos
          <strong>tokusatsus</strong>, <strong>super sentais</strong>, <strong>animes</strong> e <strong>cartoons</strong> que despertaram em mim aquilo que, mais tarde, viraria profissão: transformar histórias em objetos que cabem na mão.
        </p>
        <div class="story-quote mt-3">
          “Quando a gente não tem como comprar, a gente aprende a fazer.”
        </div>
        <div class="story-meta mt-2">Rondinelle Nery · Rondi3D</div>
      </div>
    </div>
  </section>

  <!-- SEÇÃO 1 -->
  <section class="story-section mb-5">
    <div class="d-flex align-items-center gap-2 mb-2">
      <h2 class="h4 m-0">Infância analógica, sonhos gigantes</h2>
      <span class="badge badge-era">Anos 80/90</span>
    </div>
    <p>
      A economia era apertada, o lazer era a televisão, e o primeiro shopping que pisei estava fechado. Ainda assim, o imaginário era imenso.
      Não era só comigo: <em>uma geração inteira no Brasil</em> cresceu assim — aprendendo a improvisar, a consertar, a criar. Muitos de nós viramos
      parte da comunidade <strong>maker</strong>, que hoje impulsiona negócios, carreiras e sonhos por aí.
    </p>
    <!-- FOTO 02 -->
    <figure class="story-figure mt-3">
      <img src="<?= h(BASE_URL) ?>assets/img/historia/hero-02.jpg" alt="Referências de cultura pop que inspiraram a trajetória" />
      <figcaption class="story-cap">Espaço para pôsteres, HQs, brinquedos ou referências que te marcaram.</figcaption>
    </figure>
  </section>

  <!-- SEÇÃO 2 -->
  <section class="story-section mb-5">
    <div class="d-flex align-items-center gap-2 mb-2">
      <h2 class="h4 m-0">Do papel ao 3D: a escola do <em>Papercraft</em></h2>
      <span class="badge badge-era">2011–2016</span>
    </div>
    <p>
      Como <strong>action figures</strong> eram caros, descobri o <strong>papercraft</strong> em 2011 — esculturas 3D de papel. Foi amor à primeira
      tesoura. Estudei <strong>Blender</strong> (desde 2013) até me tornar <strong>designer de papercraft</strong> em 2016. A técnica me deu disciplina,
      domínio de forma e volume, e carinho pelos detalhes. Mas o mercado nem sempre valorizava como eu sonhava. Comecei a me sentir
      desconectado daquilo.
    </p>
    <div class="callout mt-3">
      <strong>Curiosidade:</strong> muita gente na comunidade maker trilhou um caminho parecido — começou com papel, foam, MDF, argila, e migrou para
      <strong>impressão 3D</strong> quando ela ficou mais acessível. O importante é que <em>toda técnica</em> agrega repertório.
    </div>
    <!-- FOTO 03 -->
    <figure class="story-figure mt-3">
      <img src="<?= h(BASE_URL) ?>assets/img/historia/papercraft.jpg" alt="Modelos de papercraft e estudos em Blender" />
      <figcaption class="story-cap">Espaço para fotos dos papercrafts, telas do Blender, moldes e montagens.</figcaption>
    </figure>
  </section>

  <!-- SEÇÃO 3 -->
  <section class="story-section mb-5">
    <div class="d-flex align-items-center gap-2 mb-2">
      <h2 class="h4 m-0">Primeira impressora, primeira batalha</h2>
      <span class="badge badge-era">2020</span>
    </div>
    <p>
      Em 2020 comprei a primeira impressora (Longer LK4 Pro). Um ano terrível para o mundo… e duro na bancada.
      <strong>Frustração atrás de frustração</strong>, peças quebradas, noites em claro e espera de reposição no AliExpress.
      Decidi abandonar. E estava tudo bem — às vezes, a gente pausa para não desistir.
    </p>

    <div class="row g-3 mt-2 timeline">
      <div class="col-md-6">
        <div class="card h-100">
          <div class="card-header">O que aprendi nesse tropeço</div>
          <div class="card-body">
            <ul class="mb-0">
              <li>Nem sempre o problema é você. Às vezes a máquina não ajuda.</li>
              <li>Documentar erro encurta o caminho da próxima tentativa.</li>
              <li>A comunidade (fóruns, grupos) é combustível quando falta gás.</li>
            </ul>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <!-- FOTO 04 -->
        <figure class="story-figure">
          <img src="<?= h(BASE_URL) ?>assets/img/historia/primeira-impressora.jpg" alt="Primeiros testes e falhas de impressão" />
          <figcaption class="story-cap">Espaço para fotos dos testes e primeiras peças problemáticas (faz parte!).</figcaption>
        </figure>
      </div>
    </div>
  </section>

  <!-- SEÇÃO 4 -->
  <section class="story-section mb-5">
    <div class="d-flex align-items-center gap-2 mb-2">
      <h2 class="h4 m-0">A virada: outra máquina, outro jogo</h2>
      <span class="badge badge-era">2021–2023</span>
    </div>
    <p>
      Um ano depois, tentei de novo — agora com uma <strong>Elegoo</strong>. A diferença foi brutal. A qualidade veio, a confiança também,
      e logo a bancada ganhou companhia. Fui de uma impressora para <strong>dez</strong>. Enquanto isso, outras pessoas na comunidade
      maker também prosperavam: <em>o padrão é recomeçar melhor</em>, não perfeito.
    </p>
    <!-- FOTO 05 -->
    <figure class="story-figure mt-3">
      <img src="<?= h(BASE_URL) ?>assets/img/historia/frota.jpg" alt="Crescimento do parque de impressoras 3D" />
      <figcaption class="story-cap">Espaço para foto da bancada/frota de máquinas.</figcaption>
    </figure>
  </section>

  <!-- SEÇÃO 5 -->
  <section class="story-section mb-5">
    <div class="d-flex align-items-center gap-2 mb-2">
      <h2 class="h4 m-0">Quando a vida chamou: demissão e salto</h2>
      <span class="badge badge-era">Julho/2024</span>
    </div>
    <p>
      Depois de 11 anos numa empresa, fui desligado em julho de 2024. A vida me empurrou para o 3D, e eu mergulhei:
      estudo, prática, atendimento, entrega. <strong>O primeiro semestre foi bom</strong>. Voltei ao mercado formal mais tarde,
      mas o 3D não diminuiu. Continuou a crescer.
    </p>
    <div class="callout mt-3">
      <strong>Para quem está passando por isso agora:</strong> dá medo, claro. Mas existe demanda real para quem entrega
      qualidade e comunicação clara. Foque em <em>peças úteis</em>, prazos honestos e bom pós-venda. A comunidade maker brasileira está cheia de exemplos.
    </div>
    <!-- FOTO 06 -->
    <figure class="story-figure mt-3">
      <img src="<?= h(BASE_URL) ?>assets/img/historia/eventos.jpg" alt="Rondi3D em eventos e ativações" />
      <figcaption class="story-cap">Espaço para fotos no SANA, feiras, entregas e bastidores.</figcaption>
    </figure>
  </section>

  <!-- SEÇÃO 6 -->
  <section class="story-section mb-5">
    <div class="d-flex align-items-center gap-2 mb-2">
      <h2 class="h4 m-0">Hoje</h2>
      <span class="badge badge-era">Agora</span>
    </div>
    <p>
      Hoje tenho <strong>máquinas suficientes</strong> para suprir a demanda dos clientes, gerar renda para casa e ainda me divertir
      criando, expondo e trocando com a galera. A cultura pop que me formou continua aqui, mas agora em forma de
      <strong>projetos reais</strong>, que as pessoas tocam, usam e colecionam.
    </p>
    <div class="story-quote">
      “O que começou como falta virou ofício. O que era sonho virou peça.”
    </div>
  </section>

  <!-- SEÇÃO 7: Dicas práticas -->
  <section class="story-section mb-4">
    <h2 class="h5 mb-3">Para quem quer começar (e continuar) no 3D</h2>
    <div class="row g-3">
      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-body">
            <h3 class="h6">1) Comece pequeno, registre tudo</h3>
            <p class="mb-0">Versões, perfis, fotos dos erros e acertos. Documentar te poupa tempo e vira conteúdo.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-body">
            <h3 class="h6">2) Comunidade é alavanca</h3>
            <p class="mb-0">Grupos, fóruns e makers locais encurtam o caminho e abrem portas de trabalho.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-body">
            <h3 class="h6">3) Clareza de proposta</h3>
            <p class="mb-0">Explique seu processo, prazos e limites. Confiança nasce de previsibilidade.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="text-center py-4">
    <a href="<?= h(BASE_URL) ?>novo-orcamento.php" class="btn btn-primary btn-lg me-2">Pedir um orçamento</a>
    <a href="<?= h(BASE_URL) ?>galeria.php" class="btn btn-outline-secondary btn-lg">Ver trabalhos</a>
  </section>

</div>

<?php include __DIR__ . '/includes/footer.php';
