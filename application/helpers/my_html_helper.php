<?php

/**
 *
 */
defined('BASEPATH') OR exit('No direct script access allowed');




/**
 * CodeIgniter Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Albert
 * @link		h
 */
// ------------------------------------------------------------------------

if (!function_exists('print_button_outline')) {

    /**
     *
     */
    function print_button_outline($type, $message) {
        echo "<button type='button' class='btn btn-outline-$type'>" . $message . "</button>";
    }

}

if (!function_exists('button_anchor')) {

    /**
     *
     */
    function button_anchor($controller, $type_button, $text) {
        return anchor($controller, "<button type='button' class='btn btn-outline-$type_button'>" . $text . "</button>");
    }

    function img_anchor($controller, $assets_img, $arr_attibutes) {

        $img = img("assets/img/$assets_img", FALSE, $arr_attibutes);
        return anchor($controller, $img);
    }

    function video_anchor($videofile, $titre) {
        $fichier = html_entity_decode(strip_tags($videofile));
        $lien = 'https://formation.woignier.com/media/tutos/';
        return '<a class="dropdown-item"  href="' . $lien . $fichier . '">' . $titre . '</a>';
    }

}

if (!function_exists('button_anchor_popup')) {

    /**
     *
     */
    function button_anchor_popup($controller, $type_button, $text) {
        return anchor_popup($controller, "<button type='button' class='btn btn-outline-$type_button'>" . $text . "</button>", array());
    }

}


if (!function_exists('replace_page_tags')) {

    function replace_page_tags($contenu) {

        function fct_replace($matches) {
// fonction appelée par preg_replace_callback
            $type = $matches[1];
            $fichier = html_entity_decode(strip_tags($matches[2]));
            $html = '';
            $lien = 'https://formation.woignier.com/media';
            if ($type == 'img') {
                $html .= "<div style='margin:2rem; text-align: center;'>";
                $html .= "<img src='$lien/img/$fichier' class='img-fluid'>";
                $html .= "</div>";
            } else if ($type == 'vid') {
                $html .= "<div style='margin:2rem; text-align: center;'>";
                $html .= '<video width="640" height="480" controls>';
                $html .= "<source src='$lien/vid/$fichier' type='video/mp4'></video>";
                $html .= "</div>";
            } else if ($type == 'pdf') {
                $html .= "<div style='margin:2rem;'>";
                if (intval($_SESSION['pdf_ok']) === 1) {
                    // $html .= "<object type='application/pdf' data='$lien/pdf/$fichier' style='width:100%; height:2000px'></object>";
                    // $html .= "<iframe src = '$lien/pdf/$fichier' width = 980 height = 500></iframe>";
                    $html .= "<iframe src = '$lien/pdf/$fichier' style='width:100%; height:2000px'></iframe>";
                } else {
                    $html .= "<a href='$lien/pdf/$fichier' >Cliquer ici pour voir le fichier pdf</a>";
                }
                $html .= "</div>";
            }
            return $html;
        }

        $pattern = '/\[(img|vid|pdf) ([a-zA-Z0-9 \/_&;\-\.]*)\]/';

        return preg_replace_callback($pattern, 'fct_replace', $contenu);
    }

}


if (!function_exists('get_infos_exam')) {

    function get_infos_exam($infos_exo) {
// les infos sont n@ p p p p@b b
// n = numéro/nom de la figure
// p p p  = les points attribués 1er essai 2 ème essai etc
// b b = les bonus possible (généralement 2 ou 0)
// trace("get_infos_exam , param :$infos_exo");
        // trace(' get info exam  entrée', str_to_hexa($infos_exo));
        $infos = html_entity_decode(strip_tags($infos_exo));
        $infos = str_replace(array("\xa0", "\xc2"), array(" ", " "), $infos);
        // trace(' get info exam  après html entity decode strip tag', str_to_hexa($infos_exo));
        $lg = strlen($infos);
// trace("get_infos_exam lg = $lg,  info :$infos");
        $matches = explode('@', $infos);
        // trace('les matches séparés par @ ', $matches);
        $data = $points = $bonus = array();
        $nb_infos = count($matches);
        if ($nb_infos > 1 AND $nb_infos < 4) {
            $num_exo = (trim($matches[0]));

            // trace('num_exo  ; ', $num_exo);
            $les_points = preg_replace('/(&nbsp;)/', ' ', trim($matches[1]));
            $les_points = preg_replace('/\s\s+/', ' ', trim($les_points));
            // trace(' str to hexa les points avanr explode', str_to_hexa($les_points));
            $points = explode(' ', trim($les_points));
            if ($nb_infos == 3) {
                $les_bonus = preg_replace('/(&nbsp;)/', ' ', trim($matches[2]));
                $les_bonus = preg_replace('/\s\s+/', ' ', trim($les_bonus));
                $bonus = explode(' ', trim($les_bonus));
            }
            $data = array('num_exo' => $num_exo,
                'points' => $points,
                'bonus' => $bonus);
            return $data;
        } else {
            return FALSE;
        }
    }

}

if (!function_exists('get_infos_exam_poche')) {

    function get_infos_exam_poche($infos_exo) {
// les infos sont [poche nom_figure@nb_essai@nb_billes@note_essai_1 note_essai2 note_essai_3@bonus éventuel]

// trace("get_infos_exam , param :$infos_exo");
        // trace(' get info exam  entrée', str_to_hexa($infos_exo));
        $infos = html_entity_decode(strip_tags($infos_exo));
        $infos = str_replace(array("\xa0", "\xc2"), array(" ", " "), $infos);
        // trace(' get info exam  après html entity decode strip tag', str_to_hexa($infos_exo));
        $lg = strlen($infos);
// trace("get_infos_exam lg = $lg,  info :$infos");
        $matches = explode('@', $infos);
        // trace('les matches séparés par @ ', $matches);
        $data = $points =  array();
        $nb_infos = count($matches);
        if ($nb_infos > 1 AND $nb_infos < 6) {
            $num_exo = (trim($matches[0]));
            $nb_essais = preg_replace('/(&nbsp;)/', ' ', trim($matches[1]));
            $nb_billes = preg_replace('/(&nbsp;)/', ' ', trim($matches[2]));
            $les_points = preg_replace('/(&nbsp;)/', ' ', trim($matches[3]));
            $les_points = preg_replace('/\s\s+/', ' ', trim($les_points));
            // trace(' str to hexa les points avanr explode', str_to_hexa($les_points));
            $points = explode(' ', trim($les_points));
            if ($nb_infos == 5) {
                $le_bonus = preg_replace('/(&nbsp;)/', '', trim($matches[4]));
                $le_bonus = preg_replace('/\s\s+/', '', trim($le_bonus));
            } else {
                $le_bonus = 0;
            }
            $data = array('num_exo' => $num_exo,
                'nb_essais' => $nb_essais,
                'nb_billes' => $nb_billes,
                'points' => $points,
                'bonus' => $le_bonus);
            return $data;
        } else {
            return FALSE;
        }
    }

}

if (!function_exists('date_fr')) {

    function int02d($entier) {
        return sprintf("%02d", $entier);
    }

    function date_fr($date_in) {
        if (preg_match("|^([0-9]{4})[-/]{1}([0-9]{1,2})[-/]{1}([0-9]{1,2})|", $date_in, $resultat)) {
            return int02d($resultat[3]) . "-" . int02d($resultat[2]) . "-" . $resultat[1];
        }
    }

    function jour_date($date) {
        $j = array(0 => 'Dim', 1 => 'Lun', 2 => 'Mar', 3 => 'Mer', 4 => 'Jeu', 5 => 'Ven', 6 => 'Sam');
        $tabDate = explode('-', $date);
        $timestamp = mktime(0, 0, 0, $tabDate[1], $tabDate[2], $tabDate[0]);
        return $j[date('w', $timestamp)] . ' ' . date_fr($date);
    }

    function my_timepicker() {
// affiche un bloc <select> pour les créneau horaire
// pour contourner le bug :
// jquery timepicker ne fonctionne pas avec IE11
        $ch = '<select name="le_creneau" id="le_creneau">';
        for ($i = 8; $i < 21; $i++) {
            $ch .= sprintf('<option value="%02d:00">%02d:00</option>', $i, $i);
            $ch .= sprintf('<option value="%02d:30">%02d:30</option>', $i, $i);
        }
        $ch .= '</select>';
        return $ch;
    }

    function str_to_hexa($str) {
        $return = '';
        for ($i = 0; $i < strlen($str); $i++) {
            $return .= '|' . bin2hex(substr($str, $i, 1));
        }
        return $return . '|';
    }

}
