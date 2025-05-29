<?php

defined('BASEPATH') OR exit('Accès direct au script interdit.');

class Seance_build extends CI_Controller {

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
    }

    public function import($id_seance) {

        // upload d'un fichier EXCEL, lecture, et affichage du tableau avec analyse erreurs, et demande de validation

        test_acces(R_ADMIN, TRUE);

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
            trace('retour formulaire', $this->input->post(NULL, TRUE));
            if ($this->input->post('annul')) {
                redirect($this->session->previous_url);
                exit();
            }
            $data = array('erreurs' => $this->upload->display_errors(),
                'titre' => 'Sélectionner le fichier EXCEL a envoyer sur le serveur',
                'message' => 'Fichier de type xls ou xlsx de taille inférieure à 2 MO',
                'action_link' => 'seance_build/import/' . $id_seance);

            $this->load->view('form_upload', $data);
        } else {

            // le fichier a été importé
            $excel_file = $this->upload->data('file_name');
            // on va vers exploitation
            $erreur = 0;
            $pages = array();
            $pages_to_create = array();
            $this->load->helper('my_excel_helper');
            $filename = FCPATH . 'media/xls/' . $excel_file;
            $tab = read_excel($filename);
            $this->load->model('page_model');
            $premiere_ligne = TRUE;
            foreach ($tab[1] as $ligne) {
                if ($premiere_ligne) {
                    // on saute 1ère ligne en-tête.
                    $premiere_ligne = FALSE;
                    continue;
                }
                $info = 'Ok';
                $id = $ligne['A'];
                $page_name = $ligne['B'];
                if ($id == '' AND $page_name == '') {
                    // on ignore
                    continue;
                }
                if ($id == '') {
                    // il faut créer une page vide de nom  page_name
                    // voyons si pas déjà dans la liste des à créer
                    if (!in_array($page_name, $pages_to_create)) {
                        // voyons si cette page existe
                        $idx = $this->page_model->get_id_by_name($page_name);
                        if ($idx) {
                            $id = $idx;
                            $info = "La page<strong> $page_name </strong>existe déjà sous le numéro $id";
                        } else {
                            $pages_to_create[] = $page_name;
                            $info = "La page<strong> $page_name </strong>sera créée";
                            $id = 'xx';
                        }
                    } else {
                        $info = "La page <strong>$page_name </strong>est en double";
                    }
                } else {
                    // le $id existe ??
                    $page = $this->page_model->get($id);
                    if (is_null($page)) {
                        $info = "ERREUR : La page de numéro $id n'existe pas !";
                        $erreur = 1;
                    } else {
                        $page_name = $page['intitule'];
                    }
                }

                $pages[] = array('id' => $id, 'page_name' => $page_name, 'info' => $info);
            }

            $message = '';
            if ($erreur == 1) {
                $message = '<div class="alert alert-danger" style="margin-bottom:0;" role="alert">' .
                        'Le fichier EXCEL est en erreur. Corriger celui-ci et recommencez.' . '</div>';
            }

            // on passe en input hidden le tableau
            $this->load->model('seance_model');
            $data = array('pages' => $pages,
                'tab_pages' => serialize($pages),
                'commentaires' => $message,
                'erreur' => $erreur,
                'seance' => $this->seance_model->get_name($id_seance),
                'id_seance' => $id_seance);
            $this->load->view('form_seance_build', $data);
        }

        $this->load->view('footer');
    }

    public function validation_import() {

        test_acces(R_ADMIN, TRUE);

        $this->load->model('page_model');
        $this->load->model('seance_model');
        // on récupère le tableau
        if ($this->input->post('valid')) {
            // tout d'abors on supprime la liste de pages associées à la séance
            $id_seance = $this->input->post('id_seance');
            $this->seance_model->supprimer_seance_page($id_seance);
            $liste_pages = unserialize($this->input->post('tab_pages'));
            $rang = 1;
            $nb_page = count($liste_pages);
            foreach ($liste_pages as $page) {
                if ($page['id'] == 'xx') {
                    // il faut créer une page vide de nom  page_name
//                    trace('--création page', $page['page_name']);
                    $data = array('intitule' => $page['page_name']);
                    $id_page = $this->page_model->add($data);
//                    trace('--lien page seance', $page['id'] . ' seance ' . $id_seance);
                    $this->seance_model->set_seance_page($id_seance, $id_page, $rang++);
                } else if ($page['id'] != '') {
                    // le $id existe !!
//                    trace('--lien seance page', $page['id'] . ' seance ' . $id_seance);
                    $this->seance_model->set_seance_page($id_seance, $page['id'], $rang++);
                } else {
                    // page déjà crée ... on récupère rowid par son nom
//                    trace('--lien page seance', $page['page_name'] . ' seance ' . $id_seance);
                    $id_page = $this->page_model->get_id_by_name($page['page_name']);
                    $this->seance_model->set_seance_page($id_seance, $id_page, $rang++);
                }
            }
            // maj nb_page associé à la séance
            $this->seance_model->set_seance_nb_page($id_seance, $nb_page);
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

    public function build_from_list($id_seance) {
        // construction séance à partir liste de rowid de pages
        test_acces(R_ADMIN, TRUE);

        $this->load->model('page_model');
        $this->load->model('seance_model');

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');

        $this->load->view('header');
        $this->load->view('menu');

        $seance = $this->seance_model->get_seance($id_seance);
        $titre = "Construire la séance " . $seance['intitule'];
        $commentaires = '';
        $objets = 'pages';
        $id = $id_seance;
        $link = 'seance_build/build_from_list/' . $id;
        $liste_initiale = $this->seance_model->get_seance_liste_pages($id_seance);


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
                $retour = $this->seance_model->set_seance_liste_pages($id_seance, $liste);
                if ($retour === '') {
                    // tout OK
                    redirect('seance/lister_pages/' . $id_seance);
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
                redirect('seance/liste');
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

}
