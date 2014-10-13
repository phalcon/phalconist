<?php

return new \Phalcon\Config(
    [
        'github'     => [
            'client_id'     => '',
            'client_secret' => '',
            'cache_dir'     => APP_PATH . '/../cache/github-api-cache',
            'redirect_url'  => 'http://dev.phalconist.com/oauth/github',
        ],
        'disqus' => [
            'public_key' => '',
            'secret_key' => '',
        ],
        'top'        => ['updated' => 'now-15d', 'pushed' => 'now-15d'],
        'syncing'    => ['range' => 'now-1h'],
        'stopTags'   => explode(
            ',',
            'phalcon,phalconphp,framework,php,1,2,3,4,5,6,7,8,9,0,a,able,about,across,after,all,almost,also,am,among,an,and,any,are,as,at,be,because,been,but,by,can,cannot,could,dear,did,do,does,either,else,ever,every,for,from,get,got,had,has,have,he,her,hers,him,his,how,however,i,if,in,into,is,it,its,just,least,let,like,likely,may,me,might,most,must,my,neither,no,nor,not,of,off,often,on,only,or,other,our,own,please,rather,said,say,says,she,should,since,so,some,than,that,the,their,them,then,there,these,they,this,tis,to,too,twas,us,wants,was,we,were,what,when,where,which,while,who,whom,why,will,with,would,yet,you,your,а,без,более,бы,был,была,были,было,быть,в,вам,вас,весь,во,вот,все,всего,всех,вы,где,да,даже,для,до,его,ее,если,есть,еще,же,за,здесь,и,из,или,им,их,к,как,ко,когда,кто,ли,либо,мне,может,мы,на,надо,наш,не,него,нее,нет,ни,них,но,ну,о,об,однако,он,она,они,оно,от,очень,по,под,при,с,со,так,также,такой,там,те,тем,то,того,тоже,той,только,том,ты,у,уже,хотя,чего,чей,чем,что,чтобы,чье,чья,эта,эти,это,я'
        ),
        'elastica'   => [
            'host' => 'localhost',
            'port' => 9200,
        ],
        'cronLogger' => [
            'path' => APP_PATH . '/../logs/cron',
        ],
    ]
);