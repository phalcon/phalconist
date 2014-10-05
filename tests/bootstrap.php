<?php

if ( ! file_exists($file = __DIR__.'/../vendor/autoload.php')) {
    echo "You must install the dev dependencies using:\n";
    echo "    composer install --dev\n";
    exit(1);
}

$loader = require($file);
$loader->add('Tests', __DIR__);


defined('APP_PATH') || define('APP_PATH', dirname(__FILE__) . '/../app');
defined('WEB_PATH') || define('WEB_PATH', dirname(__FILE__));
defined('ENV') || define('ENV', getenv('ENV') ? getenv('ENV') : 'dev');
$services = require __DIR__ . "/../app/config/services.php";
$serviceLoader = new \Phalcon\DI\Service\Loader();
$serviceLoader->setDefinitions($services);

