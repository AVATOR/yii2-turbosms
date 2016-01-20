<?php

use yii\db\Schema;
use yii\db\Migration;

class m160117_203749_rename_and_create_field extends Migration
{
    public function up()
    {
        $this->renameColumn('{{%turbo_sms_sent}}', 'status', 'message');
        $this->addColumn('{{%turbo_sms_sent}}', 'status', Schema::TYPE_SMALLINT);

    }

    public function down()
    {
        echo "m160117_203749_rename_and_create_field cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
