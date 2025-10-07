<?php
require __DIR__.'/config.php'; // ajuste o caminho se necessário
try {
  $pdo = db();
  $row = $pdo->query("SELECT NOW() AS now, @@version AS v, @@port AS p")->fetch();
  echo "OK! Conectado<br>Hora: {$row['now']}<br>Versão: {$row['v']}<br>Porta: {$row['p']}";
} catch (Throwable $e) {
  http_response_code(500);
  echo "Falha na conexão: ".$e->getMessage();
}
