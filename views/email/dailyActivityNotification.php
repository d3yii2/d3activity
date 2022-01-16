<?php
use d3yii2\d3activity\dictionaries\D3aActionLabelDictionary;
/**
 * @var \yii\data\ActiveDataProvider $activities
 * @var string $companyName
 */


?>
Company <?=$companyName?> last activities
<div class="">
        <table class="report_table">
            <thead>
            <tr>
            <th>Name</th>
            <th>Date Time</th>
            </tr>
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

<style>
    .report_table {
        table-layout: auto; width:100%;
    }

    .report_table td {
        border: 1px solid black;
        padding: 2px 5px;
    }
    .report_table thead {
        background: #d0d0d0;
    }
</style>
