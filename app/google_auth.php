<?PHP
class GoogleAuth{
	protected $client;
	
	public function __construct(Google_Client $googleClient = null){
		$this->client = $googleClient;
		if($this->client){
			$this->client->setClientId('52227469803-iiga6gvnps9lskqibritd118e4u8dbo7.apps.googleusercontent.com');
			$this->client->setClientSecret('Pd6K4DgB1Uvg9gF7QlW__GD5');
			$this->client->setRedirectUri('http://localhost/Tutos_Unal/Home/CompruebaInicioSession.php');
			$this->client->setScopes('email');
		}
	}
	
	public function isLoggedIn(){
		return isset($_SESSION["access_token"]);
	}
	
	public function getAuthUrl(){
		return $this->client->createAuthUrl();
	}
	public function checkRedirectCode(){
		if(isset($_GET['code'])){
			$this->client->authenticate($_GET['code']);
			$this->setToken($this->client->getAccessToken());
			$payload = $this->getPayload();
			foreach($payload as $nombre => $val)$_SESSION[$nombre]=$val;//Guardando info de usuario en session
			return true;
		}
		return false;
	}
	public function setToken($token){
		$_SESSION['access_token'] = $token;
		$this->client->setAccessToken($token);
	}
	public function getPayload(){
		$payload = $this->client->verifyIdToken();
		return $payload;
	}
}
?>
