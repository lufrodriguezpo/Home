<?PHP
session_start();
require_once("../funciones/funciones.php");
$ip = getRealIP();
/*Validacion de session*/
if($ip != $_SESSION['ip'])exit("No seguro procedencia");
if(!isset($_SESSION["access_token"]))exit("No seguro su ingreso");
$nivel =$_SESSION['nivel'];
if((int)$nivel != 1)exit("No seguro su rol");
if($_REQUEST['pin']!=base64_encode($ip))exit("Pin incorrecto");

$anio = $_SESSION['anio'];
$sems = $_SESSION['semestre'];
$codarea = base64_decode($_REQUEST['codarea']);
$fechas = base64_decode($_REQUEST['fechas']);
$monitores = base64_decode($_REQUEST['monitores']);
$lugar = base64_decode($_REQUEST['lugar']);
if(ValidarCadena($codarea)!=1)exit("2;Error");
if(ValidarCadena($fechas)!=1)exit("2;Error");
if(ValidarCadena($monitores)!=1)exit("2;Error");
if(ValidarCadena($lugar)!=1)exit("2;Error");
require_once("../funciones/conexion.php");
$fechas = explode(',',$fechas);
print_r($fechas);
foreach($fechas as $ind => $fecha){
	if($fecha != ''){
		$fecha = explode('-',$fecha);
		$mes = str_pad($fecha[0],2,'0',STR_PAD_LEFT);
		$dia = str_pad($fecha[1],2,'0',STR_PAD_LEFT);
		$hora = str_pad($fecha[2],2,'0',STR_PAD_LEFT);
		$insertar = "INSERT INTO horariosambientes (anio,sems,mes,dia,hora,codarea,monitores,lugar) VALUES ('$anio','$sems','$mes','$dia','$hora','$codarea','$monitores','$lugar') ON DUPLICATE KEY UPDATE monitores='$monitores',lugar = '$lugar'";//Nuevo
		$resultado=$mysqli->query($insertar) or die("Connection failed: " . $mysqli->error.'- '.$mysqli->close().'Ok.');
	}
}
$mysqli->close();
echo 'Ok';
?>