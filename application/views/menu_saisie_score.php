
<nav class=" container-xl navbar navbar-expand-lg bg-dark navbar-dark   ">
    <a class="navbar-brand" href="#">Al VOLO - Scoring</a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="collapsibleNavbar">



        <ul class="navbar-nav">

            <li class="nav-item active">
                <a class="nav-link"  href="<?php echo site_url('score/set_players/1'); ?>">Noms des Joueurs</a>
            </li>

            <li class="nav-item active">
                <a class="nav-link"  href="<?php echo site_url('score/score_saisie'); ?>">Saisie score</a>
            </li>

            <li class="nav-item active">
                <a class="nav-link"  href="<?php echo site_url('score/score_display'); ?>">Voir affichage du score</a>
            </li>

            <li class="nav-item active">
                <a class="nav-link"  href="<?php echo site_url('score/saisie_resultats'); ?>">Texte défilant</a>
            </li>
            <li class="nav-item active btn-secondary btn-sm">
                <a class="nav-link"  href="<?php echo site_url('login/close'); ?>">Deconnexion</a>
            </li>
<li class="nav-item dropdown active">
                        <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">DEBUG</a>
                        <div class="dropdown-menu" aria-labelledby="dropdown01">
                            <a class="dropdown-item" href="<?php echo site_url('score/show_traces'); ?>">Voir mes traces</a>
                            <a class="dropdown-item" href="<?php echo site_url('score/delete_traces'); ?>">Supprimer mes traces</a>

                        </div>
                    </li>

        </ul>

    </div>
</nav>
<div class="container-xl overflow-auto" style="margin-top:2rem; background-color: #ffffcc;">

