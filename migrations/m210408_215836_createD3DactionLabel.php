<?php

use yii\db\Migration;

/**
* Class m210408_215836_createD3DactionLabel*/
class m210408_215836_createD3DactionLabel extends Migration
{
    /**
    * {@inheritdoc}
    */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE `d3d_action_label`(  
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `action_id` smallint(5) unsigned NOT NULL,
              `language` VARCHAR (25) COMMENT \'Language\',
              `label` VARCHAR (255) COMMENT \'Label\',
              PRIMARY KEY (`id`),
              KEY `action_id` (`action_id`),
              CONSTRAINT `d3a_activity_ibfkl_1` FOREIGN KEY (`action_id`) REFERENCES `d3a_action` (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8; 
        ');
    }

    public function safeDown()
    {
        echo "m210408_215836_createD3DactionLabel cannot be reverted.\n";
        return false;
    }

}