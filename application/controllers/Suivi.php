<?php

defined('BASEPATH') OR exit('Accès direct au script interdit.');

class Suivi extends CI_Controller {

    public function __construct() {
        parent::__construct();
        test_acces();
        $this->load->model('suivi_model');
    }

    public function inscrire($id_personne) {

        test_acces(R_ADMIN, TRUE);
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');

        $this->load->view('header');
        $this->load->view('menu');

        // récupérer nom de la personne
        //
        $this->load->model('personne_model');
        $personne = $this->personne_model->get_id($id_personne);
        //
        // récupérer liste des parcours
        $this->load->model('parcours_model');
        $les_parcours = $this->parcours_model->get_parcours();
        $liste_parcours = array();

        // on retire les parcours où la personne est inscrite
        for ($i = 0; $i < count($les_parcours); $i++) {
            if (!$this->suivi_model->is_inscrit($id_personne, $les_parcours[$i]['rowid'])) {
                $liste_parcours[$les_parcours[$i]['rowid']] = $les_parcours[$i]['intitule'];
            }
        }


        $this->form_validation->set_rules('id_parcours', 'Parcours', 'required');
        $this->form_validation->set_message('required', 'Le champ : {field} doit être renseigné');

        if ($this->form_validation->run() === FALSE AND strlen(validation_errors()) > 0
                AND $this->input->post('valid')) {
            // erreurs !!
            // trace('retour erreur', $this->input->post(NULL));
            $data = array(
                'commentaires' => 'Vous devez sélectionner un parcours',
                'id_personne' => $id_personne,
                'personne_name' => $personne['nom'] . ' ' . $personne['prenom'],
                'liste_parcours' => $liste_parcours
            );
            $this->load->view('form_inscription', $data);
        } else {
            if ($this->input->post('valid')) {
                // OK on enregistre
                // si nom existe on sort

                $id_personne = $this->input->post('id_personne');
                $id_parcours = $this->input->post('id_parcours');

                $this->suivi_model->inscrire($id_personne, $id_parcours);
                redirect('personne/liste');
            } else if ($this->input->post('annul')) {
                redirect('personne/liste');
            } else {
                // on affiche 1ère fois
                $data = array(
                    'commentaires' => 'Vous devez sélectionner un parcours',
                    'id_personne' => $id_personne,
                    'personne_name' => $personne['nom'] . ' ' . $personne['prenom'],
                    'liste_parcours' => $liste_parcours
                );
                $this->load->view('form_inscription', $data);
            }
        }


        $this->load->view('footer');
    }

    public function pers_par($id_personne) {

        test_acces();
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->helper('my_html_helper');
        $this->load->view('header');
        $this->load->view('menu');

        // récupérer nom de la personne
        //
        $this->load->model('personne_model');
        $personne = $this->personne_model->get_id($id_personne);
        //

        $les_parcours_suivis = $this->suivi_model->inscriptions_liste($id_personne);


        if (!is_ok(R_ADMIN)) {
            $data = array(
                'commentaires' => '',
                'id_personne' => $id_personne,
                'personne_name' => $personne['nom'] . ' ' . $personne['prenom'],
                'les_parcours_suivis' => $les_parcours_suivis
            );
            $this->load->view('parcours_suivis', $data);
            $this->load->view('footer');
        } else {
            /**
             * à partir d'ici on est en mode administrateur
             *
             */
            // récupérer liste des parcours
            $this->load->model('parcours_model');
            $les_parcours = $this->parcours_model->get_parcours();

            $liste_parcours_non_suivis = array();


            // pour l'inscription on retire les parcours où la personne est inscrite
            for ($i = 0; $i < count($les_parcours); $i++) {
                if (!$this->suivi_model->is_inscrit($id_personne, $les_parcours[$i]['rowid'])) {
                    $liste_parcours_non_suivis[$les_parcours[$i]['rowid']] = $les_parcours[$i]['intitule'];
                }
            }
            // formulaire pour l'inscription
            $this->form_validation->set_rules('id_parcours', 'Parcours', 'required');
            $this->form_validation->set_message('required', 'Le champ : {field} doit être renseigné');

            if ($this->form_validation->run() === FALSE AND strlen(validation_errors()) > 0
                    AND $this->input->post('valid')) {
                // erreurs !!
                // trace('retour erreur', $this->input->post(NULL));
                $data = array(
                    'commentaires' => 'Vous devez sélectionner un parcours',
                    'id_personne' => $id_personne,
                    'personne_name' => $personne['nom'] . ' ' . $personne['prenom'],
                    'liste_parcours_non_suivis' => $liste_parcours_non_suivis,
                    'les_parcours_suivis' => $les_parcours_suivis
                );
                $this->load->view('form_inscription', $data);
            } else {
                if ($this->input->post('valid')) {
                    // OK on enregistre
                    // si nom existe on sort

                    $id_personne = $this->input->post('id_personne');
                    $id_parcours = $this->input->post('id_parcours');

                    $this->suivi_model->inscrire($id_personne, $id_parcours);
                    redirect('personne/liste');
                } else if ($this->input->post('annul')) {
                    redirect('personne/liste');
                } else {
                    // on affiche 1ère fois
                    $data = array(
                        'commentaires' => 'Inscription à un parcours et liste des parcours déjà suivis',
                        'id_personne' => $id_personne,
                        'personne_name' => $personne['nom'] . ' ' . $personne['prenom'],
                        'liste_parcours_non_suivis' => $liste_parcours_non_suivis,
                        'les_parcours_suivis' => $les_parcours_suivis
                    );
                    $this->load->view('form_inscription', $data);
                }
            }
            // fin formulaire

            $this->load->view('footer');
        }
    }

    public function select($what_for = '') {
        //  on sélectionne un parcours
        // et on aiguille en fct de $whatfor
        $this->load->view('header');
        $this->load->view('menu');
        // trace('parcours select what for  = ' . $what_for);
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
            'commentaires' => 'Commentaires spécifiques les séances du parcours.',
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
            // trace('parcours id=' . $id, $parcours);
            $disc = $parcours['fk_t_discipline'];
            $niv = $parcours['fk_t_niveau'];
            $intit = $parcours['intitule'];
        } else {
            $disc = 1;
            $niv = 1;
            $intit = '';
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
                    'fk_t_discipline' => $this->input->post('discipline'),
                    'fk_t_niveau' => $this->input->post('niveau')
                );
                if ($this->input->post('id') !== '0') {
                    // trace("on modifie id = " . $this->input->post('id'), $tab);
                    $this->parcours_model->update($this->input->post('id'), $tab);
                } else {
                    // trace("on ajoute id = " . $this->input->post('id'), $tab);
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
                    'list_box_disciplines' => $liste_disciplines,
                    'list_box_niveaux' => $liste_niveaux
                );
                $this->load->view('form_parcours', $data);
            }
        }


        $this->load->view('footer');
    }

    public function seances($id_personne, $id_parcours) {
        // liste le suivi des séances d'une personne sur un parcours
        test_acces();
        $this->load->helper('my_html_helper');
        $this->load->view('header');
        $this->load->view('menu');

        $this->load->model('personne_model');
        $personne = $this->personne_model->get_id($id_personne);
        $nom_prenom = $personne['nom'] . ' ' . $personne['prenom'];

        $this->load->model('parcours_model');
        $parcours = $this->parcours_model->get_parcours($id_parcours);
        $nom_parcours = $parcours['intitule'];
        $les_seances_du_parcours = $this->parcours_model->get_parcours_seance($id_parcours);
        if (count($les_seances_du_parcours) == 0) {
            $this->load->view('ecran_message', array('titre' => 'Oups ...', 'message' => 'Le parcours n\'est pas encore constitué.'));
            $this->load->view('footer');
            return;
        }
//        trace('$les_seances_du_parcours', $les_seances_du_parcours);
        $id_last_seance_parcours = $les_seances_du_parcours[count($les_seances_du_parcours) - 1]['id'];
//        trace('$id_last_seance_parcours', $id_last_seance_parcours);
        $les_seances_suivies = $this->suivi_model->lister_seances_suivies($id_personne, $id_parcours);
//        trace('$les_seances_suivies', $les_seances_suivies);
        // on détermine la séance suivante
        $nb_seances_suivies = count($les_seances_suivies);
        /*         * *
         *
         * on cherche la prochaine séance à réaliser
         *
         */

        if ($nb_seances_suivies > 0) {
            $last_seance = $les_seances_suivies[$nb_seances_suivies - 1]['id_seance'];
//            trace('$last_seance', $last_seance);
            if ($les_seances_suivies[$nb_seances_suivies - 1]['id_seance'] == $id_last_seance_parcours
                    AND $les_seances_suivies[$nb_seances_suivies - 1]['validation'] == SEANCE_PARCOURS_VALIDE) {
                // on a fait la dernière seance du parcours avec succès : le parcours est terminé
                // trace('on a fait la dernière seance du parcours avec succès : le parcours est terminé');
            } else if ($les_seances_suivies[$nb_seances_suivies - 1]['validation'] == SEANCE_VALIDEE) {
                // dernière séance validée, on doit faire la suivante
//                trace('la denière seance suivie a été validée !!!');
                $next_seance = $this->suivi_model->get_next_seance($id_parcours, $last_seance);
//                trace('$next_seance', $next_seance);
                // est-ce la denière ?
                if ($next_seance) {
                    // il y a donc une next séance
//                    trace('la next seance n\'est pas la dernière séance');
                    $les_seances_suivies[] = $next_seance;
                }
            } else {
                // la dernière séance n'a pas été validée, on la refait
//                trace('la denière séance est non validée, on la refait');
                $seance_a_refaire = $les_seances_suivies[$nb_seances_suivies - 1];
                $seance_a_refaire['validation'] = 10;
                $seance_a_refaire['date_seance'] = '----';
                $seance_a_refaire['moniteur'] = '--';

                $les_seances_suivies[] = $seance_a_refaire;
            }
        } else {
            // on commence par 1ère sénce
            $next_seance = $this->suivi_model->get_next_seance($id_parcours, 0);
            $les_seances_suivies[] = $next_seance;
        }

        $data = array();
        $data['titre'] = 'Suivi de ' . $nom_prenom . ' sur le parcours ' . $nom_parcours;
        $data['les_seances'] = $les_seances_suivies;
        $data['commentaires'] = '';
        $data['id_eleve'] = $id_personne;
        $data['id_parcours'] = $id_parcours;
        $this->load->view('personne_suivi', $data);

        $this->load->view('footer');
    }

    public function seance_valider($id_parcours, $id_seance, $id_eleve) {

        test_acces(R_MON, TRUE);

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->model('personne_model');
        $this->load->model('seance_model');
        $this->load->model('suivi_model');
        $this->load->model('parcours_model');

        $this->load->view('header');
//        $this->load->view('menu');
        if ($id_eleve == 0) {
            $this->load->view('footer');
            exit();
        }

        $eleve = $this->personne_model->get_id($id_eleve);
        $seance = $this->seance_model->get_seance($id_seance);
        // trace('on rentre dans seance valider POST =', $this->input->post(NULL, TRUE));
        if ($this->input->post('do_what') == 'valid' OR $this->input->post('do_what') == 'un_valid') {
            // OK on enregistre
            $suivi = array(
                'commentaires' => $this->input->post('comments'),
                'nb_points' => $this->input->post('nb_points'),
                'id_seance' => $id_seance,
                'id_parcours' => $id_parcours,
                'id_apprenant' => $id_eleve,
                'evaluation' => $this->input->post('evaluation'),
                'id_moniteur' => $this->session->userdata('user_id'));
            if ($this->input->post('do_what') == 'valid') {
                $suivi['validation'] = SEANCE_VALIDEE;
                if (!($this->suivi_model->get_next_seance($id_parcours, $id_seance))) {
                    $suivi['validation'] = SEANCE_PARCOURS_VALIDE;
                    // on valide la parcours !!!
                    $this->suivi_model->update_inscription($id_eleve, $id_parcours, SEANCE_PARCOURS_VALIDE, date('Y-m-d'));
                }
            } else {
                $suivi['validation'] = SEANCE_ENCOURS;
            }
            // on serialze les résultats si examen

            $examen = array();
            // @TEST on prépare examen pour toutes séances ... 
            if ($seance['type'] == 1 OR is_ok(R_DEV)) {
                // on enregistre les infos pour générer fichier excel si examen
                $examen['nom_eleve'] = $eleve['nom'] . ' ' . $eleve['prenom'];
                $examen['licence_eleve'] = $eleve['license'];
                $examen['mail_eleve'] = $eleve['mail'];
                $examen['mail_moniteur'] = $_SESSION['user_mail'];
                $examen['nom_seance'] = $seance['intitule'];
                $examen['nom_parcours'] = $this->parcours_model->get_name($id_parcours);
                $examen['total'] = $_SESSION['total'];
                $examen['notation'] = $_SESSION['notation'];
                $examen['nom_moniteur'] = $this->session->user_nom . ' ' . $this->session->user_prenom;
                $examen['modele_excel'] = $this->parcours_model->get_modele_excel($id_parcours);
                // 
                // ici on teste carambole (1) ou poche (3 ou 4) ??
                //
                $discipline_id = $this->parcours_model->quelle_discipline_id($id_parcours);
                switch ($discipline_id) {
                    case 1: 
                        $this->suivi_model->examen_excel($examen);
                        break;
                    case 3:
                    case 4:
                        $this->suivi_model->examen_excel_poche($examen);
                        break;
                }
                

                // TODO voir si serialize $_SESSION['notation'] simplement
                // $serialize_examen = serialize($examen);
            }

            // on serialize les résultats examen si besoin
            $suivi['examen'] = ($seance['type'] == 1) ? serialize($examen) : NULL;

            // trace('suivi tableau', $suivi);
            $this->suivi_model->enregistrer_seance($suivi);

            unset($_SESSION['total']);
            unset($_SESSION['notation']);
            unset($_SESSION['discipline']);
            redirect("suivi/pers_par/$id_eleve");
        } else if ($this->input->post('do_what') == 'annul') {
            // trace('suivi annulation ');
            redirect("suivi/pers_par/$id_eleve");
        } else {
            // on affiche le formulaire
            $nb_points = 0;
            // @TEST on prépare examen pour toutes séances ... 
            if ($seance['type'] == 1 OR is_ok(R_DEV)) {
                $nb_points = $_SESSION['total'];
            }
            $discipline_id = $this->parcours_model->quelle_discipline_id($id_parcours);
            $choix_eval = $this->session->evaluation;
            $data = array(
                'seance' => $seance['intitule'],
                'eleve' => $eleve['nom'] . ' ' . $eleve['prenom'],
                'id_seance' => $id_seance,
                'type_exam' => $seance['type'],
                'nb_points' => $nb_points,
                'id_parcours' => $id_parcours,
                'id_eleve' => $id_eleve,
                'choix_eval' => $choix_eval
            );
            // @TEST on prépare examen pour toutes séances ... 
            if ($seance['type'] == 1 OR is_ok(R_DEV)) {
                // on charge le HTML tableau de notation en carambole ou en poche
                $data['html_exam'] = $this->load->view('html_notations_exam', 
                        array('discipline_id' => $discipline_id ,
                            'notes_figures' => $_SESSION['notation']), TRUE); // utilise les données en $_SESSION
            }
            $this->load->view('form_end_seance', $data);
        }
        $this->load->view('footer');
    }

    public function lire_commentaires($id_personne, $id_commentaire, $id_parcours) {
        if (!is_good_personne($id_personne) AND ! is_ok(R_MON)) {
            return;
        }
        $this->load->view('header');
        $this->load->model('suivi_model');
        $this->load->model('parcours_model');
        $this->load->helper('my_html_helper');

        $notation = $this->suivi_model->get_notation($id_commentaire);
        // trace ('lire_commentaires - notation', $notation);
        // on récupère les infos moniteur, eleve, parcours, séance
        $notation['infos'] = $this->suivi_model->get_infos_notation($id_commentaire);
        $notation['examen'] = unserialize($notation['examen']);

        // quel type de parcours (carambole ou poche ?) 
        // influe sur présentation examen
        $notation['discipline_id'] = $this->parcours_model->quelle_discipline_id($id_parcours);
        // trace ('lire_commentaires - notation', $notation);
        if ($notation['examen']) {
            // c'est un examen 
            // on charge le HTML tableau de notation figures en carambole ou en poche
            $notation['html_exam'] = $this->load->view('html_notations_exam',
                    array('discipline_id' => $notation['discipline_id'] ,
                            'notes_figures' => ($notation['examen']['notation'])), TRUE); // utilise les données en $_SESSION
        }
        
        $this->load->view('suivi_commentaires', $notation);

        $this->load->view('footer');
    }
    
    public function dernieres_seances() {
        // liste les séances depuis 3 mois
        test_acces();
        $this->load->helper('my_html_helper');
        $this->load->view('header');
        $this->load->view('menu');
        $data = array();
        
        $requete = "SELECT s.intitule as seance, p.intitule as parcours , elev.nom as eleve, moni.nom as moniteur, "
                . " fk_notation, date_seance, validation, fk_eleve, fk_parcours "
                . " FROM ff_personne_suivi as ps "
                . " LEFT JOIN ff_seance as s on ps.fk_seance = s.rowid "
                . " LEFT JOIN ff_parcours as p on fk_parcours = p.rowid "
                . " LEFT JOIN ff_personne as elev on fk_eleve = elev.rowid "
                . " LEFT JOIN ff_personne as moni on fk_moniteur = moni.rowid "
                . " WHERE DATEDIFF(NOW(), date_seance) < 180  ORDER BY date_seance DESC";
        $resultat = $this->db->query($requete);
        $data['les_seances'] =  $resultat->result_array();
        
        $data['titre'] = 'Les séances de formation de ces 6 derniers mois' ;
        $this->load->view('seances_suivies', $data);

        $this->load->view('footer');
    }
    
    public function stat_seances() {
        // décompte les séances données mois par mois depuis le début
        test_acces();
        $this->load->helper('my_html_helper');
        $this->load->view('header');
        $this->load->view('menu');
        $data = array();
        $this->db->query("SET lc_time_names = 'fr_FR'");
        // SET lc_time_names = 'fr_FR'
        $requete = "SELECT year(date_seance) as An, monthname(date_seance) as Mois, COUNT(fk_eleve) as nb_seances , COUNT(DISTINCT fk_eleve) as nb_eleves,
                    COUNT(DISTINCT fk_moniteur) as nb_moniteurs 
                            FROM ff_personne_suivi 
                GROUP BY year(date_seance) DESC, month(date_seance) DESC";
        
        $resultat = $this->db->query($requete);
        $data['les_seances'] =  $resultat->result_array();
        
        $data['titre'] = 'Les séances de formation ventilées par mois' ;
        $this->load->view('seances_stat_mens', $data);

        $this->load->view('footer');
    }
    
    public function stat_seances_annee() {
        // décompte les séances données annee par ann depuis le début
        test_acces();
        $this->load->helper('my_html_helper');
        $this->load->view('header');
        $this->load->view('menu');
        $data = array();
        $this->db->query("SET lc_time_names = 'fr_FR'");
        // SET lc_time_names = 'fr_FR'
        $requete = "SELECT year(date_seance) as An, COUNT(fk_eleve) as nb_seances , COUNT(DISTINCT fk_eleve) as nb_eleves,
                    COUNT(DISTINCT fk_moniteur) as nb_moniteurs 
                            FROM ff_personne_suivi 
                GROUP BY year(date_seance) DESC";
        
        $resultat = $this->db->query($requete);
        $data['les_seances'] =  $resultat->result_array();
        
        $data['titre'] = 'Les séances de formation ventilées par année calendaire' ;
        $this->load->view('seances_stat_annee', $data);

        $this->load->view('footer');
    }
}
