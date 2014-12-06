<script async defer id="github-bjs" src="https://buttons.github.io/buttons.js"></script>
<link rel="stylesheet" href="/css/github-markdown.css" />

<div class="container" style="margin-top: 30px;">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-12">
                <ul class="col-lg-12 text-left list-inline" style="padding-left: 0">
                    {# https://buttons.github.io #}
                    <li><a class="github-button" href="https://github.com/{{ project['owner']['login'] }}" data-count-href="/{{ project['owner']['login'] }}/followers" data-count-api="/users/{{ project['owner']['login'] }}#followers">Follow @{{ project['owner']['login'] }}</a></li>
                    <li><a class="github-button" href="https://github.com/{{ project['full_name'] }}" data-icon="octicon-eye" data-count-href="/{{ project['full_name'] }}/watchers" data-count-api="/repos/{{ project['full_name'] }}#subscribers_count">Watch</a></li>
                    <li><a class="github-button" href="https://github.com/{{ project['full_name'] }}" data-icon="octicon-star" data-count-href="/{{ project['full_name'] }}/stargazers" data-count-api="/repos/{{ project['full_name'] }}#stargazers_count">Star</a></li>
                    <li><a class="github-button" href="https://github.com/{{ project['full_name'] }}" data-icon="octicon-git-branch" {#data-style="mega" #}data-count-href="/{{ project['full_name'] }}/network" data-count-api="/repos/{{ project['full_name'] }}#forks_count">Fork</a></li>
                    <li><a class="github-button" href="https://github.com/{{ project['full_name'] }}/archive/master.zip" data-icon="octicon-cloud-download">Download</a>
                </ul>
                <h3>
                    <a href="{{ project['urls']['html'] }}">{{ project['name'] }}</a>
                    <small>maintained by</small>
                    <a href="https://github.com/{{ project['owner']['login'] }}" title="Projects by {{ project['owner']['login'] }}">{{ project['owner']['login'] }}</a>
                </h3>

                <p>{{ project['description'] }}</p>
            </div>
        </div>

        <hr/>

        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="row">
                {% if project['composer']['keywords'] %}
                <h5>Tags</h5>
                <ul class="list-inline col-xs-12 col-sm-10 col-sm-offset-2 col-md-9 col-md-offset-3 col-lg-9 col-lg-offset-3">
                    {% for tag in project['composer']['keywords'] %}
                        <li>{{ link_to(['action', 'action': 'search', 'tag': tag], tag) }}</li>
                    {% endfor %}
                </ul>
                {% endif %}

                <h5>Details</h5>

                <div class="col-xs-12 col-sm-2 col-md-3 col-lg-3">
                    Score:
                </div>
                <div class="col-xs-12 col-sm-10 col-md-9 col-lg-9">
                    <span class="label label-default"><i class="glyphicon glyphicon-stats"> </i> {{ (project['score'] is defined) ? project['score'] : 0 }}</span>
                </div>

                {% if project['composer']['version'] is defined AND project['composer']['version'] %}
                <div class="col-xs-12 col-sm-2 col-md-3 col-lg-3">
                    Version:
                </div>
                <div class="col-xs-12 col-sm-10 col-md-9 col-lg-9">
                    <span class="label label-default">{{ project['composer']['version'] }}</span><br/>
                </div>
                {% endif %}

                <div class="col-xs-12 col-sm-2 col-md-3 col-lg-3">
                    Pushed:
                </div>
                <div class="col-xs-12 col-sm-10 col-md-9 col-lg-9">
                    <span class="label label-default"><?= \Models\Project::utcTime($project['pushed'])->format('M d, Y') ?></span>
                </div>

                <div class="col-xs-12 col-sm-2 col-md-3 col-lg-3">
                    Updated:
                </div>
                <div class="col-xs-12 col-sm-10 col-md-9 col-lg-9">
                    <span class="label label-default"><?= \Models\Project::utcTime($project['updated'])->format('M d, Y') ?></span>
                </div>

                <div class="col-xs-12 col-sm-2 col-md-3 col-lg-3">
                    Created:
                </div>
                <div class="col-xs-12 col-sm-10 col-md-9 col-lg-9">
                    <span class="label label-default"><?= \Models\Project::utcTime($project['created'])->format('M d, Y') ?></span>
                </div>

                {% if project['downloads']['total'] is defined AND project['downloads']['total'] > 0%}
                <div class="col-xs-12 col-sm-2 col-md-3 col-lg-3">
                    Downloads:
                </div>
                <div class="col-xs-12 col-sm-10 col-md-9 col-lg-9">
                    <span class="label label-default">total: {{ project['downloads']['total'] }}</span>
                    <span class="label label-default">monthly: {{ project['downloads']['monthly'] }}</span>
                    <span class="label label-default">daily: {{ project['downloads']['daily'] }}</span>
                </div>
                {% endif %}

                {% if project['composer']['authors'] is defined AND project['composer']['authors'] %}
                <div class="col-xs-12 col-sm-2 col-md-3 col-lg-3">
                    Author:
                </div>
                <div class="col-xs-12 col-sm-10 col-md-9 col-lg-9">
                    {% for author in project['composer']['authors'] %}
                        {% set name = author['name'] ? author['name'] : author['email'] %}
                        <div>
                            <span class="label label-default">{{ author['name'] }}</span>
                            {% if author['email'] is defined %}
                                <a href="mailto:{{ author['email'] }}"><span class="label label-default"><i class="glyphicon glyphicon-envelope"></i></span></a>
                            {% endif %}
                            {% if author['homepage'] is defined %}
                                <a href="{{ author['homepage'] }}"><span class="label label-default"><i class="glyphicon glyphicon-globe"></i></span></a>
                            {% endif %}
                        </div>
                    {% endfor %}
                </div>
                {% endif %}

                {% if project['composer']['license'] is defined AND project['composer']['license'] %}
                <div class="col-xs-12 col-sm-2 col-md-3 col-lg-3">
                    License:
                </div>
                <div class="col-xs-12 col-sm-10 col-md-9 col-lg-9">
                    <span class="label label-default">{{ project['composer']['license'] }}</span>
                </div>
                {% endif %}

                {% if project['is_composer'] %}
                <div class="col-xs-12 col-sm-2 col-md-3 col-lg-3">
                    Composer:
                </div>
                <div class="col-xs-12 col-sm-10 col-md-9 col-lg-9">
                    <span class="label label-default"><i class="glyphicon glyphicon-music"></i></span>
                    <small>{{ project['composer']['name'] }}</small>
                </div>
                {% endif %}

                {% if project['composer']['require'] is defined AND project['composer']['require'] %}
                <div class="col-xs-12 col-sm-2 col-md-3 col-lg-3">
                    Requires:
                </div>
                <div class="col-xs-12 col-sm-10 col-md-9 col-lg-9">
                    {% for key, ver in project['composer']['require'] %}
                        <span class="label label-warning">{{ key ~ ' ' ~ ver }}</span>
                    {% endfor %}
                </div>
                {% endif %}
            </div>

            <hr />

            <div class="row" style="margin: 10px;">
                <div id="disqus_thread"></div>
                <script type="text/javascript">
                var disqus_shortname = 'phalconist';
                var disqus_identifier = 'project:{{ project['id'] }}';
                (function() {
                    var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
                    dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
                    (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
                })();
                </script>
                <noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
                <a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>
            </div>
        </div>

        <div class="hidden-xs col-sm-12 col-md-6 col-lg-6">
            <div class="row clearfix" style="margin-bottom: 20px;">
                <article class="markdown-body">
                    {{ project['readme'] }}
                </article>
            </div>
        </div>

    </div>
</div>
