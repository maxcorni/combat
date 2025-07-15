<?php
// Role : Afficher les logs de combat
// Paramètres :
//     log : le tableau de logs à afficher
//     moi : l'objet personnage du joueur
//     cible : l'objet personnage de la cible



// Affichage des logs de combat
?>
<h2>Résultat du combat</h2>
<p>Vous avez attaqué <strong><?= htmlentities($cible->get('pseudo')) ?></strong> dans la salle <strong><?= htmlentities($moi->get('salle')->get('nom')) ?></strong>.</p>
<p>Voici le résultat de votre attaque :</p>
<ul>
<?php foreach ($log as $ligne): ?>
    <li><?= htmlentities($ligne) ?></li>
<?php endforeach; ?>
</ul>
<p>Fin du combat.</p>


