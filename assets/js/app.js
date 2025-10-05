// Filtro de posts + modal simples (DOM)
document.addEventListener('DOMContentLoaded', () => {
  // Filtro
  const input = document.getElementById('searchInput');
  const cards = [...document.querySelectorAll('#posts [data-tags]')];
  const count = document.getElementById('resultCount');

  function applyFilter(term){
    const q = (term || '').toLowerCase().trim();
    let visible = 0;
    cards.forEach(card => {
      const tags = (card.getAttribute('data-tags') || '').toLowerCase();
      const show = q === '' || tags.includes(q);
      card.style.display = show ? '' : 'none';
      if (show) visible++;
    });
    if (count) count.textContent = `${visible} resultado${visible === 1 ? '' : 's'}`;
  }
  if (input) {
    input.addEventListener('input', e => applyFilter(e.target.value));
    applyFilter('');
  }

  // Modal Lite
  function openModal(sel){
    const el = document.querySelector(sel);
    if (!el) return;
    el.hidden = false;
    el.querySelector('input,button,.modal-lite__close')?.focus();
  }
  function closeModal(sel){
    const el = document.querySelector(sel);
    if (!el) return;
    el.hidden = true;
  }
  document.addEventListener('click', (e) => {
    const openSel = e.target.closest('[data-open]')?.getAttribute('data-open');
    const closeSel = e.target.closest('[data-close]')?.getAttribute('data-close');
    if (openSel){ e.preventDefault(); openModal(openSel); }
    if (closeSel){ e.preventDefault(); closeModal(closeSel); }
  });
});
