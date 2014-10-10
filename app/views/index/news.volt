<section>
    <div class="container ">
        <h3>News</h3>
        <div class="row col-lg-10 col-lg-offset-1">
        <ul class="list-unstyled">
            {% for item in results %}
            <li style="margin-bottom: 40px;">
                <h5 style="margin-bottom: 0">{{ link_to(item.link, item.title) }}</h5>
                <div>
                    <span class="label label-default">
                        <i class="glyphicon glyphicon-calendar"> </i>
                        {{ date('d M', item.last_activity_date) }}
                    </span>
                    &nbsp;
                    <span class="label label-default">
                        <i class="glyphicon glyphicon-eye-open"> </i>
                        {{ item.view_count }}
                    </span>
                    &nbsp;
                    <span class="label label-default">
                        <i class="glyphicon glyphicon-exclamation-sign"> </i>
                        {{ item.answer_count }}
                    </span>
                    &nbsp;
                    {% for tag in item.tags %}
                        <span class="label label-default">{{ tag }}</span>
                    {% endfor %}
                </div>
            </li>
            {% endfor %}
        </ul>
    </div>
    </div>
</section>
