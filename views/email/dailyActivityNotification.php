<?php
use d3yii2\d3activity\dictionaries\D3aActionLabelDictionary;
/**
 * @var \yii\data\ActiveDataProvider $activities
 */


?>

<div class="row file-row">
        <table style="table-layout: auto">
            <thead>
            <th>Name</th>
            <th>Date Time</th>
            </thead>
            <tbody>
            <?php foreach ($activities as $a) { ?>
                <tr>
                    <td><?= D3aActionLabelDictionary::getLabel($a->action_id) ?></td>
                    <td><?= $a->time?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

</div>


