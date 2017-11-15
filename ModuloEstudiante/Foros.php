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
$anio = $_SESSION['anio'];
$sems = $_SESSION['semestre'];

$consulta="SELECT * FROM modulos WHERE nivel='3' ";
$resultado=$mysqli->query($consulta) or die("Connection failed: " . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$seccion = $registro['seccion'];
	$cod = $registro['codigo'];
	$subCod = $registro['subcodigo'];
	$descripcion = $registro['descripcion'];
	$Modulos[$seccion][$cod][$subCod]=$descripcion;
}

$consulta="SELECT codarea,area FROM areas WHERE anio='$anio' AND sems='$sems'";
$resultado=$mysqli->query($consulta) or die("Connection failed: " . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$codarea = $registro['codarea'];
	$NomAreas[$codarea] = $registro['area'];
}
$Publicaciones = array();
$consulta="SELECT * FROM publicaciones WHERE estado='1'";
$resultado=$mysqli->query($consulta) or die("Connection failed: " . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$codpublica = $registro['codpublica'];
	$Publicaciones[$codpublica] = $registro;
}
$Categorias = array(array("image"=>'../images/ciencias.png',"name"=>"Ciencias","value"=>1),
					array("image"=>'../images/grupo.png',"name"=>"Sociedad","value"=>2),
					array("image"=>'../images/musica.png',"name"=>"Arte","value"=>3),
					array("image"=>'../images/ingenieria.png',"name"=>"Ingenieria","value"=>4),
					array("image"=>'../images/libro.png',"name"=>"Recursos Bibliograficos","value"=>5),
					array("image"=>'../images/informacion.png',"name"=>"Informacion importante","value"=>6));
?>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="icon" type="../image/png" href="../images/favicon.png" />
<title>.:Modulo Estudiante:.</title>
<script src="../js/jquery-1.10.2.js"></script>
<script src="../MultiSelect/magicsuggest-2.1.4/magicsuggest.js"></script>
<script src="../MultiSelect/magicsuggest-2.0.0/bootstrap.min.js"></script>
<script src="../js/jquery-ui.js"></script>
<script src="../js/FunPHP.js"></script>
<script src="../js/FileUploader.js"></script>
<link rel="stylesheet" href="../css/jquery-ui.css"/>
<link href="../css/icons.css?v=1.3" rel="stylesheet">
<link href="../css/fileuploader.css?v=1.2" rel="stylesheet">
     
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Dosis" rel="stylesheet"><!-- Fuente Google-->
<link href="../MultiSelect/magicsuggest-2.0.0/bootstrap.min.css" rel="stylesheet">
<link href="../MultiSelect/magicsuggest-2.0.0/custom.css" rel="stylesheet">
<link href="../MultiSelect/magicsuggest-2.0.0/magicsuggest.css" rel="stylesheet">
<link href="../MultiSelect/magicsuggest-2.0.0/gh-fork-ribbon.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="estilosEstudiante.css?v=1.1"/>
<link rel="stylesheet" type="text/css" href="estilosForos.css?v=1.6"/>
<script>
var Categorias = <?PHP echo json_encode($Categorias);?>; 
if(Categorias==null)Categorias= new Array();
var AreasM = <?PHP echo json_encode($NomAreas);?>;
var validaD = "<?PHP echo base64_encode($ip);?>";
var validaT = "<?PHP echo base64_encode($_SESSION["access_token"]["access_token"]);?>";
if(AreasM==null)AreasM = new Array();
var i_categoria = '';
$(function(){
	//var fileUploader = new FileUploader('.uploader');
    $('input:button,button').button();
	$("#dialogo").dialog({
		height: "auto",
		width: "auto",
		maxHeight:650,
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
	i_categoria = $("#categoria").magicSuggest({
        data: Categorias,
        valueField: 'value',
		maxSelection:3,
        renderer: function(data){
            return '<div style="padding: 5px; overflow:hidden;" class="contenedor_categoria">' +
                '<div style="float: left;"><img class="icono_categoria" src="' + data.image + '" /></div>' +
                '<div style="font-size:25px;float: left; margin-left: 5px;">' +
                    '<div style="font-weight: bold; color: #333; font-size:20px; line-height: 11px">' + data.name + '</div>' +
                '</div>' +
            '</div><div style="clear:both;"></div>'; // make sure we have closed our dom stuff
        },
        resultAsString: true
    });
	$("body").on("focusin","#busqueda",function(){
		$("#icono_buscar").css("filter","grayscale(0%)");	
	});
	$("body").on("focusout","#busqueda",function(){
		$("#icono_buscar").css("filter","grayscale(70%)");	
	});
	$("body").on("click","#contenedor_formulario",function(e){
		var enpublicar = $("#contenedor_publicar").find(e.target).length;
		var icon_escribir = document.getElementById("icono_escribir");
		var c_publicar = document.getElementById("contenedor_publicar");
		var c_escribir = document.getElementById("contenedor_escribir");
		var c_cat = $(".ms-res-ctn.dropdown-menu").css('display');
		if (enpublicar != 0 || icon_escribir===e.target || c_publicar===e.target || c_escribir==e.target || c_cat=="block")return 0; 
		console.log();
		$("#contenedor_publicar").hide("slow");
	});
	$("body").on("click","#icono_escribir,#contenedor_escribir",function(){
		$("#contenedor_publicar").show("slow");
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
	$("body").on("click",".opcion_card,.mapa_opccion,.contenerdor_seccion[data-href!=''],.seccion_cards[data-href!='']",function(){
		AbrirOpcion(this);
	});
	$(".opcion_card,.mapa_opccion,.contenerdor_seccion[data-href!=''],.seccion_cards[data-href!='']").each(function(){
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
	$("body").on("click","#enlace",function(){
		var enlaceA = $("#enlace").val();
		var buttons = [{text: "Aceptar", click: function() {
			$("#enlace").val($("#dialog_enlace").val());
			ValidarSet($("#enlace"));
			$(this).dialog("close");
			}},	{text: "Cancelar", click: function() {$(this).dialog("close");}}];
		var html = '<input type="text" id="dialog_enlace" placeholder="http://unal.edu.co/" style="width:400px;font-size:24px;" value="'+enlaceA+'">';
		AbreDialog(html,'Ingrese el Enlace','',buttons);
	});
	$("body").on("click","#conetenedor_boton_publicar",function(){
		ValidarPublicacion();	
	});
	
	$("body").on("change","input[type=file]",function(){
		VarificarArchivo(this);
		ValidarSet($(this));
	});
});
function ValidarSet($this){	
	if($this.val()==""){
		$this.parent().attr("seteado","");
	}else{
		$this.parent().attr("seteado","seteado");
	}
}
function ValidarPublicacion(){
	var titulo = $("#titulo_publicacion").val();
	var contenido = $("#texto_publicacion").val();
	var categoria = i_categoria.getValue().join();
	if(titulo==""){
		var buttons = [{text: "Regresar", click: function() {$(this).dialog("close");}}];
		var html = 'Por favor, diligencie el titulo de la publicación.';
		AbreDialog(html,'Validacion de Contenido','#f00',buttons);	
		return 0;	
	}
	if(contenido==""){
		var buttons = [{text: "Regresar", click: function() {$(this).dialog("close");}}];
		var html = 'Por favor, diligencie el contenido de la publicación.';
		AbreDialog(html,'Validacion de Contenido','#f00',buttons);	
		return 0;	
	}
	if(categoria==""){
		var buttons = [{text: "Regresar", click: function() {$(this).dialog("close");}}];
		var html = 'Por favor, seleccione por lo menos una categoria para la publicación.';
		AbreDialog(html,'Validacion de Contenido','#f00',buttons);	
		return 0;	
	}
	var formData = new FormData();
	formData.append("contenido"+validaT,$("body").encodeUrl(contenido));
	formData.append("categoria"+validaT,$("body").encodeUrl(categoria));
	formData.append("titulo"+validaT,$("body").encodeUrl(titulo));
	formData.append("pin",validaD);
	if($("#imagen").val()!=""){
		var imagen = document.getElementById("imagen");
		formData.append("imagen"+validaT,imagen.files[0]);
	}
	if($("#archivo").val()!=""){
		var archivo = document.getElementById("archivo");
		formData.append("archivo"+validaT,archivo.files[0]);
	}
	if($("#enlace").val()!=""){
		var enlace = $("#enlace").val();
		formData.append("enlace"+validaT,$("body").encodeUrl(enlace));
	}
	$.ajax({
		url: "GuardarPublicacion.php",
		type: 'POST',
		processData:false,
		contentType:false, 
		data: formData
	}).done(function(respuesta){
		if(respuesta.substr(-2,2)=='Ok'){
			var codigo = respuesta.split(";*0");
			codigo = codigo[1].split(";*");
			codigo = codigo[0];
			console.log(codigo);
			$("#texto_publicacion").val("");
			$("#enlace").val("");
			$("#imagen").val("");
			$("#archivo").val("");
			ValidarSet($("#enlace"));
			ValidarSet($("#imagen"));
			ValidarSet($("#archivo"));
			i_categoria.setValue([]);
			AbreDialog('La publicacion se guardo correctamente.','Guardando Informacion...','','');
			$("#contenedor_publicar").hide();
		}else if(respuesta.substr(-5,5)=='Error' && respuesta.substr(-8,2)==14){
			AbreDialog('Ocurrio un error con la extension o tipo de la imagen, por favor intente cambiando el tipo de archivo.','#FF0000','');
		}else if(respuesta.substr(-5,5)=='Error' && respuesta.substr(-8,2)==14){
			AbreDialog('Ocurrio un error con la extension o tipo del archivo, por favor intente cambiando el tipo de archivo.','#FF0000','');
		}else if(respuesta.substr(-5,5)=='Error' && respuesta.substr(-7,1)==3){
			AbreDialog('No se recibieron valores del titulo, contenido y/o categoria de la publiacion.','Error al Guardar!','#FF0000','');
		}else if(respuesta.substr(-5,5)=='Error' && respuesta.substr(-7,1)==2){
			AbreDialog('Solo se permiten valores alfa numericos en contenido y enlace.','Error al Guardar!','#FF0000','');
		}else{
			AbreDialog('Ha ocurrido un error al momento de cambiar el area, por favor, recargue la pagina  e intente de nuevo.','Error al Guardar!','#FF0000','');
		}
	});
}
function AbrirOpcion(element){
	var id=$(element).attr("id");
	var href = $(element).data("href");
	if(typeof href == "undefined" || href==""){
		var mensa ="Esta opcion se encuentra inhabilitada para su rol; por favor, contacte al administrador de la plataforma para mayor información.";
		var titulo ="<center style='color:#fff'>Opcion no disponible</center>";
		//AbreDialog(mensa,titulo,'','');
		return 0;
	}
	
	var tipo = $(element).data("tipo");
	if(tipo ==1){
		var mensa = 'Por favor, seleccione un area a asignar:<br>';
		mensa +='<table>';
		if(AreasM!=null)$.each(AreasM,function(ind,val){
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
					<div id="contenedor_herramientas">
                    	<div id="contenedor_escribir">
                        	<img id="icono_escribir" src="../images/pluma.png"/>
                        </div>
						<div id="contenedor_busqueda">
                        	<img id="icono_buscar" src="../images/buscar.png"/>
							<input type="text" id="busqueda"/>
						</div>
					</div>
					<div id="contenedor_publicar" style="display:none">
						<div id="contenedor_categoria" class="epublicar">
							<input id="categoria" placeholder="Seleccione las Categorias (Max. 3)" height="30px"/>
						</div>
						<div id="contenedor_texto" class="epublicar">
							<input id="titulo_publicacion" placeholder="Titulo de la Publicacion">
						</div>
						<div id="contenedor_texto" class="epublicar">
							<textarea id="texto_publicacion" placeholder="Contenido de la Publicacion"></textarea>
						</div>
                        <div id="contenedor_botones">
                            <div id="contenedor_adjuntos" class="epublicar">
                                <label class="label_archivo">
                                    <div class="contenedor_icono_archivo">
                                        <img class="icono_archivo" src="../images/imagen.png">
                                    </div>
                                    <div class="contenedor_texto_archivo">Imagen</div>
                                    <input id="imagen" type="file" accept="image/*">
                                </label>
                                <label class="label_archivo">
                                    <div class="contenedor_icono_archivo">
                                        <img class="icono_archivo" src="../images/documento.png">
                                    </div>
                                    <div class="contenedor_texto_archivo">Documento</div>
                                    <input id="archivo" type="file">
                                </label>
                                <label class="label_archivo">
                                    <div class="contenedor_icono_archivo">
                                        <img class="icono_archivo" src="../images/url.png">
                                    </div>
                                    <div class="contenedor_texto_archivo">Link</div>
                                    <input id="enlace" type="text">
                                </label>
                            </div>
                            <div id="conetenedor_boton_publicar">
                            	Publicar
                            </div>
						</div>
					</div>
				</div>
			<br />
			</div>
		</section>
	</div>
</body>
</html>
