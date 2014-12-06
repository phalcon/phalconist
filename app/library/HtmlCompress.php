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
            "/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:)\/\/.*))/" => '', // js comments
            '/<!--[^\[](.*?)[^\]]-->/s' => '',
            "/<\?php/" => '<?php ',
            "/\r/" => '',
            "/\n/" => ' ',
            '/\>[^\S ]+/s' => '>',
            '/[^\S ]+\</s' => '<',
            "/\n([\S])/" => ' $1',
            '/(\s)+/s' => '\\1',
            "/\t/" => ' ',
        ];
        $content = preg_replace(array_keys($replace), array_values($replace), $content);
        return $content;
    }
}