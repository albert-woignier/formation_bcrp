
<div class="starter-template">   
    <h1><?php echo $titre; ?></h1>
    <p><?php echo $message; ?></p>
</div>

<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Parcours</th>
                <th scope="col">Sélectionner</th>
                <th scope="col">Discipline</th>
                <th scope="col">Niveau</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($les_parcours as $parcours) : ?>
            <tr>
                <th scope="row"><?php echo $parcours['rowid']; ?></th>
                <td style="font-weight: bold;"><?php echo $parcours['intitule']; ?><td>
                <td><a href="<?php echo $action; ?><?php echo $parcours['rowid']; ?>"><button type="button" class="btn btn-outline-primary">Selectionner</button></a></td>
                <td>><?php echo $parcours['discipline']; ?></td>
                <td>><?php echo $parcours['niveau']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>
<p>
    &nbsp;
</p>

