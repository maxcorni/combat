<?php

// Initialisations communes à tous les controleurs 
// (à inclure en début de chaque controleur)


// mettre en place les messages d'erreur (pour la mise au point)
ini_set('display_errors',1);
error_reporting(E_ALL);

// Initialiser / récupérer les infos de session
session_start();    // gère le cookie, récupère $_SESSION avec sa dernière valeur connue

// Charger les librairies
include "library/bdd.php";
include "library/autoload.php";

// Fonction utilitaire pour envoyer une erreur JSON et arrêter le script
function send_error($message, $code = 400) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode(['error :' => $message]);
    exit;
}

// Charger les paramètres de connexion à la base de données depuis un fichier séparé
require_once  "config/database.php";
$bdd = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=UTF8", $DB_USER, $DB_PASS);
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING) ;  // En mise au point seulement