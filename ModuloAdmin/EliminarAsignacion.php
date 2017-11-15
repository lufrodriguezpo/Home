<?PHP
error_reporting(0);
session_start();
require_once("../funciones/funciones.php");
$ip = getRealIP();
/*Validacion de session*/
if($ip != $_SESSION['ip'])exit("No seguro procedencia");
if(!isset($_SESSION["access_token"]))exit("No seguro su ingreso");
$nivel =$_SESSION['nivel'];
if((int)$nivel !=1)exit("No seguro su rol");
if($_REQUEST['pin']!=base64_encode($ip))exit("Pin incorrecto");
$codigoarea = base64_decode($_REQUEST['codigoarea']);
$codigo = base64_decode($_REQUEST['codigo']);
if(ValidarCadena($codigoarea)!=1)exit("2;Error");
if(ValidarCadena($codigo)!=1)exit("2;Error");
$coor = explode('-',$codigoarea);
$usuario = $coor[0];
$codarea = $coor[1];

require_once("../funciones/conexion.php");

$anio = $_SESSION['anio'];
$sems = $_SESSION['semestre'];
$consulta="SELECT codigo FROM usuarios WHERE usuario='$usuario' AND codigo='$codigo'";
$resultado=$mysqli->query($consulta) or die("Fail: USU".$mysqli->close().'Ok.');
$registro=$resultado->fetch_assoc();
$codigoA = $registro['codigo'];
if((int)$codigoA == 0)exit("1;Error");

$remplazo = "DELETE FROM monitores WHERE codigo='$codigo' AND codarea='$codarea' AND anio='$anio' AND sems = '$sems'";
$resultado=$mysqli->query($remplazo) or die("Fail: MON".$mysqli->error.'- '.$mysqli->close().'Ok.');
	
$mysqli->close();
echo 'Ok';
?>