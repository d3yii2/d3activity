<?php

use yii\db\Migration;
use d3yii2\d3activity\models\D3aAction;

/**
* Class m210408_223207_fillD3DActionLabel*/
class m210408_223207_fillD3DActionLabel extends Migration
{
    /**
    * {@inheritdoc}
    */
    public function safeUp()
    {
        $LVlabels = [
            'invoice_action_header' => 'Detaļas',
            'invoice_action_items' => 'Vienības ',
            'invoice_action_delivery_items' => 'Piegādes detaļas',
            'invoice_action_payment_terms' => 'Maksājuma nosacījumi',
            'invoice_action_print' => 'Drukāšanas nosacījumi',
            'invoices/wizard/header' => 'Veidņa detaļas',
            'invoices/wizard/items' => 'Veidņa vienības',
            'invoices/wizard/delivery-details' => 'Veidņa piegādes nosacījumi',
            'invoices/wizard/payment-terms' => 'Veidņa maksājuma nosacījumi',
            'invoices/wizard/print' => 'Veidņa drukāšanas nosacījumi',
            'invoices/wizard/advance' => 'Veidņa avansi',
            'invoices/inv-invoice/items-update' => 'Vienību atjaunošana',
            'user/security/login' => 'Autorizēšanās',
            'd3persons/user/view' => 'Lietotāja skats',
            'user/security/logout' => 'Atvienošanās',
            'lietvediba/rk-invoice/editable' => 'Detaļas',
            'deal/deal/create' => 'Izveidot',
            'deal/deal/editable' => 'Rediģēšana',
            'deal/deal/delete' => 'Dzēšana',
            'lietvedība/contract/editable' => 'Redigēšana'
            ];

        foreach ($LVlabels as $name => $label) {
            if($action = D3aAction::findOne(['name' => $name])) {
                $action->setLabel('lv-LV',$label);
            }
        }
    }

    public function safeDown()
    {
        echo "m210408_223207_fillD3DActionLabel cannot be reverted.\n";
        return false;
    }

}