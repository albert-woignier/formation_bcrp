<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Page_model
 *
 * @author albert
 */
class Page_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database($this->session->data_base_group);
        // test_acces();
    }

    public function get($id) {
        $query = "SELECT rowid, intitule, contenu FROM ff_page WHERE rowid = $id ";
        $row = $this->db->query($query)->row_array();
        return $row;
    }

    public function add($page) {
        // trace('Page_model', 'on rentre dans add');
        $query = $this->db->insert_string('ff_page', $page);
        $result = $this->db->query($query);
        if ($result) {
            return $this->db->insert_id();
        } else {
            // trace('Page_model', 'erreur  insert !! '.$result);
            return FALSE;
        }
    }

    public function update($id, $page) {

//        trace('Page_model', 'on rentre dans update');
        $where = "rowid = $id";
        $query = $this->db->update_string('ff_page', $page, $where);

        $result = $this->db->query($query);
        if ($result) {
            return TRUE;
        } else {
//            trace('Page_model', 'erreur update !! ' . $result);
            return FALSE;
        }
    }

    public function liste($type = '') {
        $query = "SELECT p.rowid, p.intitule, LENGTH(contenu) as size FROM ff_page as p "
                . "ORDER BY p.intitule ASC";
        $result = $this->db->query($query);

        return $result->result_array();
    }

    public function get_id_by_name($name) {
        // ici on retourne une page
        $query = $this->db->get_where('page', array('intitule' => $name));
        $page = $query->row_array();
        if (isset($page['rowid'])) {
            return $page['rowid'];
        } else {
            return NULL;
        }
    }

    public function get_name($id) {
        $query = "SELECT intitule FROM ff_page WHERE rowid = $id";
        $result = $this->db->query($query);

        $page = $result->row();

        if (isset($page)) {
            return $page->intitule;
        }
        return NULL;
    }

}
