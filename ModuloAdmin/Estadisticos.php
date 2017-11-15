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
if((int)$nivel >3 || (int)$nivel < 1)header("Location: ../");

/*Proceso de la pagina*/
require_once("../funciones/conexion.php");

$consulta="SELECT * FROM modulos WHERE nivel='1' ";
$resultado=$mysqli->query($consulta) or die("Connection failed: " . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$seccion = $registro['seccion'];
	$cod = $registro['codigo'];
	$subCod = $registro['subcodigo'];
	$descripcion = $registro['descripcion'];
	$Modulos[$seccion][$cod][$subCod]=$descripcion;
}

?>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="icon" type="../image/png" href="../images/favicon.png" />
<title>.:Modulo Administrador:.</title>
<script src="../js/jquery-1.10.2.js"></script>
<script src="../js/jquery-ui.js"></script>
<script src="../js/FunPHP.js"></script>
<link rel="stylesheet" href="../css/jquery-ui.css"/>
<link rel="stylesheet" href="../css/jquery.fancybox.css"/>
<link rel="Stylesheet" href="../css/select/ui.selectmenu.css" type="text/css" />
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Dosis" rel="stylesheet"><!-- Fuente Google-->
<link rel="stylesheet" type="text/css" href="estilosAdmin.css?v=1.4"/>
<link rel="stylesheet" type="text/css" href="estilosEstadisticos.css?v=1.3"/>

<script>

var dias = [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ];
var diasMin = [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ];
var meses = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
var mesesMin = ["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"];
var rangYear = "<?PHP echo ((int)date("Y")-5).":".((int)date("Y")+5)?>";
var estadisticos = {};
estadisticos[1] = "Estadisticos/InformeAsistenciasProgramaParesRango.php";
estadisticos[2] = "Estadisticos/InformeAsistenciasProgramaAmbientesRango.php";
$(function(){
	$('input:button,button').button();
	$("#dialogo").dialog({
		height: "auto",
		width: 400,
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
	$("body").on("click",".opcion_card,.mapa_opccion,.contenerdor_seccion[data-href=''],.seccion_cards[data-href!='']",function(){
		AbrirOpcion(this);
	});
	$(".opcion_card,.mapa_opccion,.contenerdor_seccion[data-href=''],.seccion_cards[data-href='']").each(function(){
		$(this).css("cursor","pointer");
	});
	
	$("#header_titulo").css("cursor","pointer");
	$("body").on("click","#header_titulo",function(){
		window.location.href = "index.php";
	});
	
	$( ".fecha" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dayNames: dias,
		dayNamesMin: diasMin,
		monthNames: meses,
		monthNamesShort: mesesMin,
		dateFormat: 'yy-mm-dd',
		yearRange: rangYear
    });
	$("body").on("change","select[id=estadisticos]",function(){
		var val = $(this).val();
		var option = $("select[id=estadisticos] option[value="+val+"]");
		console.log(option);
		if(option.data("reqf")=="si")$("#contenedor_fechas").show();
		else $("#contenedor_fechas").hide();
	});
	$("body").on("click","#imprimir",function(){
		VerificarImprimir();
	});

});
function VerificarImprimir(){
	var val = $("select[id=estadisticos]").val();
	var option = $("select[id=estadisticos] option[value="+val+"]");
	if(option.data("reqf")=="si" && ($("#fechaini").val()=="" || $("#fechafin").val()=="") ){
		alert("Debe fijar la fecha de Inicio y Fin para el rango a imprimir.");
		return 0;
	}else{
		rq = '<input name="fechaini" value="'+$("body").encodeUrl($("#fechaini").val())+'">';
		rq += '<input name="fechafin" value="'+$("body").encodeUrl($("#fechafin").val())+'">';
	}
	
	$("#redir").html("");
	$("#redir").attr("method","post");
	$("#redir").attr("target","_blank");
	$("#redir").attr("action",estadisticos[val]);
	$("#redir").append(rq);
	$("#redir").submit();
	$("#redir").html("");
}
function AbrirOpcion(element){
	var id=$(element).attr("id");
	var href = $(element).data("href");
	var rq = "";
	if(typeof href == "undefined" || href==""){
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
	$("#contenedor_formulario").css("height",0.9*(h-hh)+"px");
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
							Administrador	
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
					</ul>
				</div>
			</div>
			<div id="contenedor_contenido"  class="eTodo">
				<div id="contenedor_formulario">
					<div id="titulo_formulario">
						Estadisticos del Sistema
					</div>
					<div id="contenedor_select">
						<select id="estadisticos">
							<option value="0">Seleccione un Estadistico...</option>
							<option value="1">1. Asistencias a Programa Pares</option>
							<option value="2">2. Asistencias a Programa Ambientes</option>
						</select>
					<div>
					<div id="contenedor_fechas" style="display:none;"> 
						<div class="titulo_campos">
							Fechas
						</div>
						<input type="text" id="fechaini" class="fecha"> &nbsp;
						<input type="text" id="fechafin" class="fecha">
					</div>
					<div id="botones"> 
						<button id="imprimir">Imprimir</button>
					</div>
				</div>
			<br />
			</div>
		</section>
	</div>
<?PHP 
$mysqli->close();
?>
</body>
</html>
