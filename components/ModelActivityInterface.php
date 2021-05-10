<?php


namespace d3yii2\d3activity\components;


interface ModelActivityInterface
{
    /**
     * @param int[] $idList model record id list
     * @return ActivityRecord[]
     */
    public static function findByIdList(array $idList, array $filter = []): array;

    /**
     * @param int $id
     * @return mixed
     */
    public static function findModel(int $id);
}