<?php

namespace Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View;
use Phalcon\Tag;

class ControllerBase extends Controller
{

    /** @var  \Elastica\Document */
    public $user;


    public function initialize()
    {
        //Tag::setTitleSeparator(' | ');

        if ($this->session->has('identity') && $this->session->get('identity')['id']) {
            $this->user = \Models\User::findById($this->session->get('identity')['id']);
        } else {
            $this->view->login_url = $this->di->get('authProvider')->makeAuthUrl();
        }

        $layout = (int)$this->request->get('layout', 'int', View::LEVEL_MAIN_LAYOUT);
        $this->view->setRenderLevel($layout);

        $this->view->setTemplateAfter('main');

        $this->view->project_count = \Models\Project::count();
        $this->view->owner_count = \Models\Project::countOwners();
        $this->view->currentUser = $this->user;
    }
}
