<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Generic extends CI_Controller {

    /**
     * Cette classe affiche une page générique avec un message d'attente
     */
    public function __construct() {
        parent::__construct();
        test_acces();
    }

    public function index() {
        $this->load->view('header');
        $this->load->view('menu');
        // afficher view générique
        $data = array('titre' => '', 'commentaires' => '');
        $this->load->view('generic_page', $data);
        $this->load->view('footer');
    }

}
