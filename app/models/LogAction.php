<?php

namespace Models;

use Phalcon\DI\Injectable;

class LogAction extends Injectable
{

    use ElasticModelTrait;

    const ACTION_ADD = 'add';

    protected static $index = 'phalconist';

    protected static $type = 'log_action';

}