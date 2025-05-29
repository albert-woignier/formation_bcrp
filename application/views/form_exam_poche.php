<div class="col-md-6 ml-auto mr-auto">

    <form>
        <label for="nb_points">Saisie points pour figure <?php echo $num_exo; ?>:</label><br>

        <div >
            <?php
            // pour 3 essais
            for ($essai = 1; $essai <= $nb_essais; $essai++) :
                ?>
            <!-- Div contient le numéro essai et la valeur des points de l'essai -->
                <div id='essai_<?php echo $essai; ?>' data-truc='<?php  echo $points[$essai - 1]; ?>'>

                        <label><stong>Essai n° <?php echo $essai; ?></stong></label><br>

                    <?php
                    for ($bille = 1; $bille <= $nb_billes; $bille++):
                        // pour chaquer bille
                        $id = "id_" . $essai . "_" . $bille;
                        $valeur = $essai;
                        $txt = 'id="' . $id . '" value="' . $valeur . '" ';
                        $total = 0;
                        ?>
                            <input type="button" name='essai_<?php echo $essai; ?>' id="<?php echo $id; ?>" class="btn btn-outline-secondary btn-sm ok_coup" style="margin:3px;" data-truc="<?php echo $points[$essai - 1]; ?>" value="<?php echo "bille " . $bille; ?> ">

                    <?php endfor;
                    if (($bonus > 0)) : ?>
                        ----><input type="button" name='essai_<?php echo $essai; ?>' class="btn btn-outline-warning btn-sm ok_bonus" style="margin:3px;" data-truc="<?php echo $bonus; ?>" value="<?php echo "bonus"; ?> ">
                    <?php endif; ?>
                            
                    <div class="form-check form-check-inline">
                        &nbsp;&nbsp;Total essai = <input type="text" readonly value="<?php echo $total; ?>" id="<?php echo $essai; ?>">
                    </div>
                </div>
                <hr>
            <?php endfor;
            ?>
        </div>

    </form>
    <br>
    <div id="my_button">
        <button type="button" id="valid" class="btn btn-danger btn-lg">enregister les points et aller à la suite</button>
    </div>


</div>

<script>
    var num_exo = '<?php echo str_replace("'", "\'", $num_exo); ?>';
    var nb_billes = '<?php echo $nb_billes; ?>';
    var is_bonus = <?php echo $bonus; ?>;
    var nb_p = 0;
    var envoi_points = false;
    var bonus = -1;
    var essai = 0;
    var total_essai = 0;
    function Essai(numero, nb_billes, tarif_reussite, serie, points_serie, bonus,total) {
        this.numero = numero;
        this.billes = nb_billes;
        this.tarif = tarif_reussite;
        this.serie=serie;
        this.pts_serie = points_serie;
        this.bonus=bonus;
        this.total=total;
    }
    const Note = {
        figure : '',
        total : 0,
        essais : new Essai()
    };
    
    var do_that = function () {
        // Sortie du formulaire, envoi résultats et next page
        var max_pt = Math.max(Number($("#1").val()), Number($("#2").val()), Number($("#3").val()) );
        var question = 'Valider le maximum obtenu : ' + max_pt + ' points ?';
        if (confirm(question)) {
            var stringError = '';
            // on enregistre les notes
            var str = '';
            var nb_p = 0;
            var tab_essais = new Array();
            $("div[id^='essai_']").each(function () {
                
                var num_essai = $(this).attr("id").substr(6);
                str = str + num_essai + '@' + nb_billes + '@' + $(this).attr('data-truc')+ '@'; // numéro essai, nb billes, nb points si reussite
                var suite = '';
                total_essai = Number($("#" + num_essai).val());
                // alert (total_essai);
                $(this).find('.ok_coup').each(function () {
                    // str = str + "'id = " +this.getAttribute('id');
                    if ($(this).hasClass('btn-primary')) {
                        suite = suite + "X";
                    } else {
                        suite = suite + "o";
                    }
                });
                // y a t'il un bonus ?
                // 2021 08 29 : bonus pas utilisé ...
                if (is_bonus > 0) {
                    $(this).find('.ok_bonus').each(function () {
                        // str = str + "'id = " +this.getAttribute('id');
                        if ($(this).hasClass('btn-warning')) {
                            bonus = Number($(this).attr('data-truc'));
                        } else {
                            bonus = 0;
                        }
                    }); 
                } else {
                    bonus = -1;
                }
                
                // alert ('essai '+ essai + '\n' +suite);
                // test validité il ne peut y avoir un X après on 'o'
                if (suite.substr(suite.indexOf('o')).indexOf('X') > 0) {
                    stringError += 'Erreur sur essai n° ' + num_essai + '\n';
                } else {
                    // tab_essais.push(JSON.stringify(new Essai(num_essai, nb_billes, $(this).attr('data-truc'), 
                    // suite, total_essai, bonus,  total_essai)));
                    tab_essais.push((new Essai(num_essai, nb_billes, $(this).attr('data-truc'), 
                    suite, total_essai, bonus,  total_essai)));
                    
                }
                // TODO : enregistrer BONUS
                str = str + suite + '@' + total_essai + '\n';

            });
            

            if (stringError != '') {
                alert('Corrigez la saisie \n' + stringError);
                return false;
            } 
            // si pas d'erreur on enregistre et on passe exo suivant
            // alert (tab_essais.toString());
            $.post(site_url('seance/ajax_exam_poche_record'), {exo: num_exo, note: max_pt, 
                nb_coups: nb_billes, 
                //essais: tab_essais.toString()
                essais: JSON.stringify(tab_essais)
            
                    },
                    function (data) {
                        if (data !== 'bug')
                        {
                            $("#valid").remove();
                            // $("#my_button").append("<br><strong>Le nouveau total est de : " + data + " points.</strong>");
                            //
                            window.location = $("#nxt_link").attr('href');
                        }
                    }, "text");
            envoi_points = true;
        } else {
            // rien on laisse tel quel
        }
        return false;
    };

    $().ready(function () {
        jquery_communs();
        $("#valid").click(do_that);
        $(".ok_coup, .ok_bonus").click(function () {
            nb_p = Number($(this).attr('data-truc'));
            essai = $(this).attr("name").substr(6);
            total_essai = Number($("#" + essai).val());
            // alert('nre de point ='+nb_p+'\n'+'essai ='+essai+'\n'+'total ='+total_essai);

            if ($(this).hasClass('btn-outline-secondary')) {
                $("#" + essai).val(total_essai + nb_p);
                $(this).toggleClass('btn-outline-secondary btn-primary');
            } else if ($(this).hasClass('btn-primary')) {
                $("#" + essai).val(total_essai - nb_p);
                $(this).toggleClass('btn-primary btn-outline-secondary');
            } else if ($(this).hasClass('btn-outline-warning')){
                $("#" + essai).val(total_essai + nb_p);
                $(this).toggleClass('btn-outline-warning btn-warning');
            } else if ($(this).hasClass('btn-warning')) {
                $("#" + essai).val(total_essai - nb_p);
                $(this).toggleClass('btn-warning btn-outline-warning');
            } 
        });

        $('a').click(function () {
            var link = $(this);
            if (!envoi_points) {
                alert('Vous devez valider les points avant de changer de page.');
                return false;
            } else {
                window.location = link.attr('href');
            }
            return false;
        });
    });

</script>