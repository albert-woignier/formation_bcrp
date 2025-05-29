
<div class="starter-template">
    <h1>Saisie modification des joueurs</h1>
    <p><?php echo $commentaires; ?></p>
</div>
<?php echo validation_errors('<span class="alert-danger">', '</span>'); ?>
<div>
    <?php
    echo form_open('score/set_players/' . $table);
    echo form_hidden('table', $table);
    ?>
    <p>
    <h5>Compétition</h5>
    <input type="text" name="competition" value="<?php echo $competition; ?>" size="80" />
</p>
    <p>
    <h5>Joueur 1, Nom Prénom</h5>
    <input type="text" name="nom_1" value="<?php echo $nom_1; ?>" size="50" />
</p>
    <p>
    <h5>Joueur 1, Club</h5>
    <input type="text" name="club_1" value="<?php echo $club_1; ?>" size="50" />
</p>

    <p>
    <h5>Joueur 2, Nom Prénom</h5>
    <input type="text" name="nom_2" value="<?php echo $nom_2; ?>" size="50" />
</p>
    <p>
    <h5>Joueur 2, Club</h5>
    <input type="text" name="club_2" value="<?php echo $club_2; ?>" size="50" />
</p>


<?php
//    trace($nom_ . ' $categorie_e', $categorie_e);
//    trace($nom_ . ' $categorie_m', $categorie_m);
//    trace($nom_ . ' $categorie_i', $categorie_i);
//    trace($nom_ . ' $categorie_a', $categorie_a);
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
