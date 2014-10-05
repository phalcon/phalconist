<?php

namespace Models;

use Phalcon\DI\Injectable;

class Project extends Injectable
{

    use ElasticModelTrait;

    protected static $index = 'phalconist';

    protected static $type = 'project';

    public $data;

    protected $githubProject;


    /**
     * @param array $query
     * @param array $options
     * @return \Elastica\ResultSet
     */
    public static function find(array $query = [], array $options = null)
    {
        return static::getStorage()->search($query, $options);
    }

    /**
     * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/search-facets-terms-facet.html
     * @param int $limit
     * @return mixed
     */
    public static function tags($limit = 25)
    {
        $di = \Phalcon\DI::getDefault();
        $query = [
            'facets' => [
                'tags' => [
                    'terms' => [
                        'fields'  => ['name', 'description', 'composer.keywords', 'composer.description'],
                        'order'   => 'count',
                        'exclude' => $di->get('config')->stopTags->toArray(),
                        'size'    => $limit,
                    ]
                ]
            ]
        ];
        $resultSet = static::getStorage()->search($query);
        $facets = $resultSet->getFacets();
        return static::toTags($facets['tags']['terms']);
    }

    /**
     * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/search-facets-terms-facet.html
     * @param int $limit
     * @return mixed
     */
    public static function owners($limit = 25)
    {
        $query = [
            'facets' => [
                'owners' => [
                    'terms' => [
                        'fields'  => ['owner.login'],
                        'order'   => 'count',
                        'size'    => $limit,
                    ]
                ]
            ]
        ];
        $resultSet = static::getStorage()->search($query);
        $facets = $resultSet->getFacets();
        return static::toTags($facets['owners']['terms']);
    }

    /**
     * @param int $limit
     * @return \Elastica\Result[]
     */
    public static function top($limit = 6)
    {
        $di = \Phalcon\DI::getDefault();
        $query = [
            '_source' => [
                'name',
                'description',
                'stars',
                'watchers',
                'forks',
                'is_composer',
                'updated',
                'created',
                'owner.login',
                'owner.avatar_url'
            ],
            'filter'  => [
                'bool' => [
                    'must'   => [
                        ['range' => ['updated' => ['gte' => $di->get('config')->top->updated]]],
                        ['term' => ['is_composer' => true]],
                    ],
                    'should' => [
                    ]
                ]
            ],
            'sort'    => [
                'watchers' => ['order' => 'desc'],
                'stars'    => ['order' => 'desc'],
                'forks'    => ['order' => 'desc'],
                'updated'  => ['order' => 'desc']
            ],
            'size'    => $limit,
        ];
        return static::getStorage()->search($query)->getResults();
    }

    /**
     * @param int $limit
     * @return \Elastica\Result[]
     */
    public static function newbie($limit = 6)
    {
        $query = new \Elastica\Query();
        $query->setSource([
            'name',
            'description',
            'stars',
            'watchers',
            'forks',
            'is_composer',
            'updated',
            'created',
            'owner.login',
            'owner.avatar_url'
        ]);
        $query->setSort(['created' => ['order' => 'desc']]);
        $query->setSize($limit);
        $resultSet = static::getStorage()->search($query);
        return $resultSet->getResults();
    }

    public function __construct(\Models\GithubProject $githubProject)
    {
        $this->githubProject = $githubProject;

        $repository = $this->githubProject->fetchRepository();
        $readme = $this->githubProject->fetchReadme();
        $composer = $this->githubProject->fetchComposer();
        $is_composer = (bool)count($composer);

        if ($is_composer && $package = $this->githubProject->getPackage()) {
            /** @var \Packagist\Api\Result\Package\Downloads $downloads */
            $downloads = $package->getDownloads();
            $downloads = ['total' => $downloads->getTotal(), 'monthly' => $downloads->getMonthly(), 'daily' => $downloads->getDaily()];
        } else {
            $downloads = ['total' => 0, 'monthly' => 0, 'daily' => 0];
        }

        $this->data = [
            'id'          => $repository['id'],
            'repo'        => $this->githubProject->getRepoName(),
            'name'        => str_replace(['-', '_'], ' ', $repository['name']),
            'full_name'   => $repository['full_name'],
            'description' => $repository['description'],
            'stars'       => $repository['stargazers_count'],
            'watchers'    => $repository['subscribers_count'],
            'forks'       => $repository['forks_count'],
            'lang'        => $repository['language'],
            'homepage'    => $repository['homepage'],
            'urls'        => [
                'html'  => $repository['html_url'],
                'git'   => $repository['git_url'],
                'ssh'   => $repository['ssh_url'],
                'clone' => $repository['clone_url'],
            ],
            'owner'       => [
                'id'         => $repository['owner']['id'],
                'login'      => $repository['owner']['login'],
                'avatar_url' => $repository['owner']['avatar_url'],
                'type'       => $repository['owner']['type'],
            ],
            'created'     => $repository['created_at'],
            'updated'     => $repository['updated_at'],
            'pushed'      => $repository['pushed_at'],
            'readme'      => $readme['html'],
            'is_composer' => $is_composer,
            'downloads'   => $downloads,
            'composer'    => [
                'name'        => empty($composer['name']) ? '' : $composer['name'],
                'description' => empty($composer['description']) ? '' : $composer['description'],
                'keywords'    => empty($composer['keywords']) ? [] : $composer['keywords'],
                'license'     => empty($composer['license']) ? '' : $composer['license'],
                'authors'     => empty($composer['authors']) ? [] : $composer['authors'],
                'version'     => empty($composer['version']) ? '' : $composer['version'],
                'require'     => empty($composer['require']) ? [] : $composer['require'],
            ],
        ];
    }

    public static function search($text = '', $tags = '', $owner = '')
    {
        $query = [
            '_source' => [
                'name',
                'description',
                'owner.login',
                'stars',
                'watchers',
                'forks',
                'updated',
                'is_composer',
                'composer.version',
                'composer.keywords'
            ],
            'sort'    => [
                '_score',
                ['is_composer' => ['order' => 'desc']],
                ['watchers' => ['order' => 'desc']],
                ['stars' => ['order' => 'desc']],
            ],
            'from'    => 0,
            'size'    => 100,
        ];

        $query['query']['bool']['should'] = [];
        if (!empty($text)) {
            $query['query']['bool']['should'] = [
                ['match' => ['name' => $text]],
                ['match' => ['description' => $text]],
                ['match' => ['owner.login' => $text]],
                ['match' => ['composer.keywords' => $text]],
            ];
        }

        if (!empty($tags)) {
            $query['query']['bool']['should'] = [
                ['match' => ['name' => $tags]],
                ['match' => ['description' => $tags]],
                ['match' => ['composer.keywords' => $tags]],
            ];
        }

        if (!empty($owner)) {
            $query['query']['bool']['should'] = [
                ['match' => ['owner.login' => $owner]],
            ];
        }

        $res = \Models\Project::find($query);
        return $res->getResults();
    }

    private static function toTags($list)
    {
        $tag_min = PHP_INT_MAX;
        $tag_max = 0;
        foreach($list as $tag) {
            $tag_min = min($tag['count'], $tag_min);
            $tag_max = max($tag['count'], $tag_max);
        }
        usort($list, function($a, $b){
            return $a['term'] > $b['term'] ? 1 : -1;
        });

        $result['list'] = $list;
        $result['min'] = $tag_min;
        $result['max'] = $tag_max;
        return $result;
    }

    /**
     * @return \Elastica\Response
     */
    public function save()
    {
        return static::add($this->data);
    }
}
