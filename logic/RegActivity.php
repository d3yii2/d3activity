<?php


namespace d3yii2\d3activity\logic;

use d3system\dictionaries\SysModelsDictionary;
use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3activity\dictionaries\D3aActionDictionary;
use d3yii2\d3activity\models\D3aActivity;
use yii\helpers\Json;

class RegActivity
{
    /**
     * @var int
     */
    private $sysCompanyId;

    /**
     * @var int
     */
    private $userId;

    /** @var string */
    private $action;

    /**
     * @var object
     */
    private $model;

    /**
     * @var array
     */
    private $data;

    /** @var string */
    private $modelClassName;

    /**
     * RegActivity constructor.
     * @param int $sysCompanyId
     * @param int $userId
     * @param string $action
     * @param object $model
     * @param array $data
     */
    public function __construct(int $sysCompanyId, int $userId, string $action, object $model, array $data = [])
    {
        $this->sysCompanyId = $sysCompanyId;
        $this->userId = $userId;
        $this->action = $action;
        $this->model = $model;
        $this->data = $data;
    }

    public function setModelClass(string $modelClassName)
    {
        $this->modelClassName = $modelClassName;
    }

    public function save()
    {
        $model = new D3aActivity();
        $model->sys_company_id = $this->sysCompanyId;
        $model->user_id = $this->userId;
        $model->time = date('Y-m-d H:i:s');
        if ($this->modelClassName) {
            $model->sys_model_id = SysModelsDictionary::getIdByClassName($this->modelClassName);
        } else {
            $model->sys_model_id = SysModelsDictionary::getIdByClassName(get_class($this->model));
        }
        $model->model_id = $this->model->id;
        $model->action_id = D3aActionDictionary::getIdByName($this->action);
        $model->data = Json::encode($this->data);
        if (!$model->save()) {
            throw new D3ActiveRecordException($model);
        }
    }
}