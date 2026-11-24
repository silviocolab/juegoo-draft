<?php
// CORS: solo si el visor se sirve desde otro origen (otra IP o puerto). Poné su dirección exacta.
$permitidos = ['http://localhost:5500','http://127.0.0.1:5500'];
$o = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($o, $permitidos, true)) {
  header("Access-Control-Allow-Origin: $o"); header('Vary: Origin'); header('Access-Control-Allow-Credentials: true');
  header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token'); header('Access-Control-Allow-Methods: GET,POST,DELETE,OPTIONS');
}
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') { http_response_code(204); exit; }
const WS_SECRETO = 'cambiame';                 // el mismo valor que WS_SECRETO del servidor Node
const WS_INTERNO = 'http://127.0.0.1:8081';    // el servidor de avisos corre en este mismo equipo
// Cambiá usuario/clave según tu MySQL (XAMPP: root sin clave)
session_set_cookie_params(['httponly'=>true,'samesite'=>'Lax','secure'=>!empty($_SERVER['HTTPS'])]);
session_start();
header('Content-Type: application/json; charset=utf-8');
$db = new PDO('mysql:host=localhost;dbname=draft7a0;charset=utf8mb4','root','',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
function out($d,$c=200){ http_response_code($c); echo json_encode($d); exit; }
function body(){ return json_decode(file_get_contents('php://input'),true) ?: []; }
function usuario(){ global $db; if(empty($_SESSION['uid'])) return null;
  $s=$db->prepare('SELECT id,alias,rol FROM usuarios WHERE id=?'); $s->execute([$_SESSION['uid']]); $r=$s->fetch(); if(!$r) return null;
  if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32));
  $r['csrf']=$_SESSION['csrf']; return $r; }
// CSRF: las operaciones que cambian datos exigen el token de la sesión en el header X-CSRF-Token
function exigirCsrf(){ $t=$_SERVER['HTTP_X_CSRF_TOKEN']??'';
  if(empty($_SESSION['csrf'])||!hash_equals($_SESSION['csrf'],$t)) out(['error'=>'Token CSRF inválido'],403); }
// Avisa a los visores que cambió el ranking. Si falla, el guardado igual vale.
function avisar($juegoId){
  $ctx=stream_context_create(['http'=>['method'=>'POST','header'=>"Content-Type: application/json\r\nx-secreto: ".WS_SECRETO."\r\n",
    'content'=>json_encode(['juegoId'=>(int)$juegoId]),'timeout'=>1,'ignore_errors'=>true]]);
  @file_get_contents(WS_INTERNO.'/notificar',false,$ctx);
}
