<?PHP 
session_start();
require_once("funciones/conexion.php");
$_SESSION['perfil']=$_REQUEST;
$codigoU = $_SESSION['codigoUsuario'];
$apellidos = $_SESSION['perfil']['name']['familyName'];
$nombres = $_SESSION['perfil']['name']['givenName'];
$fotoUrl = $_SESSION['perfil']['image']['url'];

$fotoUrl = explode('?',$fotoUrl);
$fotoUrl = $fotoUrl[0];
if(!is_array(@getimagesize($fotoUrl))){
	$fotoUrl ='../images/usuario.png';
	$consulta="SELECT sexo FROM perfiles WHERE codigo='$codigoA'";
	$resultado=$mysqli->query($consulta) or die("Connection failed: MON" . $mysqli->error.' - '.$mysqli->close().'Ok.');
	$registro = $resultado->fetch_assoc();
	if((int)$registro['sexo']==1)$fotoUrl ='../images/usuaria.png';
}
$_SESSION['perfil']['image']['url']=$fotoUrl;
$_SESSION['nombres'] = $nombres;
$_SESSION['apellidos'] = $apellidos;
$insertar = "INSERT INTO perfiles (codigo,nombres,apellidos,foto) VALUES ('$codigoU','$nombres','$apellidos','$fotoUrl') ";
$insertar .= "ON DUPLICATE KEY UPDATE nombres='$nombres', apellidos='$apellidos', foto='$fotoUrl'";
$resultado=$mysqli->query($insertar) or die("Connection failed: " . $mysqli->connect_error.'- '.$mysqli->close().'Ok.');
$mysqli->close();
?>
