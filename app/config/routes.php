<?php

$router = new \Phalcon\Mvc\Router(false);
$router->removeExtraSlashes(true);
$router->setUriSource(\Phalcon\Mvc\Router::URI_SOURCE_SERVER_REQUEST_URI);
$router->setDefaultNamespace('Controllers');

$router->notFound(array('controller' => 'index', 'action' => 'route404'));

$router->add('/{action}', ['controller' => 'index'])->setName('action');
$router->add('/{controller}/{action}')->setName('controller/action');
$router->add('/{id}-{title}', ['controller' => 'index', 'action' => 'view', 'id' => 0, 'title' => 1])->setName('view/item');

return $router;