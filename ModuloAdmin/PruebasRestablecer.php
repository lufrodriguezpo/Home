<?PHP
session_start();
require_once("../funciones/funciones.php");
/*
$sems = '1';
$anio = '17';
if(ValidarCadena($sems)!=1)exit("2;Error");
if(ValidarCadena($anio)!=1)exit("2;Error");
require_once("../funciones/conexion.php");
$tablas = array('areas','monitores','horariospares','horariosambientes','citaspares','citasambientes');
$anio = $_SESSION['anio'];
$sems = $_SESSION['semestre'];
$archivo = array();
foreach($tablas as $ind => $tabla){
	$consulta = "show columns from $tabla";
	$resultado = $mysqli->query($consulta) or die ("* ERROR AL CONSULTA HOJA VIDA *". $mysqli->error);
	$filaN = '';
	while($registro = $resultado->fetch_assoc()){
		$filaN .= ','.$registro['Field'];
	}
	$filaN = '('.substr($filaN,1).')';
	$fInsert = '';
	$consulta = "SELECT * FROM $tabla WHERE anio='$anio' AND sems='$sems'";
	$resultado=$mysqli->query($consulta) or die("No seguro - ". $mysqli->error.'Ok.');
	while($registro = $resultado->fetch_assoc()){
		$fila = '';
		foreach($registro as $campo => $valor){
			$fila .= ", '$valor' ";
		}
		$fila = '('.substr($fila,1).')';
		$fInsert .= ','.$fila;
	}
	$fInsert = substr($fInsert,1);
	$inserT = "INSERT INTO $tabla $filaN VALUES $fInsert";
	$archivo[] = $inserT;
}
$archivo=implode('*/-/*',$archivo);
$archivo = base64_encode($archivo);
$nombre_archivo = 'Respaldo/RE'.$anio.'_'.$sems.'_'.date("d_m_Y_H_i_s").'.txt';
if($farchivo = fopen($nombre_archivo, "a")){
	if(fwrite($farchivo,$archivo))echo 'Ok';
	else exit('4;Error');
	fclose($farchivo);
}else exit('3;Error');
foreach($tablas as $ind => $tabla){	
	$remplazo = "DELETE FROM $tabla WHERE sems='$sems' AND anio='$anio'";
	$resultado=$mysqli->query($remplazo) or die("No seguro - ".$mysqli->close().'Ok.');
}*/
$cadena = 'SU5TRVJUIElOVE8gYXJlYXMgKGFuaW8sc2Vtcyxjb2RhcmVhLGFyZWEpIFZBTFVFUyAoICcyMDE3JyAsICcxJyAsICcwMScgLCAnQ2FsY3VsbyBJJyApLCggJzIwMTcnICwgJzEnICwgJzAyJyAsICdDYWx1Y3VsbyBJSScgKSwoICcyMDE3JyAsICcxJyAsICcwMycgLCAnU29jaWFsZXMnICksKCAnMjAxNycgLCAnMScgLCAnMDQnICwgJ0VzcGHxb2wnICkqLy0vKklOU0VSVCBJTlRPIGhvcmFyaW9zcGFyZXMgKGFuaW8sc2Vtcyxjb2RpZ28sbWVzLGRpYSxob3JhLGNvZGFyZWEpIFZBTFVFUyAoICcyMDE3JyAsICcxJyAsICcwMDAwMDA1JyAsICcxMCcgLCAnOCcgLCAnMTInICwgJzAxJyApLCggJzIwMTcnICwgJzEnICwgJzAwMDAwMDUnICwgJzEwJyAsICc4JyAsICcxMycgLCAnMDEnICksKCAnMjAxNycgLCAnMScgLCAnMDAwMDAwNScgLCAnMTAnICwgJzgnICwgJzE0JyAsICcwMScgKSwoICcyMDE3JyAsICcxJyAsICcwMDAwMDA1JyAsICcxMCcgLCAnOCcgLCAnMTUnICwgJzAxJyApLCggJzIwMTcnICwgJzEnICwgJzAwMDAwMDUnICwgJzEwJyAsICc5JyAsICcxMicgLCAnMDEnICksKCAnMjAxNycgLCAnMScgLCAnMDAwMDAwNScgLCAnMTAnICwgJzknICwgJzEzJyAsICcwMScgKSwoICcyMDE3JyAsICcxJyAsICcwMDAwMDA1JyAsICcxMCcgLCAnOScgLCAnMTQnICwgJzAxJyApLCggJzIwMTcnICwgJzEnICwgJzAwMDAwMDUnICwgJzEwJyAsICc5JyAsICcxNScgLCAnMDEnICksKCAnMjAxNycgLCAnMScgLCAnMDAwMDAwNScgLCAnMTAnICwgJzknICwgJzE2JyAsICcwMScgKSwoICcyMDE3JyAsICcxJyAsICcwMDAwMDA1JyAsICcxMCcgLCAnOScgLCAnMTcnICwgJzAxJyApKi8tLypJTlNFUlQgSU5UTyBob3Jhcmlvc2FtYmllbnRlcyAoYW5pbyxzZW1zLG1lcyxkaWEsaG9yYSxjb2RhcmVhLG1vbml0b3JlcyxsdWdhcikgVkFMVUVTICggJzIwMTcnICwgJzEnICwgJzEwJyAsICc2JyAsICcxMicgLCAnMDEnICwgJzAwMDAwMDUsMDAwMDAwMicgLCAnTGRvcGQga2QnICksKCAnMjAxNycgLCAnMScgLCAnMTAnICwgJzYnICwgJzEzJyAsICcwMScgLCAnMDAwMDAwNSwwMDAwMDAyJyAsICdMZG9wZCBrZCcgKSwoICcyMDE3JyAsICcxJyAsICcxMCcgLCAnNicgLCAnMTQnICwgJzAxJyAsICcwMDAwMDA1LDAwMDAwMDInICwgJ0xkb3BkIGtkJyApLCggJzIwMTcnICwgJzEnICwgJzEwJyAsICc2JyAsICcxNScgLCAnMDEnICwgJzAwMDAwMDUsMDAwMDAwMicgLCAnTGRvcGQga2QnICksKCAnMjAxNycgLCAnMScgLCAnMTAnICwgJzYnICwgJzE2JyAsICcwMScgLCAnMDAwMDAwNSwwMDAwMDAyJyAsICdMZG9wZCBrZCcgKSovLS8qSU5TRVJUIElOVE8gY2l0YXNwYXJlcyAoYW5pbyxzZW1zLGNvZGNpdGEsY29kYXJlYSxtZXMsZGlhLGhvcmEsY29kYWx1LGNvZG1vbixvYnNlcnZhY2lvbixlc3RhZG8sZmVjaGFnZW4scmVzcHVlc3RhLGZlY2hhcmVzLGFyY2hpdm9yZXMsc29wb3J0ZSkgVkFMVUVTICggJzIwMTcnICwgJzEnICwgJzAwMDEnICwgJzAxJyAsICcxMCcgLCAnOCcgLCAnMTInICwgJzAwMDAwMDEnICwgJzAwMDAwMDUnICwgJ0VzdG95IG1hbCBlbiBjYWxjdWxvLCBwbGlzIGFpdWRhJyAsICc1JyAsICcyMDE3LTExLTA3IDA0OjQ3OjM0JyAsICcsbSAsc3MgLG0gc2Zr8XNmbScgLCAnMDAwMC0wMC0wMCAwMDowMDowMCcgLCAnJyAsICdTMjAxN18xXzAwMDEuanBnJyApLCggJzIwMTcnICwgJzEnICwgJzAwMDInICwgJzAxJyAsICcxMCcgLCAnOCcgLCAnMTMnICwgJzAwMDAwMDEnICwgJzAwMDAwMDUnICwgJ2ZqbmZramZubCcgLCAnNScgLCAnMjAxNy0xMS0wNyAwNTowNzoxOCcgLCAna/FtZvFrZm5nbG5nJyAsICcwMDAwLTAwLTAwIDAwOjAwOjAwJyAsICcnICwgJ1MyMDE3XzFfMDAwMi5qcGcnICksKCAnMjAxNycgLCAnMScgLCAnMDAwMycgLCAnMDEnICwgJzEwJyAsICc4JyAsICcxNCcgLCAnMDAwMDAwMScgLCAnMDAwMDAwNScgLCAnZGtsdm5sa25samcnICwgJzUnICwgJzIwMTctMTEtMDcgMDU6MjE6MzQnICwgJ25rZGZmamYgbCAtZmtmIC1ma2bxJyAsICcwMDAwLTAwLTAwIDAwOjAwOjAwJyAsICdBMjAxN18xXzAwMDMuanBnJyAsICdTMjAxN18xXzAwMDMucGRmJyApLCggJzIwMTcnICwgJzEnICwgJzAwMDQnICwgJzAxJyAsICcxMCcgLCAnOCcgLCAnMTUnICwgJzAwMDAwMDEnICwgJzAwMDAwMDUnICwgJ2NubG5nIGdsa2cnICwgJzInICwgJzIwMTctMTEtMDcgMDU6MjE6NTUnICwgJycgLCAnMDAwMC0wMC0wMCAwMDowMDowMCcgLCAnJyAsICcnICk=';
$cons = base64_decode($cadena);
$cons = explode('*/-/*',$cons);
foreach($cons as $ind => $val)echo $val.'<hr>';
$mysqli->close();
echo 'Ok';
?>