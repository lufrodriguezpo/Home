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
if($_REQUEST['codigoM']!=base64_encode( $_SESSION['codigoUsuario']))exit("1;Error");
//if(ValidarCadena($area)!=1)exit("2;Error");
require_once("../funciones/conexion.php");
$anio = $_SESSION['anio'];
$sems = $_SESSION['semestre'];
$codigoM = $_SESSION['codigoUsuario'];
$codarea = base64_decode($_REQUEST['codarea']);
if(ValidarCadena($codarea)!=1)exit("2;Error");
foreach($_REQUEST as $id => $val){
	if(substr($id,0,2)=='VF'){
		if(ValidarCadena($id)!=1)exit("2;Error");
		$fecha = explode('-',substr($id,2));
		$mes = str_pad($fecha[0],2,'0',STR_PAD_LEFT);
		$dia = str_pad($fecha[1],2,'0',STR_PAD_LEFT);
		$hora = str_pad($fecha[2],2,'0',STR_PAD_LEFT);
		if($val=='S'){
			$insertar = "INSERT INTO horariospares (anio,sems,codigo,mes,dia,hora,codarea) VALUES ('$anio','$sems','$codigoM','$mes','$dia','$hora','$codarea') ON DUPLICATE KEY UPDATE codarea='$codarea'";//Nuevo
			$resultado=$mysqli->query($insertar) or die("Connection failed: " . $mysqli->error.'- '.$mysqli->close().'Ok.');			
		}else if($val=='N'){
			$remplazo = "DELETE FROM horariospares WHERE sems='$sems' AND anio='$anio' AND codigo='$codigoM' AND mes='$mes' AND dia='$dia' AND hora='$hora' AND codarea='$codarea'";
			$resultado=$mysqli->query($remplazo) or die("No seguro - ".$mysqli->close().'Ok.');
		}
	}
}
$mysqli->close();
echo ';*0'.$codigo.';*';
echo 'Ok';
?>