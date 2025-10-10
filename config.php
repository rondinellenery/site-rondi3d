<?php
// ===== DEBUG (opcional) =====
// error_reporting(E_ALL); ini_set('display_errors', 1);

// ===== Config DB =====
define('DB_HOST', 'localhost');
define('DB_PORT', 3306);
define('DB_NAME', 'rondi3d');
define('DB_USER', 'rondi');
define('DB_PASS', '060712Arthur7*');

// ===== Descobre BASE_URL dinamicamente (respeita subpasta, ex.: /site-rondi3d) =====
$__base = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
define('BASE_URL', ($__base === '' ? '/' : $__base . '/'));

// ===== Uploads =====
// ATENÇÃO: config.php está na RAIZ do projeto.
// Portanto, o caminho correto é ./storage/uploads (sem “../”)
define('UPLOAD_DIR', __DIR__ . '/storage/uploads');                 // caminho no disco
define('UPLOAD_URL', rtrim(BASE_URL, '/') . '/storage/uploads');    // URL pública
define('UPLOAD_MAX_BYTES', 20 * 1024 * 1024);                       // 20 MB

// ===== Status do orçamento (ENUM do banco) =====
const BUDGET_STATUSES = ['RECEBIDO','EM_ANALISE','APROVADO','EM_PRODUCAO','CONCLUIDO','CANCELADO'];

// ===== Estado do cálculo =====
const CALC_STATES = ['PENDENTE','ESTIMADO','FINAL'];

// ===== Valores padrão da calculadora (usados quando o cliente só informa peso e tempo) =====
const CALC_DEFAULTS = [
  'price_per_kg'   => 150.0,
  'energy_cost'    => 1.75,
  'power_watts'    => 350.0,
  'failure_rate'   => 20.0,
  'printer_cost'   => 5000.0,
  'daily_usage_h'  => 5.0,
  'printer_life_m' => 8.0,
  'maintenance'    => 0.0,
  'freight'        => 0.0,
  'labor_mode'     => 'percent', // 'percent' | 'hourly'
  'labor_percent'  => 50.0,
  'labor_rate'     => 0.0,
  'profit_percent' => 50.0,
  'tax_percent'    => 5.0
];

// ===== PDO =====
function db() {
  static $pdo;
  if (!$pdo) {
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
  }
  return $pdo;
}

// ===== Sessão =====
if (session_status() === PHP_SESSION_NONE) {
  session_set_cookie_params(['httponly'=>true,'samesite'=>'Lax']);
  session_start();
}

// ===== Helpers de sessão =====
function current_user(){ return $_SESSION['user'] ?? null; }

function require_login(){
  if (!current_user()){
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/';
    header('Location: ' . BASE_URL . 'login.php'); // usa BASE_URL dinâmico
    exit;
  }
}

function is_admin(){ return current_user() && (int)current_user()['is_admin'] === 1; }
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// === Somente admin (bloqueia a página inteira se não for) ===
function require_admin(){
  if (!is_admin()){
    http_response_code(403);
    include __DIR__ . '/includes/header.php';
    echo '<div class="container py-4"><div class="alert alert-danger">Acesso restrito ao administrador.</div></div>';
    include __DIR__ . '/includes/footer.php';
    exit;
  }
}

// === Campo hidden pronto com token CSRF para formularios POST ===
function csrf_input(){
  return '<input type="hidden" name="csrf" value="'.h(csrf_token()).'">';
}

// ===== CSRF =====
function csrf_token(){
  if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
  return $_SESSION['csrf'];
}
function require_csrf(){
  $ok = isset($_POST['csrf']) && hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf']);
  if (!$ok){ http_response_code(400); exit('CSRF inválido.'); }
}
