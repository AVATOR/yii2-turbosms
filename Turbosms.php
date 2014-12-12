<?php

namespace avat0r\turbosms;

use Yii;
use SoapClient;
use yii\base\InvalidConfigException;
use yii\base\Component;
use common\models\TurboSmsSent;

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
    public $debug = true;

    protected $client;

    /**
     * @var string
     */
    protected $wsdl = 'http://turbosms.in.ua/api/wsdl.html';

    /**
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

            if (!$this->debug) {
                $result = $this->client->SendSMS([
                    'sender' => $this->sender,
                    'destination' => $phone,
                    'text' => $text
                ]);
            }

            $model = new TurboSmsSent();
            $model->text = $text;
            $model->phone = $phone;
            if ($this->debug || $result->SendSMSResult->ResultArray[0] == 'Сообщения успешно отправлены') {
                $model->status = 'Сообщения успешно отправлено' . ($this->debug ? ' (тестовый режим)' : '');
            } else {
                $model->status = 'Сообщения не отправлено (ошибка: "' . $result->SendSMSResult->ResultArray[0] . '")';
            }
            $model->save();
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

}