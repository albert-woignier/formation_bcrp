<?php
$texte[2] = '<span class="bg-success text-white">- parcours validé -</span>';
$texte[1] = '<span class="bg-info text-white">- séance validée -</span>';
$texte[0] = '<span class="bg-warning text-white">- séance à refaire -</span>';
$texte[''] = '';
?>
<div class="starter-template">
    <h1><?php echo $titre; ?></h1>
    <p><?php echo $commentaires; ?></p>
</div>
<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Date</th>
                <th scope="col">Moniteur</th>
                <th scope="col">Séance</th>
                <th scope="col">Validation</th>
                <th scope="col">Commentaires</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($les_seances as $seance) : ?>
                <tr>
                    <td><strong><?php echo date_fr($seance['date_seance']); ?></strong></td>
                    <td><?php echo $seance['moniteur']; ?></td>
                    <td><strong><?php echo $seance['seance']; ?></strong></td>
                    <td><strong>
                            <?php
                            if ($seance['validation'] < 10) {
                                echo $texte[$seance['validation']];
                            } else if ($seance['validation'] == 10) {
                                $controleur = 'seance/derouler_seance/' . $seance['id_seance'] . '/0/' . $id_parcours . '/' . $id_eleve; //
                                echo button_anchor_popup($controleur, 'primary', 'Commencer');
//                                if (test_acces(R_MON)) {
//                                    echo '&nbsp;&nbsp;&nbsp;';
//                                    echo button_anchor('suivi/seance_valider/' . $id_parcours . '/' . $seance['id_seance'] . '/' . $id_eleve, 'primary', 'Finaliser');
//                                }
                            }
                            ?>
                        </strong></td>
                    <td>
                        <?php
                        if ($seance['validation'] != 10) {
                            echo button_anchor_popup("suivi/lire_commentaires/$id_eleve/" . $seance['fk_notation']."/".$id_parcours, 'primary', 'Commentaires');
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
