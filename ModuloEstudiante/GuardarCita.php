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
$codigoM = base64_decode($_REQUEST['codigoM']);
$mes = base64_decode($_REQUEST['mes']);
$dia = base64_decode($_REQUEST['dia']);
$hora = base64_decode($_REQUEST['hora']);
$descripcion = base64_decode($_REQUEST['descripcion']);
$fechaH = date("Y-m-d H:i:s");
if(ValidarCadena($codarea)!=1)exit("2;Error");
if(ValidarCadena($codigoM)!=1)exit("2;Error");
if(ValidarCadena($mes)!=1)exit("2;Error");
if(ValidarCadena($dia)!=1)exit("2;Error");
if(ValidarCadena($hora)!=1)exit("2;Error");
if(ValidarCadena($descripcion)!=1)exit("2;Error");
$mes = str_pad($mes,2,'0',STR_PAD_LEFT);
$dia = str_pad($dia,2,'0',STR_PAD_LEFT);
$hora = str_pad($hora,2,'0',STR_PAD_LEFT);

require_once("../funciones/conexion.php");

$consulta="SELECT MAX(codcita) AS maximo FROM citaspares WHERE sems='$sems' AND anio='$anio'";
$resultado=$mysqli->query($consulta) or die("Fail: ". $mysqli->error.$mysqli->close().'Ok');
$registro=$resultado->fetch_assoc();
$codcita = (int)$registro['maximo']+1;
$codcita = str_pad($codcita,4,'0',STR_PAD_LEFT);

$consulta="SELECT codcita FROM citaspares WHERE sems='$sems' AND anio='$anio' AND codmon='$codigoM' AND mes='$mes' AND hora='$hora' AND dia='$dia' AND estado != '2'";
$resultado=$mysqli->query($consulta) or die("Fail: ". $mysqli->error.$mysqli->close().'Ok');
$registro=$resultado->fetch_assoc();
if((int)$registro['codcita']>0)exit("3;Error");

$insertar = "INSERT INTO citaspares (anio,sems,codcita,codarea,codmon,codalu,mes,dia,hora,observacion,estado,fechagen) ";
$insertar .= " VALUES ('$anio','$sems','$codcita','$codarea','$codigoM','$codigo','$mes','$dia','$hora','$descripcion','0','$fechaH') ";
$insertar .= "ON DUPLICATE KEY UPDATE codarea='$codarea',codmon='$codigoM',codalu='$codigo',mes='$mes',dia='$dia',hora='$hora'";
$insertar .= ",observacion='$descripcion', estado='$estado',fechagen='$fechaH'";//Nuevo
$resultado=$mysqli->query($insertar) or die("Connection failed: " . $mysqli->error.'- '.$mysqli->close().'Ok.');
$mysqli->close();
echo ';*0'.$codcita.';*';
echo 'Ok';
?>