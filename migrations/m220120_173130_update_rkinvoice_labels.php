<?php

use yii\db\Migration;
use d3yii2\d3activity\models\D3aAction;

/**
* Class m220120_173130_update_rkinvoice_labels*/
class m220120_173130_update_rkinvoice_labels extends Migration
{
    /**
    * {@inheritdoc}
    */
    public function safeUp()
    {
        $LVlabels = [
            'lietvediba/rk-invoice/editable' => 'Detaļas',
            'lietvediba/contract/editable' => 'Redigēšana',
            'lietvediba/rk-invoice/create' => 'Izveidošana'
        ];

        foreach ($LVlabels as $name => $label) {
            if($action = D3aAction::findOne(['name' => $name])) {
                $action->setLabel('lv-LV',$label);
            }
        }
    }

    public function safeDown()
    {
        echo "m220120_173130_update_rkinvoice_labels cannot be reverted.\n";
        return false;
    }

}