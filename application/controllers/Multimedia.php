<?php

defined('BASEPATH') OR exit('Accès direct au script interdit.');

class Multimedia extends CI_Controller {

    /**
     * gère les fichiers uploadés
     */
    public function __construct() {
        parent::__construct();
        test_acces();

        $this->load->helper(array('form', 'url', 'traceur'));
    }

    private function prepare_form_upload($type = 'img') {
        if ($type == 'img') {
            $extensions = 'gif|jpg|png';
            $taille = 2048;
            $le_type = 'image';
            $les_extensions = 'jpeg, jpg, png ou gif';
        } else if ($type == 'vid') {
            $extensions = 'mp4';
            $taille = 2048 * 1024;
            $le_type = 'vidéo';
            $les_extensions = 'mp4';
        } else if ($type == 'pdf') {
            $extensions = 'pdf';
            $taille = 2048;
            $le_type = 'pdf';
            $les_extensions = 'pdf';
        }
        // récupérer les sous-répertoires
        $this->load->helper('directory');
        $repertoire = FCPATH . 'media/' . $type;
        $reps = array();
        $map = directory_map(($repertoire), 1);
        sort($map);
        foreach ($map as $file) {
            if (is_dir($repertoire . '/' . $file)) {
                $reps[$file] = $file;
            }
        }

        $data = array('titre' => "Envoi d'un fichier de type $le_type sur le serveur",
            'message' => "Extensions autorisées :  <strong>$les_extensions</strong>,  de taille inférieure à <strong>$taille</strong> kilo octets",
            'action_link' => 'multimedia/add/' . $type,
            'type' => $type,
            'repertoires' => $reps);
        return $data;
    }

    public function load($type = 'img') {
        test_acces(R_ADMIN, TRUE);
        $this->load->view('header');
        $this->load->view('menu');
        $data = $this->prepare_form_upload($type);
        $this->load->view('form_upload', $data);
        $this->load->view('footer');
    }

    public function add($type = 'img') {
        test_acces(R_ADMIN, TRUE);
        $this->load->view('header');
        $this->load->view('menu');
        // $this->load->helper('form');
        $this->load->library('form_validation');
        $rep = $this->input->post('repertoire');
        if ($rep !== '') {
            $config['upload_path'] = FCPATH . 'media/' . $type . '/' . $rep . '/';
        } else {
            $config['upload_path'] = FCPATH . 'media/' . $type . '/';
        }
        if ($type == 'img') {
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size'] = 2048;
        } else if ($type == 'vid') {
            $config['allowed_types'] = 'mp4';
            $config['max_size'] = 2048 * 1024;
        } else if ($type == 'pdf') {
            $config['allowed_types'] = 'pdf';
            $config['max_size'] = 2048;
        }




        $this->load->library('upload', $config);
        // trace("Files", $_FILES['choosen_file']);

        if (!$this->upload->do_upload('choosen_file')) {
            trace('retour formulaire', $this->input->post(NULL, TRUE));
            if ($this->input->post('annul')) {
                redirect('menu/admin');
                exit();
            }

            $data = $this->prepare_form_upload($type);
            $data['erreurs'] = $this->upload->display_errors();

            $this->load->view('form_upload', $data);
        } else {
            $data = $this->prepare_form_upload($type);
            $data['message'] = 'Le fichier ' . $this->upload->data('file_name') . " a bien été enregistré.<br>Répeter l'opération<br>" . $data['message'];
            $this->load->view('form_upload', $data);
        }

        $this->load->view('footer');
    }

    public function voir($fichier, $type) {
        /*         * *
         *
         */

        $html = '';
        // les / présents dans le nom du fichier ont été transformés par +
        $fichier2 = str_replace('trucmuche_xtron', '/', $fichier);
        // trace($fichier2);
        $lien = 'https://formation.woignier.com/media';
        if ($type == 'img') {
            $html .= "<div style='margin:2rem;'>";
            $html .= "<img src='$lien/img/$fichier2' class='img-fluid' width = '600px'>";
            $html .= "</div>";
        } else if ($type == 'vid') {
            $html .= "<div style='margin:2rem;'>";
            $html .= '<video width="640" height="480" controls>';
            $html .= "<source src='$lien/vid/$fichier2' type='video/mp4'></video>";
            $html .= "</div>";
        } else if ($type == 'pdf') {
            $html .= "<div style='margin:2rem;'>";
            $html .= "<object type='application/pdf' data='$lien/pdf/$fichier2' style='width:960px; height:1000px'></object>";
            $html .= "</div>";
        }
        echo $html;
        // $this->load->view('ecran_message', array('chaine' => $texte));
        //$this->load->view('footer');
    }

    public function liste($type = 'img', $sous_repertoire = '') {

        $this->load->helper('directory');

        $this->load->view('header');
        $this->load->view('menu');

        if ($sous_repertoire == '') {
            $repertoire = FCPATH . 'media/' . $type;
            $commentaires = 'Liste des <strong>' . $type . '</strong>';
        } else {
            $repertoire = FCPATH . 'media/' . $type . '/' . $sous_repertoire;
            $commentaires = 'Liste des <strong>' . $type . ' du dossier : ' . urldecode($sous_repertoire) . '</strong>';
        }
        trace('repertoire =' . $repertoire);
        $map = directory_map(urldecode($repertoire), 1);

        sort($map);
        // on sépare fichiers et répertoires
        $repertoires = $fichiers = array();
        foreach ($map as $fichier) {
            if (strpos($fichier, '/') > 0) {
                $repertoires[] = $fichier;
            } else {
                if ($fichier !== 'index.html')
                    $fichiers[] = $fichier;
            }
        }
        // on recrée le tableau $map en mettant les répertoires à la fin
        $map = $fichiers;
        foreach ($repertoires as $fichier) {
            $map[] = $fichier;
        }             

        $this->load->view('media_liste', array('les_fichiers' => $map, 'commentaires' => $commentaires, 'type' => $type, 'sous_repertoire' => ($sous_repertoire)));


        $this->load->view('footer');
    }

    private function test_upload() {

        // fichier inutile ???


        $msg = '';

        if ($_FILES['choosen_file']['error']) {
            $msg = $_FILES['choosen_file']['error'];
            switch ($_FILES['choosen_file']['error']) {
                case 1 :
                case 2 :
                    $msg = "La taille du fichier est plus grande que la taille autorisée.";
                    break;
                case 3 :
                    $msg = "Erreur de chargement du fichier.";
                    break;
                case 4 :
                    // pas de fichier sa&isi !!
                    $msg = "Pas de fichier saisi";
                    break;
            }
            return $msg;
        }

        // on enregistre
        if (!file_exists($_FILES['imagefile']["tmp_name"])) {
            $msg = "L'envoi du fichier a échoué !";
            return $msg;
        }


        if (!move_uploaded_file($_FILES['choosen_file'], APPPATH . 'data/media/')) {
            $msg = "L'envoi du fichier a rencontré un problème !";
            return $msg;
        }
    }

    public function ajax_suppr() {
        /*         * *
         *
         */

        $type = ($_REQUEST['type']);
        $fichier = ($_REQUEST['file']);
        $repertoire = urldecode($_REQUEST['ss_rep']);
        if ($repertoire !== '') {
            $file = FCPATH . "media/$type/$repertoire/$fichier";
        } else {
            $file = FCPATH . "media/$type/$fichier";
        }
        trace('suppression de ', $file);
        $res = unlink($file);

        if (!$res) {
            $html = 'Erreur suppression de ' . $file;
        } else {
            $html = 'tout ok';
        }
        echo $html;
        exit();
    }

    public function ajax_rename() {
        /*         * *
         *
         */

        $type = ($_REQUEST['type']);
        $fichier = ($_REQUEST['file']);
        $new_name = ($_REQUEST['new_name']);
        $repertoire = urldecode($_REQUEST['ss_rep']);
        if ($repertoire !== '') {
            $old_file = FCPATH . "media/$type/$repertoire/$fichier";
            $new_file = FCPATH . "media/$type/$repertoire/$new_name";
        } else {
            $old_file = FCPATH . "media/$type/$fichier";
            $new_file = FCPATH . "media/$type/$new_name";
        }
        trace('renommer de ', $old_file);
        $html = $type . ' [ ' . $fichier . ' | ' . $new_name;

        if (file_exists($new_file)) {
            $html = "Un fichier nommé $new_name existe déjà !!!";
        } else {
            rename($old_file, $new_file);
            $html = 'ok';
        }

        echo $html;
        exit();
    }

}
