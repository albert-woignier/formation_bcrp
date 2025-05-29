<div class="starter-template">   
    <h1>Constitution du parcours : <?php echo $parcours; ?></h1>
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
                <th scope="col">Séance</th>
                <th scope="col">Commentaires</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($seances as $seance) : ?>
                <tr>
                    <td scope="row" ><?php echo $seance['id']; ?></td>
                    <td><strong><?php echo $seance['seance_name']; ?></strong></td>
                    <td>
                        <?php if (strpos($seance['info'], 'RREUR') == 1) {
                             echo  '<div class="alert alert-danger" style="margin-bottom:0;" role="alert">'.$seance['info'].'</div>';
                        } else {
                            echo  '<div class="alert alert-primary" style="margin-bottom:0;" role="alert">'. $seance['info'].'</div>';
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
echo form_open('parcours_build/validation_import');
echo form_hidden('id_parcours', $id_parcours);
echo form_hidden('tab_seances', $tab_seances);
?>
<div>
    
    <input type="submit" class="btn btn-success btn-lg btn-block" name="valid" value="Valider la constitution du parcours">
    <input type="submit" class="btn btn-warning btn-lg btn-block" name="annul" value="Quitter sans validation">

</div>
<?php
echo form_close();
endif;
