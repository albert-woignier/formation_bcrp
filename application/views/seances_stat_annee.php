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
                <th scope="col">Année</th>
                <th scope="col">Nombre de séances</th>
                <th scope="col">Nombre d'élèves</th>
                <th scope="col">Nombre de moniteurs</th>

        </thead>
        <tbody>
            <?php foreach ($les_seances as $seance) : ?>
                <tr>

                    <td><?php echo $seance['An']; ?></td>
                    <td><?php echo $seance['nb_seances']; ?></td>
                    <td><?php echo $seance['nb_eleves']; ?></td>
                    <td><?php echo $seance['nb_moniteurs']; ?></td>
                   </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
