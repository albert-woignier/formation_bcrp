<?php $this->load->helper('my_html_helper'); ?>
<div class="starter-template">
    <h1><?php echo $titre; ?></h1>
    <p><?php echo $commentaires; ?></p>
</div>

<?php if ($nb_pages > 0) : ?>
    <div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Page</th>
                    <th scope="col">Voir la page</th>
                    <th scope="col">Modifier</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pages as $page) : ?>
                    <tr>
                        <td ><?php echo $page['rowid']; ?></td>
                        <td><strong><?php echo $page['intitule']; ?></strong></td>
                        <td>
                            <?php
                            if ($page['size'] > 0) {
                                echo button_anchor_popup('page/voir/' . $page['rowid'], 'primary', 'Voir');
                            } else {
                                echo 'vide';
                            }
                            ?>
                        </td>
                        <td><?php echo button_anchor('page/add/' . $page['rowid'], 'primary', 'Modifier'); ?></td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
<?php else : ?>

    <h3>Il n'y a pas de pages enregistrées.</h3>

<?php endif; ?>

