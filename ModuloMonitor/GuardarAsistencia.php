<?PHP
error_reporting(0);
session_start();
require_once("../funciones/funciones.php");
$ip = getRealIP();
/*Validacion de session*/
if($ip != $_SESSION['ip'])exit("No seguro procedencia");
if(!isset($_SESSION["access_token"]))exit("No seguro su ingreso");
$nivel =$_SESSION['nivel'];
if((int)$nivel >2 || (int)$nivel < 1)exit("No seguro su rol");
if($_REQUEST['pin']!=base64_encode($ip))exit("Pin incorrecto");
//if(ValidarCadena($area)!=1)exit("2;Error");
$anio = $_SESSION['anio'];
$sems = $_SESSION['semestre'];
$codigo = $_SESSION['codigoUsuario'];;
$codarea = base64_decode($_REQUEST['codarea']);
$mes = base64_decode($_REQUEST['mes']);
$dia = base64_decode($_REQUEST['dia']);
$hora = base64_decode($_REQUEST['hora']);
$fechaH = date("Y-m-d H:i:s");
if(ValidarCadena($codarea)!=1)exit("12;Error");
if(ValidarCadena($mes)!=1)exit("22;Error");
if(ValidarCadena($dia)!=1)exit("32;Error");
if(ValidarCadena($hora)!=1)exit("42;Error");
$mes = str_pad($mes,2,'0',STR_PAD_LEFT);
$dia = str_pad($dia,2,'0',STR_PAD_LEFT);
$hora = str_pad($hora,2,'0',STR_PAD_LEFT);
require_once("../funciones/conexion.php");
foreach($_REQUEST as $id => $val){
	if(substr($id,0,3)=='EST'){
		$estado = base64_decode($val);
		$codigo = substr($id,3);
		$estado = explode('-',$estado);
		if(substr($estado[0],3)!=$codigo)exit("54;Error");
		$estado = $estado[1];
		if(ValidarCadena($estado)!=1)exit("52;Error");
		if(ValidarCadena($codigo)!=1)exit("63;Error");
		$remplazar = "UPDATE citasambientes SET estado='$estado', fechares='$fechaH' ";
		$remplazar .= "WHERE anio='$anio' AND sems='$sems' AND codarea='$codarea' AND ";
		$remplazar .= "codalu='$codigo' AND mes='$mes' AND dia ='$dia' AND hora='$hora'";
		echo $remplazar.'----------------';
		$resultado=$mysqli->query($remplazar) or die("Connection failed: " . $mysqli->error.'- '.$mysqli->close().'Ok.');
	}
}
$mysqli->close();
echo 'Ok';
?>