<?php


namespace d3yii2\d3activity\actions;

use \d3system\actions\D3EditableAction as BaseD3EditableAction;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
use Yii;

class D3EditableAction extends BaseD3EditableAction
{


    public function init()
    {
        parent::init();
    }

    public function run(int $id)
    {
        $request = Yii::$app->request;

        if ($request->post('hasEditable')) {
            $requestPost = $request->post();
            unset($requestPost['hasEditable']);

            $model = $this->findModel($id);
            $this->controller->module->activityRegistar->registerModel($model, $this->controller->route,ArrayHelper::toArray($requestPost));
        }

        return parent::run($id);
    }


    protected function findModel(int $id)
    {

        if (method_exists($this->controller, $this->methodName)) {
            return $this->controller->{$this->methodName}($id);
        }

        if (!class_exists($this->modelName)) {
            throw new HttpException(404, Yii::t('crud', 'Cannot update this field.'));
        }

        if (($model = $this->modelName::findOne($id)) === null) {
            throw new HttpException(404, Yii::t('crud', 'Cannot update this field.'));
        }
        return $model;
    }
}