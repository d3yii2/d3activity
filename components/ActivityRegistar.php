<?php


namespace d3yii2\d3activity\components;


use yii\db\ActiveRecord;

/**
 * Interface ActivityRegistar
 * @package d3yii2\d3activity\components
 * @property int $userId
 */
interface ActivityRegistar
{
    /**
     * registre model
     * @param ActiveRecord $model
     * @param string $action action route
     * @param array $data additional data
     * @return mixed
     */
    public function registerModel(object $model, string $action, array $data = []): void;

    /**
     * registre by class name and record id
     * @param string $className model class name
     * @param int $id model record id
     * @param string $action action route
     * @param array $data additional data
     * @return mixed
     */
    public function registerClasNameId(string $className, int $id, string $action, array $data = []): void;

}