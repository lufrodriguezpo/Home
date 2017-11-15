<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?PHP 
header('Content-Type: text/html; charset=ISO-8859-1');
error_reporting(0);
session_start();
require_once("../funciones/funciones.php");
$ip = getRealIP();
/*Validacion de session*/
if($ip != $_SESSION['ip'])header("Location: ../");
if(!isset($_SESSION["access_token"]))header("Location: ../");
$nivel =$_SESSION['nivel'];
if((int)$nivel !=1)header("Location: ../");

/*Proceso de la pagina*/
require_once("../funciones/conexion.php");
$anio = $_SESSION['anio'];
$sems = $_SESSION['semestre'];

$consulta="SELECT * FROM areas WHERE anio='$anio' AND sems='$sems' ";
$resultado=$mysqli->query($consulta) or die("Connection failed: " . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$codarea = $registro['codarea'];
	$Areas['A'.$codarea] = $registro['area'];
}

if(count($Areas)==0)$Areas = array();
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
<title>.:Asignar Areas:.</title>
<script src="../js/jquery-1.10.2.js"></script>
<script src="../js/jquery-ui.js"></script>
<script src="../js/FunPHP.js"></script>
<link rel="stylesheet" href="../css/jquery-ui.css"/>
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Dosis" rel="stylesheet"><!-- Fuente Google-->
<link rel="stylesheet" type="text/css" href="estilosAdmin.css?v=1.4"/>
<link rel="stylesheet" type="text/css" href="estilosAsignarArea.css?v=1.2"/>
<script>
var validaD = "<?PHP echo base64_encode($ip);?>";
var Areas = <?PHP echo json_encode($Areas);?>;
if(Areas==null)Areas = new Array();
$(function(){
	$('input:button,button').button();
	$("#dialogo").dialog({
		height: "auto",
		width: "auto",
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
	LlenarAreas();
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
	$("body").on("click","#agregar",function(){
		ValidarAgregar();
	});
	$("body").on("click",".eliminar.boton_accion",function(){
		ConfirmarEliminacion(this);
	});
	$("body").on("click",".editar.boton_accion",function(){
		DialogoEdicion(this);
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

function DialogoEdicion(element){
	var codigo=$(element).attr("id").substr(1);
	var area = $("#A"+codigo).html();
	var buttons = [{text: "Aceptar", click: function() {Editar(codigo);}},{text: "Cancelar", click: function() {$(this).dialog("close");}}];
	var html = "<b>Nuevo valor:</b> <input type='text' id='edicion' style='width:400px;' value='"+area+"'>";
	AbreDialog(html,'Edicion de Area','#009966',buttons);
}
function Editar(codigo){
	var edicionC = $("body").encodeUrl($("#edicion").val());
	var codigoC = $("body").encodeUrl(codigo);
	var datos = {edicion:edicionC,codigo:codigoC,pin:validaD};
	$.ajax({
		url: "EditarArea.php",
		type: 'POST',
		async: 'true',
		data: datos
	}).done(function(respuesta){
		if(respuesta.substr(-2,2)=='Ok'){
			$("#A"+codigo).html($("#edicion").val());
			$("#dialogo").dialog("close");
		}else if(respuesta.substr(-5,5)=='Error' && respuesta.substr(-7,1)==2){
			AbreDialog('El area es incorrecta, solo se permiten caracteres alfanumericos.','Error al Eliminar!','#FF0000','');
		}else{
			AbreDialog('Ha ocurrido un error al momento de cambiar el area, por favor, reecargue la pagina  e intente de nuevo.','Error al Guardar!','#FF0000','');
		}
	});
}
function ConfirmarEliminacion(element){
	var codigo=$(element).attr("id").substr(1);
	var area = $("#A"+codigo).html();
	var buttons = [{text: "Aceptar", click: function() {EliminarArea(area,codigo);}},{text: "Cancelar", click: function() {$(this).dialog("close");}}]
	AbreDialog('Esta seguro de eliminar el area <b>'+area+"</b>?",'Confirmación','#009966',buttons);
}
function EliminarArea(area,codigo){
	var areaC = $("body").encodeUrl(area);
	var codigoC = $("body").encodeUrl(codigo);
	var datos = {area:areaC,codigo:codigoC,pin:validaD};
	$.ajax({
		url: "EliminarArea.php",
		type: 'POST',
		async: 'true',
		data: datos
	}).done(function(respuesta){
		if(respuesta.substr(-2,2)=='Ok'){
			$("#dialogo").dialog("close");
			$("#F"+codigo).remove();
		}else if(respuesta.substr(-5,5)=='Error' && respuesta.substr(-7,1)==2){
			AbreDialog('El area es incorrecta, solo se permiten caracteres alfanumericos.','Error al Eliminar!','#FF0000','');
		}else{
			AbreDialog('Ha ocurrido un error al momento de eliminar el area, por favor, reecargue la pagina  e intente de nuevo.','Error al Eliminar!','#FF0000','');
		}
	});
}
function ValidarAgregar(){
	var area = $("#area").val();
	if(area==''){
		AbreDialog('No se aceptan valores nulos. Por favor ingrese un nombre de area.','Error de Validación!','#FF0000','');
		return 0;
	}
	AgregarArea();
}
function AgregarArea(){
	var area = $("body").encodeUrl($("#area").val());
	var datos = {area:area,pin:validaD};
	$.ajax({
		url: "AgregarArea.php",
		type: 'POST',
		async: 'true',
		data: datos
	}).done(function(respuesta){
		if(respuesta.substr(-2,2)=='Ok'){
			var cod = respuesta.split(';*0');
			cod = cod[1].split(';*');
			cod = cod[0];
			if(typeof Areas['A'+'01']=="undefined")$("#areas").html('<tr><th>Area</td><th>Editar</td><th>Eliminar</td></tr>');
			Areas['A'+cod] = $("#area").val();
			AgregarFila($("#area").val(),cod);
			$("#area").val("");
		}else if(respuesta.substr(-5,5)=='Error' && respuesta.substr(-7,1)==2){
			AbreDialog('El nombre del area es incorrecto, solo se permiten caracteres alfanumericos.','Error al Asignar!','#FF0000','');
		}else{
			AbreDialog('Ha ocurrido un error al momento de agregar el area, por favor, reecargue la pagina  e intente de nuevo.','Error al Guardar!','#FF0000','');
		}
	});	
}
function AgregarFila(area,codigo){
	var html = "";
	html += '<tr id="F'+codigo+'">';
	html += '<td class="area" id="A'+codigo+'">'+area+'</td>';
	html += '<td><img src="../images/editar.png" class="editar boton_accion" id="E'+codigo+'"></td>';
	html += '<td><img src="../images/eliminar.png" class="eliminar boton_accion" id="L'+codigo+'"></td>';
	html += '</tr>';
	$("#areas").append(html);
}
function LlenarAreas(){
	var html = "";
		html += '<tr>';
		html += '<th>Area</td>';
		html += '<th>Editar</td>';
		html += '<th>Eliminar</td>';
		html += '</tr>';
	var na = 0;
	$.each(Areas,function(codigo,area){
		html += '<tr id="F'+codigo.substr(1)+'">';
		html += '<td class="area" id="A'+codigo.substr(1)+'">'+area+'</td>';
		html += '<td><img src="../images/editar.png" class="editar boton_accion" id="E'+codigo.substr(1)+'"></td>';
		html += '<td><img src="../images/eliminar.png" class="eliminar boton_accion" id="L'+codigo.substr(1)+'"></td>';
		html += '</tr>';
		na++;
	});
	if(na==0)html += "<tr><td colspan='3'>No hay Areas Creadas</td></tr>";
	$("#areas").html(html);
}
function AbrirOpcion(element){
	var id=$(element).attr("id");
	var href = $(element).data("href");
	if(typeof href == "undefined" || href==""){
		var mensa ="Esta opcion se encuentra inhabilitada para su rol; por favor, contacte al administrador de la plataforma para mayor información.";
		var titulo ="<center style='color:#fff'>Opcion no disponible</center>";
		AbreDialog(mensa,titulo,'','');
		return 0;
	}
	var tipo = $(element).data("tipo");
	if(tipo ==1){
		var na = 0;
		var mensa = 'Por favor, seleccione un area a asignar:<br>';
		mensa +='<table style="margin:10px auto;">';
		if(Areas!=null)$.each(Areas,function(ind,val){
			mensa +='<tr><td><button class="redirArea" style="width:100%" data-value="'+ind.substr(1)+'" data-href="'+$(element).data("href")+'">'+val+'</button></td></tr>';
			na++;
		})
		mensa +='<table>';
		if(na==0)mensa = '<b>No hay areas creadas, no puede asignar Ambientes</b>';
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
					<div id="titulo_asignacion">
						Asignar Areas
					</div>
					<div id="adicionar_asignacion">
						<input type="text" id="area"/>
						<button id="agregar">Agregar</button>
					</div>
					<div id="contenido_asignacion">
						<div id="contenedor_areas">
						<table id="areas" cellpadding="0px" cellspacing="0px;">
							
						</table>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
<?PHP 
$mysqli->close();
?>
</body>
</html>
