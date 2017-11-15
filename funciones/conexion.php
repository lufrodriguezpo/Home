<?PHP 
if($_SERVER['SERVER_NAME']=="localhost"){
	//$link=mysql_connect("localhost","root","","tutos_un");
	$mysqli=new mysqli("localhost","root","","tutos_un");
	//mysql_select_db("tutos_un",$link);
}
?>
