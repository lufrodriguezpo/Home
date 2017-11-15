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
$area = base64_decode($_REQUEST['area']);
$codigo = base64_decode($_REQUEST['codigo']);
if(ValidarCadena($area)!=1)exit("2;Error");
if(ValidarCadena($codigo)!=1)exit("2;Error");

require_once("../funciones/conexion.php");
$anio = $_SESSION['anio'];
$sems = $_SESSION['semestre'];

$remplazo = "DELETE FROM areas WHERE codarea='$codigo' AND sems='$sems' AND anio='$anio'";
$resultado=$mysqli->query($remplazo) or die("No seguro - ".$mysqli->close().'Ok.');
	
$mysqli->close();
echo 'Ok';
?>