<div class="col-md-6 ml-auto mr-auto">
<?php
$num_exo = 'test';
$nb_essais = 3;
$nb_billes = 6 ;
$points= array(4,3,2.5);

?>
    <form>
        <label for="nb_points">Points obtenus pour la figure <?php echo $num_exo; ?>:</label><br>

        <div >
            <?php
            // pour 3 essais

            for ($essai=1; $essai <= $nb_essais; $essai++) :
                
                ?>
            <div id='essai_<?php echo $essai; ?>'>
               
                <label><stong>Essai n° <?php echo $essai; ?></stong></label><br>
             
                <?php for ($bille=1; $bille <= $nb_billes;  $bille++):
                    // pour chaquer bille
                 $id = "id_".$essai."_".$bille;
                $valeur=$essai;
                $txt = 'id="'.$id.'" value="'.$valeur.'" ';
                $total = 0;
                    ?>
                <input name='essai_<?php echo $essai; ?>' id="<?php echo $id; ?>" class="btn btn-outline-secondary btn-sm" style="margin:3px;" data-truc="<?php echo $points[$essai-1]; ?>" type="button" value="<?php echo "bille " .$bille; ?> ">

                <?php endfor;
                ?>
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
    <div>
        <button type="button" class="btn btn-warning" >TRAVAUX EN COURS : notes non enregistrées</button>
    </div>

</div>
<?php 
$num_exo = 4;

?>
<script>
    var num_exo = '<?php echo str_replace("'", "\'", $num_exo); ?>';
    var nb_p = 0;

    var question = '';
    var envoi_points = false;
    var do_that = function () {
        
        var max_pt = Math.max( Number($("#1").val()),Number($("#2").val()), Number($("#3").val()), );
        
        
            question = 'Valider le maximum obtenu : ' + max_pt + ' points ?';
        if (confirm(question)) {
            var stringError = '';
            // on enregistre les notes
            var str = '';
            var nb_p = 0;
            $( "div[id^='essai_']" ).each(function() {
                str = str + 'essai no ' + $( this ).attr( "id" ).substr(6)+' .....';
                var id_div = $( this ).attr( "id" );
                var suite = '';
                $(this).find('.btn').each(function() {
                    // str = str + "'id = " +this.getAttribute('id');
                    
                    if ( $(this).hasClass('btn-primary')) {
                        suite = suite + "X";
                    } else {
                        suite = suite + "o";
                    }

                    
                });
                essai = $( this ).attr( "id" ).substr(6);
                // alert ('essai '+ essai + '\n' +suite);
                // test validité il ne peut y avoir un X après on 'o'
                if (suite.substr(suite.indexOf('o')).indexOf('X') > 0 ) {
                    stringError += 'Erreur sur essai n° '+ essai+ '\n';
                }
  
                total_essai = Number($("#"+essai).val());
                str = str + 'Total essai = '+ total_essai+ '\n';
           
            });
            
            //str = str + 'Meilleur total retenu = '+max_pt + '\n';
            //alert (str + stringError);
            if (stringError != '') {
                alert ('Corrigez la saisie \n'+stringError);
                return false;
            } else {
                window.location = $("#nxt_link").attr('href');
            }
            
//            window.location = $("#nxt_link").attr('href');
//            $.post(site_url('seance/ajax_exam_poche_record'), {exo: num_exo, note: max_pt, essais: str},
//            function (data) {
//                if (data !== 'bug')
//                {
//                    $("#valid").remove();
//                    // $("#my_button").append("<br><strong>Le nouveau total est de : " + data + " points.</strong>");
//                    //
//                    window.location = $("#nxt_link").attr('href');
//                }
//            }, "text");
            envoi_points = true;
        } else {
            // rien on laisse tel quel
        }
        return false;
    };

    $().ready(function () {
        jquery_communs();
        $("#valid").click(do_that);
        $("input[name^='essai_']").click(function () {
            nb_p = Number($(this).attr('data-truc'));
            essai =   $(this).attr( "name" ).substr(6);
            total_essai = Number($("#"+essai).val());
            // alert('nre de point ='+nb_p+'\n'+'essai ='+essai+'\n'+'total ='+total_essai);

            if ( $(this).hasClass('btn-outline-secondary')) {
                $("#"+essai).val(total_essai+nb_p);
                 $(this).toggleClass('btn-outline-secondary btn-primary'); 
            } else {
                 $("#"+essai).val(total_essai-nb_p);
                 $(this).toggleClass('btn-primary btn-outline-secondary');
            }
        });

        $('a').click(function () {
            var link = $(this);
            //if (!envoi_points) {
            if (false) {
                alert('Vous devez valider les points avant de changer de page.');
                return false;
            } else {
                window.location = link.attr('href');
            }
            return false;
        });
    });

</script>