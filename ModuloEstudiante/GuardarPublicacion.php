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
$validaT = base64_encode($_SESSION["access_token"]["access_token"]);
$anio = $_SESSION['anio'];
$sems = $_SESSION['semestre'];
$codigo = $_SESSION['codigoUsuario'];

$titulo = base64_decode($_REQUEST['titulo'.$validaT]);
$categoria = base64_decode($_REQUEST['categoria'.$validaT]);
$contenido = base64_decode($_REQUEST['contenido'.$validaT]);
$enlace = base64_decode($_REQUEST['enlace'.$validaT]);
$fechaH = date("Y-m-d H:i:s");
if(ValidarCadena($titulo)!=1)exit("12;Error");
if(ValidarCadena($categoria)!=1)exit("12;Error");
if(ValidarCadena($contenido)!=1)exit("22;Error");
if(ValidarCadena($enlace)!=1)exit("32;Error");
echo $categoria.'-'.$contenido.'-'.$enlace;
if($categoria=='' || $contenido=='')exit("3;Error");
require_once("../funciones/conexion.php");

$consulta="SELECT MAX(codpublica) AS maximo FROM publicaciones WHERE 1 ";
$resultado=$mysqli->query($consulta) or die("Connection failed: " . $mysqli->error.' - '.$mysqli->close().'Ok.');
$registro = $resultado->fetch_assoc();
$codigoPublica = (int)$registro["maximo"]+1;
$codigoPublica = str_pad($codigoPublica,7,'0',STR_PAD_LEFT);

$path = '../documentos';
$imagen = '';
$archivo = '';
$ArchPermExt = array('bmp','gif','jpg','jpeg','png','pdf','xls', 'pptx','docx','doc','zip');
$ArchPerm = array('','image/bmp','image/gif','image/jpeg','image/jpeg','image/png','application/pdf','application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/msword','application/zip','application/vnd.openxmlformats-officedocument.presentationml.presentation');

if(isset($_FILES['imagen'.$validaT])){
	$tipo = $_FILES['imagen'.$validaT]['type'];
	$ext = explode('.',$_FILES['imagen'.$validaT]['name']);
	$ext = $ext[1];
	if(!in_array($tipo,$ArchPerm))exit("14;Error");
	if($tipo ==  '' && !in_array($ext,$ArchPermExt))exit("5;Error");
	$imagen = "PI".$codigoPublica.".".$ext;
	GuardarArchivo($_FILES['imagen'.$validaT],$path,$imagen,$imagen);
}
if(isset($_FILES['archivo'.$validaT])){
	$tipo = $_FILES['archivo'.$validaT]['type'];
	$ext = explode('.',$_FILES['archivo'.$validaT]['name']);
	$ext = $ext[1];
	if(!in_array($tipo,$ArchPerm))exit("24;Error");
	if($tipo ==  '' && !in_array($ext,$ArchPermExt))exit("5;Error");
	$archivo = "PA".$codigoPublica.".".$ext;
	GuardarArchivo($_FILES['archivo'.$validaT],$path,$archivo,$archivo);
}
$tabla = "publicaciones";
$campos = "(codpublica,usuario,categoria,anio,sems,contenido,documento,fechagen,ip,imagen,enlace,estado,titulo)";
$variables = "('$codigoPublica','$codigo','$categoria','$anio','$sems','$contenido','$archivo','$fechaH','$ip','$imagen','$enlace','1','$titulo')";
$duplicados = "usuario='$codigo',categoria='$categoria',anio='$anio',sems='$sems',";
$duplicados .= "documento='$archivo',fechagen='$fechaH',ip='$ip',imagen='$imagen',enlace='$enlace',estado='1',titulo='$titulo'";
agregar($tabla,$campos, $variables,$duplicados,0);
$mysqli->close();
echo ';*0'.$codigoPublica.';*';
echo 'Ok';
?>