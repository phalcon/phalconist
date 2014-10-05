<!-- Contact Section -->
<section id="addExt" style="padding-top: 150px">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h3>
                    {% if q is defined and q %}
                        <small>search</small> {{ q }}
                    {% endif %}
                    {% if tags is defined AND tags %}
                        <small>tag</small> {{ tags }}
                    {% endif %}
                    {% if owner is defined and owner %}
                        <small>contributor</small> {{ owner }}
                    {% endif %}
                </h3>
                <br/>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-10 col-lg-offset-1">
                <ul class="list-inline">
                    {% for item in results %}
                        {% set data = item.getData() %}
                        <li class="col-xs-4" style="height: 15em;">
                            <h4 style="margin-bottom: 0;">
                                <small>{{ data['owner']['login'] }}<br>
                                </small>{{ link_to(['action', 'action': 'view', 'id': item.getId()], data['name']) }}
                            </h4>
                            <ul class="list-inline" style="margin: 0 0 8px 0;">
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
                            <p class="small">
                                {{ data['description'] }}
                            </p>
                            <div><label class="label label-date">Last update: <?= \Models\Project::utcTime($data['updated'])->format('M d, Y') ?></label></div>
                        </li>
                    {% endfor %}
                </ul>
            </div>
        </div>
    </div>
</section>