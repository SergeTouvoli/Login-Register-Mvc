<?php

abstract class AbstractController {
    protected $template;
    
    /**
     * 
     * Affiche un fichier de vue à partir du répertoire spécifié.
     * @param string $viewName Le nom du fichier de vue, sans l'extension '.phtml'.
     * @param array $variables Un tableau de variables à extraire et rendre disponibles pour la vue.
     * 
     * @return void
    */
    public function renderView(string $viewName, array $variables = []): void {
        extract($variables);
        require_once "views/$viewName.phtml";
    }

    /**
     * Redirige l'utilisateur vers une url spécifiée
     *
     * @param string $url L'url vers laquelle rediriger l'utilisateur
     *
     * @return void
     */
    public function redirectTo(string $url) {
        if(!empty(trim($url))) {
            header('Location: ' . URL . $url);
            exit;
        }
    }  
}
