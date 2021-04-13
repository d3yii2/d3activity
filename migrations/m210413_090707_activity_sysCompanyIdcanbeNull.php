<?php

use yii\db\Migration;

class m210413_090707_activity_sysCompanyIdcanbeNull  extends Migration {

    public function safeUp() { 
        $this->execute('
            ALTER TABLE `d3a_activity`   
              CHANGE `sys_company_id` `sys_company_id` SMALLINT(5) UNSIGNED NULL;
                    
        ');
    }

    public function safeDown() {
        echo "m210413_090707_activity_sysCompanyIdcanbeNull cannot be reverted.\n";
        return false;
    }
}
