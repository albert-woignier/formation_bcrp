<?php

defined('BASEPATH') OR exit('Accès direct au script interdit.');

class Seance extends CI_Controller {

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
        $this->load->model('seance_model');
    }

    public function liste() {

        $query = $this->seance_model->get_seance();

        $data = array(
            'commentaires' => 'Commentaires spécifiques bla bla bla.',
            'titre' => 'Liste de toutes les séances enregistrées',
            'les_seances' => $query
        );

        $this->load->view('header');
        $this->load->view('menu');

        $this->load->view('seance_liste', $data);

        $this->load->view('footer');
    }

    public function select($what_for = '') {
        //  on sélectionne une séance
        // et on aiguille en fct de $whatfor
        $this->load->view('header');
        $this->load->view('menu');
//        trace('seance select what for  = ' . $what_for);
        if ($what_for == 'build') {
            $query = $this->seance_model->get_seance();

            $data = array(
                'titre' => 'Séance',
                'message' => 'Sélectionner la séance à constituer',
                'action' => site_url('seance_build/import/'),
                'les_seances' => $query);

            $this->load->view('form_seance_select', $data);



            $this->load->view('footer');
        } else {
            $this->parser->parse('ecran_message', array('titre' => 'Erreur du programme seance/select',
                'message' => 'Action inconnue'));

            $this->load->view('footer');
        }
    }

    public function lister_pages($id) {

        $seance_name = $this->seance_model->get_name($id);
        $les_pages = $this->seance_model->get_seance_page($id);


        $data = array(
            'commentaires' => 'Commentaires spécifiques les pages de la séance.',
            'les_pages' => $les_pages,
            'seance' => $seance_name,
            'nb_pages' => count($les_pages)
        );

        $this->load->view('header');
        $this->load->view('menu');

        $this->load->view('seance_pages', $data);

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
            $seance = $this->seance_model->get_seance($id);
//            trace('seance id=' . $id, $seance);
            $intit = $seance['intitule'];
            $exam = $seance['type'];
        } else {
            $intit = '';
            $exam = 0;
        }

        $this->form_validation->set_rules('intitule', 'Intitulé', 'required');
        $this->form_validation->set_message('required', 'Le champ : {field} doit être renseigné');

        if ($this->form_validation->run() === FALSE AND strlen(validation_errors()) > 0
                AND $this->input->post('valid')) {
            // erreurs !!
            $data = array(
                'commentaires' => 'Corriger les erreurs suivantes',
                'id' => $id,
                'intitule' => set_value('intitule'),
                'exam' => set_checkbox('examen', '1')
            );


            $this->load->view('form_seance', $data);
        } else {
            if ($this->input->post('valid')) {
                // OK on enregistre
                // si nom existe on sort
                $examen = ($this->input->post('examen') == '1') ? 1 : 0; // on regarde si checkbox est "checked"
                $tab = array(
                    'intitule' => $this->input->post('intitule'),
                    'type' => $examen
                );
                if ($this->input->post('id') !== '0') {
                    // trace("on modifie sence id = " . $this->input->post('id'), $tab);
                    $this->seance_model->update($this->input->post('id'), $tab);
                } else {
                    // trace("on ajoute seance id = " . $this->input->post('id'), $tab);
                    $this->seance_model->add($tab);
                }

                redirect('seance/liste');
            } else if ($this->input->post('annul')) {
                redirect('seance/liste');
            } else {

                $data = array(
                    'commentaires' => 'Saisir les informations.',
                    'id' => $id,
                    'intitule' => $intit,
                    'exam' => ($exam == 1) ? 'checked' : ''
                );
                $this->load->view('form_seance', $data);
            }
        }


        $this->load->view('footer');
    }

    public function derouler_seance($id_seance, $rang = 0, $id_parcours = 0, $id_eleve = 0) {

        $this->load->helper('my_html_helper');
        $this->load->view('header');
        $seance = $this->seance_model->get_seance($id_seance);
        $nom_seance = $seance['intitule'];
        $type = $seance['type'];

        $finalisation = 0; // flag pour la dernière page de la séance afin d'afficher un lien vers la finalisation/notation de la séance
        $this->load->model('page_model');
        if ($rang == 0) {
            // on affiche 1ère page de la séance
            $rang = 1;
            // si c'est un examen on prépare
            if ($type == 1) {
                $this->load->model('parcours_model');
                
                $_SESSION['notation'] = array();
                $_SESSION['total'] = 0;
                if ($id_parcours !== 0) {
                    $_SESSION['discipline_id'] = $this->parcours_model->quelle_discipline_id($id_parcours);
                }
            }
        }

        $nb_pages = $this->seance_model->get_nb_pages($id_seance);
        $id_page = $this->seance_model->get_id_page($id_seance, $rang);
        if ($rang == 1) {
            $lien_precedant = "";
            if ($nb_pages > 1) {
                $lien_suivant = "seance/derouler_seance/$id_seance/" . ($rang + 1) . "/$id_parcours/$id_eleve";
            } else {
                // le lien suivant est la validation
                $finalisation = 1;
                if ($id_parcours !== 0) {
                    $lien_suivant = 'suivi/seance_valider/' . $id_parcours . '/' . $id_seance . '/' . $id_eleve;
                } else {
                    $lien_suivant = '';
                }
            }
        } else if ($rang == $nb_pages) {

            // on doit finaliser la séance
            if ($id_parcours !== 0) {
                $finalisation = 1;
                $lien_suivant = 'suivi/seance_valider/' . $id_parcours . '/' . $id_seance . '/' . $id_eleve;
            } else {
                $lien_suivant = '';
            }
            $lien_precedant = "seance/derouler_seance/$id_seance/" . ($rang - 1) . "/$id_parcours/$id_eleve";
        } else {
            $lien_precedant = "seance/derouler_seance/$id_seance/" . ($rang - 1) . "/$id_parcours/$id_eleve";
            $lien_suivant = "seance/derouler_seance/$id_seance/" . ($rang + 1) . "/$id_parcours/$id_eleve";
        }
        // trace('id_page', $id_page);
        if ($id_page > 0) {
            $page = $this->page_model->get($id_page);
            // on traite les images videos et pdf entre [ ]
            $str = replace_page_tags($page['contenu']);
            $pattern = '/\[(exo)(.*)\]/';
            $pattern_poche = '/\[(poche)(.*)\]/';
            // @TEST on décode les figures examen pour toutes sessions 
            if (($type == 1 OR is_ok(R_DEV)) AND preg_match($pattern, $str, $matches)) {
                // on est sur un exo type carambole
                // trace('les infos passées :', $matches);
                $infos = get_infos_exam($matches[2]); // renvoie tableau avec n°exam, notes maxi basse et haute
                if ($infos) {
                    // trace('les infos en tableau :', $infos);
                    $html_form = $this->load->view('form_exam', $infos, TRUE);
                } else {
                    $html_form = "<strong>Erreur dans la formulation de l'exercice. Voir votre administrateur.</strong>";
                }
                $pattern2 = '!\<p\>\[(exo)(.*)\]\</p\>!';
                $str = preg_replace($pattern2, $html_form, $str);
                // @TEST on décode les figures examen pour toutes sessions 
            } else  if (($type == 1 OR is_ok(R_DEV)) AND preg_match($pattern_poche, $str, $matches)) {
                // on tester si exo type américain ...
                // on est sur un exo type carambole
                // trace('les infos passées exam poche :', $matches);
                $infos = get_infos_exam_poche($matches[2]); // renvoie tableau avec n°exam, notes maxi basse et haute
                if ($infos) {
                    // trace('les infos poche en tableau :', $infos);
                    $html_form = $this->load->view('form_exam_poche', $infos, TRUE);
                } else {
                    $html_form = "<strong>Erreur dans la formulation de l'exercice 'poche'. Voir votre administrateur.</strong>";
                }
                $pattern_poche2 = '!\<p\>\[(poche)(.*)\]\</p\>!';
                $str = preg_replace($pattern_poche2, $html_form, $str);
            } else {
                
            }
        } else {
            $str = '<strong>Cette page n\'a aucun contenu. Voir administrateur.</strong>';
        }
        $data = array(
            'titre' => $nom_seance . ' , page N° ' . $rang,
            'commentaires' => '',
            'examen' => $type,
            'finalisation' => $finalisation,
            'lien_precedant' => $lien_precedant,
            'lien_suivant' => $lien_suivant,
            'rang' => $rang,
            'pagination' => 1,
            'contenu' => $str
        );

        $this->load->view('page_apercu', $data);


        $this->load->view('footer');
    }

    public function ajax_exam_record() {
        // enregistre note et bonus pour un exercice
        // trace('ajax exo', $_REQUEST['exo']);
        // trace('ajax note', $_REQUEST['note']);
        $note = intval($_REQUEST['note']);
        $bonus = intval($_REQUEST['bonus']);
        $nu_exo = ($_REQUEST['exo']);

        $_SESSION['total'] += $note;
        if ($bonus !== -1) {
            $_SESSION['total'] += $bonus;
        }
        $_SESSION['notation'][] = array('num_exo' => $nu_exo, 'note' => $note, 'bonus' => $bonus);
        echo $_SESSION['total'];
        exit;
    }
    public function ajax_exam_poche_record() {
        // enregistre note et détail essais pour un exercice poche
        // trace('ajax exo', $_REQUEST['exo']);
        // trace('ajax note', $_REQUEST['note']);
        $note = floatval($_REQUEST['note']);
        $essais = ($_REQUEST['essais']); // chaine, voir son formattage
        $nu_exo = ($_REQUEST['exo']);
        $nb_coups = ($_REQUEST['nb_coups']); // nombre de coups (1 pour carambole) ou de billes à rentrer (poche)
        $_SESSION['total'] += $note;
        // trace ('on enregistre', 'nu exo '.$nu_exo.' note ['.$note. '] essais : '.$essais);
        $_SESSION['notation'][] = array('num_exo' => $nu_exo, 'note' => $note, 'nb_coups' => $nb_coups, 'essais' => $essais);
        // trace('session_notation', $_SESSION['notation']);
        echo $_SESSION['total'];
        exit;
    }
}
