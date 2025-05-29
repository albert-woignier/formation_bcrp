<div >
    <h1 style="text-align: center;"><?php echo $titre; ?></h1>
    <p><?php echo $commentaires; ?></p>
</div>


<?php
if (isset($examen) AND $examen == 1) {
    echo '<div style="background-color:blue; text-align: center;color:white;font-weight:bold; padding:0.5rem;">Le cumul de points obtenus est de : ' . $_SESSION['total'] . '</div>';
}
?>

<div>
    <?php echo $contenu; ?>
</div>
<?php
if (isset($pagination)) :
    if ($lien_suivant == '') {
        $disabled = 'disabled';
        $texte_suivant = 'fin de séance';
    } else {
        if ($finalisation == 1) {
            $disabled = '';
            $texte_suivant = 'VALIDATION';
        } else {
            $disabled = '';
            $texte_suivant = 'page suivante';
        }
    }
    ?>
    <div style="margin: 2rem;">
        <ul class="pagination justify-content-center">
            <li class="page-item <?php echo ($lien_precedant == '') ? 'disabled' : ''; ?> ">
                <?php echo anchor($lien_precedant, 'page précédante', 'class="page-link"'); ?>
            </li>
            <li class="page-item active">
                <a class="page-link" href="#">page n° <?php echo $rang; ?></a>
            </li>
            <li  class="page-item <?php echo $disabled; ?>">
                <?php echo anchor($lien_suivant, $texte_suivant, 'class="page-link btn-success" id="nxt_link" '); ?>
            </li>
        </ul>
    </div>
    <?php











endif;
