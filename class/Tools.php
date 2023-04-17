<?php

class Tools  {

    /**
     * Affiche une variable de manière lisible et arrête l'exécution du script
     *
     * @param mixed $data Les données à afficher
     *
     * @return void
     */
    public static function debug(mixed $data) {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        exit;
    }

    public static function formatDateFR($date, $options = array('month', 'year', 'day')) {
        setlocale(LC_TIME, 'fr_FR.UTF8', 'fr.UTF8', 'fr_FR.UTF-8', 'fr.UTF-8');
        $timestamp = strtotime($date);
        $format = '';
        if (in_array('day', $options)) {
            $format .= '%A %e ';
        }
        if (in_array('month', $options)) {
            $format .= '%B ';
        }
        if (in_array('year', $options)) {
            $format .= '%Y ';
        }
        if (in_array('time', $options)) {
            $format .= 'à %H:%M:%S';
        }
        return strftime($format, $timestamp);
    }

    /**
    * Filtre une chaîne de caractères pour éviter les injections XSS
    *
    * @param string $data La chaîne à filtrer
    * @return string La chaîne filtrée
    */
    public static function sanitize($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    

    
    /**
     * Cette fonction vérifie si le type MIME d'un fichier est autorisé
     *
     * @param string $file Le chemin vers le fichier à vérifier
     *
     * @return bool true si le type MIME est autorisé, false sinon
    */
    public static function checkMIME($file) {
        $allowedTypes = array("image/jpeg", "image/png", "image/gif","image/svg+xml");
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file);
        finfo_close($finfo);
        if (in_array($mime, $allowedTypes)) {
            return true;
        } else {
            return false;
        }
    }



}