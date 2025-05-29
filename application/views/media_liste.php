<?php $this->load->helper('my_html_helper'); ?>
<div class="starter-template">
    <h1>Les médias téléchargés sur le serveur</h1>
    <p><?php echo $commentaires; ?></p>
</div>
<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Nom du fichier</th>
                <th scope="col">aperçu</th>
                <?php if (test_acces(R_ADMIN)) : ?>
                    <th scope="col">Renommer</th>
                    <th scope="col">Supprimer</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($les_fichiers as $fichier) : ?>

                <tr>
                    <?php
                    if (strpos($fichier, '/') > 0) :
                        // c'est un sous-répertoire on retire le / en fin de chaine
                        $sous_rep = substr_replace($fichier, '', -1, 1);
                        ?>
                        <td><?php echo button_anchor("multimedia/liste/$type" . "/$sous_rep", 'primary', "<strong>dossier --> $sous_rep</strong>"); ?></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>

                        <?php
                    else :
                        if ($sous_repertoire != '') {
                            // on affiche les fichiers d'un sous répertoire
                            $chemin_fichier = $sous_repertoire . '/' . $fichier;
                        } else {
                            $chemin_fichier = $fichier;
                        }
                        ?>
                        <td><strong><?php echo $fichier; ?></strong></td>
                        <td><?php echo button_anchor_popup("multimedia/voir/" . str_replace('/', 'trucmuche_xtron', $chemin_fichier) . "/$type", 'primary', 'Voir'); ?></td>
                        <?php if (test_acces(R_ADMIN)) : ?>
                            <td>
                                <button type="button"  name = "rename" data-type="<?php echo $type; ?>" data-name="<?php echo $fichier; ?>" class=" btn  btn-outline-primary">Renommer</button>
                            </td>
                            <td>
                                <button type="button"  name = "suppr" data-type="<?php echo $type; ?>" data-name="<?php echo $fichier; ?>" class=" btn  btn-outline-primary">Supprimer</button>
                            </td>
                        <?php endif; ?>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>

<div id="dialog-form" title="Renommer fichier">
    <form>
        <fieldset>
            <label for="filename">Nouveau nom du fichier</label>
            <input type="text" name="filename" id="filename" value="" class="text ui-widget-content ui-corner-all">
            <!-- Allow form submission with keyboard without duplicating the dialog button -->
            <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
        </fieldset>
    </form>
</div>

<script>
    var type = '';
    var file = '';
    var dialog = '';
    var directory = '<?php echo $sous_repertoire; ?>';
    var supprimer = function () {
        type = $(this).attr('data-type');
        file = $(this).attr('data-name');
        // alert('action ' + what + '; type = ' + type + '; file = ' + file);
        if (confirm('Voulez-vous vraiment supprimer le fichier \n' + file)) {
            $.post(site_url('multimedia/ajax_suppr'), {type: type, file: file, ss_rep: directory},
            function (data) {

                alert(data);

                location.reload();
            }, "text");

        }

        return false;
    };

    function renommer() {
        var new_name = $("#filename").val();
        dialog.dialog("close");
        $.post(site_url('multimedia/ajax_rename'), {type: type, file: file, new_name: new_name, ss_rep: directory},
        function (data) {

            alert(data);

            location.reload();
        }, "text");
        dialog.dialog("close");
        return false;
    }

    $().ready(function () {
        jquery_communs();
        $("button[name='rename']").click(function () {
            type = $(this).attr('data-type');
            file = $(this).attr('data-name');
            dialog.dialog("open");
        });

        $("button[name='suppr']").click(supprimer);

        dialog = $("#dialog-form").dialog({
            autoOpen: false,
            height: 250,
            width: 450,
            modal: true,
            buttons: {
                "Renommer": renommer,
                "Annuler": function () {
                    dialog.dialog("close");
                }
            }
        });

//        var form = dialog.find("form").on("submit", function (event) {
//            event.preventDefault();
//            renommer();
//        });

    });


</script>