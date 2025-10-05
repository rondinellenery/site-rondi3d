<?php
// Garante helpers e sessão
require_once __DIR__ . '/../config.php';

if (!isset($title)) { $title = "Rondi3D — Impressão 3D & Geek"; }
$current = basename($_SERVER['SCRIPT_NAME']);
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= h($title) ?></title>

  <!-- SEO / Social -->
  <meta name="description" content="Impressão 3D sob medida: FDM, resina, modelagem e acessórios para boardgames.">
  <meta property="og:title" content="Rondi3D — Impressão 3D & Geek">
  <meta property="og:description" content="Peças 3D com acabamento de loja.">
  <meta property="og:image" content="assets/img/sobre.png">
  <meta name="theme-color" content="#57c2ae">
  <link rel="icon" href="assets/img/logo.png">

  <!-- Bootstrap -->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
    crossorigin="anonymous"
  />
  <!-- CSS do projeto -->
  <link rel="stylesheet" href="assets/css/styles.css?v=4">
</head>
<body>

<!-- Cabeçalho com marca -->
<header class="header py-2">
  <div class="container d-flex align-items-center justify-content-between">
    <a href="index.php" class="brand d-flex align-items-center gap-2 text-decoration-none">
      <img src="assets/img/logo.png" alt="Logotipo Rondi3D" width="36" height="36">
      <span class="fw-bold text-dark">Rondi3D</span>
    </a>
  </div>

  <!-- Faixa de contato (telefone + Whats) e sessão (Entrar/Sair) -->
  <div class="contact-strip">
    <div class="container d-flex flex-wrap align-items-center justify-content-between gap-2">
      <div class="contacts d-flex align-items-center gap-3">
        <span class="contact-item">📞 (85) 98163-6032</span>
        <a class="btn-whats" target="_blank" rel="noopener"
           href="https://wa.me/5585981636032?text=Oi%20Rondi%2C%20quero%20um%20or%C3%A7amento%20via%20site.">
          WhatsApp
        </a>
      </div>

      <div class="session-links d-flex align-items-center gap-3">
        <?php if (current_user()): ?>
          <span class="small text-dark">Olá, <?= h(current_user()['name']) ?></span>
          <a class="link-dark text-decoration-none" href="logout.php">Sair</a>
        <?php else: ?>
          <a class="link-dark text-decoration-none" href="login.php">Entrar</a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Barra arredondada verde (menu principal estilo "pílula") -->
  <nav class="pillnav my-2">
    <div class="container">
      <div class="pillnav-inner">
        <a href="quem-somos.php" class="<?= $current==='quem-somos.php'?'active':'' ?>">Quem Somos</a>
        <span class="sep">|</span>
        <a href="meus-orcamentos.php" class="<?= $current==='meus-orcamentos.php'?'active':'' ?>">Meus Orçamentos</a>
        <span class="sep">|</span>
        <a href="pedidodeorcamento.php" class="<?= $current==='pedidodeorcamento.php'?'active':'' ?>">Fale Conosco</a>
        <span class="sep">|</span>
        <?php if (current_user()): ?>
          <a href="calculadora.php" class="<?= $current==='calculadora.php'?'active':'' ?>">Calculadora 3D</a>
        <?php else: ?>
          <a href="signup.php" class="<?= in_array($current,['signup.php','login.php'])?'active':'' ?>">Cadastre-se</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>
</header>

<main id="conteudo" class="py-4">
  <div class="container">
