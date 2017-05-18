<?php
namespace avator\turbosms;

use Yii;
use SoapClient;
use yii\base\InvalidConfigException;
use yii\base\Component;
use avator\turbosms\models\TurboSmsSent;

/**
 *
 * @author AVATOR (Oleksii Golub) <oleksii.v.golub@gmail.com>
 * @since 1.0
 */
class Turbosms extends Component
{
    /**
     * Soap login
     *
     * @var string
     */
    public $login;

    /**
     * Soap password
     *
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $sender;

    /**
     * Debug mode
     *
     * @var bool
     */
    public $debug = false;

    /**
     * @var SoapClient
     */
    protected $client;

    /**
     * Wsdl url
     *
     * @var string
     */
    protected $wsdl = 'http://turbosms.in.ua/api/wsdl.html';

    /**
     * Debug suffix message
     *
     * @var string
     */
    public $debugSuffixMessage = ' (тестовый режим)';

    /**
     * Success message
     *
     * @var string
     */
    public $successMessage = 'Сообщения успешно отправлено';

    /**
     * Error message
     *
     * @var string
     */
    public $errorMessage = 'Сообщения не отправлено (ошибка: "%error%")';
    
    /**
     * Save to db log
     *
     * @var bool
     */
    public $saveToDb = true;

    /**
     * @var int
     */
    protected $sendStatus = 1;

    /**
     * @var string
     */
    protected $lastSendMessageId = '';

    /**
     * @var array
     */
    protected $lastSendMessagesIds = [];

    /**
     * Send sms and return array of message's ids in database
     *
     * @param string $text
     * @param $phones
     *
     * @return array
     *
     * @throws InvalidConfigException
     */
    public function send($text, $phones)
    {
        if (!is_array($phones)) {
            $phones = [$phones];
        }

        foreach ($phones as $phone) {
            if (!$phone) {
                continue;
            }
            $message = $this->sendMessage($text, $phone);
            $this->saveToDb($text, $phone, $message);
        }

        return $this->lastSendMessagesIds;
    }

    /**
     * Connect to Turbosms by Soap
     *
     * @return SoapClient
     * @throws InvalidConfigException
     */
    protected function connect()
    {
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
     * @param string $text
     * @param string $phone
     * @param string $message
     *
     * @return bool
     */
    public function saveToDb($text, $phone, $message)
    {
        if (!$this->saveToDb) {
            return false;
        }
        $model = new TurboSmsSent();
        $model->text = $text;
        $model->phone = $phone;
        $model->message = $message . ($this->debug ? $this->debugSuffixMessage : '');
        if ($this->lastSendMessageId) {
            $model->message_id = $this->lastSendMessageId;
        }
        $model->status = $this->sendStatus;
        $model->save();

        if ((int)$model->id) {
            $this->lastSendMessagesIds[$model->id] = $this->lastSendMessageId;
        }

        return true;
    }

    /**
     * Get balance
     *
     * @return int
     */
    public function getBalance()
    {
        return $this->debug ? 0 : intval($this->getClient()->GetCreditBalance()->GetCreditBalanceResult);
    }

    /**
     * Get message status
     *
     * @param $messageId
     *
     * @return string
     */
    public function getMessageStatus($messageId)
    {
        if ($this->debug || !$messageId) {
            return '';
        }
        $result = $this->getClient()->GetMessageStatus(['MessageId' => $messageId]);

        return $result->GetMessageStatusResult;
    }

    /**
     * Get Soap client
     *
     * @return SoapClient
     * @throws InvalidConfigException
     */
    protected function getClient()
    {
        if (!$this->client) {
            return $this->connect();
        }

        return $this->client;
    }

    /**
     * @param $text
     * @param $phone
     * @return array
     */
    protected function sendMessage($text, $phone)
    {
        $message = $this->successMessage;
        // set default status
        $this->sendStatus = 1;
        // clear variable
        $this->lastSendMessageId = '';
        if ($this->debug) {
            return $message;
        }

        $result = $this->getClient()->SendSMS([
            'sender' => $this->sender,
            'destination' => $phone,
            'text' => $text
        ]);

        if (is_array($result->SendSMSResult->ResultArray) && !empty($result->SendSMSResult->ResultArray[1])) {
            $this->lastSendMessageId = $result->SendSMSResult->ResultArray[1];
        }

        if (empty($result->SendSMSResult->ResultArray[0]) ||
            $result->SendSMSResult->ResultArray[0] != 'Сообщения успешно отправлены'
        ) {
            $this->sendStatus = 0;
            $message = preg_replace('/%error%/i', $result->SendSMSResult->ResultArray, $this->errorMessage);
        }

        return $message;
    }
}
