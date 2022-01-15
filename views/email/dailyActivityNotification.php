<?php
use d3yii2\d3activity\dictionaries\D3aActionLabelDictionary;
use yii2d3\d3persons\models\D3pPerson;
use yii2d3\d3persons\models\User;
/**
 * @var \yii\data\ActiveDataProvider $activities
 */


?>

<div class="">
        <table class="report_table">
            <thead>
            <th>Name</th>
            <th>User </th>
            <th>Date Time</th>
            </thead>
            <tbody>
            <?php foreach ($activities as $a) { ?>
                <tr>
                    <td><?= D3aActionLabelDictionary::getLabel($a->action_id) ?></td>
                    <td><?php
                        if($user = User::findOne($a->user_id)) {
                            if($person = D3pPerson::findOne(['user_id' => $a->user_id])) {
                                echo $person->getFullName();
                            } else {
                                echo $user->username;
                            }
                        } else {
                            echo '-';
                        }
                        ?></td>
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
