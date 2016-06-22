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
     * Send sms
     *
     * @param string $text
     * @param $phones
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
            $result = $this->sendMessage($text, $phone);
            $this->saveToDb($text, $phone, $result);
        }
    }
    /**
     * Connetc to Turbosms by Soap
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
     * @param string $result
     *
     * @return bool
     */
    public function saveToDb($text, $phone, $result)
    {
        if (!$this->saveToDb) {
            return false;
        }
        $model = new TurboSmsSent();
        $model->text = $text;
        $model->phone = $phone;
        $model->message = $result['message'] . ($this->debug ? $this->debugSuffixMessage : '');
	    if(isset($result['message_id'])) $model->message_id = $result['message_id'];
        $model->status = $this->sendStatus;
        $model->save();
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
            return'';
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
     * @return string
     */
    protected function sendMessage($text, $phone)
    {
	    $return = [
		    'message' => $this->successMessage
	    ];
        // set default status
        $this->sendStatus = 1;
        if ($this->debug) {
            return $return;
        }
        $result = $this->getClient()->SendSMS([
            'sender' => $this->sender,
            'destination' => $phone,
            'text' => $text
        ]);

	    if(isset($result->SendSMSResult->ResultArray[1]))
		    $return['message_id'] = $result->SendSMSResult->ResultArray[1];

        if (empty($result->SendSMSResult->ResultArray[0]) ||
            $result->SendSMSResult->ResultArray[0] != 'Сообщения успешно отправлены') {
            $this->sendStatus = 0;
	        $return['message'] = preg_replace('/%error%/i', $result->SendSMSResult->ResultArray, $this->errorMessage);
        }
        return $return;
    }
}