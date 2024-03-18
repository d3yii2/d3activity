<?php

use yii\db\Migration;

class m240318_105647_d3modules_activities_activity_add_index  extends Migration {

    public function safeUp() { 
        $this->execute('
            ALTER TABLE `d3a_activity`
              ADD INDEX `action_time` (`action_id`, `time`);
            
                    
        ');
    }

    public function safeDown() {
        echo "m240318_105647_d3modules_activities_activity_add_index cannot be reverted.\n";
        return false;
    }
}
