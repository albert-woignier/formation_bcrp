<?php

defined('BASEPATH') OR exit('Accès direct au script interdit.');

class Trace extends CI_Controller {

    /**
     * Gestionnaire des fichiers de trace
     */
    public function __construct() {
        parent::__construct();
        test_acces();
        $this->load->helper('traceur');
        $this->load->model('sgbd_model');
    }

    public function show($trace = 'trace') {

        if ($trace === 'trace') {
            $fichier = APPPATH . 'logs/' . 'trace.php';
        } else {
            $fichier = APPPATH . 'logs/log-' . date('Y-m-d') . '.php';
        }

        $this->load->view('header');
        $this->load->view('menu');
        $chaine = file_exists($fichier) ? file_get_contents($fichier) : "Le fichier n'existe pas";
        $data = array('titre' => 'fichier ' . $fichier,
            'message' => '',
            'chaine' => nl2br($chaine));
        $this->load->view('ecran_message', $data);
        $this->load->view('footer');
    }

    public function delete($trace = 'trace') {

        $this->load->view('header');
        $this->load->view('menu');
        if (file_exists(APPPATH . 'logs/trace.php')) {
            unlink(APPPATH . 'logs/trace.php');
        }
        if (file_exists(APPPATH . 'logs/log-' . date('Y-m-d') . '.php')) {
            unlink(APPPATH . 'logs/log-' . date('Y-m-d') . '.php');
        }
        $data = array('titre' => 'Les fichiers Trace et Log ont été supprimés',
            'message' => '');
        $this->load->view('ecran_message', $data);
        $this->load->view('footer');
    }

    public function show_tables() {
        $this->load->view('header');
        $this->load->view('menu');
        $tables = $this->sgbd_model->liste_tables();

        $data = array('affichage' => 'Tables',
            'titre' => 'Les tables de la base de données',
            'commentaires' => '',
            'objets' => $tables);
        $this->load->view('ecran_sgbd', $data);
        $this->load->view('footer');
    }

    public function show_champs($table_name) {
        $this->load->view('header');
        $this->load->view('menu');
        $fields = $this->sgbd_model->liste_champs($table_name);

        $data = array('affichage' => 'Champs',
            'titre' => 'Les champs de la table ' . $table_name,
            'commentaires' => '',
            'objets' => $fields);
        $this->load->view('ecran_sgbd', $data);
        $this->load->view('footer');
    }

    public function show_data($table_name) {
        $this->load->view('header');
        $this->load->view('menu');
        $tableau = $this->sgbd_model->dump_table($table_name);
        $this->load->view('ecran_message', array('tableau' => $tableau, 'titre' => 'Les lignes de la table ' . $table_name));
        $this->load->view('footer');
    }

    public function globales() {
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('ecran_message', array('tableau' => $_SESSION));
        $this->load->view('footer');
    }

    public function aw_modif() {
//        $requete = "update ff_personne_suivi set validation = 0 WHERE  date_seance = '2020-12-15'";
//        $this->db->query($requete);
    }

}
