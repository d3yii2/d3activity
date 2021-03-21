<?php

namespace d3yii2\d3activity;

use d3system\yii2\base\D3Module;
use Yii;

class Module extends D3Module
{
    public $controllerNamespace = 'd3yii2\d3activity\controllers';

    public $leftMenu = 'd3yii2\d3activity\LeftMenu';

    public function getLabel(): string
    {
        return Yii::t('d3activity', 'Activity registry');
    }
}
