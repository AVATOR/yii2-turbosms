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
        $this->renameColumn('{{%turbo_sms_sent}}', 'message', 'status');
        $this->dropColumn('{{%turbo_sms_sent}}', 'status');
    }

}
