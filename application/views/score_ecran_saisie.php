<div class="container">
    <!-- comment -->
    <div class='page'>


        <div class='content_j1'>
            <div class='player_name' id="player1"></div>
            <div class='touche minus'>-</div>
            <div class='touche points'>2</div>
            <div class='touche points'>3</div>
            <div class='touche points'>4</div>
            <div class='touche points'>5</div>
            <div class='touche points'>6</div>
            <div class='touche points'>7</div>
            <div class='touche points'>8</div>
            <div class='touche points'>9</div>
            <div class='touche points'>10</div>
            <div class='touche points'>11</div>
            <div class='touche points'>12</div>
            <div class='touche points'>13</div>
            <div class='touche points'>14</div>
            <div class='touche points'>15</div>
            <div class='touche points'>16</div>
            <div class='serie'>&nbsp;</div>
            <div class='valid_ok'>VALIDER</div>
            <div class='touche erase' >C</div>
            <div class='last_points'>&nbsp;</div>
            <div class='score' id="score1">&nbsp;</div>
            <div class='touche sets'>-</div>
            <div class='touche'>Set</div>
            <div class='touche points nb_sets' id="set1">&nbsp;</div>
            <div class='touche sets'>+</div>
        </div>
        <div class='column'>
            <!---
            <div style="height: 20px;">&nbsp;</div>
            <div class='serie cursor' id="inverser" style="font-size: 2em;">inverser joueurs</div>
            <div style="height: 300px;">&nbsp;</div>
            <div class='serie cursor' id="new_game">start game</div>
            <div style="height: 20px;">&nbsp;</div>
            <div class='serie cursor' id="new_set">RAZ score</div>
            <div style="height: 20px;">&nbsp;</div>
            <div class='serie cursor' id="the_end">TERMINER</div>
            comment -->
            <div style="height: 20px;">&nbsp;</div>
            <div id="new_set" style="text-align: center;">
                <img src="<?php echo base_url(); ?>assets/img/scoring/reset.jpg" width="60px" >
            </div>
            <div style="height: 20px;">&nbsp;</div>
            <div id="inverser" style="text-align: center;">
                <img src="<?php echo base_url(); ?>assets/img/scoring/permuter.png" width="60px" >
            </div>
            <div style="height: 20px;">&nbsp;</div>
        </div>

        <div class='content_j2'>
            <div class='player_name' id="player2"></div>
            <div class='touche minus'>-</div>
            <div class='touche points'>2</div>
            <div class='touche points'>3</div>
            <div class='touche points'>4</div>
            <div class='touche points'>5</div>
            <div class='touche points'>6</div>
            <div class='touche points'>7</div>
            <div class='touche points'>8</div>
            <div class='touche points'>9</div>
            <div class='touche points'>10</div>
            <div class='touche points'>11</div>
            <div class='touche points'>12</div>
            <div class='touche points'>13</div>
            <div class='touche points'>14</div>
            <div class='touche points'>15</div>
            <div class='touche points'>16</div>
            <div class='serie'>&nbsp;</div>
            <div class='valid_ok'>VALIDER</div>
            <div class='touche erase'>C</div>
            <div class='last_points'>&nbsp;</div>
            <div class='score' id="score2">&nbsp;</div>
            <div class='touche sets'>-</div>
            <div class='touche '>Set</div>
            <div class='touche points nb_sets' id="set2">&nbsp;</div>
            <div class='touche sets'>+</div>
            
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/score.css"><!-- comment -->
<script>
    var no_joueur_actif = 0;
    var no_joueur_inactif = 0;
    var input_actif = 0;
    var input_inactif = 0;
    var global_table_number = 1;
    var envoi_points = false;

    var initialisation = function (table_number) {
        global_table_number = table_number;
        $.post(site_url('score/ajax_get_details'),
                {table: table_number},
                function (data) {
                    const myArr = data.split(";");
                    $("#player1").html(myArr[0]);
                    $("#player2").html(myArr[4]);
                    $("#score1").html(myArr[2]);
                    $("#score2").html(myArr[6]);
                    $("#set1").html(myArr[3]);
                    $("#set2").html(myArr[7]);
                    $(".content_j1").find(".serie").html('');
                    $(".content_j2").find(".serie").html('');
                    no_joueur_actif = 0;
                    input_actif = no_joueur_actif + 1;
                    no_joueur_inactif = 1 - no_joueur_actif; // bascule
                    input_inactif = no_joueur_inactif + 1;
                },
                "text");
        return false;
    }

    var envoi_points = function () {
        var nb_points = Number($(this).parent().find(".serie").html());
        var id_player = $(this).parent().find(".player_name").attr('id');
        var id_player = id_player.substring(id_player.length - 1);
        var score = Number($(this).parent().find(".score").html());
        var set = Number($(this).parent().find(".nb_sets").html());
        // alert('set '+ set);
        $(this).parent().find(".score").html(Number(score + nb_points));
        
        $.post(site_url('score/ajax_write_score'),
                {table: global_table_number,
                    points: nb_points,
                    sets: set,
                    num_joueur: id_player},
                function (data) {

                },
                "text");
        $(this).parent().find(".serie").html('');
        $(this).parent().find(".serie").css({"background-color": "white"});
        $(this).parent().find(".points").toggleClass('on', false);
        $(this).parent().find(".valid_ok").css({"background-color": "lightgrey"});
        $(this).parent().find('.minus').css({"background-color": "#686868", "color": "white"});
        $(this).parent().find('.minus').toggleClass('on', false);
        $(this).parent().find(".last_points").html(nb_points);
        return false;
    }

    function click_points() {
        var nbp = Number($(this).html()); // valeur de la touche
        $(this).parent().find(".points").toggleClass("on", false); // RAZ des touches
        $(this).toggleClass("on", true); // on de la touche cliquée
        // la touche - est-elle enfoncée
        if ($(this).parent().find(".minus").hasClass('on')) {
            nbp = -nbp;
        }
        $(this).parent().find(".serie").html(nbp);
        // on surligne "valider"
        $(this).parent().find(".valid_ok").css({"background-color": "green"});

    }

    function click_negatif() {
        var points = Number($(this).parent().find(".serie").html());
        if ($(this).hasClass('on')) {
            $(this).toggleClass('on', false);
            $(this).css({"background-color": "#686868", "color": "white"});
            $(this).parent().find(".serie").css({"background-color": "white"});
            if (points !== 0) {
                points = -points;
                $(this).parent().find(".serie").html(points);
            }
        } else {
            $(this).css({"background-color": "red", "color": "white"});
            $(this).toggleClass('on', true);
            $(this).parent().find(".serie").css({"background-color": "pink"});
            if (points !== 0) {
                points = -points;
                $(this).parent().find(".serie").html(points);
            }
        }
    }

    function click_erase() {

        $(this).parent().find(".serie").html('');
        $(this).parent().find(".serie").css({"background-color": "white"});
        $(this).parent().find(".points").toggleClass('on', false);
        $(this).parent().find(".valid_ok").css({"background-color": "lightgrey"});
        $(this).parent().find('.minus').css({"background-color": "#686868", "color": "white"});
        $(this).parent().find('.minus').toggleClass('on', false);
    }
    
    function click_set() {
        var signe = $(this).html();
        var nb_sets = Number($(this).parent().find(".nb_sets").html());
        if (signe === '-') {
            nb_sets--;
        } else {
            nb_sets++;
        }
        $(this).parent().find(".nb_sets").html(nb_sets);
 
        var id_player = $(this).parent().find(".player_name").attr('id');
        var id_player = id_player.substring(id_player.length - 1);
        nb_sets = Number($(this).parent().find(".nb_sets").html());
        // alert('set '+ set);
        
        $.post(site_url('score/ajax_write_score'),
                {table: global_table_number,
                    points: 0,
                    sets: nb_sets,
                    num_joueur: id_player},
                function (data) {

                },
                "text");
    }
    
    function inverser() {
        if (! confirm('Voulez-vous vraiment inverser les joueurs ?')) {
            return false;
        }
        $.post(site_url('score/ajax_swap_players'),
                {table: global_table_number},
                function (data) {
                    const myArr = data.split(";");
                    $("#player1").html(myArr[0]);
                    $("#player2").html(myArr[4]);
                    $("#score1").html(myArr[2]);
                    $("#score2").html(myArr[6]);
                    $("#set1").html(myArr[3]);
                    $("#set2").html(myArr[7]);
                    $(".content_j1").find(".serie").html('');
                    $(".content_j2").find(".serie").html('');
                    no_joueur_actif = 0;
                    input_actif = no_joueur_actif + 1;
                    no_joueur_inactif = 1 - no_joueur_actif; // bascule
                    input_inactif = no_joueur_inactif + 1;
                },
                "text");
    }
    function new_game() {
        initialisation(1);
       
    }
    function new_set() {
        if (! confirm('Voulez-vous vraiment débuter un nouveau set ?')) {
            return false;
        }
        $("#score1").html(0);
        $("#score2").html(0);
        $.post(site_url('score/ajax_new_set'),
                {table: global_table_number},
                function (data) {

                },
                "text");
        
    }

     $().ready(function () {
        jquery_communs();
        // au démarrage on demande les nom des joueurs
        initialisation(1);

        $(".touche, .valid_ok, .cursor").hover(function(){
            $(this).css("cursor", "pointer");
            }, function(){
                $(this).css("cursor", "default");
        });
        $(".points").click(click_points);
        $(".valid_ok").click(envoi_points);
        $(".erase").click(click_erase);
        $(".minus").click(click_negatif);
        $(".sets").click(click_set);
        $("#inverser").click(inverser);
        $("#new_game").click(new_game);
        $("#new_set").click(new_set);
                
    });

</script>