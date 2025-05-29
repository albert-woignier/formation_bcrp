<?php

defined('BASEPATH') OR exit('Accès direct au script interdit.');

class Score extends CI_Controller {

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
        $this->load->database($this->session->data_base_group);
        test_acces();
        $this->load->model('score_model');
    }

    public function set_players($id_match) {

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');

        $this->load->view('header');
        $this->load->view('menu_saisie_score');
        trace('set players id_match =', $id_match);

        if (intval($id_match) !== 0) {
            //on modifie
            $score = $this->score_model->get_score($id_match);
            trace('score  id='.$id_match, $score);
            $nom_1 = $score['joueur_1'];
            $club_1 = $score['club_1'];
            $score_1 = $score['score_1'];
            $set_1 = $score['set_1'];
            $nom_2 = $score['joueur_2'];
            $club_2 = $score['club_2'];
            $score_2 = $score['score_2'];
            $set_2 = $score['set_2'];

            $competition = $score['competition'];

        } else {
            $nom_1 = '';
            $nom_2 = '';
            $competition = '';
        }

        $this->form_validation->set_rules('nom_1', 'Nom du 1er joueur', 'required');
        $this->form_validation->set_rules('nom_2', 'Nom du 2ème joueur', 'required');
        $this->form_validation->set_rules('competition', 'Nom de la compétition', 'required');

        if ($this->form_validation->run() === FALSE AND strlen(validation_errors()) > 0
                AND $this->input->post('valid')) {
            // erreurs !!
            $data = array(
                'commentaires' => 'Corriger les erreurs suivantes',
                'nom_1' => set_value('nom_1'),
                'nom_2' => set_value('nom_2'),
                'competition' => set_value('competition')
            );
            $this->load->view('score_saisie_joueurs', $data);
        } else {
            if ($this->input->post('valid')) {
                // OK on enregistre
                $tab = array(
                    'nom_1' => $this->input->post('nom_1'),
                    'club_1' => $this->input->post('club_1'),
                    'score_1' => $score_1,
                    'set_1' => $set_1,
                    'nom_2' => $this->input->post('nom_2'),
                    'club_2' => $this->input->post('club_2'),
                    'score_2' => $score_2,
                    'set_2' => $set_2,
                    'competition' => $this->input->post('competition'));

                    $this->score_model->set_joueurs(1, $tab['competition'],$tab['nom_1'],
                            $tab['club_1'], $score_1, $set_1,$tab['nom_2'], $tab['club_2'], $score_2,$set_2);
                    redirect('score/score_saisie');
            } else if ($this->input->post('annul')) {
                redirect('score/score_display');
            } else {
                $data = array(
                    'commentaires' => 'Saisir ou modifier les informations.',
                    'table' => 1,
                    'id' => $id_match,
                    'competition' => $competition,
                    'nom_1' => $nom_1,
                    'nom_2' => $nom_2,
                    'club_1' => $club_1,
                    'club_2' => $club_2
                );
                $this->load->view('score_saisie_joueurs', $data);
            }
        }

        $this->load->view('footer_saisie_score');
    }
    
    
    public function score_saisie() {
        //
        //
        $this->load->view('header');
       $this->load->view('menu_saisie_score');

        $this->load->view('score_ecran_saisie');
        $this->load->view('footer_saisie_score');
    }

        public function score_display() {
        $this->load->view('header');
       $this->load->view('menu_saisie_score');
        $table = 1;
        $query = $this->db->query("select joueur_1, joueur_2, score_1, score_2, "
                . "club_1, club_2, set_1, set_2, competition "
                . "from ff_score where rowid = $table");
        $row = $query->row();
        if (isset($row))
        {
            $joueur1 = $row->joueur_1;
            $joueur2 = $row->joueur_2;
            $score_1 = $row->score_1;
            $score_2 = $row->score_2;
            $set_1 = $row->set_1;
            $set_2 = $row->set_2;
            $club_1 = $row->club_1;
            $club_2 = $row->club_2;
            $competition = $row->competition;
        } else {
            $joueur1 = "inconnu";
            $joueur2 = "inconnu";
            $score_1 = " ?? ";
            $score_2 = " ?? ";
            $set_1 = " ?? ";
            $set_2 = " ?? ";
            $club_1 = $club_2 = '?';
            $competition = '----';
        }
        $data = array('joueur_1'=> $joueur1, 'joueur_2' => $joueur2, 
            'score_1' => $score_1, 'score_2' => $score_2,
            'set_1' => $set_1, 'set_2' => $set_2,
            'club_1' => $club_1, 'club_2' => $club_2, 'competition'=>$competition);
        $this->load->view('score_ecran_display', $data);
        $this->load->view('footer_saisie_score');
    }
    
    public function score_match($id_match) {
        $this->load->view('header');
       $this->load->view('menu_saisie_score');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        
        if (intval($id_match) !== 0) {
            //on modifie
            $match = $this->score_model->get_id($id_match);
            // trace('score_match  id='.$id_match, $match);
            $nom_1 = '';
            $nom_2 = '';
            $table =  '';
            $date = '';
            $infos = '';
            $set_1 = 0;
            $set_2 = 0;
            $score_1 = 0;
            $score_2 = 0;
        } else {
            $nom_1 = '';
            $nom_2 = '';
            $table =  '';
            $date = '';
            $infos = '';
            $set_1 = 0;
            $set_2 = 0;
            $score_1 = 0;
            $score_2 = 0;
            
        }
        
        
    }
    public function show_traces($trace = 'trace') {

        if ($trace === 'trace') {
            $fichier = APPPATH . 'logs/' . 'trace.php';
        } else {
            $fichier = APPPATH . 'logs/log-' . date('Y-m-d') . '.php';
        }

        $this->load->view('header');
        $this->load->view('menu_saisie_score');
        $chaine = file_exists($fichier) ? file_get_contents($fichier) : "Le fichier n'existe pas";
        $data = array('titre' => 'fichier ' . $fichier,
            'message' => '',
            'chaine' => nl2br($chaine));
        $this->load->view('ecran_message', $data);
        $this->load->view('footer_saisie_score');
    }

    public function delete_traces($trace = 'trace') {

        $this->load->view('header');
        $this->load->view('menu_saisie_score');
        if (file_exists(APPPATH . 'logs/trace.php')) {
            unlink(APPPATH . 'logs/trace.php');
        }
        if (file_exists(APPPATH . 'logs/log-' . date('Y-m-d') . '.php')) {
            unlink(APPPATH . 'logs/log-' . date('Y-m-d') . '.php');
        }
        $data = array('titre' => 'Les fichiers Trace et Log ont été supprimés',
            'message' => '');
        $this->load->view('ecran_message', $data);
        $this->load->view('footer_saisie_score');
    }
   
    
    public function saisie_resultats() {

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');

        $this->load->view('header');
        $this->load->view('menu_saisie_score');

// on va chercher le texte à faire défiler
        $texte =  $this->score_model->get_texte_defil();
        
            if ($this->input->post('valid')) {
                // OK on enregistre

                $texte =  $this->input->post('texte');
                $this->score_model->set_texte_defil($texte);
                redirect('score/score_display');
            } else if ($this->input->post('annul')) {
                redirect('score/score_display');
            } else {
                // on affiche
                $data = array(
                    'texte' => $texte
                );
                $this->load->view('score_saisie_resultats', $data);
            }
        $this->load->view('footer_saisie_score');
    }
    
    public function ajax_write_score() {
        
        $numero_joueur = $_REQUEST['num_joueur']; // 1 ou 2
        $points = $_REQUEST['points'];
        $sets = $_REQUEST['sets'];
        $table = $_REQUEST['table']; 
        // trace('ajax score', 'Joueur '. $_REQUEST['num_joueur']. '  - nb pts : '.$_REQUEST['points']. 'sets '.$_REQUEST['sets']);
        $colonne_points = ' score_'.$numero_joueur;
        $colonne_set = ' set_'.$numero_joueur;
        $query = "UPDATE ff_score SET ".$colonne_points. " = ". $colonne_points . " + " .$points . ","
                . "$colonne_set = ". $sets ." WHERE rowid = " .$table ;
        $result  = $this->db->query($query);
        // trace('ajax score', " query = $query --- RESULTAT UPDATE = ".$result);
        exit;
    }
    
    public function ajax_get_score() {
        $table = $_REQUEST['table']; 
        $query = $this->db->query("select joueur_1, joueur_2, score_1, score_2, club_1, club_2, set_1, set_2 from ff_score where rowid = $table");
        $row = $query->row();
        if (isset($row))
        {
            $joueur_1 = $row->joueur_1;
            $joueur_2 = $row->joueur_2;
            $score_1 = $row->score_1;
            $score_2 = $row->score_2;
            $club_1 = $row->club_1;
            $club_2 = $row->club_2;
            $set_1 = $row->set_1;
            $set_2 = $row->set_2;
            
        } else {
            $joueur_1 = "inconnu1";
            $joueur_2 = "inconnu2";
            $club_1 = $club_2 = '';
            $score_1 = $score_2 = 0;
            $set_1 = $set_2 = 0;
            
        }
        echo  $joueur_1 . '<br>' . $club_1 . ';'. $score_1  . ' points (sets ' . $set_1. ')' .
                ';'.$joueur_2 . '<br>' . $club_2 . ';'. $score_2  .  ' points (sets ' . $set_2. ')';
        exit;
    }
    public function ajax_get_details() {
        $table = $_REQUEST['table']; 
        $query = $this->db->query("select joueur_1, joueur_2, score_1, score_2, club_1, club_2, set_1, set_2 from ff_score where rowid = $table");
        $row = $query->row();
        if (isset($row))
        {
            $joueur_1 = $row->joueur_1;
            $joueur_2 = $row->joueur_2;
            $score_1 = $row->score_1;
            $score_2 = $row->score_2;
            $club_1 = $row->club_1;
            $club_2 = $row->club_2;
            $set_1 = $row->set_1;
            $set_2 = $row->set_2;
            
        } else {
            $joueur_1 = "inconnu1";
            $joueur_2 = "inconnu2";
            $club_1 = $club_2 = '';
            $score_1 = $score_2 = 0;
            $set_1 = $set_2 = 0;
            
        }
        echo  $joueur_1 . ';' . $club_1 . ';'. $score_1  . ';' . $set_1.
                ';'.$joueur_2 . ';' . $club_2 . ';'. $score_2  .  ';' . $set_2;
        exit;
    } 
    
    public function ajax_swap_players() {
        $table = $_REQUEST['table']; 
        $this->score_model->permute_joueurs($table);
        $query = $this->db->query("select joueur_1, joueur_2, score_1, score_2, club_1, club_2, set_1, set_2 from ff_score where rowid = $table");
        $row = $query->row();
        if (isset($row))
        {
            $joueur_1 = $row->joueur_1;
            $joueur_2 = $row->joueur_2;
            $score_1 = $row->score_1;
            $score_2 = $row->score_2;
            $club_1 = $row->club_1;
            $club_2 = $row->club_2;
            $set_1 = $row->set_1;
            $set_2 = $row->set_2;
            
        } else {
            $joueur_1 = "inconnu1";
            $joueur_2 = "inconnu2";
            $club_1 = $club_2 = '';
            $score_1 = $score_2 = 0;
            $set_1 = $set_2 = 0;
            
        }
        
        echo  $joueur_1 . ';' . $club_1 . ';'. $score_1  . ';' . $set_1.
                ';'.$joueur_2 . ';' . $club_2 . ';'. $score_2  .  ';' . $set_2;
        exit;
    } 
    
    public function ajax_players() {

        // $table = $_REQUEST['table']; 
        $table = 1;
        $query = $this->db->query("select joueur_1, joueur_2 from ff_score where rowid = $table");
        $row = $query->row();
        if (isset($row))
        {
            $joueur1 = $row->joueur_1;
            $joueur2 = $row->joueur_2;
        } else {
            $joueur1 = "inconnu";
            $joueur2 = "inconnu";
        }

        echo  $joueur1. ';'.$joueur2;
        exit;
    }
    
    public function ajax_new_set() {
        $query = "UPDATE ff_score SET score_1 = 0, score_2 = 0  WHERE rowid = 1 " ;
        $result  = $this->db->query($query);
        
    }
    public function ajax_get_defil() {
        // trace('ajax_get_defil', 'on rentre');
        $texte =  $this->score_model->get_texte_defil();
        $texte = trim(str_replace("\r\n", ' &bull; &bull; &bull; ', $texte));
        // trace('ajax_get_defil texte', $texte);
        echo $texte;
        
    }

}


