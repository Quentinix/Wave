<?php
// phpcs:disable Generic.Files.LineLength

namespace Wave;

use Exception;

/**
 * Class de la gestion d'utilisateurs
 *
 * @package  Wave
 * @author   Quentinix <git@quentinix.fr>
 */
class Wave extends Config
{
    private $sqlConnect;

    /**
     * Connexion à la base de données lors de l'appel de la class
     */
    public function __construct()
    {
        // if (@file_exists(".configOK") == false and @file_exists("vendor/quentinix/wave/.configOK") == false) {
        //     throw new Exception("La configuration de Wave n'est pas appliquée.");
        // } Solution reportée !
        $this->sqlConnect = mysqli_connect($this->getConfigSqlHost(), $this->getConfigSqlUser(), $this->getConfigSqlPass(), $this->getConfigSqlDb());
        if (mysqli_errno($this->sqlConnect)) {
            throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
        }
    }

    /**
     * Déconnexion de la base de données
     */
    public function __destruct()
    {
        mysqli_close($this->sqlConnect);
    }

    /**
     * Retourne la version de la bibliothèque
     *
     * @return string
     */
    public function version()
    {
        return "1.3.0-dev";
    }

    /**
     * Permet la création d'un mot de passe facile à retenir
     *
     * @return string
     */
    public function createMdp()
    {
        $lettreConsonne = array(
            "b", "c", "d", "f", "g", "h", "j", "k", "l", "m", "n", "p", "q", "r", "s", "t", "v", "w", "x", "z"
        );
        $lettreVoyelle = array(
            "a", "e", "i", "o", "u", "y"
        );
        $lettreSpecial = array(
            "&", "(", "-", "_", ")", "=", ",", ";", ":", "!", "$", "*"
        );
        $i = 0;
        for ($i = 0; $i < 4; $i++) {
            if ($i == 0) {
                $rand = array_rand($lettreConsonne);
                $return = strtoupper($lettreConsonne[$rand]);
                $rand = array_rand($lettreVoyelle);
                $return .= $lettreVoyelle[$rand];
            }
            if ($i == 1 || $i == 2) {
                $rand = array_rand($lettreConsonne);
                $return .= $lettreConsonne[$rand];
                $rand = array_rand($lettreVoyelle);
                $return .= $lettreVoyelle[$rand];
            }
            if ($i == 3) {
                $rand = array_rand($lettreSpecial);
                $return .= $lettreSpecial[$rand];
                $return .= rand(0, 9);
            }
            $i++;
        }
        return $return;
    }
}
