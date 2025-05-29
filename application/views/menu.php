<?php
if ($this->session->data_base_group == 'bcrp') :
    ?>
    <nav class=" container-xl navbar navbar-expand-lg bg-dark navbar-dark   ">
        <a class="navbar-brand" href="#">BCRP<br>Formation</a>
        <?php
    else :
        ?>
        <nav class=" container-xl navbar navbar-expand-lg bg-primary navbar-dark   ">
            <!--        <nav class=" container navbar navbar-expand-lg navbar-light bg-warning   ">-->
            <img src="https://formation.woignier.com/assets/img/logo_ghost.png" width="80px" alt="logo fantome" >
            <!-- <a class="navbar-brand" href="#">Formation<br>TEST</a> -->
        <?php
    endif;
        ?>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="collapsibleNavbar">



            <ul class="navbar-nav">
                <?php if (is_ok(R_ELEV)) : ?>
                    <li class="nav-item active">
                        <a class="nav-link"  href="<?php echo site_url('personne/mes_infos'); ?>">Mes infos</a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link active"  href="<?php echo site_url('parcours/liste'); ?>">Parcours</a>
                </li>
                <?php if (is_ok(R_MON)) : ?>
                    <li class="nav-item active">
                        <a class="nav-link"  href="<?php echo site_url('personne/liste'); ?>">Personnes</a>
                    </li>
                <?php endif; ?>



                <?php if (is_ok(R_ELEV)) : ?>
                    <li class="nav-item dropdown active">
                        <a class="nav-link dropdown-toggle" href="#" id="dropdown02" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Agenda</a>
                        <div class="dropdown-menu" aria-labelledby="dropdown02">
                            <a class="dropdown-item" href="<?php echo site_url('agenda/show'); ?>">Voir Agenda</a>
                            <a class="dropdown-item" href="<?php echo site_url('agenda/lister_inscriptions'); ?>">Voir Inscriptions</a>
                        </div>
                    </li>
                <?php endif; ?>

                <?php if (is_ok(R_MON)) : ?>
                    <li class="nav-item active">
                        <a class="nav-link"  href="<?php echo site_url('menu/admin'); ?>">Administration</a>
                    </li>
                    <?php
                endif;

                if (is_ok(R_DEV) OR $this->session->user_nom == 'WOIGNIER' ) :
                    ?>

                    <li class="nav-item dropdown active">
                        <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">DEBUG</a>
                        <div class="dropdown-menu" aria-labelledby="dropdown01">
                            <a class="dropdown-item" href="<?php echo site_url('trace/show'); ?>">Voir mes traces</a>
                            <a class="dropdown-item" href="<?php echo site_url('trace/show/log'); ?>">Voir fichier log</a>
                            <a class="dropdown-item" href="<?php echo site_url('trace/delete'); ?>">Supprimer mes traces</a>
                            <a class="dropdown-item" href="<?php echo site_url('trace/globales'); ?>">Voir Globales</a>
                            <a class="dropdown-item" href="<?php echo site_url('trace/show_tables'); ?>">Voir les tables SGBD</a>
                            <a class="dropdown-item" href="<?php echo site_url('test/nb_lignes'); ?>">Nombre de lignes de code</a>
                            <a class="dropdown-item" href="<?php echo site_url('page/test_pocket');                  ?>">TEST EXAM POCHES</a>
                            <a class="dropdown-item" href="<?php // echo site_url('test/le_temps');                  ?>">TEST 2</a>
                            <br>
                            <a class="dropdown-item" href="<?php echo site_url('score/score_display');                  ?>">DISPLAY SCORE </a>
                            <a class="dropdown-item" href="<?php echo site_url('score/score_saisie');                  ?>">SAISIE  SCORE </a>

                        </div>
                    </li>
                <?php endif; ?>

                <li class="nav-item active btn-secondary btn-sm">
                    <a class="nav-link"  href="<?php echo site_url('login/close'); ?>">Deconnexion</a>
                </li>
                <li class="nav-item active btn-secondary btn-sm">
                    <?php
                    $user = $this->session->user_prenom . ' ' . $this->session->user_nom .
                            '<br>(' . $this->session->user_categorie . ') ... <span style="font-size:0.8em">' .
                            $this->session->data_base_group . '</span>';
                    echo $user;
                    ?>

                </li>

            </ul>

        </div>
    </nav>
    <?php
    if (current_url() !== $this->session->current_url) {
        $this->session->set_userdata('previous_url', $this->session->current_url);
        $this->session->set_userdata('current_url', current_url());
    }
    ?>
    <div class="container-xl overflow-auto" style="margin-top:2rem; background-color: #ffffcc;">

