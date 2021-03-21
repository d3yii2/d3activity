<?php


namespace d3yii2\d3activity\components;


interface ModelActivityInterface
{
    /**
     * @param int[] $idList model record id list
     * @return ActivityRecord[]
     */
    public static function findByIdlist(array $idList): array;
}