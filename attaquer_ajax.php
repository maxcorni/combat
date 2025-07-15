<?php

/*
Controleur : Recuperer les info de 2 personnages et simuler un combat entre eux
//   Paramètres :
    idCible : l'id du personnage cible
    session : la session de l'attaquant (le personnage qui attaque)

Retour : un fragment HTML (UL) contenant le résultat du combat

*/

// Initialisations
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
// Récupération de l'ID de la cible depuis les paramètres POST
$idCible = isset($_POST['idCible']) ? intval($_POST['idCible']) : 0;
if (!$idCible || $idCible == $moi->id()) {
    send_error("Cible invalide.", 400);
}

// Chargement de la cible
$cible = new personnage($idCible);
if (!$cible->is() || $cible->get('salle') != $moi->get('salle')) {
    send_error("Cible non trouvée ou pas dans la même salle.", 404);
}

// Vérification de la visibilité
// Si la cible est invisible (visibilite == 1), on ne peut pas l'attaquer et que le personnage attaquant est aussi invisible
if ($cible->get('visibilite') == 1 || $moi->get('visibilite') == 1) {
    send_error("Vous ne pouvez pas attaquer en etant invisible ou un personnage invisible.", 403);
}

// Combat via la méthode objet
$log = $moi->attaquerPersonnage($cible);

// Vérification si le combat a généré des logs
if (!isset($log) || !is_array($log)) {
    $log = [];
    $log[] = "Aucun combat n'a eu lieu.";
}

include "templates/fragments/logs.php";



