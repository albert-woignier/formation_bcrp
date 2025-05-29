<?php $this->load->helper('my_html_helper'); ?>
<div class="starter-template">
    <h1><?php echo $titre; ?></h1>
    <p><?php echo $commentaires; ?></p>
</div>
<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col"><?php echo $affichage; ?></th>
                <th scope="col">-----</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($objets as $objet) : ?>
                <tr>
                    <td><strong><?php echo $objet; ?></strong></td>
                    <td>
                        <?php
                        if ($affichage == 'Tables') {
                            echo button_anchor("trace/show_champs/" . $objet, 'primary', 'les champs');
                            echo button_anchor("trace/show_data/" . $objet, 'primary', 'dump table');
                        } else {
                            echo '------';
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>
