<?php

namespace Library;

class Url extends \Phalcon\Mvc\Url
{

    /**
     * Generates a URL with GET params
     * <code>
     * $router->add('/{controller}/{action}/{id}')->setName('controller_action_id');
     * echo \Phalcon\Tag::linkTo(array(
     *      'controller_action_id', // name of route or 'for' => 'controller_action_id'
     *      'controller' => 'videos',
     *      'action'     => 'list',
     *      'id'         => 123,
     *      'sort'       => 'date',
     *      'offset'     => 25
     * ), 'Videos');
     * // Result: "/videos/list/123/?sort=date&offset=25"
     * Volt syntax:
     * <code>
     * {{ link_to(['controller_action_id', 'controller' => 'videos', 'action' => 'list','id' => 123,'sort' => 'date','offset' => 25], 'Videos') }}
     * </code>
     * </code>
     * @param null $uri
     * @param null $args
     * @param null $local
     * @return string
     */
    public function get($uri = null, $args = null, $local = null)
    {
        if (is_array($uri)) {
            $used_params = ['for' => 0];
            if (!isset($uri['for'])) {
                $uri['for'] = array_shift($uri);
            }

            /** @var \Phalcon\Mvc\Router $router */
            $router = $this->getDI()->get('router');

            if ($route = $router->getRouteByName($uri['for'])) {
                $used_params += $route->getPaths();
            }

            $args = (array)$args;
            $args += array_diff_key($uri, $used_params);
        }

        return parent::get($uri, $args, $local);
    }
}
