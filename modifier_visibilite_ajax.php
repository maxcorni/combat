<?php

/*
    Controleur pour Ajax : Récupérer et modifier la visibilite du personnage connecté
    Paramètres :
        Néant (utilise la session du personnage connecté)
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

// Récupération de la visibilité actuelle 0 ou 1
$visibilite = $moi->get('visibilite');

// Inversion de la visibilité
if ($visibilite === 1) {
    $nouvelleVisibilite = 0; // De visible à invisible
} else {
    $nouvelleVisibilite = 1; // De invisible à visible
}
// Mise à jour de la visibilité dans la session
if (!$moi->set('visibilite', $nouvelleVisibilite)) {
    send_error("Erreur lors de la mise à jour de la session", 500);
}

// Enregistrement de la nouvelle visibilité dans la base de données
if (!$moi->update()) {
    send_error("Erreur lors de la mise à jour de la base de données", 500);
}
