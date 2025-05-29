<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Parcous_model
 *
 * @author albert
 */
class Seance_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database($this->session->data_base_group);
        test_acces();
    }

    public function update($id, $table) {
        // $table (array('nom'=>'trucmuche', 'prenom'=>'zozo', etc
        $where = "rowid = $id";
        $query = $this->db->update_string('ff_seance', $table, $where);

        $result = $this->db->query($query);
        if ($result) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function add($table) {
        $query = $this->db->insert_string('ff_seance', $table);
        $result = $this->db->query($query);
        if ($result) {
            return $this->db->insert_id();
        } else {
            return FALSE;
        }
    }

    public function get_seance($id = FALSE) {
        if ($id === FALSE) {
            $data = array();
            $query = "SELECT * FROM ff_seance "
                    . "ORDER BY intitule ASC";
            $result = $this->db->query($query);

            foreach ($result->result_array() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        // ici on retourne une séance
        $query = $this->db->get_where('seance', array('rowid' => $id));
        return $query->row_array();
    }

    public function get_id_by_name($name) {
        // ici on retourne une séance
        $query = $this->db->get_where('seance', array('intitule' => $name));
        $seance = $query->row_array();
        if (isset($seance['rowid'])) {
            return $seance['rowid'];
        } else {
            return NULL;
        }
    }

    public function get_name($id) {
        $query = "SELECT intitule FROM ff_seance WHERE rowid = $id";
        $result = $this->db->query($query);

        $seance = $result->row();

        if (isset($seance->intitule)) {
            return $seance->intitule;
        }
        return NULL;
    }

    public function set_seance_page($id_seance, $id_page, $rang) {
        $data = array(
            'fk_page' => $id_page,
            'fk_seance' => $id_seance,
            'ordre' => $rang
        );
        return $this->db->insert('ff_seance_page', $data);
    }

    public function get_seance_page($id_seance) {
        // revoie liste des pages d'une séance
        $requete = "SELECT sp.ordre, sp.fk_page as id, p.intitule, LENGTH(p.contenu) as size FROM ff_seance_page as sp "
                . " LEFT JOIN ff_page as p on sp.fk_page = p.rowid "
                . " WHERE sp.fk_seance = $id_seance ORDER BY sp.ordre ASC";
        $resultat = $this->db->query($requete);
        return $resultat->result_array();
    }

    public function get_nb_pages($id_seance) {
        // revoie nb des pages d'une séance
        $requete = "SELECT COUNT(sp.ordre) as nb FROM ff_seance_page as sp "
                . " WHERE sp.fk_seance = $id_seance";
        $query = $this->db->query($requete);
        $row = $query->row();

        if (isset($row)) {
            return $row->nb;
        }
        return NULL;
    }

    public function get_id_page($id_seance, $rang) {
        // renvoir id de la page de rang  pour une seance
        $requete = "SELECT fk_page FROM ff_seance_page  "
                . " WHERE fk_seance = $id_seance AND ordre = $rang";

        $query = $this->db->query($requete);
        $row = $query->row();

        if (isset($row)) {
            return $row->fk_page;
        }
        return NULL;
    }

    public function supprimer_seance_page($id_seance) {
        // on supprime les couples seance<->page
        $this->db->delete('seance_page', array('fk_seance' => $id_seance));
        // on reset le nb_page de la séance à 0;
        $this->db->where('rowid', $id_seance);
        $this->db->update('seance', array('nb_page' => 0));
    }

    public function set_seance_nb_page($id_seance, $nb_page) {
        $this->db->where('rowid', $id_seance);
        $this->db->update('seance', array('nb_page' => $nb_page));
    }

    public function set_seance_liste_pages($id_seance, $liste_pages) {
        // si les pages sont saisies sous forme 'n, n, n, n'
        // les pages doivent exister
        $this->load->model('page_model');

        $erreur = 0;
        $info = '';
        $tab = explode(',', $liste_pages);
        $nb_pages = count($tab);
//        trace('la liste ', $liste_pages);
//        trace('tableau', $tab);
        foreach ($tab as $id_page) {
            // le $id existe ??
            $page = $this->page_model->get(intval($id_page));
            if (is_null($page)) {
                $info .= "ERREUR : La page de numéro " . intval($id_page) . " n'existe pas !<br>";
                $erreur = 1;
            }
        }
        if ($erreur) {
            return $info;
        }
        // sinon on crée
        $rang = 1;
        $this->seance_model->supprimer_seance_page($id_seance);
        foreach ($tab as $id_page) {
            trace('--lien seance page :', intval($id_page) . ', seance ' . $id_seance);
            $this->seance_model->set_seance_page($id_seance, intval($id_page), $rang++);
        }
        //
        $this->db->where('rowid', $id_seance);
        $this->db->update('seance', array('nb_page' => $nb_pages));
        return '';
    }

    public function get_seance_liste_pages($id_seance) {
        // renvoie liste des pages sous la forme 'n, n, n, n'
        $requete = "SELECT sp.fk_page as id FROM ff_seance_page as sp "
                . " WHERE sp.fk_seance = $id_seance ORDER BY sp.ordre ASC";
        $resultat = $this->db->query($requete);
        $tab = $resultat->result_array();
        $liste = '';
        foreach ($tab as $value) {
            $liste .= $value['id'] . ', ';
        }
        return substr($liste, 0, -2);
    }

}
