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
if((int)$nivel != 1)header("Location: ../");

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

$consulta = "show columns from configuracion";
$resultado = $mysqli->query($consulta) or die ("* ERROR AL CONSULTYA HOJA VIDA *". $mysqli->error);
while($registro = $resultado->fetch_assoc()){
	$columnsExist[$registro['Field']] = $registro['Field'];
}
unset($columnsExist["anio"]);
unset($columnsExist["semestre"]);
$anioA = $registro['anio'];
$semsA = $registro['semestre'];
$consulta="SELECT * FROM configuracion WHERE 1 ";
$resultado=$mysqli->query($consulta) or die("Connection failed: " . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$anio = $registro['anio'];
	$sems = $registro['semestre'];
	$Configuraciones[$anio.'-'.$sems] = $registro;
	if((int)$registro['activo']==1){
		$anioA = $registro['anio'];
		$semsA = $registro['semestre'];
	}
}
$consulta="SELECT * FROM areas WHERE anio='$anio' AND sems='$sems' order BY area";
$resultado=$mysqli->query($consulta) or die("Connection failed: ARE" . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$codarea = $registro['codarea'];
	$Areas['A'.$codarea] = $registro['area'];
}
?>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="icon" type="../image/png" href="../images/favicon.png" />
<title>.:Configuracion del Sistema:.</title>
<script src="../js/jquery-1.10.2.js"></script>
<script src="../js/jquery-ui.js"></script>
<script src="../js/jquery-ui-timepicker-addon.js"></script>
<script src="../js/FunPHP.js"></script>
<link rel="stylesheet" href="../css/jquery-ui.css"/>
<link rel="stylesheet" href="../css/jquery-ui-timepicker-addon.css"/>
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Dosis" rel="stylesheet"><!-- Fuente Google-->
<link rel="stylesheet" type="text/css" href="estilosAdmin.css?v=1.4"/>
<link rel="stylesheet" type="text/css" href="estilosConfiguracionAdmin.css?v=1.7"/>

<script>
var validaD = "<?PHP echo base64_encode($ip);?>";
var anioASemA = "<?PHP echo $anioA.'-'.$semsA;?>";
var columnsExist = <?PHP echo json_encode($columnsExist)?>;
var Configuraciones = <?PHP echo json_encode($Configuraciones)?>;
var nomSems = new Array('','I','II','Intersemestral');
if(Configuraciones==null)Configuraciones = new Array();
var formData = new FormData();
var Areas = <?PHP echo json_encode($Areas);?>;
if(Areas==null)Areas = new Array();
$(function(){
	$('input:button,button').button();
	$('.hora').timepicker();
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
	
	InicializarFormulario();
	CambiarAnioSem(anioASemA);
	$("body").on("click","#boton_cerrar",function(){
		VerificarCerrarSession();
	});
	$("body").on("click",".mapa_seccion_img",function(){
		CambiarEstadoSeccionMenu(this);
	});
	
	$("body").on("click",".card",function(){
		VisibilidadMenu(this);
	});
	$("body").on("click",".opcion_card,.mapa_opccion,.contenerdor_seccion[data-href!=''],.seccion_cards[data-href!='']",function(){
		AbrirOpcion(this);
	});
	$(".opcion_card,.mapa_opccion,.contenerdor_seccion,.seccion_cards").each(function(){
		$(this).css("cursor","pointer");
	});
	
	$("#header_titulo").css("cursor","pointer");
	$("body").on("click","#header_titulo",function(){
		window.location.href = "index.php";
	});
	
	
	$("body").on("keydown",".numero", function(e){
		var tvalidos = new Array(8,97,98,99,100,101,102,103,104,105,49,50,51,52,53,54,55,56,57,48,96);
		if(tvalidos.indexOf(e.which) == -1)e.preventDefault();
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
	$("body").on("click","button[id=limpiar]", function(e){
		ConfirmarRestablecimiento();
	});	
	$("body").on("change","select[id=anio],select[id=semestre]", function(e){
		var aniosems = $("select[id=anio]").val()+'-'+$("select[id=semestre]").val();;
		CambiarAnioSem(aniosems)
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
function ConfirmarRestablecimiento(){
	var anio = $("select[id=anio]").val();
	var sems = $("select[id=semestre]").val();
	if(anio=="" || typeof anio == "undefined"){
		alert("Seleccione un Año a Restablecer");
		return 0;
	}
	if(sems=="" || typeof sems == "undefined"){
		alert("Seleccione un semestre a Restablecer");
		return 0;
	}
	var nsems = nomSems[parseInt(sems)];
	var html = "¿Esta seguro de Restablecer el Año <b>"+anio+"</b> - Semestre <b>"+nsems+"</b>?<br><span style='font-style:italic;;font-size:18px;'>";
	html += "*Se borraran todos los registros de este periodo.</span>";
	var buttons = [{text: "Aceptar", click: function() {RestablecerSistema(anio,sems);}},
					{text: "Regresar", click: function() {$(this).dialog("close");}}];
	AbreDialog(html,"Confirmacion de proceso","",buttons);
}
function RestablecerSistema(anio,sems){
	var datos = {};
	datos['anio'] = $("body").encodeUrl(sems);
	datos['sems'] = $("body").encodeUrl(anio);
	datos['pin'] = validaD;
	$.ajax({
		url: "RestablecerPeriodo.php",
		type: 'POST',
		async: 'true',
		data: datos
	}).done(function(respuesta){
		if(respuesta.substr(-2,2)=='Ok'){
			AbreDialog('El proceso se realizo satisfactoriamente','Guardando información...','','');
		}else if(respuesta.substr(-5,5)=='Error' && respuesta.substr(-7,1)==2){
			AbreDialog('Ha ocurrido un error con los datos recibidos, por favor recargue la pagina.','Error al Guardar!','#FF0000','');
		}else{
			AbreDialog('Ha ocurrido un error, por favor recargue la pagina.','Error al Guardar!','#FF0000','');
		}
	});
}
function CambiarAnioSem(aniosems){
	var confi = Configuraciones[aniosems];
	var as = aniosems.split('-');
	if(typeof confi == "undefined") confi = new Array();
	$("select[id=anio] option[value="+as[0]+"]").attr("selected","selected");
	$("select[id=semestre] option[value="+as[1]+"]").attr("selected","selected");
	formData.append("anio",$("select[id=anio]").val());
	formData.append("semestre",$("select[id=semestre]").val());
	$.each(columnsExist, function(id,val){
		var value = confi[id];
		if(typeof value == "undefined")value = '';
		var elemen = $("#"+id);
		if(elemen.length > 0)var thtml = elemen[0]['localName'];
		else {
			var thtml =  '';
		}
		if(thtml == 'input'){
			var type = elemen.attr("type");
			if(type == 'text')elemen.val(value);
			if(type == 'radio')$("input[type=radio][id="+id+"][value="+value+"]").attr("checked","true");
			if(type == 'checkbox'){
				$("input[type=checkbox][id="+id+"]").each(function(){
					console.log($(this).prop("checked"));
					$(this).prop("checked",false);
				});
				$("input[type=checkbox][id="+id+"][value="+value+"]").prop("checked",true);
			}
		}else if(thtml == 'select' ){
			value = value.replace("/","");
			if(value=="")value = "0";
			$("select[id="+id+"] option[value="+value+"]").attr("selected","selected");
			if($("select[id="+id+"]").attr("class")=="tipodepar")ActualicarMunicipios(value,$("select[id="+id+"]").attr("ciudad"));
			if($("select[id="+id+"]").attr("class")=="nivelestudio")CambiarCarreras($("select[id="+id+"]"));
		}
	});
	
}
function GrabarFormulario(){
	if($("select[id=anio]").val() =="" || $("select[id=semestre]").val() ==""){
		AbreDialog('<center>Se requiere un semestre y un Año para guardar la configuración.</center>','Validación del Proceso','#f00','');
		return 0;
	}
	$.ajax({
		url: "GuardarConfiguracion.php",
		type: 'POST',
		processData:false,
		contentType:false, 
		data: formData
	}).done(function(respuesta){
		console.log(respuesta);
		
		if(respuesta.substr(-2,2)=='Ok'){
			AbreDialog('La información se guardo satisfactoriamente.','Información Guardada','','');
			InicializarFormulario();
		}else if(respuesta.substr(-5,5)=='Error' && respuesta.substr(-7,1)==1){
			AbreDialog('No se recibio el Año al momento de guardar.','Error al Guardar!','#FF0000','');
		}else if(respuesta.substr(-5,5)=='Error' && respuesta.substr(-7,1)==2){
			AbreDialog('No se recibio el Semestre al momento de guardar.','Error al Guardar!','#FF0000','');
		}else{
			AbreDialog('<center>Ha ocurrido un error al momento de guardar la informacion, por favor revise su conexcion de internet, salga del sistema y verifique si su informacion se guardo correctamente</center>','Error al Guardar!','#FF0000','');
		}
	});
}
function InicializarFormulario(){
	formData = new FormData();
	formData.append("anio",$("select[id=anio]").val());
	formData.append("semestre",$("select[id=semestre]").val());
	formData.append("pin",validaD);
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
		if($("input[id="+id+"]:checked").length==0)val = '-';
		console.log(val);
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
					<div id="titulo_asignacion">
						Configuración del Sistema
					</div>
					<div id="contenedor_secciones">
						<div class="configuracion_seccion">						
							<span class="seccion_titulo">
								Periodo a Trabajar
							</span><br/>					
							<div class="seccion_contenido">
								<table class="formulario">
									<tr>
										<td class="titulo_valor">Año:</td>
										<td class="valor"><select id="anio">
											<?PHP
												echo '<option value="">Seleccione..</option>';
												echo '<option value="'.(date("Y")+1).'">'.(date("Y")+1).'</option>';
												for($i=date("Y");$i>=date("Y")-5;$i--){
													echo '<option value="'.$i.'">'.$i.'</option>';
												}
											?>
										</select></td>
									</tr>
									<tr>
										<td class="titulo_valor">Semestre:</td>
										<td class="valor">
										<select id="semestre">
											<option value="">Selccione un semes..</option>;
											<option value="1">Semestre I</option>;
											<option value="2">Semestre II</option>;
											<option value="3">Semestre Intersemestral</option>;
										</select></td>
									</tr>
									<tr>
										<td class="titulo_valor">Activo:</td>
										<td class="valor">
											<input type="checkbox" name="activo" id="activo" value="1"/>
										</td>
									</tr>
								</table>
							</div>
						</div>
						
						
						<div class="configuracion_seccion">						
							<span class="seccion_titulo">
								Informacion del programa
							</span><br/>					
							<div class="seccion_contenido">
								<table class="formulario">
									<tr>
										<td class="titulo_valor">Telefono:</td>
										<td class="valor">
											<input type="text" class="numero" id="telefono" />
										</td>
									</tr>
									<tr>
										<td class="titulo_valor">Ubicación:</td>
										<td class="valor">
											<input type="text" id="ubicacion"/>
										</td>
									</tr>
									<tr>
										<td class="titulo_valor">Correo:</td>
										<td class="valor">
											<input type="text" id="correo"/>
										</td>
									</tr>
									<tr>
										<td class="titulo_valor">Encargado:</td>
										<td class="valor">
											<input type="text" id="encargado"/>
										</td>
									</tr>
								</table>
							</div>
						</div>
						
						<div class="configuracion_seccion">						
							<span class="seccion_titulo">
								Progama Ambientes
							</span><br/>					
							<div class="seccion_contenido">
								<table class="formulario">
									<tr>
										<td class="titulo_valor">Horas por monitor:</td>
										<td class="valor">
											<input type="text" class="numero cantidad" id="horasa"  size="2"/>
										</td>
									</tr>
									<tr>
										<td class="titulo_valor">Max. cantidad de estudiantes por sesión:</td>
										<td class="valor">
											<input type="text" class="numero cantidad" id="maxestudiantesa" size="2"/>
										</td>
									</tr>
								</table>
							</div>
						</div>
						
						<div class="configuracion_seccion">						
							<span class="seccion_titulo">
								Progama Pares
							</span><br/>					
							<div class="seccion_contenido">
								<table class="formulario">
									<tr>
										<td class="titulo_valor">Horas por monitor:</td>
										<td class="valor">
											<input type="text" class="numero cantidad" id="horasp"  size="2"/>
										</td>
									</tr>
									<tr>
										<td class="titulo_valor">Hora de Inicio:</td>
										<td class="valor">
											<input type="text" class="hora" id="horainicio"/>
										</td>
									</tr>
									<tr>
										<td class="titulo_valor">Hora de Finalización:</td>
										<td class="valor">
											<input type="text" class="hora" id="horafin"/>
										</td>
									</tr>
								</table>
							</div>
						</div>
						
					</div>
						<br />
						<div>
						<center>
							<button id="grabar">GRABAR INFORMACIÓN</button>
							<button id="limpiar" title="Eliminar registros del semestre actual" style="width:60px;height:60px;overflow:hidden;border-radius:30px;padding:0px">
								<img src="../images/basura.png" height="50"/>
							</button>
						</center>
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
