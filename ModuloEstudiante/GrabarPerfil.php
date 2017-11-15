<?php session_start();
require_once("../funciones/funciones.php");
$ip = getRealIP();
$codigoA = $_SESSION['codigoUsuario'];
if(!isset($codigoA))exit('No se encontro codigo de alumno!.');
if($_REQUEST['pin']!=base64_encode($ip))exit("Pin incorrecto".base64_encode($ip));

require_once("../funciones/conexion.php");
//-----------------------

$consulta = "SELECT * FROM perfiles LIMIT 1";
$resultado = $mysqli->query($consulta) or die ("* ERROR AL CONSULTYA HOJA VIDA *". $mysqli->error);
while($registro = $resultado->fetch_assoc()){
	$columnsExist = $registro;
}
$consulta = "show columns from perfiles";
$resultado = $mysqli->query($consulta) or die ("* ERROR AL CONSULTYA HOJA VIDA *". $mysqli->error);
while($registro = $resultado->fetch_assoc()){
	$columnsExist[$registro['Field']] = $registro['Field'];
}
$columnsExist = array_keys($columnsExist);

$campos = 'codigo';
$valores = "'$codigoA'";
$duplica = '';
foreach($_REQUEST as $colum => $valor){
	if(in_array($colum,$columnsExist) && $colum != 'codigo'){
		if($valor =='-')$valor='';
		if(ValidarCadena($valor)!=1)exit("2;Error");
		$campos .= ", $colum";
		$valores .= ", '$valor'";
		$duplica .= ", $colum = '$valor'";
		$n++;
	}else echo "No existe: ".$colum.'<hr>';
}
$tabla = 'perfiles';
$duplica = substr($duplica,1);
$campos = "($campos)";
$variables = "($valores)";
$duplicados = $duplica;
if($n> 0)agregar($tabla,$campos, $variables,$duplicados);
echo 'La informacion se Guardo satisfactoriamente.';
$mysqli->close();
echo 'Ok';
?>
