<!-- Navigation -->
<nav class="navbar navbar-default navbar-fixed-top">
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
            <a class="navbar-brand" href="/" style="padding-bottom:0;height:auto">
                <img src="/img/logo.png" alt="phalcon php" style="width: 36px;margin-top: -30px"/>
                Phalconist
            </a>
            <small class="navbar-brand-caption" style="margin-left: 46px;">Phalcon Framework Extensions</small>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <li style="margin-right: 30px;">
                    <form name="search" method="get" action="/search">
                        <input name="q" type="text" class="form-control" placeholder="search ext." id="search" required style="margin-top: 10px;">
                    </form>
                </li>
{#
                <li>
                    <a href="/add"><i class="glyphicon glyphicon-plus"> </i> Add Ext</a>
                </li>
#}
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container-fluid -->
</nav>

{{ content() }}

<!-- Footer -->
<footer class="text-center">
    <div class="footer-below">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    Copyright &copy; Phalconist.com 2014
                </div>
            </div>
        </div>
    </div>
</footer>
