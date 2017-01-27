<?php

$config = require ENV . '.php';

//$eventsManager = new \Phalcon\Events\Manager();
//$eventsManager->attach("view:afterRender", new \Library\HtmlCompress());

return new \Phalcon\Config(
    [
        'config' => $config,
        'router' => function () {
            return include(APP_PATH . '/config/routes.php');
        },
        'elastica' => function ($di) {
            $client = new \Elastica\Client($di->get('config')->elastica->toArray());
            return $client;
        },
        'github' => function ($di) {
            $github = $di->get('config')->github;
            $client = new \Github\Client(new \Github\HttpClient\CachedHttpClient(['cache_dir' => $github->cache_dir]));
            if ($github->client_id) {
                $client->authenticate($github->client_id, $github->client_secret, \Github\Client::AUTH_URL_CLIENT_ID);
            }
            return $client;
        },
        'authProvider' => function ($di) {
            $github = $di->get('config')->github;
            $config['redirectUri'] = $github->redirect_url;
            $config['provider']['Github']['applicationId'] = $github->client_id;
            $config['provider']['Github']['applicationSecret'] = $github->client_secret;
            $service = new \SocialConnect\Auth\Service($config, null);
            $service->setHttpClient(new \SocialConnect\Common\Http\Client\Curl());
            $provider = $service->getProvider('Github');
            return $provider;
        },
        'packagist' => function ($di) {
            return new \Packagist\Api\Client();
        },
        'url' => [
            'className' => '\Library\Url',
            'calls' => [
                [
                    'method' => 'setBaseUri',
                    'arguments' => [
                        [
                            'type' => 'parameter',
                            'value' => 'http://' . $_SERVER['SERVER_NAME'] . '/'
                        ]
                    ]
                ]
            ]
        ],
        'fileCache' => [
            'className' => '\Phalcon\Cache\Backend\File',
            'arguments' => [
                [
                    'type' => 'instance',
                    'className' => '\Phalcon\Cache\Frontend\Data',
                    'arguments' => ['lifetime' => 3600]
                ],
                [
                    'type' => 'parameter',
                    'value' => [
                        'cacheDir' => APP_PATH . '/../cache/files/',
                    ]
                ],
            ],
        ],
        'cache' => [
            'className' => '\Phalcon\Cache\Backend\Libmemcached',
            'arguments' => [
                [
                    'type' => 'instance',
                    'className' => '\Phalcon\Cache\Frontend\Data',
                    'arguments' => ['lifetime' => 60]
                ],
                [
                    'type' => 'parameter',
                    'value' => [
                        'host' => 'localhost',
                        'port' => 11211
                    ]
                ]
            ]
        ],
        'viewCache' => [
            'className' => '\Phalcon\Cache\Backend\Libmemcached',
            'arguments' => [
                [
                    'type' => 'instance',
                    'className' => '\Phalcon\Cache\Frontend\Output',
                    'arguments' => ['lifetime' => 60]
                ],
                [
                    'type' => 'parameter',
                    'value' => [
                        'host' => 'localhost',
                        'port' => 11211
                    ]
                ],
            ],
        ],
        'voltEngine' => [
            'className' => '\Phalcon\Mvc\View\Engine\Volt',
            'calls' => [
                [
                    'method' => 'setOptions',
                    'arguments' => [
                        [
                            'type' => 'parameter',
                            'value' => [
                                'compiledPath' => APP_PATH . '/../cache/volt/',
                                'compiledSeparator' => '_',
                                'compiledExtension' => '.php',
                                'stat' => true,
                            ]
                        ],
                    ]
                ],
            ],
        ],
        'view' => [
            'className' => '\Phalcon\Mvc\View',
            'calls' => [
                [
                    'method' => 'setViewsDir',
                    'arguments' => [
                        ['type' => 'parameter', 'value' => APP_PATH . '/views/'],
                    ]
                ],
                //[
                //    'method' => 'setEventsManager',
                //    'arguments' => [
                //        ['type' => 'parameter', 'value' => $eventsManager],
                //    ]
                //],
                [
                    'method' => 'registerEngines',
                    'arguments' => [
                        [
                            'type' => 'parameter',
                            'value' => [
                                '.volt' => 'voltEngine',
                                '.phtml' => 'Phalcon\Mvc\View\Engine\Php',
                            ]
                        ],
                    ]
                ],
            ],
        ],
        'session' => [
            'className' => '\Phalcon\Session\Adapter\Files',
            'calls' => [['method' => 'start']],
        ],
        'cookie' => [
            'className' => 'Phalcon\Http\Response\Cookies',
            'calls' => [
                [
                    'method' => 'useEncryption',
                    'arguments' => [
                        ['type' => 'parameter', 'value' => true],
                    ]
                ],
            ],
        ],
        'log' => [
            'className' => '\Phalcon\Logger\Adapter\File',
            'arguments' => [
                ['type' => 'parameter', 'value' => APP_PATH . '/../logs/app.log'],
            ],
        ],
        'flash' => [
            'className' => '\Phalcon\Flash\Session',
            'arguments' => [
                [
                    'type' => 'parameter',
                    'value' => [
                        'error' => 'alert alert-danger col-lg-10 col-lg-offset-1',
                        'success' => 'alert alert-success',
                        'notice' => 'alert alert-info',
                        'warning' => 'alert alert-warning',
                    ]
                ]
            ]
        ],
    ]
);
