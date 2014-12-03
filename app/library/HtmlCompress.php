<?php

namespace Library;

class HtmlCompress
{

    /**
     * @param $event
     * @param $view
     */
    public function afterRender($event, $view)
    {
        $view->setContent(self::clean($view->getContent()));
    }

    private static function clean($content)
    {
        $replace = [
            '/<!--[^\[](.*?)[^\]]-->/s' => '',
            "/<\?php/" => '<?php ',
            "/\n([\S])/" => ' $1',
            "/\r/" => '',
            "/\n/" => '',
            "/\t/" => ' ',
            "/ +/" => ' ',
        ];
        $content = preg_replace(array_keys($replace), array_values($replace), $content);
        return $content;
    }
}