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
$sems = base64_decode($_REQUEST['sems']);
$anio = base64_decode($_REQUEST['ano']);
if(ValidarCadena($sems)!=1)exit("2;Error");
if(ValidarCadena($anio)!=1)exit("2;Error");
require_once("../funciones/conexion.php");
$tablas = array('areas','monitores','horariospares','horariosambientes','citaspares','citasambientes');
$anio = $_SESSION['anio'];
$sems = $_SESSION['semestre'];
$archivo = array();
foreach($tablas as $ind => $tabla){
	$consulta = "show columns from $tabla";
	$resultado = $mysqli->query($consulta) or die ("* ERROR AL CONSULTA HOJA VIDA *". $mysqli->error);
	$filaN = '';
	while($registro = $resultado->fetch_assoc()){
		$filaN .= ','.$registro['Field'];
	}
	$filaN = '('.substr($filaN,1).')';
	$fInsert = '';
	$consulta = "SELECT * FROM $tabla WHERE anio='$anio' AND sems='$sems'";
	$resultado=$mysqli->query($consulta) or die("No seguro - ". $mysqli->error.'Ok.');
	$nf = 0;
	while($registro = $resultado->fetch_assoc()){
		$valor = utf8_encode($valor);
		$fila = '';
		foreach($registro as $campo => $valor){
			$fila .= ", '$valor' ";
		}
		$fila = '('.substr($fila,1).')';
		$fInsert .= ','.$fila;
		$nf++;
	}
	$fInsert = substr($fInsert,1);
	$inserT = "INSERT INTO $tabla $filaN VALUES $fInsert";
	if($nf>0)$archivo[] = $inserT;
}
$archivo=implode('*/-/*',$archivo);
$archivo = base64_encode($archivo);
$nombre_archivo = 'Respaldo/RE'.$anio.'_'.$sems.'_'.date("d_m_Y_H_i_s").'.txt';
if($farchivo = fopen($nombre_archivo, "a")){
	if(fwrite($farchivo,$archivo))echo 'Ok';
	else exit('4;Error');
	fclose($farchivo);
}else exit('3;Error');
foreach($tablas as $ind => $tabla){	
	$remplazo = "DELETE FROM $tabla WHERE sems='$sems' AND anio='$anio'";
	$resultado=$mysqli->query($remplazo) or die("No seguro - ".$mysqli->close().'Ok.');
}
$mysqli->close();
echo 'Ok';
?>