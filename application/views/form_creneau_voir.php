<!------   view du dialog pop up embeded dans calendrier.php -->

<div style="font-size: 0.8em">
    <div class="row">
        <div class="col text-right">
            Places disponibles
        </div>
        <div class="col text-left">
            <?php echo $disponibilite; ?>
        </div>
    </div>
    <div class="row">
        <div class="col text-right">
            Moniteur
        </div>
        <div class="col text-left">
            <strong><?php echo $moniteur; ?></strong>
        </div>
    </div>
    <div class="row">
        <div class="col text-right">
            Jour
        </div>
        <div class="col text-left">
            <?php echo $datefr; ?>
        </div>
    </div>
    <div class="row">
        <div class="col text-right">
            de
        </div>
        <div class="col text-left">
            <?php echo substr($heure_debut, 0, -3) . ' à ' . substr($heure_fin, 0, -3); ?>
        </div>
    </div>
    <?php
    foreach ($eleves as $eleve) :
        ?>
        <div class="row">
            <div class="col text-right">
                <?php echo $eleve['statut']; ?>
            </div>
            <div class="col text-left">
                <?php echo $eleve['eleve']; ?>
            </div>
        </div>
        <?php
    endforeach;
    ?>
</div>
