<?php

/*
  Controleur pour Ajax : Récupérer et afficher les stats du personnage connecté
  Paramètres :
        Néant (utilise la session du personnage connecté)

  Retour : un fragment HTML (UL)
*/

include "library/init.php";

// Vérification de la session
if (!session::estConnecte()) {
    send_error("Accès interdit", 403);
}

$moi = session::moi();
// Vérification que le personnage existe
if (!$moi) {
    send_error("Utilisateur non trouvé", 401);
}

include "templates/fragments/header.php";

