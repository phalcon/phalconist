<!DOCTYPE html>
<html lang="en">
<head>
    {{ get_title() }}
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ description is defined ? description|escape : '' }}">
    <link rel="canonical" href="http://phalconist.com"/>
    <link href="/dist/project.css" rel="stylesheet">
    <link href="http://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css">
    <link rel="shortcut icon" href="/img/favicon.ico" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body id="page-top" class="index">
    <script src="/dist/min.js"></script>
    {{ content() }}

    <div class="scroll-top page-scroll visible-xs visble-sm">
        <a class="btn btn-primary" href="#page-top">
            <i class="fa fa-chevron-up"></i>
        </a>
    </div>

    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
      ga('create', 'UA-55599252-1', 'auto');
      ga('require', 'linkid', 'linkid.js');
      ga('require', 'displayfeatures');
      ga('send', 'pageview');
    </script>

    {# See link: https://developers.google.com/webmasters/richsnippets/sitelinkssearch #}
    <script type="application/ld+json">
    {
       "@context": "http://schema.org",
       "@type": "WebSite",
       "url": "http://phalconist.com/",
       "potentialAction": {
         "@type": "SearchAction",
         "target": "http://phalconist.com/search?q={search_term_string}",
         "query-input": "required name=search_term_string"
       }
    }
    </script>
    <script type="text/javascript">(window.Image ? (new Image()) : document.createElement('img')).src = location.protocol + '//vk.com/rtrg?r=F4w6U*6XlAlyNkldsetLNoFcG*cVs1mkG7zelkcWSJPONxSK7LxJpEbm/KgvYqJbr2n4h3tSLfW38AQOwBwRLEfmjp6Lyft4BihqWP6Dk*htZ2GxjX5iHOBZRUnGrgJVdE2Yr4vuOw/7SECT4zFZFSaNgatD1NMHVD1oHMJvW3U-';</script>
</body>
</html>
