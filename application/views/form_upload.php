
<div class="starter-template">
    <h1><?php echo isset($titre) ? $titre : ''; ?></h1>
    <div class="alert-info alert" role="alert"><?php echo isset($message) ? $message : ''; ?></div>
    <p>
        &nbsp;
    </p>
</div>
<span class="alert-danger"><?php echo isset($erreurs) ? $erreurs : ''; ?></span>
<div style="padding:20px;">
    <?php
    echo form_open_multipart($action_link);
    echo form_fieldset('Sélectionner le fichier sur votre PC, puis cliquer sur "Enregistrer"');
    ?>
    <input type="file" name="choosen_file" size="60" />
    <?php echo form_fieldset_close(); ?>
</div>
<div style="padding:20px;">
    <?php
    if (isset($repertoires)) {
        echo form_fieldset('Dans quel dossier enregistrer le fichier ? ');
        echo form_dropdown('repertoire', $repertoires);
        echo form_fieldset_close();
    }
    ?>

</div>
<p>
    &nbsp;
</p>
<div>
    <input type="submit" class="btn btn-success btn-lg btn-block" name="valid" value="Enregistrer">
    <input type="submit" class="btn btn-warning btn-lg btn-block" name="annul" value="Annuler">
</div>
<?php
echo form_close();
