<?php
$this->load->helper('my_html_helper');

// $texte['wait'] = '<span class="bg-danger text-white">- non confirmé -</span>';
$texte[RESA_WAIT] = '<i class="bi-alarm-fill text-danger blink_me" style="font-size: 1.4rem;"></i>';
// $texte['ok'] = '<span class="bg-success text-white">- confirmé -</span>';
$texte[RESA_OK] = '<i class=" bi-check-square-fill text-success" style="font-size: 1rem;"></i>&nbsp;&nbsp;';
// $texte['refus'] = '<span class="bg-warning text-white">- refusé -</span>';
$texte[RESA_REFUS] = '<i class=" bi-hand-thumbs-down text-danger" style="font-size: 1.4rem;"></i>';
$texte[RESA_ANNUL] = '<i class=" bi-x-square-fill text-danger" style="font-size: 1.4rem;"></i>';
$texte[''] = '';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
<div class="starter-template">
    <h1>Liste des créneaux et RDV</h1>
    <p style="font-size:0.7rem;">
        <?php if (is_ok(R_MON)) : ?>
            <button type='button' class='btn btn-sm btn-outline-danger' data-creneau="" >Annuler</button> : vous annulez la séance ! Si des personnes sont inscrites, un mail est envoyé.<br>
            <i class="bi-person-x-fill text-danger " style="font-size: 1.4rem;" title="Annuler le RDV" data-resa=""></i> : déinscrire une personne qui se désiste. Pas de mail envoyé.&nbsp;&nbsp;
            <i class="bi-person-plus-fill text-primary " style="font-size: 1.4rem;"></i> : inscrire un personne sur le créneau. Pas de mail envoyé.
        <?php endif; ?>
    </p>
</div>
<div>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">Date - heure</th>
                <?php if (is_ok(R_MON)) : ?>
                    <th scope="col">Annuler</th>
                <?php endif; ?>
                <th scope="col">Moniteur</th>
                <th scope="col">ins/max</th>
                <th scope="col">Inscrits</th>
                <?php if (is_ok(R_MON)) : ?>
                    <th scope="col">Inscrire</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $tr_class[0] = "table-primary";
            $tr_class[1] = "";
            $i = 0;
            $old_id_creneau = 0;
            foreach ($les_creneaux as $creneau) :
                // on fait la bascule pour strier la table par paquet de créneau
                if ($creneau['id_creneau'] !== $old_id_creneau) {
                    $old_id_creneau = $creneau['id_creneau'];
                    $i = 1 - $i;
                }
                $classtd = '';
                if ($creneau['statut'] == RESA_ANNUL) {
                    $classtd = ' class="text-decoration-line-through"'; // on raye le nom de l'élève
                }
                ?>
                <tr class = "<?php echo $tr_class[$i]; ?>">
                    <td style="font-weight: bold; color: blue;"><?php echo jour_date($creneau['date']) . ' à ' . substr($creneau['heure_debut'], 0, 5); ?></td>
                    <?php if (is_ok(R_MON)) : ?>
                        <td><button type='button' class='btn btn-sm btn-outline-danger' data-creneau="<?php echo $creneau['id_creneau']; ?>" >Annuler</button></td>
                    <?php endif; ?>
                    <td><?php echo $creneau['moniteur']; ?></td>
                    <td><?php
                        echo $creneau['nb_resa'] . ' / ' . $creneau['nb_pers_max'];
                        if ($creneau['complet'] == 1) {
                            echo img("assets/img/bullet_ball_red.png");
                        } else {
                            echo img("assets/img/bullet_ball_glass_green.png");
                        }
                        ?></td>
                    <td>
                        <?php
                        if ($creneau['eleve']) {
                            echo $texte[RESA_OK];
                            echo $creneau['eleve'];
                        }

                        if (is_ok(R_MON) AND $creneau['statut'] == RESA_OK) {
                            echo '&nbsp;&nbsp;<i class="bi-person-x-fill text-danger " style="font-size: 1.4rem;" title="Annuler le RDV de ' . $creneau['eleve'] . '"' .
                            ' data-resa="' . $creneau['id_resa'] . '"></i>';
                        }
                        ?>
                    </td>

                    <?php if (is_ok(R_MON)) : ?>
                        <td>
                            <?php
                            if ($creneau['complet'] == 0) :
                                // img_anchor($controller, $assets_img, $arr_attibutes);
                                // trace('---' . $creneau['id_creneau']);
                                echo anchor('agenda/inscrire/' . $creneau['id_creneau'], '&nbsp;&nbsp;<i class="bi-person-plus-fill text-primary " style="font-size: 1.4rem;"></i>', array('title' => 'Inscrire un apprenant'));
                            endif;
                            ?>
                        </td>
                    <?php endif; ?>
                </tr>
                <?php
            endforeach;
            ?>
        </tbody>
    </table>

</div>

<script>
    var dialog;
    var dialog_add;
    var le_jour_selected;
    var id_creneau;
    $().ready(function () {
        jquery_communs();
        $(".btn-outline-success").click(function () {
            var id = $(this).attr('data-resa');
            if (id == '')
                return false;
            $.post(site_url('agenda/ajax_gestion_agenda'), {id: id, action: 'confirm'},
            function (data) {
                if (data != 'ok')
                    alert(data);
                location.reload();
            }, "text");
        });

        $(".refus").click(function () {
            var id = $(this).attr('data-resa');
            if (id == '')
                return false;
            $.post(site_url('agenda/ajax_gestion_agenda'), {id: id, action: 'refus'},
            function (data) {
                if (data != 'ok')
                    alert(data);
                location.reload();
            }, "text");
        });

        $(".btn-outline-danger").click(function () {
            var id = $(this).attr('data-creneau');
            if (id == '')
                return false;
            if (!confirm('Attention : cette opération va annuler la séance et envoyer un mail aux apprenants qui sont inscrits.\n Voulez-vous continuer ?')) {
                return false;
            }
            $.post(site_url('agenda/ajax_gestion_agenda'), {id: id, action: 'cancel'},
            function (data) {
                if (data != 'ok')
                    alert(data);
                location.reload();
            }, "text");
        });

        $(".bi-person-x-fill").click(function () {
            var resa_eleve = $(this).attr('data-resa');
            if (resa_eleve == '')
                return false;
            if (!confirm('Voulez-vous vraiement ' + $(this).attr('title'))) {
                return false;
            }
            $.post(site_url('agenda/ajax_gestion_agenda'), {id: resa_eleve, action: 'suppr_rdv'},
            function (data) {
                if (data != 'ok')
                    alert(data);
                location.reload();
            }, "text");


        });

    });


</script>
