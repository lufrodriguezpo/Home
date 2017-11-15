<?PHP
error_reporting(0);
session_start();
require_once("../funciones/funciones.php");
$ip = getRealIP();
/*Validacion de session*/
if($ip != $_SESSION['ip'])exit("No seguro procedencia");
if(!isset($_SESSION["access_token"]))exit("No seguro su ingreso");
$nivel =$_SESSION['nivel'];
if((int)$nivel != 1)exit("No seguro su rol");
if($_REQUEST['pin']!=base64_encode($ip))exit("Pin incorrecto");
if((int)$_REQUEST['anio']==0)exit("1;Error");
if((int)$_REQUEST['semestre']==0)exit("2;Error");


require_once("../funciones/conexion.php");

$consulta = "show columns from configuracion";
$resultado = $mysqli->query($consulta) or die ("* ERROR AL CONSULTYA HOJA VIDA *". $mysqli->error);
while($registro = $resultado->fetch_assoc()){
	$columnsExist[$registro['Field']] = $registro['Field'];
}
$columnsExist = array_keys($columnsExist);

$campos = '';
$valores = "";
$duplica = '';
foreach($_REQUEST as $colum => $valor){
	if(in_array($colum,$columnsExist) && $colum != 'codigo'){
		if($valor =='-')$valor='';
		$valor = utf8_decode($valor);
		if(ValidarCadena(utf8_decode($valor))!=1)exit($colum."3;Error");
		$_SESSION[$colum]=$valor;
		$campos .= ", $colum";
		$valores .= ", '$valor'";
		$duplica .= ", $colum = '$valor'";
		$n++;
	}else echo "No existe: ".$colum.'<hr>';
}
$tabla = 'configuracion';
$campos = substr($campos,1);
$valores = substr($valores,1);
$duplica = substr($duplica,1);
$campos = "($campos)";
$variables = "($valores)";
$duplicados = $duplica;
if($n> 0){
	if((int)$_REQUEST['activo']==1){
		$remplazo = "UPDATE configuracion SET activo='0' WHERE 1";
		$resultado=$mysqli->query($remplazo) or die("No seguro".$mysqli->connect_error.'- '.$mysqli->close().'Ok.');
	}
	agregar($tabla,$campos, $variables,$duplicados);
}
$mysqli->close();
echo 'Ok';
?>