<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>

<body>
<?php
    include('conexion.php');
    $conn=conectarse();
    $conalu="SELECT nombres,apellidos,direccion,telefono FROM alumnos WHERE codcolegio='00047' LIMIT 0,50 ";
    $result = mysql_query($conalu,$conn) or die("ERROR EN LA CONSULTA ALUMNOS".mysql_error());
    if (mysql_affected_rows($conn)>0){
        while ($alumno=mysql_fetch_array($result)) {		
		    $reg[0]=$alumno["nombres"];
		    $reg[1]=$alumno["apellidos"];
		    $reg[2]=$alumno["direccion"];
		    $reg[3]=$alumno["telefono"];
		    $data[]=$reg;
		
		}
    } 
    mysql_close($conn);
?>
</body>
</html>
