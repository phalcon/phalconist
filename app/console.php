<?php

defined('APP_PATH') || define('APP_PATH', dirname(__FILE__));
if (!defined('ENV') && array_search('env=prod', $argv) !== false) {
    define('ENV', 'prod');
} else {
    define('ENV', getenv('ENV') ? getenv('ENV') : 'dev');
}

$_SERVER['SERVER_NAME'] = 'phalconist.com';

try {

    require_once __DIR__ . '/../vendor/autoload.php';

    $services = require APP_PATH . '/config/console.php';

    /**
     * Load services
     */
    $di = new \Phalcon\DI\FactoryDefault\CLI();
    $serviceLoader = new \Phalcon\DI\Service\Loader($di);
    $serviceLoader->setDefinitions($services, ['loader']);

    /**
     * Handle the request
     */
    array_shift($argv);
    $task = array_shift($argv);
    $action = array_shift($argv);
    $handle_params = [];
    foreach ($argv as $param) {
        if (strpos($param, '=') === false) {
            $handle_params[] = $param;
        } else {
            list($name, $value) = explode('=', $param);
            $handle_params[$name] = $value;
        }
    }
    $handle_params = array_merge(
        [
            'task'   => $task,
            'action' => $action
        ],
        $handle_params
    );

    $console = new \Phalcon\CLI\Console($di);
    $di->setShared('console', $console);
    $console->handle($handle_params);
} catch(\Exception $e) {
    error_log('[' . date('Y-m-d H:i:s') . '] -- ' . $e->getMessage());
    error_log($e->getTraceAsString() . "\n");
    echo '[' . date('Y-m-d H:i:s') . '] -- ' . $e->getMessage() . "\n";
    exit(255);
}
