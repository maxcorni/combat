<?php

/*
  Controleur : Ajoute 20 personnages bots avec des stats aléatoires
  Paramètres :
    - Aucun, le nombre de bots est fixé à 20

*/

include "library/init.php";

// Nombre de personnages à créer
$nb = 100;

for ($i = 1; $i <= $nb; $i++) {
    // Générer un pseudo unique
    $pseudo = "Bot" . uniqid() . $i;

    // Générer un mot de passe aléatoire (hashé)
    $password = "pass" . rand(1000, 9999);
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Générer des stats valides (force + agilite + resistance = 15)
    $stats = [0, 0, 0];
    // Répartir 15 points aléatoirement
    for ($j = 0; $j < 15; $j++) {
        $stats[rand(0, 2)]++;
    }
    list($force, $agilite, $resistance) = $stats;

    // Vérifier unicité du pseudo
    $p = new personnage();
    if ($p->find(['pseudo' => $pseudo])) {
        // Collision très improbable, recommencer cette itération
        $i--;
        continue;
    }

    // Créer le personnage
    $p->set('pseudo', $pseudo);
    $p->set('password', $password_hash);
    $p->set('force', $force);
    $p->set('agilite', $agilite);
    $p->set('resistance', $resistance);
    $p->set('pv', 100);
    $p->set('visibilite', 0);
    $p->set('salle', rand(2, 10));

    if ($p->insert()) {
        echo "Créé : $pseudo | pass: $password | F:$force A:$agilite R:$resistance<br>";
    } else {
        echo "Erreur création $pseudo<br>";
    }
}

