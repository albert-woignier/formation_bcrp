<div class="starter-template">
    <h1>
        <?php
        if (isset($titre)) {
            echo $titre;
        }
        ?>
    </h1>
    <?php if (isset($message)) : ?>
        <div class="alert-info alert" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <hr>

    <?php
    if (isset($agenda)) :
        $this->load->helper('my_html_helper');
        ?>

        <div class="container agenda">
            <div class="row "> <!---  mois courant et liens    --->
                <div class="col-sm lien_avant"><?php echo button_anchor($mois_precedent, 'info  btn-lg mb-2', 'mois précédent'); ?></div>
                <div class="col-sm mois_courant"><?php echo print_button_outline('info  btn-lg mb-2', "<strong>$mois_courant</strong>"); ?></div>
                <div class="col-sm lien_apres"><?php echo button_anchor($mois_suivant, 'info  btn-lg mb-2', 'mois suivant'); ?></div>
            </div>
<?php
            $j = array(1 => 'Lu', 2 => 'Ma', 3 => 'Me', 4 => 'Je', 5 => 'Ve', 6 => 'Sa', 7 => 'Di');

            foreach ($agenda as $no_week => $week) :
                ?>
                <div class="row week container">
                    <?php
                    foreach ($week as $no_jour_week => $jour) :
                        // quel jour sommes-nous, et style 
                        $jour_courant02=sprintf('%02d', $jour['jour']); // jour courant sur 2 caractères avec 0
                        $today_style = 'semaine';
                        if  ($Y_m.$jour_courant02 < date("Ymd")) {
                            $today_style = 'yesterday';
                        } else if ($no_jour_week == 6 OR $no_jour_week == 7 ) {
                                $today_style = 'week_end';
                        }
                        
                        // 1er test jour vide ? si oui on 
                        if ($jour['jour'] == '') {
                            // carré vide sans couleur
                            echo '<div class="col jour container p-2 rounded">'
                            . '</div>';
                            continue;
                        } // sinon 
                        ?>
                        <div class="col jour <?php echo $today_style; ?> container p-2 rounded">
                                <div  class="row container">
                                <?php if (is_ok(R_MON) AND $Y_m.$jour_courant02 > date("Ymd")) : ?>
                                        <div class="col day add text-center" data-date="<?php echo $jour['date']; ?>"  >
                                            <?php echo $j[$no_jour_week]. ' '. $jour['jour']; ?>
                                        </div>
                                <?php else : ?>
                                    <div class="col day  text-center">
                                        <?php echo ($jour['jour'] == '') ? '&nbsp;' : $j[$no_jour_week]. ' '. $jour['jour']; ?>
                                    </div>
                                <?php endif; ?>
                                </div>

                            <?php
                            foreach ($jour['creneaux'] as $creneau) :
                                $cre_text = substr($creneau['heure_debut'], 0, 5) . ' : ' . strtoupper($creneau['initiales']);
                                if ($creneau['complet'] == 1) {
                                    $img = "bullet_ball_red.png";
                                } else {
                                    $img = "bullet_ball_glass_green.png";
                                }
                                ?>
                                <div class="row creneau" data-creneau="<?php echo $creneau['rowid']; ?>">
                                    <div class="col">
                                        <strong><?php echo $cre_text; ?></strong>&nbsp;
                                        <img src="<?php echo base_url(); ?>assets/img/<?php echo $img; ?>"  >
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            &nbsp;

                        </div>
                        <?php
                    endforeach;
                    ?>
                </div>
                <?php
            endforeach;
            ?>

            <div class="row "> <!---  mois courant et liens    --->
                <div class="col-sm lien_avant"><?php echo button_anchor($mois_precedent, 'info  btn-lg mb-2', 'mois précédent'); ?></div>
                <div class="col-sm mois_courant"><?php echo print_button_outline('info  btn-lg mb-2', "<strong>$mois_courant</strong>"); ?></div>
                <div class="col-sm lien_apres"><?php echo button_anchor($mois_suivant, 'info  btn-lg mb-2', 'mois suivant'); ?></div>
            </div>

        </div>


    <?php endif; ?>


</div>

<!---
Div de saisie d'un nouveau créneau
-->

<div id="dialog_add_creneau" title="Saisie disponibilité ">

    <p class="validateTips">Saisir le créneau horaire.</p>

    <form>
        <fieldset>
            <label for="le_creneau">Heure début du créneau de 2 heures</label>

            <p>
                <?php echo my_timepicker(); ?>
            </p>
            <label for="nb_pers">Nombre d'apprenants maxi</label>
            <p>
                <input type='number' min="1" max="5" value = "1" name="nb_pers" id="nb_pers">
            </p>

            <!-- Allow form submission with keyboard without duplicating the dialog button -->
            <input type = "submit" tabindex = "-1" style = "position:absolute; top:-1000px" >
        </fieldset>
    </form>

</div>

<!---
Div qui recoit le contenu d'un créneau via un appel ajax
-->


<div id="dialog_show_creneau" title="Détail créneau horaire">

    <div class ="container" id='infos_creneau'>


    </div>


    <div class ="container" id='annuler' display="none">
        <p>
            Les apprenants inscrits seront prévenus par mail de cette annulation.<br>
            Prévenez les par téléphone si le délai est court.
        </p>
        <p>
            <button id="annulation" data-creneau="">Supprimer ce créneau</button>
        </p>
    </div>

    <div  class ="container" id='inscrire' display="none">
        <p>
            Cliqyez pour vous inscrire, le moniteur sera prévenu par mail.
        </p>
        <p>
            <button id="inscription" data-creneau="">S'inscrire</button>
        </p>

    </div>
</div>




<script>
    var dialog;
    var dialog_add;
    var le_jour_selected;
    var id_creneau;
    $().ready(function () {
        jquery_communs();
        $(".creneau").click(function () {
            var id = $(this).attr('data-creneau');
            id_creneau = id;
            $.post(site_url('agenda/ajax_voir_creneau'), {id: id},
            function (data) {
                // do whatever data.variable ...
                // remplir info_creneau
                console.log(data);
                $("#infos_creneau").html(data.html);
                $("#inscription").attr('data-creneau', id_creneau);
                $("#annulation").attr('data-creneau', id_creneau);

                if (data.rdv_possible == 0) {
                    $("#inscrire").hide();
                } else {
                    $("#inscrire").show();
                }

                if (data.suppr_possible == 0) {
                    $("#annuler").hide();
                } else {
                    $("#annuler").show();
                }

                dialog.dialog('open');

            }, "json");
        });
        $(".add").click(function () {
            le_jour_selected = $(this).attr('data-date');
            dialog_add.dialog('open');
            return false;
        });

        dialog = $("#dialog_show_creneau").dialog({
            autoOpen: false,
            height: 400,
            width: 360,
            modal: true,
            buttons: {
                "Annuler": function () {
                    dialog.dialog("close");
                }
            },
            close: function () {
                dialog.dialog("close");
            }
        });

//        $('.timepicker').timepicker({
//            'timeFormat': 'H:i',
//            'minTime': '8:00am',
//            'maxTime': '8:00pm',
//            'useSelect': true
//        });

        dialog_add = $("#dialog_add_creneau").dialog({
            autoOpen: false,
            height: 400,
            width: 350,
            modal: true,
            buttons: {
                "Ajouter créneau": function () {
                    alert('Créneau à ajouter : ' + $('#le_creneau').val());
                    $.post(site_url('agenda/ajax_add_creneau'), {
                        hour: $('#le_creneau').val(),
                        nb_pers: $('#nb_pers').val(),
                        la_date: le_jour_selected},
                    function (data) {
                        if (data !== 'ok')
                            alert(data);
                        dialog_add.dialog("close");
                        location.reload();
                    }, "text");
                },
                "Annuler": function () {
                    dialog_add.dialog("close");
                }
            },
            close: function () {

                dialog_add.dialog("close");
            }


        });

        $("#inscription").click(function () {
            $.post(site_url('agenda/ajax_action_creneau'), {
                action: 'inscrire',
                id_creneau: id_creneau},
            function (data) {
                // TODO : que fait-on au retour ???
                alert(data);
                dialog.dialog("close");
                location.reload();
            }, "text");
            return false;
        });

        $("#annulation").click(function () {
            // alert($(this).attr('data-creneau'));
            if (!confirm('Attention : cette opération va annuler la séance et envoyer un mail aux apprenants qui sont inscrits.\n Voulez-vous continuer ?')) {
                dialog.dialog("close");
                return false;
            }
            $.post(site_url('agenda/ajax_gestion_agenda'), {
                action: 'cancel',
                id: id_creneau},
            function (data) {
                if (data)
                    alert(data);
                dialog.dialog("close");
                location.reload();
            }, "text");
            return false;
        });

        $(".day").hover(
                function () {
                    $(this).removeClass("day");
                    $(this).addClass("day-vert");
                }, function () {
            $(this).removeClass("day-vert");
            $(this).addClass("day");
        }
        );


    });


</script>
