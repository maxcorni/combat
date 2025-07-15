<?php

/*
    Controleur pour Ajax : Ajoute 1 agilité au personnage connecté jusqu'à 15
    Paramètres :
                session::moi() : session du personnage connecté

    Retour : un fragment HTML (UL)
*/

include "library/init.php";

// Vérification de la session
if (!session::estConnecte()) {
    send_error("Accès interdit", 403);
}

// Chargement du personnage connecté
$moi = session::moi();

// Vérification que le personnage existe
if (!$moi) {
    send_error("Utilisateur non trouvé", 401);
}

// Ajout de l'agilité automatique
$result = $moi->ajouterAgiliteAuto();

// Vérification du résultat de l'ajout d'agilité
if ($result !== true) {
    send_error($result, 400);
}

// Recharge le fragment stats (ou ce que tu veux)
include "templates/fragments/header.php";
