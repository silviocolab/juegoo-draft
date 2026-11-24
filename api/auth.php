<?php
require 'db.php';
$a=$_GET['accion']??'';
if($a==='me'){ $u=usuario(); $u ? out($u) : out(['error'=>'Sin sesión'],401); }
if($a==='logout'){ session_destroy(); out(['ok'=>true]); }
$b=body(); $alias=trim($b['alias']??''); $pass=$b['password']??'';
if($a==='registro'){
  if(!preg_match('/^\w{3,20}$/',$alias)||strlen($pass)<8) out(['error'=>'Alias de 3 a 20 caracteres y contraseña de al menos 8'],400);
  try{ $db->prepare('INSERT INTO usuarios(alias,hash) VALUES(?,?)')->execute([$alias,password_hash($pass,PASSWORD_DEFAULT)]); }
  catch(PDOException $e){ out(['error'=>'Ese alias ya existe'],409); }
  $id=$db->lastInsertId();
} elseif($a==='login'){
  $s=$db->prepare('SELECT id,hash FROM usuarios WHERE alias=?'); $s->execute([$alias]); $r=$s->fetch();
  if(!$r||!password_verify($pass,$r['hash'])) out(['error'=>'Alias o contraseña incorrectos'],401);
  $id=$r['id'];
} else out(['error'=>'Acción inválida'],400);
session_regenerate_id(true); $_SESSION['uid']=(int)$id; $_SESSION['csrf']=bin2hex(random_bytes(32)); out(usuario());
