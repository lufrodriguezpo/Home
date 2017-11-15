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
if((int)$nivel >2 || (int)$nivel < 1)header("Location: ../");

/*Proceso de la pagina*/
require_once("../funciones/conexion.php");

$codigoU = $_SESSION['codigoUsuario'];
$anio = $_SESSION['anio'];
$sems = $_SESSION['semestre'];
$consulta="SELECT * FROM modulos WHERE nivel='2' ";
$resultado=$mysqli->query($consulta) or die("Connection failed: " . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$seccion = $registro['seccion'];
	$cod = $registro['codigo'];
	$subCod = $registro['subcodigo'];
	$descripcion = $registro['descripcion'];
	$Modulos[$seccion][$cod][$subCod]=$descripcion;
}
$Citas = array();
$Estudiantes = array();
$Areas = array();
$consulta="SELECT * FROM carreras WHERE 1 ";
$resultado=$mysqli->query($consulta) or die("Connection failed: " . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$nivel = $registro['nivel'];
	$cod = $registro['codigo'];
	$nombre = $registro['nombre'];
	$nombre = str_replace('DOCTORADO','DT. ',$nombre);
	$nombre = str_replace('ESPECIALIZACION','ESP. ',$nombre);
	$nombre = str_replace('MAESTRÍA','M. ',$nombre);
	$nombre = str_replace('MAESTRIA','M. ',$nombre);
	$Carreras[$nivel][$cod]=utf8_encode($nombre);
}
$consulta="SELECT * FROM citaspares WHERE anio='$anio' AND sems='$sems' AND codmon='$codigoU' ORDER BY mes ASC,dia ASC,hora ASC";
$resultado=$mysqli->query($consulta) or die("Connection failed: " . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$codcita = 'C'.$registro['codcita'];
	$Citas[$codcita][0] = $registro['codcita'];
	$Citas[$codcita][1] = $registro['codarea'];
	$Citas[$codcita][2] = $registro['mes'];
	$Citas[$codcita][3] = $registro['dia'];
	$Citas[$codcita][4] = $registro['hora'];
	$Citas[$codcita][5] = $registro['codalu'];
	$Citas[$codcita][6] = $registro['codmon'];
	$Citas[$codcita][7] = $registro['fechagen'];
	$Citas[$codcita][8] = $registro['estado'];
	$Citas[$codcita][9] = $registro['observacion'];
	$Citas[$codcita][10] = $registro['respuesta'];
	$Citas[$codcita][11] = $registro['fechares'];
	$Citas[$codcita][12] = $registro['archivores'];
	$Citas[$codcita][13] = $registro['soporte'];
	$CodsUsu[$registro['codalu']] = $registro['codalu']; 
}
$CodsUsu = implode(',',$CodsUsu);
$consulta="SELECT usuario,codigo FROM usuarios WHERE FIND_IN_SET(codigo,'$CodsUsu') ORDER BY usuario";
$resultado=$mysqli->query($consulta) or die("Connection failed: USU" . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$codigo = $registro['codigo'];
	$Estudiantes[$codigo][0] = $registro['usuario'].'@unal.edu.co';
}

$consulta="SELECT carrera,nivele,nombres,apellidos,sexo,codigo,foto FROM perfiles WHERE FIND_IN_SET(codigo,'$CodsUsu')";
$resultado=$mysqli->query($consulta) or die("Connection failed: PER" . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$registro['foto'] = explode('?',$registro['foto']);
	$registro['foto'] = $registro['foto'][0];
	$codigo = $registro['codigo'];
	$Estudiantes[$codigo][1] = $registro['nombres'].' '.$registro['apellidos'];
	$Estudiantes[$codigo][2] = $Carreras[$registro['nivele']][$registro['carrera']].'';
	$Estudiantes[$codigo][3] = (is_array(@getimagesize($registro['foto'])))?$registro['foto']:'N/A';
	if($Estudiantes[$codigo][3]=='N/A')$Estudiantes[$codigo][3] = ((int)$registro['sexo']==1)?'../images/profesora.png':'../images/profesor.png';
}

$consulta="SELECT codarea,area FROM areas WHERE anio='$anio' AND sems='$sems' ";
$resultado=$mysqli->query($consulta) or die("Connection failed: " . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$codarea = $registro['codarea'];
	$Areas['A'.$codarea] = $registro['area'];
}

$consulta="SELECT codarea FROM monitores WHERE codigo='$codigoU' AND anio='$anio' AND sems='$sems'";
$resultado=$mysqli->query($consulta) or die("Connection failed: " . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$codarea = $registro['codarea'];
	$AreasM[$codarea] =$Areas['A'.$codarea];
}
?>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="icon" type="../image/png" href="../images/favicon.png" />
<title>.:Modulo Tutor:.</title>
<script src="../js/jquery-1.10.2.js"></script>
<script src="../js/jquery-ui.js"></script>
<script src="../js/FunPHP.js"></script>
<link rel="stylesheet" href="../css/jquery-ui.css"/>
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Dosis" rel="stylesheet"><!-- Fuente Google-->
<link rel="stylesheet" type="text/css" href="estilosMonitor.css?v=1.1"/>
<link rel="stylesheet" type="text/css" href="estilosHistorialCitas.css?v=1.9"/>

<script>
var Citas = <?PHP echo json_encode($Citas);?>;
var Estudiantes = <?PHP echo json_encode($Estudiantes);?>;
var Areas = <?PHP echo json_encode($Areas);?>;
var validaD = "<?PHP echo base64_encode($ip);?>";
var anio = parseInt('<?PHP echo $anio;?>');
var sems = parseInt('<?PHP echo $sems;?>');
var AreasM = <?PHP echo json_encode($AreasM);?>;
var estadoCitas = new Array('espera','aprobada','cancelada','rechazada','cancelada','realizada');
$(function(){
	$("#dialogo").dialog({
		height: "auto",
		width: "auto",
		maxHeight:650,
		dialogClass: "hide-close",
		autoOpen: false,
		resizable: false,
		draggable: false,
		open: function(){$("#telon").css("display", "block");},
		close: function(){$("#telon").css("display", "none");$('*[data-toggle=tooltip]').tooltip(); },
		show: {effect: "clip",duration: 350}
	});
	ResizeFondo();
	$(window).resize(function(){
		ResizeFondo();
	});
	LlenarCitas();
	$('*[data-toggle=tooltip]').tooltip(); 
	$('input:button,button').button();
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
	$("body").on("click",".cita",function(){
		var id = $(this).attr("id").substr(1);
		DialogoEstado(id,$(this).data("estado"));
	});
	$("body").on("change","input[type=file]",function(){
		VarificarArchivo(this);
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
function DialogoEstado(codigocita,estado){	
	var buttons = [
	{text: "Aceptar Cita", click: function() {EstadoCita(codigocita,1);$(this).dialog("close");}},
	{text: "Rechazar Cita", click: function() {EstadoCita(codigocita,3);}},
	{text: "Regresar", click: function() {$(this).dialog("close");}}];
	
	if(estado=='aprobada'){
		buttons = [{text: "Archivar", click: function() {EstadoCita(codigocita,11);}},
					{text: "Cancelar Cita", click: function() {EstadoCita(codigocita,4);$(this).dialog("close");}},
					{text: "Regresar", click: function() {$(this).dialog("close");}}];
	}
	if(estado=='rechazada' || estado == 'cancelada' || estado == 'realizada')buttons = [{text: "Regresar", click: function() {$(this).dialog("close");}}];
	var estudiante = Estudiantes[Citas['C'+codigocita][5]];
	if(typeof estudiante == "undefined")estudiante = new Array();
	var html = '';
	var cita = Citas['C'+codigocita];
	if(typeof cita=="undefined")cita = new Array();
	html +='<table width="100%">';
	html +='<tr><td><b>ESTUDIANTE:</b></td><td style="padding-left:10px;">';
	html +='<table>';
	html +='<tr><td rowspan="4"><div class="contenedor_card_perfil_foto">';
	html += '<img  class="card_perfil_foto" src="'+estudiante[3]+'"/>';
	html += '</div></td></tr>';
	html += '<tr><td><div class="card_perfil_info_nombre">'+estudiante[1]+'</div></td></tr>';
	html += '<tr><td><div class="card_perfil_info_correo">'+estudiante[0]+'</div></td></tr>'
	html += '<tr><td><div class="card_perfil_info_carrera">'+estudiante[2]+'</div></td></tr>';
	html +='</tr>';
	html += '</table>';
	html +='</td></tr>';
	html +='<tr><td><b>FECHA:</b></td><td style="padding-left:10px;">'+anio+'-'+(parseInt(cita[2])+1)+'-'+cita[3]+''+'</td></tr>';
	html +='<tr><td><b>AREA:</b></td><td style="padding-left:10px;">'+Areas['A'+cita[1]]+'</td></tr>';
	html +='<tr><td><b>OBSERVACIÓN:</b></td><td style="padding-left:10px;"><div class="observacionUser">'+cita[9]+'</div></td></tr>';
	if(cita[10]!='' && typeof cita[10]!="undefined" && estado == 'realizada')html +='<tr><td><b>RESPUESTA:</b></td><td style="padding-left:10px;"><div class="observacionUser">'+cita[10]+'</div></td></tr>';
	if(cita[12]!='' && typeof cita[12]!="undefined" && estado == 'realizada'){
		html +='<tr><td><b>DOC. APOYO:</b></td><td style="padding-left:10px;"><a href="../documentos/'+cita[12]+'" target="_blank"><img src="../images/adjunto.png" width="20"></a></td></tr>';
	}
	if(cita[13]!='' && typeof cita[13]!="undefined" && estado == 'realizada'){
		html +='<tr><td><b>DOC. SOPORTE:</b></td><td style="padding-left:10px;"><a href="../documentos/'+cita[13]+'"  target="_blank"><img src="../images/adjunto.png" width="20"></a></td></tr>';
	}
	html += '</table>';	
	AbreDialog(html,'Informacion de Cita','',buttons);
}
function EstadoCita(codigocita,estadoCita){	
	var formData = new FormData();
	formData.append("codigo",$("body").encodeUrl(codigocita));
	formData.append("codarea",$("body").encodeUrl(Citas['C'+codigocita][1]));
	formData.append("codigoE",$("body").encodeUrl(Citas['C'+codigocita][5]));
	formData.append("mes",$("body").encodeUrl(Citas['C'+codigocita][2]));
	formData.append("dia",$("body").encodeUrl(Citas['C'+codigocita][3]));
	formData.append("hora",$("body").encodeUrl(Citas['C'+codigocita][4]));
	formData.append("pin",validaD);
	if(estadoCita==3){
		var html= '<textarea id="motivo" placeholder="Por favor, Ingrese un motivo por el cual rechaza la cita."></textarea>';
		var buttons = [{text: "Guardar", click: function() {EstadoCita(codigocita,10);}},
					{text: "Cancelar", click: function() {$(this).dialog("close");}}];
		AbreDialog(html,'Motivo de rechazo','',buttons);
		return 0;
	}else if(estadoCita==10){
		estadoCita=3;
		formData.append("respuesta",$("body").encodeUrl($("#motivo").val()));
	}else if(estadoCita==11){
		var html= '<table>';
		html+='<tr><td colspan="2" style="text-align:center;"><textarea id="respuesta" placeholder="Alguna respuesta para el estudiante?"></textarea><br></td></tr>';
		html+='<tr><td><b>Documento de Apoyo:</b></td><td><input type="file" id="apoyo"/></td></tr>';
		html+='<tr><td><b>Documento de Soporte(Para la DirAcad):</b></td><td><input type="file" id="soporte"/></td></tr>';
		var buttons = [{text: "Guardar", click: function() {EstadoCita(codigocita,5);}},
					{text: "Cancelar", click: function() {$(this).dialog("close");}}];
		AbreDialog(html,'Archivar Cita','',buttons);
		return 0;
	}else if(estadoCita==5){
		formData.append("respuesta",$("body").encodeUrl($("#respuesta").val()));
		if($("#soporte").val()==""){
			alert("Es obligatorio adjuntar el documento de soporte para archivar la cita!");
			return 0;
		}
		var soporte = document.getElementById("soporte");
		var apoyo = document.getElementById("apoyo");
		formData.append("soporte",soporte.files[0]);
		console.log($("#apoyo").val());
		if($("#apoyo").val() != '')formData.append("apoyo",apoyo.files[0]);
	}
	formData.append("estadoC",$("body").encodeUrl(estadoCita));
	$.ajax({
		url: "RechazarCita.php",
		type: 'POST',
		processData:false,
		contentType:false, 
		data: formData
	}).done(function(respuesta){
		if(respuesta.substr(-2,2)=='Ok'){
			$("#C"+codigocita).data("estado",estadoCitas[estadoCita]);
			$("#E"+codigocita).attr("class","estado "+estadoCitas[estadoCita]);
			$("#C"+codigocita).attr("title",estadoCitas[estadoCita].toUpperCase());
			if(estadoCita==3){
				Citas['C'+codigocita][10] = $("#motivo").val();
			}
			if(estadoCita==5){
				if($("#apoyo").val()!=''){
					var noma = $("#apoyo").val();
					console.log(noma);
					noma = noma.split(".");
					noma  = "A"+anio+'_'+sems+'_'+codigocita+'.'+noma[1];
					Citas['C'+codigocita][12] = noma;					
				}
				if($("#soporte").val()!=''){
					var noms = $("#soporte").val();
					console.log(noms);
					noms = noms.split(".");
					noms  = "S"+anio+'_'+sems+'_'+codigocita+'.'+noms[1];
					console.log(noms);
					Citas['C'+codigocita][13] = noms;					
				}
				Citas['C'+codigocita][10] = $("#respuesta").val();
			}
			$("#dialogo").dialog("close");
		}else if(respuesta.substr(-5,5)=='Error' && respuesta.substr(-7,1)==5){
			AbreDialog('Ocurrio un error con la extension o tipo de archivo, por favor intente cambiando el tipo de archivo.','#FF0000','');
		}else if(respuesta.substr(-5,5)=='Error' && respuesta.substr(-7,1)==2){
			AbreDialog('Solo se permiten caracteres alfanumericos.','Error al Guardar!','#FF0000','');
		}else{
			AbreDialog('Ha ocurrido un error al momento de cambiar el area, por favor, recargue la pagina  e intente de nuevo.','Error al Guardar!','#FF0000','');
		}
	});
}
function LlenarCitas(){
	var html = "";
		html += '<tr>';
		html += '<th>Fecha</td>';
		html += '<th>Hora</td>';
		html += '<th>Area</td>';
		html += '<th>Estudiante</td>';
		html += '<th>Generada</td>';
		html += '<th>Estado</td>';
		html += '</tr>';
	var na = 0;
	$.each(Citas,function(codigo,cita){
		html += '<tr id="C'+codigo.substr(1)+'" class="cita" data-estado="'+estadoCitas[cita[8]]+'"  title="'+estadoCitas[cita[8]].toUpperCase()+'" data-toggle="tooltip">';
		html += '<td>'+anio+'-'+(parseInt(cita[2])+1)+'-'+cita[3]+''+'</td>';
		html += '<td>'+cita[4]+':00</td>';
		html += '<td>'+Areas['A'+cita[1]]+'</td>';
		html += '<td>'+Estudiantes[cita[5]][1]+'</td>';
		html += '<td>'+cita[7]+'</td>';
		html += '<td><div  id="E'+codigo.substr(1)+'" class="estado '+estadoCitas[cita[8]]+'">&nbsp;</div></td>';
		html += '</tr>';
		na++;
	});
	if(na==0)html += "<tr><td colspan='6'>No hay Citas Creadas</td></tr>";
	$("#citas").html(html);
}

function AbrirOpcion(element){
	var id=$(element).attr("id");
	var href = $(element).data("href");
	if(typeof href == "undefined" || href==""){
		return 0;
	}
	
	var tipo = $(element).data("tipo");
	if(tipo ==1){
		var mensa = 'Por favor, seleccione un area a asignar:<br>';
		mensa +='<table>';
		$.each(AreasM,function(ind,val){
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
	$("#contenedor_formulario").css("height",0.9*(h-hh)+"px");
	$('*[data-toggle=tooltip]').tooltip(); 
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
	$('input:button,button').button();
}
function VarificarArchivo(input) {	
	//30kb = 30720
	var tiposPerm = new Array('image/bmp','image/gif','image/jpeg','image/jpeg','image/png','application/pdf','application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/msword','application/zip','application/vnd.openxmlformats-officedocument.presentationml.presentation');
	var file = input.files[0];
	var tipo = file.type;
	var size = file.size;
	var id = input.id;
	if(tiposPerm.indexOf(tipo)==-1){
		$("#"+id).val("");
		alert("Alert este tipo de archivo es desconocido!");
	}
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
					</ul>
				</div>
			</div>
			<div id="contenedor_contenido"  class="eTodo">
				<div id="contenedor_formulario">
				
				<div id="titulo_asignacion">
					Hitorial de Citas
				</div>
				
				<div id="contenido_citas">
					<div id="contenedor_citas">
					<table id="citas" cellpadding="0px" cellspacing="0px;" width="100%">
						
					</table>
					</div>
				</div>
				
				</div>
			<br />
			</div>
		</section>
	</div>
</body>
</html>
