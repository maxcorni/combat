<?php

// class static : fonctions de gestion de la session


/*
Fonctions de gestion de la session :
    gestion de la durée de session (30 minutes)
    connecter un personnage : connecter(id)
    deconnecter le personnage connecté : deconnecter()
    savoir si un personnage est connecté : estConnecte()
    vérifier les codes de connexion : verifLogin($login, $password)  : retourne objet personnage (celui qui correspond) ou false
    recupérer le personnage connecté : moi()


*/


/*
Comment on va gérer les infos de connexion dans cette class static
$_SESSION["id"] sera vide (ou 0) si personne est connecté, contiendra le id du personnage connecté si un personnage est connecté

*/
class session {
    // Classe pour gérer la session, mais on utilise directement les fonctions pour simplifier
    // On pourrait ajouter des méthodes ici si besoin

    const DUREE_SESSION = 1800; // 30 minutes


    static function connecter($id) {
        // Rôle : déclarer qu'un personnage est connecté
        // Paramètres : 
        //      $id : id de le personnage à déclarer connecté
        // Retour : true si réussi, false sinon

        $_SESSION["id"] = $id;
        $_SESSION["last_activity"] = time();
        return true;
    }

    static function deconnecter() {
        // Rôle : déconnecter le personnage connecté
        // Paramètres : néant
        // Retour : true si réussi false sinon

        $_SESSION["id"] = 0;
        unset($_SESSION["last_activity"]);
        return true;
    }

    static function estConnecte() {
        // Rôle : indiquer si un personnage est connecté
        // Paramètres : néant
        // Retour : true si une conection est active; false sinon

        return (!empty($_SESSION["id"]));
        // Vérifie la durée de session
        if (isset($_SESSION["last_activity"]) && (time() - $_SESSION["last_activity"] > self::DUREE_SESSION)) {
            self::deconnecter();
            return false;
        }
        // Rafraîchit l'activité
        $_SESSION["last_activity"] = time();
        return true;
    }

    static function verifLogin($login, $password) {
        // Rôle : vérifier que les codes de connexion sont valides
        // Paramètres :
        //      $login : login de connexion (pseudo)
        //      $password : mot de passe à vérifier
        // Retour : le personnage correspondant aux codes si il existe, false sinon

        // Chercher le personnage éventuel qui a le login indiqué
        $personnage = new personnage();
        $personnage->loadBy('pseudo', $login);
        // Si il n'existe pas : on retourne false
        if (!$personnage->is()) {
            return false;
        }

        // Vérifier que le mot de passe correspond (hashé) :
        //   si oui : on retourne le objet personnage
        //   sinon : on retourne false

        if (password_verify($password, $personnage->get("password"))) {
            return $personnage;
        } else {
            return false;
        }
    }

    static function moi() {
        // Rôle : Récupérer le personnage connecté (moi)
        // Paramètres  : néant
        // Retour : objet utiisateur, chargé avec le personnage connecté, ou non chargé

        if (self::estConnecte()) {
            return new personnage($_SESSION["id"]);
        } else {
            return new personnage();
        }
    }
    
}




