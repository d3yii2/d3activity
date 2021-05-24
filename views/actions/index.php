<?php

use d3yii2\d3activity\components\ActivityRecord;
use yii\grid\GridView;

/**
 * @var \yii\data\ActiveDataProvider $doclist
 */

?>

<div class="row file-row">
    <div class="col-sm-8">
        <?= GridView::widget([
            'dataProvider' => $doclist,
            'filterModel' => $doclist,
            'layout' => '{items}',
            'columns' => [
                [
                    'header' => Yii::t('activities', 'Name'),
                    'format' => 'raw',
                    'attribute' => 'label',
                ],
                [
                    'attribute' => 'dateTime',
                    'value' => static function (ActivityRecord $model) {
                        return $model->dateTime->format('Y-m-d H:i:s');
                    },
                    'options' => [
                        'style' => 'width:100px;'
                    ]
                ]
            ]
        ]) ?>
    </div>
</div>


