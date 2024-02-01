<?php


namespace d3yii2\d3activity\actions;

use d3system\actions\D3EditableAction as BaseD3EditableAction;
use yii\helpers\ArrayHelper;

class D3EditableAction extends BaseD3EditableAction
{

    public function afterSave($model, array $requestPost): void
    {
        parent::afterSave($model, $requestPost);
        $this->controller->module->activityRegistar->registerModel($model, $this->controller->route,ArrayHelper::toArray($requestPost));

    }

}