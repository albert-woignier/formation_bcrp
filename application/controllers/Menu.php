<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends CI_Controller {

    /**
     * Cette classe affiche une page générique avec un message d'attente
     */
    
    public function __construct() {
        parent::__construct();
        test_acces();
    }
    
    public function admin() {
        $this->load->view('header');
        $this->load->view('menu');

        $this->load->view('menu_admin');
        $this->load->view('footer');
    }

}
