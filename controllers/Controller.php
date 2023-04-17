<?php
require_once "models/User.php";

class Controller extends AbstractController {
    private $user;

    public function __construct(){
        $this->user = new User;
    }

    /**
     * Affiche la page de profil de l'utilisateur connecté
     *
     * @return void
     */
    public function getProfilePage(): void{
        $pageTitle = "Profil";
        $pageDescription = "";

        if(!$this->user->isConnected()){
            $this->redirectTo(LOGIN_PAGE);
            return;
        };

        // Récupère les informations de l'utilisateur connecté
        $user = $this->user->getInfosOfUserConnected($_SESSION['user']['id']);

        // Si l'utilisateur existe, formate certaines informations et les stocke dans la variable $user
        if(!empty($user)){
            $user['pseudo'] = htmlspecialchars($user['pseudo']);
            $user['email'] = htmlspecialchars($user['email']);
            $user['avatar'] = htmlspecialchars($user['avatar']);
            $user['created_at'] = Tools::formatDateFR($user['created_at'],['day', 'month', 'year','time']);
        }

        $this->renderView("profile", [
            "pageTitle" => $pageTitle,
            "pageDescription" => $pageDescription,
            "user" => $user,
        ]);
    }

    /**
     * Affiche la page de connexion
     *
     * @return void
     */
    public function getLoginPage(): void{
        $pageTitle = "Connexion";
        $pageDescription = "Page de connexion";

        if($this->user->isConnected()){
            $this->redirectTo(PROFILE_PAGE);
            return;
        }

        $this->renderView("login", [
            "pageTitle" => $pageTitle,
            "pageDescription" => $pageDescription,
        ]);
    }

    /**
     * Affiche la page d'inscription
     *
     * @return void
     */
    public function getRegisterPage(): void{
        $pageTitle = "Inscription";
        $pageDescription = "Page d'inscription";

        if($this->user->isConnected()){
            $this->redirectTo(PROFILE_PAGE);
            return;
        }

        $this->renderView("register", [
            "pageTitle" => $pageTitle,
            "pageDescription" => $pageDescription,
        ]);
    }

    /**
     * Déconnecte l'utilisateur et redirige vers la page de profil
     *
     * @return void
     */
    public function logout(){
        $this->user->logout();
        $this->redirectTo(PROFILE_PAGE);
    }
}



