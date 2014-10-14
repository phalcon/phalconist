<?php

namespace Models;

use Elastica\Exception\NotFoundException;
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
                        'fields' => ['name', 'description', 'composer.keywords', 'composer.description'],
                        'order' => 'count',
                        'exclude' => $di->get('config')->stopTags->toArray(),
                        'size' => $limit,
                    ]
                ]
            ]
        ];
        $resultSet = static::getStorage()->search($query);
        $facets = $resultSet->getFacets();
        return static::toTags($facets['tags']['terms']);
    }

    /**
     * @param int $limit
     * @return mixed
     */
    public static function types($limit = 25)
    {
        $query = [
            'aggs' => [
                'types' => [
                    'terms' => [
                        'field' => 'composer.type',
                        'size' => $limit,
                    ],
                ]
            ]
        ];
        $resultSet = static::getStorage()->search($query);
        return static::toTags($resultSet->getAggregation('types')['buckets'], 'key', 'doc_count');
    }

    /**
     * @param int $limit
     * @return mixed
     */
    public static function langs($limit = 25)
    {
        $query = [
            'aggs' => [
                'langs' => [
                    'terms' => [
                        'field' => 'lang',
                        'size' => $limit,
                    ],
                ]
            ]
        ];
        $resultSet = static::getStorage()->search($query);
        return $resultSet->getAggregation('langs')['buckets'];
    }

    /**
     * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/search-facets-terms-facet.html
     * @param int $limit
     * @return mixed
     */
    public static function owners($limit = 25)
    {
        $query = [
            'aggs' => [
                'owners' => [
                    'terms' => [
                        'field' => 'owner.login',
                        'size' => $limit,
                    ],
                ]
            ]
        ];
        $resultSet = static::getStorage()->search($query);
        $buckets = $resultSet->getAggregation('owners')['buckets'];
        return static::toTags($buckets, 'key', 'doc_count');
    }

    /**
     * @param int $limit
     * @return \Elastica\Result[]
     */
    public static function top($limit = 6)
    {
        $di = \Phalcon\DI::getDefault();
        $config = $di->get('config');
        $query = [
            '_source' => [
                'name',
                'repo',
                'description',
                'stars',
                'watchers',
                'forks',
                'score',
                'is_composer',
                'updated',
                'created',
                'pushed',
                'owner.login',
                'owner.avatar_url',
                'composer.keywords',
                'composer.description',
            ],
            'filter' => [
                'bool' => [
                    'must' => [
                        ['range' => ['pushed' => ['gte' => $config->top->pushed]]],
                        ['term' => ['is_composer' => true]],
                    ],
                    //'should' => [
                    //]
                ]
            ],
            'sort' => [
                'score' => ['order' => 'desc'],
            ],
            'size' => $limit,
        ];
        return static::getStorage()->search($query)->getResults();
    }

    /**
     * @param int $limit
     * @return \Elastica\Result[]
     */
    public static function fresh($limit = 6)
    {
        $query = new \Elastica\Query();
        $query->setSource(
            [
                'name',
                'repo',
                'description',
                'stars',
                'watchers',
                'forks',
                'score',
                'is_composer',
                'updated',
                'created',
                'pushed',
                'owner.login',
                'owner.avatar_url',
                'composer.keywords',
                'composer.description',
            ]
        );
        $query->setSort(['created' => ['order' => 'desc']]);
        $query->setSize($limit);
        $resultSet = static::getStorage()->search($query);
        return $resultSet->getResults();
    }

    public static function search($text = '', $tags = '', $owner = '', $type = '')
    {
        $query = [
            '_source' => [
                'name',
                'repo',
                'description',
                'owner.login',
                'owner.avatar_url',
                'stars',
                'watchers',
                'forks',
                'score',
                'created',
                'updated',
                'pushed',
                'is_composer',
                'composer.version',
                'composer.keywords',
                'composer.type',
            ],
            'from' => 0,
            'size' => 60,
        ];

        $query['query']['bool']['should'] = [];
        if (!empty($text)) {
            $query['query']['bool']['should'] = [
                ['match' => ['name' => $text]],
                ['match' => ['description' => $text]],
                ['match' => ['owner.login' => $text]],
                ['match' => ['composer.keywords' => $text]],
            ];
            $query['sort'] = [
                '_score',
                ['score' => ['order' => 'desc']],
                //['watchers' => ['order' => 'desc']],
                //['stars' => ['order' => 'desc']],
            ];
        }

        if (!empty($tags)) {
            $query['query']['bool']['should'] = [
                ['match' => ['name' => $tags]],
                ['match' => ['description' => $tags]],
                ['match' => ['composer.keywords' => $tags]],
            ];
            $query['sort'] = [
                ['score' => ['order' => 'desc']],
                //['is_composer' => ['order' => 'desc']],
                //['watchers' => ['order' => 'desc']],
                //['stars' => ['order' => 'desc']],
            ];
        }

        if (!empty($owner)) {
            $query['query']['bool']['should'] = [
                ['match' => ['owner.login' => $owner]],
            ];
            $query['sort'] = [
                ['score' => ['order' => 'desc']],
                //['is_composer' => ['order' => 'desc']],
                //['watchers' => ['order' => 'desc']],
                //['stars' => ['order' => 'desc']],
            ];
        }

        if (!empty($type)) {
            $query['query']['bool']['should'] = [
                ['match' => ['composer.type' => $type]],
            ];
        }

        $res = \Models\Project::find($query);
        return $res->getResults();
    }

    private static function toTags($list, $key = 'term', $count = 'count')
    {
        $tag_min = PHP_INT_MAX;
        $tag_max = 0;
        foreach ($list as $item) {
            $tag_min = min($item[$count], $tag_min);
            $tag_max = max($item[$count], $tag_max);
        }
        usort(
            $list,
            function ($a, $b) use ($key) {
                return $a[$key] > $b[$key] ? 1 : -1;
            }
        );

        $result['list'] = $list;
        $result['min'] = $tag_min;
        $result['max'] = $tag_max;
        return $result;
    }

    public static function count()
    {
        $query = [
            'size' => 0,
            'aggs' => [
                'count' => [
                    'value_count' => ['field' => 'id']
                ]
            ]
        ];
        $resultSet = static::getStorage()->search($query);
        return $resultSet->getAggregation('count')['value'];
    }

    /**
     * @link http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/search-aggregations-metrics-cardinality-aggregation.html
     * @return mixed
     */
    public static function countOwners()
    {
        $query = [
            'size' => 0,
            'aggs' => [
                'count' => [
                    'cardinality' => [
                        'field' => 'owner.id',
                        'precision_threshold' => 100,
                    ]
                ]
            ]
        ];
        $resultSet = static::getStorage()->search($query);
        return $resultSet->getAggregation('count')['value'];
    }

    /**
     * @param int $limit
     * @return \Elastica\Result[]
     */
    public static function lastAdded($limit = 5)
    {
        $query = [
            '_source' => [
                'name',
                'repo',
                'score',
                'added',
                'owner.login',
            ],
            'filter' => [
                'bool' => [
                    'must' => [
                        ['range' => ['added' => ['gte' => 0]]],
                    ],
                ]
            ],
            'from' => 0,
            'size' => $limit,
            'sort' => [
                'added' => ['order' => 'desc'],
            ],
        ];
        $resultSet = static::getStorage()->search($query);
        return $resultSet->getResults();
    }

    public function __construct(\Models\GithubProject $githubProject)
    {
        $this->githubProject = $githubProject;

        try {
            $repository = $this->githubProject->fetchRepository();
        } catch(\Exception $e) {
            return null;
        }

        $readme = $this->githubProject->fetchReadme();
        $readme_html = empty($readme) ? '' : $this->githubProject->markdown($readme);
        $composer = $this->githubProject->fetchComposer();
        $is_composer = (bool)count($composer);
        $travis = $this->githubProject->fetchTravis();
        $is_travis = !empty($travis);

        if ($is_composer && $package = $this->githubProject->getPackage()) {
            /** @var \Packagist\Api\Result\Package\Downloads $downloads */
            $downloads = $package->getDownloads();
            $downloads = [
                'total' => $downloads->getTotal(),
                'monthly' => $downloads->getMonthly(),
                'daily' => $downloads->getDaily()
            ];
        } else {
            $downloads = ['total' => 0, 'monthly' => 0, 'daily' => 0];
        }

        // PhalconSkeleton => Phalcon Skeleton
        $name = preg_replace('/([a-z])([A-Z])/', '$1_$2', $repository['name']);

        // Phalcon-Skeleton => Phalcon Skeleton
        $name = str_replace(['-', '_'], ' ', $name);

        $score = $this->calcScore(
            $repository['pushed_at'],
            $repository['stargazers_count'],
            $repository['subscribers_count'],
            $readme,
            $repository['description'],
            $is_travis,
            $is_composer,
            !empty($package)
        );

        $this->data = [
            'id' => $repository['id'],
            'repo' => $this->githubProject->getRepoName(),
            'name' => $name,
            'full_name' => $repository['full_name'],
            'description' => $repository['description'],
            'score' => $score,
            'stars' => $repository['stargazers_count'],
            'watchers' => $repository['subscribers_count'],
            'forks' => $repository['forks_count'],
            'lang' => $repository['language'],
            'homepage' => $repository['homepage'],
            'urls' => [
                'html' => $repository['html_url'],
                'git' => $repository['git_url'],
                'ssh' => $repository['ssh_url'],
                'clone' => $repository['clone_url'],
            ],
            'owner' => [
                'id' => $repository['owner']['id'],
                'login' => $repository['owner']['login'],
                'avatar_url' => $repository['owner']['avatar_url'],
                'type' => $repository['owner']['type'],
            ],
            'created' => $repository['created_at'],
            'updated' => $repository['updated_at'],
            'pushed' => $repository['pushed_at'],
            'readme' => $readme_html,
            'is_composer' => $is_composer,
            'is_travis' => $is_travis,
            'downloads' => $downloads,
            'composer' => [
                'type' => empty($composer['type']) ? '' : $composer['type'],
                'name' => empty($composer['name']) ? '' : $composer['name'],
                'description' => empty($composer['description']) ? '' : $composer['description'],
                'keywords' => empty($composer['keywords']) ? [] : $composer['keywords'],
                'license' => empty($composer['license']) ? '' : $composer['license'],
                'authors' => empty($composer['authors']) ? [] : $composer['authors'],
                'version' => empty($composer['version']) ? '' : $composer['version'],
                'require' => empty($composer['require']) ? [] : $composer['require'],
            ],
        ];
    }

    /**
     * @return \Elastica\Response
     */
    public function save()
    {
        try {
            static::findById($this->data['id']);
        } catch(NotFoundException $e) {
            $this->data['added'] = static::utcTime()->format(DATE_ISO8601);
        }

        return static::add($this->data);
    }

    public function get($attr)
    {
        return $this->data[$attr];
    }

    private function calcScore($pushed, $stars, $watchers, $readme, $description, $is_travis, $is_composer, $is_package)
    {
        $now = self::utcTime();
        $pushed = self::utcTime($pushed);
        $diff = $now->diff($pushed);

        $score = 0;
        $score += 1 * $stars;
        $score += 2 * $watchers;
        $score += 5 * (int)($diff->days < 30);
        $score += 5 * (int)(strlen($description) > 10);
        $score += 5 * (int)$is_travis;
        $score += 5 * (int)$is_composer;
        $score += 5 * (int)$is_package;
        $score += 5 * (int)(str_word_count($readme) > 29);

        return $score;
    }
}
