<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?PHP 
session_start();
error_reporting(0);
header('Content-Type: text/html; charset=ISO-8859-1');
require_once('app/init.php');
require_once('vendor/autoload.php');
require_once('app/google_auth.php');
$googleClient = new Google_Client();
$auth = new GoogleAuth($googleClient);
?>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="icon" type="image/png" href="images/favicon.png" />
<title>Login</title>
<link rel="stylesheet" type="text/css" href="estilosLogin.css?v=1.4"/>
<script src="js/jquery-1.10.2.js"></script>
<script src="js/jquery-ui.js"></script>
<script>
var indImgB = 1;
$(function(){
	var timeoutID = window.setInterval(CambiarFondo,10000);
	ResizeFondo();
	$(window).resize(function(){
		ResizeFondo();
	});
	
});
function CambiarFondo(){
	var tam = 7;
	indImgB++;
	if(indImgB==tam)indImgB=1;
	console.log("cambio");
	$("body").css({
		"background":"url(images/imagen"+indImgB+".jpg",
		"background-attachment":"fixed",
		"background-repeat":"no-repeat",
		"background-size":"cover"
	});
}
function ResizeFondo(){
	var h = $(document).height();
	var hh = $("header").height();
	$("body").css("min-height",h+"px");
	$("#contenido").css("min-height",(h-hh)+"px");
}
function redireccionar(){
	var pagina = '<?PHP echo (!$auth->isLoggedIn())?$auth->getAuthUrl():"CompruebaInicioSession.php"; ?>';
	window.location.href = pagina;
}
</script>
</head>
<body>
	<div id="contenedor-body">
		<header>
			<div id="contenedor-header">
				<div id="logo" class="eHeader">
					<img src="images/favicon.png"/>
					<div id="logo-titulo">TUTOS-UN</div>
				</div>
				<div id="menu" class="eHeader">
					<div class="menu-opcion">Login</div>
					<div class="menu-opcion">Servivios</div>
					<div class="menu-opcion">Nosotros</div>
					<div class="menu-opcion">Contacto</div>
				</div>
			</div>
		</header>
		<section id="contenido">
			<div id="contenedor-login">
				<div id="titulo-login">Bienvenido!</div>
				<div id="advertencia-login">
					Solo está permitido el ingreso a estudiantes de la universidad nacional, 
					por lo cual el ingreso será validado por medio del correo Institucional.
				</div>
				<div id="botton-login" onclick="redireccionar();">Continuar</div></a>
			</div>
		</section>
	</div>
</body>
</html>
