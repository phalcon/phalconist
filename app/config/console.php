<?php

/** @var \Phalcon\Config $services */
$services = require 'services.php';

$services->merge(new \Phalcon\Config([
    'loader' => function () {
        $loader = new \Phalcon\Loader();
        $loader->registerDirs([APP_PATH . '/console/',]);
        $loader->register();
        return $loader;
    },
    'router' => '\Phalcon\Cli\Router',
    'cronLogger' => [
        'className' => '\Phalcon\Logger\Adapter\File',
        'arguments' => array(
            array('type' => 'parameter', 'value' => $services->get('config')->cronLogger->path . '/info.log'),
        ),
        'calls' => [
            ['method' => 'setLogLevel', 'arguments' => [
                ['type' => 'parameter', 'value' => \Phalcon\Logger::INFO],
            ]],
        ],
    ],
]));

return $services;