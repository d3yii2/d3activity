<?php

use yii\db\Migration;

class m240105_115212_d3yii2_d3activity_activity_change_data_to_utf8mb4  extends Migration {

    public function safeUp() { 
        $this->execute('
            ALTER TABLE `d3a_activity`
              CHANGE `data` `data` TEXT CHARSET utf8mb4 NULL;
            
                    
        ');
    }

    public function safeDown() {
        echo "m240105_115212_d3yii2_d3activity_activity_change_data_to_utf8mb4 cannot be reverted.\n";
        return false;
    }
}
