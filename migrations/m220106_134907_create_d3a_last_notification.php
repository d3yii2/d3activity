<?php

use yii\db\Migration;

/**
* Class m220106_134907_create_d3a_last_notification*/
class m220106_134907_create_d3a_last_notification extends Migration
{
    /**
    * {@inheritdoc}
    */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE `d3a_last_notification` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `sys_company_id` smallint(5) unsigned NOT NULL,
              `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`)
            )
        ');
    }

    public function safeDown()
    {
        echo "m220106_134907_create_d3a_last_notification cannot be reverted.\n";
        return false;
    }

}