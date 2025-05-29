
<div class="starter-template">
    <h1>Suivi de <?php echo $personne_name; ?></h1>
    <p><?php echo $commentaires; ?></p>
</div>
<?php echo validation_errors('<span class="alert-danger">', '</span>'); ?>
<div id="formulaire">
    <?php
    echo form_open('suivi/inscrire/' . $id_personne);
    echo form_hidden('id_personne', $id_personne);
    ?>

    <h5>Parcours</h5>
    <?php echo form_dropdown('id_parcours', $liste_parcours_non_suivis, '', 'required="required"'); ?>

    <p>
        &nbsp;
    </p>
    <div>
        <input type="submit" class="btn btn-success btn-lg btn-block" name="valid" value="Inscrire au parcours">
        <input type="submit" class="btn btn-warning btn-lg btn-block" name="annul" value="Annuler">
    </div>

</div>
<?php
echo form_close();
?>

<div>
    <h4>Les parcours suivis</h4>
    <div>

        <div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Parcours</th>
                        <th scope="col">Situation</th>
                        <th scope="col">Inscription</th>
                        <th scope="col">Terminé</th>
                        <?php if (test_acces(R_ADMIN) OR test_acces(R_MON) OR is_good_personne($id_personne)) : ?>
                            <th scope="col">Détails</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($les_parcours_suivis as $parcours) : ?>
                        <?php
                        $style = ($parcours['etat'] !== 'en cours') ? 'style = "background-color :  #d5dbdb  ;"' : '';
                        ?>
                        <tr  <?php echo $style; ?>>
                            <td><?php echo $parcours['rowid']; ?></td>
                            <td style="color:blue"><strong><?php echo $parcours['intitule']; ?></strong></td>
                            <td>
                                <?php
                                if ($parcours['fk_etat_suivi'] == SEANCE_PARCOURS_VALIDE) {
                                    echo img("assets/img/crown64.gif");
                                } else {
                                    echo $parcours['etat'];
                                }
                                ?>

                            </td>
                            <td><?php echo date_fr($parcours['date_inscription']); ?></td>
                            <td><?php echo date_fr($parcours['date_fin']); ?></td>
                            <?php if (test_acces(R_ADMIN) OR test_acces(R_MON) OR is_good_personne($id_personne)) : ?>
                                <td><?php echo button_anchor("suivi/seances/$id_personne/{$parcours['rowid']}", 'primary', 'Détails'); ?></td>
                            <?php endif; ?>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>
