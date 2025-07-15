<?php
/*

Controleur : Préparer l'interface de connexion ou afficher le jeu si déjà connecté

Paramètres : néant

*/
// Initialisations (toutes)
include "library/init.php";

if ( ! session::estConnecte()) {
    //  - afficher templates/pages/formulaire_connexion (pas de paramètres)
    //  - fin du controleur (exit)
    include "templates/pages/formulaire_connexion.php";
    exit;
}

// Si on est ici, c'est que l'utilisateur est connecté
include "templates/pages/ecran_jeu.php";
