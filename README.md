# Yii Loggly

This module is a log writer for [Yii](http://www.yiiframework.com/) that will send all log messages to a [Loggly](http://loggly.com/) input.

### Requirements

 - php >= 5.2
 - php5-curl extension
 - Yii 1.1.13 (should work on prior versions but not tested)

### Usage

Decompress put Loggly folder on protected/extensions/
In protected/config/main.php in components section add:

```php
    'log' => array(
        'class' => 'CLogRouter',
        'routes' => array(
                array(
                    'class'=>'ext.Loggly.LogglyRoute',
                    'inputKey' => '<put here your input key>',
                    'finishRequest' => true,
                    'levels'=>'error, warning',
                ),
        ),
    ),
```

### Resources

 - (GitHub repo)[https://github.com/aotd1/yii-loggly]
