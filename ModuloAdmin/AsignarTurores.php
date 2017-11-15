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
if((int)$nivel !=1)header("Location: ../");

/*Proceso de la pagina*/
require_once("../funciones/conexion.php");


$anio = $_SESSION['anio'];
$sems = $_SESSION['semestre'];
$consulta="SELECT * FROM modulos WHERE nivel='1' ";
$resultado=$mysqli->query($consulta) or die("Connection failed: Modulos" . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$seccion = $registro['seccion'];
	$cod = $registro['codigo'];
	$subCod = $registro['subcodigo'];
	$descripcion = $registro['descripcion'];
	$Modulos[$seccion][$cod][$subCod]=$descripcion;
}

$Monitores = array();
$Users = array();
$ValsM = array();
$List = array();
$consulta="SELECT codigo,codarea FROM monitores WHERE sems='$sems' AND anio='$anio' ORDER BY codigo";
$resultado=$mysqli->query($consulta) or die("Connection failed: MON" . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$codigo = $registro['codigo'];
	$codarea = $registro['codarea'];
	$MonitoresC[$codigo][$codarea] = $codigo.'-'.$codarea;
	$CodsUsu[$codigo] = $codigo;
}
$CodsUsu = implode(',',$CodsUsu);
$consulta="SELECT usuario,codigo FROM usuarios WHERE FIND_IN_SET(codigo,'$CodsUsu') ORDER BY usuario";
$resultado=$mysqli->query($consulta) or die("Connection failed: USU" . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$codigo = $registro['codigo'];
	$Users[$codigo][0] = $registro['usuario'];
	$Users[$codigo][1] = '';
}
$consulta="SELECT usuario,codigo FROM usuarios WHERE nivel='3' ORDER BY usuario";
$resultado=$mysqli->query($consulta) or die("Connection failed: USU" . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$List[] = $registro['usuario'];
}

$consulta="SELECT codigo,nombres,apellidos FROM perfiles WHERE FIND_IN_SET(codigo,'$CodsUsu') ORDER BY nombres,apellidos";
$resultado=$mysqli->query($consulta) or die("Connection failed: PER" . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$codigo = $registro['codigo'];
	$Users[$codigo][1] = $registro['apellidos'].' '.$registro['nombres'];
}

$consulta="SELECT * FROM areas WHERE anio='$anio' AND sems='$sems' order BY area";
$resultado=$mysqli->query($consulta) or die("Connection failed: ARE" . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$codarea = $registro['codarea'];
	$Areas['A'.$codarea] = $registro['area'];
}

foreach($Users as $codigo => $user){
	$areas = $MonitoresC[$codigo];
	foreach($areas as $codarea => $dupla){
		$Monitores[$user[0].'-'.$codarea] = $codigo;
		$ValsM[]= $user[0].'-'.$codarea;
	}
}
?>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="icon" type="../image/png" href="../images/favicon.png" />
<title>.:Asignar Tutores:.</title>
<script src="../js/jquery-1.10.2.js"></script>
<script src="../js/jquery-ui.js"></script>
<script src="../js/FunPHP.js"></script>
<link rel="stylesheet" href="../css/jquery-ui.css"/>
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Dosis" rel="stylesheet"><!-- Fuente Google-->
<link rel="stylesheet" type="text/css" href="estilosAdmin.css?v=1.4"/>
<link rel="stylesheet" type="text/css" href="estilosAsignarTurores.css?v=1.11"/>
<script>
var validaD = "<?PHP echo base64_encode($ip);?>";
var Monitores = <?PHP echo json_encode($Monitores);?>;
if(Monitores==null)Monitores = new Array();
var ValsM = <?PHP echo json_encode($ValsM);?>;
if(ValsM==null)ValsM = new Array();
var Users = <?PHP echo json_encode($Users);?>;
if(Users==null)Users = new Array();
var Areas = <?PHP echo json_encode($Areas);?>;
if(Areas==null)Areas = new Array();
var List = <?PHP echo json_encode($List);?>;
if(List==null)List = new Array();
$(function(){
    $( "#usuario" ).autocomplete({
      source: List
    });
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
	LlenarMonitores();
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
	var coor = codigo.split('-');
	var buttons = [{text: "Aceptar", click: function() {EditarMonitor(codigo);}},{text: "Cancelar", click: function() {$(this).dialog("close");}}];
	var areas = $("#area").html();
	var selec = "<select id='edicion'>"+areas+"</select>";
	var html = "<table border='0'>";
	html +="<tr><td style='padding:5px'><b>Usuario:</b></td><td style='padding:5px'><input type='text' id='usu' style='text-align:center;width:200px;' value='"+coor[0]+"' readonly></td></tr>";
	html +="<tr><td style='padding:5px'><b>Area:</b></td><td style='padding:5px'> "+selec+"</td></tr>";
	html +="<table>";
	AbreDialog(html,'Edición de carga <i>'+coor[0]+'-'+Areas['A'+coor[1]]+"</i>",'#009966',buttons);
	$("select[id=edicion] opcion[value="+coor[1]+"]").attr("selected",true);
}
function EditarMonitor(codigo){	
	var edicion = $("#edicion").val();
	if(edicion==''){
		AbreDialog('Debe seleccionar una area para asignar.','Error de Validación!','#FF0000','');
		return 0;
	}
	var coor = codigo.split('-');
	var codigoArea = coor[0]+"-"+edicion;
	
	if(ValsM.indexOf(codigoArea) != -1){
		AbreDialog('La asignacion que intenta hacer ya se encuentra realizada.','Error de Validación!','#FF0000','');
		return 0;
	}
	var codigoU= Monitores[codigo];
	var codareaC = $("body").encodeUrl(coor[1]);
	var codigoC = $("body").encodeUrl(codigoU);
	var edicionC = $("body").encodeUrl(edicion);
	var datos = {codarea:codareaC,codigo:codigoC,edicion:edicionC,pin:validaD};
	$.ajax({
		url: "EditarArea.php",
		type: 'POST',
		async: 'true',
		data: datos
	}).done(function(respuesta){
		if(respuesta.substr(-2,2)=='Ok'){
			$("#dialogo").dialog("close");
			console.log(codigoArea);
			var user = Users[codigoU];
			if(user == null)user = new Array('N/A','N/A');
			var html = "";
			html += '<tr id="F'+codigoArea+'">';
			html += '<td class="usuario" id="U'+codigoArea+'">'+coor[0]+'</td>';
			html += '<td class="usuario" id="N'+codigoArea+'">'+user[1]+'</td>';
			html += '<td class="usuario" id="A'+codigoArea+'">'+Areas['A'+edicion]+'</td>';
			html += '<td><img src="../images/editar.png" class="editar boton_accion" id="E'+codigoArea+'"></td>';
			html += '<td><img src="../images/eliminar.png" class="eliminar boton_accion" id="L'+codigoArea+'"></td>';
			html += '</tr>';
			$("#F"+codigo).replaceWith(html);
			var indb = ValsM.indexOf(codigoArea);
			Monitores[codigoArea] = codigoU;
			delete(ValsM[codigo]);
		}else if(respuesta.substr(-5,5)=='Error' && respuesta.substr(-7,1)==1){
			AbreDialog('El usuario no se encuentra asignado como monitor en el momento, por favor, recargue la pagina e intente nuevamente.','Error al Eliminar!','#FF0000','');
		}else if(respuesta.substr(-5,5)=='Error' && respuesta.substr(-7,1)==2){
			AbreDialog('El usuario es incorrecto, solo se permiten caracteres alfanumericos.','Error al Eliminar!','#FF0000','');
		}else{
			AbreDialog('Ha ocurrido un error al momento de agregar al tutor, por favor, reecargue la pagina  e intente de nuevo.','Error al Guardar!','#FF0000','');
		}
	});
}
function ConfirmarEliminacion(element){
	var codigo=$(element).attr("id").substr(1);
	var coor = codigo.split('-');
	var buttons = [{text: "Aceptar", click: function() {EliminarMonitor(codigo);}},{text: "Cancelar", click: function() {$(this).dialog("close");}}]
	AbreDialog('Esta seguro de eliminar la asignacion <b>'+coor[0]+'-'+Areas['A'+coor[1]]+"</b>?",'Confirmación','#009966',buttons);
}
function EliminarMonitor(codigoArea){
	var codigoAreaC = $("body").encodeUrl(codigoArea);
	var codigo = Monitores[codigoArea];
	var codigoC = $("body").encodeUrl(Monitores[codigoArea]);
	var datos = {codigoarea:codigoAreaC,codigo:codigoC,pin:validaD};
	$.ajax({
		url: "EliminarAsignacion.php",
		type: 'POST',
		async: 'true',
		data: datos
	}).done(function(respuesta){
		if(respuesta.substr(-2,2)=='Ok'){
			$("#dialogo").dialog("close");
			$("#F"+codigoArea).remove();
			var indb = ValsM.indexOf(codigoArea);
			delete(ValsM[indb]);
		}else if(respuesta.substr(-5,5)=='Error' && respuesta.substr(-7,1)==1){
			AbreDialog('El usuario no se encuentra asignado como monitor en el momento, por favor, recargue la pagina e intente nuevamente.','Error al Eliminar!','#FF0000','');
		}else if(respuesta.substr(-5,5)=='Error' && respuesta.substr(-7,1)==2){
			AbreDialog('El usuario es incorrecto, solo se permiten caracteres alfanumericos.','Error al Eliminar!','#FF0000','');
		}else{
			AbreDialog('Ha ocurrido un error al momento de agregar al tutor, por favor, reecargue la pagina  e intente de nuevo.','Error al Guardar!','#FF0000','');
		}
	});
}
function ValidarAgregar(){
	var usuario = $("#usuario").val();
	var codarea = $("#area").val();
	if(usuario==''){
		AbreDialog('No se aceptan valores nulos. Por favor ingrese un usuario.','Error de Validación!','#FF0000','');
		return 0;
	}
	if(codarea==''){
		AbreDialog('Debe seleccionar una area para asignar.','Error de Validación!','#FF0000','');
		return 0;
	}
	console.log(ValsM+'-'+usuario+'-'+codarea);
	if(ValsM.indexOf(usuario+'-'+codarea) != -1){
		AbreDialog('La asignacion que intenta hacer ya se encuentra realizada.','Error de Validación!','#FF0000','');
		return 0;
	}
	AgregarUsuario();
}
function AgregarUsuario(){
	var usuario = $("#usuario").val();
	var codarea = $("#area").val();
	var usuarioC = $("body").encodeUrl($("#usuario").val());
	var codareaC = $("body").encodeUrl($("#area").val());
	var datos = {usuario:usuarioC,codarea:codareaC,pin:validaD};
	$.ajax({
		url: "AgregarAsignacion.php",
		type: 'POST',
		async: 'true',
		data: datos
	}).done(function(respuesta){
		if(respuesta.substr(-2,2)=='Ok'){
			var cod = respuesta.split(';*0');
			cod = cod[1].split(';*');
			cod = cod[0];
			ValsM[ValsM.length] = usuario+'-'+codarea;
			Monitores[usuario+'-'+codarea] = cod;
			if($("#usuarios").data("vacio")=="si"){
				$("#usuarios").html('<tr><th>Usuario</th><th>Nombres</th><th>Area</th><th>Editar</th><th>Eliminar</th></tr>');
				$("#usuarios").data("vacio","no");
			}
			AgregarFila(usuario+'-'+codarea,cod);
			$("#usuario").val("");
		}else if(respuesta.substr(-5,5)=='Error' && respuesta.substr(-7,1)==1){
			AbreDialog('El usuario tiene nivel Administrador, no se puede modificar el nivel.','Error al Asignar!','#FF0000','');
		}else if(respuesta.substr(-5,5)=='Error' && respuesta.substr(-7,1)==2){
			AbreDialog('El usuario es incorrecto, solo se permiten caracteres alfanumericos.','Error al Asignar!','#FF0000','');
		}else{
			AbreDialog('Ha ocurrido un error al momento de agregar al tutor, por favor, reecargue la pagina  e intente de nuevo.','Error al Guardar!','#FF0000','');
		}
	});	
}
function AgregarFila(codigoArea,codigo){
	var user = Users[codigo];
	if(user == null)user = new Array('N/A','N/A');
	var coor = codigoArea.split('-');
	var codarea = coor[1];
	var html = "";
	html += '<tr id="F'+codigoArea+'">';
	html += '<td class="usuario" id="U'+codigoArea+'">'+coor[0]+'</td>';
	html += '<td class="usuario" id="N'+codigoArea+'">'+user[1]+'</td>';
	html += '<td class="usuario" id="A'+codigoArea+'">'+Areas['A'+codarea]+'</td>';
	html += '<td><img src="../images/editar.png" class="editar boton_accion" id="E'+codigoArea+'"></td>';
	html += '<td><img src="../images/eliminar.png" class="eliminar boton_accion" id="L'+codigoArea+'"></td>';
	html += '</tr>';
	$("#usuarios").append(html);
}
function LlenarMonitores(){
	var html = "";
	html += '<tr>';
	html += '<th>Usuario</td>';
	html += '<th>Nombres</td>';
	html += '<th>Area</td>';
	html += '<th>Editar</td>';
	html += '<th>Eliminar</td>';
	html += '</tr>';
	$n= 0;
	$("#usuarios").html(html);
	$.each(Monitores,function(codigoArea,codigo){
		AgregarFila(codigoArea,codigo);
		$n++;
	});
	if($n==0)$("#usuarios").append("<tr><td colspan='5'>No hay monitores Asignados</td></tr>");
	if($n==0)$("#usuarios").data("vacio","si");
}
function AbrirOpcion(element){
	var id=$(element).attr("id");
	var href = $(element).data("href");
	if(typeof href == "undefined" || href==""){
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
						Asignar Tutores
					</div>
					<div id="adicionar_asignacion">
						<input type="text" id="usuario"/>
						<select id="area" style="font-size:20px;margin-left:10px;">
							<option value="" style="font-size:20px;" >Area</option>
							<?PHP
							foreach($Areas as $acodarea => $area){
								$area = utf8_decode($area);
								echo '<option value="'.substr($acodarea,1).'" style="font-size:20px;" title="'.$area.'">'.substr($area,0,15).'</option>';
							}
							?>
						</select>
						<button id="agregar">Agregar</button>
					</div>
					<div id="contenido_asignacion">
						<div id="contenedor_usuarios">
						<table id="usuarios" cellpadding="0px" cellspacing="0px;">
							
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
