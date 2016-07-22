<?php

namespace avator\turbosms\models;

use Yii;

/**
 * This is the model class for table "turbo_sms_sent".
 *
 * @property integer $id
 * @property string $date_sent
 * @property string $text
 * @property string $phone
 * @property string $message
 * @property string $status
 * @property string $message_id
 */
class TurboSmsSent extends \yii\db\ActiveRecord
{
    const STATUS_SUCCESS = 1;
    const STATUS_ERROR = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%turbo_sms_sent}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date_sent'], 'safe'],
            [['text'], 'string'],
            [['phone', 'status'], 'integer'],
            [['phone', 'message', 'message_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'date_sent' => Yii::t('app', 'Date Sent'),
            'text' => Yii::t('app', 'Text'),
            'phone' => Yii::t('app', 'Phone'),
            'message' => Yii::t('app', 'Message'),
            'status' => Yii::t('app', 'Status'),
            'message_id' => Yii::t('app', 'Message ID'),
        ];
    }
}
