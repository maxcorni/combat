<?php
/*
Template de fragment : les boutons d'action du jeu
Paramètres : 
    $moi : objet personnage, le personnage connecté        
*/
?>
<button onclick="sallePrecedente()" class="button">Salle précédente</button>
<button onclick="changeVisibility(<?= (int)!$moi->get('visibilite') ?>)" class="button" id="btn-visibilite">
    <?= $moi->get('visibilite') ? "Redevenir visible" : "Devenir invisible" ?>
</button>        
<button onclick="salleSuivante()" class="button">Salle suivante (Agi nécessaire: <?= htmlentities($moi->get("salle")->get('miniagi')) ?>)</button>

