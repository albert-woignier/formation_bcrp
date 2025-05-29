<div class="starter-template">
    <h1>Saisie modification des scores en cours</h1>

</div>
<?php echo validation_errors('<span class="alert-danger">', '</span>'); ?>
<div>
    <?php
    echo form_open('score/saisie_resultats/');
    ?>
    <h5>Saisir les scores </h5>
    <p>LAMBIASE 45 (0) - BONGIOVANI 27 (1)<br>
        COSTA FREITAS 47 (1) - GUERIN 60 (2)<br> etc.<!-- comment -->
    </p>
    <textarea name='texte'  style="width:600px; height:600px;">
        <?php echo $texte; ?>
    </textarea>
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
