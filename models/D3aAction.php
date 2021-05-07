<?php

namespace d3yii2\d3activity\models;

use d3system\yii2\db\D3Db;
use d3yii2\d3activity\dictionaries\D3aActionDictionary;
use d3yii2\d3activity\models\base\D3aAction as BaseD3aAction;

/**
 * This is the model class for table "d3a_action".
 */
class D3aAction extends BaseD3aAction
{

    public static function getDb()
    {
        return D3Db::clone();
    }


    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        D3aActionDictionary::clearCache();
    }

    public function afterDelete()
    {
        parent::afterDelete();
        D3aActionDictionary::clearCache();
    }
    
    public function setLabel(string $language, string $label)
    {
        if($Alabel = D3DActionLabel::findOne(['action_id' => $this->id])) {
            $Alabel->label = $label;
            $Alabel->language = $language;
            $Alabel->save();
        } else {
            $newAlabel = new D3DActionLabel();
            $newAlabel->action_id = $this->id;
            $newAlabel->language = $language;
            $newAlabel->label = $label;
            $newAlabel->save();
        }
    }

}
