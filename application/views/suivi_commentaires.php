<div class="starter-template">
    <h1>Suivi de <?php echo $infos['eleve']; ?></h1>
</div>
<div class="container">
    <div class="row">
        <div class="col-4 bg-light  text-right m-2">
            Parcours
        </div>
        <div class="col  bg-warning m-2">
            <?php echo $infos['parcours']; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-4 bg-light  text-right m-2">
            Séance
        </div>
        <div class="col   bg-warning m-2">
            <?php echo $infos['seance']; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-4 bg-light  text-right m-2">
            Moniteur
        </div>
        <div class="col  bg-warning m-2">
            <?php echo $infos['moniteur']; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-4 bg-light  text-right m-2">
            Date
        </div>
        <div class="col  bg-warning m-2">
            <?php echo date_fr($infos['date_seance']); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-4 bg-light  text-right m-2">
            Commentaires du moniteur
        </div>
        <div class="col  bg-light font-weight-bold m-2">
            <?php echo $commentaires; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-4 bg-light  text-right m-2">
            Evaluation de l'apprentissage
        </div>
        <div class="col  bg-light font-weight-bold m-2">
            <?php echo $evaluation; ?>
        </div>
    </div>

</div>
<hr>
<?php 
if ($examen) : 
    echo $html_exam;
endif;