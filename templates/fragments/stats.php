<?php
/*
Template de fragment : Liste des bouton pour modifier les stats du personnage
Paramètres : 
    $moi : utilisateur connecté

*/
?>

<h2 class="stats-title">Modifier Statistique</h2>
<p class="stats-info">1 stats coute 3 agi</p>
<p class="stats-info">2 stats coute 5 agi</p>
<p class="stats-info">3 stats coute 10 pv</p>
<ul class="stats-list">
    <li class="stats-label">Force : <span class="stats-value"><?= htmlentities($moi->get("force")) ?></span></li>
    <li><button class="stats-btn" onclick="addForce(1)">1</button></li>
    <li><button class="stats-btn" onclick="addForce(2)">2</button></li>
    <li><button class="stats-btn" onclick="addForce(3)">3</button></li>
</ul>
<ul class="stats-list">
    <li class="stats-label">Résistance : <span class="stats-value"><?= htmlentities($moi->get('resistance')) ?></span></li>
    <li><button class="stats-btn" onclick="addResistance(1)">1</button></li>
    <li><button class="stats-btn" onclick="addResistance(2)">2</button></li>
    <li><button class="stats-btn" onclick="addResistance(3)">3</button></li>
</ul>
