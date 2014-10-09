<?php

namespace Models;

use Phalcon\DI\Injectable;

class LogAction extends Injectable
{

    use ElasticModelTrait;

    const ACTION_ADD = 'add';
    const ACTION_LOGIN = 'login';
    const ACTION_LOGOUT = 'logout';
    const ACTION_DELETE = 'delete';

    protected static $index = 'phalconist';

    protected static $type = 'log_action';


    /**
     * @param string     $action
     * @param string|int $user_id
     * @param array      $data
     * @return \Elastica\Response
     */
    public static function log($action, $user_id, array $data = [])
    {
        return self::add(
            $doc = [
                    '_uid' => $user_id,
                    '_act' => $action,
                    '_ts'  => self::utcTime()->format(DATE_ISO8601),
                ] + $data
        );
    }
}
