// Fonctions JavaScript pour le site de jeu de rôle
function verifierSommeStats() {
    const force = parseInt(document.getElementById('force').value) || 0;
    const agilite = parseInt(document.getElementById('agilite').value) || 0;
    const resistance = parseInt(document.getElementById('resistance').value) || 0;
    const somme = force + agilite + resistance;
    const message = document.getElementById('stats-message');
    const submitBtn = document.querySelector('form button[type="submit"], form input[type="submit"]');
    let valid = true;
    let messages = [];

    if (somme !== 15) {
        messages.push("La somme des statistiques doit être exactement 15 (actuellement " + somme + ")");
        valid = false;
    }

    // Vérification du pseudo (> 3 caractères)
    const pseudoInput = document.getElementById('pseudo');
    if (pseudoInput && pseudoInput.value.length < 3) {
        messages.push("Le pseudo doit contenir au moins 3 caractères");
        valid = false;
    }

    // Vérification du mot de passe (> 5 caractères)
    const passwordInput = document.getElementById('password');
    if (passwordInput && passwordInput.value.length < 5) {
        messages.push("Le mot de passe doit contenir au moins 5 caractères");
        valid = false;
    }

    message.textContent = messages.join(' | ');
    if (submitBtn) submitBtn.disabled = !valid;
}

// Empêche la soumission du formulaire si la somme n'est pas valide ou si pseudo/password invalides
document.addEventListener('DOMContentLoaded', function() {
    verifierSommeStats();
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const force = parseInt(document.getElementById('force').value) || 0;
            const agilite = parseInt(document.getElementById('agilite').value) || 0;
            const resistance = parseInt(document.getElementById('resistance').value) || 0;
            const pseudoInput = document.getElementById('pseudo');
            const passwordInput = document.getElementById('password');
            let valid = true;

            if (force + agilite + resistance !== 15) valid = false;
            if (pseudoInput && pseudoInput.value.length <= 3) valid = false;
            if (passwordInput && passwordInput.value.length < 5) valid = false;

            if (!valid) {
                e.preventDefault();
                verifierSommeStats();
            }
        });
        // Met à jour le bouton submit à chaque changement
        ['force', 'agilite', 'resistance', 'pseudo', 'password'].forEach(id => {
            const input = document.getElementById(id);
            if (input) input.addEventListener('input', verifierSommeStats);
        });
    }
});



