<?php
/* 
Template de fragment: fragment header contenant les informations du personnage
Parametres : 
    $moi : utilisateur connecté
*/
?>
<nav>
    <ul>
        <li><b>Pseudo</b> : <?= htmlentities($moi->get("pseudo")) ?></li>
        <li><b>Pv: </b> : <?= htmlentities($moi->get("pv")) ?> / 100</li>
        <li><b>Force: </b> : <?= htmlentities($moi->get("force")) ?></li>
        <li><b>Résistance: </b> : <?= htmlentities($moi->get("resistance")) ?></li>
        <li><b>Agilité :</b> <span data-stat="agilite"><?= htmlentities($moi->get('agilite')) ?></span></li>            
        <li><b>Visibilité: </b> : <?= $moi->get("visibilite") == 0 ? "visible" : "invisible" ?></li>
        <li><a href="deconnecter.php" class="button">Déconnexion</a></li>
    </ul>
</nav>
