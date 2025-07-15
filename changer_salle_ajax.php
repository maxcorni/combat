<?php

/*
  Controleur pour Ajax : Changer la salle du personnage connecté 
        Si on avance dans la salle suivante on consomme miniagi de la salle pour y aller
        Si on recul on consomme rien et on gagne miniagi en pv
  Paramètres :
        session::moi() : session du personnage connecté
        $_POST['direction'] : 'suivante' ou 'precedente' pour changer de salle
    Retour : un message d'erreur ou true si le changement de salle a réussi
*/

// Initialisations
include "library/init.php";

// Vérification de la session
if (!session::estConnecte()) {
    send_error("Accès interdit", 403);
}

// Récupération des paramètres
if (empty($_POST['direction']) || !in_array($_POST['direction'], ['suivante', 'precedente'])) {
    send_error("Direction invalide", 422);
}

// Chargement du personnage connecté
$moi = session::moi();

// Vérification que le personnage existe
if (!$moi) {
    send_error("Utilisateur non trouvé", 401);
}

// Changement de salle via la méthode objet
$result = $moi->changerSalle($_POST['direction']);

// Vérification du résultat de la méthode changerSalle
if ($result !== true) {
    send_error($result, 400);
}



