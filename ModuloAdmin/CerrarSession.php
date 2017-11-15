<?PHP
session_start();
echo $_REQUEST["id"].'-'.$_SESSION["email"];
if($_REQUEST["id"]==$_SESSION["email"]){
	$_SESSION=array();
	echo 'Ok';
}else{
	echo 'Error';
}
?>
