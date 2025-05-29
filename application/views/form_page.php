<div class="starter-template">
    <h1>Saisie modification d'une page d'une séance</h1>
    <p><?php echo $commentaires; ?></p>
</div>
<?php echo validation_errors('<span class="alert-danger">', '</span>'); ?>
<div>
    <?php
    echo form_open('page/add/' . $id);
    echo form_hidden('id', $id);
    ?>

    <h5>Intitulé de la page</h5>
    <input type="text" name="intitule" value="<?php echo $intitule; ?>"  size="50"/>

    <h5>Contenu de la page</h5>
    <textarea name='contenu'  style="width:600px; height:600px;">
        <?php echo $contenu; ?>
    </textarea>
    <script>
        tinymce.init({
            selector: 'textarea',
            plugins: '     autolink lists  media image      table   ',
            toolbar: 'a11ycheck addcomment showcomments casechange checklist code formatpainter pageembed permanentpen table',
            toolbar_mode: 'floating',
            tinycomments_mode: 'embedded',
            tinycomments_author: 'Author name',
        });
    </script>



</div>
<p>
    <?php
    //$this->load->helper('directory');
    //$map = directory_map('./media/', 1);
    //echo print_r($map);
    //echo '<br>' . FCPATH . 'media/';
    ?>
</p>
<div>
    <input type="submit" class="btn btn-success btn-lg btn-block" name="valid" value="Enregistrer">
    <input type="submit" class="btn btn-warning btn-lg btn-block" name="annul" value="Annuler">
</div>
<?php
echo form_close();
