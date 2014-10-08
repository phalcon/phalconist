var
    gulp = require('gulp'),
    minifyCSS = require('gulp-minify-css'),
    $ = require('gulp-load-plugins')();

cssSrc = [
    './public/css/bootstrap.min.css',
    './public/css/freelancer.css',
    './public/src/vendor/font-awesome/css/font-awesome.min.css'
];

gulp.task('stylesheets', function() {
    return gulp.src(cssSrc).pipe($.concat('project.css')).pipe(minifyCSS({
        removeEmpty: true,
        keepSpecialComments: 0
    })).pipe(gulp.dest('./public/dist/'));
});

gulp.task('default', ['stylesheets']);