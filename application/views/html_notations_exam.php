<!------   html dees notations de sfigures d'un examen -->

<div class="container">
     <h5>Détail des points obtenus</h5>
     <table style=" border: 1px solid black;">
<?php
if ($discipline_id == 1) :
    // carambole
?>
            <thead>
            <th style=" border: 1px solid black;">Numéro de figure</th>
            <th style=" border: 1px solid black;">Points</th>
            <th style=" border: 1px solid black;">Bonus</th>
            <th style=" border: 1px solid black;">Total</th>
            </thead>
            <tbody>
                <?php
                $total_note = 0;
                $total_bonus = 0;
                // trace('CARAMBOLE html_notations_exam', $notes_figures);
                foreach ($notes_figures as $row) :
                    $note = $row['note'];
                    if ($row['bonus'] == -1) {
                        $bonus = '---';
                        $total = $note;
                    } else {
                        $bonus = $row['bonus'];
                        $total = $note + $bonus;
                        $total_bonus += $bonus;
                    }
                    $total_note += $note;
                    ?>
                    <tr>
                        <td style=" border: 1px solid black;"><?php echo $row['num_exo']; ?></td>
                        <td style=" border: 1px solid black;"><?php echo $note; ?></td>
                        <td style=" border: 1px solid black;"><?php echo $bonus; ?></td>
                        <td style=" border: 1px solid black;"><?php echo $total; ?></td>
                    </tr>
                    <?php
                endforeach;
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th style=" border: 1px solid black;">TOTAUX</th>
                    <th style=" border: 1px solid black;"><?php echo $total_note; ?></th>
                    <th style=" border: 1px solid black;"><?php echo $total_bonus; ?></th>
                    <th style=" border: 1px solid black;"><?php echo $total_note + $total_bonus; ?></th>
                </tr>
            </tfoot>
<?php 
elseif ($discipline_id == 3 OR $discipline_id == 4 ) :
    // américain ou blackball
    //  "Figure", "essai 1", "essai 2", "essai 3", "Points"
?>   
            <thead>
            <th style=" border: 1px solid black;">Figure</th>
            <th style=" border: 1px solid black;">Essai 1</th>
            <th style=" border: 1px solid black;">Essai 2</th>
            <th style=" border: 1px solid black;">Essai 3</th>
            <th style=" border: 1px solid black;">Points</th>
            </thead>
            <tbody>
                <?php
                $total_note = 0;
                // trace('POCHE html_notations_exam', $notes_figures);
                foreach ($notes_figures as $row) :
                    // trace('html_notations_exam row', $row);
                    $note = $row['note'];
                    $total_note += $note;
                    $essais = json_decode($row['essais']);
                    // trace("json_decode", $essais);
                    ?>
                    <tr>
                        <td style=" border: 1px solid black;"><?php echo $row['num_exo']; ?></td>
                        <td style=" border: 1px solid black;"><?php echo $essais[0]->serie; ?></td>
                        <td style=" border: 1px solid black;"><?php echo $essais[1]->serie; ?></td>
                        <td style=" border: 1px solid black;"><?php echo $essais[2]->serie; ?></td>
                        <td style=" border: 1px solid black;"><?php echo $note; ?></td>
                    </tr>
                    <?php
                endforeach;
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th style=" border: 1px solid black;">TOTAL</th>
                    <th style=" border: 1px solid black;">-</th>
                    <th style=" border: 1px solid black;">-</th>
                    <th style=" border: 1px solid black;">-</th>
                    <th style=" border: 1px solid black;"><?php echo $total_note; ?></th>
                </tr>
            </tfoot>
<?php  
endif;
?>
  </table>
    </div>