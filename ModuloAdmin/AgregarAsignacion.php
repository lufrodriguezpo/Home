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
$usuario = base64_decode($_REQUEST['usuario']);
$codarea = base64_decode($_REQUEST['codarea']);
if(ValidarCadena($usuario)!=1)exit("2;Error");
if(ValidarCadena($codarea)!=1)exit("2;Error");

require_once("../funciones/conexion.php");
$anio = $_SESSION['anio'];
$sems = $_SESSION['semestre'];
$consulta="SELECT MAX(codigo) AS maximo FROM usuarios WHERE 1 ";
$resultado=$mysqli->query($consulta) or die("Fail: USU2".$mysqli->close().'Ok');
$registro=$resultado->fetch_assoc();
$codigo = (int)$registro['maximo']+1;
$codigo = str_pad($codigo,7,'0',STR_PAD_LEFT);


$consulta="SELECT codigo,nivel FROM usuarios WHERE usuario='$usuario' ";
$resultado=$mysqli->query($consulta) or die("Fail: USU1".$mysqli->close().'Ok.');
$registro=$resultado->fetch_assoc();
$codigoA = $registro['codigo'];
$nivelA = $registro['nivel'];
if((int)$nivelA==1)exit("1;Error");
if((int)$codigoA > 0){
	$codigo = $codigoA;
	$insertar = "INSERT INTO monitores (anio,sems,codigo,codarea) VALUES ('$anio','$sems','$codigo','$codarea')
  ON DUPLICATE KEY UPDATE codarea='$codarea' ";//Antiguo
	$resultado=$mysqli->query($insertar) or die("Connection failed: MON1" . $mysqli->error.'- '.$mysqli->close().'Ok.');
	
}else{
	
	$insertar = "INSERT INTO usuarios (usuario,codigo,nivel) VALUES ('$usuario','$codigo','3')
  ON DUPLICATE KEY UPDATE codigo='$codigo', nivel='3'";//Nuevo
	$resultado=$mysqli->query($insertar) or die("Connection failed: usu 3" . $mysqli->error.'- '.$mysqli->close().'Ok.');	
	
	$insertar = "INSERT INTO monitores (anio,sems,codigo,codarea) VALUES ('$anio','$sems','$codigo','$codarea')
  ON DUPLICATE KEY UPDATE codarea='$codarea'";//Nuevo
	$resultado=$mysqli->query($insertar) or die("Connection failed: mon 2" . $mysqli->error.'- '.$mysqli->close().'Ok.');	
}
$mysqli->close();
echo ';*0'.$codigo.';*';
echo 'Ok';
?>