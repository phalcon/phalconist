/*!
 * Start Bootstrap - Freelancer Bootstrap Theme (http://startbootstrap.com)
 * Code licensed under the Apache License v2.0.
 * For details, see http://www.apache.org/licenses/LICENSE-2.0.
 */

// jQuery for page scrolling feature - requires jQuery Easing plugin
$(function() {
    $('.page-scroll a').bind('click', function(event) {
        var $anchor = $(this);
        $('html, body').stop().animate({
            scrollTop: $($anchor.attr('href')).offset().top
        }, 1500, 'easeInOutExpo');
        event.preventDefault();
    });

    $('#search').focus();
});

// Floating label headings for the contact form
$(function() {
    $("body").on("input propertychange", ".floating-label-form-group", function(e) {
        $(this).toggleClass("floating-label-form-group-with-value", !! $(e.target).val());
    }).on("focus", ".floating-label-form-group", function() {
        $(this).addClass("floating-label-form-group-with-focus");
    }).on("blur", ".floating-label-form-group", function() {
        $(this).removeClass("floating-label-form-group-with-focus");
    });
});

// Highlight the top nav as scrolling occurs
$('body').scrollspy({
    target: '.navbar-fixed-top'
});

// Closes the Responsive Menu on Menu Item Click
$('.navbar-collapse ul li a').click(function() {
    $('.navbar-toggle:visible').click();
});


window.fetchLastComments = function(disqus_public_key){
    $.ajax({
        type: 'GET',
        url: 'https://disqus.com/api/3.0/forums/listPosts.json?api_key=' + disqus_public_key + '&forum=phalconist&related=thread&limit=7',
        cache: false,
        dataType: 'jsonp',
        success: function(resp) {
            if (resp.response && resp.response.length) {
                $('.a-comments-header').html('<h4>Comments</h4>');
                for(var i in resp.response) {
                    $('#comment_widget_js').append(
                            '<div class="media">' +
                                '<span class="pull-left">' +
                                    '<img class="media-object img-rounded" src="' + resp.response[i].author.avatar.cache + '" alt="" width="40">' +
                                '</span>' +
                                '<div class="media-body">' +
                                    '<h5 class="media-heading">' +
                                        '<a href="' + resp.response[i].thread.link + '#disqus_thread">' + resp.response[i].thread.clean_title + '</a>' +
                            '       </h5>' +
                                    '<span class="small" style="color:#999">' + resp.response[i].author.name + '</span>' +
                                    '<div>' + resp.response[i].raw_message.substr(0, 200) + '</div>' +
                                '</div>' +
                            '</div>');
                }
            }
        }
    });
};