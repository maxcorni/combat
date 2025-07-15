<?php
/*
Template de fragment : Chaque appel de ce fragment génère une ligne dans le tableau des ennemis en contenant 1.
Paramètres : 
    $enemie : objet personnage, contenant les informations d'un ennemi à afficher
*/
?>

<tr>
    <td><?= htmlentities($enemie->get('pseudo')) ?></td>
    <td><button onclick="attaquer(<?= $enemie->id() ?>)" class="button">Combat</button></td>
</tr>