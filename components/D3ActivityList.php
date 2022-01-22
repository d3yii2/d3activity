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

    /** @var array */
    public $filter = [];

    /** @var array */
    public $additionalFields = [];

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
     * @param int $offset
     * @return \d3yii2\d3activity\components\ActivityRecord[]
     * @throws \d3system\exceptions\D3ActiveRecordException
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public function getDescListByClassNames(array $modelClassNameList, int $limit = 20, int $offset = 0): array
    {
        $idList = [];
        foreach($modelClassNameList as $className){
            $idList[] = SysModelsDictionary::getIdByClassName($className);
        }

        return $this->getDescList($idList,$limit, $offset);
    }

    /**
     * get descending activity record list
     * @param array $sysModelIdList
     * @param int $limit
     * @param int $offset
     * @return ActivityRecord[]
     * @throws \d3system\exceptions\D3ActiveRecordException
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public function getDescList(array $sysModelIdList, int $limit = 20, int $offset = 0): array
    {
        $baseList = $this->getBaseList($sysModelIdList, (int)($limit * 1.3), $offset);
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
        foreach ($sysModelsRecordIdList as $sysModelId => $modelIdList) {
            /** @var ModelActivityInterface $modelDetailClass */
            if ($modelDetailClass = $this->getModelDetailClassName($sysModelId)) {
                /** @var ActivityRecord[] $modelDetail */
                foreach ($modelDetailClass::findByIdList($modelIdList, $this->filter, $this->additionalFields) as $activityRecord) {
                    $key = $sysModelId . ' ' . $activityRecord->recordId;
                    $activityRecord->dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $baseList[$key]['maxTime']);
                    $baseList[$key]['record'] = $activityRecord;
                }
            }
        }
        $returnList = [];
        /**
         * for deleted model record not found record and its ignore
         */
        foreach($baseList as $baseListKey => $baseListRow){

            if(!isset($baseListRow['record'])){
                continue;
            }
            $returnList[$baseListKey] = $baseListRow['record'];
            if(count($returnList) >= $limit){
                break;
            }
        }
        $this->filter = [];
        $this->additionalFields = [];
        return $returnList;

    }

    /**
     * get activities fro models
     * @param array $sysModelIdList
     * @param int $limit
     * @param int $offset
     * @return array
     * @throws \yii\db\Exception
     */
    public function getBaseList(array $sysModelIdList, int $limit, int $offset): array
    {

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
                      AND sys_model_id IN (' . implode(',', $sysModelIdList) . ')
                    GROUP BY `sys_model_id`,
                      `model_id` 
                    ORDER BY `time` DESC 
                    LIMIT :limit OFFSET :offset;            
            ', [
                ':sysCompanyId' => $this->sysCompanyId,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        )
        ->queryAll();
    }

    /**
     * from component config get model deltails class
     * @param int $sysModelId
     * @return string|null
     * @throws Exception
     * @throws \d3system\exceptions\D3ActiveRecordException
     */
    private function getModelDetailClassName(int $sysModelId): ?string
    {
        foreach ($this->modelsData as $modelData) {
            if (SysModelsDictionary::getIdByClassName($modelData['modelClass']) === $sysModelId) {
                return $modelData['detailClass'];
            }
        }
        return null;
    }
}
