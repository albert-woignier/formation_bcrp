<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Disponibilite_model
 *
 * @author albert
 */
class Disponibilite_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database($this->session->data_base_group);
// test_acces();
    }

    public function get_date($where = array()) {
        $query = "SELECT d.rowid, d.fk_moniteur, d.date, d.heure_debut, d.heure_fin, "
                . " d.nb_pers_max, d.nb_resa, d.complet, "
                . " CONCAT(SUBSTRING(P.prenom, 1,1), SUBSTRING(P.nom, 1,1)) as initiales "
                . " FROM ff_disponibilite as d"
                . " LEFT JOIN ff_personne as P ON P.rowid = d.fk_moniteur "
                . " WHERE d.statut = 1 "; // statut = 1 = 'actif'
        if (count($where) == 2) {
// de telle date à telle date
            $condition = " AND d.date >= '{$where[0]} AND d.date <= {$where[1]}' ORDER BY d.date ASC d.heure_debut ASC";
        } else if (count($where) == 1) {
            $condition = " AND d.date = '{$where[0]}' ORDER BY d.heure_debut ASC";
        } else {
            $condition = " ORDER BY d.date ASC d.heure_debut ASC";
        }
        $result = $this->db->query($query . $condition);
        $data = array();
        foreach ($result->result_array() as $row) {
            $data[] = $row;
        }
        return $data;
    }

    public function get_creneau($id_creneau) {
        $query = "SELECT d.rowid, d.fk_moniteur, d.date, d.heure_debut, d.heure_fin, "
                . " d.nb_pers_max, d.nb_resa, d.complet, "
                . " CONCAT(SUBSTRING(P.prenom, 1,1), SUBSTRING(P.nom, 1,1)) as initiales , "
                . " CONCAT(P.prenom, ' ', P.nom) as moniteur, "
                . " P.mail, P.phone "
                . " FROM ff_disponibilite as d "
                . " LEFT JOIN ff_personne as P ON P.rowid = d.fk_moniteur "
                . " WHERE d.rowid= $id_creneau ";

        $result = $this->db->query($query);

        return $result->row_array();
    }

    public function already_creneau($id_moniteur, $date, $debut) {
        $query = "SELECT rowid FROM ff_disponibilite "
                . " WHERE fk_moniteur= $id_moniteur AND date = '$date' AND heure_debut = '$debut' ";
        $result = $this->db->query($query);
        $creneau = $result->row_array();
        if (isset($creneau['rowid'])) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_creneau_resa($id_moniteur = 0, $id_eleve = 0) {
        if ($id_moniteur > 0) {
            $where = " AND d.fk_moniteur = $id_moniteur ";
        } else if ($id_eleve > 0) {
            $where = " AND resa.fk_eleve = $id_eleve ";
        } else {
            $where = '';
        }
        $condition_statuts = "resa.statut in ('" . RESA_OK . "', '" . RESA_WAIT . "')"; // on ne prend que ces 2 ststuts
        $query = "SELECT d.rowid as id_creneau, d.fk_moniteur, d.date, d.heure_debut, d.heure_fin,
            d.nb_pers_max, d.nb_resa, d.complet,
            CONCAT(SUBSTRING(P.prenom, 1,1), SUBSTRING(P.nom, 1,1)) as initiales ,
            CONCAT(P.prenom, ' ', P.nom) as moniteur,
            CONCAT(E.prenom, ' ', E.nom) as eleve, resa.rowid as id_resa, resa.fk_eleve, resa.statut
            FROM ff_disponibilite as d
            LEFT JOIN ff_personne as P ON P.rowid = d.fk_moniteur
            LEFT JOIN ff_resa as resa ON resa.fk_disponibilite = d.rowid AND " . $condition_statuts .
                " LEFT JOIN ff_personne as E ON E.rowid = resa.fk_eleve
            WHERE d.date >= NOW() AND d.statut = 1 " . $where . " ORDER BY d.date ASC, d.heure_debut ASC";
//        trace('requete ', $query);

        $result = $this->db->query($query);

        return $result->result_array();
    }

    public function add($creneau) {
// $creneau tableau associatif
// trace('Disponibilite_model', 'on rentre dans add');
        $query = $this->db->insert_string('ff_disponibilite', $creneau);
        $result = $this->db->query($query);
        if ($result) {
            return $this->db->insert_id();
        } else {
            trace('Disponibilite_model', 'erreur  add !! ' . $result);
            return FALSE;
        }
    }

    public function creneau_supprimer($id_creneau) {
// Annuler une séance.
//

        $msg = '';
        // on récupère les infos du créneau
        $creneau = $this->get_creneau($id_creneau);
        $infos['date'] = $creneau['date'];
        $infos['heure_debut'] = $creneau['heure_debut'];
        $infos['nom_moniteur'] = $creneau['moniteur'];
        $infos['mail_moniteur'] = $creneau['mail'];
        $infos['mail_eleve'] = array();
        trace('creneau_supprimer, le créneau ', $infos);

        // on modifie la séance, statut inactif
        $this->update($id_creneau, array('statut' => 0));

        // on récupère les inscrits sur ce créneau
        $condition_statuts = " r.statut in ('" . RESA_OK . "', '" . RESA_WAIT . "')"; // on ne prend que ces 2 ststuts
        $query = "SELECT r.rowid as id_resa, CONCAT(P.prenom, ' ', P.nom) as nom_eleve, "
                . " P.mail as mail_eleve "
                . " FROM ff_resa as r "
                . " LEFT JOIN ff_personne as P ON P.rowid = r.fk_eleve "
                . " WHERE r.fk_disponibilite = $id_creneau AND  $condition_statuts";
        $result = $this->db->query($query);
        $les_resas = $result->result_array();
        trace('creneau_supprimer, les résas : ', $les_resas);
        // on modifie chaque résa, statut
        foreach ($les_resas as $resa) {
            // on modifie la résa
            $where = "rowid = " . $resa['id_resa'];
            $query = $this->db->update_string('ff_resa', array('statut' => RESA_SEANCE_ANNUL), $where);
            trace('creneau_supprimer, update : ', $query);
            $result = $this->db->query($query);
            if (!$result) {
                trace('creneau_supprimer', 'erreur update !! ' . $result);
            }
            // on enregistre le mail de l'élève
            if ($resa['mail_eleve'] !== '') {
                $infos['mail_eleve'][] = $resa['mail_eleve'];
            } else {
                $msg .= $resa['nom_eleve'] . ' n\'a pas de mail, il n\'a pas pu être averti.   ';
            }
        }
        if (count($infos['mail_eleve']) > 0) {
            // on envoie mail
            $this->send_email_resa('annul_seance', $infos);
        }
        return $msg;
    }

    public function update($id, $creneau) {

        // trace('Disponibilite_model', 'on rentre dans update');
        $where = "rowid = $id";
        $query = $this->db->update_string('ff_disponibilite', $creneau, $where);

        $result = $this->db->query($query);
        if ($result) {
            return TRUE;
        } else {
            trace('Disponibilite_model', 'erreur update !! ' . $result);
            return FALSE;
        }
    }

//
// `ff_resa`(`rowid`, `fk_eleve`, `fk_disponibilite`, `statut`)
// ststut : RESA_WAIT , ....
//
    public function add_resa($id_creneau, $id_eleve, $statut = RESA_OK) {

        $query = $this->db->insert_string('ff_resa', array('fk_eleve' => $id_eleve, 'fk_disponibilite' => $id_creneau, 'statut' => $statut));
        $result = $this->db->query($query);
        // et on met à jour le créneau
        $this->update_nb_resa($id_creneau, 1);
    }

    public function already_resa($id_creneau, $id_eleve) {

// vérifier si eleve déja inscrit sur ce créneau
        $condition_statuts = "statut in ('" . RESA_OK . "', '" . RESA_WAIT . "')"; // on ne prend que ces 2 ststuts
        $result = $this->db->query("SELECT statut FROM ff_resa WHERE fk_eleve = $id_eleve AND fk_disponibilite = $id_creneau AND $condition_statuts");
        $row = $result->row_array();
        if (isset($row)) {
            return $row['statut']; // déjà inscrit sur ce créneau
        } else {
            return FALSE;
        }
    }

    public function cancel_resa($id_resa) {

// TODO voir si utilisé et supprimer

        $where = "rowid = $id_resa";
        $query = $this->db->update_string('ff_resa', array('statut' => RESA_REFUS), $where);

        $result = $this->db->query($query);
        if ($result) {
            return TRUE;
        } else {
            trace('Disponibilite_model', 'erreur cancel_resa !! ' . $result);
            return FALSE;
        }
    }

    public function confirm_resa($id_resa) {

// on regarde si le créneau est complet ou non
        $query = "SELECT resa.fk_disponibilite as id_creneau,
            CONCAT(P.prenom, ' ', P.nom) as nom_eleve, P.mail as mail_eleve,
            d.nb_pers_max, d.nb_resa, d.complet, d.date, d.heure_debut,
            CONCAT(M.prenom, ' ', M.nom) as nom_moniteur, M.phone
            FROM ff_resa as resa
            LEFT JOIN ff_disponibilite as d ON resa.fk_disponibilite = d.rowid
            LEFT JOIN ff_personne as P ON P.rowid = resa.fk_eleve
            LEFT JOIN ff_personne as M ON M.rowid = d.fk_moniteur
            WHERE resa.rowid=$id_resa";
        $result = $this->db->query($query);

        $resa = $result->row_array();

        $complet = $resa['complet'];
        if ($complet) {
// on modifie la résa à RESA_REFUS
            $where = "rowid = $id_resa";
            $query = $this->db->update_string('ff_resa', array('statut' => RESA_REFUS), $where);
            $this->send_email_resa('cancel', $resa);
            return "Créneau déjà complet. La demande est refusée.";
        }
        $id_creneau = $resa['id_creneau'];
        $nb_max = $resa['nb_pers_max'];
        if ($resa['nb_resa'] + 1 == $nb_max) {
            $complet = 1;
        }
// c'est ok on modifie le créneau
        $query = "UPDATE ff_disponibilite set nb_resa = nb_resa+1, complet = $complet "
                . " WHERE rowid = $id_creneau ";
        $this->db->query($query);

// on modifie la résa à RESA_OK
        $where = "rowid = $id_resa";
        $query = $this->db->update_string('ff_resa', array('statut' => RESA_OK), $where);
        $this->db->query($query);

        $this->send_email_resa('confirm', $resa);

        return 'Créneau mis à jour';
    }

    public function annul_resa($id_resa) {
// on supprime une résa d'un élève sur un créneau.
// pas d'envoi de mail tout s'est fait par téléphone !!

        $query = "SELECT resa.fk_disponibilite as id_creneau,
            d.fk_moniteur as id_moniteur, d.complet
            FROM ff_resa as resa
            LEFT JOIN ff_disponibilite as d ON resa.fk_disponibilite = d.rowid
            WHERE resa.rowid=$id_resa";
        $result = $this->db->query($query);
        $resa = $result->row_array();

// on modifie la résa à 'annul'
        $where = "rowid = $id_resa";
        $query = $this->db->update_string('ff_resa', array('statut' => RESA_ANNUL, 'fk_user' => $this->session->user_id), $where);
        $result = $this->db->query($query);
        trace('annul_resa, update resa = ' . $result);
        $id_creneau = $resa['id_creneau'];

// on modifie le créneau
        trace('annul_resa, id_creneau =' . $id_creneau);
        $this->update_nb_resa($id_creneau, -1);

        return 'Inscription Annulée.';
    }

    public function infirm_resa($id_resa) {

// on modifie la résa à RESA_REFUS
        $query = "SELECT resa.fk_disponibilite as id_creneau,
            CONCAT(P.prenom, ' ', P.nom) as nom_eleve, P.mail as mail_eleve,
            d.nb_pers_max, d.nb_resa, d.complet, d.date, d.heure_debut,
            CONCAT(M.prenom, ' ', M.nom) as nom_moniteur, M.phone
            FROM ff_resa as resa
            LEFT JOIN ff_disponibilite as d ON resa.fk_disponibilite = d.rowid
            LEFT JOIN ff_personne as P ON P.rowid = resa.fk_eleve
            LEFT JOIN ff_personne as M ON M.rowid = d.fk_moniteur
            WHERE resa.rowid=$id_resa";
        $result = $this->db->query($query);
        $resa = $result->row_array();
        $where = "rowid = $id_resa";
        $query = $this->db->update_string('ff_resa', array('statut' => RESA_REFUS), $where);
        $this->db->query($query);
        $this->send_email_resa('cancel', $resa);
        return 'Réservation refusée';
    }

    public function update_nb_resa($id_creneau, $number_to_add) {
        // on récupère les infos du créneau
        $query = "SELECT d.nb_pers_max, d.nb_resa, d.complet
            FROM ff_disponibilite as d
            WHERE d.rowid=$id_creneau";
        $result = $this->db->query($query);
        $resa = $result->row_array();
        $nb_pers_max = $resa['nb_pers_max'];
        $nb_resa = $resa['nb_resa'];
        $complet = $resa['complet'];
        $nb_resa = $nb_resa + $number_to_add; // on ajoute ou décrémente le nb de réservations su r ce créneau
        if ($nb_resa == $nb_pers_max) {
            $complet = 1;
        } else {
            $complet = 0;
        }
        // on modifie le créneau
        $query = "UPDATE ff_disponibilite set nb_resa = $nb_resa, complet = $complet "
                . " WHERE rowid = $id_creneau ";
        $this->db->query($query);

        return 'Créneau mis à jour';
    }

    public function liste_resa_creneau($id_creneau) {
// renvoie index : id_resa, id_eleve, eleve, mail éléve
        $condition_statuts = "r.statut in ('" . RESA_OK . "', '" . RESA_WAIT . "')"; // on ne prend que ces 2 ststuts

        $query = "SELECT r.rowid as id_resa, r.statut as statut, r.fk_eleve as id_eleve, CONCAT(P.prenom, ' ', P.nom) as eleve,"
                . " P.mail  "
                . " FROM ff_resa as r "
                . " LEFT JOIN ff_personne as P ON P.rowid = r.fk_eleve "
                . " WHERE r.fk_disponibilite = $id_creneau AND " . $condition_statuts;

        $result = $this->db->query($query);

        return $result->row_array();
    }

    public function liste_resa_eleve($id_eleve) {
// TODO : jointure avec id_eleve, id_moniteur ...
        // NON UTILISE
        $condition_statuts = "r.statut in ('" . RESA_OK . "', '" . RESA_WAIT . "')"; // on ne prend que ces 2 ststuts
        $query = "SELECT  r.rowid as id_resa, r.statut as statut, r.fk_disponibilite as id_creneau, "
                . "d.fk_moniteur, d.date, d.heure_debut, d.heure_fin, "
                . " CONCAT(P.prenom, ' ', P.nom) as moniteur  "
                . " FROM ff_resa as r "
                . " LEFT JOIN ff_disponibilite as d ON r.fk_disponibilte = d.rowid "
                . " LEFT JOIN ff_personne as P ON P.rowid = d.fk_moniteur "
                . " WHERE r.fk_eleve = $id_eleve AND $condition_statuts";

        $result = $this->db->query($query);

        return $result->row_array();
    }

    public function liste_eleves_creneau($id_creneau) {
// renvoie index : id_resa, id_eleve, eleve
        $condition_statuts = "r.statut in ('" . RESA_OK . "', '" . RESA_WAIT . "')"; // on ne prend que ces 2 ststuts
        $query = "SELECT r.rowid as id_resa, r.statut as statut, r.fk_eleve as id_eleve, CONCAT(P.prenom, ' ', P.nom) as eleve "
                . " FROM ff_resa as r "
                . " LEFT JOIN ff_personne as P ON P.rowid = r.fk_eleve "
                . " WHERE r.fk_disponibilite = $id_creneau AND $condition_statuts "
                . " ORDER BY r.rowid ASC";

        $result = $this->db->query($query);

        return $result->result_array();
    }

    public function send_email_resa($action, $infos) {
        $this->load->library('email');
        $this->load->helper('my_html_helper');

        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from('formation@billard-romans.fr', 'Formation BCRP');
        $link = site_url('');
        if ($action == 'resa') {
// mail à moniteur copie élève
            $subject = '[BCRP Formation] Inscription au créneau horaire du ' . date_fr($infos['date']) . ' à ' . $infos['heure_debut'];
            $this->email->to($infos['mail']);
            $this->email->subject($subject);
            $message = $this->session->user_nom_complet . ' s\'est inscrit sur votre créneau horaire :';
            $message .= ' <br>Date : ' . date_fr($infos['date']);
            $message .= ' <br>Heure de début : ' . ($infos['heure_debut']);
            $message .= ' <br>Nombre de personnes max : ' . ($infos['nb_pers_max']);
            $message .= ' <br>Nombre de personnes déjà acceptées : ' . ($infos['nb_resa']);
            $message .= '<br><br>En cas de problème contactez directement ' . $this->session->user_nom_complet;
            $this->email->message($message);
            $this->email->send();

            // copie à éléve
            $this->email->to($this->session->user_mail);
            $this->email->subject($subject);
            $message = 'Votre inscription a été transmise à ' . $infos['moniteur'] . ' pour le créneau :';
            $message .= ' <br>Date : ' . date_fr($infos['date']);
            $message .= ' <br>Heure de début : ' . ($infos['heure_debut']);
            $message .= ' <br>Nombre de personnes max : ' . ($infos['nb_pers_max']);
            // $message .= ' <br>Nombre de personnes déjà acceptées : ' . ($infos['nb_resa']);
            $message .= ' <br><br>En cas de problème contactez directement ' . $infos['moniteur'];
            $this->email->message($message);
            $this->email->send();
            return '';
        } else if ($action == 'confirm') {
// mail à élève copie moniteur
            // N'a plus lieu d'être


            $this->email->to($infos['mail_eleve']); // élève
            // $this->email->cc($this->session->user_mail); // moniteur

            $this->email->subject('[BCRP Formation] Confirmation de votre inscription à un créneau horaire');
            $message = $this->session->user_nom_complet . ' confirme votre inscription pour le créneau horaire :';
            $message .= ' <br>Animé par : ' . ($infos['nom_moniteur']);
            $message .= ' <br>Date : ' . date_fr($infos['date']);
            $message .= ' <br>Heure de début : ' . ($infos['heure_debut']);
            $message .= ' <br>Nom du joueur inscrit : ' . $infos['nom_eleve'];
            $message .= " <br><br>IMPORTANT : Joignez votre moniteur par mail ou téléphone ({$infos['phone']}) en cas d'impossibilité de vous rendre au rendez-vous.";
            $this->email->message($message);

            $this->email->send();
            return '';
        } else if ($action == 'cancel') {
// mail à élève copie moniteur
            // N'a plus lieu d'être

            $this->email->to($infos['mail_eleve']); // élève
            // $this->email->cc($this->session->user_mail); // moniteur

            $this->email->subject('[BCRP Formation] Annulation de votre demande d\'inscription à un créneau horaire');
            $message = $this->session->user_nom_complet . ' ne peut satisfaire votre demande pour le créneau :';
            $message .= ' <br>Animé par  : ' . $infos['nom_moniteur'];
            $message .= ' <br>Date : ' . date_fr($infos['date']);
            $message .= ' <br>Heure de début : ' . ($infos['heure_debut']);
            $message .= ' <br>Nom joueur : ' . $infos['nom_eleve'];
            $message .= " <br><br>Désolé pour cette réponse due soit à une inscription plus ancienne que la votre, soit à une annulation de la séance.";
            $this->email->message($message);

            $this->email->send();
            return '';
        } else if ($action == 'annul_seance') {
// mail à élève copie moniteur
            if (is_array($infos['mail_eleve'])) {
                $les_mail = implode(",", $infos['mail_eleve']);
            }
            $this->email->to($les_mail); // élève

            $this->email->cc($infos['mail_moniteur']); // moniteur

            $subject = '[BCRP Formation] Annulation de la séance du ' . date_fr($infos['date']) . ' à ' . $infos['heure_debut'];
            $this->email->subject($subject);
            $message = $this->session->user_nom_complet . ' est contraint d\'annuler la séance :';
            $message .= ' <br>Animé par  : ' . $infos['nom_moniteur'];
            $message .= ' <br>Date : ' . date_fr($infos['date']);
            $message .= ' <br>Heure de début : ' . ($infos['heure_debut']);
            $message .= " <br><br>Désolé pour cette annulation et merci de votre compréhension.";
            $this->email->message($message);

            $this->email->send();
            return '';
        } else {

        }
    }

}
