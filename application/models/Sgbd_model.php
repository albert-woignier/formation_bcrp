<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Agenda_model
 *
 * @author albert
 */
class Sgbd_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        // connecte à la base
        $this->load->database($this->session->data_base_group);
    }

    public function liste_tables() {

        return $this->db->list_tables();
    }

    public function liste_champs($nom_table) {

        return $this->db->list_fields($nom_table);
    }

    public function dump_table($nom_table) {
        $query = $this->db->query("SELECT * FROM $nom_table");
        return $query->result_array();
    }

    public function requette($query) {

    }

}
