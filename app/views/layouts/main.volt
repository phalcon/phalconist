<nav class="navbar navbar-default">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header page-scroll">
            <button type="button" class="navbar-toggle" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/" style="padding-bottom:0;height:auto;display: block">
                <img src="/img/logo.png" alt="" style="width: 36px;margin-top: -6px;" />
                <strong>Phalconist</strong>
            </a>
            <small class="navbar-brand-caption" style="margin-left: 37px;">Framework Resources</small>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <li style="margin-right: 30px;">
                    <form name="search" method="get" action="/search">
                        <input name="q" type="text" class="form-control" placeholder="search" id="search" required style="margin-top: 10px;">
                    </form>
                </li>
                <li>
                    <a href="/add" rel="nofollow"><i class="glyphicon glyphicon-plus"> </i> Add</a>
                </li>
                {% if currentUser is defined %}
                <li>
                    <a href="{{ url(['controller/action', 'controller': 'user', 'action': 'logout']) }}">
                        Logout
                    </a>
                </li>
                {% else %}
                {% if (login_url is defined) %}
                <li class="page-scroll">
                    <a href="{{ login_url }}" rel="nofollow">
                        <i class="fa fa-github"></i> Login
                    </a>
                </li>
                {% endif %}
                {% endif %}
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container-fluid -->
</nav>

{% if flash.getMessages(null, false)|length %}
<div class="col-lg-10 col-lg-offset-1 col-md-10 col-md-offset-1 col-sx-10 col-sx-offset-1 col-xs-12">
    {{ flash.output() }}
</div>
{% endif %}

{{ content() }}

<footer class="text-center">
    <div class="footer-above">
        <div class="container">
            <div class="row">
                {% if project_count is defined %}
                <noindex>
                    <div class="footer-col col-md-4">
                        <h3>Statistics</h3>

                        <p>
                            <small>Projects:</small> {{ project_count }}</p>
                        <p>
                            <small>Owners:</small> {{ owner_count }}</p>
                    </div>
                </noindex>
                {% endif %}
                {% if last_added is defined %}
                <div class="footer-col col-md-4">
                    <h3><a href="/last" style="color:#fff">Last added</a></h3>
                    <ul class="list-unstyled">
                        {% for item in last_added %}
                        {% set last_added_item = item.getData() %}
                        <li class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 10px;">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-right" style="padding-right: 0;">
                                <span class="label label-default" style="background-color: #18bc9c;color: #000;">
                                    <?= \Models\Project::utcTime($last_added_item['added'])->format('d M') ?>
                                </span>
                            </div>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 text-left" data-id="{{ item.getId() }}" data-url="{{ url(['view/item', 'owner': last_added_item['owner']['login'], 'repo': last_added_item['repo']]) }}">
                                {{ link_to(['view/item', 'owner': last_added_item['owner']['login'], 'repo': last_added_item['repo']], last_added_item['name']|capitalize) }}
                            </div>
                        </li>
                        {% endfor %}
                    </ul>
                    <div class="text-right col-lg-4 col-md-4 col-sm-4 col-xs-4 text-right" style="padding: 0">
                    <a href="/last">more</a>
                    </div>
                </div>
                {% endif %}
                <div class="footer-col col-md-4">
                </div>
            </div>
        </div>
    </div>
    <div class="footer-below">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                </div>
                <div class="col-lg-4">
                    Copyright &copy; Phalconist.com {{ date("Y") }}
                    <ul class="list-inline">
                        <li>
                            <a href="https://github.com/phalconist/phalconist" title="https://github.com/phalconist/phalconist" class="btn-social btn-outline"><i class="fa fa-fw fa-github"></i></a>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-4">
                </div>
            </div>
        </div>
    </div>
</footer>
