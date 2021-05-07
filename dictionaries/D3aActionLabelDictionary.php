<?php

namespace d3yii2\d3activity\dictionaries;

use Yii;
use d3yii2\d3activity\models\D3DActionLabel;
use yii\helpers\ArrayHelper;
use d3system\exceptions\D3ActiveRecordException;

class D3aActionLabelDictionary{

    private const CACHE_KEY_LIST = 'D3aActionLabelDictionaryList';
    public static function getList(): array
    {
        return Yii::$app->cache->getOrSet(
            self::CACHE_KEY_LIST,
            static function () {
                return ArrayHelper::map(
                    D3DActionLabel::find()
                 //       ->where(['language' => 'lv-LV'])   //  Yii::$app->language  ? en-US
                    ->select([
                        'action_id' => 'action_id',
                        'label' => 'label'
                    ])
                                        ->orderBy([
                        'label' => SORT_ASC,
                    ])
                    ->asArray()
                    ->all()
                ,
                'action_id',
                'label'
                );
            },
            60 * 60
        );
    }

    /**
    * get label
    * @param int $id
    * @return string|null
    */
    public static function getLabel(int $id)
    {
        return self::getList()[$id]??null;
    }

    public static function clearCache(): void
    {
        Yii::$app->cache->delete(self::CACHE_KEY_LIST);
    }
}
