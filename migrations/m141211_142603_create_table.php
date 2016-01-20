<?php

use yii\db\Schema;
use yii\db\Migration;

class m141211_142603_create_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%turbo_sms_sent}}', [
            'id' => Schema::TYPE_PK,
            'date_sent' => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'text' => Schema::TYPE_TEXT,
            'phone' => Schema::TYPE_STRING,
            'status' => Schema::TYPE_STRING,
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%turbo_sms_sent}}');
    }
}
