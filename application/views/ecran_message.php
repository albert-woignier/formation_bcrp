
<div class="starter-template">
    <h1><?php
        if (isset($titre)) {
            echo $titre;
        }
        ?></h1>
    <div class="alert-info alert" role="alert"><?php
        if (isset($message)) {
            echo $message;
        }
        ?></div>
    <hr>
    <div class="alert-info alert" role="alert">
        <?php
        if (isset($tableau)) {
            $this->load->helper('my_func');
            afficher_tableau($tableau);
        } else if (isset($chaine)) {
            echo $chaine;
        }
        ?>

    </div>

    <!--    <div>
            <button type="button" id="my_test" class="btn btn-danger btn-lg">bouton test albert</button>

        </div>-->

</div>

<div>
</div>
<script>




    var do_that = function () {
        if (confirm('Question ???')) {
            my_func(733);
        } else {
            //
        }

        // $.confirm('titre du truc', 'voulez-vous tester ajax?', 'YESSS', my_func(733));
        return false;
    };

    function my_func(nParameter) {
        $.post(site_url('test/ajax_test'), {id: 45, code: 'del_presta'},
        function (data) {
            if (data !== 'ok')
                alert(data + nParameter);
            else {
                alert('les présences ont été notées pour les candidats' + nParameter);
            }

        }, "text");
    }
    ;
    //"yes" callback



    $().ready(function () {
        jquery_communs();



        $("#my_test").click(do_that);
    });

</script>
