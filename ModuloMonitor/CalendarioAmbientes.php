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

$codareaE = base64_decode($_REQUEST['v']);

/*Proceso de la pagina*/
require_once("../funciones/conexion.php");

$anio = $_SESSION['anio'];
$sems = $_SESSION['semestre'];
$codigoU = $_SESSION['codigoUsuario'];
$consulta="SELECT * FROM modulos WHERE nivel='2' ";
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
if($NomAreas[$codareaE] == ''){
	echo "<script>alert('No se recibio el area correcta!');window.location.href = 'index.php';</script>";
}
$consulta="SELECT codigo FROM monitores WHERE anio='$anio' AND sems='$sems' AND codarea='$codareaE'";
$resultado=$mysqli->query($consulta) or die("Connection failed: " . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$CodsUsu[$registro["codigo"]] = $registro["codigo"];
}
$Monitores = array();
//if(count($CodsUsu)==0)??????
$CodsUsu = implode(',',$CodsUsu);
$consulta="SELECT usuario,codigo FROM usuarios WHERE FIND_IN_SET(codigo,'$CodsUsu') ORDER BY usuario";
$resultado=$mysqli->query($consulta) or die("Connection failed: USU" . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$codigo = $registro['codigo'];
	$Monitores[$codigo][0] = $registro['usuario'].'@unal.edu.co';
	$Monitores[$codigo][1] = '';
	$Monitores[$codigo][2] = '';
	$Monitores[$codigo][3] = '../images/profesor.png';
	$Monitores[$codigo][4] = '';
	$Monitores[$codigo][5] = $registro['usuario'];
}

$consulta="SELECT carrera,nivele,nombres,apellidos,sexo,codigo,foto FROM perfiles WHERE FIND_IN_SET(codigo,'$CodsUsu')";
$resultado=$mysqli->query($consulta) or die("Connection failed: PER" . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$registro['foto'] = explode('?',$registro['foto']);
	$registro['foto'] = $registro['foto'][0];
	$codigo = $registro['codigo'];
	$Monitores[$codigo][1] = $registro['nombres'].' '.$registro['apellidos'];
	$Monitores[$codigo][2] = $Carreras[$registro['nivele']][$registro['carrera']].'';
	$Monitores[$codigo][3] = (is_array(@getimagesize($registro['foto'])))?$registro['foto']:'N/A';
	if($Monitores[$codigo][3]=='N/A')$Monitores[$codigo][3] = ((int)$registro['sexo']==1)?'../images/profesora.png':'../images/profesor.png';
}
$Horarios = array();
$InfoCita = array();
$consulta="SELECT * FROM horariosambientes WHERE anio='$anio' AND sems='$sems' AND codarea = '$codareaE' AND monitores  LIKE '%$codigoU%'";
$resultado=$mysqli->query($consulta) or die("Connection failed: " . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$mes = (int)$registro['mes'];
	$dia = (int)$registro['dia'];
	$hora = (int)$registro['hora'];
	$Horarios[$mes.'-'.$dia.'-'.$hora] = 'S';
	$Horarios[$mes.'-'.$dia] = 'S';
	$Horarios[$mes] = 'S';
	$InfoCita[$mes.'-'.$dia.'-'.$hora] = array($registro['monitores'],$registro['lugar']);
}
$Agendadas = array();
function DiasSemana($nd){
	$nds = $nd;
	if($nd>6)$nds= $nd-7;
	else if($nd <0)$nds= 7+$nd;
	return $nds;
}
$Meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$Semana = array("Lunes","Martes","Miercoles","Jueves","Viernes","Sabado","Domingo");	
$horaIni = $_SESSION['horainicio'];
$horaIni = explode(':',$horaIni);
$horaIni = $horaIni[0];
$horaFin = $_SESSION['horafin'];
$horaFin = explode(':',$horaFin);
$horaFin = $horaFin[0];
$Horas = array();
$RangoHoras = array();
for($i=$horaIni;$i<=$horaFin;$i++)$Horas[]=$i;
foreach($Horas as $ind => $h)$RangoHoras["h".$ind] = $h;
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
<link rel="stylesheet" type="text/css" href="estilosCalendario.css?v=1.10"/>
<link rel="stylesheet" type="text/css" href="estilosMonitor.css?v=1.2"/>
<link rel="stylesheet" type="text/css" href="estilosCalendarioAmbientes.css?v=1.3"/>
<script>
var anio = parseInt('<?PHP echo $anio;?>');
var sems = parseInt('<?PHP echo $sems;?>');
var Meses = <?PHP echo json_encode($Meses);?>;
var NomSemana = <?PHP echo json_encode($Semana);?>;
var horaInicio = parseInt('<?PHP echo $horaIni;?>');
var horaFin = parseInt('<?PHP echo $horaFin;?>');
var rangoHora = <?PHP echo json_encode($RangoHoras);?>;
var Monitores = <?PHP echo json_encode($Monitores);?>;
var AreasM = <?PHP echo json_encode($NomAreas);?>; 
var InfoCita = <?PHP echo json_encode($InfoCita);?>;
var Horarios = <?PHP echo json_encode($Horarios);?>;
if(Horarios==null)Horarios = {};
var Agendadas = <?PHP echo json_encode($Agendadas);?>;
if(Agendadas==null)Agendadas = {};
var codarea = '<?PHP echo base64_encode($codareaE);?>';
var validaD = "<?PHP echo base64_encode($ip);?>";
var diaActual;
var mousePress = 0;
var cellInicio = '';
var cellFin = '';
$(function(){
	diaActual = new Date();
	ArmarCalendario(anio,diaActual.getMonth(),diaActual.getDate());
	ArmarSemana();
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
	$("body").on("click",".botton_meses.botton_meses",function(){
		var delta = parseInt($(this).attr("id").substr(2)); 
		if(delta == -1 && diaActual.getMonth()==0){
			AbreDialog('No tiene permitido agendar citas para el año '+(anio-1)+'.','Accion no permitida!','#FF0000','');
			return 0;
		}
		if(delta == 1 && diaActual.getMonth()==11){
			AbreDialog('No tiene permitido agendar citas para el año '+(anio+1)+'.','Accion no permitida!','#FF0000','');
			return 0;
		}
		diaActual = new Date(anio,diaActual.getMonth()+delta,diaActual.getDate());
		ArmarCalendario(anio,diaActual.getMonth(),diaActual.getDate());
	});
	$("body").on("click","#mes",function(){
		$("#dias").hide(100);
		$("#meses").show(100);
	});
	$("body").on("click",".contenedor_calendario_mes",function(){
		var mes = parseInt($(this).attr("id").substr(1));
		diaActual = new Date(anio,mes,diaActual.getDate());
		ArmarCalendario(anio,diaActual.getMonth(),diaActual.getDate());
		ArmarSemana();
		$("#dias").show(100);
		$("#meses").hide(100);
	});
	$("body").on("click",".mes_pasado:not(.anio_pasado),.mes_siguiente:not(.anio_siguiente)",function(){
		var id = $(this).attr("id").substr(1);
		id = id.split('-');
		diaActual = new Date(anio,parseInt(id[0]),parseInt(id[1]));
		ArmarCalendario(anio,diaActual.getMonth(),diaActual.getDate());
		ArmarSemana();
	});
	
		
	$("body").on("dblclick",".text_dia.mes_actual",function(){
		var id = $(this).attr("id").substr(1);
		id = id.split('-');
		$(".text_dia.mes_actual.actual").attr("class","text_dia mes_actual");
		$(this).attr("class","text_dia mes_actual actual");
		diaActual = new Date(anio,parseInt(id[0]),parseInt(id[1]));
		ArmarSemana();
		$("#dias").hide(100);
		$("#semanas").show(100);
	});
		
	$("body").on("click",".text_dia.mes_actual:not(.actual)",function(){
		var id = $(this).attr("id").substr(1);
		id = id.split('-');
		$(".text_dia.mes_actual.actual").attr("class","text_dia mes_actual");
		$(this).attr("class","text_dia mes_actual actual");
		diaActual = new Date(anio,parseInt(id[0]),parseInt(id[1]));
		ArmarSemana();
	});	
	$("body").on("click","#mes_semana",function(){
		$("#semanas").hide(100);
		$("#dias").show(100);
	});
	$("body").on("click",".contenedor_calendario_titulo_dia_semana:not(.dactual,#mes_semana)",function(){
		var date = $(this).data("date");
		date = date.split("-"); 
		diaActual = new Date(anio,parseInt(date[1]),parseInt(date[0]));
		ArmarCalendario(anio,diaActual.getMonth(),diaActual.getDate());
		ArmarSemana();
	});
	$("body").on("click",".contenedor_calendario_hora[asignada=asignada]",function(){
		DialogoAsignada($(this));
	});
});
function DialogoAsignada(element){
	var agendada = element.attr("agendada");
	var html = '';
	var hora = element.data("hora");
	var dia = element.data("dia");
	var mes = element.data("mes");
	html += '<table class="inscripcion">';
	var inf = InfoCita[parseInt(mes)+'-'+parseInt(dia)+'-'+parseInt(hora)];
	mons = inf[0].split(',');
	var nmons = '';
	var nm = 0;
	if(mons != null)$.each(mons,function(ind,codm){
		if(typeof Monitores[codm]!="undefined"){
			nmons+= '<tr><td>'+Monitores[codm][1]+'('+Monitores[codm][5]+')'+'</td></tr>';
			nm++;
		}
	});
	html += '<tr><th>Fecha:</th><td>'+anio+'/'+parseInt(mes)+'/'+parseInt(dia)+'</td>';
	html += '<tr><th>Hora:</th><td>'+element.data("hora")+':00</td>';
	html += '<tr><th>Lugar:</th><td>'+inf[1]+'</td>';
	html += '<tr><th rowspan="'+nm+'">Monitores:</th>'+nmons.substr(4);
	html += '</table>';		
	AbreDialog(html,"Información de Ambiente","","");
}
function ArmarSemana(){
	var semana = {0:RDiaSem(diaActual.getDay()-3),1:RDiaSem(diaActual.getDay()-2),2:RDiaSem(diaActual.getDay()-1),3:RDiaSem(diaActual.getDay()),4:RDiaSem(diaActual.getDay()+1),5:RDiaSem(diaActual.getDay()+2),6:RDiaSem(diaActual.getDay()+3)};
	
	var ultimoDiaMesA = new Date(anio,diaActual.getMonth(),0);
	var ultimoDiaMes = new Date(anio,diaActual.getMonth()+1,0);
	var diasMes =  {};
	var posd = diaActual.getDate()-3;
	for(var i = 0;i<=6;i++){
		if(posd <= 0)diasMes[i] = new Array(ultimoDiaMesA.getDate()+posd,diaActual.getMonth()-1);
		else if(posd > ultimoDiaMes.getDate())diasMes[i] = new Array(posd-ultimoDiaMes.getDate(),diaActual.getMonth()+1);
		else diasMes[i] = new Array(posd,diaActual.getMonth());
		posd++;
	}
	
	var html = '';
	html+='<div class="contenedor_calendrio_dia_semana titulo_horas">';
	html+='<div class="contenedor_calendario_titulo_dia_semana eDiaSemana" id="mes_semana">'+Meses[diaActual.getMonth()]+'</div>';
	$.each(rangoHora,function(ind,val){
		html+='<div class="contenedor_calendario_hora eDiaSemana titulo_hora" id="'+ind+'">'+(val)+':00</div>'
	});
	html+='</div>';
	$.each(semana,function(inds,nds){
		var dis = ' dactual';
		if(inds!=3)dis=' enable';
		var diaGraf = new Date(anio,diasMes[inds][1],diasMes[inds][0]);
		if(diaGraf.getFullYear() != anio)return 0;
		html+='<div class="contenedor_calendrio_dia_semana" id="d'+inds+'">';
		html+='<div class="contenedor_calendario_titulo_dia_semana eDiaSemana'+dis+'" id="t'+(inds-3)+'" data-date="'+diasMes[inds][0]+'-'+diasMes[inds][1]+'">'+NomSemana[nds]+' '+diasMes[inds][0]+'</div>';
		$.each(rangoHora,function(ind,val){
			var agen = Agendadas[parseInt(diasMes[inds][1])+'-'+parseInt(diasMes[inds][0])+'-'+parseInt(val)];
			var selec = Horarios[parseInt(diasMes[inds][1])+'-'+parseInt(diasMes[inds][0])+'-'+parseInt(val)];
			var info = '';
			var titleD = '';
			var datagen = '';
			if(selec == 'S'){
				var inf = InfoCita[parseInt(diasMes[inds][1])+'-'+parseInt(diasMes[inds][0])+'-'+parseInt(val)];
				mons = inf[0].split(',');
				var nmons = '';
				var nmonsT = '';
				if(mons != null)$.each(mons,function(ind,codm){
					if(typeof Monitores[codm]!="undefined"){
						nmons+=','+Monitores[codm][5];
						nmonsT+=','+Monitores[codm][1]+'('+Monitores[codm][5]+')';
					}
				});				
				info +='<div><span class="titLug">Lugar:</span>'+inf[1]+'</div>';
				info +='<div><span class="titMon">Mon:</span>';
				info +=nmons.substr(1)+'</div>';
				titleD = 'Lugar: '+inf[1]+'	Monitores: '+nmonsT.substr(1);
			}
			if(selec=='S')selec = 'asignada="asignada"';
			else selec = '';
			if(agen=='S')datagen = 'agendada="si"';
			html+='<div title="'+titleD+'" class="contenedor_calendario_hora eDiaSemana horenable" data-dia="'+diasMes[inds][0];
			html+='" data-hora="'+val+'" data-mes="'+diasMes[inds][1]+'" '+selec+' '+datagen+'>';
			html+=info+'</div>';
		});
		html += '</div>';
	});
	$("#semanas").html(html);
	
}
function RDiaSem(nd){
	nd = parseInt(nd)-1;
	var nds = nd;
	if(nd>6)nds= nd-7;
	else if(nd <0)nds= 7+nd;
	return nds;
}
function numerosRango(inic,fin,includ){
	inic = parseInt(inic);
	fin = parseInt(fin);
	if(isNaN(inic) || isNaN(fin))console.log("Error rango: Incio="+inici+' Fin='+fin);
	var rango = {};
	var pos =0;
	if(inic < fin){
		for(var i = inic+1-includ;i<fin+includ;i++){
			rango[pos]=i;
			pos++;
		}
	}else if(inic > fin){
		for(var i = fin+1-includ;i<inic+includ;i++){
			rango[pos]=i;
			pos++;
		}
	}else if(inic==fin){
		if(includ==1)rango[0]=inic;
	}
	return rango;
}
function ArmarCalendario(ano,mes,dia){
	var ultimoDiaMesA = new Date(ano,mes,0);
	var primerDiaMes = new Date(ano,mes,1);
	var ultimoDiaMes = new Date(ano,mes+1,0);
	var Calendario = new Array();
	var flatMes = -1;
	var ndia = ultimoDiaMesA.getDate()-ultimoDiaMesA.getDay();//Primer de la primera semana
	if(ndia==ultimoDiaMesA.getDate())ndia-=7;
	var diaGraf = '';
	var semana = '';
	var html = '';
	clasesMeses = {"-1":"mes_pasado","0":"mes_actual","1":"mes_siguiente"};
	var clasedia= '';
	var idM ='';
	
	$(".contenedor_calendario_mes.actual").attr("class","contenedor_calendario_mes");
	$("#m"+mes).attr("class","contenedor_calendario_mes actual");
	for(var i = 0; i< 6; i++){//Semanas son 6 semanas para un mes 
		semana = new Array();
		html += '<div class="contenedor_semana">';
		for(var j = 0;j<=6;j++){//dias semana
			ndia++;
			semana[j] = new Array(ndia,flatMes);
			diaGraf = new Date(ano,mes+flatMes,ndia);
			if(dia==ndia && flatMes==0)clasedia= 'actual';
			else clasedia= '';
			idM = 'id="d'+(mes+flatMes)+'-'+ndia+'"';
			var iddh = (parseInt(mes+flatMes))+'-'+parseInt(ndia);
			if(Horarios[iddh]=='S')clasedia += ' asignado';
			if(mes==0 && flatMes==-1)clasedia += ' anio_pasado';
			if(mes==11 && flatMes==1)clasedia += ' anio_siguiente';
			html += '<div class="contenedor_dia_mes"><div class="text_dia '+clasesMeses[String(flatMes)]+' '+clasedia+'" '+idM+'>'+ndia+'</div></div>';
			if(flatMes==-1 && diaGraf.getDate()==ultimoDiaMesA.getDate()){
				ndia = 0;
				flatMes= 0;
				diaGraf = new Date(ano,mes+flatMes,ndia);
			}else if(flatMes==0 && diaGraf.getDate()==ultimoDiaMes.getDate()){
				ndia = 0;
				flatMes= 1;
				diaGraf = new Date(ano,mes+flatMes,ndia);
			}
		}
		html += '</div>';
		Calendario[i] = semana;
	}
	$("#dias .contenedor_dias_mes").html(html);
	$("#dias .nombre_mes").html(Meses[mes]);
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
							TUTOR	
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
								
				<div id="titulo_form">
					Horarios Disponibles -<?PHP echo utf8_decode($NomAreas[$codareaE]);?>
				</div>
				<section class="contenedor_calendario_dias" id="dias">
					<div class="contenedor_titulo eCalendario_dias">
						
						<div class="botton_meses botton_mes_anterior" id="bm-1"> < </div>
						<div class="titulo_mes">
							<div class="nombre_mes" id="mes">
								Octubre
							</div>
							<div class="subtitulo_mes">
								<?PHP 
									echo $anio;
								?>
							</div>
						</div>
						<div class="botton_meses botton_mes_siguiente" id="bm1"> > </div>
						
					</div>
					<div class="contenedor_dias_semana eCalendario_dias">
						<div class="dia_semana eDiasSemana">Lunes</div>
						<div class="dia_semana eDiasSemana">Martes</div>
						<div class="dia_semana eDiasSemana">Miercoles</div>
						<div class="dia_semana eDiasSemana">Jueves</div>
						<div class="dia_semana eDiasSemana">Viernes</div>
						<div class="dia_semana eDiasSemana">Sabado</div>
						<div class="dia_semana eDiasSemana">Domingo</div>
					</div>
					<div class="contenedor_dias_mes eCalendario_dias">
						<div class="contenedor_semana">
							<div class="contenedor_dia_mes"><div class="text_dia">1</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">2</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">3</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">4</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">5</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">6</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">7</div></div>
						</div>	
						<div class="contenedor_semana">
							<div class="contenedor_dia_mes"><div class="text_dia">1</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">2</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">3</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">4</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">5</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">6</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">7</div></div>
						</div>	
						<div class="contenedor_semana">
							<div class="contenedor_dia_mes"><div class="text_dia">1</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">2</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia actual">3</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">4</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">5</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">6</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">7</div></div>
						</div>	
						<div class="contenedor_semana">
							<div class="contenedor_dia_mes"><div class="text_dia">1</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">2</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">3</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">4</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">5</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">6</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">7</div></div>
						</div>	
						<div class="contenedor_semana">
							<div class="contenedor_dia_mes"><div class="text_dia">1</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">2</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">3</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">4</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">5</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">6</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">7</div></div>
						</div>	
						<div class="contenedor_semana">
							<div class="contenedor_dia_mes"><div class="text_dia">1</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">2</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">3</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">4</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">5</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">6</div></div>
							<div class="contenedor_dia_mes"><div class="text_dia">7</div></div>
						</div>				
					</div>
				</section>
				<section class="contenedor_calendario_meses" id="meses" style="display:none;">
					<div class="contenedor_calendrio_trimestre">
						<div class="contenedor_calendario_mes" id="m0">Enero</div>
						<div class="contenedor_calendario_mes" id="m1">Febrero</div>
						<div class="contenedor_calendario_mes"  id="m2">Marzo</div>
					</div>
					<div class="contenedor_calendrio_trimestre">
						<div class="contenedor_calendario_mes"  id="m3">Abril</div>
						<div class="contenedor_calendario_mes"  id="m4">Mayo</div>
						<div class="contenedor_calendario_mes"  id="m5">Junio</div>
					</div>
					<div class="contenedor_calendrio_trimestre">
						<div class="contenedor_calendario_mes"  id="m6">Julio</div>
						<div class="contenedor_calendario_mes"  id="m7">Agosto</div>
						<div class="contenedor_calendario_mes"  id="m8">Septiembre</div>
					</div>
					<div class="contenedor_calendrio_trimestre">
						<div class="contenedor_calendario_mes actual"  id="m9">Octubre</div>
						<div class="contenedor_calendario_mes"  id="m10">Noviembre</div>
						<div class="contenedor_calendario_mes"  id="m11">Diciembre</div>
					</div>
				</section>
				<section class="contenedor_calendario_semanas" id="semanas" style="display:none;">
					
					<div class="contenedor_calendrio_dia_semana titulo_horas">
						<div class="contenedor_calendario_titulo_dia_semana eDiaSemana">Hora</div>
						<?PHP
							foreach($Horas as $ind => $h)echo '<div class="contenedor_calendario_hora eDiaSemana titulo_hora" id="h'.$ind.'">'.$h.':00</div>';
						?>
					</div>
					<?PHP
						for($i=0;$i<=6;$i++){
							$html = '';
							$html .= '<div class="contenedor_calendrio_dia_semana" id="d'.$i.'">';
							$html .= '<div class="contenedor_calendario_titulo_dia_semana eDiaSemana">'.$Semana[$i].'</div>';
							for($j=$horaIni;$j<=$horaFin;$j++)$html .= '<div class="contenedor_calendario_hora eDiaSemana horenable" data-dia="'.$i.'" data-hora="'.$j.'"></div>';
							$html .= '</div>';
							echo $html;
						}
					?>
				</section>
				
				</div>
			<br />
			</div>
		</section>
	</div>
</body>
</html>
