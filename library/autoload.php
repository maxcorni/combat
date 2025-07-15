<?php
// Library d'autoloading des classes

spl_autoload_register(function ($className) {
    // Rôle : fonction d'autoloading des classes
    // Paramètres : $className : nom de la classe à charger
    // Retour : néant, mais charge la classe si elle existe dans le répertoire "modele"
    
    $file = "modele/$className.php";
    
    if (file_exists($file)) {
        require_once $file;
    }
});