
<div class="starter-template">
    <h1>Inscription d'une personne</h1>
    <p>Au créneau du <?php echo $date; ?> à <?php echo $heure; ?></p>
</div>
<?php echo validation_errors('<span class="alert-danger">', '</span>'); ?>
<div id="formulaire">
    <?php
    echo form_open('agenda/inscrire/' . $id_creneau);
    echo form_hidden('id_moniteur', $id_moniteur);
    ?>

    <h5>Liste des apprentis</h5>
    <?php echo form_dropdown('id_eleve', $liste_eleves, '', 'required="required"'); ?>

    <p>
        &nbsp;
    </p>
    <div>
        <input type="submit" class="btn btn-success btn-lg btn-block" name="valid" value="Inscrire">
        <input type="submit" class="btn btn-warning btn-lg btn-block" name="annul" value="Annuler">
    </div>

</div>
<?php
echo form_close();
?>

