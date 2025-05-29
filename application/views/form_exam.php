<div class="col-md-6 ml-auto mr-auto">

    <form>
        <label for="nb_points">Points obtenus pour la figure :</label><br>

        <div  data-toggle="buttons">
            <?php
            // $row = 0;
            foreach ($points as $point) :
//                if ($row++ == 3) {
//                    echo '<br>';
//                    $row = 1;
//                }
                ?>
                <input name='nb_points' class="btn btn-outline-primary" style="margin:3px;" data-truc="<?php echo $point; ?>" type="button" value="<?php echo $point; ?> pts">
                &nbsp;&nbsp;
            <?php endforeach;
            ?>
        </div>
        <?php if (count($bonus) > 1) : ?>
            <label for="nb_points">Bonus :</label><br>
            <div  data-toggle="buttons">
                <?php
                // $row = 0;
                foreach ($bonus as $point) :
//                if ($row++ == 3) {
//                    echo '<br>';
//                    $row = 1;
//                }
                    ?>
                    <input name='bonus' class="btn btn-outline-primary" style="margin:3px;" data-truc="<?php echo $point; ?>" type="button" value="<?php echo $point; ?> pts">
                    &nbsp;&nbsp;
                <?php endforeach;
                ?>
            </div>
        <?php endif; ?>

    </form>
    <br>
    <div id="my_button">
        <button type="button" id="valid" class="btn btn-danger btn-lg">enregister les points et aller à la suite</button>

    </div>

</div>

<script>
    var num_exo = '<?php echo str_replace("'", "\'", $num_exo); ?>';
    var nb_p = 0;
    var is_bonus = <?php echo count($bonus); ?>;
    var bonus = 0;
    var question = '';
    var envoi_points = false;
    var do_that = function () {
        if (is_bonus > 1) {
            question = 'Valider les ' + nb_p + ' points \n et le bonus de ' + bonus + ' points ?';
        } else {
            question = 'Valider les ' + nb_p + ' points ?';
        }
        if (confirm(question)) {
            bonus = (is_bonus === 0) ? -1 : bonus;
            $.post(site_url('seance/ajax_exam_record'), {exo: num_exo, note: nb_p, bonus: bonus},
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
            $("input[name='nb_points']").attr('class', 'btn btn-outline-primary');
            $("input[name='bonus']").attr('class', 'btn btn-outline-primary');
            nb_p = 0;
            bonus = 0;
        }
        return false;
    };

    $().ready(function () {
        jquery_communs();
        $("#valid").click(do_that);
        $("input[name='nb_points']").click(function () {
            nb_p = $(this).attr('data-truc');
            $("input[name='nb_points']").attr('class', 'btn btn-outline-primary');
            $(this).attr('class', 'btn btn-primary');
        });
        $("input[name='bonus']").click(function () {
            bonus = $(this).attr('data-truc');
            $("input[name='bonus']").attr('class', 'btn btn-outline-primary');
            $(this).attr('class', 'btn btn-primary');
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
