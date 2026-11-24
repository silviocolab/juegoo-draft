<?php
require 'db.php';
$e=$db->query('SELECT id,pais,anio FROM equipos')->fetchAll();
$j=$db->query('SELECT id,equipo_id,nombre,posicion,rating FROM jugadores')->fetchAll();
foreach($e as &$q){ $q['jugadores']=array_values(array_filter($j,fn($x)=>$x['equipo_id']==$q['id'])); }
out($e);
