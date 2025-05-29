<?php
$texte[2] = '<span class="bg-success text-white">parcours validé</span>';
$texte[1] = '<span class="bg-info text-white">validé</span>';
$texte[0] = '<span class="bg-warning text-white">à refaire</span>';
$texte[''] = '';
?>
<div class="starter-template">
    <h1><?php echo $titre; ?></h1>
</div>
<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Date</th>
                <th scope="col">Moniteur</th>
                <th scope="col">Apprenant</th>
                <th scope="col">Parcours</th>
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
                    <td><?php echo $seance['eleve']; ?></td>
                    <td><?php echo $seance['parcours']; ?></td>
                    <td><?php echo $seance['seance']; ?></td>
                    <td><?php echo $texte[$seance['validation']]; ?></td>
                    <td><?php echo button_anchor_popup("suivi/lire_commentaires/". $seance['fk_eleve']. "/" . $seance['fk_notation']."/".$seance['fk_parcours'], 'primary', 'Commentaires'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
