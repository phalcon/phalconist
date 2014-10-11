<!-- Header -->
<header>
    <div class="container" style="padding-bottom: 0px;">
        <div class="row text-left">
            <div class="intro-text text-center" style="margin: 50px;">
                <h1 class="skills">Phalcon Framework Resources</h1>
            </div>

            <div class="col-lg-12 clearfix">
                <div class="col-lg-2">
                    <h2><a href="/top" style="color:#fff" title="The most popular projects">Top</a></h2>
                </div>
                <ul class="list-inline col-lg-10 clearfix">
                    {% for item in top %}
                        <li class="col-xs-12 col-sm-6 col-md-4 col-lg-2" style="overflow: hidden;margin-top: 30px">
                            {% set data = item.getData() %}
                            <span class="badge"><i class="glyphicon glyphicon-star"> </i> {{ data['stars'] }}</span><br>
                            <h5 style="margin-bottom: 0;">
                                {{ link_to(['action', 'action': 'view', 'id': item.getId()], data['name'], 'style': 'color:#fff') }}
                            </h5>
                            {#<small>{{ data['owner']['login'] }}</small>#}
                            {% if data['description'] %}
                                <small>{{ data['description'] }}</small>
                            {% else %}
                                <small>{{ data['composer']['description'] }}</small>
                            {% endif %}
                        </li>
                    {% endfor %}
                </ul>
                <div class="text-right">
                    <a href="/top" style="color:#fff" title="The most popular projects">more</a>
                </div>
            </div>

            <div class="col-lg-12 clearfix">
                <hr class="col-lg-12" />
                <div class="col-lg-2">
                    <h2><a href="/fresh" style="color:#fff" title="Recently created projects">Fresh</a></h2>
                </div>
                <ul class="list-inline col-lg-10">
                    {% for item in fresh %}
                        <li class="col-xs-12 col-sm-6 col-md-4 col-lg-2" style="min-height:10em;margin-bottom:20px;overflow: hidden;margin-top: 30px;">
                            {% set data = item.getData() %}
                            <span class="badge"><i class="glyphicon glyphicon-star"> </i> {{ data['stars'] }}</span><br>
                            <h5 style="margin-bottom: 0;"
                                title="{{ data['name']|escape }}">{{ link_to(['action', 'action': 'view', 'id': item.getId()], data['name'], 'style': 'color:#fff') }}</h5>
                            <span class="label label-date" title="Created"><?= \Models\Project::utcTime($data['created'])->
                                format('d M') ?></span><br>
                            {#<small>{{ data['owner']['login'] }}</small>#}
                            {% if data['description'] %}
                                <small>{{ data['description'] }}</small>
                            {% else %}
                                <small>{{ data['composer']['description'] }}</small>
                            {% endif %}
                        </li>
                    {% endfor %}
                </ul>

                <div class="text-right" style="margin-bottom: 20px">
                    <a href="/fresh" style="color:#fff" title="Recently created projects">more</a>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="container">
    <div class="row">
        <br />

        <div class="col-lg-12">
            <div class="col-lg-6">
                <hr class="col-lg-12" />
                <noindex><h3>Tags</h3></noindex>
                <ul class="list-inline">
                    {% for tag in tags['list'] %}
                        {% set size = 2.2 * tag['count'] / tags['max'] %}
                        {% set size = size < 0.8 ? 0.8 : size %}
                        <li style="font-size: {{ size }}em;">
                            <noindex>
                            {{ link_to(['action', 'action': 'search', 'tag': tag['term']], tag['term'], 'title': tag['term'], 'rel': 'nofollow') }}
                            </noindex>
                        </li>
                    {% endfor %}
                </ul>
            </div>
            <div class="col-lg-6">
                <hr class="col-lg-12" />
                <noindex><h3 style="margin-bottom: 21px">Owners</h3></noindex>
                <ul class="list-inline" style="margin-bottom: 40px">
                    {% for owner in owners['list'] %}
                        {% set size = 2.2 * owner['doc_count'] / owners['max'] %}
                        {% set size = size < 0.8 ? 0.8 : size %}
                        <li style="font-size: {{ size }}em;">
                            {{ link_to(['action', 'action': 'search', 'owner': owner['key']], owner['key'], 'title': owner['key']) }}
                        </li>
                    {% endfor %}
                </ul>
            </div>
        </div>
        <div class="col-lg-4">
        </div>
    </div>

    <div class="raw clearfix" style="margin-bottom: 40px">
        <hr />
        <div class="col-lg-4">
            <div class="a-comments-header"></div>
            <ul id="comment_widget_js" class="media-list"></ul>
        </div>
        <div class="col-lg-4">
            <noindex class="col-lg-12">
            <a class="twitter-timeline"  href="https://twitter.com/search?q=phalconphp" data-widget-id="519842350013489152">phalconphp</a> <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
            </noindex>
        </div>
        <div class="col-lg-4">
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function(){
        window.fetchLastComments('{{ disqus_public_key }}');
    });
</script>