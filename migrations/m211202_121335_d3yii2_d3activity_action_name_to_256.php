<?php

use yii\db\Migration;

class m211202_121335_d3yii2_d3activity_action_name_to_256  extends Migration {

    public function safeUp() { 
        $this->execute('
            ALTER TABLE `d3a_action`
              CHANGE `name` `name` VARCHAR (256) CHARSET utf8 COLLATE utf8_general_ci NULL;
            
                    
        ');
    }

    public function safeDown() {
        echo "m211202_121335_d3yii2_d3activity_action_name_to_256 cannot be reverted.\n";
        return false;
    }
}
