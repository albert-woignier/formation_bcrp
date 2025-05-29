<?php $this->load->helper('my_html_helper'); ?>
<div class="starter-template">
    <h1>Les pages de la séance <?php echo $seance; ?></h1>
    <p><?php echo $commentaires; ?></p>
</div>

<?php if ($nb_pages > 0) : ?>
    <div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Ordre</th>
                    <th scope="col">#</th>
                    <th scope="col">Page</th>
                    <th scope="col">Voir</th>
                    <?php if (test_acces(R_ADMIN)) : ?>
                        <th scope="col">Modifier</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($les_pages as $page) : ?>
                    <tr>
                        <td><strong><?php echo $page['ordre']; ?></strong></td>
                        <td scope="row"><?php echo $page['id']; ?></td>
                        <td><strong><?php echo $page['intitule']; ?></strong></td>
                        <td><?php
                            if ($page['size'] != 0) {
                                echo button_anchor_popup('page/voir/' . $page['id'], 'primary', 'Voir');
                            } else {
                                echo '-';
                            }
                            ?></td>
                        <?php if (test_acces(R_ADMIN)) : ?>
                            <td>
                                <?php echo button_anchor('page/add/' . $page['id'], 'primary', 'Modifier'); ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
<?php else : ?>

    <h3>Cette séance ne contient aucune page pour l'instant.</h3>

<?php endif;
