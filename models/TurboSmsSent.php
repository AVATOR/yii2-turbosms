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
 * @property string $status
 * @property integer $status_code
 */
class TurboSmsSent extends \yii\db\ActiveRecord
{
    const STATUS_CODE_SUCCESS = 1;
    const STATUS_CODE_FAIL = 0;

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
            [['phone', 'status'], 'string', 'max' => 255],
            [['status_code'], 'integer'],
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
            'status' => Yii::t('app', 'Status'),
            'status_code' => Yii::t('app', 'Status Code'),
        ];
    }
}
