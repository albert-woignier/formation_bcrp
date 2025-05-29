
<div class="starter-template">
    <h1>Saisie modification d'une séance</h1>
    <p><?php echo $commentaires; ?></p>
</div>
<?php echo validation_errors('<span class="alert-danger">', '</span>'); ?>
<div class="starter-template">
    <?php
    echo form_open('seance/add/' . $id);
    echo form_hidden('id', $id);
    ?>

    <h5>Intitulé de la séance (255 caractères maxi)</h5>
    <input type="text" name="intitule" value="<?php echo $intitule; ?>" size="120" />

</div>
<div class="starter-template">
    &nbsp;
</div>
<div class="starter-template">
    <fieldset>
        <h5>Séance de type examen</h5>

        <input type="checkbox"  name='examen' value="1" <?php echo $exam; ?> />
        <label>Cocher la case si il s'agit d'une séance avec figures notées </label>
    </fieldset>

</div>
<p>
    &nbsp;
</p>
<div class="starter-template">
    <input type="submit" class="btn btn-success btn-lg btn-block" name="valid" value="Enregistrer">
    <input type="submit" class="btn btn-warning btn-lg btn-block" name="annul" value="Annuler">
</div>
<?php
echo form_close();
