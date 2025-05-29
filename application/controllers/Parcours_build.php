<?php

defined('BASEPATH') OR exit('Accès direct au script interdit.');

class Parcours_build extends CI_Controller {

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
    }

    public function import($id_parcours) {

        // upload d'un fichier EXCEL, lecture, et affichage du tableau avec analyse erreurs, et demande de validation
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');

        $this->load->view('header');
        $this->load->view('menu');

        $config['upload_path'] = FCPATH . 'media/xls';
        $config['allowed_types'] = 'xls|xlsx';
        $config['max_size'] = 2048;
        $config['max_width'] = 1024;
        $config['max_height'] = 1024;

        $this->load->library('upload', $config);
        // trace("Files", $_FILES['choosen_file']);
        if (!$this->upload->do_upload('choosen_file')) {
            $data = array('erreurs' => $this->upload->display_errors(),
                'titre' => 'Sélectionner le fichier EXCEL a envoyer sur le serveur',
                'message' => 'Fichier de type xls ou xlsx de taille inférieure à 2 MO',
                'action_link' => 'parcours_build/import/' . $id_parcours);

            $this->load->view('form_upload', $data);
        } else {

            // le fichier a été importé
            $excel_file = $this->upload->data('file_name');
            // on va vers exploitation
            $erreur = 0;
            $seances = array();
            $seances_to_create = array();
            $this->load->helper('my_excel_helper');
            $filename = FCPATH . 'media/xls/' . $excel_file;
            $tab = read_excel($filename);
            $this->load->model('seance_model');
            $premiere_ligne = TRUE;
            foreach ($tab[1] as $ligne) {
                if ($premiere_ligne) {
                    // on saute 1ère ligne en-tête.
                    $premiere_ligne = FALSE;
                    continue;
                }
                $info = 'Ok';
                $id = $ligne['A'];
                $seance_name = $ligne['B'];
                if ($id == '' AND $seance_name == '') {
                    // on ignore
                    continue;
                }
                if ($id == '') {
                    // il faut créer une séance vide de nom  seance_name
                    // voyons si pas déjà dans la liste des à créer
                    if (!in_array($seance_name, $seances_to_create)) {
                        // voyons si le nom existe déjà en base des séances
                        // voyons si cette page existe
                        $idx = $this->seance_model->get_id_by_name($seance_name);
                        if ($idx) {
                            $id = $idx;
                            $info = "La séance <strong> $seance_name </strong>existe déjà sous le numéro $id";
                        } else {
                            $seances_to_create[] = $seance_name;
                            $info = "La séance<strong> $seance_name </strong>sera créée";
                            $id = 'xx';
                        }
                    } else {
                        $info = "La séance <strong>$seance_name </strong>est en double";
                    }
                } else {
                    // le $id existe ??
                    $seance = $this->seance_model->get_seance($id);
                    if (is_null($seance)) {
                        $info = "ERREUR : La séance de numéro $id n'existe pas !";
                        $erreur = 1;
                    } else {
                        $seance_name = $seance['intitule'];
                    }
                }

                $seances[] = array('id' => $id, 'seance_name' => $seance_name, 'info' => $info);
            }

            $message = '';
            if ($erreur == 1) {
                $message = '<div class="alert alert-danger" style="margin-bottom:0;" role="alert">' .
                        'Le fichier EXCEL est en erreur. Corriger celui-ci et recommencez.' . '</div>';
            }

            // on passe en input hidden le tableau
            $this->load->model('parcours_model');
            $data = array('seances' => $seances,
                'tab_seances' => serialize($seances),
                'commentaires' => $message,
                'erreur' => $erreur,
                'parcours' => $this->parcours_model->get_name($id_parcours),
                'id_parcours' => $id_parcours);
            $this->load->view('form_parcours_build', $data);
        }

        $this->load->view('footer');
    }

    public function validation_import() {

        $this->load->model('seance_model');
        $this->load->model('parcours_model');
        // on récupère le tableau
        if ($this->input->post('valid')) {
            $id_parcours = $this->input->post('id_parcours');
            $liste_seances = unserialize($this->input->post('tab_seances'));
            $nb_seance = count($liste_seances);
            // tout d'abors on supprime la liste de séances associées au parcours
            $this->parcours_model->supprimer_parcours_seance($id_parcours);
            $rang = 1;
            foreach ($liste_seances as $seance) {
                if ($seance['id'] == 'xx') {
                    // il faut créer une séance vide de nom  seance_name
//                    trace('--création séance', $seance['seance_name']);
                    $data = array('intitule' => $seance['seance_name']);
                    $id_seance = $this->seance_model->add($data);
//                    trace('--lien seance parcours', $seance['id'] . ' parcours ' . $id_parcours);
                    $this->parcours_model->set_parcours_seance($id_parcours, $id_seance, $rang++);
                } else if ($seance['id'] != '') {
                    // le $id existe !!
//                    trace('--lien seance parcours', $seance['id'] . ' parcours ' . $id_parcours);
                    $this->parcours_model->set_parcours_seance($id_parcours, $seance['id'], $rang++);
                } else {
                    // séance déjà crée ... on récupère rowid par son nom
//                    trace('--lien seance parcours', $seance['seance_name'] . ' parcours ' . $id_parcours);
                    $id_seance = $this->seance_model->get_id_by_name($seance['seance_name']);
                    $this->parcours_model->set_parcours_seance($id_parcours, $id_seance, $rang++);
                }
            }
            $this->parcours_model->set_parcours_nb_seance($id_parcours, $nb_seance);

            $chaine = '<div class="alert alert-primary" style="margin-bottom:0;" role="alert">L\'opération est terminée avec succès.</div>';
        } else if ($this->input->post('annul')) {
            $chaine = '<div class="alert alert-danger" style="margin-bottom:0;" role="alert">L\'opération a été annulée.</div>';
        } else {
            $chaine = '<div class="alert alert-danger" style="margin-bottom:0;" role="alert">Accès interdit</div>';
        }


        $this->load->view('header');
        $this->load->view('menu');
        $data = array('chaine' => $chaine);
        $this->load->view('ecran_message', $data);
        $this->load->view('footer');
    }

    public function build_from_list($id_parcours) {
        // construction parcours à partir liste de rowid de seances
        test_acces(R_ADMIN, TRUE);

        $this->load->model('seance_model');
        $this->load->model('parcours_model');

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');

        $this->load->view('header');
        $this->load->view('menu');

        $parcours = $this->parcours_model->get_parcours($id_parcours);
        $titre = "Construire le parcours " . $parcours['intitule'];
        $commentaires = '';
        $objets = 'seances';
        $id = $id_parcours;
        $link = 'parcours_build/build_from_list/' . $id;
        $liste_initiale = $this->parcours_model->get_parcours_liste_seances($id_parcours);


        $this->form_validation->set_rules('liste', 'Liste', 'required');
        $this->form_validation->set_message('required', 'Le champ : {field} doit être renseigné');

        if ($this->form_validation->run() === FALSE AND strlen(validation_errors()) > 0
                AND $this->input->post('valid')) {
            // erreurs !!
            $data = array(
                'commentaires' => 'Corriger les erreurs suivantes',
                'id' => $id,
                'titre' => $titre,
                'liste' => set_value('liste'),
                'link' => $link,
                'objets' => $objets
            );
            $this->load->view('form_liste_id', $data);
        } else {
            if ($this->input->post('valid')) {
                // OK on enregistre
                $liste = $this->input->post('liste');
                $retour = $this->parcours_model->set_parcours_liste_seances($id_parcours, $liste);
                if ($retour === '') {
                    // tout OK
                    redirect('parcours/lister_seances/' . $id_parcours);
                } else {
                    //
                    $data = array(
                        'commentaires' => $retour,
                        'id' => $id,
                        'titre' => $titre,
                        'liste' => set_value('liste'),
                        'link' => $link,
                        'objets' => $objets
                    );
                    $this->load->view('form_liste_id', $data);
                }
            } else if ($this->input->post('annul')) {
                redirect('parcours/liste');
            } else {
                // TODO les données du rowid et/
                $data = array(
                    'commentaires' => $commentaires,
                    'id' => $id,
                    'titre' => $titre,
                    'liste' => $liste_initiale,
                    'link' => $link,
                    'objets' => $objets
                );
                $this->load->view('form_liste_id', $data);
            }
        }


        $this->load->view('footer');
    }
    
    public function set_modele_examen($id_parcours) {
        // 
        // importe le fichier excel modele pour les examens
        //
       // upload d'un fichier EXCEL, lecture, et affichage du tableau avec analyse erreurs, et demande de validation
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');

        $this->load->view('header');
        $this->load->view('menu');

        $config['upload_path'] = FCPATH . 'media/modeles_examen';
        $config['allowed_types'] = 'xls|xlsx';
        $config['max_size'] = 2048;
        $config['max_width'] = 1024;
        $config['max_height'] = 1024;

        $this->load->library('upload', $config);
        // trace("Files", $_FILES['choosen_file']);
        if (!$this->upload->do_upload('choosen_file')) {
            if ($this->input->post('annul')) {
                redirect('parcours/liste');
                exit();
            }
            $data = array('erreurs' => $this->upload->display_errors(),
                'titre' => 'Sélectionner le modele EXCEL d\'examen à  associer au parcours',
                'message' => 'Fichier de type xls ou xlsx de taille inférieure à 2 MO',
                'action_link' => 'parcours_build/set_modele_examen/' . $id_parcours);

            $this->load->view('form_upload', $data);
        } else {

            // le fichier a été importé 
            // on met à jour recors du parcours
            
            $excel_file = $this->upload->data('file_name');
            $this->parcours_model->update($id_parcours, array('modele_examen' =>$excel_file));
            redirect('parcours/liste');
                exit();
        }

        $this->load->view('footer');
        
    }

}
