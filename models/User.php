<?php

require_once "class/DatabaseTools.php";
require_once "class/Tools.php";

class User extends DatabaseTools {

    private $dbTools;
    private $db;
    
    public function __construct(){
        $this->dbTools  = new DatabaseTools;
        $this->db = $this->dbTools->connexion;
    }

    /**
     * Récupère l'utilisateur correspondant à l'adresse email fournie
     *
     * @param string $email Adresse email de l'utilisateur à récupérer
     * @return array Tableau associatif représentant les informations de l'utilisateur, ou un tableau vide si l'utilisateur n'a pas été trouvé
     */
    public function getUser(string $email){
        $sql = "SELECT * FROM users WHERE email = :email";
        $sth = $this->db->prepare($sql);
        $sth->bindValue('email', $email, PDO::PARAM_STR);
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * Récupère le nom de fichier de l'avatar de l'utilisateur correspondant à l'identifiant fourni
     *
     * @param int $idUser Identifiant de l'utilisateur dont on souhaite récupérer l'avatar
     * @return string Nom de fichier de l'avatar de l'utilisateur, ou "default.png" si l'utilisateur n'a pas d'avatar
    */
    public function getAvatar(int $idUser) {
        $sql = "SELECT avatar FROM users WHERE id = :user_id";
        $params = array("user_id" =>  $idUser);
        $result = $this->dbTools->dbSelectOne($sql,$params);
    
        return $result['avatar'];
    }

    /**
     * Met à jour le nom de fichier de l'avatar de l'utilisateur correspondant à l'identifiant fourni
     *
     * @param string $newAvatar Nom de fichier du nouvel avatar
     * @param int $idUser Identifiant de l'utilisateur dont on souhaite mettre à jour l'avatar
     * @return bool Indique si la mise à jour de l'avatar a été effectuée avec succès
     */
    public function updateAvatar($newAvatar,int $idUser){
        $sql = "UPDATE users SET avatar = :newAvatar WHERE id = :idUser";
        $params = array("newAvatar" =>  $newAvatar, "idUser" => $idUser);
        $execution = $this->dbTools->updateInBdd($sql,$params);

        return $execution;
    }
    /**
     * Charge un fichier avatar téléchargé et vérifie s'il est valide. Si le fichier est valide, il est déplacé dans le dossier cible
     * et un nom de fichier unique est généré pour lui. Si le téléchargement est réussi, un tableau de réponse est renvoyé indiquant le nom de fichier généré.
     *
     * @param array $file Tableau contenant les informations du fichier avatar téléchargé.
     * @param string $folder (optionnel) Le dossier cible où le fichier doit être déplacé. Par défaut, le dossier UPLOAD_FOLDER est utilisé.
     * @param array $fileExtensions (optionnel) Tableau contenant les extensions de fichier autorisées. Par défaut, FILE_EXT_IMG est utilisé.
     * @return array Un tableau contenant les informations sur le téléchargement de fichier. 
     * Si le téléchargement est réussi, 'filename' contiendra le nom de fichier généré, 'success' sera vrai et 'error' sera une chaîne vide. 
     * Sinon, 'filename' sera une chaîne vide, 'success' sera faux et 'error' contiendra un message d'erreur.
     */
    public static function uploadAvatar(array $file,string $folder = UPLOAD_FOLDER , array $fileExtensions = FILE_EXT_IMG){
        $filename = '';
        $error = '';
        $response = array();
        // Vérifie s'il y a des erreurs lors du téléchargement du fichier
        if ($file["error"] === UPLOAD_ERR_OK) {
            // Vérifie si le fichier a un type MIME valide
            if(Tools::checkMIME($file["tmp_name"])){
                // Vérifie si l'extension de fichier est autorisée
                $tmpNameArray = explode(".", $file["name"]);
                $tmpExt = end($tmpNameArray);
                if(in_array($tmpExt,$fileExtensions)){
                    // Crée un nouveau nom de fichier unique
                    $filename = uniqid().'-'.basename($file["name"]);
                    // Déplace le fichier vers le dossier cible
                    if(!move_uploaded_file($file["tmp_name"],$folder.$filename)){
                        $error = 'Le fichier n\'a pas été enregistré correctement';
                    }
                }else {
                    $error = 'Ce type de fichier n\'est pas autorisé !';
                }
            }else {
                $error = 'Ce type de fichier n\'est pas autorisé !';
            }
        }else if($file["error"] == UPLOAD_ERR_INI_SIZE || $file["error"] == UPLOAD_ERR_FORM_SIZE) {
            $error = 'Le fichier est trop volumineux';
        }
        else {
            $error = 'Une erreur a eu lieu lors du téléchargement';
        }

        if($error == ''){ 
            $response = [
                'filename' => $filename,
                'success' => true,
                'error'  => ''
            ];
        } else{ 
            $response = [
                'filename' => "",
                'success' => false,
                'error'  => $error
            ];
        }

        return $response;
    }

    /**
     * Récupère les informations d'un utilisateur connecté à partir de son identifiant.
     *
     * @param int $id L'identifiant de l'utilisateur.
     * @return array Les informations de l'utilisateur récupérées depuis la base de données.
     */
    public function getInfosOfUserConnected(int $id){
        $sql = "SELECT * FROM users WHERE id = :id";
        $sth = $this->db->prepare($sql);
        $sth->bindValue('id', $_SESSION['user']['id'], PDO::PARAM_INT);
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * Vérifie si un pseudo existe déjà en base de données
     *
     * @param string $pseudo Le pseudo à vérifier
     * @return bool True si le pseudo existe déjà, false sinon
     */
    public function pseudoExist(string $pseudo){
        $sql = "SELECT pseudo FROM users WHERE pseudo = :pseudo";
        $sth = $this->db->prepare($sql);
        $sth->bindValue('pseudo', $pseudo, PDO::PARAM_STR);
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_ASSOC);

        return empty($result) ? false : true;
    }

    /**
     * Vérifie si une adresse email existe déjà en base de données
     *
     * @param string $email L'adresse email à vérifier
     * @return bool True si l'adresse email existe déjà, false sinon
     */
    public function emailExist(string $email){
        $sql = "SELECT email FROM users WHERE email = :email";
        $sth = $this->db->prepare($sql);
        $sth->bindValue('email', $email, PDO::PARAM_STR);
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_ASSOC);

        if(empty($result)){
            return false;
        } else {
            return true;
        }
    }

    /**
     * Enregistre un nouvel utilisateur en base de données
     *
     * @param string $pseudo Le pseudo de l'utilisateur
     * @param string $email L'adresse email de l'utilisateur
     * @param string $password Le mot de passe de l'utilisateur (en clair, sera hashé avant l'insertion en base)
     * @return bool True si l'enregistrement a réussi, false sinon
     */
    public function register(string $pseudo, string $email, string $password){
        $sql = "INSERT INTO users (pseudo, email, password) VALUES (:pseudo, :email, :password)";
        $sth = $this->db->prepare($sql);
        $sth->bindValue('pseudo', $pseudo, PDO::PARAM_STR);
        $sth->bindValue('email', $email, PDO::PARAM_STR);
        $sth->bindValue('password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
        return $sth->execute();
    }


    /**
     * Déconnecte l'utilisateur en supprimant sa session
     *
     * @return void
     */
    public function logout(){
        unset($_SESSION['user']);
    }
 
    /**
    * Vérifie si un utilisateur est connecté.
    *
    * @return bool Vrai si un utilisateur est connecté, faux sinon.
    */
    public function isConnected() {
        return isset($_SESSION['user']['isConnected']);
    }



    
}

