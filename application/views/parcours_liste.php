<?php
$this->load->helper('my_html_helper');
$this->load->helper('my_func_helper');
?>
<div class="starter-template">
    <h1>Les parcours de formation</h1>
    <p><?php echo $commentaires; ?></p>
</div>
<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Parcours</th>
                <th scope="col">Discipline</th>
                <th scope="col">Niveau</th>
                <th scope="col">Voir séances</th>
                <?php if (test_acces(R_ADMIN)) : ?>
                    <th scope="col">Construire parcours</th>
                    <th scope="col">Modèle examen</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($les_parcours as $parcours) :
                // trace('les parcours suivis', $parcours);
                ?>
                <tr>
                    <td scope="row"><?php echo $parcours['rowid']; ?></td>
                    <td style="color:blue"><strong>
                            <?php
                            if (is_ok(R_ADMIN)) {
                                echo anchor("parcours/add/" . $parcours['rowid'], $parcours['intitule']);
                            } else {
                                echo $parcours['intitule'];
                            }
                            ?>
                        </strong></td>
                    <td><?php echo $parcours['discipline']; ?></td>
                    <td><?php echo $parcours['niveau']; ?></td>
                    <td>
                        <?php
                        if ($parcours['nb_seance'] > 0) {
                            echo button_anchor("parcours/lister_seances/" . $parcours['rowid'], 'primary', 'Séances');
                        } else {
                            echo is_ok(R_MON) ? 'à construire' : 'à venir ...';
                        }
                        ?>
                    </td>
                    <?php if (test_acces(R_ADMIN)) : ?>
                        <td><?php
                            echo button_anchor("parcours_build/build_from_list/" . $parcours['rowid'], 'danger', 'Liste');
                            ?></td>
                        <td><?php
                            $modele = ($parcours['modele_examen'] != '')?$parcours['modele_examen']:'---';
                            echo anchor("parcours_build/set_modele_examen/" . $parcours['rowid'], $modele);
                            ?></td>
                    <?php endif; ?>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>
