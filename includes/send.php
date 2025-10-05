<?php
// Coleta e normaliza
$nome     = trim($_POST['nome'] ?? '');
$email    = trim($_POST['email'] ?? '');
$mensagem = trim($_POST['mensagem'] ?? '');
$material = trim($_POST['material'] ?? 'Indefinido');
$prazo    = trim($_POST['prazo'] ?? '');

// Validação servidor
$erros = [];
if ($nome === '' || mb_strlen($nome) < 2) $erros[] = 'nome';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = 'email';
if ($mensagem === '' || mb_strlen($mensagem) < 10) $erros[] = 'mensagem';

// Se houver erros, redireciona mantendo valores
if ($erros) {
  $qs = http_build_query([
    'err'      => implode(',', $erros),
    'nome'     => $nome,
    'email'    => $email,
    'mensagem' => $mensagem,
    'material' => $material,
    'prazo'    => $prazo,
  ]);
  header('Location: pedidodeorcamento.php?'.$qs);
  exit;
}

// (Mock AP1) — enviar e-mail/DB poderia ir aqui
header('Location: pedidodeorcamento.php?ok=1');
exit;
