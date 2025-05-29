<div class="starter-template">   
    <h1>Les séances à associer au parcours <?php echo $parcours; ?></h1>
    <p><?php echo $commentaires; ?></p>
</div>
<div>
    <?php
    $this->load->helper('form');

    echo form_open('select');

    // tableau des id et intitulés
    foreach ($seances as $seance) :
        ?>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="<?php echo $seance['rowid']; ?>" name="seance[]">
            <label class="form-check-label" >
    <?php echo $seance['intitule']; ?>
            </label>
        </div>
    <?php endforeach; ?>
    <div>
    <button type="submit" class="btn btn-success btn-lg btn-block">Valider le choix des séances</button>
<button type="submit" class="btn btn-warning btn-lg btn-block">Quitter sans validation</button>
    </div>
    <?php
    echo form_close();
    ?>


</div>
