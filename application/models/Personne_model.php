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
class Personne_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database($this->session->data_base_group);
    }

    public function get_all($categorie = '') {
        if ($categorie == '') {
            $query = "SELECT rowid, nom, prenom, license, phone, mail, categorie, statut, "
                    . "CONCAT(SUBSTRING(prenom, 1,1), SUBSTRING(nom, 1,1)) as initiales "
                    . " FROM ff_personne"
                    . " ORDER BY categorie ASC, nom ASC, prenom ASC";
        } else {
            $query = "SELECT rowid, nom, prenom, license, phone, mail, categorie, statut, "
                    . "CONCAT(SUBSTRING(prenom, 1,1), SUBSTRING(nom, 1,1)) as initiales "
                    . " FROM ff_personne WHERE categorie = '$categorie' "
                    . " ORDER BY nom ASC, prenom ASC";
        }

        $result = $this->db->query($query);

        return $result->result_array();

//        foreach ($result->result_array() as $row) {
//            $data[] = $row;
//        }
//        return $data;
    }

    public function get_id($id) {
        // uniquement 1 personne
        $query = "SELECT rowid, nom, prenom, license, phone, mail, categorie, statut, "
                . "CONCAT(SUBSTRING(prenom, 1,1), SUBSTRING(nom, 1,1)) as initiales "
                . " FROM ff_personne WHERE rowid = $id ";
        $result = $this->db->query($query);
        return $result->row_array();
    }
    
    public function get_id_and_moniteurs($id) {
        // une personne et tous les moniteurs
        $categorie = R_MON;
        $query = "SELECT rowid, nom, prenom, license, phone, mail, categorie, statut, "
                . "CONCAT(SUBSTRING(prenom, 1,1), SUBSTRING(nom, 1,1)) as initiales "
                . " FROM ff_personne "
                . "WHERE rowid = $id OR categorie =  '$categorie' "
                . " ORDER BY categorie ASC, nom ASC, prenom ASC";
        $result = $this->db->query($query);
        return $result->result_array();
    }

    public function existe($nom, $prenom) {
        $query = "SELECT rowid, nom, prenom, license, phone, mail, categorie, statut, "
                . "CONCAT(SUBSTRING(prenom, 1,1), SUBSTRING(nom, 1,1)) as initiales "
                . "FROM ff_personne WHERE nom = '$nom' AND prenom = '$prenom' ";
        $row = $this->db->query($query)->row();
        if (isset($row)) {
            return $row->rowid;
        } else {
            return FALSE;
        }
    }

    public function good_psswd($nom, $password) {

        $psswd_crypted = crypt($password, '$6$rounds=5000$yorky$');
        $query = "SELECT rowid "
                . "FROM ff_personne WHERE nom = '$nom' AND password = '$psswd_crypted'";
        $row = $this->db->query($query)->row();
        if (isset($row)) {
            return $row->rowid;
        } else {
            return FALSE;
        }
    }

    public function add($table) {
        trace('add personne input ', $table);
        $query = $this->db->insert_string('ff_personne', $table);
        trace('add personne query ', $query);
        $result = $this->db->query($query);
        trace('add personne result ', $result);
        if ($result) {
            return $this->db->insert_id();
        } else {
            trace('erreur add personne', $this->db->error());
            return FALSE;
        }
    }

    public function update($id, $table) {
        // $table (array('nom'=>'trucmuche', 'prenom'=>'zozo', etc
        $where = "rowid = $id";
        $query = $this->db->update_string('ff_personne', $table, $where);

        $result = $this->db->query($query);
        if ($result) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
