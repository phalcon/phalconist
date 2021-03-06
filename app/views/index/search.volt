<!-- Contact Section -->
<section id="addExt" style="padding-top: 30px">
    <div class="container">
        <div class="row">
            <h3>
                {% if q is defined and q %}
                <div class="col-lg-12"><small>search</small></div>
                <div class="col-lg-12">{{ q }}</div>
                {% endif %}
                {% if tags is defined AND tags %}
                <div class="col-lg-12"><small>tag</small></div>
                <div class="col-lg-12">{{ tags }}</div>
                {% endif %}
                {% if owner is defined and owner %}
                <div class="col-lg-12"><small>owner</small></div>
                <div class="col-lg-12">{{ owner }}</div>
                {% endif %}
                {% if section is defined and section %}
                <div class="col-lg-12"><small>rating</small></div>
                <div class="col-lg-12">{{ section }}</div>
                {% endif %}
                {% if category is defined and category %}
                <div class="col-lg-12"><small>category</small></div>
                <div class="col-lg-12">{{ category }}</div>
                {% endif %}
            </h3>
            <br/>
        </div>
        <div class="row col-lg-10 col-lg-offset-1">
                <ul class="list-inline clearfix">
                    {% for item in results %}
                        {% set data = item.getData() %}
                        <li class="col-xs-12 col-sm-6 col-md-4 col-lg-4 item-inline">
                            <h4 style="margin-bottom: 0;">
                                <small>
                                    {{ link_to(['owner', 'owner': data['owner']['login']], data['owner']['login'], 'style': 'color: #b4bcc2') }}
                                </small>
                                <br>
                                {{ link_to(['view/item', 'action': 'view', 'owner': data['owner']['login'], 'repo': data['repo']], data['name']) }}
                            </h4>
                            <ul class="list-inline" style="margin: 0 0 8px 0;">
                                <li class="label label-default" title="Position">
                                    <i class="glyphicon glyphicon-stats"> </i> {{ (data['position'] is defined) ? data['position'] : 'n/a' }}
                                </li>
                                {% if data['watchers'] %}
                                <li class="label label-default" title="Number of watchers">
                                    <i class="glyphicon glyphicon-eye-open"></i> {{ data['watchers'] }}
                                </li>
                                {% endif %}
                                <li class="label label-default" title="Number of stars">
                                    <i class="glyphicon glyphicon-star"></i> {{ data['stars'] }}
                                </li>
                                {% if data['composer']['version'] is defined AND data['composer']['version'] %}
                                    <li class="label label-default" title="Version">
                                        v.{{ data['composer']['version'] }}
                                    </li>
                                {% endif %}
                                {% if data['is_composer'] %}
                                    <li class="label label-default" title="Composer support">
                                        <i class="glyphicon glyphicon-music"></i>
                                    </li>
                                {% endif %}
                            </ul>
                            <div class="row">
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                    <a href="{{ url(['owner', 'owner': data['owner']['login']]) }}">
                                        <img src="{{ data['owner']['avatar_url'] }}&s=40" alt="" class="img-rounded" style="width:40px;height:40px;"/>
                                    </a>
                                </div>
                                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
                                    {% if is_show_date_added is defined AND data['added'] is defined %}
                                    <label class="label label-date">Added: <?= \Models\Project::utcTime($data['added'])->format('M d, Y') ?></label><br>
                                    {% else %}
                                    <label class="label label-date">Pushed: <?= \Models\Project::utcTime($data['pushed'])->format('M d, Y') ?></label>
                                    {% endif %}
                                    {% if data['composer']['keywords'] %}
                                    <ul class="list-inline">
                                        <li class="small">tags:</li>
                                        {% for tag in data['composer']['keywords'] %}
                                            <li>{{ link_to(['action', 'action': 'search', 'tag': tag], tag) }}</li>
                                        {% endfor %}
                                    </ul>
                                    {% endif %}
                                </div>
                            </div>
                            <p class="small">
                                {{ data['description'] }}
                            </p>
                        </li>
                    {% endfor %}
                </ul>
        </div>
    </div>
</section>