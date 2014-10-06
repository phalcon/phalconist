<?php

namespace Controllers;

use Phalcon\Tag;

class IndexController extends ControllerBase
{

    public function indexAction()
    {
        Tag::prependTitle('Phalconist Main');

        $tags = \Models\Project::tags(50);
        $owners = \Models\Project::owners(30);
        $top = \Models\Project::top(6);
        $newbie = \Models\Project::newbie(12);
        $types = \Models\Project::types(25);
        $langs = [];//\Models\Project::langs(25);

        $this->view->tags = $tags;
        $this->view->owners = $owners;
        $this->view->top = $top;
        $this->view->newbie = $newbie;
        $this->view->types = $types;
        $this->view->langs = $langs;
    }

    public function viewAction()
    {
        $id = (int)$this->request->get('id', 'int');
        if (empty($id)) {
            $this->response->redirect('');
        }
        $project = \Models\Project::findById($id);
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
        $this->view->results = \Models\Project::search($text, $tags, $owner, $type);
    }

    public function newAction()
    {
        $newbie = \Models\Project::newbie(60);
        $this->view->results = $newbie;
        $this->view->section = 'New';
        $this->view->pick('index/search');
    }

    public function topAction()
    {
        $newbie = \Models\Project::top(60);
        $this->view->results = $newbie;
        $this->view->section = 'Top';
        $this->view->pick('index/search');
    }

    public function addAction()
    {
    }

    public function addExtAction()
    {
        $this->view->disable();

        if (!$this->request->isPost()) {
            return $this->response->redirect('');
        }

        $url = $this->request->getPost('url', ['trim', 'striptags']);

        try {
            $githubProject = new \Models\GithubProject($url);
            $project = new \Models\Project($githubProject);
            $project->save();
        } catch(\Exception $e) {
            error_log(__METHOD__ . $e->getMessage());
        }
        $this->response->redirect('');
    }

    public function deleteAction()
    {
        $this->view->disable();

        if (!$this->request->isPost()) {
            return $this->response->redirect('');
        }

        $id = $this->request->getPost('id', ['trim', 'striptags']);
        \Models\Project::deleteById($id);
    }

}

