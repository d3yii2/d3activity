<?php


namespace d3yii2\d3activity\tests\components;


use yii\db\ActiveRecord;

class TestModel extends ActiveRecord
{
    static public $tableName = 'ActivityTestTable';
    public $id;
}