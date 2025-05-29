<?php

/**
 * 
 */
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

if (!function_exists('read_excel')) {

    /**
     * 
     */
    function read_excel($file_name) {

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_name);

        $sheet = $spreadsheet->getActiveSheet();

// Store data from the activeSheet to the varibale in the form of Array
        $data = array(1, $sheet->toArray(null, true, true, true));

// Display the sheet content 
        return($data);
    }

}


