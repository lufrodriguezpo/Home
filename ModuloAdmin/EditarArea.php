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
$edicion = base64_decode($_REQUEST['edicion']);
$codigo = base64_decode($_REQUEST['codigo']);
$codarea = base64_decode($_REQUEST['codarea']);
if(ValidarCadena($edicion)!=1)exit("2;Error");
if(ValidarCadena($codigo)!=1)exit("2;Error");
if(ValidarCadena($codarea)!=1)exit("2;Error");

require_once("../funciones/conexion.php");
$anio = $_SESSION['anio'];
$sems = $_SESSION['semestre'];

$insertar = "UPDATE monitores SET codarea='$edicion' WHERE sems='$sems' AND anio='$anio' AND codarea='$codarea' AND codigo='$codigo'";//Nuevo
$resultado=$mysqli->query($insertar) or die("Connection failed: " . $mysqli->error.'- '.$mysqli->close().'Ok.');	

$mysqli->close();
echo ';*0'.$codigo.';*';
echo 'Ok';
?>