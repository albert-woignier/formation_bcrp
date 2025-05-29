
<div class="starter-template">   
    <h1><?php echo $titre; ?></h1>
    <p><?php echo $message; ?></p>
</div>

<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Séance</th>
                <th scope="col">Sélectionner</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($les_seances as $seance) : ?>
            <tr>
                <th scope="row"><?php echo $seance['rowid']; ?></th>
                <td style="font-weight: bold;"><?php echo $seance['intitule']; ?><td>
                <td><a href="<?php echo $action; ?><?php echo $seance['rowid']; ?>"><button type="button" class="btn btn-outline-primary">Selectionner</button></a></td>

            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>
<p>
    &nbsp;
</p>

