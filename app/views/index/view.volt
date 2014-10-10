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
                <h3 style="margin-top: 40px">
                    <small><a href="https://github.com/{{ project['owner']['login'] }}">{{ project['owner']['login'] }}</a></small>
                </h3>
                <h2>
                    <a href="{{ project['urls']['html'] }}">{{ project['name'] }}</a>
                </h2>

                <p>{{ project['description'] }}</p>
            </div>
        </div>

        <div class="row">
            <hr/>
            <div class="col-lg-6">
                <h5>Details</h5>
                Created: <span class="label label-default"><?= \Models\Project::utcTime($project['created'])->format('M d, Y') ?></span><br>
                Updated: <span class="label label-default"><?= \Models\Project::utcTime($project['updated'])->format('M d, Y') ?></span><br>
                Composer:
                <span class="label label-default">
                    <i class="glyphicon glyphicon-music"></i>
                    {% if project['is_composer'] %}
                        &nbsp;&nbsp;<i class="glyphicon glyphicon-check"></i>
                    {% else %}
                        &nbsp;&nbsp;<i class="glyphicon glyphicon-unchecked"></i>
                    {% endif %}
                </span><br>

                {% if project['composer']['license'] is defined AND project['composer']['license'] %}
                    License:
                    <span class="label label-default">{{ project['composer']['license'] }}</span><br>
                {% endif %}
                {% if project['composer']['version'] is defined AND project['composer']['version'] %}
                    Version:
                    <span class="label label-default">{{ project['composer']['version'] }}</span><br/>
                {% endif %}

                {% if project['composer']['authors'] is defined AND project['composer']['authors'] %}
                    Authors:
                    {% for author in project['composer']['authors'] %}
                        <span class="label label-default">{{ author['name'] }}</span>
                    {% endfor %}
                    <br/>
                {% endif %}

                {% if project['composer']['require'] is defined AND project['composer']['require'] %}
                    <ul class="list-inline">
                        <li>Requires:</li>
                        {% for key, ver in project['composer']['require'] %}
                            <li class="label label-warning">{{ key ~ ' ' ~ ver }}</li>
                        {% endfor %}
                    </ul>
                {% endif %}
            </div>

            <div class="col-lg-6">
                {% if project['downloads']['total'] is defined AND project['downloads']['total'] > 0%}
                <h5>Downloads</h5>
                Total: <span class="label label-default">{{ project['downloads']['total'] }}</span><br>
                Monthly: <span class="label label-default">{{ project['downloads']['monthly'] }}</span><br>
                Daily: <span class="label label-default">{{ project['downloads']['daily'] }}</span><br>
                {% endif %}

                {% if project['composer']['keywords'] %}
                <h5>Tags</h5>
                <ul class="list-inline">
                    {% for tag in project['composer']['keywords'] %}
                        <li>{{ link_to(['action', 'action': 'search', 'tag': tag], tag) }}</li>
                    {% endfor %}
                </ul>
                {% endif %}
            </div>
        </div>


        <div class="row col-lg-12">
            <hr/>
            <article class="markdown-body">
                {{ project['readme'] }}
            </article>
            <hr/>
        </div>

        <div class="row col-lg-12">
            <div id="disqus_thread"></div>
            <script type="text/javascript">
            /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
            var disqus_shortname = 'phalconist'; // required: replace example with your forum shortname
            var disqus_identifier = 'project:{{ project['id'] }}';

            /* * * DON'T EDIT BELOW THIS LINE * * */
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
</div>
