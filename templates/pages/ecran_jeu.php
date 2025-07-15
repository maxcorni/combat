<?php
/*

Template de page : afficher l'ecran du jeu de role
paramètres :
- Neant

// ON va devoir travailer sur l'utilisateur connecté


*/

$moi = session::moi();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Jeu de rôle en ligne</title>
</head>
<body data-visibilite="<?= (int)$moi->get('visibilite') ?>">
<header>
<?php
    include_once "templates/fragments/header.php";
?> 
</header>

<main> 
    <section class="container">
        <h1>Jeu de rôle en ligne</h1>
        <section class="salle-info" aria-label="Informations de la salle"></section>
        <div class="action" aria-label="Actions principales">
            <?php
            include_once "templates/fragments/bouton_action.php";
            ?>
        </div>

        <div class="row">
            <section class="col">
                <section class="enemies" aria-labelledby="enemies-title">
                    <h2 id="enemies-title">Liste des ennemis</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Pseudo</th>
                                <th>Bouton combat</th>
                            </tr>
                        </thead>
                        <tbody class="enemies-list">
                            <!-- Les lignes <tr> des ennemis seront insérées ici par AJAX -->
                        </tbody>
                    </table>
                </section>
            </section>
            <section class="col">
                <section class="stats" aria-label="Statistiques du joueur">
                    <?php
                    include_once "templates/fragments/stats.php";
                    ?>
                </section>
                <section class="combat-result" aria-labelledby="combat-result-title">
                    <h2 id="combat-result-title">Résultat de combat</h2>
                </section>
            </section>
        </div>
    </section>
</main>
<script src="js/fonctions.js"></script>
</body>
</html>
