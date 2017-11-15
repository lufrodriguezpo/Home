<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?PHP 
error_reporting(0);
header('Content-Type: text/html; charset=ISO-8859-1');
session_start();
require_once("../funciones/funciones.php");
$ip = getRealIP();
/*Validacion de session*/
if($ip != $_SESSION['ip'])header("Location: ../");
if(!isset($_SESSION["access_token"]))header("Location: ../");
$nivel =$_SESSION['nivel'];
if($nivel > 2  || (int)$nivel < 1)header("Location: ../");
$anio = $_SESSION['anio'];
$sems = $_SESSION['semestre'];

/*Proceso de la pagina*/
require_once("../funciones/conexion.php");
$codigoU  = $_SESSION['codigoUsuario'];
$consulta="SELECT * FROM modulos WHERE nivel='2' ";
$resultado=$mysqli->query($consulta) or die("Connection failed: " . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$seccion = $registro['seccion'];
	$cod = $registro['codigo'];
	$subCod = $registro['subcodigo'];
	$descripcion = $registro['descripcion'];
	$Modulos[$seccion][$cod][$subCod]=$descripcion;
}
$Areas = array();
$consulta="SELECT codarea,area FROM areas WHERE anio='$anio' AND sems='$sems'";
$resultado=$mysqli->query($consulta) or die("Connection failed: " . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$codarea = $registro['codarea'];
	$NomAreas[$codarea] = $registro['area'];
}
$consulta="SELECT codarea FROM monitores WHERE codigo='$codigoU' AND anio='$anio' AND sems='$sems'";
$resultado=$mysqli->query($consulta) or die("Connection failed: " . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$codarea = $registro['codarea'];
	if($NomAreas[$codarea]!='')$Areas[$codarea] = $NomAreas[$codarea];
}
?>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="icon" type="image/png" href="../images/favicon.png" />
<title>.:Modulo Tutor:.</title>
<script src="../js/jquery-1.10.2.js"></script>
<script src="../js/jquery-ui.js"></script>
<script src="../js/FunPHP.js"></script>
<link rel="stylesheet" href="../css/jquery-ui.css"/>
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Dosis" rel="stylesheet"><!-- Fuente Google-->
<link rel="stylesheet" type="text/css" href="estilosMonitor.css?v=1.1"/>
<link rel="stylesheet" type="text/css" href="estilosCards.css?v=1.10"/>
<script>

var Areas = <?PHP echo json_encode($Areas);?>;
var nA = <?PHP echo count($Areas);?>;
$(function(){
	$('input:button,button').button();
	$("#dialogo").dialog({
		height: "auto",
		width: "auto",
		maxWidth: 800,
		dialogClass: "hide-close",
		autoOpen: false,
		resizable: false,
		draggable: false,
		open: function(){$("#telon").css("display", "block");},
		close: function(){$("#telon").css("display", "none");},
		show: {effect: "clip",duration: 350}
	});
	ResizeFondo();
	$(window).resize(function(){
		ResizeFondo();
	});
});

$(document).ready(function(){
	$("body").on("click","#boton_cerrar",function(){
		VerificarCerrarSession();
	});
	$("body").on("click",".mapa_seccion_img",function(){
		CambiarEstadoSeccionMenu(this);
	});
	
	$("body").on("click",".card",function(){
		VisibilidadMenu(this);
	});
	$("body").on("click",".opcion_card,.mapa_opccion,.contenerdor_seccion[data-href=''],.seccion_cards[data-href='']",function(){
		AbrirOpcion(this);
	});
	$(".opcion_card,.mapa_opccion,.contenerdor_seccion[data-href=''],.seccion_cards[data-href='']").each(function(){
		$(this).css("cursor","pointer");
	});
	
	
	$("#header_titulo").css("cursor","pointer");
	$("body").on("click","#header_titulo",function(){
		window.location.href = "index.php";
	});
	$("body").on("click",".redirArea",function(){
		var href = $(this).data("href");
		var v = $(this).data("value");
		$("#redir").html("");
		$("#redir").attr("method","post");
		$("#redir").attr("action",href);
		$("#redir").append('<input type="hidden" name="v" value="'+$("body").encodeUrl(v)+'">');
		$("#redir").submit();
	});
});
function AbrirOpcion(element){
	var id=$(element).attr("id");
	var href = $(element).data("href");
	var tipo = $(element).data("tipo");
	if(typeof href == "undefined" || href==""){
		return 0;
	}
	if(tipo ==1){
		var mensa = 'Por favor, seleccione un area a asignar:<br>';
		mensa +='<table>';
		$.each(Areas,function(ind,val){
			mensa +='<tr><td><button class="redirArea" style="width:100%" data-value="'+ind+'" data-href="'+$(element).data("href")+'">'+val+'</button></td></tr>';
		})
		mensa +='<table>';
		AbreDialog(mensa,'Redireccionando...','','');
		$('input:button,button').button();
		return 0;
	}
	window.location.href = $(element).data("href");
}
function CambiarEstadoSeccionMenu(element){
	var conte = $(element).parent().parent("li.contenerdor_seccion");
	var estado = conte.attr("desplegada");
	if(typeof estado=="undefined")estado = "false";
	if(estado=="false")estado="true";
	else estado="false";
	conte.attr("desplegada",estado);
} 
function VisibilidadMenu(element){
	var padre = $(element).parent(".seccion_cards");
	var vis = padre.attr("visible-menu");
	if(typeof vis == "undefined")vis="false";
	if(vis=="false")vis="true";
	else vis="false";
	var h = (vis=="true")?padre.find(".menu").show():padre.find(".menu").hide();
	//padre.find(".menu").css("height",h);
	var vis = padre.attr("visible-menu",vis);
}
function VerificarCerrarSession(){
	console.log("Abririendo");
	var html = "<center>Esta seguro de cerrar la sesión?</center>";
	var titulo = "<center>Confirmación</center>";
	var color = "#57D069";
	var buttons = [{text: "Cerrar Session", click: function() {CerrarSession();$(this).dialog("close");}},
					{text: "Cancelar", click: function() {$(this).dialog("close");}}];
	AbreDialog(html,titulo,color,buttons);
}
function CerrarSession(){
	var datos = {"id":"<?PHP echo $_SESSION['email'];?>"};
	$.ajax({
		url: 'CerrarSession.php',
		type: 'POST',
		async: 'true',
		data: datos,
		beforeSend: function(){
			var mensa = 'Cerrando session...';
			AbreDialog(mensa,'Información',"#57D069",'');
	   }
	}).done(function(respuesta){
		if(respuesta.substr(-2,2)=="Ok"){
			var buttons = [{text: "Regresar", click: function() {$(this).dialog("close");window.location.href="../"}}];
			var html = "<center>La session se cerro satisfactoriamente...</center>";
			AbreDialog(html,'Información',"#57D069",buttons);
			//window.location.href="../";
			setTimeout(function(){$("#dialogo").dialog("close"); window.location.href="../"}, 3000);
		}else{
			var html = "<center>Ahh ocurrido un error en la conexcion, ingrese nuevamente</center>";
			AbreDialog(html,'Información',"#57D069",'');
			//window.location.href="../";
			setTimeout(function(){$("#dialogo").dialog("close"); }, 3000);
		}
	});
}
function ResizeFondo(){
	var h = $(document).height();
	var hh = $("header").height();
	$("body").css("min-height",h+"px");
	$("#contenedor_todo").css("height",(h-hh)+"px");
	$("#contenedor_todo").css("max-height",(h-hh)+"px");
	$("#contenedor_contenido").css("height",(h-hh)+"px");
	$("#contenedor_contenido").css("max-height",(h-hh)+"px");
}
function AbreDialog(html,titulo,color,buttons){
	titulo = '<center>'+titulo+'</center>';
	html = '<center>'+html+'</center>';
	$("#dialogo").html(html);
	if(buttons == '') buttons = [{text: "Regresar", click: function() {$(this).dialog("close");}}];
	if(color == '')color = "#57D069";
	$("#dialogo").dialog('option', 'title',titulo);
	$("#dialogo").dialog('option', 'buttons',buttons);
	$("#dialogo").parent().find(".ui-dialog-titlebar").css("background",color);
	$("#dialogo").parent().find(".ui-dialog-titlebar").html(titulo);
	$("#dialogo").dialog( "option", "position",{ my: "center", at: "center", of: window });
	$("#dialogo").dialog("open");
}
</script>
</head>
<body>
	<form id="redir"></form>
	<div id="telon" style="background:rgba(0,0,0, 0.5); position:fixed; width:100%; height:2000px; display:none; top:0px;left:0;"></div>
	<div id="dialogo"></div>
	<div id="contenedor-body">
		<header>
			<div id="contenedor-header">
				<div id="header_logo" class="eHeader">
					<img src="../images/logo_unal.png?v=1.0" height="70"/>
				</div>
				<div id="header_titulo" class="eHeader">
					TUTOS-UN
				</div>
				<div id="header_session" class="eHeader">
					<span id="boton_cerrar">Cerrar Sesión</span>
				</div>
			</div>
		</header>
		<section id="contenedor_todo">
			<div id="contenedor_nav" class="eTodo">
				<div id="perfil" class="contenedor_perfil">
					<div id="perfil_foto" style="background-image:url('<?PHP echo $_SESSION['perfil']['image']['url']?>')">
						<div id="perfil_foto_anuncio">
							Editar Perfil
						</div>
					</div>
					<div id="perfil_info">
						<div id="perfil_info_nombre">
						<?PHP 
							echo $_SESSION['nombres'].' '.$_SESSION['apellidos'];						
						?>
						</div>
						<div id="perfil_info_rol">
							Tutor
						</div>
					</div>
				</div>
				<div id="mapa_contenedor">
					<div id="mapa_titulo">
						MAPA DEL SITIO
					</div>
					<ul class="mapa">
						<?PHP
							foreach($Modulos[1] as $codigo => $modulo)echo ConstruirModulo(2, $codigo, $modulo);
						?>
						<!--
						<li class="contenerdor_seccion" desplegada="false">
							<div class="mapa_seccion">
								<div class="mapa_seccion_img">
									&nbsp;
								</div>
								<div class="mapa_seccion_descripcion">
									Seccion 1
								</div>
							</div>
							<div class="mapa_opcciones">
								<ul class="mapa_contenerdor_opciones">
									<li class="mapa_opccion">Opcion</li>
									<li class="mapa_opccion">Opcion</li>
									<li class="mapa_opccion">Opcion</li>
									<li class="mapa_opccion">Opcion</li>
								</ul>
							</div>
						</li>-->
					</ul>
				</div>
			</div>
			<div id="contenedor_contenido"  class="eTodo">
			
				<?PHP
					$orientacion ="derecho";
					foreach($Modulos[1] as $codigo => $modulo){
						$modulo['orientacion'] = $orientacion;
						echo ConstruirModulo(1, $codigo, $modulo);
						if($orientacion=="derecho")$orientacion="izquierdo";
						else $orientacion="derecho";
					}
				?>
				<!--
				<section class="seccion_cards" visible-menu="false" lado="derecho">
					<article class="card">
						<div class="card_img" style="background-image:url('images/agendar.jpg')">
							
							<div class="card_etiqueta">ETIQUETA</div>
						</div>
						<div class="card_contenedor_contenido">
							<div class="card_titulo">
								Titulo de mi opcion
							</div>
							<div class="card_subtitulo">
								Subtitulo Opcional para esta option
							</div>
							<div class="card_descripcion">
								Puede elegir un tema de color de acuerdo con sus preferencias al iniciar Dreamweaver. También puede cambiar esta preferencia en cualquier momento. Seleccione Edición > Preferencias (Windows) o Dreamweaver > Preferencias (Macintosh). Seleccione Interfaz en la lista Categoría de la izquierda
							</div>
						</div>
					</article>
					<article class="menu">
						<div class="opcion_card">
							<div class="icono_opcion_card eOpcion_card">
								<img src="images/calendario.png"/>
							</div>
							<div class="titulo_opcion_card eOpcion_card">
								Titulo
							</div>
							<div class="borde_opcion_card eOpcion_card">
								&nbsp;
							</div>
						</div>
						
						
						<div class="opcion_card">
							<div class="icono_opcion_card eOpcion_card">
								<img src="images/calendario.png"/>
							</div>
							<div class="titulo_opcion_card eOpcion_card">
								Este es otro titulo 
							</div>
							<div class="borde_opcion_card eOpcion_card">
								&nbsp;
							</div>
						</div>
						
					</article>
				</section>-->
			<br />
			</div>
		</section>
	</div>
</body>
</html>
