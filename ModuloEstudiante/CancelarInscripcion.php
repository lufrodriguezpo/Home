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
//if(ValidarCadena($area)!=1)exit("2;Error");
$anio = $_SESSION['anio'];
$sems = $_SESSION['semestre'];
$codigo = $_SESSION['codigoUsuario'];
$codarea = base64_decode($_REQUEST['codarea']);
$mes = base64_decode($_REQUEST['mes']);
$dia = base64_decode($_REQUEST['dia']);
$hora = base64_decode($_REQUEST['hora']);
$fechaH = date("Y-m-d H:i:s");
if(ValidarCadena($codarea)!=1)exit("2;Error");
if(ValidarCadena($mes)!=1)exit("2;Error");
if(ValidarCadena($dia)!=1)exit("2;Error");
if(ValidarCadena($hora)!=1)exit("2;Error");
$mes = str_pad($mes,2,'0',STR_PAD_LEFT);
$dia = str_pad($dia,2,'0',STR_PAD_LEFT);
$hora = str_pad($hora,2,'0',STR_PAD_LEFT);

require_once("../funciones/conexion.php");
$remplazar = "DELETE FROM citasambientes WHERE anio = '$anio' AND sems='$sems' AND codarea='$codarea' AND mes='$mes' AND dia='$dia' AND hora='$hora' AND codalu='$codigo'";
$resultado=$mysqli->query($remplazar) or die("Connection failed: " . $mysqli->error.'- '.$mysqli->close().'Ok.');
$mysqli->close();
echo ';*0'.$codcita.';*';
echo 'Ok';
?>