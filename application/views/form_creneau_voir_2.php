<!------   view du dialog pop up embeded dans calendrier.php -->
<?php
$etat['wait'] = 'en attente';
$etat['ok'] = 'inscrit';
?>

<div style="font-size: 0.9em">
    <p>
        Places disponibles : <?php echo  '<strong>' . $disponibilite . '</strong>' ; ?><br>

        Moniteur : <strong><?php echo $moniteur; ?></strong><br>

        Le <?php echo '<strong>'.$datefr . '</strong> de <strong>' . substr($heure_debut, 0, -3) . '</strong> à <strong>' . substr($heure_fin, 0, -3).'</strong>'; ?>
    </p>
    <?php
    if (count($eleves) > 0) :
        echo "<p> <i>Apprenants inscrits :</i><br>";
        foreach ($eleves as $eleve) :
            if ($eleve['statut'] !== 'refus') :
                echo '<strong>' . $eleve['eleve'] . '</strong><br>';
            endif;

        endforeach;
        echo "</p>";
    endif;
    ?>
</div>
