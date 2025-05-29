<?php
$texte['dev'] = '<span class="bg-dark text-white">- dev -</span>';
$texte['administrateur'] = '<span class="bg-danger text-white">- Administrateur -</span>';
$texte['moniteur'] = '<span class="bg-primary text-white">- Moniteur -</span>';
$texte['apprenant'] = '<span class="bg-success text-white">- Apprenant -</span>';
$texte['invité'] = '<span class="bg-light text-dark">- Invité -</span>';
// $texte['parti'] = '<span class="bg-warning text-dark">- PARTI -</span>';
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
                <?php
                if (is_ok(R_ADMIN)) {
                    echo '<th scope="col">#</th>';
                }
                ?>
                <th scope="col">Nom - Prénom</th>
                <th class="d-none d-sm-block" scope="col">Licence</th>
                <th scope="col">Téléphone</th>
                <th scope="col">Mail</th>
                <th class="d-none d-sm-block" scope="col">Type</th>
                <th scope="col">Parcours</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($les_personnes as $personne) :
                if ($personne['categorie'] != R_DEV OR is_ok(R_DEV)) :
                    ?>
                    <tr>
                        <?php
                        if (is_ok(R_ADMIN)) {
                            echo '<td>' . $personne['rowid'] . '</td>';
                        }
                        ?>
                        <td style="font-weight: bold; color: blue;">
                            <?php
                            if (is_ok(R_ADMIN)) {
                                echo anchor('personne/mod/' . $personne['rowid'], $personne['nom'] . ' ' . $personne['prenom']);
                            } else {
                                echo $personne['nom'] . ' ' . $personne['prenom'];
                            }
                            if (( $personne['categorie'] == R_ADMIN OR $personne['categorie'] == R_MON)) {
                                echo " ({$personne['initiales']})";
                            }
                            ?>
                        </td>
                        <td class="d-none d-sm-block" ><?php echo $personne['license']; ?></td>
                        <td><?php echo $personne['phone']; ?></td>
                        <td><?php echo $personne['mail']; ?></td>
                        <td class="d-none d-sm-block" ><?php echo $texte[$personne['categorie']]; ?></td>
                        <td>
                            <?php
                            // Un moniteur ou admin ne peut s'inscrire à un parcours !
                            if ($personne['categorie'] == R_ELEV ) {
                                echo button_anchor('suivi/pers_par/' . $personne['rowid'], 'primary', 'Parcours');
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                endif;
            endforeach;
            ?>
        </tbody>
    </table>

</div>
