<?php
// guia-orcamento-3d.php — Página fixa: Como funciona o orçamento de impressão 3D
require __DIR__ . '/config.php';
$title = 'Como funciona o orçamento de impressão 3D';
include __DIR__ . '/includes/header.php';
?>

<!-- Hero simples -->
<section class="mb-4">
  <div class="p-4 p-md-5 rounded" style="background:#0f172a; color:#e2e8f0">
    <h1 class="h2 mb-2">Como funciona o orçamento de impressão 3D</h1>
    <p class="mb-0">
      Sem mistério: calculamos pelo <strong>material</strong> consumido, <strong>tempo de impressão</strong> e
      <strong>acabamento</strong>. O valor exato sai quando analisamos o <strong>arquivo 3D</strong>.
    </p>
  </div>
</section>

<article class="mx-auto" style="max-width: 880px;">
  <header class="mb-3">
    <p class="text-muted small mb-2">Guia rápido para quem está pedindo orçamento pela primeira vez</p>
  </header>

  <div class="content">
    <blockquote class="border-start ps-3 text-muted" style="border-color:#e5e7eb !important;">
      <p class="mb-1"><em>“Tem tabela de preço ou é só com o modelo?”</em></p>
      <strong>Resposta curta:</strong> não existe tabela fixa. A gente calcula com base no
      <strong>material gasto</strong>, <strong>tempo de impressão</strong> e <strong>acabamento (com ou sem pintura)</strong>.
      E isso só dá pra saber certinho <strong>com o arquivo do modelo em mãos</strong>.
    </blockquote>

    <h2 class="h4 mt-4">Por que não existe tabela única?</h2>
    <p>
      Cada peça é um universo: tamanho, densidade, detalhes finos, tipo de material e até a posição em que ela é impressa
      mudam o consumo e o tempo. Duas peças do mesmo tamanho podem ter valores bem diferentes se uma tiver paredes finas
      e a outra for mais maciça.
    </p>

    <h2 class="h4 mt-4">O que entra no valor do seu orçamento</h2>
    <ul>
      <li><strong>Material (g)</strong>: PLA, PETG, Resina — calculamos o consumo real.</li>
      <li><strong>Tempo de impressão (h)</strong>: depende da complexidade, camada, velocidade e suportes.</li>
      <li><strong>Acabamento</strong>: peça “crua”, lixada, primer, pintura e verniz (se desejar).</li>
      <li><strong>Tipo de uso</strong>: decorativo, funcional ou protótipo (muda material e densidade interna).</li>
      <li><strong>Tamanho e orientações</strong>: peças maiores e/ou com muito suporte consomem mais.</li>
      <li><strong>Prazo</strong>: urgência pode exigir fila prioritária.</li>
      <li><strong>Extras</strong>: colagem, roscas metálicas, imãs, montagem, etc.</li>
    </ul>

    <h2 class="h4 mt-4">Precisa mesmo do arquivo?</h2>
    <p>
      Sim, para um valor <strong>preciso</strong>. Com o arquivo (<code>.STL</code>, <code>.3MF</code> ou <code>.OBJ</code>),
      o software informa <strong>material</strong> e <strong>tempo</strong> que a peça consome.
      Sem o arquivo, podemos dar <strong>uma estimativa</strong> com base em fotos e medidas, mas o valor final pode variar
      quando o modelo chega.
    </p>
    <p class="mb-0">
      <strong>Não tem o arquivo?</strong> Sem problemas! Também fazemos <strong>modelagem 3D sob medida</strong> a partir
      de desenho, foto ou ideia. O custo de modelagem é separado da impressão.
    </p>

    <h2 class="h4 mt-4">Exemplos rápidos (só para entender a lógica)</h2>
    <ul>
      <li><strong>Chaveiro simples (PLA)</strong>: ~8 g, 25 min → valor baixo.(R$ 20,00) a unidade, mas se pedir a quantidade de 10, o valor cai consideravelmente.</li>
      <li><strong>Suporte funcional (PETG)</strong>: ~120 g, 6 h → valor intermediário (R$ 120,00 à R$ 150,00).</li>
      <li><strong>Escultura com pintura</strong>: ~300 g, 12 h + acabamento → valor maior pelo trabalho manual (R$ 380,00 à R$ 550,00).</li>
    </ul>
    <p class="text-muted small">
      * Exemplos ilustrativos — seu caso a gente calcula certinho com o arquivo.
    </p>

    <h2 class="h4 mt-4">Como pedir um orçamento certeiro</h2>
    <ol>
      <li>Envie o arquivo (<code>.STL</code>, <code>.3MF</code>, <code>.OBJ</code>).</li>
      <li>Diga o <strong>tamanho desejado</strong> (se quiser redimensionar).</li>
      <li>Escolha <strong>material</strong> e <strong>cor</strong> (podemos sugerir).</li>
      <li>Informe se quer <strong>acabamento</strong> (lixa/pintura) e se a peça será <strong>funcional</strong>.</li>
      <li>Se tiver <strong>prazo</strong>, avise 😉</li>
    </ol>

    <div class="alert alert-info">
      <strong>Resumo turbo:</strong> sem tabela fixa; valor = <em>material + tempo + acabamento</em>.
      Arquivo = orçamento preciso. Sem arquivo = estimativa. Fazemos modelagem, se precisar.
    </div>

    <div class="d-flex flex-wrap gap-2 mt-4">
      <a class="btn btn-primary" href="<?= h(BASE_URL) ?>novo-orcamento.php">Pedir orçamento</a>
      <a class="btn btn-outline-secondary" href="<?= h(BASE_URL) ?>calculadora.php">Calcular agora</a>
    </div>
  </div>
</article>

<!-- FAQ curto -->
<section class="mx-auto mt-5" style="max-width: 880px;">
  <h2 class="h5 mb-3">Perguntas rápidas</h2>
  <div class="row g-3">
    <div class="col-md-6">
      <div class="border rounded p-3 h-100">
        <strong>Posso mandar só foto/medida?</strong>
        <p class="mb-0">Pode. Passamos uma <em>estimativa</em> e explicamos o que pode variar quando o arquivo chegar.</p>
      </div>
    </div>
    <div class="col-md-6">
      <div class="border rounded p-3 h-100">
        <strong>Fazem modelagem 3D também?</strong>
        <p class="mb-0">Sim! Transformamos sua ideia em um modelo 3D, usando ferramentas 3D ou inteligência artificial. A modelagem é orçada separadamente e de acordo com o tempo e uso da ferramenta.</p>
      </div>
    </div>
    <div class="col-md-6">
      <div class="border rounded p-3 h-100">
        <strong>Você tem peças pronta entrega?</strong>
        <p class="mb-0">Sim! Vamos alimentar a galeria com produtos em estoque na loja virtual em breve.</p>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
