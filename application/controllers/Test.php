<?php

defined('BASEPATH') OR exit('Accès direct au script interdit.');

class Test extends CI_Controller {

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

    public function zx_update_ca_br() {
        
        return;

        $this->load->view('header');
        $this->load->view('menu');

        $data = array();
        $output = '';
        $this->load->database($this->session->data_base_group);

        $query = "SELECT p.rowid, p.intitule, p.contenu FROM ff_page as p "
                . "ORDER BY p.rowid ASC";
        $result = $this->db->query($query);

        foreach ($result->result_array() as $row) {
            $contenu = $row['contenu'];
            // $pattern = '/(\[exo .*\])/';
            $pattern = '/(\[img CA OR.*\])/';
            if (preg_match($pattern, $contenu, $matches)) {
                $info_exo = $matches[1];
                $info = str_replace(array("CA AR/", "CA OR/"), array("CA_AR/", "CA_OR/"), $info_exo);

                $output .= "$info_exo ----> $info<br>";

                $contenu_new = preg_replace($pattern, $info, $contenu);

                $where = "rowid = " . $row['rowid'];
                $page = array('contenu' => $contenu_new);
                $query = $this->db->update_string('ff_page', $page, $where);
                $result = $this->db->query($query);
                if ($result) {
                    $data[] = $row['intitule'] . 'OK ';
                } else {
                    $data[] = $row['intitule'] . ' pas OK OK ' . $result;
                }
            }
        }

        $this->load->view('ecran_message', array('chaine' => $output, 'tableau' => $data));
        $this->load->view('footer');
    }

    public function zx_mk_db_bcrp() {

        $this->load->database('bcrp');

        $this->db->close();
    }

    public function test() {
        //
        //
        $this->load->view('header');
        $this->load->view('menu');

//        $this->load->database($this->session->data_base_group);
//        $query = "SELECT rowid, intitule "
//                . " FROM ff_page "
//                . " WHERE  contenu LIKE '%_modif.pdf%' ";
//
//        $result = $this->db->query($query);
//        $tab = $result->result_array();
//
//        foreach ($tab as $page) {
//
//        }
//        $where = "rowid = $id_resa";
//        $query = $this->db->update_string('ff_resa', array('statut' => 'refus'), $where);
//
//        $result = $this->db->query($query);
//        if ($result) {
//            return TRUE;
//        } else {
//            trace('Disponibilite_model', 'erreur cancel_resa !! ' . $result);
//            return FALSE;
//        }
//
//        $this->load->view('ecran_message', array('tableau' => $tab));

        $psswd_crypted = crypt('bcrp', '$6$rounds=5000$yorky$');
        $this->load->view('ecran_message', array('message' => $psswd_crypted));
        
        $jsonobj = '[{"Peter":35,"Ben":37,"Joe":43}, {"Jon":40,"yu":37,"toto":43}]';

        var_dump(json_decode($jsonobj, true));

        $this->load->view('ecran_message', array('message' => print_r(json_decode($jsonobj, true), true)));
        $this->load->view('footer');
    }

    public function nb_lignes() {
        //
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->helper('directory');
        $total_lignes = 0;
        $nb_fic_total = 0;
        $total_functions = 0;
        $chaine = '<br>';
        $reps = array('controllers', 'models', 'views', 'helpers');
        foreach ($reps as $rep) {
            $fichiers = directory_map(APPPATH . $rep, 1);
            // echo '===> ' . $rep . '<br>';
            $nb = 0;
            $nb_fic = 0;
            $nb_func = 0;
            foreach ($fichiers as $fichier) {
                if (is_dir((APPPATH . $rep . '/' . $fichier)))
                    continue;
                // echo '------> ' . $fichier;
                $nb_fic++;
                $nb_fic_total++;
//
//                $tab = file(APPPATH . $rep . '/' . $fichier);
//                $nb_lignes = count($tab);
//
                $contenu = file_get_contents(APPPATH . $rep . '/' . $fichier);
                $nb_lignes = preg_match_all('~\n~', $contenu);
                $nb_func += mb_substr_count($contenu, ' function ');

                // echo ' -- ' . $nb_lignes . ' lignes<br>';
                $nb += $nb_lignes;
            }
            // echo '<strong>--------------> TOTAL ' . $rep . " = $nb lignes</strong><br>";
            $chaine .= "<strong>$nb</strong> lignes de code dans $nb_fic fichiers du répertoire <strong>$rep</strong>  (~~" . intdiv($nb, $nb_fic) . " lignes/fichier), et $nb_func fonctions <br>";
            $total_lignes += $nb;
            $total_functions += $nb_func;
        }
        // echo "<strong>--------------> TOTAL GENERAL  = $total lignes</strong><br>";
        $chaine .= "<br><strong>TOTAL de $total_lignes lignes de code dans $nb_fic_total fichiers et $total_functions fonctions.</strong><br>";

        $this->load->view('ecran_message', array('chaine' => $chaine));
        $this->load->view('footer');
    }

    public function ajax_test() {
        trace('ajax exo', $_REQUEST['exo']);
        trace('ajax note', $_REQUEST['note']);
        $note = intval($_REQUEST['note']);
        $bonus = intval($_REQUEST['bonus']);
        $nu_exo = intval($_REQUEST['exo']);
        $_SESSION['total'] += $note + $bonus;
        $_SESSION['notation'][$nu_exo] = $note;
        echo $_SESSION['total'];
        exit;
    }

    public function test_excel() {
        $this->load->view('header');
        $this->load->view('menu');
        // $this->load->helper('directory');
        $this->load->model('suivi_model');
        $this->suivi_model->examen_excel_test();
        $this->load->view('footer');
    }

    public function enregistrer_examens_historique() {
        // à partir d'un fichier excel
        
        exit;
             
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->model('parcours_model');
        $this->load->model('suivi_model');
        $excel_file = 'Saisie_historique_examensV3.xls';
        $this->load->helper('my_excel_helper');
        $filename = FCPATH . 'media/xls/' . $excel_file;
        $tab = read_excel($filename);
        $nblignes = count($tab[1]);
        $out = '';
        $moniteurs  = $eleves = '';
        $index = 1;

        $out .= sprintf("nombre de lignes = %d <br>", $nblignes);

        while ($index < $nblignes) {
            
            if ($tab[1][$index]['A'] == 'Numéro_Séance') {
                $index++;
                $out .= sprintf(" new, index %d <br>", $index);
                // on est sur un exam
                $id_seance = $tab[1][$index]['A'];
                $id_parcours = $tab[1][$index]['C'];
                $nom_moniteur = $tab[1][$index]['D'];
                $id_moniteur = $tab[1][$index]['E'];
                $nom_eleve = $tab[1][$index]['F'];
                $id_eleve = $tab[1][$index]['G'];
                $date = $tab[1][$index]['H'];
                $commentaires = $tab[1][$index]['K'];
                
                $total = 0;
                $moniteurs .= sprintf("Moniteur %s --%d<br>",$nom_moniteur,$id_moniteur );
                $eleves .= sprintf("Eleve %s --%d<br>",$nom_eleve,$id_eleve );
                $index++;
                $index++;
                $notations['notation'] = array();
                while ($tab[1][$index]['A'] !== 'TOTAL' AND $index < $nblignes) {
                    // on enregistre la note
                    $fig = $tab[1][$index]['A'];
                    $note = intval($tab[1][$index]['B']);
                    $bonus = isset($tab[1][$index]['C']) ? intval($tab[1][$index]['C']) : -1;
                    $notations['notation'][] = array('num_exo' => $fig, 'note' => $note, 'bonus' => $bonus);
                    $total += $note;
                    if ($bonus != -1) {
                        $total += $bonus;
                    }
                    $out .= sprintf("%s : %d + %d => %d<br>", $fig, $note, $bonus, $total);
                    $index++;
                }
                
                
                
                // on crée la notation à la séance

                $data = array('commentaires' => $commentaires, 'evaluation' => 3, 'nb_points' => $total, 'examen' => serialize($notations));
                
                $rowid_notation = 0;

                $this->db->insert('ff_seance_notation', $data);
                $rowid_notation = $this->db->insert_id(); // id inséré

                //
                //On crée le suivi 
                // ordre de la séance
                $ordre = $this->parcours_model->get_ordre_seance($id_parcours, $id_seance);
                
                $out .= sprintf("le %s, %s avec %s parcours %s, séance %s, ordre = %d, commentaire : %s <br>", 
                        $date, $nom_eleve, $nom_moniteur, $id_parcours, $id_seance, $ordre, $commentaires);
                $out .= sprintf("       index %d et TOTAL = %d <br>", $index, $total);
                
                $data = array('fk_eleve' => $id_eleve,
                    'fk_moniteur' => $id_moniteur,
                    'fk_parcours' => $id_parcours,
                    'fk_seance' =>$id_seance,
                    'ordre_seance' => $ordre,
                    'date_seance' => $date,
                    'validation' => SEANCE_PARCOURS_VALIDE,
                    'fk_notation' => $rowid_notation);
                $this->db->insert('ff_personne_suivi', $data);
                
                // on update le parcours_suivi succés parcours et date fin
                $this->suivi_model->update_inscription($id_eleve, $id_parcours, SEANCE_PARCOURS_VALIDE, $date);
            
            
            }
            $index++;
        }
        

        $this->load->view('ecran_message', array('chaine' => $moniteurs.$eleves.$out));
        $this->load->view('footer');
    }

    public function enregistrer_seance_historique() {
        
        exit;
        
        // à partir d'un fichier excel
        if($_SESSION['data_base_group'] !== 'test') exit();
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->model('parcours_model');
        $this->load->model('suivi_model');
        $excel_file = 'Saisie_de_l\'historique_BASE TEST.xlsx';
        $this->load->helper('my_excel_helper');
        $filename = FCPATH . 'media/xls/' . $excel_file;
        $tab = read_excel($filename);
        $premiere_ligne = TRUE;
        $text = '';
        $old_eleve = 0;
        foreach ($tab[1] as $ligne) {
            if ($premiere_ligne OR $ligne['A'] == '') {
                // on saute 1ère ligne en-tête.
                $premiere_ligne = FALSE;
                continue;
            }
            if ($ligne['G'] !== $old_eleve) {
                // on change élève
                // on inscrit élève au parcours
                $tab = array('fk_personne' => $ligne['G'],
                    'fk_parcours' => $ligne['C'],
                    'fk_etat_suivi' => $ligne['I'],
                    'date_inscription' => $ligne['H']);
                $query = $this->db->insert_string('ff_personne_parcours', $tab);
                $result = $this->db->query($query);

                $old_eleve = $ligne['G'];
            }
            // on crée la notation à la séance
            $commentaires = '';
            if (isset($ligne['K'])) {
                $commentaires = $ligne['K'];
            }
            $data = array('commentaires' => $commentaires, 'evaluation' => $ligne['J'], 'nb_points' => 0, 'examen' => NULL);
            $this->db->insert('ff_seance_notation', $data);
            $rowid_notation = $this->db->insert_id(); // id inséré
            // ordre de la séance
            $ordre = $this->parcours_model->get_ordre_seance($ligne['C'], $ligne['A']);

            $data = array('fk_eleve' => $ligne['G'],
                'fk_moniteur' => $ligne['E'],
                'fk_parcours' => $ligne['C'],
                'fk_seance' => $ligne['A'],
                'ordre_seance' => $ordre,
                'date_seance' => $ligne['H'],
                'validation' => 1,
                'fk_notation' => $rowid_notation);
            $this->db->insert('ff_personne_suivi', $data);

            // $text .= "seance = {$ligne['A']} ; parcours = {$ligne['C']} ; moni = {$ligne['E']} ; elev = {$ligne['G']} ; date = {$ligne['H']} ; <br>";
        }


        $this->load->view('ecran_message', array('chaine' => $text));
        $this->load->view('footer');
    }

    public function update_page_pdf() {
        exit;

        $this->load->view('header');
        $this->load->view('menu');

        $data = array();
        $output = '';
        if ($this->session->data_base_group !== 'test') exit('pas bon');
        $this->load->database($this->session->data_base_group);

        $query = "SELECT rowid, intitule, contenu FROM ff_page "
                . "WHERE intitule LIKE 'CA %' "
                . "ORDER BY rowid ASC";
        $result = $this->db->query($query);

        foreach ($result->result_array() as $row) {
            $output .= $row['intitule'] . '<br>';
            $seance =  substr($row['intitule'], 3,2);
            $output .= $seance;
            if ($seance == 'BR' OR $seance == 'AR' OR $seance == 'OR') {
                $replacement = 'pdf CA_'.$seance.'/';
            } else {
                continue;
            }
            $contenu = $row['contenu'];
            // $pattern = '/(\[exo .*\])/';
            // $pattern = '/(\[img CA OR.*\])/';
            $pattern = '/(\[pdf .*\])/';
            if (preg_match($pattern, $contenu, $matches)) {
                $info_pdf = $matches[1];
                $info = str_replace("pdf ", $replacement, $info_pdf);
                $contenu_new = preg_replace($pattern, $info, $contenu);
                $output .= $contenu_new . '<br>';

                $where = "rowid = " . $row['rowid'];
                $page = array('contenu' => $contenu_new);
                $query = $this->db->update_string('ff_page', $page, $where);
                $result = $this->db->query($query);
                
 
                
                if ($result) {
                    $output .= $row['intitule'] . 'OK <br>';
                } else {
                    $output .= $row['intitule'] . ' pas OK OK ' . $result . '<br>';
                }
            }
        }

        $this->load->view('ecran_message', array('chaine' => $output));
        $this->load->view('footer');
    }
    
    public function test_updt_mdp() {
        $this->load->view('header');
        $this->load->view('menu');
        $psswd_crypted = crypt('romans', '$6$rounds=5000$yorky$');
        $output = '';
        if ($this->session->data_base_group !== 'test') exit('pas bon');
        $this->load->database($this->session->data_base_group);

        $where = "categorie = 'moniteur'" ;
        $output .= 'Moniteur : ';
        $personne = array('password' => $psswd_crypted);
        $query = $this->db->update_string('ff_personne', $personne, $where);
        $result = $this->db->query($query);
        if ($result) {
            $output .= ' OKOK ';
        } else {
            $output .= ' PROBLEME PROBLEME ';
        }
        
        
        $output .= 'Administrateur : ';
        $psswd_crypted = crypt('delay32', '$6$rounds=5000$yorky$');
        $where = "categorie = 'administrateur'" ;
        $personne = array('password' => $psswd_crypted);
        $query = $this->db->update_string('ff_personne', $personne, $where);
        $result = $this->db->query($query);
        if ($result) {
            $output .= ' OKOK ';
        } else {
            $output .= ' PROBLEME PROBLEME ';
        }
        
        
        
        
        $this->load->view('ecran_message', array('chaine' => $output));
        
    }
    
    public function test_diff() {
        
        $this->load->database($this->session->data_base_group);
        
            $data_where = array('fk_notation' => 499);
        $query = $this->db->get_where('ff_personne_suivi', $data_where, 1);
        $exists = $query->row_array();
        if (isset($exists)) {
            // est-ce un bug ??? à surveiller
            // SELECT TIMESTAMPDIFF(MINUTE,
            //         (Select stamp from ff_personne_suivi where fk_notation = 499),
            //         CURRENT_TIMESTAMP);
            $stamp = $exists['stamp'];
            $query = $this->db->query("SELECT TIMESTAMPDIFF(MINUTE,'$stamp',CURRENT_TIMESTAMP) as delay");
            $diff = $query->row();
            if (isset($diff)) {
                   trace('diff minutes = '. $diff->delay);
            }
            $query = $this->db->query("SELECT TIMESTAMPDIFF(SECOND,'$stamp',CURRENT_TIMESTAMP) as delay");
            $diff = $query->row();
            if (isset($diff)) {
                   trace('diff secondes = '. $diff->delay);
            }
        }
        
    }
        

}


