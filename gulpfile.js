//
// Flextype Admin Gulp.js
// (c) Sergey Romanenko <https://github.com/Awilum>
//

var Promise = require("es6-promise").Promise,
    gulp = require('gulp'),
    csso = require('gulp-csso'),
    concat = require('gulp-concat'),
    sourcemaps = require('gulp-sourcemaps'),
    autoprefixer = require('gulp-autoprefixer'),
    sass = require('gulp-sass');

gulp.task('css', function() {
    return gulp.src('assets/scss/admin.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer({
            browsers: ['last 2 versions'],
            cascade: false
        }))
        .pipe(csso())
        .pipe(concat('admin.min.css'))
        .pipe(gulp.dest('assets/dist/css/'));
});

gulp.task('js', function(){
  return gulp.src(['node_modules/jquery/dist/jquery.min.js',
                   'node_modules/popper.js/dist/umd/popper.min.js',
                   'node_modules/bootstrap/dist/js/bootstrap.min.js',
                   'node_modules/trumbowyg/dist/trumbowyg.min.js'])
    .pipe(sourcemaps.init())
    .pipe(concat('admin.min.js'))
    .pipe(sourcemaps.write())
    .pipe(gulp.dest('assets/dist/js/'));
});

gulp.task('bootstrap-css', function() {
    return gulp.src('node_modules/bootstrap/dist/css/bootstrap.min.css')
        .pipe(gulp.dest('assets/dist/css/'));
});


gulp.task('trumbowyg-icons', function() {
    return gulp.src('node_modules/trumbowyg/dist/ui/icons.svg')
        .pipe(gulp.dest('assets/dist/icons/'));
});

gulp.task('trumbowyg-js', function() {
    return gulp.src('node_modules/trumbowyg/dist/trumbowyg.min.js')
        .pipe(gulp.dest('assets/dist/js/trumbowyg'));
});

gulp.task('trumbowyg-js-lang', function() {
    return gulp.src('node_modules/trumbowyg/dist/*langs/**/*')
        .pipe(gulp.dest('assets/dist/js/trumbowyg'));
});

gulp.task('trumbowyg-js-plugins', function() {
    return gulp.src('node_modules/trumbowyg/*plugins/**/*')
        .pipe(gulp.dest('assets/dist/js/trumbowyg'));
});

gulp.task('trumbowyg-css', function() {
    return gulp.src('node_modules/trumbowyg/dist/ui/trumbowyg.min.css')
        .pipe(gulp.dest('assets/dist/css/'));
});

gulp.task('default', ['css',
                      'js',
                      'bootstrap-css',
                      'trumbowyg-css',
                      'trumbowyg-js',
                      'trumbowyg-js-lang',
                      'trumbowyg-js-plugins',
                      'trumbowyg-icons']);
