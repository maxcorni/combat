<?php

/*
Librairie de fonctions d'accès à la Base de données

Les fonctions s'appuient sur $bdd, variable globale contenant un objet PDO initialisé sur la bonne base

*/


function bddRequest($sql, $param = []) {         
    // Rôle : préparer et exécuter une requète, et de retourner false ou un objet "PDOstatement" (requête récupérée par $bdd->prepare())
    // Paramètres :
    //      $sql : texte de la commande SQL complète, avec des paramètres :xxx  exemple : 
    //      $param : tableau de valorisation des paramètres :xxx                        
    // Retour : soit false, soit la requête préparée et exécutée
    global $bdd;
    $req = $bdd->prepare($sql);

    if (empty($req))   {
        return false;
    }

    $cr = $req->execute($param);
    if ($cr) {
        return $req;
    } else {  
        return false;
    }

}

function bddGetRecord($sql, $param = []) {
    // Rôle : Retourne un enregsitrement de la base de données (la première ligne récupérée par un SELECT) sous forme d'un tableau indexé
    // Paramètres :
    //      $sql : texte de la commande SQL complète, avec des paramètres :xxx
    //      $param : tableau de valorisation des paramètres :xxx
    // Retour : soit false (si on a aucune ligne), soit la première ligne récupérée (tableau indexé)
    
    $req = bddRequest($sql, $param);
    if ($req === false) {
        return false;
    }

    $ligne = $req->fetch(PDO::FETCH_ASSOC);
    if (empty($ligne)) {
        return false;
    } else {
        return $ligne;
    }

}

function bddGetRecords($sql, $param = []) {
    // Rôle : Retourne les lignes récupérées par un SELECT
    // Paramètres :
    //      $sql : texte de la commande SQL complète, avec des paramètres :xxx
    //      $param : tableau de valorisation des paramètres :xxx
    // Retour : un tableau comprenant des tableaux indexés par les noms des colonnes (ou un tableau vide)

    $req = bddRequest($sql, $param);

    if ($req == false) {
        return [];
    }

    return $req->fetchAll(PDO::FETCH_ASSOC);

}

function bddInsert($table, $valeurs) {
    // Rôle : Insert un enregistrement dans la base de données et retourne la clé primaire créée 
    // Paramètres :
    //      $table : nom de la table dasn la BDD
    //      $valeurs : tableau donnant les valeurs des champs (colonnes de la table) [ "nomChamp1" => valeurAdonner, ... ]
    // Retour : 0 en cas d'échec, la clé primaire créée sinon

    $sql = "INSERT INTO `$table` ";
    $param = [];
    $tab = [];

    foreach($valeurs as $nomChamp => $valeurChamp) {
        $tab[] = "`$nomChamp` = :$nomChamp";
        $param[":$nomChamp"] = $valeurChamp;
    }
    $sql .= " SET " . implode(", ", $tab);

    $req = bddRequest($sql, $param);

    if ($req == false) {
        return 0;
    }

    global $bdd;
    return $bdd->lastInsertId();


}

function bddUpdate($table, $valeurs, $id) {
    // Rôle : Mettre à jour un enregistrement dans la base de données
    // Paramètres :
    //      $table : nom de la table dans la BDD
    //      $valeurs : tableau donnant les nouvelles valeurs des champs (colonnes de la table) [ "nomChamp1" => valeurAdonner, ... ]
    //      $id : valeur de la clé primaire (la clé primaire doit s'appeler id)
    // Retour : true si ok, false sinon

    $sql = "UPDATE `$table` ";
    $param = [];
    $tab = [];
    foreach($valeurs as $nomChamp => $valeurChamp) {
        $tab[] = "`$nomChamp` = :$nomChamp";
        $param[":$nomChamp"] = $valeurChamp;
    }
    $sql .= " SET " . implode(", ", $tab);

    $sql .= " WHERE `id` = :id";
    $param[":id"] = $id;

    $req = bddRequest($sql, $param);

    if ($req == false) {
        return false;
    } else { 
        return true;
    }

}

function bddDelete($table, $id) {
    // Rôle : Supprimer un enregistrement dans la base de données
    // Paramètres :
    //      $table : nom de la table dans la BDD
    //      $id : valeur de la clé primaire (la clé primaire doit s'appeler id)
    // Retour : true si ok, false sinon

    $sql = "DELETE `$table`  WHERE `id` = :id";
    $param = [ ":id" => $id ];

    $req = bddRequest($sql, $param);

    if ($req == false) {
        return false;
    } else { 
        return true;
    }
}

