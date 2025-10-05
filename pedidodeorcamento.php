<?php
  $title = "Rondi3D — Pedido de orçamento";
  include __DIR__."/includes/header.php";

  $ok   = isset($_GET['ok']);
  $errs = isset($_GET['err']) ? explode(',', $_GET['err']) : [];

  // Reaproveitar valores após erro
  $old = [
    'nome'     => $_GET['nome']     ?? '',
    'email'    => $_GET['email']    ?? '',
    'mensagem' => $_GET['mensagem'] ?? '',
    'material' => $_GET['material'] ?? 'Indefinido',
    'prazo'    => $_GET['prazo']    ?? '',
  ];
?>

<section class="mb-4">
  <h1 class="h3 mb-3">Pedido de orçamento</h1>
  <p class="text-muted">Descreva sua peça/acessório. Respondemos geralmente no mesmo dia útil.</p>

  <?php if ($ok): ?>
    <div class="alert alert-success" role="status">
      Recebemos seu pedido! Em breve entraremos em contato pelo e-mail informado.
    </div>
  <?php endif; ?>

  <?php if ($errs): ?>
    <div class="alert alert-danger" role="alert" tabindex="-1">
      <strong>Corrija os campos:</strong>
      <ul class="mb-0">
        <?php foreach ($errs as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form data-orcamento action="send.php" method="post" novalidate class="row g-3">
    <div class="col-md-6">
      <label class="form-label" for="nome">Nome</label>
      <input
        class="form-control<?= in_array('nome', $errs) ? ' is-invalid' : '' ?>"
        id="nome" name="nome" type="text" required
        value="<?= htmlspecialchars($old['nome']) ?>"
        aria-describedby="nomeHelp"
      >
      <div id="nomeHelp" class="form-text">Como devemos te chamar?</div>
    </div>

    <div class="col-md-6">
      <label class="form-label" for="email">E-mail</label>
      <input
        class="form-control<?= in_array('email', $errs) ? ' is-invalid' : '' ?>"
        id="email" name="email" type="email" required
        value="<?= htmlspecialchars($old['email']) ?>"
        aria-describedby="emailHelp"
      >
      <div id="emailHelp" class="form-text">Seu orçamento será respondido por aqui.</div>
    </div>

    <div class="col-12">
      <label class="form-label" for="mensagem">Descrição do projeto</label>
      <textarea
        class="form-control<?= in_array('mensagem', $errs) ? ' is-invalid' : '' ?>"
        id="mensagem" name="mensagem" rows="5" required
        placeholder="Material, tamanho aproximado, prazo desejado…"
        aria-describedby="mensagemHelp"
      ><?= htmlspecialchars($old['mensagem']) ?></textarea>
      <div id="mensagemHelp" class="form-text">Quanto mais detalhes, mais preciso é o orçamento.</div>
    </div>

    <div class="col-sm-6">
      <label class="form-label" for="material">Material desejado</label>
      <select id="material" name="material" class="form-select">
        <?php foreach (['Indefinido','PLA','PETG','Resina'] as $op): ?>
          <option <?= $old['material']===$op ? 'selected' : '' ?>><?= $op ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-sm-6">
      <label class="form-label" for="prazo">Prazo ideal</label>
      <input id="prazo" name="prazo" type="date" class="form-control" value="<?= htmlspecialchars($old['prazo']) ?>">
    </div>

    <div class="col-12 d-flex gap-2">
      <button class="btn btn-primary px-4" type="submit">Enviar orçamento</button>
      <a class="btn btn-success" target="_blank" rel="noopener"
         href="https://wa.me/5585981636032?text=Oi%20Rondi!%20Quero%20um%20or%C3%A7amento%20de%20pe%C3%A7a%203D.">
        Falar no WhatsApp
      </a>
    </div>
  </form>
</section>

<!-- Validação do formulário -->
<script defer src="assets/js/form.js?v=2"></script>

<!-- Limpa o formulário após sucesso (UX) -->
<?php if ($ok): ?>
<script>
  document.addEventListener('DOMContentLoaded', ()=> {
    document.querySelector('form[data-orcamento]')?.reset();
  });
</script>
<?php endif; ?>

<?php include __DIR__."/includes/footer.php"; ?>
