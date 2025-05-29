
<div class="starter-template">
    <h1><?php echo $titre; ?></h1>
    <p><?php echo $commentaires; ?></p>
</div>
<?php echo validation_errors('<span class="alert-danger">', '</span>'); ?>
<div>
    <?php
    echo form_open($link);
    echo form_hidden('id', $id);
    ?>

    <h5>Saisir ici la liste des numéros des <?php echo $objets; ?> séparés par une virgule.</h5>
    <p>Eventuellement la liste actuelle des <?php echo $objets; ?> est affichée.</p>
    <textarea name='liste'  style="width:600px; height:200px;"><?php echo $liste; ?></textarea>



</div>
<div>
    <input type="submit" class="btn btn-success btn-lg btn-block" name="valid" value="Enregistrer">
    <input type="submit" class="btn btn-warning btn-lg btn-block" name="annul" value="Annuler">
</div>
<?php
echo form_close();
