<?php

namespace Models;

use Phalcon\DI\Injectable;

class GithubProject extends Injectable
{

    /** @var \Github\Client */
    protected $client;

    /** @var  \Github\Api\Repo */
    protected $repo;

    protected $user_name;

    protected $repo_name;

    protected static $patterns = [
        'https?://([^/]+)/([^/]+)/([^/]+)/?',
        'https?://[^/]+@([^/]+)/([^/]+)/([^/]+)/?',
        'git@(.+):([^/]+)/([^/]+)/?',
        'git://([^/]+)/([^/]+)/([^/]+)/?',
        'ssh://git@([^/]+)/([^/]+)/([^/]+)/?',
        'https?://([^/]+)/([^/]+)/([^/]+)\\.git',
        'https?://[^/]+@([^/]+)/([^/]+)/([^/]+)\\.git',
        'git@(.+):([^/]+)/([^/]+)\\.git',
        'git://([^/]+)/([^/]+)/([^/]+)\\.git',
        'ssh://git@([^/]+)/([^/]+)/([^/]+)\\.git',
    ];


    public function __construct($url)
    {
        $matches = null;
        foreach (static::$patterns as $pattern) {
            if (preg_match("#$pattern#i", $url, $matches)) {
                break;
            }
        }
        if (!$matches) {
            throw new \InvalidArgumentException('Invalid argument');
        }

        $this->user_name = $matches[2];
        $this->repo_name = $matches[3];
        $this->client = $this->di->get('github');
        $this->repo = $this->client->api('repo');
    }

    public function fetchRepository()
    {
        return $this->repo->show($this->user_name, $this->repo_name);
    }

    public function fetchReadme()
    {
        try {
            $readme_data = $this->repo->readme($this->user_name, $this->repo_name);
            if (empty($readme_data)) {
                return null;
            }

            if ($readme_data['type'] !== 'file' || $readme_data['encoding'] !== 'base64') {
                return null;
            }

            $content = base64_decode($readme_data['content']);
            if (empty($content)) {
                return null;
            }

            $html = $this->client->markdown()->render($content);
            return $html;
        } catch (\Exception $e) {
            error_log(__METHOD__ . ' -- ' . $e->getMessage());
            return '';
        }
    }

    public function fetchComposer()
    {
        try {
            $composer_data = $this->repo->contents()
                ->show($this->user_name, $this->repo_name, 'composer.json', 'master');
            if (empty($composer_data)) {
                return null;
            }

            if ($composer_data['type'] !== 'file' || $composer_data['encoding'] !== 'base64') {
                return null;
            }

            return json_decode(base64_decode($composer_data['content']), true);
        } catch (\Exception $e) {
            error_log(__METHOD__ . ' -- ' . $e->getMessage());
            return [];
        }
    }

    /**
     * @return \Packagist\Api\Result\Package
     */
    public function getPackage()
    {
        try {
            $packagist = $this->di->get('packagist');
            return $packagist->get($this->user_name . '/' . $this->repo_name);
        } catch (\Exception $e) {
            error_log(__METHOD__ . ' -- ' . $e->getMessage());
            return null;
        }
    }

    /**
     * @return mixed
     */
    public function getRepoName()
    {
        return $this->repo_name;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->user_name;
    }
}
