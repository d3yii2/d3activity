<?php

use yii\db\Migration;
use d3yii2\d3activity\models\D3aAction;

/**
* Class m220124_170410_update_d3persons_d3pop_labels*/
class m220124_170410_update_d3persons_d3pop_labels extends Migration
{
    /**
    * {@inheritdoc}
    */
    public function safeUp()
    {
        $LVlabels = [
            'user/registration/connect' => 'Autorizēšanās caur sociālo vietni',
            'd3persons/user/delete' => 'Lietotāja dzēšana',
            'd3persons/user/block' => 'Lietotāja bloķēšana',
            'd3persons/user/editable' => 'Lietotāja rediģēšana',
            'd3yii2/d3pop2/D3PoP3/read' => 'Saņemts e-pasts', // ???
            'd3yii2/d3pop3/D3PoP3/read' => 'Saņemts e-pasts', // ???
            'd3persons/user/create' => 'Lietotāja izveidošana',
            'd3persons/users/send-recovery-message' => 'Lietotāja konta atgūšana'
        ];

        foreach ($LVlabels as $name => $label) {
            if($action = D3aAction::findOne(['name' => $name])) {
                $action->setLabel('lv-LV',$label);
            }
        }
    }

    public function safeDown()
    {
        echo "m220124_170410_update_d3persons_d3pop_labels cannot be reverted.\n";
        return false;
    }

}