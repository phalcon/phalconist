<?php

use Models\GithubProject;
use Models\Project;

class ExtTask extends \Phalcon\CLI\Task
{

    public function mainAction()
    {
        echo __METHOD__ . PHP_EOL;
    }

    public function updateAction()
    {
        $limit = min(3, (int)$this->dispatcher->getParam('limit'));
        $query = [
            '_source' => [
                'id',
                'synced',
                'updated',
                'created',
                'urls.html'
            ],
            'filter' => [
                'bool' => [
                    'must' => [
                        ['range' => ['synced' => ['lte' => 'now-1d']]],
                    ],
                ]
            ],
            'sort' => [
                'synced' => ['order' => 'asc']
            ],
            'size' => $limit,
        ];
        $done = [];
        $results = Project::find($query)->getResults();
        foreach ($results as $project) {
            $data = $project->getData();
            try {
                $githubProject = new GithubProject($data['urls']['html']);
                $project = new Project($githubProject);
                $project->save();
                $done[] = $githubProject->getUserName() . '/' . $githubProject->getRepoName();
            } catch(\Exception $e) {
                if ($e->getCode() === 404) {
                    Project::deleteById($data['id']);
                    error_log(__METHOD__ . ' -- ' . $e->getMessage() . ' -- Deleted: ' . $data['urls']['html']);
                    return;
                }
                error_log(__METHOD__ . ' -- ' . $e->getMessage() . ' -- ' . $e->getTraceAsString());
            }
        }

        /** @var \Phalcon\Logger\Adapter\File $cronLogger */
        $cronLogger = $this->di->get('cronLogger');
        $cronLogger->info(__METHOD__ . ': ' . join(', ', $done));
    }

    public function urlListAction()
    {
        $query = [
            'query' => ['match_all' => []],
            '_source' => ['urls.html'],
            'size' => 100000,
        ];
        $results = Project::find($query)->getResults();
        foreach ($results as $project) {
            $data = $project->getData();
            echo $data['urls']['html'] . PHP_EOL;
        }
    }

    public function addAction()
    {
        $url = $this->dispatcher->getParam('url', ['trim', 'striptags']);

        try {
            $githubProject = new GithubProject($url);
            $project = new Project($githubProject);
            $project->save();
        } catch(\Exception $e) {
            error_log(__METHOD__ . ' -- ' . $e->getMessage());
        }
    }

    public function positionsAction()
    {
        $query = [
            'sort' => [
                'score' => ['order' => 'desc']
            ]
        ];

        $results = Project::find($query)->getResults();
        $pos = 0;
        foreach ($results as $project) {
            $data = $project->getData();
            try {
                $data['position'] = $pos + 1;
                Project::update($data);
                $pos++;
            } catch(\Exception $e) {
                if ($e->getCode() === 404) {
                    Project::deleteById($data['id']);
                    error_log(__METHOD__ . ' -- ' . $e->getMessage() . ' -- Deleted: ' . $data['urls']['html']);
                    return;
                }
                error_log(__METHOD__ . ' -- ' . $e->getMessage() . ' -- ' . $e->getTraceAsString());
            }
        }

        /** @var \Phalcon\Logger\Adapter\File $cronLogger */
        $cronLogger = $this->di->get('cronLogger');
        $cronLogger->info(__METHOD__ . ' -- Done (' . $pos . ').');
    }
}
