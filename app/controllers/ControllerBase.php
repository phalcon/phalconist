<?php

namespace Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View;
use Phalcon\Tag;

class ControllerBase extends Controller
{

    public function initialize()
    {
        //Tag::setTitleSeparator(' | ');

        $layout = (int)$this->request->get('layout', 'int', View::LEVEL_MAIN_LAYOUT);
        $this->view->setRenderLevel($layout);

        $this->view->setTemplateAfter('main');
    }
}
