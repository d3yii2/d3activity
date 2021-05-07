<?php

namespace d3yii2\d3activity\models;

use d3yii2\d3activity\dictionaries\D3aActionLabelDictionary;
use \d3yii2\d3activity\models\base\D3DActionLabel as BaseD3DActionLabel;

/**
 * This is the model class for table "d3d_action_label".
 */
class D3DActionLabel extends BaseD3DActionLabel
{
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        D3aActionLabelDictionary::clearCache();
    }

    public function afterDelete()
    {
        parent::afterDelete();
        D3aActionLabelDictionary::clearCache();
    }
}
