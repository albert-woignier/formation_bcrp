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
class Parcours_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database($this->session->data_base_group);
        test_acces();
    }

    public function update($id, $table) {
        // $table (array('nom'=>'trucmuche', 'prenom'=>'zozo', etc
        $where = "rowid = $id";
        $query = $this->db->update_string('ff_parcours', $table, $where);

        $result = $this->db->query($query);
        if ($result) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function add($table) {
        $query = $this->db->insert_string('ff_parcours', $table);
        $result = $this->db->query($query);
        if ($result) {
            return $this->db->insert_id();
        } else {
            return FALSE;
        }
    }

    public function get_parcours($id = FALSE) {
        if ($id === FALSE) {
            $data = array();
            $query = "SELECT p.rowid, p.intitule as intitule, p.modele_examen, c.intitule as discipline, n.intitule as niveau, nb_seance FROM ff_parcours as p "
                    . "LEFT JOIN ff_t_discipline as c on p.fk_discipline = c.rowid "
                    . "LEFT JOIN ff_t_niveau as n on p.fk_niveau = n.rowid "
                    . "ORDER BY c.rowid ASC, n.rowid ASC, p.ordre ASC";
            $result = $this->db->query($query);

            foreach ($result->result_array() as $row) {
                $data[] = $row;
            }
            return $data;
        }

        $query = $this->db->get_where('parcours', array('rowid' => $id));
        return $query->row_array();
    }
    
    public function quelle_discipline_id($id) {
                    $query = "SELECT p.fk_discipline, c.intitule as discipline FROM ff_parcours as p "
                    . "LEFT JOIN ff_t_discipline as c on p.fk_discipline = c.rowid "
                    . " WHERE p.rowid = $id";
            $result = $this->db->query($query);
        $parcours = $result->row_array();
        return $parcours['fk_discipline'];
    }

    public function get_id_name() {
        // liste des parcours

        $data = array();
        $query = "SELECT p.rowid as parcours_id, p.intitule as parcours_name FROM ff_parcours as p "
                . "ORDER BY p.ordre ASC";
        $result = $this->db->query($query);

        return $result->result_array();
    }

    public function get_name($id) {
        $query = "SELECT intitule FROM ff_parcours WHERE rowid = $id";
        $result = $this->db->query($query);

        $parcours = $result->row();

        if (isset($parcours)) {
            return $parcours->intitule;
        }
        return NULL;
    }

    public function get_disciplines() {
        $data = array();
        $query = "SELECT rowid, intitule FROM ff_t_discipline ORDER BY rowid ASC ";
        $result = $this->db->query($query);
        foreach ($result->result_array() as $row) {
            $data[$row['rowid']] = $row['intitule'];
        }
        return $data;
    }

    public function get_niveaux() {
        $data = array();
        $query = "SELECT rowid, intitule FROM ff_t_niveau ORDER BY rowid ASC ";
        $result = $this->db->query($query);
        foreach ($result->result_array() as $row) {
            $data[$row['rowid']] = $row['intitule'];
        }
        return $data;
    }

    public function set_parcours_nb_seance($id_parcours, $nb_seance) {
        $this->db->where('rowid', $id_parcours);
        $this->db->update('parcours', array('nb_seance' => $nb_seance));
    }

    public function set_parcours_seance($id_parcours, $id_seance, $rang) {
        $data = array(
            'fk_parcours' => $id_parcours,
            'fk_seance' => $id_seance,
            'ordre' => $rang
        );
        return $this->db->insert('ff_parcours_seance', $data);
    }

    public function get_parcours_seance($id_parcours) {

        $requete = "SELECT ps.ordre, ps.fk_seance as id, s.intitule, s.type, s.nb_page FROM ff_parcours_seance as ps "
                . " LEFT JOIN ff_seance as s on ps.fk_seance = s.rowid "
                . " WHERE ps.fk_parcours = $id_parcours ORDER BY ps.ordre ASC";
        $resultat = $this->db->query($requete);
        return $resultat->result_array();
    }

    public function set_parcours_liste_seances($id_parcours, $liste_seances) {
        // si les seances  sont saisies sous forme 'n, n, n, n'
        // les seances doivent exister
        $this->load->model('seance_model');

        $erreur = 0;
        $info = '';
        $tab = explode(',', $liste_seances);
        $nb_seances = count($tab);
        trace('la liste ', $liste_seances);
        trace('tableau', $tab);
        foreach ($tab as $id_seance) {
            // le $id existe ??
            $seance = $this->seance_model->get_seance(intval($id_seance));
            if (is_null($seance)) {
                $info .= "ERREUR : La seance de numéro " . intval($id_seance) . " n'existe pas !<br>";
                $erreur = 1;
            }
        }
        if ($erreur) {
            return $info;
        }
        // sinon on crée
        $rang = 1;
        $this->parcours_model->supprimer_parcours_seance($id_parcours);
        foreach ($tab as $id_seance) {
            trace('--lien parcours seance :', intval($id_seance) . ', parcours :' . $id_parcours);
            $this->parcours_model->set_parcours_seance($id_parcours, intval($id_seance), $rang++);
        }
        //
        $this->db->where('rowid', $id_parcours);
        $this->db->update('parcours', array('nb_seance' => $nb_seances));
        return '';
    }

    public function get_parcours_liste_seances($id_parcours) {
        // renvoie liste des seances sous la forme 'n, n, n, n'
        $requete = "SELECT sp.fk_seance as id FROM ff_parcours_seance as sp "
                . " WHERE sp.fk_parcours = $id_parcours ORDER BY sp.ordre ASC";
        $resultat = $this->db->query($requete);
        $tab = $resultat->result_array();
        $liste = '';
        foreach ($tab as $value) {
            $liste .= $value['id'] . ', ';
        }
        return substr($liste, 0, -2);
    }

    public function get_ordre_seance($id_parcours, $id_seance) {

        $requete = "SELECT ps.ordre  FROM ff_parcours_seance as ps "
                . " LEFT JOIN ff_seance as s on ps.fk_seance = s.rowid "
                . " WHERE ps.fk_parcours = $id_parcours AND  ps.fk_seance = $id_seance";
        $resultat = $this->db->query($requete);

        $seance = $resultat->row();


        return $seance->ordre;
    }

    public function supprimer_parcours_seance($id_parcours) {
        $this->db->delete('parcours_seance', array('fk_parcours' => $id_parcours));
        // on reset le nb_seance du parcours à 0;
        $this->db->where('rowid', $id_parcours);
        $this->db->update('parcours', array('nb_seance' => 0));
    }

    public function personnes_parcours($id_parcours, $statut) {
        $where = "";
        if ($id_parcours !== 0) {
            $where = " AND PP.fk_parcours = $id_parcours";
        }

        $query = "SELECT PP.fk_parcours as id_parcours, PP.fk_personne as id_eleve, PP.fk_etat_suivi, PP.date_inscription, PP.date_fin, PA.intitule,  "
                . " CONCAT(pers.nom, ' ', pers.prenom) as eleve "
                . " FROM ff_personne_parcours as PP "
                . " LEFT JOIN ff_parcours as PA ON PP.fk_parcours = PA.rowid "
                . " LEFT JOIN ff_personne AS pers ON  PP.fk_personne = pers.rowid "
                . " WHERE fk_etat_suivi = $statut " . $where
                . " ORDER BY PA.ordre ASC, pers.nom ASC";
        $result = $this->db->query($query);
        return $result->result_array();
    }
    
    public function get_modele_excel($id_parcours) {
        $query = "SELECT p.modele_examen FROM ff_parcours as p "
                    . " WHERE p.rowid = $id_parcours";
        $result = $this->db->query($query);
        $parcours = $result->row_array();
        return $parcours['modele_examen'];
    }

}
