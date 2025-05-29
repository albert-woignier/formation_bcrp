
<div class="starter-template">   
    <h1>Saisie modification d'un parcours</h1>
    <p><?php echo $commentaires; ?></p>
</div>
<?php echo validation_errors('<span class="alert-danger">', '</span>'); ?>
<div>
    <?php echo form_open('parcours/add/'.$id); 
    echo form_hidden('id', $id); ?>

    <h5>Intitulé du parcours (255 caractères)</h5>
    <input type="text" name="intitule" value="<?php echo $intitule; ?>" size="120" />

    <h5>Discipline du parcours</h5>
    <?php echo $list_box_disciplines; ?>

    <h5>Niveau du parcours</h5>
     <?php echo $list_box_niveaux; ?>
    <p>
    <h5>Modèle Excel pour l'examen : "<?php echo $modele_examen; ?>"</h5>
    </p>
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
