<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Excel_export extends CI_Controller {

    public function __construct() {
        parent::__construct();
        test_acces();
    }

    function parcours() {
        $this->load->model("parcours_model");
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        /* set column names */
        $table_columns = array("numero", "parcours", "discipline", "niveau", "nb seances");
        $column = 1;
        foreach ($table_columns as $field) {
            $sheet->setCellValueByColumnAndRow($column, 1, $field);
            $column++;
        }
        /* end set column names */
        $parcours_data = $this->parcours_model->get_parcours(); //get your data from model

        $excel_row = 2; //now from row 2

        foreach ($parcours_data as $row) {
            $sheet->setCellValueByColumnAndRow(1, $excel_row, $row['rowid']);
            $sheet->setCellValueByColumnAndRow(2, $excel_row, $row['intitule']);
            $sheet->setCellValueByColumnAndRow(3, $excel_row, $row['discipline']);
            $sheet->setCellValueByColumnAndRow(4, $excel_row, $row['niveau']);
            $sheet->setCellValueByColumnAndRow(5, $excel_row, $row['nb_seance']);
            $excel_row++;
        }
        $object_writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Liste_parcours.xls"');
        $object_writer->save('php://output');
    }

    function seances() {
        $this->load->model("seance_model");
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        /* set column names */
        $table_columns = array("Numéro", "Séance", "nb pages");
        $column = 1;
        foreach ($table_columns as $field) {
            $sheet->setCellValueByColumnAndRow($column, 1, $field);
            $column++;
        }
        /* end set column names */
        $seance_data = $this->seance_model->get_seance(); //get your data from model

        $excel_row = 2; //now from row 2

        foreach ($seance_data as $row) {
            $sheet->setCellValueByColumnAndRow(1, $excel_row, $row['rowid']);
            $sheet->setCellValueByColumnAndRow(2, $excel_row, $row['intitule']);
            $sheet->setCellValueByColumnAndRow(3, $excel_row, $row['nb_page']);
            $excel_row++;
        }
        $object_writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Liste_séances"');
        $object_writer->save('php://output');
    }

    function pages() {
        $this->load->model("page_model");
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        /* set column names */
        $table_columns = array("Numéro", "Pages", "taille");
        $column = 1;
        foreach ($table_columns as $field) {
            $sheet->setCellValueByColumnAndRow($column, 1, $field);
            $column++;
        }
        /* end set column names */
        $seance_data = $this->page_model->liste(); //get your data from model

        $excel_row = 2; //now from row 2

        foreach ($seance_data as $row) {
            $sheet->setCellValueByColumnAndRow(1, $excel_row, $row['rowid']);
            $sheet->setCellValueByColumnAndRow(2, $excel_row, $row['intitule']);
            $sheet->setCellValueByColumnAndRow(3, $excel_row, $row['size']);
            $excel_row++;
        }
        $object_writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Liste_pages"');
        $object_writer->save('php://output');
    }

}
