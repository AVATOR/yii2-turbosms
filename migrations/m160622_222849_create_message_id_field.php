<?php

use yii\db\Schema;
use yii\db\Migration;

class m160622_222849_create_message_id_field extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%turbo_sms_sent}}', 'message_id', Schema::TYPE_STRING);

    }

    public function safeDown()
    {
        echo "m160622_222849_create_message_id_field cannot be reverted.\n";

        return false;
    }

}
