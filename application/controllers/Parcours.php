<?php

defined('BASEPATH') OR exit('Accès direct au script interdit.');

class Parcours extends CI_Controller {

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
        $this->load->model('parcours_model');
        // $this->load->helper('url_helper');
    }

    public function liste() {

        $query = $this->parcours_model->get_parcours();

        $data = array(
            'commentaires' => '',
            'les_parcours' => $query
        );

        $this->load->view('header');
        $this->load->view('menu');

        $this->load->view('parcours_liste', $data);

        $this->load->view('footer');
    }

    public function select($what_for = '') {
        //  on sélectionne un parcours
        // et on aiguille en fct de $whatfor
        $this->load->view('header');
        $this->load->view('menu');
//        trace('parcours select what for  = ' . $what_for);
        if ($what_for == 'build') {
            $query = $this->parcours_model->get_parcours();

            $data = array(
                'titre' => 'Parcours',
                'message' => 'Sélectionner le parcours à constituer',
                'action' => site_url('parcours_build/import/'),
                'les_parcours' => $query);

            $this->load->view('form_parcours_select', $data);



            $this->load->view('footer');
        } else {
            $this->parser->parse('ecran_message', array('titre' => 'Erreur du programme parcours/select',
                'message' => 'Action inconnue'));

            $this->load->view('footer');
        }
    }

    public function lister_seances($id) {

        $parcours = $this->parcours_model->get_name($id);

        $les_seances = $this->parcours_model->get_parcours_seance($id);
        $nb_seances = count($les_seances);

        $data = array(
            'commentaires' => '',
            'les_seances' => $les_seances,
            'parcours' => $parcours,
            'nb_seances' => $nb_seances
        );

        $this->load->view('header');
        $this->load->view('menu');

        $this->load->view('parcours_seances', $data);

        $this->load->view('footer');
    }

    public function add($id = 0) {

        test_acces(R_ADMIN, TRUE);

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');

        $this->load->view('header');
        $this->load->view('menu');

        if (intval($id) !== 0) {
            // on modifie
            $parcours = $this->parcours_model->get_parcours($id);
//            trace('parcours id=' . $id, $parcours);
            $disc = $parcours['fk_discipline'];
            $niv = $parcours['fk_niveau'];
            $intit = $parcours['intitule'];
            $modele_examen = $parcours['modele_examen'];
        } else {
            $disc = 1;
            $niv = 1;
            $intit = '';
            $modele_examen = '';
        }

        $this->form_validation->set_rules('intitule', 'Intitulé', 'required');
        $this->form_validation->set_message('required', 'Le champ : {field} doit être renseigné');

        if ($this->form_validation->run() === FALSE AND strlen(validation_errors()) > 0
                AND $this->input->post('valid')) {
            // erreurs !!
            $liste_disciplines = form_dropdown('discipline', $this->parcours_model->get_disciplines(), set_value('discipline'));
            $liste_niveaux = form_dropdown('niveau', $this->parcours_model->get_niveaux(), set_value('niveau'));
            $data = array(
                'commentaires' => 'Corriger les erreurs suivantes',
                'id' => $id,
                'intitule' => set_value('intitule'),
                'modele_examen' => $modele_examen,
                'list_box_disciplines' => $liste_disciplines,
                'list_box_niveaux' => $liste_niveaux
            );


            $this->load->view('form_parcours', $data);
        } else {
            if ($this->input->post('valid')) {
                // OK on enregistre
                // si nom existe on sort

                $tab = array(
                    'intitule' => $this->input->post('intitule'),
                    'fk_discipline' => $this->input->post('discipline'),
                    'fk_niveau' => $this->input->post('niveau')
                );
                if ($this->input->post('id') !== '0') {
//                    trace("on modifie id = " . $this->input->post('id'), $tab);
                    $this->parcours_model->update($this->input->post('id'), $tab);
                } else {
//                    trace("on ajoute id = " . $this->input->post('id'), $tab);
                    $this->parcours_model->add($tab);
                }

                redirect('parcours/liste');
            } else if ($this->input->post('annul')) {
                redirect('parcours/liste');
            } else {

                $liste_disciplines = form_dropdown('discipline', $this->parcours_model->get_disciplines(), $disc);
                $liste_niveaux = form_dropdown('niveau', $this->parcours_model->get_niveaux(), $niv);
                $data = array(
                    'commentaires' => 'Saisir les informations.',
                    'id' => $id,
                    'intitule' => $intit,
                    'modele_examen' => $modele_examen,
                    'list_box_disciplines' => $liste_disciplines,
                    'list_box_niveaux' => $liste_niveaux
                );
                $this->load->view('form_parcours', $data);
            }
        }


        $this->load->view('footer');
    }

    public function liste_pers_par($statut) {
        test_acces();
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->helper('my_html_helper');
        $this->load->view('header');
        $this->load->view('menu');

        // liste des parcours
        //
        $this->load->model('parcours_model');
        $parcours = $this->parcours_model->get_id_name();
        //
        // liste des personnes à succès
        $eleves = $this->parcours_model->personnes_parcours(0, $statut);
        if ($statut == SEANCE_PARCOURS_VALIDE) {
            $titre = 'Les apprenants diplômés';
        } else {
            $titre = 'Les apprenants en cours de parcours ';
        }
        $data = array(
            'titre' => $titre,
            'commentaires' => '',
            'les_parcours' => $parcours,
            'les_eleves' => $eleves
        );
        $this->load->view('personne_parcours', $data);

        $this->load->view('footer');
    }

}
