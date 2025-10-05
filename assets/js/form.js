// Validação acessível (cliente) do formulário de orçamento
document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('form[data-orcamento]');
  if (!form) return;

  // cria/recicla região de erros
  let alertBox = form.querySelector('.alert[role="alert"]');
  if (!alertBox) {
    alertBox = document.createElement('div');
    alertBox.className = 'alert alert-danger';
    alertBox.setAttribute('role', 'alert');
    alertBox.setAttribute('tabindex', '-1');
    alertBox.hidden = true;
    alertBox.innerHTML = `<strong>Corrija os campos:</strong><ul id="form-errors" class="mb-0"></ul>`;
    form.prepend(alertBox);
  }
  const errorList = alertBox.querySelector('#form-errors');

  function showErrors(msgs){
    errorList.innerHTML = msgs.map(m => `<li>${m}</li>`).join("");
    alertBox.hidden = msgs.length === 0;
    if (msgs.length) alertBox.focus();
  }

  form.addEventListener('submit', (e) => {
    const msgs = [];
    const nome  = form.querySelector('#nome');
    const email = form.querySelector('#email');
    const msg   = form.querySelector('#mensagem');

    if (!nome || nome.value.trim().length < 2) msgs.push("Informe seu nome.");
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) msgs.push("E-mail inválido.");
    if (!msg || msg.value.trim().length < 10) msgs.push("Descreva melhor o que precisa (mín. 10 caracteres).");

    if (msgs.length){
      e.preventDefault();
      showErrors(msgs);
      (nome.value.trim().length < 2 ? nome : (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value) ? email : msg)).focus();
    } else {
      showErrors([]);
    }
  });
});
