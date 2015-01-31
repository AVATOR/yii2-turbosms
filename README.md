Yii2 turbosms
=============
Yii2 turbosms

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist avator/yii2-turbosms "*"
```

or add

```
"avator/yii2-turbosms": "*"
```

to the require section of your `composer.json` file.

## Basic setup

You should have registered account at http://turbosms.ua/

### Configuration

Add the following in your config:

```php
<?php
...
    'components'=>array(
        'turbosms' => [
            'class' => 'avator\turbosms\Turbosms',
            'sender' => 'your_sender',
            'login' => 'your_login',
            'password' => 'your_password',
        ],
        ...
    ),
...
```
If you want test sms in debug mode change config:
```php
<?php
...
    'components'=>array(
        'turbosms' => [
            'class' => 'avator\turbosms\Turbosms',
            'sender' => 'your_sender',
            'login' => 'your_login',
            'password' => 'your_password',
            'debug' => true,
        ],
        ...
    ),
...
```
in debug mode sms not send only add to db table.

Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
 <?php Yii::$app->turbosms->send('test', '+380XXXXXXXXX'); ?>
 ```

## License

**yii2-turbosms** is released under the BSD 3-Clause License. See the bundled `LICENSE.md` for details.
