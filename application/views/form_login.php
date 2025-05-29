<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <title>BCRP Formation - Login</title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Bootstrap core CSS -->

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">

    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>
    <!-- Custom styles for this template -->
    <link href="<?php echo base_url(); ?>assets/css/signin.css" rel="stylesheet">
</head>
<body class="text-center">

    <div style="align-content: center;margin:auto;">


        <img class="mb-4" src="<?php echo base_url(); ?>assets/img/logobcrp.gif" alt="logo BCRP" >
        <h1 class="h3 mb-3 font-weight-normal">BCRP<br>Droit à la Formation</h1>


        <?php echo form_open('login/index', 'class="form_signin"'); ?>


        <br>
        <?php
        echo validation_errors('<span class="alert-danger">', '</span>');
        If (isset($erreur)) {
            echo '<span class="alert-danger">' . $erreur . '</span>';
        }
        ?>
        <br>

<!--        <div  class="alert-danger" style="font-size:1.2rem; font-weight:bold;">Le site de formation est fermé pour ce week-end du 20 mars, des travaux de mise à jour sont en cours.<br>
            Revenez dès lundi 22 mars.</div>-->
        <br>
        <h5>Identifiant</h5>
        <input type="text" name="user_id" value="<?php echo set_value('user_id'); ?>" size="30" placeholder="Identifiant" />

        <h5>Mot de passe</h5>
        <input type="password" name="user_mdp" value="" size="30" placeholder="mot de passe" />

        <p>
            &nbsp;
        </p>

        <input type="submit" class="btn btn-success btn-lg" style="width : 200px; align-content: center;" name="valid" value="Connexion">


        <p class="mt-5 mb-3 text-muted"><?php echo img("assets/img/logo_al_volo_200.png"); ?><br>&copy; Al Volo - 2020</p>
        <p class="mt-5 mb-3 text-muted"></p>
        <?php echo form_close(); ?>
    </div>
</body>
</html>
