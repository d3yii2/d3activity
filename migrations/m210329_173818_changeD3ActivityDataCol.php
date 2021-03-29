<?php

use yii\db\Migration;

/**
* Class m210329_173818_changeD3ActivityDataCol*/
class m210329_173818_changeD3ActivityDataCol extends Migration
{
    /**
    * {@inheritdoc}
    */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE `d3a_activity`
                CHANGE COLUMN `data` `data` TEXT NULL DEFAULT NULL COLLATE 'utf8_general_ci' AFTER `action_id`;
        ");
    }

    public function safeDown()
    {
        echo "m210329_173818_changeD3ActivityDataCol cannot be reverted.\n";
        return false;
    }

}