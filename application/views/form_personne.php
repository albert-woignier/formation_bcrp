
<div class="starter-template">
    <h1>Saisie modification d'une personne</h1>
    <p><?php echo $commentaires; ?></p>
</div>
<?php echo validation_errors('<span class="alert-danger">', '</span>'); ?>
<div>
    <?php
    echo form_open('personne/'.$fonction.'/' . $id);
    echo form_hidden('id', $id);
    ?>
    <p>
    <h5>Nom</h5>
    <input type="text" name="nom" value="<?php echo $nom_; ?>" size="50" />
</p>
<p>
<h5>Prénom</h5>
<input type="text" name="prenom" value="<?php echo $prenom_; ?>" size="50" />
</p>
<p>
<h5>Licence</h5>
<input type="text" name="license" value="<?php echo $license_; ?>" size="50" />
</p>
<p>
<h5>Email</h5>
<input type="email" name="mail" value="<?php echo $mail_; ?>" size="50" />
</p>
<p>
<h5>Téléphone</h5>
<input type="text" name="phone" value="<?php echo $phone_; ?>" size="50" />
</p>
<?php
//    trace($nom_ . ' $categorie_e', $categorie_e);
//    trace($nom_ . ' $categorie_m', $categorie_m);
//    trace($nom_ . ' $categorie_i', $categorie_i);
//    trace($nom_ . ' $categorie_a', $categorie_a);
?>
<div class="container">
    <div class="row">
        <div class="col-3">
            <h5>Catégorie</h5>
            <input type='radio' name='categorie' value="apprenant" <?php echo $categorie_e; ?> />
            <label>apprenant</label><br>
            <input type='radio' name='categorie' value="moniteur" <?php echo $categorie_m; ?> />
            <label>moniteur</label><br>
            <input type='radio' name='categorie' value="invite" <?php echo $categorie_i; ?> />
            <label>invité</label><br>
                <?php if (test_acces(R_ADMIN)) : ?>
                <input type='radio' name='categorie' value="administrateur" <?php echo $categorie_a; ?> />
                <label>administrateur</label><br>
            <?php endif; ?>
            <?php if (test_acces(R_DEV)) : ?>
                <input type='radio' name='categorie' value="dev" <?php echo $categorie_d; ?> />
                <label>developpement</label><br>
            <?php endif; ?>
        </div>
        <?php if ($id != 0) : ?>
            <div class="col-4">
                <h5>Mot de passe</h5>
                <input class="form-check-input" type="checkbox"  name='mdp' value="1"  />
                <label>générer un nouveau mot de passe</label><br>
            </div>
            <div class="col-4" style="background-color: yellow; margin:10px">
                <h5>Statut(non opérationnel)</h5>
            
                 
                <input class="form-check-input" 
                       type="checkbox"  name='statut' value="parti" />

                <label>Cocher si adhérent a quitté le club</label><br>
            </div>
        <?php endif; ?>
        <div class="col">
            &nbsp;&nbsp;
        </div>
    </div>


</div>



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
