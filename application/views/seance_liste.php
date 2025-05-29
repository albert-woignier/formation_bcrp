<?php $this->load->helper('my_html_helper'); ?>
<div class="starter-template">
    <h1><?php echo $titre; ?></h1>
    <p><?php echo $commentaires; ?></p>
</div>
<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="text-center" scope="col">#</th>
                <th scope="col">Séance</th>
                <th class="text-center" scope="col">Type examen</th>
                <th class="text-center" scope="col">Contenu</th>
                <?php if (test_acces(R_ADMIN)) : ?>
                    <th class="text-center" scope="col">Construire séance</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($les_seances as $seance) : ?>
                <tr>
                    <td scope="row"><?php echo $seance['rowid']; ?></td>
                    <td style="color:blue"><strong><?php echo anchor("seance/add/" . $seance['rowid'], $seance['intitule']); ?></strong></td>
                    <td class="text-center">
                        <?php
                        if ($seance['type'] == 1) {
                            echo 'EXAMEN';
                        } else {
                            echo ' -- ';
                        }
                        ?>
                    </td>
                    <td class="text-center" >
                        <?php
                        if ($seance['nb_page'] > 0) {
                            echo button_anchor("seance/lister_pages/" . $seance['rowid'], 'primary', 'Pages');
                            echo '&nbsp;&nbsp;&nbsp;';
                            echo button_anchor_popup("seance/derouler_seance/" . $seance['rowid'], 'primary', 'Commencer');
                        } else {
                            echo 'à construire';
                        }
                        ?>
                    </td>

                    <?php if (test_acces(R_ADMIN)) : ?>
                        <td class="text-center" ><?php
                            echo button_anchor("seance_build/import/" . $seance['rowid'], 'primary', 'Excel');
                            echo '&nbsp;&nbsp;&nbsp;';
                            echo button_anchor("seance_build/build_from_list/" . $seance['rowid'], 'danger', 'Liste');
                            ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>
