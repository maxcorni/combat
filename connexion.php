<?php
/*

Controleur : Connecter un personnage à partir de ses identifiants (vérifier que les identifiants sont corrects)

Paramètres : 
        POST : pseudo, password  (pseudo et mot de passe de connexion)


*/


// Initialisations (toutes)
include "library/init.php";

if (session::estConnecte()) {
    //  - afficher templates/pages/ecran_jeu.php
    //  - fin du controleur (exit)
    include "templates/pages/ecran_jeu.php";
    exit;
}

// Chargement des paramètres principaux
// On récupère pseudo et password dans $pseudo et $password
if (isset($_POST["pseudo"])) {
    $pseudo = $_POST["pseudo"];
} else {
    $pseudo = "";
}
if (isset($_POST["password"])) {
    $password = $_POST["password"];
} else {
    $password = "";
}

// Vérifier si les codes de connexion correspondent à un personnage
$personnage = session::verifLogin($pseudo, $password);
  
// Si non : on revient au formulaire (on affiche le formulaire et on a fini)
if ($personnage === false) {
    include "templates/pages/formulaire_connexion.php";
    exit;
}

// Si oui : on le connecte   
session::connecter($personnage->id());

// Affichage final : page ecran_jeu (pas de paramètres)
include "templates/pages/ecran_jeu.php";