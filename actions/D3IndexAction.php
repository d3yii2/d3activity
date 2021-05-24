<?php


namespace d3yii2\d3activity\actions;

use yii\base\Action;
use yii\data\ActiveDataProvider;

class D3IndexAction extends Action
{
    /** @var \d3yii2\d3activity\components\D3ActivityList */
    public $activityList;

    /** @var string[] */
    public $modelClassNameList = [];

    public $pageSize = 10;

    public $view;

    public function init()
    {
        parent::init();
        if (!$this->view) {
            $this->view = '@vendor/d3yii2/d3activity/views/actions/index';
        }
    }

    public function run(int $page = 1, array $filter = []): string
    {
        if (!$page) {
            $page = 1;
        }
        $this->activityList->filter = $filter;
        $doclistData = new ActiveDataProvider([
            'models' => $this->activityList->getDescListByClassNames($this->modelClassNameList, $this->pageSize),
        ]);

        return $this->controller->render($this->view, [
            'doclist' => $doclistData,
            'page' => $page,
        ]);
    }

}