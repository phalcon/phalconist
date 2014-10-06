<?php


class ExtTask extends \Phalcon\CLI\Task
{

    public function mainAction()
    {
        echo __METHOD__ . PHP_EOL;
    }

    public function updateAction()
    {
        $query = [
            '_source' => [
                'synced',
                'updated',
                'created',
                'urls.html'
            ],
            'filter'  => [
                'bool' => [
                    'must' => [
                        ['range' => ['synced' => ['gte' => 'now-1d']]],
                        ['term' => ['is_composer' => true]],
                    ],
                ]
            ],
            'sort'    => [
                'synced' => ['order' => 'asc']
            ],
            'size'    => 3,
        ];
        $results = \Models\Project::find($query)->getResults();
        foreach ($results as $project) {
            $data = $project->getData();
            try {
                $githubProject = new \Models\GithubProject($data['urls']['html']);
                $project = new \Models\Project($githubProject);
                $project->save();
            } catch(\Exception $e) {
                error_log(__METHOD__ . ' -- ' . $e->getMessage() . ' -- ' . $e->getTraceAsString());
            }
        }
    }

    public function urlListAction()
    {
        $query = [
            'query'   => ['match_all' => []],
            '_source' => ['urls.html'],
            'size' => 100000,
        ];
        $results = \Models\Project::find($query)->getResults();
        foreach ($results as $project) {
            $data = $project->getData();
            echo $data['urls']['html'] . PHP_EOL;
        }
    }

    public function addAction()
    {
        $url = $this->dispatcher->getParam('url', ['trim', 'striptags']);

        try {
            $githubProject = new \Models\GithubProject($url);
            $project = new \Models\Project($githubProject);
            $project->save();
        } catch(\Exception $e) {
            error_log(__METHOD__ . $e->getMessage());
        }
    }
}
