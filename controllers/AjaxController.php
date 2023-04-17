<?php
require_once "models/User.php";
require_once "class/Tools.php";

class AjaxController extends AbstractController {

    private $user;

    public function __construct(){
        $this->user = new User;
    }

    /**
     * Traite le formulaire de connexion
     *
     * @return void
     */
    public function login() {
        if(!isset($_POST) || empty($_POST)){
            header('Location: ' . URL);
            exit();
        }

        $response = [
            'success' => false,
            'errors' => [],
        ];

        $email = Tools::sanitize($_POST['email']);
        $password = Tools::sanitize($_POST['password']);

        if(empty($email) || $email == ""){
            $response['errors'][] = array(
                'msg' => "L'adresse email est obligatoire",
                'param' => 'email'
            );
        }
        elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $response['errors'][] = array(
                'msg' => "L'adresse email n'est pas valide",
                'param' => 'email'
            );
        }
        elseif(empty($password) || $password == ""){
            $response['errors'][] = array(
                'msg' => "Le mot de passe est obligatoire",
                'param' => 'password'
            );
        }
          
        if(empty($response['errors'])){
            $user = $this->user->getUser($email);
            if(!empty($user) && password_verify($password, $user['password'])){
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'pseudo' => $user['pseudo'],
                    'email' => $user['email'],
                    'isConnected' => true,
                ];
                $response['success'] = true;
            } else {
                $response['errors'][] = array(
                    'msg' => "L'adresse email ou le mot de passe est incorrect",
                    'param' => 'password'
                );
            }  
        }

        echo json_encode($response);
    }


    /**
     * Traite le formulaire d'inscription d'un utilisateur et l'insère en base de données
     *
     * @return void
     */
    public function register() {
        if (!isset($_POST) || empty($_POST)) {
            header('Location: ' . URL);
            exit();
        }

        $response = [
            'success' => false,
            'errors' => [],
        ];

        $pseudo = Tools::sanitize($_POST['pseudo']);
        $email = Tools::sanitize($_POST['email']);
        $password = Tools::sanitize($_POST['password']);
        $passwordConfirm = Tools::sanitize($_POST['confirm_password']);

        $this->checkPseudo($response, $pseudo);
        $this->checkEmail($response, $email);
        $this->checkPassword($response, $password, $passwordConfirm);

        if (empty($response['errors'])) {
            if ($this->user->register($pseudo, $email, $password)) {
                $response['success'] = true;
            } else {
                $response['errors'][] = "Une erreur est survenue";
            }
        }

        echo json_encode($response);
    }

    /**
     * Vérifie que le pseudo est valide
     *
     * @param array $response
     * @param string $pseudo
     * @return void
     */
    private function checkPseudo(array &$response, string $pseudo)
    {
        if (empty($pseudo)) {
            $response['errors'][] = [
                'msg' => "Le pseudo est obligatoire",
                'param' => 'pseudo'
            ];
        } elseif (!preg_match('/^[a-zA-Z0-9]+$/', $pseudo)) {
            $response['errors'][] = [
                'msg' => "Le pseudo ne doit contenir que des lettres et des chiffres",
                'param' => 'pseudo'
            ];
        } elseif (strlen($pseudo) < 3 || strlen($pseudo) > 20) {
            $response['errors'][] = [
                'msg' => "Le pseudo doit faire entre 3 et 20 caractères",
                'param' => 'pseudo'
            ];
        } elseif ($this->user->pseudoExist($pseudo)) {
            $response['errors'][] = [
                'msg' => 'Le pseudo est déjà utilisé',
                'param' => 'pseudo'
            ];
        }
    }

    /**
     * Vérifie que l'email est valide
     *
     * @param array $response
     * @param string $email
     * @return void
     */
    private function checkEmail(array &$response, string $email)
    {
        if (empty($email)) {
            $response['errors'][] = [
                'msg' => "L'adresse email est obligatoire",
                'param' => 'email'
            ];
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['errors'][] = [
                'msg' => "L'adresse email n'est pas valide",
                'param' => 'email'
            ];
        } elseif ($this->user->emailExist($email)) {
            $response['errors'][] = [
                'msg' => "L'adresse email est déjà utilisée",
                'param' => 'email'
            ];
        }
    }

    /**
     * Vérifie que le mot de passe est valide
     *
     * @param array $response
     * @param string $password
     * @param string $passwordConfirm
     * @return void
     */
    private function checkPassword(array &$response, string $password, string $passwordConfirm)
    {
        if(empty($password) || $password == ""){
            $response['errors'][] = array(
                'msg' => "Le mot de passe est obligatoire",
                'param' => 'password'
            );
        }
        elseif(strlen($password) < 8 || strlen($password) > 20){
            $response['errors'][] = array(
                'msg' => "Le mot de passe doit faire entre 8 et 20 caractères",
                'param' => 'password'
            );
        }
        elseif($password != $passwordConfirm){
            $response['errors'][] = array(
                'msg' => "Les mots de passe ne correspondent pas",
                'param' => 'password'
            );
        }
    }
    
    /**
     * Gère la maj de l'avatar
     * 
     * @return void
     */
    public function uploadAvatar(){

        $response = [
            'success' => false,
            'errors' => [],
        ];

        $idUser = $_SESSION['user']['id'];
        if(isset($_FILES['avatar']) && !empty($_FILES['avatar']['name'])){
            $newAvatar = User::uploadAvatar($_FILES['avatar']);

            if($newAvatar['success'] == false){
                $response['errors'][] = [
                    'msg' => $newAvatar['error'],
                    'param' => "uploadError",
                ];
            }else{
                // On supprime l'ancien avatar
                $actualAvatar = $this->user->getAvatar($idUser);
                if($actualAvatar != "default.png"){
                    unlink(UPLOAD_FOLDER.$actualAvatar);
                }
                if($this->user->updateAvatar($newAvatar['filename'], $idUser)){
                    $response['success'] = true;
                }
            }
        }

        echo json_encode($response);
    }

}
