Yii2 turbosms
=============
Yii2 turbosms

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist avat0r/yii2-turbosms "*"
```

or add

```
"avat0r/yii2-turbosms": "*"
```

to the require section of your `composer.json` file.

## Basic setup

### Configuration

Add the following in your config:

```php
<?php
...
    'components'=>array(
        'turbosms' => [
            'class' => 'avat0r\turbosms\Turbosms',
            'sender' => 'your_sender',
            'login' => 'your_login',
            'password' => 'your_password',
        ],
        ...
    ),
...
```

Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
 <?php Yii::$app->turbosms->send('test', '+380XXXXXXXXX'); ?>
 ```

## License

**yii2-turbosms** is released under the BSD 3-Clause License. See the bundled `LICENSE.md` for details.
