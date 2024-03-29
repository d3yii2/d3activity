<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace d3yii2\d3activity\models\base;

use Yii;

/**
 * This is the base-model class for table "d3a_action".
 *
 * @property integer $id
 * @property string $name
 *
 * @property \d3yii2\d3activity\models\D3aActivity[] $d3aActivities
 * @property \d3yii2\d3activity\models\D3dActionLabel[] $d3dActionLabels
 * @property string $aliasModel
 */
abstract class D3aAction extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'd3a_action';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'smallint Unsigned' => [['id'],'integer' ,'min' => 0 ,'max' => 65535],
            [['name'], 'string', 'max' => 256]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('docreg', 'ID'),
            'name' => Yii::t('docreg', 'Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getD3aActivities()
    {
        return $this->hasMany(\d3yii2\d3activity\models\D3aActivity::className(), ['action_id' => 'id'])->inverseOf('action');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getD3dActionLabels()
    {
        return $this->hasMany(\d3yii2\d3activity\models\D3dActionLabel::className(), ['action_id' => 'id'])->inverseOf('action');
    }



}
