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
$codigoA = $_SESSION['codigoUsuario'];
/*Proceso de la pagina*/
require_once("../funciones/conexion.php");


$consulta = "SELECT codeparta, departamento FROM departamentos ORDER BY departamento";
$resultado = $mysqli->query($consulta) or die("<< ERROR AL CONSULTAR CONTENIDOS PAISES >>".$mysqli->error);
while ($registro = $resultado->fetch_assoc()){
	$codepar = $registro['codeparta'];
	$Departamentos['D'.$codepar] = utf8_encode($registro['departamento']);
}
$consulta = "SELECT codeparta, codmuni, ciudad FROM municipios ORDER BY ciudad";
$resultado = $mysqli->query($consulta) or die("<< ERROR AL CONSULTAR CONTENIDOS PAISES >>".$mysqli->error);
while ($registro =$resultado->fetch_assoc()){
	$codmuni = $registro['codmuni'];
	$codepar = $registro['codeparta'];
	$Municipios['D'.$codepar]['M'.$codmuni] = utf8_encode($registro['ciudad']);
}

$consulta="SELECT * FROM modulos WHERE nivel='2' ";
$resultado=$mysqli->query($consulta) or die("Connection failed: " . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$seccion = $registro['seccion'];
	$cod = $registro['codigo'];
	$subCod = $registro['subcodigo'];
	$descripcion = $registro['descripcion'];
	$Modulos[$seccion][$cod][$subCod]=$descripcion;
}
$consulta = "SELECT * FROM perfiles WHERE codigo='$codigoA' ";
$resultado=$mysqli->query($consulta) or die("<< ERROR AL CONSULTAR CONTENIDOS PROFE >>".$mysqli->error);
$Perfil = $resultado->fetch_assoc();

$Perfil['departanac'] = 'D'.$Perfil['departanac'];
$Perfil['ciudadnac'] = 'M'.$Perfil['ciudadnac'];
$Perfil['carrera'] = 'C'.$Perfil['carrera'];


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
	$Carreras[$nivel]['C'.$cod]=utf8_encode($nombre);
}
foreach($Carreras as $niv => $carrs){
	asort($carrs);
	$Carreras[$niv] = $carrs;
}
asort($Perfil);
?>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="icon" type="../image/png" href="../images/favicon.png" />
<title>.:Modulo Estudiante:.</title>
<script src="../js/jquery-1.10.2.js"></script>
<script src="../js/jquery-ui.js"></script>
<link rel="stylesheet" href="../css/jquery-ui.css"/>
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Dosis" rel="stylesheet"><!-- Fuente Google-->
<link rel="stylesheet" type="text/css" href="estilosEstudiante.css?v=1.1"/>
<link rel="stylesheet" type="text/css" href="estilosPerfil.css?v=1.7"/>

<script>
var validaD = "<?PHP echo base64_encode($ip);?>";
var Departamentos = <?PHP echo json_encode($Departamentos);?>;
var Municipios = <?PHP echo json_encode($Municipios);?>;
var nivelesE = {"1":"Pregrado","2":"Posgrado"};
var tiposSexo = {"1":"Femenino","2":"Masculino"};
var Carreras = <?PHP echo json_encode($Carreras);?>;
var dias = [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ];
var diasMin = [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ];
var meses = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
var mesesMin = ["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"];
var rangYear = "<?PHP echo ((int)date("Y")-50).":".((int)date("Y")+5)?>";
var Perfil = <?PHP echo json_encode($Perfil);?>;
var codalu = '<?PHP echo $codigoA;?>';
var formData = new FormData();
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
	
	ResizeFondo();
	$(window).resize(function(){
		ResizeFondo();
	});
	
	
	$(".tipodepar").each(function(index){
		var opciones = '<option value="">Seleccione un Departamento</option>';
		$.each(Departamentos,function(index,val){
			opciones += '<option value="'+index+'">'+val+'</option>';
		});
		$(this).html(opciones);
	});
	
	$(".tipociudad").each(function(index){
		var opciones = '<option value="">Seleccione una ciudad</option>';
		$(this).html(opciones);
	});
	
	$(".nivelestudio").each(function(index){
		var opciones = '<option value="">Nivel</option>';
		$.each(nivelesE ,function(index,val){
			opciones += '<option value="'+index+'">'+val+'</option>';
		});
		$(this).html(opciones);
	});
	
	$(".tiposexo").each(function(index){
		var opciones = '<option value="">Seleccione un sexo</option>';
		$.each(tiposSexo,function(index,val){
			opciones += '<option value="'+index+'">'+val+'</option>';
		});
		$(this).html(opciones);
	});
	
	
	if(Perfil!=null)$.each(Perfil, function(id,value){
		var elemen = $("#"+id);
		if(elemen.length > 0)var thtml = elemen[0]['localName'];
		else {
			var thtml =  '';
		}
		if(thtml == 'input'){
			var type = elemen.attr("type");
			if(type == 'text')elemen.val(value);
			if(type == 'radio')$("input[type=radio][id="+id+"][value="+value+"]").attr("checked","true");
			if(type == 'checkbox')$("input[type=checkbox][id="+id+"][value="+value+"]").attr("checked","true");
		}else if(thtml == 'select' ){
			value = value.replace("/","");
			if(value=="")value = "0";
			$("select[id="+id+"] option[value="+value+"]").attr("selected","selected");
			console.log(value+'-'+$("select[id="+id+"]").attr("class"));
			if($("select[id="+id+"]").attr("class")=="tipodepar")ActualicarMunicipios(value,$("select[id="+id+"]").attr("ciudad"));
			if($("select[id="+id+"]").attr("class")=="nivelestudio")CambiarCarreras($("select[id="+id+"]"));
		}
		if(id=="fechanacio")ActualizaEdad($("#"+id));
	});
	
});

$(document).ready(function(){
	InicializarFormulario();
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
	
	$("body").on("change",".nivelestudio",function(){
		CambiarCarreras($(this));
	});
	
	$("body").on("keydown",".numero", function(e){
		var tvalidos = new Array(8,97,98,99,100,101,102,103,104,105,49,50,51,52,53,54,55,56,57,48,96);
		if(tvalidos.indexOf(e.which) == -1)e.preventDefault();
	});
	
	
	$("body").on("change","select[class=tipodepar]",function(){
		var ciudad = $(this).attr("ciudad");
		ActualicarMunicipios($(this).val(),ciudad);
	});
	
	
	$("body").on("change","input",function(){
		GrabarInput(this);
	});
	
	$("body").on("change","select", function(e){
		GrabarSelect(this);
	});
	$("body").on("click","button[id='grabar']", function(e){
		GrabarFormulario();
	});

});
function InicializarFormulario(){
	formData = new FormData();
	formData.append("codigo",codalu);
	formData.append("pin",validaD);
}
function GrabarFormulario(){
	$.ajax({
		url: "GrabarPerfil.php",
		type: 'POST',
		processData:false,
		contentType:false, 
		data: formData
	}).done(function(respuesta){
		console.log(respuesta);
		if(respuesta.substr(-2,2)=='Ok'){
			AbreDialog('<center>La información se guardo satisfactoriamente</center>','<center>Informacion Guardada</center>','','');
		}else{
			AbreDialog('<center>Ha ocurrido un error al momento de guardar la informacion, por favor revise su conexcion de internet, salga del sistema y verifique si su informacion se guardo correctamente</center>','Error al Guardar!','#FF0000','');
		}
		InicializarFormulario();
	});
}
function GrabarSelect(elementActual){
	var id = $(elementActual).attr("id");
	var val = $("select[id='"+id+"'] option:selected").attr("value");
	var clase = $("select[id='"+id+"']").attr("class");
	if(clase=="tipodepar" || clase=="tipociudad" || clase=="carrera")val = val.substr(1);
	formData.append(id,val);
}
function GrabarInput(elementActual){
	var id = $(elementActual).attr("id");
	var type = $(elementActual).attr("type");
	if(type == "text"){
		formData.append(id,$(elementActual).val());
	}else if(type == "radio"){
		formData.append(id,$(elementActual).attr("value"));
	}else if(type == "checkbox"){
		var val = $(elementActual).attr("value");
		if($(elementActual).attr("checked")!="checked")val = '-';
		formData.append(id,val);
	}else if(type == "file"){
		var file = elementActual.files[0];
		typoImg = file.type.split('/');
		typoImg = typoImg[1];
		formData.append('archivo',elementActual.files[0]);
		formData.append('imag',imag);
		//formData = new FormData(document.getElementById("formarchivo"));
	}
}
function ActualicarMunicipios(codepar,id){
	console.log(codepar+'-'+id);
	var munis = Municipios[codepar];
	var opciones = '<option value="">Seleccione un ciudad</option>';
	if(munis!=null)$.each(munis,function(index,val){
		opciones += '<option value="'+index+'">'+val+'</option>';
	});
	$("select[id="+id+"]").html(opciones);
}
function CambiarCarreras(element){
	console.log(element);
	var carrera = element.attr("carrera");
	var val = element.val();
	var opciones = '<option value="">selecione una carrera</option>';
	$.each(Carreras[val] ,function(index,val){
	
		opciones += '<option value="'+index+'" title="'+val+'">'+val.substr(0,30)+'</option>';
	});
	$("#"+carrera).html(opciones);
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
							Estudiante	
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
						Informacion del Usuario
					</div>
					
					<div id="contenido_formulario">
						<table width="100%" style="margin:10px auto;">
							<tr>
								<td class="titulo" width="20%">
									Identificación
								</td>
								<td class="valor" width="30%">
									<input type="text" id="cedula" class="numero"/>
								</td>
								<td class="titulo" width="20%">
									Carrera
								</td>
								<td class="valor" width="30%">
									<select class="nivelestudio" id="nivele" carrera="carrera"></select>
									<br />
									<select class="carrera" id="carrera"><option>seleccione una carrera</option></select>
								</td>
							</tr>
							<tr>
								<td class="titulo" width="20%">
									Ciudad de Nacimiento
								</td>
								<td class="valor" width="30%">
									<select class="tipodepar" id="departanac" ciudad="ciudadnac" data-obligatorio="si" title="Departamento de Nacimiento"></select> <br> 
                            		<select class="tipociudad" id="ciudadnac" data-obligatorio="si" title="Municipio de Nacimiento"></select>
								</td>
								
								<td class="titulo">
									Fecha de Nacimiento
								</td>
								<td class="valor">
									<input type="text" id="fechanac" class="fecha"/>
								</td>
								
								
							</tr>
							<tr>
								<td class="titulo">
									Sexo
								</td>
								<td class="valor">
									<select class="tiposexo" id="sexo" data-obligatorio="si" title="Sexo"></select>
								</td>
								
								<td class="titulo">
									Periodo de Ingreso
								</td>
								<td class="valor">
									<select  id="fechaingreso" data-obligatorio="si" title="Periodo de Ingreso">
									<option value="">Seleccione</option>
									<?PHP 
										for($i = date('y')-10;$i<=date('y');$i++){
											$anio = str_pad($i,2,'0',STR_PAD_LEFT);
											echo '<option value="20'.$anio.'-1">20'.$anio.' - I</option>';
											if(!($i==date('y') && date('m')<7))echo '<option value="20'.$anio.'-2">20'.$anio.' - II</option>';
										}
									?>
									</select>
								</td>
							</tr>
						</table>
						<br />
						<center><button id="grabar">GRABAR INFORMACIÓN</button></center>
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
