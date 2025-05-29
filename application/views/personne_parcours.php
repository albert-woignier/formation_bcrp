<?php
$old_par = '';
$i = 0;
?>
<div class="starter-template">
    <h1><?php echo $titre; ?></h1>
    <p><?php echo $commentaires; ?></p>
</div>

<div class="container">
    <table class="table">
        <thead>
            <tr>
                <th scope="col">Parcours</th>
                <th scope="col">Apprenant</th>
                <th scope="col">Date</th>
                <th scope="col">Détail</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($les_eleves as $eleve) : ?>
                <?php
                if ($eleve['id_parcours'] !== $old_par) {
                    $old_par = $eleve['id_parcours'];
                    $i = 1 - $i; // bascule pour le tr
                }
                ?>
                <tr class='tr<?php echo $i; ?>' >
                    <td><strong><?php echo $eleve['intitule']; ?></strong></td>
                    <td><strong><?php echo $eleve['eleve']; ?></strong></td>
                    <td><strong><?php echo date_fr($eleve['date_fin']); ?></strong></td>
                    <td><strong><?php echo button_anchor("suivi/seances/{$eleve['id_eleve']}/{$eleve['id_parcours']}", 'primary', 'Détails'); ?></strong></td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>
