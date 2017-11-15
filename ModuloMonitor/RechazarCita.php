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
$codcita = base64_decode($_REQUEST['codigo']);
$codarea = base64_decode($_REQUEST['codarea']);
$codigoE = base64_decode($_REQUEST['codigoE']);
$estado = base64_decode($_REQUEST['estadoC']);
$mes = base64_decode($_REQUEST['mes']);
$dia = base64_decode($_REQUEST['dia']);
$hora = base64_decode($_REQUEST['hora']);
$respuesta = base64_decode($_REQUEST['respuesta']);
$fechaH = date("Y-m-d H:i:s");
if(ValidarCadena($codarea)!=1)exit("2;Error");
if(ValidarCadena($codigoE)!=1)exit("2;Error");
if(ValidarCadena($mes)!=1)exit("2;Error");
if(ValidarCadena($dia)!=1)exit("2;Error");
if(ValidarCadena($hora)!=1)exit("2;Error");
if(ValidarCadena($codcita)!=1)exit("2;Error");
if(ValidarCadena($estado)!=1)exit("2;Error");
if(ValidarCadena($respuesta)!=1)exit("2;Error");
print_r($_FILES);
$path = '../documentos';
$soporte = '';
$apoyo = '';
$ArchPermExt = array('bmp','gif','jpg','jpeg','png','pdf','xls', 'pptx','docx','doc','zip');
$ArchPerm = array('','image/bmp','image/gif','image/jpeg','image/jpeg','image/png','application/pdf','application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/msword','application/zip','application/vnd.openxmlformats-officedocument.presentationml.presentation');

if(isset($_FILES['soporte'])){
	$tipo = $_FILES['soporte']['type'];
	$ext = explode('.',$_FILES['soporte']['name']);
	$ext = $ext[1];
	if(!in_array($tipo,$ArchPerm))exit("5;Error");
	if($tipo ==  '' && !in_array($ext,$ArchPermExt))exit("5;Error");
	$soporte = "S".$anio.'_'.$sems.'_'.$codcita.".".$ext;
	GuardarArchivo($_FILES['soporte'],$path,$soporte,$soporte);
}
if(isset($_FILES['apoyo'])){
	$tipo = $_FILES['apoyo']['type'];
	$ext = explode('.',$_FILES['apoyo']['name']);
	$ext = $ext[1];
	if(!in_array($tipo,$ArchPerm))exit("5;Error");
	if($tipo ==  '' && !in_array($ext,$ArchPermExt))exit("5;Error");
	$apoyo = "A".$anio.'_'.$sems.'_'.$codcita.".".$ext;
	GuardarArchivo($_FILES['apoyo'],$path,$apoyo,$apoyo);
}
require_once("../funciones/conexion.php");
$remplazar = "UPDATE citaspares SET estado='$estado' ";
if($respuesta != '')$remplazar .= ",respuesta='$respuesta' ";
if($soporte != '')$remplazar .= ",soporte='$soporte' ";
if($apoyo != '')$remplazar .= ",archivores='$apoyo' ";
$remplazar .= "WHERE anio='$anio' AND sems='$sems' AND codarea='$codarea' ";
$remplazar .= "AND codcita='$codcita' AND codalu='$codigoE' AND codmon='$codigo' AND mes='$mes' AND dia ='$dia' AND hora='$hora'";
echo $remplazar.'<hr>';
$resultado=$mysqli->query($remplazar) or die("Connection failed: " . $mysqli->error.'- '.$mysqli->close().'Ok.');
$mysqli->close();
echo ';*0'.$codcita.';*';
echo 'Ok';
?>