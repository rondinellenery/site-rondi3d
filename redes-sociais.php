<?php
// redes-sociais.php — página institucional com links das redes ativas
require __DIR__ . '/config.php';
$title = 'Redes sociais';

// imagem de topo (pode trocar depois)
$heroImg = BASE_URL . 'assets/img/logorede.png';

// seus links (somente redes ativas)
$links = [
  'instagram'  => 'https://www.instagram.com/rondi3d',
  'linkedin'   => 'https://www.linkedin.com/in/rondinellenery/',
  'tiktok'     => 'https://www.tiktok.com/@rondi3d',
  'googlemaps' => 'https://www.google.com/maps/place/Impressao+3D+-+Rondi3d+-+instagram/@-3.7852406,-38.5860126,17.73z/data=!4m6!3m5!1s0x7c74dee5f1f95d3:0xbcd5aaa17f727fd9!8m2!3d-3.7842917!4d-38.5858969!16s%2Fg%2F11trxjjkcm?entry=ttu&g_ep=EgoyMDI1MTAwNC4wIKXMDSoASAFQAw%3D%3D',
];

include __DIR__ . '/includes/header.php';
?>

<style>
/* ===== estilos só desta página (escopados) ===== */
.rs-hero{background:#f3f4f6;border:1px solid #e5e7eb;border-radius:16px;padding:24px}
@media (min-width:768px){ .rs-hero{padding:28px 32px} }
.rs-hero h1{font-weight:800;letter-spacing:.2px}
.rs-lead{color:#475569}

.rs-figure img{
  width:100%; height:auto; border-radius:12px; object-fit:cover;
  aspect-ratio: 4/3;
  box-shadow: 0 8px 30px rgba(2,6,23,.08);
}

/* botões arredondados das redes */
.rs-icons{display:flex; gap:12px; flex-wrap:wrap}
.rs-icon{
  width:48px; height:48px; display:inline-flex; align-items:center; justify-content:center;
  border-radius:999px; background:#fff; border:1px solid #e5e7eb; text-decoration:none;
  box-shadow:0 1px 4px rgba(2,6,23,.05);
  transition:transform .12s ease, box-shadow .12s ease, border-color .12s ease, background .12s ease;
}
.rs-icon:hover{transform:translateY(-1px); box-shadow:0 6px 18px rgba(2,6,23,.08); border-color:#d1d5db}
.rs-icon svg{width:22px; height:22px; display:block}

/* cores das marcas */
.rs-icon.ig svg{fill:#E1306C}       /* Instagram  */
.rs-icon.in svg{fill:#0A66C2}       /* LinkedIn   */
.rs-icon.tt svg{fill:#010101}       /* TikTok base (preto) */
.rs-icon.gm svg{fill:#34A853}       /* Google Maps predominante */

/* detalhe sutil no hover por rede */
.rs-icon.ig:hover{background:rgba(225,48,108,.06)}
.rs-icon.in:hover{background:rgba(10,102,194,.06)}
.rs-icon.tt:hover{background:rgba(1,1,1,.04)}
.rs-icon.gm:hover{background:rgba(52,168,83,.06)}
</style>

<div class="site-container py-4">
  <section class="rs-hero">
    <div class="row g-4 align-items-center">
      <div class="col-md-6">
        <figure class="rs-figure m-0">
          <img src="<?= h($heroImg) ?>" alt="Rondi3D nas redes sociais">
        </figure>
      </div>

      <div class="col-md-6">
        <header class="mb-3">
          <h1 class="display-6 m-0">Siga-nos nas redes sociais</h1>
        </header>

        <p class="rs-lead mb-3">
          Bastidores, novidades e trabalhos recentes — acompanhe a Rondi3D nos canais oficiais:
        </p>

        <div class="rs-icons mb-2">
          <!-- Instagram -->
          <a class="rs-icon ig" href="<?= h($links['instagram']) ?>" target="_blank" rel="noopener" aria-label="Instagram">
            <!-- Instagram SVG -->
            <svg viewBox="0 0 24 24" role="img"><path d="M12 2.2c3.2 0 3.584.012 4.85.07 1.17.054 1.957.24 2.66.513.72.28 1.33.654 1.93 1.257.602.6.976 1.21 1.257 1.93.273.703.459 1.49.513 2.66.058 1.266.07 1.65.07 4.85s-.012 3.584-.07 4.85c-.054 1.17-.24 1.957-.513 2.66a5.01 5.01 0 0 1-1.257 1.93 5.01 5.01 0 0 1-1.93 1.257c-.703.273-1.49.459-2.66.513-1.266.058-1.65.07-4.85.07s-3.584-.012-4.85-.07c-1.17-.054-1.957-.24-2.66-.513a5.01 5.01 0 0 1-1.93-1.257 5.01 5.01 0 0 1-1.257-1.93c-.273-.703-.459-1.49-.513-2.66C2.212 15.584 2.2 15.2 2.2 12s.012-3.584.07-4.85c.054-1.17.24-1.957.513-2.66.28-.72.654-1.33 1.257-1.93.6-.602 1.21-.976 1.93-1.257.703-.273 1.49-.459 2.66-.513C8.416 2.212 8.8 2.2 12 2.2ZM12 5.8a6.2 6.2 0 1 0 0 12.4 6.2 6.2 0 0 0 0-12.4Zm0 10.2a4 4 0 1 1 0-8 4 4 0 0 1 0 8Zm6.35-11.65a1.45 1.45 0 1 0 0 2.9 1.45 1.45 0 0 0 0-2.9Z"/></svg>
          </a>

          <!-- LinkedIn -->
          <a class="rs-icon in" href="<?= h($links['linkedin']) ?>" target="_blank" rel="noopener" aria-label="LinkedIn">
            <svg viewBox="0 0 24 24" role="img"><path d="M20.45 20.45h-3.56v-5.59c0-1.33-.02-3.05-1.86-3.05-1.86 0-2.14 1.45-2.14 2.95v5.69H9.33V9h3.41v1.56h.05c.47-.9 1.62-1.86 3.33-1.86 3.56 0 4.22 2.35 4.22 5.41v6.34ZM5.34 7.44a2.07 2.07 0 1 1 0-4.14 2.07 2.07 0 0 1 0 4.14ZM7.13 20.45H3.54V9h3.59v11.45Z"/></svg>
          </a>

          <!-- TikTok -->
          <a class="rs-icon tt" href="<?= h($links['tiktok']) ?>" target="_blank" rel="noopener" aria-label="TikTok">
            <!-- logo simplificado em SVG (preto) -->
            <svg viewBox="0 0 24 24" role="img"><path d="M16.5 3.1c.6 1.6 1.8 3 3.3 3.8.7.4 1.5.6 2.3.7v3.2c-1.6-.1-3.1-.6-4.5-1.5-.4-.3-.8-.5-1.1-.8v7.2c0 4-3.2 7.3-7.2 7.3S2.9 19.7 2.9 15.7s3.2-7.2 7.2-7.2c.4 0 .9 0 1.3.1v3.4c-.4-.1-.8-.1-1.3-.1-2.1 0-3.8 1.7-3.8 3.8S8 19.5 10.1 19.5s3.8-1.7 3.8-3.8V2.5h2.6v.6Z"/></svg>
          </a>

          <!-- Google Maps -->
          <a class="rs-icon gm" href="<?= h($links['googlemaps']) ?>" target="_blank" rel="noopener" aria-label="Google Maps">
            <!-- pin/marker simplificado nas cores Google -->
            <svg viewBox="0 0 24 24" role="img">
              <path d="M12 2.2c-3.9 0-7 3.1-7 7 0 5.1 7 12.6 7 12.6s7-7.5 7-12.6c0-3.9-3.1-7-7-7Z" fill="#34A853"/>
              <circle cx="12" cy="9.2" r="3.2" fill="#EA4335"/>
            </svg>
          </a>
        </div>

        <p class="text-muted small mb-0">Clique para abrir em uma nova aba.</p>
      </div>
    </div>
  </section>
</div>

<?php include __DIR__ . '/includes/footer.php';
