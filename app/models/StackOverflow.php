<?php
namespace Models;

use Phalcon\DI\Injectable;

class StackOverflow extends Injectable
{

    public static function fetchActivity($limit)
    {
        $di = \Phalcon\DI::getDefault();

        /** @var \Phalcon\Cache\Backend\File $cache */
        $cache = $di->get('fileCache');
        $key = 'stackoverflow.cache';
        if (!$data = $cache->get($key, 3600)) {
            $stack_url = "compress.zlib://https://api.stackexchange.com/2.2/search/advanced?order=desc&sort=creation&answers=1&tagged=phalcon&site=stackoverflow";
            $result = file_get_contents($stack_url);
            $data = json_decode($result);
            $cache->save($key, $data);
        }

        return array_slice($data->items, 0, $limit);
    }
}