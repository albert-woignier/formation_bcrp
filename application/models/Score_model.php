<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Score_model
 *
 * @author albert
 */
class Score_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database($this->session->data_base_group);
// test_acces();
    }

    public function get_score($id_match) {
        $query = "SELECT competition, joueur_1, club_1, set_1, score_1, joueur_2, club_2, set_2, score_2 FROM `ff_score` WHERE rowid = ".$id_match;
        $result = $this->db->query($query);
        $data = array();
        return $result->row_array();
    }
    
    public function list_matchs() {
        // select liste des matchs du jour
        
    }

    public function new_match($joueur1, $joueur2, $date) {
        // crée un nouveau match, renvoie l'id du match
        $data = array(
            'joueur_1' => '', 
            'joueur_2' => '',
            'jour' => $date
        );
        return $this->db->insert('ff_score', $data);
    }
    
    public function set_joueurs($id_match, $competition, $joueur1, $club1, $score1, $set1, $joueur2, $club2, $score2,  $set2) {
        // utilisé pour permutter les joueurs au tableau affichage
        $data = array(
            'competition' => $competition,
            'joueur_1' => $joueur1,
            'club_1' => $club1,
            'joueur_2' => $joueur2,
            'club_2' => $club2,
            'score_1' => $score1,
            'score_2' => $score2,
            'set_1' => $set1,
            'set_2' => $set2
        );
        $this->db->where('rowid', $id_match);
        $this->db->update('ff_score', $data);
    }
    
      public function permute_joueurs($id_match) {
        // utilisé pour permutter les joueurs au tableau affichage
        $query = "SELECT `joueur_1`, 'club_1', `set_1`, `score_1`, `joueur_2`, 'club_2', `set_2`, `score_2` FROM `ff_score` WHERE rowid = ".$id_match;
        $result = $this->db->query($query);
        $data = array();
        $data = $result->row_array();
        $data_perm = array(
            'joueur_1' => $data['joueur_2'],
            'club_1' => $data['club_2'],
            'joueur_2' => $data['joueur_1'],
            'club_2' => $data['club_1'],
            'score_1' => $data['score_2'],
            'score_2' => $data['score_1'],
            'set_1' => $data['set_2'],
            'set_2' => $data['set_1']
        );
        $this->db->where('rowid', $id_match);
        $this->db->update('ff_score', $data_perm);
    }  

    public function add_points($id_match, $numero_joueur, $nb_points) {
        // ajoute ou soustrait un nombre de points
        //$numero_joueur = 1 ou 2
        $col_score = 'score_'.$numero_joueur;
        $query = "UPDATE `ff_score` SET $col_score = $col_score + $nb_points WHERE rowid = ".$id_match ;
        $this->db->query($query);
    }
    
     public function add_set($id_match, $id_joueur, $nb_set) {
        // ajoute ou soustrait un point de set
        $col_set = 'set_'.$numero_joueur;
        $query = "UPDATE `ff_score` SET $col_set = $col_set + $nb_set WHERE rowid = ".$id_match ;
        $this->db->query($query);
    }
    
    public function set_texte_defil($texte) {
        $query = "UPDATE `ff_score_defil` SET texte = '$texte' WHERE rowid = 1" ;
        $this->db->query($query);
    }
    public function get_texte_defil() {
        $query = "SELECT `texte` FROM `ff_score_defil` WHERE rowid = 1";
        $result = $this->db->query($query);
        $data = $result->row_array();
        return $data['texte'];
    }    


}
