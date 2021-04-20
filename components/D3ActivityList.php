<?php


namespace d3yii2\d3activity\components;


use d3system\dictionaries\SysModelsDictionary;
use DateTime;
use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\helpers\ArrayHelper;


/**
 * get activity history list
 *
 * Component definition:
 * ```php
 *  'activityHistory' =>[
 *      'class' => 'd3yii2\components\logic\D3aActivityList',
 *      'sysCompanyId' => static function(){
 *                           return \Yii::$app->SysCmp->getActiveCompanyId();
 *                        },
 *      'modelsData' => [
 *          [
 *              'class'  => 'd3modules\d3invoices\components\InvInvoiceD3Activity'
 *           ]
 *      ]
 *  ]
 * ```
 *
 * Class D3aActivityList
 * @package d3yii2\components\logic
 */
class D3ActivityList extends Component
{

    /** @var int|callable */
    public $sysCompanyId;

    /**
     * definitons for models
     *
     * @var array
     */
    public $modelsData;

    public function __construct($config = [])
    {
        parent::__construct($config);
        if ($this->sysCompanyId && is_callable($this->sysCompanyId, true)) {
            $this->sysCompanyId = call_user_func($this->sysCompanyId);
        }
    }

    /**
     * @param array $modelClassNameList
     * @param int $limit
     * @return \d3yii2\d3activity\components\ActivityRecord[]
     * @throws \d3system\exceptions\D3ActiveRecordException
     * @throws \yii\base\Exception
     */
    public function getDescListByClassNames(array $modelClassNameList, int $limit = 20): array
    {
        $idList = [];
        foreach($modelClassNameList as $className){
            $idList[] = SysModelsDictionary::getIdByClassName($className);
        }

        return $this->getDescList($idList,$limit);
    }

    /**
     * get descending activity record list
     * @param array $sysModelIdList
     * @param int $limit
     * @return ActivityRecord[]
     * @throws Exception
     * @throws \d3system\exceptions\D3ActiveRecordException
     */
    public function getDescList(array $sysModelIdList, int $limit = 20): array
    {
        $baseList = $this->getBaseList($sysModelIdList, $limit);
        $baseList = ArrayHelper::index($baseList, 'rowKey');

        /**
         * grouping by models
         */
        $sysModelsRecordIdList = [];
        foreach ($baseList as $baseRow) {
            $sysModelsRecordIdList[$baseRow['sys_model_id']][] = $baseRow['model_id'];
        }

        /**
         * for each model get ActivityRecords
         */
        $returnList = [];
        foreach ($sysModelsRecordIdList as $sysModelId => $modelIdList) {
            /** @var ModelActivityInterface $modelDetailClass */
            $modelDetailClass = $this->getModelDetailClassName($sysModelId);
            /** @var ActivityRecord[] $modelDetail */
            foreach ($modelDetailClass::findByIdList($modelIdList) as $activityRecord) {
                $key = $sysModelId . ' ' . $activityRecord->recordId;
                $activityRecord->dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $baseList[$key]['maxTime']);
                $returnList[$key] = $activityRecord;
            }
        }

        return $returnList;

    }

    /**
     * get activities fro models
     * @param array $sysModelIdList
     * @param int $limit
     * @return array
     * @throws \yii\db\Exception
     */
    public function getBaseList(array $sysModelIdList, int $limit): array
    {
        if ($sysModelIdList) {
            $sysModels = 'AND sys_model_id IN (' . implode(',', $sysModelIdList) . ')';
        } else {
            $sysModels = '';
        }

        return Yii::$app->db->createCommand(
            '
                    SELECT 
                      sys_model_id,
                      model_id,
                      MAX(`time`) maxTime,
                      CONCAT(sys_model_id,\' \',model_id) rowKey 
                    FROM
                      `d3a_activity` 
                    WHERE sys_company_id = :sysCompanyId 
                      ' . $sysModels . ' 
                    GROUP BY `sys_model_id`,
                      `model_id` 
                    ORDER BY `time` DESC 
                    LIMIT :limit ;            
            ', [
                ':sysCompanyId' => $this->sysCompanyId,
                ':limit' => $limit
            ]
        )
            ->queryAll();
    }

    /**
     * from component config get model deltails class
     * @param int $sysModelId
     * @return string
     * @throws Exception
     * @throws \d3system\exceptions\D3ActiveRecordException
     */
    private function getModelDetailClassName(int $sysModelId): string
    {
        foreach ($this->modelsData as $modelData) {
            if (SysModelsDictionary::getIdByClassName($modelData['modelClass']) === $sysModelId) {
                return $modelData['detailClass'];
            }
        }
        throw new Exception('Can not find model detail class for sysModelId: ' . $sysModelId);
    }
}