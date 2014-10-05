<!-- Header -->
<header>
    <div class="container" style="padding-bottom: 0px;">
        <div class="row text-left">
            <div class="intro-text" style="margin: 50px;">
                <span class="skills"></span>
            </div>

            <div class="col-lg-12 clearfix">
                <ul class="list-inline col-lg-12 clearfix">
                    <li class="col-lg-2"><h2><a href="/top" style="color:#fff">Top</a></h2></li>
                    {% for item in top %}
                        <li class="col-lg-2" style="overflow: hidden">
                            {% set data = item.getData() %}
                            <span class="badge"><i class="glyphicon glyphicon-star"> </i> {{ data['stars'] }}</span><br>
                            <h5 style="margin-bottom: 0;">{{ loop.index }}
                                . {{ link_to(['action', 'action': 'view', 'id': item.getId()], data['name'], 'style': 'color:#fff') }}</h5>
                            {#<small>{{ data['owner']['login'] }}</small>#}
                            <small>{{ data['description'] }}</small>
                        </li>
                    {% endfor %}
                </ul>
            </div>

            <hr class="col-lg-12"/>

            <div class="col-lg-12 clearfix">
                <ul class="list-inline">
                    <li class="col-lg-2" style="min-height: 25em;"><h2><a href="/new" style="color:#fff">New</a></h2>
                    </li>
                    {% for item in newbie %}
                        <li class="col-lg-2" style="min-height:10em;margin-bottom:20px;overflow: hidden">
                            {% set data = item.getData() %}
                            <span class="badge"><i class="glyphicon glyphicon-star"> </i> {{ data['stars'] }}</span><br>
                            <h5 style="margin-bottom: 0;"
                                title="{{ data['name']|escape }}">{{ link_to(['action', 'action': 'view', 'id': item.getId()], data['name'], 'style': 'color:#fff') }}</h5>
                            <span class="label label-date">Created: <?= \Models\Project::utcTime($data['created'])->
                                format('d M') ?></span><br>
                            {#<small>{{ data['owner']['login'] }}</small>#}
                            <small>{{ data['description'] }}</small>
                        </li>
                    {% endfor %}
                </ul>
            </div>
        </div>
    </div>
</header>

<div class="container">
    <div class="row">
        <br/>
        <div class="col-lg-12">
            <ul class="list-inline">
                <li class="col-lg-2" style="min-height: 10em;"><h3>Tags</h3></li>
                {% for tag in tags['list'] %}
                    {% set size = 2.2 * tag['count'] / tags['max'] %}
                    {% set size = size < 0.8 ? 0.8 : size %}
                    <li style="font-size: {{ size }}em;">
                        {{ link_to(['action', 'action': 'search', 'tag': tag['term']], tag['term']) }}
                    </li>
                {% endfor %}
            </ul>
        </div>

        <hr class="col-lg-12"/>

        <div class="col-lg-12">
            <ul class="list-inline">
                <li class="col-lg-2" style="min-height: 10em;"><h3>Owners</h3></li>
                {% for owner in owners['list'] %}
                    {% set size = 2.2 * owner['count'] / owners['max'] %}
                    {% set size = size < 0.8 ? 0.8 : size %}
                    <li style="font-size: {{ size }}em;">
                        {{ link_to(['action', 'action': 'search', 'owner': owner['term']], owner['term']) }}
                    </li>
                {% endfor %}
            </ul>
        </div>
    </div>
</div>

