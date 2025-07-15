<?php

/* Classe _model : manipulation d'un objet générique du MCD */

// La clé primaire s'appelle id


class _model {

    // Décrire l'objet réel : attributs pour décrire l'objet réel
    // On décrit le modèle conceptuel
    protected $table = "";
    protected $champs = [];     // Liste simple des champs, sauf l'id
    protected $liens = [];      // tableau  [ nomChamp => objetPointé, .... ]

    protected $valeurs = [];    // stockage des valeurs des attributs (quand remplis) [ "champ1" = "valeur1", "champ2" => "valeur2" ]
    protected $id;              // Stockage de l'id

    function __construct($id = null) {
        // Rôle : constructeur (appelée automatiquement au "new") : charger un ligne de la BDD si on précise un id
        // Paramètre :
        //      $id (facultatif) : id d la lign à charger

        if (! is_null($id)) {
            $this->loadFromId($id);
        }
    }

    function is() {
        // Rôle : indiquer si l'objt est chargé ou non
        // Paramètres : néant
        // Retour : true si l'objet est chargé, false sinon

        // Si il est chargé, on a un id on vide

        return ! empty($this->id);

    }

    function id() {
        // Rôle : retourner l'id d l'objet dans la BDD ou 0
        // Paramètres : néant
        // Retour : la valur de l'id ou 0
        return $this->id;

    }


    // Getters
    function getTable() {
        return $this->table;
    }

    function get($nom) {
        // Rôle : getter, récupérant la valeur d'un champ donné (du modèle conceptuel, champs simples - texte, nombre, date -, liens simples)
        // Paramètres : 
        //      $nom : nom du champ à récupérer
        // Retour : valeur du champ ou valeur par défaut (chaine vide)
        //              si le champ est un lien, on retourne l'objet pointé 
        
        if ( ! in_array($nom, $this->champs))  {
            // Si ce n'est pas un champ
            return "";
        }

        if (isset($this->liens[$nom])) {
            // On veut retournr l'objt pointé
            $typeObjet = $this->liens[$nom];
            $objetPointe = new $typeObjet(isset($this->valeurs[$nom]) ? $this->valeurs[$nom] : 0);
            return $objetPointe;

        }

        if (isset($this->valeurs[$nom])) {
            // On a un valeur : on la retourne
            return $this->valeurs[$nom];
        } else {
            return "";
        }


    }

    // Setters

    function set($nom, $valeur) {
        // Rôle : setter, donne ou modifie la valeur d'un champ
        // Paramètres :
        //      $nom : nom du champ concerné
        //      $valeur : nouvelle valeur
        // Retour : true si ok, false sinon

        if ( ! in_array($nom, $this->champs))  {
            return false;
        }
        $this->valeurs[$nom] = $valeur;
        return true;

    }

    // Méthodes d'accés à la base de donnés

    function loadFromId($id) {
        // Rôle : charger un objet depuis un ligne de la BDD ayant un id donné
        // Paramètre :
        //  $id : id à chercher
        // Retour :
        //  true si on a réussi à charger, false sinon

        // Cette méthode va donc remplir le tableau $this->valeurs

        // Construire la requête : SELECT `id`, `nomChamp1`, `nomChamp2`, ... FROM `nomTable` WHERE `id` = :id
        $sql = "SELECT " . $this->listeChampsPourSelect() . " FROM `$this->table` WHERE `id` = :id";

        $tab = bddGetRecord($sql, [ ":id" =>  $id ]);

        if ($tab == false) {
            $this->valeurs = [];
            $this->id = null;
            return false;
        }

        $this->loadFromTab($tab);
        $this->id = $id;
        return true;

    }

    function update() {
        // Rôle : mette à jour l'objet courant dans la BDD
        // Paramètres : néant
        // Retour : true si ok, false sinon

        if (! $this->is()) {
            return false;
        }

        return bddUpdate($this->table, $this->valeurs, $this->id);

    }

    function insert() {
        // Rôle : créer l'objet courant dans la BDD
        // Paramètres : néant
        // Retour : true si ok, false sinon

        if ($this->is()) {
            return false;
        }

        $id = bddInsert($this->table, $this->valeurs);
        if (empty($id)) {
            return false;
        } else {
            // mise à jour de l'id
            $this->id = $id;
            return true;
        }

    }

    function delete() {
        // Rôle : supprimer l'objet courant de  la BDD
        // Paramètres : néant
        // Retour : true si ok, false sinon

        if (! $this->is()) {
            return false;
        }

        $cr = bddDelete($this->table, $this->id);

        if (!$cr) {
            return false;
        } else {
            $this->id = null;
            return true;
        }
    }

    function listAll() {
        // Rôle : récupérer tous les objets de ce type dans la BDD
        // Paramètres : néant
        // Retour : list (tableau indexé) d'objete de cette nature, indexé par l'id

        $sql = "SELECT " . $this->listeChampsPourSelect() . " FROM `$this->table`";

        $tab = bddGetRecords($sql);
        return $this->tab2TabObjects($tab);
    }

    function find($criteres) {
        // Rôle : recherche un objet selon un ou plusieurs critères (ex : ['pseudo' => 'toto'])
        // Paramètres :
        //      $criteres : tableau associatif des critères de recherche, indexé par le nom du champ (ex : ['pseudo' => 'toto'])
        // Retour : true si trouvé (et charge l'objet), false sinon

        if (empty($criteres) || !is_array($criteres)) {
            return false;
        }

        $where = [];
        $params = [];
        foreach ($criteres as $champ => $valeur) {
            $where[] = "`$champ` = :$champ";
            $params[":$champ"] = $valeur;
        }
        $sql = "SELECT " . $this->listeChampsPourSelect() . " FROM `$this->table` WHERE " . implode(" AND ", $where) . " LIMIT 1";
        $ligne = bddGetRecord($sql, $params);

        if (empty($ligne)) {
            return false;
        }

        $this->loadFromTab($ligne);
        $this->id = $ligne["id"];
        return true;
    }

    function loadBy($champ, $valeur) {
        // Rôle : charger un objet depuis un champ et une valeur donnés
        // Paramètres :
        //      $champ : nom du champ à utiliser pour la recherche
        //      $valeur : valeur à chercher dans le champ
        // Retour : true si l'objet est trouvé et chargé, false sinon

        if (!in_array($champ, $this->champs) && $champ !== 'id') {
            return false;
        }

        $sql = "SELECT " . $this->listeChampsPourSelect() . " FROM `$this->table` WHERE `$champ` = :valeur";
        $param = [ ":valeur" => $valeur ];
        $ligne = bddGetRecord($sql, $param);

        if (empty($ligne)) {
            return false;
        }

        $this->loadFromTab($ligne);
        $this->id = $ligne["id"];
        return true;
    }


    // Méthodes utilitaires
    function listBddToListObj($lignes) {
        // Rôle : convertir un tableau de données en tableau d'objets de la classe courante
        // Paramètres :
        //      $lignes : tableau de données (résultat de la BDD)
        // Retour : tableau d'objets ou false si erreur

        if (empty($lignes)) {
            return false;
        }

        $objets = [];
        foreach ($lignes as $ligne) {
            $objet = new static(); // Créer un objet de la classe courante
            $objet->loadFromTab($ligne);
            $objets[] = $objet;
        }

        return $objets;
    }

    function loadFromTab($tab) {  // par exempl pour un fournisseur [ "nom" => "Siemens", "Ville" => "Paris];  // les champs sont "nom", "cp", "ville", "adresse"
        // Rôle : charger les valeurs des champs à partir de données dans un tableau indexé par les noms des champs (sauf l'id)
        // Paramètres :
        //      $tab : tableau indexé comportant en index des noms de chmaps de cet objet et en valeur la valeur du champ
        // Retour : true si ok, false sinon

        foreach ($this->champs as $nomChamp) {
            if (isset($tab[$nomChamp])) {  
                $this->set($nomChamp, $tab[$nomChamp] );
            }
        }
        return true; 

    }


    function listeChampsPourSelect() {
        // Rôle : construire la liste des champs de cet objet pour un SELECT, cad `id`,`nomChamp1`,`nomChamp2`, ...
        // Paramètres : néant
        // Retour : le texte à inclure dans du SQL

        $texte = "`id`";
        foreach($this->champs as $nomChamp) {
            $texte .= ",`$nomChamp`";
        }
        return $texte;

    }

    function tab2TabObjects($tab) {
        // Rôle : à partir d'un tableau simple de tableaux indexés des valurs des champs (type de tablau récupére la BDD) en un tablau d'objest de la classe courante
        // Paramètres :
        //      $tab : le tablau à transformer
        // Retour : tableau d'objets de la classe courant, indexé par l'id

        if (empty($tab) || !is_array($tab)) {
            return [];
        }

        $resultat = [];
        foreach ($tab as $elt) {
            $objet = new static();
            $objet->loadFromTab($elt);
            $objet->id = $elt["id"];    
            $resultat[$objet->id()] = $objet;

        }
        return $resultat;

    }

}

