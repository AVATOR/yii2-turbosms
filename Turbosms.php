<?php

namespace avator\turbosms;

use Yii;
use SoapClient;
use yii\base\InvalidConfigException;
use yii\base\Component;
use avator\turbosms\models\TurboSmsSent;

/**
 *
 * @author AVATOR (Oleksii Golub) <sclub2018@yandex.ua>
 * @since 1.0
 *
 */
class Turbosms extends Component
{

    /**
     * @var string
     */
    public $login;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $sender;

    /**
     * @var bool
     */
    public $debug = false;

    protected $client;

    /**
     * @var string
     */
    protected $wsdl = 'http://turbosms.in.ua/api/wsdl.html';

    /**
     * Send sms
     *
     * @param $text
     * @param $phones
     *
     * @throws InvalidConfigException
     */
    public function send($text, $phones) {

        if (!$this->debug || !$this->client) {
            $this->connect();
        }

        if (!is_array($phones)) {
            $phones = [$phones];
        }
        foreach ($phones as $phone) {

            $message = 'Сообщения успешно отправлено';
            $statusCode = TurboSmsSent::STATUS_CODE_SUCCESS;

            if (!$this->debug) {
                $result = $this->client->SendSMS([
                    'sender' => $this->sender,
                    'destination' => $phone,
                    'text' => $text
                ]);

                if ($result->SendSMSResult->ResultArray[0] != 'Сообщения успешно отправлены') {
                    $message = 'Сообщения не отправлено (ошибка: "' . $result->SendSMSResult->ResultArray[0] . '")';
                    $statusCode = TurboSmsSent::STATUS_CODE_FAIL;
                }
            }

            $this->saveToDb($text, $phone, $message, $statusCode);
        }
    }

    /**
     * @return SoapClient
     * @throws InvalidConfigException
     */
    protected function connect() {

        if ($this->client) {
            return $this->client;
        }

        $client = new SoapClient($this->wsdl);

        if (!$this->login || !$this->password) {
            throw new InvalidConfigException('Enter login and password from Turbosms');
        }

        $result = $client->Auth([
            'login' => $this->login,
            'password' => $this->password,
        ]);

        if ($result->AuthResult . '' != 'Вы успешно авторизировались') {
            throw new InvalidConfigException($result->AuthResult);
        }

        $this->client = $client;

        return $this->client;
    }

    /**
     * Save sms to db
     *
     * @param $text
     * @param $phone
     * @param $message
     * @param $statusCode
     *
     * @return boolean
     */
    public function saveToDb($text, $phone, $message, $statusCode) {
        $model = new TurboSmsSent();
        $model->text = $text;
        $model->phone = $phone;
        $model->status = $message . ($this->debug ? ' (тестовый режим)' : '');
        $model->status_code = $statusCode;
        return $model->save();
    }

    /**
     * Get balance
     *
     * @return int
     */
    public function getBalance() {
        $result = $this->client->GetCreditBalance();
        return intval($result->GetCreditBalanceResult);
    }

    /**
     * @param $messageId
     *
     * @return mixed
     */
    public function getMessageStatus($messageId) {
        $result = $this->client->GetMessageStatus(['MessageId' => $messageId]);
        return $result->GetMessageStatusResult;
    }

}
