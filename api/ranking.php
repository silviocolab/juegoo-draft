<?php
require 'db.php';
$j=(int)($_GET['juegoId']??0);
$s=$db->prepare('SELECT 1 FROM juegos WHERE id=?'); $s->execute([$j]);
if(!$s->fetch()) out(['error'=>'El juego no existe'],404);
// Mejor puntaje de cada usuario; si empatan, primero quien lo logró antes
$s=$db->prepare('SELECT u.id AS usuarioId,u.alias,m.puntos FROM (SELECT p.usuario_id,p.puntos,MIN(p.creada) AS cuando FROM partidas p WHERE p.juego_id=:j AND p.puntos=(SELECT MAX(q.puntos) FROM partidas q WHERE q.juego_id=p.juego_id AND q.usuario_id=p.usuario_id) GROUP BY p.usuario_id,p.puntos) m JOIN usuarios u ON u.id=m.usuario_id ORDER BY m.puntos DESC,m.cuando ASC LIMIT 20');
$s->execute([':j'=>$j]);
out(array_map(fn($r)=>['usuarioId'=>(int)$r['usuarioId'],'alias'=>$r['alias'],'puntos'=>(int)$r['puntos']],$s->fetchAll()));
