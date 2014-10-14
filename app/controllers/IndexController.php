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
        Tag::prependTitle('Phalcon Framework Resources');

        $exists = $this->view->getCache()->exists(__METHOD__);
        if (!$exists) {
            $tags = Project::tags(50);
            $owners = Project::owners(30);
            $top = Project::top(6);
            $fresh = Project::fresh(6);
            $langs = [];//\Models\Project::langs(25);

            $this->view->tags = $tags;
            $this->view->owners = $owners;
            $this->view->top = $top;
            $this->view->fresh = $fresh;
            $this->view->langs = $langs;
            $this->view->disqus_public_key = $this->di->get('config')->disqus->public_key;
        }

        $this->view->cache(['lifetime' => 60, 'key' => __METHOD__]);
    }

    public function route404Action()
    {
        Tag::prependTitle('404 Not Found');
        $this->response->setStatusCode(404, 'Not Found');
    }

    public function route500Action()
    {
        Tag::prependTitle('500 Not Found');
        $this->response->setStatusCode(500, 'Not Found');
    }

    public function viewAction()
    {
        $id = (int)$this->dispatcher->getParam('id', 'int');
        if (empty($id)) {
            // todo
            $this->response->redirect('');
        }
        $project = Project::findById($id);
        $this->view->project = $project->getData();

        $description = $project->get('name') ?
            $project->get('name') :
            empty($project->get( 'composer')['description']) ?
                $project->get('composer')['description'] :
                null;

        if ($description) {
            $this->view->description = $description;
        }
        Tag::setTitle($project->get('name') . ' / ' . $project->get('owner')['login'] . ' / Phalconist');
    }

    public function searchAction()
    {
        $text = $this->request->get('q', ['striptags', 'trim']);
        $tags = $this->request->get('tag', ['striptags', 'trim']);
        $owner = $this->request->get('owner', ['striptags', 'trim']);
        $type = $this->request->get('type', ['striptags', 'trim']);

        if ($text) {
            $title = '"' . ucfirst($text) . '" / Search / Phalconist';
        } else if ($tags) {
            $title = '"' . ucfirst($tags) . '" / Tag / Phalconist';
        } else if ($owner) {
            $title = '"' . ucfirst($owner) . '" / Owner / Phalconist';
        } else {
            $title = '"' . ucfirst($type) . '" / Repository type / Phalconist';
        }

        $escaper = new \Phalcon\Escaper();
        Tag::prependTitle($escaper->escapeHtmlAttr($title));

        $this->view->q = $text;
        $this->view->tags = $tags;
        $this->view->owner = $owner;
        $this->view->type = $type;
        $this->view->results = Project::search($text, $tags, $owner, $type);
    }

    public function freshAction()
    {
        $exists = $this->view->getCache()->exists(__METHOD__);
        if (!$exists) {
            $fresh = Project::fresh(60);
            $this->view->results = $fresh;
            $this->view->section = 'Fresh';
            $this->view->pick('index/search');
        }

        $this->view->cache(['lifetime' => 60, 'key' => __METHOD__]);
    }

    public function topAction()
    {
        $exists = $this->view->getCache()->exists(__METHOD__);
        if (!$exists) {
            $topList = Project::top(60);
            $this->view->results = $topList;
            $this->view->section = 'Top';
            $this->view->pick('index/search');
        }

        $this->view->cache(['lifetime' => 60, 'key' => __METHOD__]);
    }

    public function ownersAction()
    {
        $owners = Project::owners(999999);
        $this->view->results = $owners;
    }

    public function newAction()
    {
        $this->response->redirect(['action', 'action' => 'news'], false, 301);
    }

    public function newsAction()
    {
        $so = \Models\StackOverflow::fetchActivity(10);
        $this->view->results = $so;
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
                    LogAction::log(
                        LogAction::ACTION_ADD,
                        $this->user->get('id'),
                        ['project_id' => $project->get('id')]
                    );
                } else {
                    // todo
                }
            } catch(\Exception $e) {
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
