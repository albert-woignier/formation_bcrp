<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Parcous_model
 *
 * @author albert
 */
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Suivi_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database($this->session->data_base_group);
        test_acces();
        // évaluation d'une session
        if (!$this->session->has_userdata('evaluation')) {
            $evaluation = array();
            $evaluation[1] = 'quelques difficultés';
            $evaluation[2] = 'satisfaisant';
            $evaluation[3] = 'avec facilités';
            $this->session->set_userdata('evaluation', $evaluation);
        }
    }

    public function inscrire($id_pers, $id_parcours) {
        // fk_etat_suivi 1 == 'en cours'
        $tab = array('fk_personne' => $id_pers,
            'fk_parcours' => $id_parcours,
            'fk_etat_suivi' => 1,
            'date_inscription' => date('Y-m-d'));
        $query = $this->db->insert_string('ff_personne_parcours', $tab);
        $result = $this->db->query($query);
//        trace('insert inscription', $result);
        return $result;
    }

    public function update_inscription($id_pers, $id_parcours, $motif, $date = FALSE) {
        // $table (array('nom'=>'trucmuche', 'prenom'=>'zozo', etc
        $table = array('fk_etat_suivi' => $motif);
        if ($date) {
            $table['date_fin'] = $date;
        }
        $where = " fk_personne = $id_pers  AND fk_parcours = $id_parcours ";
        $query = $this->db->update_string('ff_personne_parcours', $table, $where);

        trace('update_inscription', $query);

        $result = $this->db->query($query);
        if ($result) {
            return TRUE;
        } else {
            trace('update_inscription', 'erreur b!!!!!!!');
            return FALSE;
        }
    }

    public function inscriptions_liste($id_pers) {
        $data = array();
        $query = "SELECT PP.fk_parcours, PP.fk_etat_suivi, SP.etat as etat, PP.date_inscription, PP.date_fin, PA.rowid, PA.intitule  "
                . " FROM ff_personne_parcours as PP "
                . " LEFT JOIN ff_parcours as PA ON PP.fk_parcours = PA.rowid "
                . " LEFT JOIN ff_t_suivi_parcours AS SP ON  PP.fk_etat_suivi = SP.rowid"
                . " WHERE PP.fk_personne = $id_pers "
                . " ORDER BY PP.date_inscription DESC";
        $result = $this->db->query($query);

        foreach ($result->result_array() as $row) {
            $data[] = $row;
        }
        return $data;
    }

    public function is_inscrit($id_pers, $id_parcours) {
        // la personne est-elle inscrite à ce parcours
        $query = "SELECT * FROM ff_personne_parcours "
                . "WHERE fk_personne = $id_pers AND fk_parcours = $id_parcours";
        $result = $this->db->query($query);
        $row = $result->row_array();
        if (isset($row)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function lister_seances_suivies($id_eleve, $id_parcours) {
        $data = array();
        $query = "SELECT PS.date_seance, PS.ordre_seance, PS.validation, PS.fk_seance as id_seance, PS.fk_notation, S.intitule as seance, PE.nom as moniteur "
                . " FROM ff_personne_suivi as PS"
                . " LEFT JOIN ff_seance as S ON S.rowid = PS.fk_seance"
                . " LEFT JOIN ff_personne as PE ON PE.rowid = PS.fk_moniteur"
                . " WHERE PS.fk_eleve = $id_eleve AND PS.fk_parcours = $id_parcours "
                . " ORDER BY PS.date_seance ASC";
        // trace('lister_seances_suivies', $query);
        $result = $this->db->query($query);
        // trace('lister_seances_suivies', $result);
        foreach ($result->result_array() as $row) {
            $data[] = $row;
        }
        return $data;
    }

    public function get_next_seance($id_parcours, $id_last_seance = 0) {
        // on renvoie la prochaine séance au même format que lister_seances_suivies
        $requete = "SELECT '0000-00-00' as date_seance, ps.ordre as ordre_seance, 10 as validation, ps.fk_seance as id_seance, s.intitule as seance,"
                . " '???' as moniteur FROM ff_parcours_seance as ps "
                . " LEFT JOIN ff_seance as s on ps.fk_seance = s.rowid "
                . " WHERE ps.fk_parcours = $id_parcours ORDER BY ps.ordre ASC";
        $resultat = $this->db->query($requete);
        $tab_seances = $resultat->result_array();
        if (count($tab_seances) == 0) {
            return array(); // le parcours ets vide
        }
        if ($id_last_seance == 0) {
            // on veut la première séance du parcours
            return $tab_seances[0];
        }
        // sinon
        for ($i = 0; $i < count($tab_seances); $i++) {
            if ($tab_seances[$i]['id_seance'] == $id_last_seance) {
                if ($i + 1 == count($tab_seances)) {
                    // il n'y a pas d'autre sénaces
                    return FALSE;
                } else {
                    return $tab_seances[$i + 1];
                }
            }
        }
    }

    public function enregistrer_seance($tableau) {
        // si $etat = succès et séance est la dernière du parcours alors parcours success !!
        // $id_eleve, $id_moniteur, $id_parcours, $id_seance, $etat, $commentaires
               
        // BUG saisies en double ... 2021 04 
        // vérifier si séance déjà enregistrée
        $data_where = array('fk_eleve' => $tableau['id_apprenant'],
            'fk_moniteur' => $tableau['id_moniteur'],
            'fk_parcours' => $tableau['id_parcours'],
            'fk_seance' => $tableau['id_seance'],
            'validation' => $tableau['validation']);
        $query = $this->db->get_where('ff_personne_suivi', $data_where, 1);
        $exists = $query->row_array();
        if (isset($exists)) {
            // est-ce un bug ??? à surveiller

            $stamp = $exists['stamp'];
            $query = $this->db->query("SELECT TIMESTAMPDIFF(MINUTE,'$stamp',CURRENT_TIMESTAMP) as delay");
            $diff = $query->row();
            $minutes = $diff->delay;
            if ($minutes < 4) {
                trace('BUG BUG : on SHUNTE un enregistre à nouveau une séance à MOINS de 4 minutes !!!');
                return;
            } else {
                trace('BUG BUG : on enregistre à nouveau une séance à PLUS de 4 minutes !!!');
            }
        }
        
        $data = array('commentaires' => $tableau['commentaires'], 'evaluation' => $tableau['evaluation'], 'nb_points' => $tableau['nb_points'], 'examen' => $tableau['examen']);
        $this->db->insert('ff_seance_notation', $data);
        $rowid_notation = $this->db->insert_id(); // id inséré

        $this->load->model('parcours_model');
        $ordre = $this->parcours_model->get_ordre_seance($tableau['id_parcours'], $tableau['id_seance']);

        $data = array('fk_eleve' => $tableau['id_apprenant'],
            'fk_moniteur' => $tableau['id_moniteur'],
            'fk_parcours' => $tableau['id_parcours'],
            'fk_seance' => $tableau['id_seance'],
            'ordre_seance' => $ordre,
            'date_seance' => date('y-m-d'),
            'validation' => $tableau['validation'],
            'fk_notation' => $rowid_notation);
        $this->db->insert('ff_personne_suivi', $data);
    }
    
    

    public function get_notation($id_notation) {
        $requete = "SELECT * FROM ff_seance_notation WHERE rowid = $id_notation";
        trace('requete', $requete);
        $resultat = $this->db->query($requete);

        $notation = $resultat->row_array();
        $evaluation = $this->session->userdata('evaluation');
        if ($notation['evaluation']) {
            $notation['evaluation'] = $evaluation[$notation['evaluation']];
        } else {
            $notation['evaluation'] = 'Non évalué';
        }
        // trace ('tableau notation',$notation);
        return $notation;
    }

    public function get_infos_notation($id_notation) {
        $requete = "SELECT CONCAT(P.nom, ' ',P.prenom) as moniteur,
            CONCAT(PE.nom, ' ',PE.prenom) as eleve,
            Par.intitule as parcours,
            Sea.intitule as seance,
            PS.date_seance as date_seance
            FROM ff_personne_suivi as PS
            LEFT JOIN ff_personne as P ON P.rowid = PS.fk_moniteur
            LEFT JOIN ff_personne as PE ON PE.rowid = PS.fk_eleve
            LEFT JOIN ff_parcours as Par ON Par.rowid = PS.fk_parcours
            LEFT JOIN ff_seance as Sea ON Sea.rowid = PS.fk_seance
            Where PS.fk_notation =  $id_notation";
        $resultat = $this->db->query($requete);
        $infos = $resultat->row_array();
        return $infos;
    }

    public function examen_excel($examen) {

        // $examen, tableau
        trace('excel_carambole, modèle = --'.$examen['modele_excel'].'--' );
        
        if ($examen['modele_excel'] == '' OR strlen($examen['modele_excel']) < 5) {
            trace('excel_carambole','New spreadsheet');
            $spreadsheet = new Spreadsheet();
        } else {
            $filename = FCPATH . 'media/modeles_examen/' . $examen['modele_excel'];
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filename);
        }
        
        $sheet = $spreadsheet->getActiveSheet();
        // on écrit le parcours, la séance, l'élève, la date, le moniteur
        $excel_row = 4;
        $sheet->setCellValueByColumnAndRow(1, $excel_row, "Ce jour ");
        $sheet->setCellValueByColumnAndRow(2, $excel_row, date("d-m-Y"));
        $excel_row++;

        $sheet->setCellValueByColumnAndRow(1, $excel_row, "Nom du club organisateur :");
        $sheet->setCellValueByColumnAndRow(2, $excel_row, "Billard Club Romanais Péageois (BCRP)");
        $excel_row++;

        $sheet->setCellValueByColumnAndRow(1, $excel_row, "Ligue ");
        $sheet->setCellValueByColumnAndRow(2, $excel_row, "Ligue Auvergne-Rhône-Alpes");
        $excel_row++;


        $sheet->setCellValueByColumnAndRow(1, $excel_row, "Nom candidat, club");
        $sheet->setCellValueByColumnAndRow(2, $excel_row, $examen['nom_eleve'] . ', club BCRP');
        $excel_row++;
        $sheet->setCellValueByColumnAndRow(1, $excel_row, "Licence FFB n° ");
        $sheet->setCellValueByColumnAndRow(2, $excel_row, $examen['licence_eleve']);
        $excel_row++;
        $sheet->setCellValueByColumnAndRow(1, $excel_row, "Parcours ");
        $sheet->setCellValueByColumnAndRow(2, $excel_row, $examen['nom_parcours']);
        $excel_row++;
        $sheet->setCellValueByColumnAndRow(1, $excel_row, "Séance ");
        $sheet->setCellValueByColumnAndRow(2, $excel_row, $examen['nom_seance']);
        $excel_row++;
        $sheet->setCellValueByColumnAndRow(1, $excel_row, "Nom Moniteur, club");
        $sheet->setCellValueByColumnAndRow(2, $excel_row, $examen['nom_moniteur'] . ', club BCRP');
        $excel_row++;
        $excel_row++;
        $sheet->setCellValueByColumnAndRow(1, $excel_row, "Points obtenus pour chacun des exercices");
        $excel_row++;
        /* set column names */
        $table_columns = array("Exercice", "points", "bonus", "total");
        $column = 1;
        foreach ($table_columns as $field) {
            $sheet->setCellValueByColumnAndRow($column, $excel_row, $field);
            $column++;
        }
        /* end set column names */
        $total_note = 0;
        $total_bonus = 0;
        $is_bonus = false;
        $excel_row++;
        foreach ($examen['notation'] as $row) {
            $sheet->setCellValueByColumnAndRow(1, $excel_row, $row['num_exo']);
            $sheet->setCellValueByColumnAndRow(2, $excel_row, $row['note']);
            if ($row['bonus'] !== -1) {
                $sheet->setCellValueByColumnAndRow(3, $excel_row, $row['bonus']);
                $sheet->setCellValueByColumnAndRow(4, $excel_row, $row['note'] + $row['bonus']);
                $is_bonus = true;
                $total_bonus += $row['bonus'];
            } else {
                $sheet->setCellValueByColumnAndRow(3, $excel_row, '---');
                $sheet->setCellValueByColumnAndRow(4, $excel_row, $row['note']);
            }
            $total_note += $row['note'];
            $excel_row++;
        }
        // ligne de totaux
        $sheet->setCellValueByColumnAndRow(1, $excel_row, 'TOTAUX');
        $sheet->setCellValueByColumnAndRow(2, $excel_row, $total_note);
        if ($is_bonus) {
            $sheet->setCellValueByColumnAndRow(3, $excel_row, $total_bonus);
            $sheet->setCellValueByColumnAndRow(4, $excel_row, $total_note + $total_bonus);
        } else {
            $sheet->setCellValueByColumnAndRow(3, $excel_row, '---');
            $sheet->setCellValueByColumnAndRow(4, $excel_row, $total_note);
        }

        // enregister
        $eleve_date = $examen['nom_eleve'] . ' ' . date("d-m-Y");
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save(FCPATH . 'media/examens/' . $eleve_date . '.xlsx');

        // envoyer par mail : éleve, moniteur, administrateur

        $this->load->library('email');
        $this->email->from('formation@billard-romans.fr', 'Formation BCRP');
        $this->email->bcc('woignier@gmail.com');
        $this->email->to(array($examen['mail_moniteur'], $examen['mail_eleve']));
        $this->email->cc('bcrpromans@gmail.com');
        // $this->email->bcc('them@their-example.com');
        $this->email->attach(FCPATH . 'media/examens/' . $eleve_date . '.xlsx');
        $this->email->subject('[BCRP Formation] Résultat examen ' . $eleve_date);
        $this->email->message('En pièce jointe les résultats de la séance examen.');

        $this->email->send();
    }

    public function examen_excel_poche($examen) {
        // $examen, tableau
        trace('excel_poche, modèle = --'.$examen['modele_excel'].'--' );
        if ($examen['modele_excel'] == '' OR strlen($examen['modele_excel']) < 5) {
            trace('excel_poche','New spreadsheet');
            $spreadsheet = new Spreadsheet();
        } else {
            $filename = FCPATH . 'media/modeles_examen/' . $examen['modele_excel'];
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filename);
        }
        
        $sheet = $spreadsheet->getActiveSheet();
        // on écrit le parcours, la séance, l'élève, la date, le moniteur
        $toprint = array();
        $toprint[] = array('Ce jour', date("d-m-Y"));
        $toprint[] = array("Nom du club organisateur :", "Billard Club Romanais Péageois (BCRP)");
        $toprint[] = array("Ligue :", "Ligue Auvergne-Rhône-Alpes");
        $toprint[] = array("Nom candidat, club", $examen['nom_eleve'] . ', club BCRP');
        $toprint[] = array("Licence FFB n° ", $examen['licence_eleve']);
        $toprint[] = array("Parcours", $examen['nom_parcours']);
        $toprint[] = array("Séance", $examen['nom_seance']);
        $toprint[] = array("Nom Moniteur, club", $examen['nom_moniteur'] . ', club BCRP');
        $toprint[] = array(" ", " "); // saut de ligne  
        $toprint[] = array("Points obtenus pour chacun des exercices");
        
         /* set column names */
        $toprint[] =  array("Exercice", "essai 1", "essai 2", "essai 3", "Points");
        
        // on exploite les lignes de chaque figure
        $total_note = 0;
        
        foreach ($examen['notation'] as $row) {
            // num exo, essai 1, essai2, essai3 avec bonus, total
            // essai = '55---' ou '---'ou '-' '3 +2' si bonus (<> -1) A VOIR
            // avec test si bonus on écrit note +b0 ou note +b2
            // et on n'oublie pas de faire le total
            $essais = json_decode($row['essais']);
            $toprint[] = array($row['num_exo'], 
                str_replace('o','-',$essais[0]->serie),
                str_replace('o','-',$essais[1]->serie),
                str_replace('o','-',$essais[2]->serie),
                $row['note']);
            $total_note += $row['note'];
        }

        
        // les totaux
        $toprint[] = array("TOTAL", "", "", "", $total_note);
//
//        AU final 
//
        $excel_row = 4; // on commence à la 4ème ligne
        foreach ($toprint as $ligne) {
            $col = 1;
            foreach ($ligne as $cellule) {
                $sheet->setCellValueByColumnAndRow($col, $excel_row, $cellule);
                $col++;
            }
            $excel_row++;
        }
        // et voilà !!

        // enregister

        $eleve_date = $examen['nom_eleve'] . ' ' . date("d-m-Y");
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save(FCPATH . 'media/examens/' . $eleve_date . '.xlsx');

        // envoyer par mail : éleve, moniteur, administrateur

        $this->load->library('email');
        $this->email->from('formation@billard-romans.fr', 'Formation BCRP');
        $this->email->bcc('woignier@gmail.com');
        $this->email->to(array($examen['mail_moniteur'], $examen['mail_eleve']));
        $this->email->cc('bcrpromans@gmail.com');
        $this->email->attach(FCPATH . 'media/examens/' . $eleve_date . '.xlsx');
        $this->email->subject('[BCRP Formation] Résultat examen ' . $eleve_date);
        $this->email->message('En pièce jointe les résultats de la séance examen.');

        $this->email->send();
    }

}
