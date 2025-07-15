<?php

/*
  Controleur pour Ajax : Récupérer la liste des enemies du personnage connecté
  Paramètres :
        Néant (utilise la session du personnage connecté)

  Retour : un fragment HTML (UL)
*/

include "library/init.php";

// Vérification de la session
if (!session::estConnecte()) {
    send_error("Accès interdit", 403);
}

// Chargement des contacts du personnage connecté
$enemies = session::moi()->getPersonnagesDansSalleHostile(
    session::moi()->get('salle')->id(),
    session::moi()->id()
);

// Vérification que des ennemis existent
if (empty($enemies)) {
    send_error("Aucun ennemi trouve dans la salle actuelle", 404);
}

// Affichage du fragment HTML pour les ennemis
foreach ($enemies as $enemie) {
    // Pour chaque ennemi, on va afficher une ligne    
    include "templates/fragments/enemies.php";
}

