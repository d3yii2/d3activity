<?php

namespace d3yii2\d3activity;

use Yii;

class LeftMenu
{

    public function list()
    {
        return [
            [
                'label' => Yii::t('d3activity', '????'),
                'type' => 'submenu',
                //'icon' => 'truck',
                'url' => ['/D3Activity/????/index'],
            ],
        ];
    }
}
