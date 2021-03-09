<?php

use yii\db\Migration;

class m210309_140707_init  extends Migration {

    public function safeUp() { 
        $this->execute('
            CREATE TABLE `d3a_action` (
              `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
              `name` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1        
        ');

        $this->execute('
            CREATE TABLE `d3a_activity` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `sys_company_id` smallint(5) unsigned NOT NULL,
              `user_id` smallint(5) unsigned DEFAULT NULL,
              `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              `sys_model_id` tinyint(3) unsigned NOT NULL,
              `model_id` int(10) unsigned NOT NULL,
              `action_id` smallint(5) unsigned NOT NULL,
              `data` text,
              PRIMARY KEY (`id`),
              KEY `action_id` (`action_id`),
              KEY `sys_company_id` (`sys_company_id`),
              KEY `sys_model_id` (`sys_model_id`),
              CONSTRAINT `d3a_activity_ibfk_1` FOREIGN KEY (`action_id`) REFERENCES `d3a_action` (`id`),
              CONSTRAINT `d3a_activity_ibfk_2` FOREIGN KEY (`sys_company_id`) REFERENCES `d3c_company` (`id`),
              CONSTRAINT `d3a_activity_ibfk_3` FOREIGN KEY (`sys_model_id`) REFERENCES `sys_models` (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1
        
        ');
    }

    public function safeDown() {
        echo "m210309_140707_init cannot be reverted.\n";
        return false;
    }
}
