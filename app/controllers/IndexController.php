<?php

namespace Controllers;

use Models\GithubProject;
use Models\LogAction;
use Models\Project;
use Phalcon\Tag;

class IndexController extends ControllerBase
{

    public function indexAction()
    {
        Tag::prependTitle('Phalconist Main');

        $tags = Project::tags(50);
        $owners = Project::owners(30);
        $top = Project::top(6);
        $newbie = Project::newbie(6);
        $langs = [];//\Models\Project::langs(25);

        $this->view->tags = $tags;
        $this->view->owners = $owners;
        $this->view->top = $top;
        $this->view->newbie = $newbie;
        $this->view->langs = $langs;
    }

    public function route404Action()
    {
    }

    public function viewAction()
    {
        $id = (int)$this->request->get('id', 'int');
        if (empty($id)) {
            // todo
            $this->response->redirect('');
        }
        $project = Project::findById($id);
        $this->view->project = $project->getData();
    }

    public function searchAction()
    {
        $text = $this->request->get('q', ['striptags', 'trim']);
        $tags = $this->request->get('tag', ['striptags', 'trim']);
        $owner = $this->request->get('owner', ['striptags', 'trim']);
        $type = $this->request->get('type', ['striptags', 'trim']);

        $escaper = new \Phalcon\Escaper();
        Tag::prependTitle('Search result for ' . $escaper->escapeHtmlAttr($text));

        $this->view->q = $text;
        $this->view->tags = $tags;
        $this->view->owner = $owner;
        $this->view->type = $type;
        $this->view->results = Project::search($text, $tags, $owner, $type);
    }

    public function newAction()
    {
        $newbie = Project::newbie(60);
        $this->view->results = $newbie;
        $this->view->section = 'New';
        $this->view->pick('index/search');
    }

    public function topAction()
    {
        $newbie = Project::top(60);
        $this->view->results = $newbie;
        $this->view->section = 'Top';
        $this->view->pick('index/search');
    }

    public function addAction()
    {
        if (!$this->user) {
            // todo
            return $this->response->redirect('login');
        }

        if ($this->request->isPost()) {
            $url = $this->request->getPost('url', ['trim', 'striptags']);

            try {
                $githubProject = new GithubProject($url);
                if ($project = new Project($githubProject)) {
                    $project->save();
                    LogAction::log(LogAction::ACTION_ADD, $this->user->get('id'), ['project_id' => $project->get('id')]);
                } else {
                    // todo
                }
            } catch (\Exception $e) {
                error_log(__METHOD__ . ' -- ' . $e->getMessage() . " [$url]");
            }

            return $this->response->redirect('');
        }
    }

    public function deleteAction()
    {
        $this->view->disable();

        if (!$this->request->isPost()) {
            // todo
            return $this->response->redirect('');
        }

        if (!$this->user) {
            // todo
            return $this->response->redirect('');
        }

        $id = $this->request->getPost('id', ['trim', 'striptags']);
        //Project::deleteById($id);
        LogAction::log(LogAction::ACTION_DELETE, $this->user->get('id'), ['project_id' => $id]);

        return $this->response->redirect('');
    }

    public function loginAction()
    {
        $this->view->login_url = $this->di->get('authProvider')->makeAuthUrl();
    }
}
