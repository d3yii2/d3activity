<?php


namespace d3yii2\d3activity\components;


interface ActivityRegistar
{
    /**
     * @param object $model
     * @param string $action
     * @param array $data
     * @return mixed
     */
    public function registerModel(object $model, string $action, array $data = []): void;

    /**
     * @param string $className
     * @param int $id
     * @param string $action
     * @param array $data
     * @return mixed
     */
    public function registerClasNameId(string $className, int $id, string $action, array $data = []): void;

}