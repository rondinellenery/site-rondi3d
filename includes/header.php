<?php
// /includes/header.php
require_once __DIR__ . '/../config.php';
$user = current_user();

// dados do whatsapp
$fone_visivel = '(85) 98163-6032';
$fone_num     = preg_replace('/\D+/', '', $fone_visivel);
$wa_link      = "https://wa.me/55{$fone_num}?text=Ol%C3%A1%2C%20vim%20pelo%20site%20Rondi3D%20e%20gostaria%20de%20um%20or%C3%A7amento.";
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title><?= isset($title) ? h($title).' · ' : '' ?>Rondi3D</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

  <!-- Seu CSS -->
  <link rel="stylesheet" href="<?= h(BASE_URL) ?>assets/css/styles.css">
</head>
<body>

<!-- Topbar -->
<div class="topbar">
  <div class="site-container d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
      <span class="phone-badge"><span class="phone-ico">☎</span> <?= h($fone_visivel) ?></span>
      <a class="btn btn-wa btn-sm" href="<?= h($wa_link) ?>" target="_blank" rel="noopener">WhatsApp</a>
    </div>
    <div class="small text-light">
      Orçamentos rápidos — <a class="link-light text-decoration-underline" href="<?= h(BASE_URL) ?>novo-orcamento.php">Pedir agora</a>
    </div>
  </div>
</div>

<!-- Header / Nav -->
<header class="header py-2">
  <div class="site-container d-flex align-items-center justify-content-between">
    <a class="d-flex align-items-center gap-2 text-decoration-none" href="<?= h(BASE_URL) ?>index.php">
      <img src="<?= h(BASE_URL) ?>assets/img/logo.png" alt="Rondi3D" height="26">
      <strong class="text-dark">Rondi3D</strong>
    </a>

    <nav class="d-flex align-items-center gap-3 main-menu">
      <a class="nav-link" href="<?= h(BASE_URL) ?>index.php">Home</a>
      <a class="nav-link" href="<?= h(BASE_URL) ?>quem-somos.php">Quem Somos</a>
      <a class="nav-link" href="<?= h(BASE_URL) ?>posts.php">Artigos</a>
      <a class="nav-link" href="<?= h(BASE_URL) ?>calculadora.php">Calculadora 3D</a>
      <a class="nav-link" href="<?= h(BASE_URL) ?>meus-orcamentos.php">Meus Orçamentos</a>

      <?php if (is_admin()): ?>
        <a class="nav-link text-danger fw-semibold" href="<?= h(BASE_URL) ?>admins_orcamentos.php">Admin</a>
        <a class="nav-link text-danger fw-semibold" href="<?= h(BASE_URL) ?>admin_posts.php">Posts (Admin)</a>
      <?php endif; ?>

      <?php if ($user): ?>
        <span class="small text-muted d-none d-md-inline">Olá, <?= h($user['name']) ?></span>
        <a class="btn btn-outline-secondary btn-sm" href="<?= h(BASE_URL) ?>logout.php">Sair</a>
      <?php else: ?>
        <a class="btn btn-outline-primary btn-sm" href="<?= h(BASE_URL) ?>login.php">Entrar / Cadastrar-se</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<!-- Subnav -->
<div class="mainnav">
  <div class="site-container d-flex align-items-center justify-content-between">
    <div class="small text-muted">
      Impressão FDM • Resina • Modelagem 3D • Protótipos • Artesanato e Esculturas • Projetos Acadêmicos
    </div>
    <form action="<?= h(BASE_URL) ?>posts.php" method="get" class="d-none d-md-block">
      <input class="form-control form-control-sm" type="text" name="q" placeholder="Buscar artigos…">
    </form>
  </div>
</div>

<!-- CONTEÚDO -->
<main class="site-container py-4">
