<?php

defined('BASEPATH') OR exit('Accès direct au script interdit.');

class Login extends CI_Controller {

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

        // $this->load->helper('url_helper');
        log_message('debug', 'construct de Login');
    }

    public function index($id = 0) {

        $statut = 0; // on rentre pour la 1ere fois
//        log_message('debug', 'FCPATH =' . FCPATH);
//        log_message('debug', 'APPPATH =' . APPPATH);
//        log_message('debug', 'BASEPATH =' . BASEPATH);
//        log_message('debug', 'base_url =' . base_url());
//        log_message('debug', 'site_url =' . site_url());

        $this->load->helper(array('form', 'url'));

        $this->load->library('form_validation');

        $this->form_validation->set_rules('user_id', 'Identifiant', 'required');
        $this->form_validation->set_rules('user_mdp', 'Mot de passe', 'required');
        $this->form_validation->set_message('required', 'Le champ : {field} doit être renseigné');
            log_message('debug', 'avant form_validation');
        if ($this->form_validation->run() === FALSE AND strlen(validation_errors()) > 0
                AND $this->input->post('valid')) {
            // erreurs de saisie !!
            log_message('debug', 'erreur de saisie');
            $data = array(
                'commentaires' => 'Corriger les erreurs suivantes'
            );
            $this->load->view('form_login', $data);
            $statut = 1; // on a affiché le HTML
        } else {
            log_message('debug', 'vérifier si user OK');
            if ($this->input->post('valid')) {
                // on vérifie si user OK
                $this->load->model('login_model');
                $id = $this->login_model->good_psswd($this->input->post('user_id'), $this->input->post('user_mdp'));
                if ($id) {
                    // OK on login
                    //
                    // $this->load->library('session');
//                    trace('Login , id = ' . $id);
                    log_message('debug', 'user OK');
                    $this->load->model('personne_model');
                    $personne = $this->personne_model->get_id($id);
                    switch ($personne['categorie']) {
                        case R_INVIT:
                            $droits = 1; // en binaire 0001
                            break;
                        case R_ELEV:
                            $droits = 3; // en binaire 0011
                            break;
                        case R_MON:
                            $droits = 7; // en binaire 0111
                            break;
                        case R_ADMIN:
                            $droits = 15; // en binaire 1111
                            break;
                        case R_DEV:
                            $droits = 31; // en binaire 11111
                            break;
                    }
                    $this->session->set_userdata(array(
                        'user_id' => $id,
                        'user_prenom' => $personne['prenom'],
                        'user_nom' => $personne['nom'],
                        'user_nom_complet' => $personne['prenom'] . ' ' . $personne['nom'],
                        'user_mail' => $personne['mail'],
                        'user_phone' => $personne['phone'],
                        'user_categorie' => $personne['categorie'],
                        'droits' => $droits));

//                    trace('session', $this->session->userdata());
                    //

                    // pour le scoring
                    if ($this->input->post('user_id') == 's' AND $this->input->post('user_mdp') == 's') {
                        log_message('debug', 'on va vers site de scoring');
                        redirect('/score/score_display');
                    } 
                    log_message('debug', 'on va vers /login/hello');
                    // mail
                    $this->load->helper('email');
                    $subject = '[BCRP Formation] Login de '. $this->session->user_nom;
                    send_email('woignier@gmail.com', $subject, 'OK');
                    redirect('/login/hello');
                    // header("location: $link");
                } else {
                    // mauvaise saisie id et mdp ...
                    // on renvoie le formulaire
                    log_message('debug', 'maivaise saisie user te mdp');
                    $data = array('erreur' => 'Identifiant ou mot de passe incorrect.');
                    $this->load->view('form_login', $data);
                }
            } else {
                // on affiche le formulaire vide pour la 1ere fois
                //
                log_message('debug', 'afficher formulaire pour la 1ere fois');
                $data = array();
                $this->load->view('form_login', $data);
            }
        }
    }

    public function liste($qui) {
        test_acces(R_MON, TRUE);
        $this->load->view('header');
        $this->load->view('menu');
        // afficher view de bienvenue

        $this->load->model('login_model');
        $data = $this->login_model->login_list($qui);
        $titre = 'Les connexions ...';
        if (is_ok(R_MON)) {
            $data = array_slice($data, 0, 45);
            $titre = 'Les 45 dernières connexions ...';
        }
        $this->load->view('login_liste', array('logins' => $data, 'titre' => $titre));
        $this->load->view('footer');
    }

    public function hello() {

        //$this->load->library('session');
        log_message('debug', 'hello enter, user_nom_prenom = ' . $this->session->user_prenom . ' ' . $this->session->user_prenom);
//        trace('session', $this->session->userdata());
        $this->load->view('header');
        $this->load->view('menu');
        // afficher view de bienvenue
        $this->load->view('hello_page');
        $this->load->view('footer');
    }

    public function message_err($message) {

        $this->load->view('header');
        $this->load->view('menu');

        $this->load->view('ecran_message', array('titre' => 'Erreur', 'message' => urldecode($message)));
        $this->load->view('footer');
    }

    public function close() {
        //$this->load->library('session');
        $this->session->sess_destroy();
        // $link = site_url() . '/login';
        redirect('/login');
        //header("location: $link");
    }

    public function ajax_get_device() {
        // récupère le device sur lequel l'utilisateur travaille
//        trace('on rentre ajax_get_device');
        $pdf_ok = $_REQUEST['pdf_ok'];
        $win_size = $_REQUEST['view_port'];
//        trace('on rentre ajax_get_device pdf_ok = ' . $pdf_ok);
        $this->session->set_userdata(array(
            'pdf_ok' => $pdf_ok));
        $this->session->set_userdata(array(
            'view_port' => $win_size));
        $this->load->model('login_model');
        $this->login_model->save_winsize($this->session->login_id, $win_size);
        echo ('ok guys');
        exit;
    }

    public function tuto() {
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('tuto_view');

        $this->load->view('footer');
    }

}
