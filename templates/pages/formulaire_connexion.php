<?php
/*

Template de page : afficher le formulaire de connexion

Paramètres : néant


*/
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Connexion</title>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li>FightRoom</li>
                <li><a href="afficher_formulaire_creation.php" class="button">Créer un personnage</a></li>
            </ul>
        </nav>
    </header>
<main>
    <img src="assets/images/background.png" alt="Image de fond" class="background-image">
    <div class="colonne-right">
        <h1>Connexion</h1>
        <p><?= isset($message) ? $message : '' ?></p>
        <form method="POST" action="connexion.php" class="connexion-form">
            <label>Pseudo <input type="text" value="<?= isset($_POST["pseudo"]) ?  htmlentities($_POST["pseudo"]) : "" ?>" name="pseudo"></label><br>
            <label>Mot de passe <input type="password" name="password"></label><br>
            <input type="submit"  value="Se connecter" class="button"><br>
        </form>
    </div>
</main> 
</body>
</html>