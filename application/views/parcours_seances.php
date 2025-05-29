<?php $this->load->helper('my_html_helper'); ?>
<div class="starter-template">
    <h1>Les séances du parcours <?php echo $parcours; ?></h1>
    <p><?php echo $commentaires; ?></p>
</div>

<?php if ($nb_seances > 0) : ?>
    <div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="text-center" scope="col">Ordre</th>
                    <?php if (test_acces(R_ADMIN)) : ?>
                        <th class="text-center" scope="col">#</th>
                    <?php endif; ?>
                    <th scope="col">Séance</th>
                    <th scope="col">Type</th>
                    <th class="text-center" scope="col">Contenu</th>
                    <?php if (test_acces(R_ADMIN)) : ?>
                        <th class="text-center" scope="col">Construire séance</th>
                    <?php endif; ?>

                </tr>
            </thead>
            <tbody>
                <?php foreach ($les_seances as $seance) : ?>
                    <tr>
                        <td><strong><?php echo $seance['ordre']; ?></strong></td>
                        <?php if (test_acces(R_ADMIN)) : ?>
                            <td><?php echo $seance['id']; ?></td>
                        <?php endif; ?>
                        <td style="color:blue"><strong>
                                <?php
                                if (test_acces(R_ADMIN)) :
                                    echo anchor("seance/add/" . $seance['id'], $seance['intitule']);
                                else :
                                    echo $seance['intitule'];
                                endif;
                                ?>
                            </strong>
                        </td>
                        <td class="text-center" >
                            <?php
                            if ($seance['type'] == 1) :
                                echo 'examen';
                            else :
                                echo ' -- ';
                            endif;
                            ?>
                        </td>
                        <td class="text-center" >
                            <?php
                            if ($seance['nb_page'] > 0) {
                                if (test_acces(R_MON)) {
                                    echo button_anchor("seance/lister_pages/" . $seance['id'], 'primary', 'Pages');
                                    echo '&nbsp;&nbsp;&nbsp;';
                                }
                                echo button_anchor_popup("seance/derouler_seance/" . $seance['id'], 'primary', 'Consulter');
                            } else {
                                echo 'à construire';
                            }
                            ?>
                        </td>
                        <?php if (test_acces(R_ADMIN)) : ?>
                            <td class="text-center" >
                                <?php
                                echo button_anchor("seance_build/import/" . $seance['id'], 'primary', 'Excel');
                                echo '&nbsp;&nbsp;&nbsp;';
                                echo button_anchor("seance_build/build_from_list/" . $seance['id'], 'danger', 'Liste');
                                ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
<?php else : ?>

    <h3>Ce parcours ne contient aucune séance pour l'instant.</h3>

<?php endif;
