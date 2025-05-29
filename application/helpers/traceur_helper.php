<?php

/**
 *
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter HTML Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Albert
 * @link		h
 */
// ------------------------------------------------------------------------

if (!function_exists('trace')) {

    /**
     *
     */
    function trace($titre, $str_or_array = "") {

        // debug : écrit dans le fichier trace
        $fichier = APPPATH . 'logs/' . 'trace.php';

        $user = isset($_SESSION["user_nom"]) ? $_SESSION["user_nom"] : "?";
        $f = fopen($fichier, "a");
        fwriteln($f, "==TRACE===================================");
        fwriteln($f, "*** " . date("d-m-Y H:i:s") . " *** ($user)");
        fwriteln($f, "*** script : " . uri_string());
        fwriteln($f, "*** titre  : " . $titre);

        if ($str_or_array != "") {
            if (is_array($str_or_array)) {
                fwriteln($f, print_r($str_or_array, TRUE));
            } else if (is_object($str_or_array)) {
                $vars = get_object_vars($str_or_array);
                fwriteln($f, print_r($vars, TRUE));
            } else {
                fwriteln($f, "*** messge : " . $str_or_array);
            }
        }
        fwriteln($f, "==========================================");
        fclose($f);
    }

}

if (!function_exists('fwriteln')) {

    function fwriteln($fic, $chaine = "") {
        // ecrite dans fichier avec saut de ligne
        fwrite($fic, $chaine . "\n");
    }

}


