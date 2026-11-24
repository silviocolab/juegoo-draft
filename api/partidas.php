<?php
require 'db.php';
$u=usuario(); if(!$u) out(['error'=>'Iniciá sesión'],401);   // la identidad sale de la sesión
$m=$_SERVER['REQUEST_METHOD'];
if($m==='POST'||$m==='DELETE') exigirCsrf();   // protección CSRF en lo que modifica datos
if($m==='POST'){
  $b=body(); $j=$b['juegoId']??null; $p=$b['puntos']??null; $d=$b['duracionSegundos']??null;
  $s=$db->prepare('SELECT 1 FROM juegos WHERE id=?'); $s->execute([(int)$j]);
  if(!is_int($j)||!is_int($p)||!is_int($d)||!$s->fetch()||$p<0||$p>1500||$d<1||$d>7200) out(['error'=>'Resultado inválido'],400);
  $db->prepare('INSERT INTO partidas(usuario_id,juego_id,puntos,duracion_segundos) VALUES(?,?,?,?)')->execute([$u['id'],$j,$p,$d]);
  $id=(int)$db->lastInsertId(); avisar($j); out(['id'=>$id],201);   // se avisa recién con la base confirmada
}
if($m==='DELETE'){   // DELETE api/partidas.php?id=84
  if($u['rol']!=='admin') out(['error'=>'Solo administradores'],403);
  $id=(int)($_GET['id']??0); $s=$db->prepare('SELECT juego_id FROM partidas WHERE id=?'); $s->execute([$id]); $r=$s->fetch();
  if(!$r) out(['error'=>'La partida no existe'],404);
  $db->prepare('DELETE FROM partidas WHERE id=?')->execute([$id]); avisar($r['juego_id']); out(['ok'=>true]);
}
if($m==='GET'){   // GET api/partidas.php?juegoId=1  (solo admin: lista para poder borrar)
  if($u['rol']!=='admin') out(['error'=>'Solo administradores'],403);
  $s=$db->prepare('SELECT p.id,u.alias,p.puntos,p.creada FROM partidas p JOIN usuarios u ON u.id=p.usuario_id WHERE p.juego_id=? ORDER BY p.creada DESC LIMIT 50');
  $s->execute([(int)($_GET['juegoId']??1)]); out($s->fetchAll());
}
out(['error'=>'Método no permitido'],405);
