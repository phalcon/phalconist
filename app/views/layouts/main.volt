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
                    <a href="/add"><i class="glyphicon glyphicon-plus"> </i> Add</a>
                </li>
                {% if currentUser %}
                <li>
                    <a href="{{ url(['controller/action', 'controller': 'user', 'action': 'logout']) }}">
                        Logout
                    </a>
                </li>
                {% else %}
                <li class="page-scroll">
                    <noindex>
                    <a href="{{ login_url }}">
                        <i class="fa fa-github"></i> Login
                    </a>
                    </noindex>
                </li>
                {% endif %}
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container-fluid -->
</nav>

{{ content() }}

<footer class="text-center">
    <div class="footer-above">
        <div class="container">
            <div class="row">
                <noindex>
                    <div class="footer-col col-md-4">
                        <h3>Statistics</h3>

                        <p>
                            <small>Projects:</small> {{ project_count }}</p>
                        <p>
                            <small>Owners:</small> {{ owner_count }}</p>
                    </div>
                </noindex>
                <div class="footer-col col-md-4">
                </div>
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
                    Copyright &copy; Phalconist.com 2014
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
