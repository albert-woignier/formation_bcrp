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

if (!function_exists('afficher_tableau')) {

    /**
     *
     */
    function afficher_tableau($array_name, $ident = 0) {
        if (is_array($array_name)) {
            foreach ($array_name as $k => $v) {
                if (is_array($v)) {
                    for ($i = 0; $i < $ident * 10; $i++) {
                        echo "&nbsp;";
                    }
                    echo $k . " => " . "<br>";
                    afficher_tableau($v, $ident + 1);
                } else {
                    for ($i = 0; $i < $ident * 10; $i++) {
                        echo "&nbsp;";
                    }
                    echo $k . " => " . $v . "<br>";
                }
            }
        } else {
            echo "Variable = " . $array_name;
        }
    }

}

if (!function_exists('test_acces')) {

    /**
     * on teste si utilisateur courant est de la catégorie (R_ADMIN, R_MON, R_ELEV, R_INVIT)
     * si $strict == FALSE on renvoie true or false
     * si $STRICT == TRUE on détourne vers page erreur
     */
    function test_acces($categorie = '', $strict = FALSE) {

        if (!isset($_SESSION['user_id'])) {
            // la session n'est pas ouverte
            // on sort vers login
            redirect('/login');
            exit();
        }

        if ($categorie == '') {
            // on voulait juste savoir si session ouverte
            // on a testé au-dessus
            return TRUE;
        } else if (is_ok($categorie)) {
            // ok
            return TRUE;
        } else {
            // l'utilisateur n'a pas les droits requis
            if ($strict) {
                // on va vers page erreur
                redirect('/login/message_err/Accès interdit');
                exit();
            } else {
                //
                return FALSE;
            }
        }
    }

}

if (!function_exists('is_good_personne')) {

    /**
     * on teste si utilisateur courant est de la catégorie (R_ADMIN, R_MON, R_ELEV, R_INVIT)
     * si $strict == FALSE on renvoie true or false
     * si $STRICT == TRUE on détourne vers page erreur
     */
    function is_good_personne($id_personne) {
        if ($id_personne == $_SESSION['user_id']) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}

if (!function_exists('is_ok')) {

    function is_ok($droits_requis, $exact = FALSE) {
        // on gère les droits d'accès aux fonctionnalités
        // $droits_de_l_utilisateur = $this->session->userdata('droits');
        // $exact = FALSE : il suffit que les droits requis soient inférieurs au égaux au niveau de droit de l'utilisateur
        // $exact = TRUE : il faut que l'utilisateur ait exactement lzes droit requis  et pas plus

        $droits_de_l_utilisateur = $_SESSION['droits'];
        if ($droits_de_l_utilisateur > 16 AND $droits_requis == R_DEV)
            return TRUE;
        if ($droits_de_l_utilisateur > 8 AND $droits_requis == R_ADMIN)
            return TRUE;
        if ($droits_de_l_utilisateur > 4 AND $droits_requis == R_MON)
            return TRUE;
        if ($droits_de_l_utilisateur > 2 AND $droits_requis == R_ELEV)
            return TRUE;
        if ($droits_de_l_utilisateur > 0 AND $droits_requis == R_INVIT)
            return TRUE;
        return FALSE;
    }

}

if (!function_exists('set_previous_page')) {

    function set_previous_page() {
        $_SESSION['referred_from'] = current_url();
        // $this->session->set_userdata('referred_from', current_url());
    }

}


if (!function_exists('go_previous_page')) {

    function go_previous_page() {
        $referred_from = $_SESSION['referred_from'];
        redirect($referred_from, 'refresh');
    }

}

if (!function_exists('mois_avant_apres')) {

    function mois_avant_apres($annee, $mois, $sens) {
        // renvoie annee, mois avant ou après +1 ou -1

        if ($sens > 0) {
            $mois++;
            if ($mois > 12) {
                $mois = 1;
                $annee ++;
            }
        } else {
            $mois--;
            if ($mois <= 0) {
                $mois = 12;
                $annee --;
            }
        }
        return array($annee, $mois);
    }

}


if (!function_exists('walk_recursive_directory')) {

    function walk_recursive_directory($array_map, $prefixe) {
        global $file_array;
        $file_array = array();
        foreach ($array_map as $k => $v) {

            if (is_array($v)) {
                walk_recursive_directory($v, $k);
            } else {
                $file_array[] = $prefixe . $v;
            }
        }
        return $file_array;
    }

}
