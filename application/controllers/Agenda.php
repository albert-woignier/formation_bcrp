<?php

defined('BASEPATH') OR exit('Accès direct au script interdit.');

class Agenda extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('disponibilite_model');
    }

    public function show($annee = '', $mois = '') {
        test_acces(R_ELEV, TRUE);
        $this->load->helper('my_func_helper');
        $this->load->model('disponibilite_model');
        $tab_jours_semaine = array(1 => 'Lun', 2 => 'Mar', 3 => ' Mer', 4 => 'Jeu', 5 => 'Ven', 6 => 'Sam', 7 => 'Dim');
        $tab_mois = array("", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Décembre");
//        $this->load->view('header_timepicker');
        $this->load->view('header');
        $this->load->view('menu');
        // $this->calendar->default_template();
        if ($annee == '' AND $mois == '') {
            $annee = date('Y');
            $mois = date('m');
        }
        $nom_mois = $tab_mois[intval($mois)];
        $t1 = mktime(12, 0, 0, $mois, 1, $annee); // 1er 12 2020
        $numero_jour_semaine = idate('w', $t1);
        $numero_jour_semaine = ($numero_jour_semaine == 0) ? 7 : $numero_jour_semaine; // on lun = 1 et dim = 7 
        $week = idate('W', $t1);
        $nbjours = date('t', $t1);
        $nb_jours_avant = $numero_jour_semaine - 1;
        $tab = array();
        $n = 1;
        $jour_du_mois = 1;
        for ($no_week = 1; $no_week <= 5; $no_week++) { // pour chacune des 5 semaines du mois
            for ($no_jour_week = 1; $no_jour_week <= 7; $no_jour_week++) { // pour chacun des 7 jours de la semaine, du lundi au dimanche
                if ($n <= $nb_jours_avant OR $jour_du_mois > $nbjours) {
                    $tab[$no_week][$no_jour_week]['jour'] = '';
                    $tab[$no_week][$no_jour_week]['creneaux'] = array();
                    $n++;
                } else {
                    $tab[$no_week][$no_jour_week]['jour'] = $jour_du_mois;
                    $tab[$no_week][$no_jour_week]['date'] = $annee . '-' . $mois . '-' . $jour_du_mois;
                    //
                    // VOIR ici si il y a des créneaux pour les afficher
                    $date = $annee . '-' . $mois . '-' . $jour_du_mois;
                    $creneaux = $this->disponibilite_model->get_date(array($date));
                    $tab[$no_week][$no_jour_week]['creneaux'] = $creneaux;
                    $jour_du_mois++;
                }
            }
        }
        list($annee_prec, $mois_prec) = mois_avant_apres($annee, $mois, -1);
        list($annee_suiv, $mois_suiv) = mois_avant_apres($annee, $mois, +1);

        if (is_ok(R_MON)) {
            $chaine = 'Cliquer sur une date pour ajouter/modifier une disponibilité';
        } else if (is_ok(R_ELEV)) {
            $chaine = 'Cliquer sur un créneau pour réserver un RDV avec le moniteur';
        }

        if ($this->session->view_port > 650) {
            $vue_agenda = 'calendrier'; // view calendrier.php
        } else {
            $vue_agenda = 'calendrier_sm'; // view calendrier_sm.php small
        }
        $this->load->view($vue_agenda, array('titre' => 'agenda',
            'message' => $chaine,
            'mois_courant' => $nom_mois . ' ' . $annee,
            'mois_precedent' => site_url("agenda/show/$annee_prec/$mois_prec"),
            'mois_suivant' => site_url("agenda/show/$annee_suiv/$mois_suiv"),
            'Y_m' => $annee.sprintf('%02d', $mois), // AAAAmm
            'agenda' => $tab
        ));

        $this->load->view('footer');
    }

    public function ajax_voir_creneau() {

        $id = $_REQUEST['id'];
        //trace('ajax_voir_creneau', $id);
        $this->load->model('disponibilite_model');
        $this->load->helper('my_html_helper');
        $creneau = $this->disponibilite_model->get_creneau($id);
        $creneau['datefr'] = date_fr($creneau['date']);
//        trace('ajax creneau', $creneau);
        if ($creneau['complet']) {
            // $creneau['disponibilite'] = '<strong>COMPLET</strong> : (' . $creneau['nb_resa'] . '/' . $creneau['nb_pers_max'] . ')';
            $creneau['disponibilite'] = '<strong>COMPLET</strong>';
        } else {
            // $creneau['disponibilite'] = $creneau['nb_pers_max'] - $creneau['nb_resa'] . ' place(s) sur ' . $creneau['nb_pers_max'] . ' max';
            $creneau['disponibilite'] = $creneau['nb_pers_max'] - $creneau['nb_resa']  . ' place(s)';
        }
        $data['suppr_possible'] = 0;
        if ($_SESSION['user_id'] === $creneau['fk_moniteur'] AND $creneau['date'] > date('Y-m-d') AND $creneau['nb_resa'] == 0) {
            $data['suppr_possible'] = 1;
        }

        $data['rdv_possible'] = 0;
        if (is_ok(R_MON)) {
            $data['rdv_possible'] = 0; // un moniteur ne peut prendre rdv !!
        } else if (is_ok(R_ELEV) AND $creneau['date'] > date('Y-m-d') AND $creneau['complet'] == 0) {
            $data['rdv_possible'] = 1;
        }

        $creneau['eleves'] = $this->disponibilite_model->liste_eleves_creneau($id);
//        trace('créneau', $creneau);
//        trace("créneau {$creneau['datefr']}, suppr = {$data['suppr_possible']}, inscr = {$data['rdv_possible']}");
        $data['html'] = $this->load->view('form_creneau_voir_2', $creneau, TRUE);
        $data['id_creneau'] = $id;
        echo json_encode($data);
    }

    public function ajax_add_creneau() {

        $heure = $_REQUEST['hour'];
        $nb_pers = $_REQUEST['nb_pers'];
        $la_date = $_REQUEST['la_date'];
        $id = $_SESSION['user_id'];

        $this->load->model('disponibilite_model');
//        trace('ajax_add_creneau', array('fk_moniteur' => $id,
//            'date' => $la_date,
//            'heure_debut' => $heure,
//            'heure_fin' => date('H:i', strtotime('+2 hour', strtotime($heure))),
//            'nb_pers_max' => $nb_pers));
        if ($this->disponibilite_model->already_creneau($id, $la_date, $heure)) {
            echo ('Vous avez déjà un créneau ...');
            exit();
        }
        $this->disponibilite_model->add(array('fk_moniteur' => $id,
            'date' => $la_date,
            'heure_debut' => $heure,
            'heure_fin' => date('H:i', strtotime('+2 hour', strtotime($heure))),
            'nb_pers_max' => $nb_pers));

        echo 'ok';
        exit();
    }

    public function ajax_action_creneau() {
        $id_creneau = $_REQUEST['id_creneau'];
        $action = $_REQUEST['action'];
        // trace("ajax_action_creneau , $action sur creneau $id_creneau");
        $link = site_url('');
        if ($action == 'inscrire') {
            $statut = $this->disponibilite_model->already_resa($id_creneau, $this->session->user_id);
            if ($statut == RESA_OK OR $statut == RESA_WAIT) {
                echo 'Vous êtes déjà inscrit sur ce créneau horaire';
            } else {
                $id_resa = $this->disponibilite_model->add_resa($id_creneau, $this->session->user_id);

                // TODO : envoyer mail à moniteur
                $creneau = $this->disponibilite_model->get_creneau($id_creneau);

                $ok = $this->disponibilite_model->send_email_resa('resa', $creneau);

                if ($ok == '') {
                    echo 'Le moniteur a été informé de votre inscription.';
                }
            }
        } else if ($action == 'refuser') {

        }
    }

    public function lister_inscriptions() {
        // liste les inscriptions accptées, refusées en attente pour 1 moniteur ou tous moniteurs
        // si la personne est moniteur alors possibilité de changer le staut des inscriptions

        $tab = array();
        if (is_ok(R_ADMIN)) {
            // tous les créneaux de tous les moniteurs nom des éléves et statut. Possibilité de valider, refuser, annuler un créneau
            $tab = $this->disponibilite_model->get_creneau_resa();
        } else if (is_ok(R_MON)) {
            // tous les créneaux futurs par date, nom des éléves et statut. Possibilité de valider, refuser, annuler un créneau
            $tab = $this->disponibilite_model->get_creneau_resa($this->session->user_id);
        } else if (is_ok(R_ELEV)) {
            // tous les créneaux par date nom des moniteurs et statut.Possibilité d'annuler un RDV
            $tab = $this->disponibilite_model->get_creneau_resa(0, $this->session->user_id);
        } else {
            // ERREUR
        }

        $this->load->view('header');
        $this->load->view('menu');

        $this->load->view('creneaux_liste', array('commentaires' => '', 'les_creneaux' => $tab));
        $this->load->view('footer');
    }

    public function ajax_gestion_agenda() {
        $id_objet = $_REQUEST['id'];
        $action = $_REQUEST['action'];
        // $id = $_SESSION['user_id'];
        // trace("ajax_gestion agenda; id=$id_objet, action=$action");
        if ($action == 'confirm') {
            echo $this->disponibilite_model->confirm_resa($id_objet);
        } else if ($action == 'refus') {
            echo $this->disponibilite_model->infirm_resa($id_objet);
        } else if ($action == 'cancel') {
            $message = $this->disponibilite_model->creneau_supprimer($id_objet);
            if ($message !== '') {
                echo $message;
            }
        } else if ($action == 'suppr_rdv') {
            $this->disponibilite_model->annul_resa($id_objet);
            echo 'Inscription RDV supprimée.';
        } else {
            echo 'Oups ... ça \'set mal passé quelque part ...';
        }
        exit();
    }

    public function inscrire($id_creneau) {
        $this->load->view('header');
        $this->load->view('menu');

        $this->load->model('disponibilite_model');
        $this->load->helper('my_html_helper');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');

        $creneau = $this->disponibilite_model->get_creneau($id_creneau);
        $data['date'] = date_fr($creneau['date']);
        $data['heure'] = substr($creneau['heure_debut'], 0, -3);

//        trace('ajax creneau', $creneau);

        $this->load->model('personne_model');
        $eleves = $this->personne_model->get_all('apprenant');

        // on supprime les élèves déjà inscrits sur ce créneau
        $liste_eleves = array();
        foreach ($eleves as $key => $eleve) {
            $statut = $this->disponibilite_model->already_resa($id_creneau, $eleve['rowid']);
            if ($statut !== RESA_OK AND $statut !== RESA_WAIT) {
                $liste_eleves[$eleve['rowid']] = $eleve['nom'] . ' ' . $eleve['prenom'];
            }
        }

        $data['liste_eleves'] = $liste_eleves;
        $data['id_creneau'] = $id_creneau;
        $data['id_moniteur'] = $creneau['fk_moniteur'];


        $this->form_validation->set_rules('id_eleve', 'Nom', 'required');
        $this->form_validation->set_message('required', 'Le champ : {field} doit être renseigné');

        if ($this->form_validation->run() === FALSE AND strlen(validation_errors()) > 0
                AND $this->input->post('valid')) {
            // erreurs !! on recharge la page
            $this->load->view('personne_inscrire', $data);
        } else {
            if ($this->input->post('valid')) {
                // OK on enregistre
                // trace('personne à inscrire, id = ', $this->input->post('id_eleve'));
                // ajouter une résa (id_resa, id_eleve)
                $this->disponibilite_model->add_resa($id_creneau, $this->input->post('id_eleve'), RESA_OK);
                // modifier nb_eleves du créneau ...
                // $this->disponibilite_model->update_nb_resa($id_creneau, 1);

                redirect('agenda/lister_inscriptions');
            } else if ($this->input->post('annul')) {
                redirect('agenda/lister_inscriptions');
            } else {
                // premier affichage
                $this->load->view('personne_inscrire', $data);
            }
        }


        $this->load->view('footer');
    }

}
