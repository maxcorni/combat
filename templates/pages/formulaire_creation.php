<?php
/*

Template de page : afficher le formulaire de creation de personnage

Paramètres : néant


*/
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Creation</title>
</head>
<body>
<header>
    <nav>
        <ul>
            <li>FightRoom</li>
            <li><a href="index.php" class="button">Se connecter</a></li>
        </ul>
    </nav>
</header>
<main>
    <img src="assets/images/background.png" alt="Image de fond" class="background-image">
    <div class="colonne-right">
        <h1>Nouveau Personnage</h1>
        <p><?= isset($message) ? $message : '' ?></p>
        <form method="POST" action="ajouter_personnage.php">
            <label>Pseudo <input type="text" name="pseudo" id="pseudo"></label><br>
            <label>Mot de passe <input type="password" name="password" id="password"></label><br>
            <fieldset>
                <legend>Caractéristiques du personnage</legend>
                <label>Points de vie (PV) = 100PV</label><br>
                <label>Force <input type="number" name="force" id="force" min="0" max="15" value="5" oninput="verifierSommeStats()"></label><br>
                <label>Agilité <input type="number" name="agilite" id="agilite" min="0" max="15" value="5" oninput="verifierSommeStats()"></label><br>
                <label>Résistance <input type="number" name="resistance" id="resistance" min="0" max="15" value="5" oninput="verifierSommeStats()"></label><br>
            </fieldset>
            <div>
                <span id="stats-message"></span>

            </div>
            <input type="submit"  value="Créer le personnage" class="button"><br>
        </form>
    </div>
</main> 
<script src="js/fonctions_creation_personnage.js" type="text/javascript" ></script>
</body>
</html>