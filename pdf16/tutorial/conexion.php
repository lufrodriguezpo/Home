<?php 
function Conectarse() { 
$tipo=0; // 0=local 1=remoto
if ($tipo==0) {
   if (!($link=mysql_connect("localhost","root", ""))) { 
      echo "Error al conectar la base de datos...Notas2000"; 
      exit(); 
   } 
   if (!mysql_select_db("Notas2000",$link)) { 
      echo "Error al seleccionar la base de datos...Notas2000"; 
      exit(); 
   }
   return $link; 	   
} else {
      $link=mysql_connect('localhost','intersys_notas','alumnos');
      mysql_select_db('notas2000');
      return $link; 
  }
} 
?> 