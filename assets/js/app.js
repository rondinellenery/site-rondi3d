/* app.js
 * Utilidades gerais do site Rondi3D.
 * - Marca item ativo do menu automaticamente
 * - Preview de imagem e checagem de tamanho/tipo (inputs file)
 * - Protege contra envio duplo de formulários
 * - Helper para salvar/restaurar resultado da calculadora via sessionStorage
 */

(function () {
  // ====== Menu ativo ======
  function markActiveNav() {
    try {
      var here = location.pathname.split('/').pop() || 'index.php';
      document.querySelectorAll('a.nav-link').forEach(function (a) {
        var href = (a.getAttribute('href') || '').split('?')[0];
        if (href === here) a.classList.add('active');
      });
    } catch (e) {}
  }

  // ====== Preview de imagem + validação de upload ======
  // Regras fixadas pelo projeto:
  var MAX_SIZE = 20 * 1024 * 1024; // 20 MB
  var ALLOWED = ['image/jpeg', 'image/png', 'image/webp'];

  function wireFileInputs() {
    document.querySelectorAll('input[type="file"]').forEach(function (input) {
      input.addEventListener('change', function () {
        var f = input.files && input.files[0];
        if (!f) return;

        // valida tipo
        if (ALLOWED.indexOf(f.type) === -1) {
          alert('Formato não suportado. Envie JPG, PNG ou WEBP.');
          input.value = '';
          return;
        }
        // valida tamanho
        if (f.size > MAX_SIZE) {
          alert('Arquivo acima de 20MB.');
          input.value = '';
          return;
        }

        // preview (se existir um alvo)
        var preview = document.querySelector(input.getAttribute('data-preview')) ||
                      document.querySelector('#preview');
        if (preview) {
          var img = preview.querySelector('img');
          if (!img) {
            img = document.createElement('img');
            img.style.maxWidth = '260px';
            img.style.height = 'auto';
            img.className = 'img-fluid rounded border';
            preview.innerHTML = '';
            preview.appendChild(img);
          }
          var reader = new FileReader();
          reader.onload = function (e) { img.src = e.target.result; };
          reader.readAsDataURL(f);
        }
      });
    });
  }

  // ====== Evita envio duplo ======
  function wireSafeSubmit() {
    document.querySelectorAll('form').forEach(function (form) {
      form.addEventListener('submit', function (ev) {
        if (form.dataset.submitting === '1') {
          ev.preventDefault();
          return false;
        }
        form.dataset.submitting = '1';
        var btn = form.querySelector('button[type="submit"], .btn[type="submit"]');
        if (btn) {
          btn.dataset.originalText = btn.innerHTML;
          btn.innerHTML = 'Enviando…';
          btn.disabled = true;
        }
      });
    });
  }

  // ====== Bridge da Calculadora (sessionStorage) ======
  // calculadora.php deve chamar window.saveCalcResult(obj)
  // onde obj = { peso, t, finalPrice, breakdown, material }
  function exposeCalcHelpers() {
    window.saveCalcResult = function (obj) {
      try {
        if (!obj || typeof obj !== 'object') return;
        sessionStorage.setItem('calc_result', JSON.stringify(obj));
        // feedback opcional
        console.log('Resultado da calculadora salvo.', obj);
      } catch (e) {}
    };
    window.clearCalcResult = function () {
      try { sessionStorage.removeItem('calc_result'); } catch (e) {}
    };
    window.getCalcResult = function () {
      try {
        var j = sessionStorage.getItem('calc_result');
        return j ? JSON.parse(j) : null;
      } catch (e) { return null; }
    };
  }

  // ====== Init ======
  document.addEventListener('DOMContentLoaded', function () {
    markActiveNav();
    wireFileInputs();
    wireSafeSubmit();
    exposeCalcHelpers();
  });
})();

/* ===================================================================
   Drawer Mobile (menu lateral para <992px)
   - Não altera desktop
   - Usa #navToggle, #mobileDrawer, #drawerClose, #drawerBackdrop
   =================================================================== */
(function () {
  const html = document.documentElement;
  const btn = document.getElementById('navToggle');
  const drawer = document.getElementById('mobileDrawer');
  const closeBtn = document.getElementById('drawerClose');
  const backdrop = document.getElementById('drawerBackdrop');

  if (!btn || !drawer) return;

  function openDrawer() {
    drawer.classList.add('open');
    if (backdrop) {
      backdrop.hidden = false;
      requestAnimationFrame(() => backdrop.classList.add('show'));
    }
    btn.setAttribute('aria-expanded', 'true');
    drawer.setAttribute('aria-hidden', 'false');
    html.classList.add('drawer-open'); // anima o hambúrguer
    document.body.style.overflow = 'hidden'; // trava scroll
  }
  function closeDrawer() {
    drawer.classList.remove('open');
    if (backdrop) {
      backdrop.classList.remove('show');
      backdrop.addEventListener('transitionend', () => { backdrop.hidden = true; }, { once: true });
    }
    btn.setAttribute('aria-expanded', 'false');
    drawer.setAttribute('aria-hidden', 'true');
    html.classList.remove('drawer-open');
    document.body.style.overflow = '';
  }

  btn.addEventListener('click', openDrawer);
  if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
  if (backdrop) backdrop.addEventListener('click', closeDrawer);
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeDrawer(); });

  // Se voltar para desktop, garante reset
  const mq = window.matchMedia('(min-width: 992px)');
  mq.addEventListener('change', () => { if (mq.matches) closeDrawer(); });
})();
// Preview simples das imagens da galeria (galeria-view.php)
(function(){
  const modalEl = document.getElementById('imgModal');
  if(!modalEl) return;
  const modalImg = modalEl.querySelector('img');
  document.querySelectorAll('[data-viewer]').forEach(function(img){
    img.addEventListener('click', function(){
      modalImg.src = img.getAttribute('data-src') || img.src;
      const m = new bootstrap.Modal(modalEl);
      m.show();
    });
  });
})();
(function(){
  const modalEl = document.getElementById('imgModal');
  if(!modalEl) return;
  const modalImg = modalEl.querySelector('img');
  document.querySelectorAll('[data-viewer]').forEach(function(img){
    img.addEventListener('click', function(){
      modalImg.src = img.getAttribute('data-src') || img.src;
      const m = new bootstrap.Modal(modalEl);
      m.show();
    });
  });
})();
/* ===================================================================
   Lightbox da galeria:
   - Abre ao clicar na imagem principal (.gallery-view .hero-img)
   - Ajustado à viewport por padrão (contain)
   - Clique na imagem alterna zoom in/out
   - Clique no fundo ou ESC fecha
   =================================================================== */
(function () {
  const hero = document.querySelector('.gallery-view .hero-img');
  const lb   = document.getElementById('lightbox');
  const lbImg= document.getElementById('lbImg');
  const btnX = document.getElementById('lbClose');
  if (!hero || !lb || !lbImg) return;

  function open(src) {
    lb.classList.remove('zoomed');
    lbImg.src = src;
    lb.classList.add('show');
    lb.setAttribute('aria-hidden','false');
    document.body.style.overflow = 'hidden';
  }
  function close() {
    lb.classList.remove('show','zoomed');
    lb.setAttribute('aria-hidden','true');
    lbImg.src = '';
    document.body.style.overflow = '';
  }

  // abre ao clicar na imagem principal
  hero.addEventListener('click', function () {
    const src = hero.getAttribute('data-full') || hero.getAttribute('src');
    if (src) open(src);
  });

  // toggle zoom ao clicar na própria imagem do lightbox
  lbImg.addEventListener('click', function (e) {
    // se já está zoomed, um clique faz zoom-out; se não, zoom-in
    lb.classList.toggle('zoomed');
  });

  // fecha no fundo/ESC/botão
  lb.addEventListener('click', function (e) {
    if (e.target === lb) close();
  });
  btnX && btnX.addEventListener('click', close);
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && lb.classList.contains('show')) close();
  });

  // se você troca a imagem principal ao clicar nas miniaturas,
  // mantenha o atributo data-full atualizado para qualidade máxima:
  document.querySelectorAll('.gallery-view .thumbs img').forEach(function (t) {
    t.addEventListener('click', function () {
      // exemplo de sincronização básica (caso você já tenha código próprio, pode ignorar):
      // const big = t.getAttribute('data-full') || t.src;
      // hero.src = big;
      // hero.setAttribute('data-full', big);
    });
  });
})();
