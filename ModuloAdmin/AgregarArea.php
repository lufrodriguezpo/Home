<?PHP
error_reporting(0);
session_start();
require_once("../funciones/funciones.php");
$ip = getRealIP();
/*Validacion de session*/
if($ip != $_SESSION['ip'])exit("No seguro procedencia");
if(!isset($_SESSION["access_token"]))exit("No seguro su ingreso");
$nivel =$_SESSION['nivel'];
if((int)$nivel >3 || (int)$nivel < 1)exit("No seguro su rol");
if($_REQUEST['pin']!=base64_encode($ip))exit("Pin incorrecto");
$area = base64_decode($_REQUEST['area']);
$area = $area;
echo $area;
if(ValidarCadena($area)!=1)exit("2;Error");

require_once("../funciones/conexion.php");
$anio = $_SESSION['anio'];
$sems = $_SESSION['semestre'];
$consulta="SELECT MAX(codarea) AS maximo FROM areas WHERE sems='$sems' AND anio='$anio'";
$resultado=$mysqli->query($consulta) or die("Fail: ". $mysqli->error.$mysqli->close().'Ok');
$registro=$resultado->fetch_assoc();
$codigo = (int)$registro['maximo']+1;
$codigo = str_pad($codigo,2,'0',STR_PAD_LEFT);

$insertar = "INSERT INTO areas (anio,sems,codarea,area) VALUES ('$anio','$sems','$codigo','$area') ON DUPLICATE KEY UPDATE area='$area'";//Nuevo
$resultado=$mysqli->query($insertar) or die("Connection failed: " . $mysqli->error.'- '.$mysqli->close().'Ok.');	

$mysqli->close();
echo ';*0'.$codigo.';*';
echo 'Ok';
?>