<?php

namespace d3yii2\d3activity\dictionaries;

use Yii;
use d3yii2\d3activity\models\D3aAction;
use yii\helpers\ArrayHelper;
use d3system\exceptions\D3ActiveRecordException;

class D3aActionDictionary{

    private const CACHE_KEY_LIST = 'D3aActionDictionaryList';
    public static function getIdByName(string $name, bool $isFirstTime = true): int
    {
        $list = self::getList();
        if($id = (int)array_search($name, $list, true)){
            return $id;
        }
        if(!$isFirstTime){
            throw new Exception('Added to CmdCmp ' .$name. ', but not found');
        }
        $model = new D3aAction();
        $model->name = $name;
        if(!$model->save()){
            throw new D3ActiveRecordException($model);
        }

        self::clearCache();
        return self::getIdByName($name, false);

    }
    public static function getList(): array
    {
        return Yii::$app->cache->getOrSet(
            self::CACHE_KEY_LIST,
            static function () {
                return ArrayHelper::map(
                    D3aAction::find()
                    ->select([
                        'id' => 'id',
                        'name' => 'name',
                        //'name' => 'CONCAT(code,\' \',name)'
                    ])
                                        ->orderBy([
                        'name' => SORT_ASC,
                    ])
                    ->asArray()
                    ->all()
                ,
                'id',
                'name'
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
