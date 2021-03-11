<?php

namespace d3yii2\d3activity\components;

use d3system\dictionaries\SysModelsDictionary;
use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3activity\dictionaries\D3aActionDictionary;
use d3yii2\d3activity\models\D3aActivity;
use yii\base\Component;
use yii\helpers\Json;


class DbActivityRegistar  extends Component {


    /**
     * @var int
     */
    private $sysCompanyId;

    /**
     * @var int
     */
    private $userId;

    public function init(): void
    {
        $this->sysCompanyId = \Yii::$app->SysCmp->getActiveCompanyId();
        $this->userId = \Yii::$app->user->id;
    }

    public function registerModel(object $model, string $action, array $data = [])
    {
        $model = $this->newD3aActivity($action, $data);
        $model->sys_model_id = SysModelsDictionary::getIdByClassName(get_class($model));
        $model->model_id = $model->id;
        if(!$model->save()){
            throw new D3ActiveRecordException($model);
        }
    }

    public function registerClasNameId(string $className,int $id, string $action, array $data = [])
    {
        $model = $this->newD3aActivity($action, $data);
        $model->sys_model_id = SysModelsDictionary::getIdByClassName($className);
        $model->model_id = $id;
        if(!$model->save()){
            throw new D3ActiveRecordException($model);
        }
    }

    /**
     * @param string $action
     * @return D3aActivity
     * @throws D3ActiveRecordException
     */
    private function newD3aActivity(string $action, array $data): D3aActivity
    {
        $model = new D3aActivity();
        $model->sys_company_id = $this->sysCompanyId;
        $model->user_id = $this->userId;
        $model->time = date('Y-m-d H:i:s');
        $model->action_id = D3aActionDictionary::getIdByName($action);
        $model->data = Json::encode($data);
        return $model;
    }
}
