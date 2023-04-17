<?php 

// Définition de l'URL de base du site
define("URL", str_replace("index.php","",(isset($_SERVER['HTTPS']) ? "https" : "http")."://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]"));

const UPLOAD_FOLDER = "public/uploads/";
const FILE_EXT_IMG = ['jpg','jpeg','gif','png','svg','JPG','JPEG','GIF','PNG','SVG'];

// Constante pour la connexion à la bdd 
const DB_HOST= 'localhost'; 
const DB_NAME = 'login-register'; 
const DB_USER = 'root'; 
const DB_PASS = '';

// Définition des routes
const LOGIN_PAGE = "connexion";
const REGISTER_PAGE = "inscription";
const REGISTER_ROUTE = "register";
const UPLOAD_AVATAR_ROUTE = "uploadAvatar";
const PROFILE_PAGE = "profil";
const LOGIN_ROUTE = "login";
const AJAX = "ajax";
const LOGOUT = "deconnexion";

?>