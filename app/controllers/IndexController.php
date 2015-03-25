<?php

namespace Controllers;

use Library\Svg;
use Models\GithubProject;
use Models\LogAction;
use Models\Project;
use Models\StackOverflow;
use Phalcon\Tag;

class IndexController extends ControllerBase
{

    public function indexAction()
    {
        Tag::setTitle('Phalcon Framework Resources');

        $exists = !$this->user && $this->view->getCache()->exists(__METHOD__);
        if (!$exists) {
            $tags = Project::tags(50);
            $owners = Project::owners(30);
            $top = Project::top(6);
            $fresh = Project::fresh(6);
            $langs = [];//\Models\Project::langs(25);
            $categories = $this->di->get('config')->categories;

            $this->view->categories = $categories;
            $this->view->tags = $tags;
            $this->view->owners = $owners;
            $this->view->top = $top;
            $this->view->fresh = $fresh;
            $this->view->langs = $langs;
            $this->view->disqus_public_key = $this->di->get('config')->disqus->public_key;
        }

        if (!$this->user) {
            $this->view->cache(['lifetime' => 60, 'key' => __METHOD__]);
        }
    }

    public function route404Action()
    {
        Tag::setTitle('404 Not Found');
        $this->view->setTemplateAfter('main');
        $this->response->setStatusCode(404, 'Not Found');
    }

    public function route500Action()
    {
        Tag::setTitle('500 Not Found');
        $this->view->setTemplateAfter('main');
        $this->response->setStatusCode(500, 'Not Found');
    }

    public function view301Action()
    {
        if ($id = (int)$this->request->get('id')) {
            // Handle old urls
            $project = Project::findById($id);
            return $this->response->redirect(
                [
                    'view/item',
                    'action' => 'view',
                    'owner' => $project->get('owner')['login'],
                    'repo' => $project->get('repo')
                ],
                false,
                301
            );
        }

        $project_id = (int)$this->dispatcher->getParam('id', 'int');
        if (empty($project_id)) {
            return $this->dispatcher->forward(['controller' => 'index', 'action' => 'route404']);
        }

        $project = Project::findById($project_id);

        return $this->response->redirect(
            [
                'view/item',
                'action' => 'view',
                'owner' => $project->get('owner')['login'],
                'repo' => $project->get('repo')
            ],
            false,
            301
        );
    }

    public function viewAction()
    {
        $owner = $this->dispatcher->getParam('owner', ['string', 'striptags']);
        $repoName = $this->dispatcher->getParam('repo', ['string', 'striptags']);
        $repo = "$owner/$repoName";

        $project = Project::getByFullName($repo);
        if (empty($project)) {
            return $this->dispatcher->forward(['controller' => 'index', 'action' => 'route404']);
        }

        $project = $project->getData();
        $this->view->project = $project;
        if (empty($project['name'])) {
            if (empty($project['composer']['description'])) {
                $description = null;
            } else {
                $description = $project['composer']['description'];
            }
        } else {
            $description = $project['name'];
        }

        if ($description) {
            $this->view->description = $description;
        }

        $this->view->urlSvg = $this->url->get([
            'badge',
            'owner' => $project['owner']['login'],
            'repo' => $project['repo'],
            'type' => 'default'
        ], null, false);

        Tag::setTitle($project['name'] . ' / ' . $project['owner']['login']);
    }

    public function viewCategoryAction()
    {
        $category_name = $this->dispatcher->getParam('name');
        $categories = $this->di->get('config')->categories->toArray();
        $names = array_keys($categories);

        if (!in_array($category_name, $names)) {
            return $this->dispatcher->forward(['controller' => 'index', 'action' => 'route404']);
        }

        $title = ucfirst($category_name) . ' / Category / Phalcon';

        $escaper = new \Phalcon\Escaper();
        Tag::prependTitle($escaper->escapeHtmlAttr($title));

        $results = Project::search('', $categories[$category_name]['query']);
        $this->view->category = $category_name;
        $this->view->results = $results;
        $this->view->pick('index/search');
    }

    public function viewOwner301Action()
    {
        $owner = $this->dispatcher->getParam('owner', ['string', 'striptags']);
        return $this->response->redirect(['owner', 'owner' => $owner], false, 301);
    }

    public function viewOwnerAction()
    {
        $owner_name = $this->dispatcher->getParam('owner');
        $title = ucfirst($owner_name) . ' / Owner / Phalcon';

        $escaper = new \Phalcon\Escaper();
        Tag::prependTitle($escaper->escapeHtmlAttr($title));

        $results = Project::search('', '', $owner_name);
        $this->view->owner = $owner_name;
        $this->view->results = $results;
        $this->view->pick('index/search');
    }

    public function searchAction()
    {
        $text = $this->request->get('q', ['striptags', 'trim']);
        $tags = $this->request->get('tag', ['striptags', 'trim']);
        $owner = $this->request->get('owner', ['striptags', 'trim']);
        $type = $this->request->get('type', ['striptags', 'trim']);

        if ($text) {
            $title = ucfirst($text) . ' / Search';
        } else if ($tags) {
            $title = ucfirst($tags) . ' / Tag';
        } else if ($owner) {
            $title = ucfirst($owner) . ' / Owner';
        } else {
            $title = ucfirst($type) . ' / Repository type';
        }

        $title .= ' / Phalcon';

        $escaper = new \Phalcon\Escaper();
        Tag::setTitle($escaper->escapeHtmlAttr($title));

        $this->view->q = $text;
        $this->view->tags = $tags;
        $this->view->owner = $owner;
        $this->view->type = $type;
        $this->view->results = Project::search($text, $tags, $owner, $type);
    }

    public function freshAction()
    {
        $fresh = Project::fresh(60);
        $this->view->results = $fresh;
        $this->view->section = 'Fresh';
        $this->view->pick('index/search');

        Tag::setTitle('Recently created projects for Phalcon framework');
    }

    public function topAction()
    {
        $topList = Project::top(60);
        $this->view->results = $topList;
        $this->view->section = 'Top';
        $this->view->pick('index/search');

        Tag::setTitle('Top projects with Phalcon framework');
    }

    public function ownerAction()
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
        $this->view->results = StackOverflow::fetchActivity(10);
    }

    public function lastAction()
    {
        $this->view->results = Project::last(60);
        $this->view->q = 'Last added';
        $this->view->is_show_date_added = true;
        $this->view->pick('index/search');

        Tag::setTitle('Recently added projects for Phalcon framework');
    }

    public function addAction()
    {
        if (!$this->user) {
            $this->flash->warning('You should be authorized');
            return $this->response->redirect(['controller/action', 'controller' => 'user', 'action' => 'login']);
        }

        if ($this->request->isPost()) {
            $url = $this->request->getPost('url', ['trim', 'striptags']);

            try {
                $githubProject = new GithubProject($url);
                if ($project = new Project($githubProject)) {
                    if (!$project->save()) {
                        throw new \Exception('Something is going wrong. Try again later.');
                    }
                    LogAction::log(
                        LogAction::ACTION_ADD,
                        $this->user->get('id'),
                        ['project_id' => $project->get('id')]
                    );

                    $url = $this->url->get([
                        'view/item',
                        'action' => 'view',
                        'owner' => $project->get('owner')['login'],
                        'repo' => $project->get('repo')
                    ], null, false);

                    $urlSvg = $this->url->get([
                        'badge',
                        'owner' => $project->get('owner')['login'],
                        'repo' => $project->get('repo'),
                        'type' => 'default'
                    ], null, false);

                    $this->flash->success('
                        <h4>Your project was added</h4>
                        <a href="' . $url . '">' . $url . '</a>
                        <hr/>
                        <b>Badge</b>
                        <div><img src="' . $urlSvg . '"> </div>
                        <div><small>' . $urlSvg . '</small></div>
                    ');
                } else {
                    throw new \Exception('Failed to read from GitHub. Try again later.');
                }
            } catch(\Exception $e) {
                $this->flash->warning($e->getMessage());
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

        $project_id = $this->request->getPost('id', ['trim', 'striptags']);
        //Project::deleteById($id);
        LogAction::log(LogAction::ACTION_DELETE, $this->user->get('id'), ['project_id' => $project_id]);

        return $this->response->redirect('');
    }

    public function badgeAction()
    {
        $owner = $this->dispatcher->getParam('owner', ['string', 'striptags']);
        $repoName = $this->dispatcher->getParam('repo', ['string', 'striptags']);
        $type = $this->dispatcher->getParam('type');

        $allow_types = ['default'];
        if (!in_array($type, $allow_types)) {
            return $this->response->setStatusCode(400, 'Bad Request');
        }

        /** @var \Phalcon\Cache\Backend\Memcache $cache */
        $cache = $this->getDI()->get('cache');

        $repo = "$owner/$repoName";
        $key = __METHOD__ . "-$repo";
        if (!$svgContent = $cache->get($key)) {
            if ($projectData = Project::getByFullName($repo)) {
                $data = $projectData->getData();
                $svg = new Svg();
                $svgContent = $svg->generate($data['score']);
            } else {
                $svgContent = '404';
            }
            $cache->save($key, $svgContent, 5 * 60);
        }

        if ($svgContent === '404') {
            return $this->response->setStatusCode(404, 'Not Found');
        }

        return $this->response
            ->setContentType('image/svg+xml', 'utf-8')
            ->setContent($svgContent);
    }
}
