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
class Login_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function good_psswd($nom, $password) {

        $id = FALSE;
        $bd = '';
        //on teste si username + password correspondent à la bd de test (test) puis à la bd officielle (bcrp)
        $sgbd = array('bcrp', 'test');
        // $sgbd = array('bcrp');
        $psswd_crypted = crypt($password, '$6$rounds=5000$yorky$');
        $query = "SELECT rowid "
                . "FROM ff_personne WHERE nom = '$nom' AND password = '$psswd_crypted'";
        log_message('debug', "good_psswd - requete : $query");
        foreach ($sgbd as $bd_group) {
            log_message('debug', "good_psswd - load bd $bd_group");
            $this->load->database($bd_group);
            log_message('debug', "good_psswd - load database OK");
            $row = $this->db->query($query)->row();
            if (isset($row)) {
                log_message('debug', "good_psswd - bd $bd_group");
                $id = $row->rowid;
                $bd = $bd_group;
                $this->session->set_userdata('data_base_group', $bd_group);
                $data = array(
                    'fk_personne' => $id,
                    'date_in' => date("Y-m-d H:i:s"),
                    'ip_address' => $_SERVER["REMOTE_ADDR"],
                    'base' => $bd_group
                );
                $this->db->insert('ff_login', $data);
                $this->session->set_userdata('login_id', $this->db->insert_id());
                break;
            }
            $this->db->close();
        }
        // passedroit pour accéder aux fonctions de scoring sur base de test
        if (!$id) {
            if ($nom == 's' AND $password == 's') {
                $id = 1;
                $this->session->set_userdata('data_base_group', 'test');
            } else {
                // pas le bon user/mdp
                log_message('debug', "good_psswd - PAS BON USER/MDP !!!");
            }
        }
        return $id;
    }

    public function login_list($qui) {
        // les login de la table ff_login par dates décroissante
        $this->load->database($this->session->data_base_group);
        $where ='';
        if ($qui == 'apprenants') {
            $where = " WHERE P.categorie = 'apprenant' ";
        }
        $query = "SELECT CONCAT(P.nom, ' ', P.prenom) as nom, P.categorie as cat, date_in, ip_address, base, winsize "
                . "FROM ff_login "
                . "LEFT JOIN ff_personne as P ON fk_personne = P.rowid "
                . $where
                . "ORDER BY date_in DESC";
        $result = $this->db->query($query);

        return $result->result_array();
    }

    public function save_winsize($id_login, $winsize) {
        $this->load->database($this->session->data_base_group);
        $where = "rowid = $id_login";
        $query = $this->db->update_string('ff_login', array('winsize' => $winsize), $where);
        $result = $this->db->query($query);
        if ($result) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
