<?php

return new \Phalcon\Config([
    'github'   => [
        'client_id'     => 'bc8e766e4415d928f141',
        'client_secret' => '2ca24559e90ff7e6530e512f88430ff72b078ef5',
        'cache_dir'     => '/tmp/github-api-cache',
    ],
    'top' => ['updated' => 'now-15d'],
    'stopTags' => explode(',', 'phalcon,phalconphp,framework,php,1,2,3,4,5,6,7,8,9,0,a,able,about,across,after,all,almost,also,am,among,an,and,any,are,as,at,be,because,been,but,by,can,cannot,could,dear,did,do,does,either,else,ever,every,for,from,get,got,had,has,have,he,her,hers,him,his,how,however,i,if,in,into,is,it,its,just,least,let,like,likely,may,me,might,most,must,my,neither,no,nor,not,of,off,often,on,only,or,other,our,own,please,rather,said,say,says,she,should,since,so,some,than,that,the,their,them,then,there,these,they,this,tis,to,too,twas,us,wants,was,we,were,what,when,where,which,while,who,whom,why,will,with,would,yet,you,your'),
    'elastica' => [
        'host'  => 'localhost',
        'posrt' => 9200,
    ]
]);