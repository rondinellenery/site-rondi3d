/* form.js
 * Regras de formulário específicas:
 * - Novo orçamento: exige cálculo prévio e descrição mínima
 * - Login/Signup: validações básicas de campos
 */

(function () {
  var MAX_SIZE = 20 * 1024 * 1024; // 20 MB
  var ALLOWED = ['image/jpeg', 'image/png', 'image/webp'];

  // Util
  function $(sel, root) { return (root || document).querySelector(sel); }
  function getCalc() {
    try {
      var j = sessionStorage.getItem('calc_result');
      return j ? JSON.parse(j) : null;
    } catch (e) { return null; }
  }
  function invalid(el, msg) {
    if (!el) return;
    el.classList.add('is-invalid');
    var fb = el.nextElementSibling;
    if (!fb || !fb.classList.contains('invalid-feedback')) {
      fb = document.createElement('div');
      fb.className = 'invalid-feedback';
      el.parentNode.appendChild(fb);
    }
    fb.textContent = msg || 'Campo inválido.';
  }
  function clearInvalid(form) {
    form.querySelectorAll('.is-invalid').forEach(function (n) {
      n.classList.remove('is-invalid');
    });
  }

  // ====== NOVO ORÇAMENTO ======
  function wireNovoOrcamento() {
    var form = $('#formQuote');
    if (!form) return;

    var desc = form.querySelector('textarea[name="description"]');
    var file = form.querySelector('input[type="file"][name="photo"]');

    form.addEventListener('submit', function (ev) {
      clearInvalid(form);

      // precisa haver cálculo salvo
      var r = getCalc();
      if (!r) {
        ev.preventDefault();
        alert('Calcule antes na página "Calculadora 3D" para enviar o orçamento.');
        return false;
      }

      // valida descrição (mín. 10 chars)
      if (desc && (desc.value || '').trim().length < 10) {
        ev.preventDefault();
        invalid(desc, 'Descreva melhor seu projeto (mín. 10 caracteres).');
        desc.focus();
        return false;
      }

      // valida arquivo (se houver)
      if (file && file.files && file.files[0]) {
        var f = file.files[0];
        if (ALLOWED.indexOf(f.type) === -1) {
          ev.preventDefault();
          invalid(file, 'Formato não suportado. Envie JPG, PNG ou WEBP.');
          return false;
        }
        if (f.size > MAX_SIZE) {
          ev.preventDefault();
          invalid(file, 'Arquivo acima de 20MB.');
          return false;
        }
      }

      // Preenche os hiddens com o resultado da calculadora
      $('#h_weight', form).value     = r.peso ?? 0;
      $('#h_time', form).value       = r.t ?? 0;
      $('#h_price', form).value      = r.finalPrice ?? 0;
      $('#h_breakdown', form).value  = JSON.stringify(r);
    });
  }

  // ====== LOGIN ======
  function wireLogin() {
    var form = document.querySelector('form[data-form="login"]');
    if (!form) return;
    var email = form.querySelector('input[name="email"]');
    var pass  = form.querySelector('input[name="password"]');

    form.addEventListener('submit', function (ev) {
      clearInvalid(form);
      var ok = true;

      if (email && !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email.value.trim())) {
        invalid(email, 'Informe um e-mail válido.');
        ok = false;
      }
      if (pass && pass.value.trim().length < 6) {
        invalid(pass, 'Senha muito curta (mín. 6).');
        ok = false;
      }
      if (!ok) ev.preventDefault();
    });
  }

  // ====== SIGNUP ======
  function wireSignup() {
    var form = document.querySelector('form[data-form="signup"]');
    if (!form) return;

    var name  = form.querySelector('input[name="name"]');
    var email = form.querySelector('input[name="email"]');
    var pass  = form.querySelector('input[name="password"]');

    form.addEventListener('submit', function (ev) {
      clearInvalid(form);
      var ok = true;

      if (name && name.value.trim().length < 2) {
        invalid(name, 'Seu nome está muito curto.');
        ok = false;
      }
      if (email && !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email.value.trim())) {
        invalid(email, 'Informe um e-mail válido.');
        ok = false;
      }
      if (pass && pass.value.trim().length < 6) {
        invalid(pass, 'Defina uma senha com ao menos 6 caracteres.');
        ok = false;
      }

      if (!ok) ev.preventDefault();
    });
  }

  // ====== Init ======
  document.addEventListener('DOMContentLoaded', function () {
    wireNovoOrcamento();
    wireLogin();
    wireSignup();
  });
})();
