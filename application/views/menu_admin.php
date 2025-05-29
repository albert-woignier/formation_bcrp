
<div class="starter-template">
    <h1>Statistiques et Administration</h1>
    <p><?php //echo $commentaires;                ?></p>
</div>

<div style="align-content: center;margin:auto;">
    <h4>Statistiques</h4>
    <?php if (is_ok(R_MON)) : ?>
    <a class="dropdown-item" href="<?php echo site_url('login/liste/all'); ?>">Qui s'est connecté ?</a>
    <a class="dropdown-item" href="<?php echo site_url('login/liste/apprenants'); ?>">Les apprenants qui utilisent le logiciel ?</a>
    <a class="dropdown-item" href="<?php echo site_url('suivi/dernieres_seances'); ?>">Les séances des 6 derniers mois</a>
    <a class="dropdown-item" href="<?php echo site_url('suivi/stat_seances'); ?>">Les séances mois par mois</a>
    <a class="dropdown-item" href="<?php echo site_url('suivi/stat_seances_annee'); ?>">Les séances par année</a>

    
    <?php endif; ?>
    <?php if (is_ok(R_ADMIN)) : ?>
    <a class="nav-link"  href="<?php echo site_url('login/tuto'); ?>">test Tutoriels</a>
    <?php endif; ?>
    <?php if (is_ok(R_MON)) : ?>
    <h4>Les personnes</h4>
    <a class="dropdown-item" href="<?php echo site_url('parcours/liste_pers_par/2'); ?>">Liste des apprenants diplômés</a>
    <a class="dropdown-item" href="<?php echo site_url('parcours/liste_pers_par/1'); ?>">Liste des apprenants en cours</a>
    <?php endif; ?>
    <?php if (is_ok(R_ADMIN)) : ?>
    <a class="dropdown-item" href="<?php echo site_url('personne/add'); ?>">Ajouter une personne</a>

    <h4>Les parcours</h4>
    <a class="dropdown-item" href="<?php echo site_url('parcours/liste'); ?>">Lister les Parcours</a>
    <a class="dropdown-item" href="<?php echo site_url('parcours/add/0'); ?>">Ajouter un Parcours vide</a>
    <a class="dropdown-item" href="">Liste des apprenants et diplômés d'un parcours (bientôt)</a>
    <!---

    -->
    <h4>Les Séances</h4>
    <a class="dropdown-item" href="<?php echo site_url('seance/liste'); ?>">Lister les Séances</a>
    <a class="dropdown-item" href="<?php echo site_url('seance/add/0'); ?>">Ajouter une Séance vide</a>
    <a class="dropdown-item" href="<?php echo site_url('excel_export/seances'); ?>">Export EXCEL séances</a>
    <!---

    -->
    <h4>Les pages</h4>
    <a class="dropdown-item" href="<?php echo site_url('page/add/0'); ?>">Ajouter Page</a>
    <a class="dropdown-item" href="<?php echo site_url('page/liste'); ?>">Lister Pages</a>
    <a class="dropdown-item" href="<?php echo site_url('excel_export/pages'); ?>">Export EXCEL pages</a>

    <h4>Les images, vidéos, fichiers pdf</h4>
    <a class="dropdown-item" href="<?php echo site_url('multimedia/load/img'); ?>">Charger une image</a>
    <a class="dropdown-item" href="<?php echo site_url('multimedia/load/vid'); ?>">Charger une vidéo</a>
    <a class="dropdown-item" href="<?php echo site_url('multimedia/load/pdf'); ?>">Charger un PDF</a>
    <a class="dropdown-item" href="<?php echo site_url('multimedia/liste/img'); ?>">Lister image</a>
    <a class="dropdown-item" href="<?php echo site_url('multimedia/liste/vid'); ?>">Lister vidéo</a>
    <a class="dropdown-item" href="<?php echo site_url('multimedia/liste/pdf'); ?>">Lister PDF</a>

    <?php endif; ?>


</div>
<p>
    &nbsp;
</p>


