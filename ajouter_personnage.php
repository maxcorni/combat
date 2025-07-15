<?php

/*
Controleur : Récupérer et enregistrer dans la base de données un nouveau personnage
Paramètres : 
    POST :  pseudo nom du personnage à créer
    POST :  password identifiants du personnage à créer
    POST :  force statistique du personnage
    POST :  agilité statistique du personnage
    POST :  résistance statistique du personnage
*/

// Initialisations
include "library/init.php";

// Chargement des paramètres principaux
$pseudo = isset($_POST["pseudo"]) ? $_POST["pseudo"] : "";
$password = isset($_POST["password"]) ? $_POST["password"] : "";
$force = isset($_POST["force"]) ? (int)$_POST["force"] : 0;
$agilite = isset($_POST["agilite"]) ? (int)$_POST["agilite"] : 0;
$resistance = isset($_POST["resistance"]) ? (int)$_POST["resistance"] : 0;

// Vérifications des contraintes
if (strlen($pseudo) < 3) {
    $message = "Le pseudo doit contenir au moins 3 caractères.";
    include "templates/pages/formulaire_connexion.php";
    exit;
}

if (strlen($password) < 5) {
    $message = "Le mot de passe doit contenir au moins 5 caractères.";
    include "templates/pages/formulaire_connexion.php";
    exit;
}

if (($force + $agilite + $resistance) != 15) {
    $message = "La somme de force, agilité et résistance doit être égale à 15.";
    include "templates/pages/formulaire_connexion.php";
    exit;
}

$personnage = new personnage();

// Vérification si le pseudo existe déjà
$existe = $personnage->find(['pseudo' => $pseudo]);
if ($existe) {
    $message = "Ce pseudo existe déjà. Veuillez en choisir un autre.";
    include "templates/pages/formulaire_connexion.php";
    exit;
}

// Création du nouvel personnage
$personnage->set('pseudo', $pseudo);
$personnage->set('password', password_hash($password, PASSWORD_DEFAULT)); // Hashage du mot de passe
$personnage->set('force', $force);
$personnage->set('agilite', $agilite);
$personnage->set('resistance', $resistance);
$personnage->set('pv', 100); // Par default, le personnage commence avec 100 points de vie
$personnage->set('visibilite', 0); // Par défaut, le personnage est visible
$personnage->set('salle', 1); // Par défaut, le personnage est dans la salle 1 

// On enregistre l'personnage
if ($personnage->insert()) {
    $message = "personnage créé avec succès.";
    include "templates/pages/formulaire_connexion.php";
} else {
    $message = "Erreur lors de la création du personnage";
    include "templates/pages/formulaire_connexion.php";
}
