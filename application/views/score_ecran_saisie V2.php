<div class="container">
<!-- comment -->
  <div class="row">
    <div id="nom_1" class="col-sm">
        Lucien BRETOUGRE<br>BCRP
    </div>

    <div id="nom_2" class="col-sm">
        Jean-Pierre DUGENOUX<br>USAPP Lyon
    </div>
  </div>

<!-- SCORE TOTAL -->
  <div class="row ">
    <div class="col-sm">
        <button type="button" id="bt_score_1" class="btn btn-primary">score 1</button>
    </div>

    <div class="col-sm">
        <button type="button" id="bt_score_2" class="btn btn-primary">score 2</button>
    </div>
  </div>

<!-- saisie score -->
<form>
  <div class="row">
    <div class="col-sm">
        <input type="number" class="form-control" id="points_1">
    </div>

    <div class="col-sm">
        <input type="number" class="form-control" id="points_2">
    </div>
  </div>
</form>
<p>
    &nbsp;
</p>

<!-- comment -->
<div class="col-md-6 ml-auto mr-auto">
    <div id="my_button">
        <button type="button" id="valid" class="btn btn-danger btn-lg">OK</button>
    </div>
</div>
</div>
<script>
    var no_joueur_actif = 0;
    var no_joueur_inactif = 0;
    var input_actif = 0;
    var input_inactif = 0;
    var global_table_number = 1;
    var envoi_points = false;

    
    var initialisation = function (table_number) {
        global_table_number = table_number;
            $.post(site_url('score/ajax_get_score'), 
                {table: table_number},
                function (data) {
                    if (data !== 'bug') {
                        const myArr = data.split(";");
                        $("#nom_1").html(myArr[0]);
                        $("#nom_2").html(myArr[1]);
                       
                        $("#bt_score_1").html(myArr[2]);
                        $("#bt_score_2").html(myArr[3]);
                        no_joueur_actif = 0;
                        input_actif = no_joueur_actif + 1;
                        no_joueur_inactif = 1 - no_joueur_actif; // bascule
                        input_inactif = no_joueur_inactif + 1;
                        
                        $('#points_'+input_inactif).attr('readonly', true);
                        $('#points_'+input_actif).attr('readonly', false);
                    }
                }, 
            "text");
            return false;
    }
    
 

    $().ready(function () {
        jquery_communs();
        // au démarrage on demande les nom des joueurs
        initialisation(1);
      
        $("#valid").click(do_validation);
    });

</script>