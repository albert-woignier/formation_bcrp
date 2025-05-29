<?php

defined('BASEPATH') OR exit('Accès direct au script interdit.');

class Personne extends CI_Controller {

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
        $this->load->model('personne_model');
        // $this->load->helper('url_helper');
    }

    public function add() {

        test_acces(R_ADMIN, TRUE);

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');

        $this->load->view('header');
        $this->load->view('menu');

            $nom = '';
            $prenom = '';
            $license = '';
            $mail = '';
            $phone = '';
            $categorie = '';


        $this->form_validation->set_rules('nom', 'Nom', 'required');
        $this->form_validation->set_rules('prenom', 'Prénom', 'required');
//        $this->form_validation->set_rules('mail', 'Mail', 'required');
        $this->form_validation->set_rules('categorie', 'Type de personne', 'required');
        $this->form_validation->set_message('required', 'Le champ : {field} doit être renseigné');

        if ($this->form_validation->run() === FALSE AND strlen(validation_errors()) > 0
                AND $this->input->post('valid')) {
            // erreurs !! on remet le formulaire de saisie
            $data = array(
                'fonction' => 'add',
                'commentaires' => 'Corriger les erreurs suivantes',
                'id' => 0,
                'nom_' => set_value('nom'),
                'prenom_' => set_value('prenom'),
                'license_' => set_value('license'),
                'mail_' => set_value('mail'),
                'phone_' => set_value('phone'),
                'statut_' => set_checkbox('statut', 'parti'),
                'categorie_e' => set_radio('categorie', 'eleve', TRUE),
                'categorie_m' => set_radio('categorie', 'moniteur'),
                'categorie_i' => set_radio('categorie', 'invite'),
                'categorie_a' => set_radio('categorie', 'administrateur'),
                'categorie_d' => set_radio('categorie', 'dev')
            );
            $this->load->view('form_personne', $data);
        } else {
            if ($this->input->post('valid')) {
                // OK on enregistre
                // si nom existe on sort
                if ($this->personne_model->existe($this->input->post('nom'),$this->input->post('nom'))) {
                    // la personne existe déjà
                    // afficher view générique
                    $data = array('titre' => 'Attention :',
                        'message' => "<strong>La personne " . $this->input->post('nom') . " " . $this->input->post('prenom') . " existe déjà !!</strong>",
                        'chaine' => "");
                    $this->load->view('ecran_message', $data);
                } else {
                // on cree un mdp
                    $mdp = $this->make_passw();
                    $tab = array(
                        'nom' => $this->input->post('nom'),
                        'prenom' => $this->input->post('nom'),
                        'license' => $this->input->post('license'),
                        'phone' => $this->input->post('phone'),
                        'mail' => $this->input->post('mail'),
                        'categorie' => $this->input->post('categorie'));

                    $tab['password'] = crypt($mdp, '$6$rounds=5000$yorky$');
                    
                    $this->personne_model->add($tab);
                    
                    // afficher view générique
                    $data = array('titre' => '',
                        'message' => "Les données de " . $this->input->post('nom') . " " . $this->input->post('prenom') . " ont été enregistrées",
                        'chaine' => "Le mot de passe de la personne est : <strong>$mdp</strong>");
                    $this->load->view('ecran_message', $data);
                }

            } else if ($this->input->post('annul')) {
                redirect('personne/liste');
            } else {
                // on affiche formulire vierge
                $data = array(
                    'fonction' => 'add',
                    'commentaires' => 'Saisir les informations.',
                    'id' => 0,
                    'nom_' => $nom,
                    'prenom_' => $prenom,
                    'license_' => $license,
                    'mail_' => $mail,
                    'phone_' => $phone,
                    'statut_' => $statut,
                    'categorie_e' => set_radio('categorie', 'eleve', ($categorie == 'apprenant') ? TRUE : FALSE),
                    'categorie_m' => set_radio('categorie', 'moniteur', ($categorie == 'moniteur') ? TRUE : FALSE),
                    'categorie_i' => set_radio('categorie', 'invite', ($categorie == 'invité') ? TRUE : FALSE),
                    'categorie_a' => set_radio('categorie', 'administrateur', ($categorie == 'administrateur') ? TRUE : FALSE),
                    'categorie_d' => set_radio('categorie', 'dev', ($categorie == 'dev') ? TRUE : FALSE)
                );
                $this->load->view('form_personne', $data);
            }
        }


        $this->load->view('footer');

        // $query = $this->personne_model->add_personne($array);
    }

    public function mod($id) {
        /*
         *
         */
        test_acces(R_ADMIN, TRUE);

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');

        $this->load->view('header');
        $this->load->view('menu');

            $personne = $this->personne_model->get_id($id);
            // trace('personne  id='.$id, $personne);
            $nom = $personne['nom'];
            $prenom = $personne['prenom'];
            $license = $personne['license'];
            $mail = $personne['mail'];
            $phone = $personne['phone'];
            $categorie = $personne['categorie'];
            $statut = $personne['statut'];
//            trace($nom . ' categorie', $categorie);


        $this->form_validation->set_rules('nom', 'Nom', 'required');
        $this->form_validation->set_rules('prenom', 'Prénom', 'required');
//        $this->form_validation->set_rules('mail', 'Mail', 'required');
        $this->form_validation->set_rules('categorie', 'Type de personne', 'required');
        $this->form_validation->set_message('required', 'Le champ : {field} doit être renseigné');

        if ($this->form_validation->run() === FALSE AND strlen(validation_errors()) > 0
                AND $this->input->post('valid')) {
            // erreurs !!
            trace('Personne mod erreur', 'set_checkbox = '.set_checkbox('statut', 'parti'));
            $data = array(
                'fonction' => 'mod',
                'commentaires' => 'Corriger les erreurs suivantes',
                'id' => $id,
                'nom_' => set_value('nom'),
                'prenom_' => set_value('prenom'),
                'license_' => set_value('license'),
                'mail_' => set_value('mail'),
                'phone_' => set_value('phone'),
                'statut_' => set_checkbox('statut', 'parti'),
                'mdp_'  => 0,
                'categorie_e' => set_radio('categorie', 'eleve', TRUE),
                'categorie_m' => set_radio('categorie', 'moniteur'),
                'categorie_i' => set_radio('categorie', 'invite'),
                'categorie_a' => set_radio('categorie', 'administrateur'),
                'categorie_d' => set_radio('categorie', 'dev')
            );
            $this->load->view('form_personne', $data);
        } else {
            if ($this->input->post('valid')) {
                // OK on enregistre les modifications
                // trace('personne on enregistre, id = ', $this->input->post('id'));

                if (intval($this->input->post('mdp')) === 1 ) {
                    trace('personne, on va générer mot de passe');
                    $mdp = $this->make_passw();
                } else {
                    $mdp = '';// on ne change pas le mdp
                }
                $tab = array(
                    'nom' => $this->input->post('nom'),
                    'prenom' => $this->input->post('prenom'),
                    'license' => $this->input->post('license'),
                    'phone' => $this->input->post('phone'),
                    'mail' => $this->input->post('mail'),
                    'categorie' => $this->input->post('categorie'));
                if ($mdp !== '') {
                    $tab['password'] = crypt($mdp, '$6$rounds=5000$yorky$');
                }
                trace('personne mod valid', 'input post = '.$this->input->post('statut'));
                if (($this->input->post('statut')) === 'parti' ) {
                    trace('personne, statut parti coché');
                    $tab['statut'] = 'parti';
                }

                trace('personne update, id = ', $this->input->post('id'));
                $this->personne_model->update($this->input->post('id'), $tab);

                if ($mdp !== '') {
                    // afficher view générique
                    $data = array('titre' => '',
                        'message' => "Les données de " . $this->input->post('nom') . " " . $this->input->post('prenom') . " ont été enregistrées",
                        'chaine' => "Le mot de passe de la personne est : <strong>$mdp</strong>");
                    $this->load->view('ecran_message', $data);
                } else {
                    redirect('personne/liste');
                }
            } else if ($this->input->post('annul')) {
                redirect('personne/liste');
            } else {
                // on affiche le formulaire
                $data = array(
                    'fonction' => 'mod',
                    'commentaires' => 'Saisir les informations.',
                    'id' => $id,
                    'nom_' => $nom,
                    'prenom_' => $prenom,
                    'license_' => $license,
                    'mail_' => $mail,
                    'phone_' => $phone,
                    'statut_' => $statut,
                    'categorie_e' => set_radio('categorie', 'eleve', ($categorie == 'apprenant') ? TRUE : FALSE),
                    'categorie_m' => set_radio('categorie', 'moniteur', ($categorie == 'moniteur') ? TRUE : FALSE),
                    'categorie_i' => set_radio('categorie', 'invite', ($categorie == 'invité') ? TRUE : FALSE),
                    'categorie_a' => set_radio('categorie', 'administrateur', ($categorie == 'administrateur') ? TRUE : FALSE),
                    'categorie_d' => set_radio('categorie', 'dev', ($categorie == 'dev') ? TRUE : FALSE)
                );
                $this->load->view('form_personne', $data);
            }
        }


        $this->load->view('footer');
    }

    public function liste() {

        $this->load->helper('my_html_helper');
        $this->load->helper('my_func_helper');
        $this->load->view('header');
        $this->load->view('menu');

        $liste = $this->personne_model->get_all();
        // on vérifie qui demande la lsite
        if (!is_ok(R_MON)) {
            // apprenant ou invité, ils ne voient que les noms et type
            foreach ($liste as $key => $personne) {
                if (!is_good_personne($personne['rowid'])) {
                    $liste[$key]['license'] = $liste[$key]['phone'] = $liste[$key]['mail'] = '---';
                }
            }
        }

        $data = array(
            'titre' => 'Liste des personnes',
            'commentaires' => '',
            'les_personnes' => $liste
        );

        $this->load->view('personne_liste', $data);

        $this->load->view('footer');
    }

    public function mes_infos() {

        $this->load->helper('my_html_helper');
        $this->load->helper('my_func_helper');
        $this->load->view('header');
        $this->load->view('menu');
        $list = array();
        $id = $this->session->userdata('user_id');
        $liste = $this->personne_model->get_id_and_moniteurs($id);
        // on vérifie qui demande la lsite
        $data = array(
            'titre' => 'Mes informations, mes parcours',
            'commentaires' => 'Pour toute erreur contactez l\'administrateur',
            'les_personnes' => $liste
        );

        $this->load->view('personne_liste', $data);

        $this->load->view('footer');
    }

    public function parcours_liste($id) {
        // liste les parcours suivis par la personne
        $this->load->model('suivi_model');
    }

    public function make_passw() {
        // on créé un password
        // construit un password de 8 caracteres
        test_acces(R_ADMIN, TRUE);
        $nbcar = 6;
        $list = "2345epwxpmz67fr89abctxxy4596321789yzzde1fhijabrpm92de6cvsop1475964mbeo96rnsfmwxz";
        mt_srand((double) microtime() * 1000000);
        $pssw = "";
        while (strlen($pssw) < $nbcar) {
            $pssw .= $list[mt_rand(0, strlen($list) - 1)];
        }
        return $pssw;
    }

}
