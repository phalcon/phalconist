<?php

$router = new \Phalcon\Mvc\Router(false);
$router->removeExtraSlashes(true);
$router->setUriSource(\Phalcon\Mvc\Router::URI_SOURCE_SERVER_REQUEST_URI);
$router->setDefaultNamespace('Controllers');

$router->notFound(array('controller' => 'index', 'action' => 'route404'));

$router->add('/{owner}', ['controller' => 'index', 'action' => 'viewOwner', 'owner' => 0])->setName('owner');
$router->add('/{owner}/{repo}', ['controller' => 'index', 'action' => 'view', 'owner' => 0, 'repo' => 1])->setName('view/item');
$router->add('/{controller:user|oauth}/{action}')->setName('controller/action');
$router->add('/{id:[0-9]+}-{title}', ['controller' => 'index', 'action' => 'view301', 'id' => 0, 'title' => 1]);
$router->add('/id{id:[0-9]+}', ['controller' => 'index', 'action' => 'id', 'id' => 0]);
$router->add('/owner/{owner}', ['controller' => 'index', 'action' => 'viewOwner301', 'owner' => 0]);
$router->add('/{owner}/{repo}/{type}.svg', ['controller' => 'index', 'action' => 'badge', 'owner' => 0, 'repo' => 1, 'type' => 2])->setName('badge');
$router->add('/category/{name}', ['controller' => 'index', 'action' => 'viewCategory', 'name' => 0])->setName('category');
$router->add('/{action:top|fresh|search|owner|new|news|last|add}', ['controller' => 'index'])->setName('action');
$router->add('/', ['controller' => 'index', 'action' => 'index']);

return $router;