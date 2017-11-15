<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?PHP 
session_start();
error_reporting(0);
require_once('vendor/autoload.php');
require_once('app/google_auth.php');
require_once("funciones/funciones.php");
$ip = getRealIP();
if($ip != $_SESSION['ip'])header("Location: index.php");
$googleClient = new Google_Client();
$auth = new GoogleAuth($googleClient);
if($auth->checkRedirectCode())header("Location: CompruebaInicioSession.php");
if(!isset($_SESSION["access_token"]))header("Location: index.php");//Solo se verifica si no hay nada en el GET
require_once("funciones/conexion.php");

$consulta="SELECT * FROM configuracion WHERE activo='1' ";
$resultado=$mysqli->query($consulta) or die("Connection failed: " . $mysqli->error.' - '.$mysqli->close().'Ok.');
$Config=$resultado->fetch_assoc();
foreach($Config as $campo => $valor){
	$_SESSION[$campo] = $valor;
}


$anio = $_SESSION['anio'];
$sems = $_SESSION['semestre'];

$usuario = $_SESSION['email'];
$usuario = explode('@',$usuario);
$usuario = $usuario[0];
$consulta="SELECT MAX(codigo) AS maximo FROM usuarios WHERE 1 ";
$resultado=$mysqli->query($consulta) or die("Connection failed: " . $mysqli->connect_error);
$registro=$resultado->fetch_assoc();
$codigo = (int)$registro['maximo']+1;
$codigo = str_pad($codigo,7,'0',STR_PAD_LEFT);

$consulta="SELECT codigo,nivel FROM usuarios WHERE usuario='$usuario' ";
$resultado=$mysqli->query($consulta) or die("Connection failed: " . $mysqli->error.' - '.$mysqli->close().'Ok.');
$registro=$resultado->fetch_assoc();
$codigoA = $registro['codigo'];
$nivel = $registro['nivel'];

if((int)$codigoA > 0){
	$_SESSION['codigoUsuario']=$codigoA;//Antiguo
	$_SESSION['usuario']=$usuario;//Antiguo
	$_SESSION['nivel']=$nivel;//Antiguo		
	if($nivel==3){
		$consulta="SELECT codigo FROM monitores WHERE sems='$sems' AND anio='$anio' AND codigo='$codigoA'";
		$resultado=$mysqli->query($consulta) or die("Connection failed: MON" . $mysqli->error.' - '.$mysqli->close().'Ok.');
		$registro = $resultado->fetch_assoc();
		if((int)$registro["codigo"]>0)$_SESSION['nivel']=$nivel=2;//Monitor
	}
}else{
	$insertar = "INSERT INTO usuarios (usuario,codigo,nivel) VALUES ('$usuario','$codigo','3')
  ON DUPLICATE KEY UPDATE codigo='$codigo'";//Nuevo
  $resultado=$mysqli->query($insertar) or die("Connection failed: " . $mysqli->connect_error.'- '.$mysqli->close().'Ok.');
	$_SESSION['codigoUsuario']=$codigo;
	$_SESSION['usuario']=$usuario;
	$_SESSION['nivel']=3;// Es alumno nuevo en ingresos
}  
$mysqli->close();
$pag = "index.php";
if($_SESSION['nivel']==1)$pag = "SeleccionarModulo.php";
else if($_SESSION['nivel']==2)$pag = "SeleccionarModulo.php";
else if($_SESSION['nivel']==3)$pag = "ModuloEstudiante/";
$yainicio ="NO";
if(isset($_SESSION['perfil']['image']['url']))$yainicio = "Ok";
?>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="icon" type="image/png" href="../images/favicon.png" />
<title>.:Redireccionando:.</title>
<script src="js/jquery-1.10.2.js"></script>
<script src="js/jquery-ui.js"></script>
<script>
var pag = "<?PHP echo $pag;?>";
var yainicio = "<?PHP echo $yainicio;?>";
$(function(){
	if(yainicio == "Ok")window.location.href=pag;
	InfoCuenta();
});
function InfoCuenta(){
	var datos = {key:"Pd6K4DgB1Uvg9gF7QlW__GD5",access_token:"<?PHP echo $_SESSION["access_token"]['access_token'];?>",userIp:"<?PHP echo $ip;?>"};
	
	$.ajax({
		url: 'https://www.googleapis.com/plus/v1/people/'+'<?PHP echo $_SESSION['sub']?>',
		type: 'GET',
		async: 'true',
		data: datos
	}).done(function(respuesta){
		EnviarInfo(respuesta);
	});
}
function EnviarInfo(respuesta){
	$.ajax({
		url: 'ObtenerInfo.php',
		type: 'GET',
		async: 'true',
		data: respuesta
	}).done(function(resp){
		window.location.href=pag;
		console.log(resp);
	});
}
</script>
</head>
<body>
<div is="respuesta"></div>
</body>
</html>
