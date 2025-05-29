<div class="starter-template">   
    <h1>Constitution de la séance : <?php echo $seance; ?></h1>
    <p><?php echo $commentaires; ?></p>
</div>
<?php
$this->load->helper('form');


// tableau des id et intitulés
?>

<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Numéro</th>
                <th scope="col">Page</th>
                <th scope="col">Commentaires</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pages as $page) : ?>
                <tr>
                    <td scope="row" ><?php echo $page['id']; ?></td>
                    <td><strong><?php echo $page['page_name']; ?></strong></td>
                    <td>
                        <?php if (strpos($page['info'], 'RREUR') == 1) {
                             echo  '<div class="alert alert-danger" style="margin-bottom:0;" role="alert">'.$page['info'].'</div>';
                        } else {
                            echo  '<div class="alert alert-primary" style="margin-bottom:0;" role="alert">'. $page['info'].'</div>';
                        }
                             ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>

<?php
if ($erreur !== 1) :
echo form_open('seance_build/validation_import');
echo form_hidden('id_seance', $id_seance);
echo form_hidden('tab_pages', $tab_pages);
?>
<div>
    
    <input type="submit" class="btn btn-success btn-lg btn-block" name="valid" value="Valider la constitution de la séance">
    <input type="submit" class="btn btn-warning btn-lg btn-block" name="annul" value="Quitter sans validation">

</div>
<?php
echo form_close();
endif;
