var
    gulp = require('gulp'),
    minifyCSS = require('gulp-minify-css'),
    $ = require('gulp-load-plugins')(),
    distPath = './public/dist/';

var cssSrc = [
    './public/css/bootstrap.min.css',
    './public/css/freelancer.css',
    './public/src/vendor/font-awesome/css/font-awesome.min.css'
];

gulp.task('stylesheets', function() {
    $.run('cp public/src/vendor/font-awesome/fonts/* ./public/fonts/ -R');

    return gulp.src(cssSrc).pipe($.concat('project.css')).pipe(minifyCSS({
        removeEmpty: true,
        keepSpecialComments: 0
    })).pipe(gulp.dest(distPath));
});

var jsSrc = [
    './public/src/vendor/jquery/dist/jquery.min.js',
    './public/src/vendor/bootstrap/dist/js/bootstrap.min.js',
    './public/src/vendor/jquery.easing/js/jquery.easing.min.js',
    './public/js/classie.js',
    './public/js/cbpAnimatedHeader.js',
    './public/js/jqBootstrapValidation.js',
    './public/js/contact_me.js',
    './public/js/freelancer.js'
];

gulp.task('js', function() {
    return gulp.src(jsSrc).pipe($.concat('min.js')).pipe($.uglify())
        .pipe(gulp.dest(distPath));
});

gulp.task('default', ['stylesheets', 'js']);