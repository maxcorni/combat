<?php
/*
Classe décrivant l'objet utilisateur du modèle conceptuel



*/


class personnage extends _model {


    // Décrire l'objet réel : attributs pour décrire l'objet réel
    // On décrit le modèle conceptuel
    protected $table = "personnage";
    protected $champs = [ "pseudo", "password", "pv", "force", "agilite", "resistance", "visibilite", "salle"];     // Liste simple des champs, sauf l'id
    protected $liens = [ "salle" => "salle" ];      // tableau  [ nomChamp => objetPointé, .... ]


    function getPersonnagesDansSalleHostile($idSalle, $idMoi) {
        // Récupère tous les personnages dans la salle $idSalle, hostile=1, sauf $idMoi
        // Paramètres :
        //      $idSalle : l'id de la salle dans laquelle on cherche les personnages
        //      $idMoi : l'id du personnage qui effectue la requête, pour ne pas le récupérer lui-même
        // Retourne un tableau d'objets personnage
        $sql = "SELECT " . $this->listeChampsPourSelect() . " FROM `$this->table` 
                WHERE salle = :salle 
                  AND id != :idMoi 
                  AND visibilite = 0
                  AND salle IN (SELECT id FROM salle WHERE hostile = 1)
                ORDER BY pseudo";
        $params = [
            ":salle" => $idSalle,
            ":idMoi" => $idMoi
        ];
        $tab = bddGetRecords($sql, $params);


        if (!$tab) return [];
        return $this->tab2TabObjects($tab);

    }

    function attaquerPersonnage($cible) {
        // Attaque un personnage cible et gère les effets de l'attaque.
        // Paramètres :
        //      $cible : objet personnage, la cible de l'attaque
        // Retourne un tableau de logs décrivant les actions effectuées.

        $log = [];
        $forceAttaque = $this->get('force');
        $pvCibleAvant = $cible->get('pv');

        $result = $cible->subirAttaque($this, $forceAttaque, $log);

        // Récompenses/punitions pour l'attaquant
        if ($result['result'] == 'esquive') {
            // Si l’adversaire a esquivé et que l’on a 10 points de force ou plus, un point de force devient un point de résistance.
            if ($this->get('force') >= 10) {
                $this->set('force', $this->get('force') - 1);
                $this->set('resistance', $this->get('resistance') + 1);
                $this->update();
                $log[] = htmlentities($this->get('pseudo')) . " échange 1 force contre 1 résistance suite à l'esquive de l'adversaire.";
            }
            // L'attaquant ne gagne rien d'autre
        } elseif ($result['result'] == 'defense_gagne') {
            // L'attaquant a perdu, il perd 1 PV
            $this->set('pv', max(0, $this->get('pv') - 1));
            $this->update();
            $log[] = htmlentities($this->get('pseudo')) . " perd 1 PV car la défense adverse a réussi.";
        } elseif ($result['result'] == 'defense_perdu') {
            // L'attaquant a gagné, il reçoit les récompenses
            if ($this->get('agilite') < 15) {
                $this->set('agilite', $this->get('agilite') + 1);
                $log[] = htmlentities($this->get('pseudo')) . " gagne 1 agilité.";
            } else {
                $this->set('pv', min(100, $this->get('pv') + 1));
                $log[] = htmlentities($this->get('pseudo')) . " gagne 1 PV.";
            }
            // Si on a tué l'adversaire, on récupère en plus les PV qui lui restaient juste avant le combat
            if ($cible->get('pv') <= 0) {
                $this->set('pv', min(100, $this->get('pv') + $pvCibleAvant));
                $log[] = htmlentities($this->get('pseudo')) . " a tué " . htmlentities($cible->get('pseudo')) . " et gagne $pvCibleAvant PV.";
            }
            $this->update();
        }
        return $log;
    }

    function subirAttaque($attaquant, $forceAttaque, &$log, $isRiposte = false) {
        // Rôle : Gérer la défense d'un personnage contre une attaque.
        // Paramètres :
        //      $attaquant : objet personnage, l'attaquant
        //      $forceAttaque : force de l'attaque de l'attaquant
        //      $log : tableau de logs pour enregistrer les actions effectuées
        //      $isRiposte : booléen, indique si c'est une riposte
        // Retourne un tableau avec le résultat de l'attaque et les PV perdus si applicable.
        $agilite = $this->get('agilite');
        $force = $this->get('force');
        $resistance = $this->get('resistance');
        $pv = $this->get('pv');
        $nomAttaquant = htmlentities($attaquant->get('pseudo'));
        $nomDefenseur = htmlentities($this->get('pseudo'));

        // Esquive
        if ($agilite >= $forceAttaque + 3) {
            $this->set('agilite', max(0, $agilite - 1));
            $this->update();
            $log[] = "$nomDefenseur esquive l'attaque de $nomAttaquant et perd 1 agilité.";
            return ['result' => 'esquive'];
        }

        // Riposte (force strictement supérieure)
        if ($force > $forceAttaque) {
            $log[] = "$nomDefenseur riposte contre $nomAttaquant !";
            // La riposte fonctionne comme une attaque, mais pas de contre-riposte possible
            $riposteResult = $attaquant->subirAttaque($this, $force, $log, true);
            if ($riposteResult['result'] == 'defense_gagne') {
                $this->set('pv', min(100, $pv + 1));
                $this->update();
                $log[] = "$nomDefenseur gagne la riposte et gagne 1 PV.";
                return ['result' => 'defense_gagne'];
            } else {
                $this->set('pv', max(0, $pv - 2));
                $this->update();
                $log[] = "$nomDefenseur perd la riposte et perd 2 PV.";
                return ['result' => 'defense_perdu', 'riposte' => true];
            }
        }

        // Défense
        if ($resistance >= $forceAttaque) {
            $this->set('pv', min(100, $pv + 1));
            $this->update();
            $log[] = "$nomDefenseur se défend avec succès et gagne 1 PV.";
            return ['result' => 'defense_gagne'];
        } else {
            $perte = $forceAttaque - $resistance;
            $this->set('pv', max(0, $pv - $perte));
            $this->update();
            $log[] = "$nomDefenseur ne résiste pas et perd $perte PV.";
            return ['result' => 'defense_perdu', 'pvPerdus' => $perte];
        }
    }

    function ajouterStat($stat, $val) {
        // Role : Ajouter un point à une statistique (force ou résistance) du personnage.
        // Paramètres :
        //      $stat : le nom de la statistique à modifier ('force' ou 'resistance')
        //      $val : la valeur à ajouter et soustraire (1, 2 ou 3)
        // Retourne true si la modification a été effectuée, false sinon.   

        $val = intval($val);
        $stat = strtolower($stat);
        $autre = ($stat === 'force') ? 'resistance' : (($stat === 'resistance') ? 'force' : null);
        if (!in_array($stat, ['force', 'resistance']) || $val < 1 || $val > 3) return false;

        // Calcul du coût
        if ($val == 1 && $this->get('agilite') >= 3) {
            $this->set('agilite', $this->get('agilite') - 3);
        } elseif ($val == 2 && $this->get('agilite') >= 5) {
            $this->set('agilite', $this->get('agilite') - 5);
        } elseif ($val == 3 && $this->get('pv') >= 10) {
            $this->set('pv', $this->get('pv') - 10);
        } else {
            return false;
        }

        // Appliquer la règle d'opposition
        $newStat = $this->get($stat) + $val;
        $newAutre = max(0, $this->get($autre) - $val);
        $this->set($stat, $newStat);
        $this->set($autre, $newAutre);
        $this->update();
        return true;
    }

    function changerSalle($direction) {
        // Rôle : Gérer le changement de salle du personnage.
        // Paramètres :
        //      $direction : 'suivante' ou 'precedente' pour changer de salle
        // Retourne true si le changement de salle a réussi, ou un message d'erreur
        
        $salleActuelle = $this->get('salle')->id();
        $agilite = $this->get('agilite');
        $pv = $this->get('pv');

        // Vérification de la direction
        if ($direction === 'suivante') {
            $salleSuivante = $salleActuelle + 1;
            $salleObj = new salle($salleSuivante);
            $miniagi = $salleObj->get('miniagi');
            if ($miniagi === "" || $agilite < $miniagi) {
                return "Agilité insuffisante ou salle inexistante";
            }
            $this->set('salle', $salleSuivante);
            $this->set('agilite', $agilite - $miniagi);
            $this->update();
            return true;
        } elseif ($direction === 'precedente') {
            $sallePrecedente = $salleActuelle - 1;
            if ($sallePrecedente <= 0) {
                return "Impossible de reculer (déjà dans la première salle)";
            }
            $salleObj = new salle($sallePrecedente);
            $miniagi = $salleObj->get('miniagi');
            $nouveauxPv = min($pv + (is_numeric($miniagi) ? (int)$miniagi : 0), 100);
            $this->set('salle', $sallePrecedente);
            $this->set('pv', $nouveauxPv);
            $this->update();
            return true;
        }
        return "Direction invalide";
    }

    function ajouterAgiliteAuto() {
        // Rôle : Ajoute 1 point d'agilité au personnage si possible
        // Paramètres : aucun
        // Retourne true si l'agilité a été ajoutée, ou un message d'er
        if ($this->get('visibilite') != 0) {
            return "Vous devez être visible pour gagner de l'agilité automatiquement.";
        }
        if ($this->get('agilite') >= 15) {
            return "Agilité déjà au maximum.";
        }
        // Ajoute ici d'autres vérifications métier si besoin (ex: pas en combat)
        $this->set('agilite', $this->get('agilite') + 1);
        $this->update();
        return true;
    }
}