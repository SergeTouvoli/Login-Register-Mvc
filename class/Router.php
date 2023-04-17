<?php
require_once "config/config.php";
require_once "controllers/AbstractController.php";
require_once "controllers/AjaxController.php";
require_once "controllers/Controller.php";

class Router {
    
    private $ajaxController;
    private $controller;

    public function __construct() {
        $this->ajaxController = new AjaxController();
        $this->controller = new Controller();
    }

    public function run(){
        try {
            if (isset($_GET['page']) && !empty($_GET['page'])) {
                // Explose l'URL en un tableau à partir du caractère '/'
                $url = explode("/", filter_input(INPUT_GET, 'page', FILTER_SANITIZE_URL));

                // Récupère la première partie de l'URL (la page demandée)
                $page = $url[0];

                switch ($page) {
                    case PROFILE_PAGE:
                        $this->controller->getProfilePage();
                        break;
                    case LOGIN_PAGE:
                        $this->controller->getLoginPage();
                        break;
                    case REGISTER_PAGE:
                        $this->controller->getRegisterPage();
                        break;
                    case LOGOUT:
                        $this->controller->logout();
                        break;
                    case AJAX:
                        if(isset($url[1]) && !empty($url[1])){
                            switch ($url[1]) {
                                case LOGIN_ROUTE:
                                    $this->ajaxController->login();
                                    break;
                                case REGISTER_ROUTE:
                                    $this->ajaxController->register();
                                    break;
                                case UPLOAD_AVATAR_ROUTE:
                                    $this->ajaxController->uploadAvatar();
                                    break;
                            }
                        } else {
                            throw new Exception("La page n'existe pas");
                        }
                        break;
                    default: throw new Exception("La page n'existe pas");
                }
            } else {
                $this->controller->getLoginPage();
            }
        } catch (Exception $e) {
            $pageTitle = "Erreur : ".http_response_code();
            $pageDescription = "Page de gestion des erreurs";
            $page = $page;
            $errorMessage = $e->getMessage();
            require "views/errorPage.phtml";
        }
    }
}