<?php

defined('BASEPATH') OR exit('Accès direct au script interdit.');

class Page extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     * 	- or -
     * 		http://example.com/index.php/welcome/index
     * 	- or -
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/user_guide/general/urls.html
     */
    public function __construct() {
        parent::__construct();
        test_acces();
        $this->load->model('page_model');
        $this->load->helper('traceur');
        test_acces();
    }

    public function add($id = 0) {

        test_acces(R_ADMIN, TRUE);

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');

        $this->load->view('header_tiny');
        $this->load->view('menu');

        if (intval($id) !== 0) {
            // on modifie
            $page = $this->page_model->get($id);
            // trace('page id=' . $id, $page);
            $intit = $page['intitule'];

            $contenu = html_entity_decode($page['contenu'], ENT_HTML5, 'UTF-8');
        } else {
            // on ajoute

            $contenu = 'Saisir ici le contenu';
            $intit = '';
        }


        $this->form_validation->set_rules('intitule', 'Intitulé de la page', 'required');
        $this->form_validation->set_rules('contenu', 'Contenu de la page', 'required');
        // $this->form_validation->set_rules('parcours', 'Parcours principal de la page', 'required');


        $this->form_validation->set_message('required', 'Le champ {field} doit être renseigné');

        if ($this->form_validation->run() === FALSE AND strlen(validation_errors()) > 0
                AND $this->input->post('valid')) {
            // erreurs !!
            $data = array(
                'commentaires' => 'Corriger les erreurs suivantes',
                'id' => $id,
                'intitule' => set_value('intitule'),
                'contenu' => set_value('contenu')
            );
            $this->load->view('form_page', $data);
        } else {
            if ($this->input->post('valid')) {
                // OK on enregistre

                $tab = array(
                    'intitule' => $this->input->post('intitule'),
                    'contenu' => $this->input->post('contenu')
                );

//                trace('Page', 'on a validé, id = ' . $this->input->post('id'));

                if (intval($this->input->post('id')) !== 0) {
                    // trace("page ctrl", 'on va vers update');
                    $this->page_model->update($this->input->post('id'), $tab);
                } else {
                    // trace("page ctrl", 'on va vers add');

                    $this->page_model->add($tab);
                }
                trace('enregistre , on retourne vers : ', $this->session->previous_url);
                redirect($this->session->previous_url);

//                $this->load->helper('my_html_helper');
//                $str = replace_page_tags($this->input->post('contenu'));
//                $data = array('titre' => '',
//                    'message' => 'La page a bien été enregistrée.',
//                    'chaine' => $str);
//                $this->load->view('ecran_message', $data);
            } else if ($this->input->post('annul')) {
                trace('annule , on retourne vers : ', $this->session->previous_url);
                redirect($this->session->previous_url);
//                $data = array('titre' => '',
//                    'message' => 'Votre saisie a bien été annulée.');
//                $this->load->view('ecran_message', $data);
            } else {
                // TODO les données du rowid et/
                // PKOI SET_VALUE AU 1ER APPEL ????
                $data = array(
                    'commentaires' => 'Saisir les informations.',
                    'id' => $id,
                    'intitule' => $intit,
                    'contenu' => $contenu
                );
                $this->load->view('form_page', $data);
            }
        }


        $this->load->view('footer');
    }

    public function mod($id) {
        /*         * *

         */
    }

    public function liste() {

        test_acces(R_ADMIN, TRUE);
        $this->load->view('header');
        $this->load->view('menu');

        $pages = $this->page_model->liste();
        $data = array(
            'nb_pages' => count($pages),
            'titre' => 'Liste des pages',
            'commentaires' => '',
            'pages' => $pages
        );

        $this->load->view('pages_liste', $data);
        $this->load->view('footer');
    }

    public function voir($id) {


        $this->load->view('header');
        //$this->load->view('menu');

        $page = $this->page_model->get($id);

        // on traite les images videos et pdf entre [ ]

        $this->load->helper('my_html_helper');
        $str = replace_page_tags($page['contenu']);

        $data = array(
            'titre' => $page['intitule'],
            'commentaires' => '',
            'contenu' => $str
        );

        $this->load->view('page_apercu', $data);
        $this->load->view('footer');
    }
    
    public function test_pocket() {
        
        $this->load->view('header');
        $this->load->view('menu');

        $this->load->view('form_test_poche');
        $this->load->view('footer');
        
        
    }
}
