<div class="starter-template container">
    <h1><?php echo $titre; ?></h1>
    <p><?php // echo $commentaires;   ?></p>
</div>


<div class=""container>
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Personne</th>
                <th scope="col">--</th>
                <th scope="col">date</th>
                <th scope="col">IP</th>
                <th scope="col">écran</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            foreach ($logins as $login) :      
            ?>
                <tr>
                    <td ><strong><?php echo $login['nom']; ?></strong></td>
                    <td><?php echo $login['cat']; ?></td>
                    <td><?php echo $login['date_in']; ?></td>
                    <td><?php echo $login['ip_address']; ?></td>
                    <td><?php echo $login['winsize']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>


