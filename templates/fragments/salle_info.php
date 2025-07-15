<?php
/*
Template de fragment : Info de la salle
Paramètres : 
    $moi : objet personnage, le personnage connecté        
*/
?>

<h2>Vous êtes dans la salle  <?= htmlentities($moi->get("salle")->get('nom')) ?></h2>
<h2>Agilité requise pour accéder à la salle suivante: <?= htmlentities($moi->get("salle")->get('miniagi'))?></h2>
<h2>Agilité possédé: <?= htmlentities($moi->get("agilite")) ?></h2>

