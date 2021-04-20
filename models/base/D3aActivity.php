<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace d3yii2\d3activity\models\base;

use d3system\dictionaries\SysModelsDictionary;
use d3yii2\d3activity\dictionaries\D3aActionDictionary;
use d3yii2\d3activity\models\D3aActivityQuery;
use d3yii2\d3activity\models\SysModels;
use Yii;

use d3system\behaviors\D3DateTimeBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the base-model class for table "d3a_activity".
 *
 * @property string $id
 * @property integer $sys_company_id
 * @property integer $user_id
 * @property string $time
 * @property integer $sys_model_id
 * @property integer $model_id
 * @property integer $action_id
 * @property string $data
 *
 * @property \d3yii2\d3activity\models\D3aAction $action
 * @property SysModels $sysModel
 * @property string $aliasModel
 */
abstract class D3aActivity extends ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'd3a_activity';
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = [
        ];
        $behaviors = array_merge(
            $behaviors,
            D3DateTimeBehavior::getConfig(['time'])
        );
        return $behaviors;
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'required' => [['sys_model_id', 'model_id', 'action_id'], 'required'],
            'tinyint Unsigned' => [['sys_model_id'],'integer' ,'min' => 0 ,'max' => 255],
            'smallint Unsigned' => [['sys_company_id','user_id','action_id'],'integer' ,'min' => 0 ,'max' => 65535],
            'integer Unsigned' => [['model_id'],'integer' ,'min' => 0 ,'max' => 4294967295],
            'bigint Unsigned' => [['id'],'integer' ,'min' => 0 ,'max' => 1.844674407371E+19],
            [['time'], 'safe'],
            [['data'], 'string'],
            [['action_id'], 'in', 'range' => array_keys(D3aActionDictionary::getList())],
            [['sys_model_id'], 'in', 'range' => array_keys(SysModelsDictionary::getClassList())],
            'D3DateTimeBehavior' => [['time_local'],'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('docreg', 'ID'),
            'sys_company_id' => Yii::t('docreg', 'Sys Company ID'),
            'user_id' => Yii::t('docreg', 'User ID'),
            'time' => Yii::t('docreg', 'Time'),
            'sys_model_id' => Yii::t('docreg', 'Sys Model ID'),
            'model_id' => Yii::t('docreg', 'Model ID'),
            'action_id' => Yii::t('docreg', 'Action ID'),
            'data' => Yii::t('docreg', 'Data'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAction()
    {
        return $this->hasOne(\d3yii2\d3activity\models\D3aAction::className(), ['id' => 'action_id'])->inverseOf('d3aActivities');
    }

    /**
     * @return ActiveQuery
     */
    public function getSysModel()
    {
        return $this->hasOne(SysModels::className(), ['id' => 'sys_model_id'])->inverseOf('d3aActivities');
    }


    
    /**
     * @inheritdoc
     * @return D3aActivityQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new D3aActivityQuery(get_called_class());
    }


}
