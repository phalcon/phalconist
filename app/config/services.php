<?php

return new \Phalcon\Config([
    'config' => require ENV . '.php',
    'router' => function() {
        return include(APP_PATH . '/config/routes.php');
    },
    'elastica' => function($di) {
        $client = new \Elastica\Client($di->get('config')->elastica->toArray());
        return $client;
    },
    'github' => function($di) {
        $github = $di->get('config')->github;
        $client = new \Github\Client(new \Github\HttpClient\CachedHttpClient([
            'cache_dir' => $github->cache_dir
        ]));
        $client->authenticate($github->client_id, $github->client_secret, \Github\Client::AUTH_URL_CLIENT_ID);
        return $client;
    },
    'packagist' => function($di){
        return new \Packagist\Api\Client();
    },
    'parsedown' => '\Parsedown',
    'url' => '\Library\Url',
    'voltEngine' => [
        'className' => '\Phalcon\Mvc\View\Engine\Volt',
        'calls' => [
            ['method' => 'setOptions', 'arguments' => [
                ['type' => 'parameter', 'value' => [
                    'compiledPath' => APP_PATH . '/../cache/volt/',
                    'compiledSeparator' => '_',
                    'compiledExtension' => '.php',
                    'stat' => true,
                ]],
            ]],
        ],
    ],
    'view'   => [
        'className' => '\Phalcon\Mvc\View',
        'calls' => [
            ['method' => 'setViewsDir', 'arguments' => [
                ['type' => 'parameter', 'value' => APP_PATH . '/views/'],
            ]],
            ['method' => 'registerEngines', 'arguments' => [
                ['type' => 'parameter', 'value' => [
                    '.volt' => 'voltEngine',
                    '.phtml' => 'Phalcon\Mvc\View\Engine\Php',
                ]],
            ]],
        ],
    ],
]);
