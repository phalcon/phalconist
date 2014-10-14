<?php

defined('APP_PATH') || define('APP_PATH', dirname(__FILE__) . '/../app');
defined('WEB_PATH') || define('WEB_PATH', dirname(__FILE__));
defined('ENV') || define('ENV', getenv('ENV') ? getenv('ENV') : 'dev');

try {

    require_once __DIR__ . '/../vendor/autoload.php';

    /**
     * Read services
     */
    $services = require __DIR__ . "/../app/config/services.php";

    /**
     * Load services
     */
    $serviceLoader = new \Phalcon\DI\Service\Loader();
    $serviceLoader->setDefinitions($services);

    /**
     * Handle the request
     */
    $application = new \Phalcon\Mvc\Application($serviceLoader->getDI());
    echo $application->handle()->getContent();
} catch(\Exception $e) {
    if (ENV == 'dev') {
        var_dump($e);
    } else {
        echo $application->handle('/index/route404')->getContent();
    }
}
