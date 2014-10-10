<?php


class MainTask extends \Phalcon\CLI\Task
{

    public function mainAction()
    {
        echo __METHOD__ . PHP_EOL;
    }

    public function initAction()
    {
        /** @var \Elastica\Client $elastica */
        $elastica = $this->di->get('elastica');

        try {
            $elastica->request('/phalconist', \Elastica\Request::DELETE);
        } catch(\Exception $e) {
            echo __METHOD__ . ' -- ' . $e->getMessage();
        }

        $res = $elastica->request(
            '/phalconist',
            \Elastica\Request::PUT,
            [
                'settings' => [
                    'analysis' => [
                        'analyzer' => [
                            'enru_morphology' => [
                                'type' => 'custom',
                                'char_filter' => ['html_strip'],
                                'tokenizer' => 'standard',
                                'filter' => [
                                    'lowercase',
                                    'russian_morphology',
                                    'english_morphology',
                                    'my_stopwords'
                                ]
                            ],
                            'dashed_term' => [
                                'type' => 'custom',
                                'tokenizer' => 'keyword',
                            ]
                        ],
                        'filter' => [
                            'my_stopwords' => [
                                'type' => 'stop',
                                'stopwords' => 'а,без,более,бы,был,была,были,было,быть,в,вам,вас,весь,во,вот,все,всего,всех,вы,где,да,даже,для,до,его,ее,если,есть,еще,же,за,здесь,и,из,или,им,их,к,как,ко,когда,кто,ли,либо,мне,может,мы,на,надо,наш,не,него,нее,нет,ни,них,но,ну,о,об,однако,он,она,они,оно,от,очень,по,под,при,с,со,так,также,такой,там,те,тем,то,того,тоже,той,только,том,ты,у,уже,хотя,чего,чей,чем,что,чтобы,чье,чья,эта,эти,это,я,a,an,and,are,as,at,be,but,by,for,if,in,into,is,it,no,not,of,on,or,such,that,the,their,then,there,these,they,this,to,was,will,with'
                            ]
                        ]
                    ]
                ]
            ]
        );
        if ($res->getStatus() != 200) {
            echo $res->getError();
            return;
        }

        $res = $elastica->request(
            '/phalconist/project/_mapping',
            \Elastica\Request::PUT,
            [
                'project' => [
                    //'_all' => ['analyzer' => 'english_morphology'],
                    'properties' => [
                        'name' => ['type' => 'string', 'analyzer' => 'enru_morphology'],
                        'description' => ['type' => 'string', 'analyzer' => 'enru_morphology'],
                        'owner' => [
                            'properties' => [
                                'login' => ['type' => 'string', 'analyzer' => 'dashed_term']
                            ],
                        ],
                    ]
                ]
            ]
        );
        if ($res->getStatus() != 200) {
            echo $res->getError();
            return;
        }

        $res = $elastica->request('/phalconist/_refresh', \Elastica\Request::POST);
        if ($res->getStatus() != 200) {
            echo $res->getError();
            return;
        }

        /** @var \Phalcon\CLI\Console $cli */
        $cli = $this->di->get('console');
        $cli->handle(
            [
                'task' => 'ext',
                'action' => 'add',
                'url' => 'https://github.com/phalcon/phalcon-devtools'
            ]
        );

        echo 'DONE.' . PHP_EOL;
    }
}
