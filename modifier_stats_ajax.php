<?php
/*
  Controleur pour Ajax : Modifier les stats du personnage connecté
  Paramètres :
        - stat : la stat à modifier (force ou resistance)
        - val : la valeur à ajouter (1, 2 ou 3)
  
  Retour : un fragment HTML pour les stats
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

// Récupération des paramètres
if (empty($_POST['stat']) || empty($_POST['val'])) {
    send_error("Paramètres manquants", 422);
}

// Récupération de la stat et de la valeur
$stat = $_POST['stat'];
if (!in_array($stat, ['force', 'resistance'])) {
    send_error("Stat non reconnue", 422);
}

// Récupération de la valeur, avec une valeur par défaut de 0 si non définie
$val = intval($_POST['val']);
if ($val < 1 || $val > 3) {
    send_error("Modification non autorisée", 422);
}

// Ajout de la stat
if (!$moi->ajouterStat($stat, $val)) {
    send_error("Modification impossible (ressources insuffisantes ou stat invalide)", 400);
}

// Affichage du fragment HTML pour les stats
include "templates/fragments/stats.php";
