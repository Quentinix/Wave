<?php
namespace UserManager;

use Exception;

require 'UserManager/Config.php';

class UserManager {
	private $sqlConfig;
	private $sqlConnect;
	
	function __construct() {
		$this->sqlConfig = new Config();
		$this->sqlConnect = mysqli_connect(Config::nameHost(), Config::nameUser(), Config::namePass(), Config::nameDb());
	}
	
	function __destruct() {
		mysqli_close($this->sqlConnect);
	}
	
	function version(){
		return "0.0.1";
	}
	
	function accountCreate($user, $pass, $email = NULL, $nom = NULL, $prenom = NULL, $adresse = NULL, $ville = NULL, $code_postal = NULL) {
		if($user == "")
			throw new Exception("User n'est pas renseignée.");
		if($pass == "")
			throw new Exception("Pass n'est pas renseignée.");
		$exeTimeBegin = time();
		$sqlResult = mysqli_query($this->sqlConnect, "SELECT * FROM " . Config::tablenameUser() . " WHERE `user` LIKE '".$user."'");
		if (mysqli_errno($this->sqlConnect))
			throw new Exception("Echec requête SQL : ".mysqli_errno($this->sqlConnect)." : ".mysqli_error($this->sqlConnect));
		if (mysqli_fetch_array($sqlResult) != NULL)
			return false;
		$passCrypt = $this->hashCrypt($pass);
		mysqli_query($this->sqlConnect, "INSERT INTO `" . Config::tablenameUser() . "` (`id`, `user`, `pass`, `email`, `nom`, `prenom`, `adresse`, `ville`, `code_postal`) VALUES (NULL, '".$user."', '".$pass_crypt."', '".$email."', '".$nom."', '".$prenom."', '".$adresse."', '".$ville."', '".$code_postal."')");
		if (mysqli_errno($this->sqlConnect))
			throw new Exception("Echec requête SQL : ".mysqli_errno($this->sqlConnect)." : ".mysqli_error($this->sqlConnect));
		while (true) {
			$sqlResult = mysqli_query($this->sqlConnect, "SELECT * FROM `" . Config::tablenameUser() . "` WHERE `user` LIKE '".$user."'");
			if (mysqli_errno($this->sqlConnect))
				throw new Exception("Echec requête SQL : ".mysqli_errno($this->sqlConnect)." : ".mysqli_error($this->sqlConnect));
			if (mysqli_fetch_array($sqlResult) != NULL)
				return true;
			sleep(1);
			if (ini_get("max_execution_time") > time() - $exeTimeBegin - 2)
				return false;
		}
	}
}