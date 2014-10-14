<?php

namespace Controllers;

use Elastica\Exception\NotFoundException;
use Models\Project;
use Models\User;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View;
use Phalcon\Tag;

class ControllerBase extends Controller
{

    /** @var  \Elastica\Document */
    public $user;

    /** @var string */
    public $description = '';


    public function initialize()
    {
        if ($this->session->has('identity')) {
            $identity = $this->session->get('identity');
            try {
                $user = User::findById($identity['id']);
                $this->user = $user;
            } catch(NotFoundException $e){
            }
        }

        if (!$this->user) {
            $this->view->login_url = $this->di->get('authProvider')->makeAuthUrl();
        }

        $layout = (int)$this->request->get('layout', 'int', View::LEVEL_MAIN_LAYOUT);
        $this->view->setRenderLevel($layout);

        $this->view->setTemplateAfter('main');

        $this->view->description = 'Phalcon framework resources'; // todo
        $this->view->project_count = Project::count();
        $this->view->owner_count = Project::countOwners();
        $this->view->currentUser = $this->user;
        $this->view->last_added = Project::lastAdded();
    }
}
