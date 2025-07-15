<?php
/*
    Controleur : déconnecter l'utilisateur connecté
    Paramètres : néant
*/


// Initialisations (toutes)
include "library/init.php";

// Déconnecter l'utilisateur
session::deconnecter();

// Affiche le formulaire de connexion (pas de paramètres)
include "templates/pages/formulaire_connexion.php";