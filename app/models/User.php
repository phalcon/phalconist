<?php

namespace Models;

use Phalcon\DI\Injectable;

class User extends Injectable
{

    use ElasticModelTrait;

    protected static $index = 'phalconist';

    protected static $type = 'user';
}
