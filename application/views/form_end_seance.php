
<div class="starter-template">
    <h1>Finalisation d'une séance</h1>
    <p>Séance : <?php echo $seance; ?>, Apprenant : <?php echo $eleve; ?></p>
</div>
<?php echo validation_errors('<span class="alert-danger">', '</span>'); ?>
<div class='container'>
    <?php
    echo form_open('suivi/seance_valider/' . $id_parcours . '/' . $id_seance . '/' . $id_eleve, 'id="my_form"');
    ?>
    <p>
    <h5>Commentaires éventuels sur la séance</h5>
    <?php
    $data = array(
        'name' => 'comments',
        'value' => '',
        'rows' => '6',
        'cols' => '80'
    );
    echo form_textarea($data);
    ?>
</p>

<div class="container">
    <div class="row">
        <div class="col">
            <h5>Evaluation de l'Apprentissage</h5>
            <!--     -->
            <?php foreach ($choix_eval as $indice => $eval) : ?>
                <input type="radio" name="evaluation" value="<?php echo $indice; ?>" <?php echo set_radio('evaluation', $indice); ?> />
                <label><?php echo $eval; ?></label><br>
            <?php endforeach; ?>
        </div>
        <?php
        if ($type_exam == 1) :
            ?>
            <div class="col">
                <h5>Nombre de points obtenus</h5>
                <?php echo $nb_points; ?>
            </div>
            <?php
        endif;
        ?>

    </div>
</div>



</div>
<p>
    &nbsp;
    <input type="hidden" name="nb_points" value="<?php echo $nb_points; ?>">
    <input type="hidden" id='do_what' name='do_what' value="">
</p>
<div class="container">
    <div class="row">
        <div class="col">
            <input type="button"  onclick="go_go('valid')" class="btn btn-success btn-lg btn-block" name="valid" value="Succès">
        </div>

        <div class="col">
            <input type="button"  onclick="go_go('un_valid')" class="btn btn-secondary btn-lg btn-block" name="un_valid" value="Séance à refaire">
        </div>

    </div>
    <br>
    <div class="row">
        <div class="col">
            <input type="button"  onclick="go_go('annul')" class="btn btn-warning btn-lg btn-block" name="annul" value="Annuler">
        </div>
    </div>
</div>
<p>
    &nbsp;
</p>
<?php
if (isset($html_exam) ) :
    //
    // on affiche les data html du tableau des résultats
    //
    echo $html_exam;
    ?>

    <?php
endif;
?>


<?php
echo form_close();
?>
<script>


    // alert("form action = " + $("#my_form").attr('action'));
    function go_go(what) {
//        $("#my_form").submit(function () {
        $("#do_what").val(what);
        // submit form
        var retour = $("#my_form").serializeArray();
        $.post($("#my_form").attr('action'), $("#my_form").serializeArray());
        // alert
        alert("La fenetre va se fermer ");
        // close window
        window.opener.location.reload();
        window.close();
        // return
        return false;
    }



</script>
