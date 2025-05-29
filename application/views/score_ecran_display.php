<?php
if ($club_1 !== '') {
    $joueur_1 = $joueur_1 . ' (' . $club_1 . ')';
}

if ($club_2 !== '') {
    $joueur_2 = $joueur_2 . ' (' . $club_2 . ')';
}
?>

<style>

    /* Texte défilant */
    .messagedefilant {
        display: block;
        margin: 20px auto;
        padding: 0;
        overflow: hidden;
        position: relative;
        width: 80%;

        height: 40px;
        background-color: white; /* pour visualiser */
    }

    .messagedefilant div {
        position: absolute;
        min-width: 100%; /* au minimum la largeur du conteneur */
    }

    .messagedefilant div span {
        position: relative;
        top:0; left:50;
        display: inline-block;
        white-space: nowrap;
        font-size: 1.3em;
        animation: defilement 30s infinite linear;
    }
/**µ
    .messagedefilant div span:first-child {
        animation: defilement 15s infinite linear;
        background: #cde; 
    }

    .messagedefilant div span:last-child {
        position: absolute;
        animation: defilement2 15s infinite linear;
        background: #cde; 
    }
**/
    @keyframes defilement {
        0% { margin-left: 0; }
        100% { margin-left: -100%; }
    }


    .table-style  {
        border-collapse: collapse;
        box-shadow: 0 5px 50px rgba(0,0,0,0.15);
        cursor: pointer;
        margin: 0px auto;
        border: 2px solid midnightblue;
        width: 300px;

    }
    thead tr {
        background-color: midnightblue;
        color: #fff;
        text-align: center;
    }
    th, td {
        padding: 0px 5px;
        font-size: 1.3em;
        font-weight: bold;

    }
    tbody tr, td, th {
        border: 1px solid #ddd;
    }
    .nom1, .score1 {background-color: white}
    .set1, .set2 {
        background-color: black;
        color: white;
    }
    .score1, .score2 {
        font-size: 1.6em;
    }
    .nom2, .score2 {background-color: #ffff66}
    .score1, .score2, .set1, .set2 {text-align: center}
    .nom1, .nom2 {
        width: 300px;
    }
    .set1, .set2 {
        background-image:
            linear-gradient(
            #2979FF, whitesmoke
            );
        color: white;
    }

    .t_b2 {
        border-collapse: collapse;
        box-shadow: 0 5px 50px rgba(0,0,0,0.15);
        cursor: pointer;
        margin: 0px auto;
        width: 900px;
    }
    .b_nom {
        width: 300px;
        background-color: #36322C;
        color: white;
    }
    .b_score {
        width: 80px;
        background-color: #E7E21F;
        color: black;
        text-align: center;
    }
    .b_set {
        width: 50px;
        background-color: #229082;
        color: white;
        text-align: center;
    }
    .b_dist {
        width: 40px;
        background-color: #229082;
        color: white;
        text-align: center;
    }


</style>
<p>&nbsp;</p>

<table class="table-style">
    <thead>
        <tr>
            <th colspan="3"><?php echo $competition; ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="nom1"><?php echo $joueur_1; ?></td>
            <td class="set1" ><?php echo $set_1; ?></td>
            <td class="score1"><?php echo $score_1; ?></td>
        </tr>
        <tr>
            <td class="nom2"><?php echo $joueur_2; ?></td>
            <td class="set2"><?php echo $set_2; ?></td>
            <td class="score2"><?php echo $score_2; ?></td>
        </tr>
    </tbody>
</table>

<p>&nbsp;</p>

<table class="t_b2">
    <tr>
        <td class="b_nom"  id="bt_joueur_1">
<?php echo $joueur_1; ?>
        </td>
        <td class="b_score" id="bt_score_1" >
            <?php echo $score_1; ?>
        </td>
        <td class="b_set" id="bt_set_1">
            <?php echo $set_1; ?>
        </td>
        <td class="b_dist">
            (3)
        </td>
        <td class="b_set" id="bt_set_2">
<?php echo $set_2; ?>
        </td>
        <td class="b_score" id="bt_score_2" >
            <?php echo $score_2; ?>
        </td>
        <td class="b_nom" id="bt_joueur_2">
            <?php echo $joueur_2; ?>
        </td>
    </tr>
</table>
<p>&nbsp;</p>
<div class="messagedefilant" id="message_defilant">
    &nbsp;
</div>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/score.css">

<script>
    var quel_score = function () {
        var table_number = 1;
        $.post(site_url('score/ajax_get_details'),
                {table: table_number},
                function (data) {
                    if (data !== 'bug') {
                        // alert (data);
                        const myArr = data.split(";");
                        var span = ' <span style="font-size: 0.8em;">(';
                        var end_span = ')</span> ';
                        var joueur1 = myArr[0] + (myArr[1] !== '' ? span + myArr[1] + end_span : '');
                        var joueur2 = myArr[4] + (myArr[5] !== '' ? span + myArr[5] + end_span : '');
                        $("#bt_joueur_1").html(joueur1); 
                        $("#bt_score_1").html(myArr[2]);
                        $("#bt_set_1").html(myArr[3]);
                        $("#bt_joueur_2").html(joueur2);
                        $("#bt_score_2").html(myArr[6]);
                        $("#bt_set_2").html(myArr[7]);
                        $(".nom1").html(joueur1); 
                        $(".score1").html(myArr[2]);
                        $(".set1").html(myArr[3]);
                        $(".nom2").html(joueur2);
                        $(".score2").html(myArr[6]);
                        $(".set2").html(myArr[7]);
                        
                    }
                },
                "text");
        return false;
    }

    var texte;
    function marqueelike() {
            $.post(site_url('score/ajax_get_defil'),
                    function (data) {
                        // alert(data);
                        if (data !== 'bug') {
                            // alert (data);
                            $('#message_defilant').html('<div><span>' + data + '</span></div>');
                        }
                    },
                    "text");
                // alert('ok');
            return false;
        }

    $().ready(function () {
        jquery_communs();
        // au démarrage on demande les nom des joueurs
        var tid = setInterval(quel_score, 5000);
        marqueelike();
        var tid2 = setInterval(marqueelike, 50000);
    });

</script>