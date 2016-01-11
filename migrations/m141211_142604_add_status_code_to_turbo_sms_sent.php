<?php

use yii\db\Schema;
use yii\db\Migration;

class m141211_142604_add_status_code_to_turbo_sms_sent extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->addColumn('{{%turbo_sms_sent}}', '{{%status_code}}', 'integer');
    }

    public function down()
    {
        $this->dropColumn('{{%turbo_sms_sent}}', '{{%status_code}}');
    }
}
