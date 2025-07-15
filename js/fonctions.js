// Fonctions JavaScript pour le site de jeu de rôle
// Rafraîchit automatiquement les ennemis, le header et les actions toutes les 0.5s
setInterval(() => {
    refreshEnemies();
    refreshHeader();
    resheshSalleInfo();
}, 500);

function refreshEnemies() {
    refresh('recuperer_enemies_ajax.php','.enemies-list');
}

function refreshHeader() {
    refresh('rafraichir_header_ajax.php','header');
    console.log('Header refreshed');
}

function resheshActionButton() {
    refresh('rafraichir_bouton_action_ajax.php','.action');
}

function resheshSalleInfo() {
    refresh('rafraichir_salle_info_ajax.php','.salle-info');
}


function refresh(url, select) {
    // Rôle : rafraîchit une zone en récupérant le fragment HTML depuis le serveur
    // Paramètres : 
    //      url : URL à appeler pour récupérer le fragment HTML
    //      select : sélecteur CSS de l'élément à mettre à jour
    // Retour : néant
    // Utilise la fonction getFragmentAjax pour récupérer le fragment HTML des enemies
    getFragmentAjax(url, function(fragment) {
        const elem = document.querySelector(select);
            if (elem) {
                elem.innerHTML = fragment;
            }
        });
}



function sallePrecedente() {
    // Rôle : change la salle pour aller à la précédente
    // Paramètres : néant
    // Retour : met à jour l'affichage de la salle
    // Utilise fetch pour envoyer une requête POST à changer_salle_ajax.php
    fetch('changer_salle_ajax.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'direction=precedente'
    })
    .then(response => response.text())
    .then(fragment => {
        refreshEnemies();
        refreshHeader();
        resheshActionButton();
    });
}

function salleSuivante() {
    // Rôle : change la salle pour aller à la Suivante
    // Paramètres : néant
    // Retour : met à jour l'affichage de la salle
    // Utilise fetch pour envoyer une requête POST à changer_salle_ajax.php
    fetch('changer_salle_ajax.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'direction=suivante'
    })
    .then(response => response.text())
    .then(fragment => {
        refreshEnemies();
        refreshHeader();
        resheshActionButton();
        startAgiliteAutoGain();

    })
}

function addForce(val) {
    modifierStatAjax('force', val);
}

function addResistance(val) {
    modifierStatAjax('resistance', val);
}

function modifierStatAjax(stat, val) {
    // Rôle : envoie une requête AJAX pour modifier une statistique du personnage
    // Paramètres :
    //      stat : nom de la statistique à modifier (par exemple 'force', 'resistance')
    //      val : nouvelle valeur de la statistique
    // Retour : met à jour l'élément HTML correspondant avec le nouveau fragment reçu
    // Utilise fetch pour envoyer une requête POST à modifier_stats_ajax.php
    // et met à jour l'élément avec la classe 'stats' avec le fragment HTML reçu

    const statsElem = document.querySelector('.stats');
    fetch('modifier_stats_ajax.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `stat=${encodeURIComponent(stat)}&val=${encodeURIComponent(val)}`
    })
    .then(response => response.text())
    .then(fragment => {
        // Ici, on suppose que si fragment n'est pas vide, la modification a réussi
        if (fragment && statsElem) {
            statsElem.innerHTML = fragment;
            statsElem.style.pointerEvents = 'none';
            statsElem.style.opacity = '0.6';
            setTimeout(() => {
                statsElem.style.pointerEvents = '';
                statsElem.style.opacity = '';
            }, 60000);
        }

        refreshHeader();
    });
}

let agiInterval = null;
let isAttacking = false; // À mettre à true si le joueur attaque

function startAgiliteAutoGain() {
    // Rôle : démarre l'intervalle d'ajout d'agilité toutes les 3 secondes
    // Vérifie la visibilité du personnage avant de commencer
    // Si l'intervalle est déjà en cours, ne rien faire
    if (agiInterval) return; // déjà lancé
    agiInterval = setInterval(() => {
        // On vérifie la visibilité avant chaque ajout
        const visibilite = window.currentVisibilite || 0;
        if (visibilite === 0 && !isAttacking) {
            // Vérifie la valeur d'agilité avant d'ajouter
            const agiliteElem = document.querySelector('header [data-stat="agilite"]');                 
            let agilite = 0;
            if (agiliteElem) {
                agilite = parseInt(agiliteElem.textContent, 10) || 0;
            }
            if (agilite >= 15) {
                stopAgiliteAutoGain();
                return;
            }
            fetch('ajouter_agilite_ajax.php')
                .then(response => response.text())
                .then(fragment => {
                    // Met à jour la zone stats si besoin
                    const statsElem = document.querySelector('.header');
                    if (statsElem && fragment) {
                        statsElem.innerHTML = fragment;
                        // Vérifie à nouveau après mise à jour
                        const newAgiElem = statsElem.querySelector('[data-stat="agilite"]');
                        let newAgi = 0;
                        if (newAgiElem) {
                            newAgi = parseInt(newAgiElem.textContent, 10) || 0;
                        }
                        if (newAgi >= 15) {
                            stopAgiliteAutoGain();
                        }
                    }
                });
        }
    }, 3000);
}

function stopAgiliteAutoGain() {
    // Rôle : arrête l'intervalle d'ajout d'agilité
    if (agiInterval) {
        clearInterval(agiInterval);
        agiInterval = null;
    }
}

// À appeler quand la visibilité change
function onVisibiliteChange(val) {
    // Rôle : gère le changement de visibilité du personnage
    // Paramètres : val : nouvelle valeur de la visibilité (0 ou 1)
    // Met à jour la visibilité courante
    // Si la visibilité est 0, on démarre l'auto-gain d'agilité, sinon on l'arrête
    window.currentVisibilite = parseInt(val, 10);
    if (window.currentVisibilite === 0 && !isAttacking) {
        startAgiliteAutoGain();
        // Si on redevient visible, on désactive le bouton 15s
        const btn = document.getElementById('btn-visibilite');

        btn.disabled = true;
        btn.style.pointerEvents = 'none';
        btn.style.opacity = '0.6';
        setTimeout(() => {
            btn.disabled = false;
            btn.style.pointerEvents = '';
            btn.style.opacity = '';
        }, 15000);

    } else {
        stopAgiliteAutoGain();
    }
}

// Modifie la fonction changeVisibility pour appeler onVisibiliteChange
function changeVisibility(val) {
    // Rôle : recupere la nouvelle valeur de la visibilite du personnage
    // Si la valeur 1 (devient invisible), si elle est 0 
    // Paramètres : val : valeur de la visibilité (1 ou 0)

    fetch('modifier_visibilite_ajax.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `val=${encodeURIComponent(val)}`
    })
    .then(response => response.text())
    .then(() => {
        // Rafraîchit les bouton et le header
        resheshActionButton();
        refreshHeader();
        // Met à jour la logique d'agilité auto
        onVisibiliteChange(val);
    });
}

// Appelle onVisibiliteChange au chargement de la page avec la valeur courante
document.addEventListener('DOMContentLoaded', function() {
    window.currentVisibilite = parseInt(document.body.getAttribute('data-visibilite') || '0', 10);
    onVisibiliteChange(window.currentVisibilite);
    refreshEnemies();
});


function attaquer(idCible) {
    isAttacking = true;
    // Désactive les boutons d'attaque pendant le combat
    document.querySelectorAll('.enemies-list button').forEach(btn => btn.disabled = true);

    fetch('attaquer_ajax.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'idCible=' + encodeURIComponent(idCible)
    })
    .then(response => response.text())
    .then(fragment => {
        // Affiche le résultat du combat
        const resultDiv = document.querySelector('.combat-result');
        if (resultDiv) {
            resultDiv.innerHTML = fragment;
        } else {
            alert(fragment);
        }
        // Rafraîchir les stats et ennemis
        refreshEnemies();
        refreshHeader();
    })
    .finally(() => {
        isAttacking = false;
        // Réactive les boutons après le combat
        document.querySelectorAll('.enemies-list button').forEach(btn => btn.disabled = false);
    });
}

// Exemple de librairie Ajax simplifiée
function getFragmentAjax(url, callBack) {
    // Role : demander une URL en GET, puis donner le résultat de type HTML à une fonction d'affichage
    // Paramètres :
    //      url : URL à appeler
    //      callBack : fonction à appeler pour traiter le retour de l'url
    fetch(url).then(function(response) {
        return response.text(); // attend du HTML (texte brut)
    }).then(callBack);

}



