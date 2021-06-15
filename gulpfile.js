const gulp         = require('gulp');
const concat       = require('gulp-concat');
const csso         = require('gulp-csso');
const autoprefixer = require('gulp-autoprefixer');
const sass         = require('gulp-sass');
const size         = require("gulp-size");
const gzip         = require("gulp-gzip");
const rename       = require("gulp-rename")
sass.compiler      = require('node-sass');

/**
 * Task: gulp css
 */
 gulp.task("css", function () {
    return gulp
        .src([
            'assets/src/scss/admin.scss'
        ])
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer({
            overrideBrowserslist: [
                "last 1 version"
            ],
            cascade: false
        }))
        .pipe(csso())
        .pipe(concat('admin.min.css'))
        .pipe(gulp.dest("assets/dist/css/"))
        .pipe(size({ showFiles: true }))
        .pipe(gzip())
        .pipe(rename("admin.min.css.gz"))
        .pipe(gulp.dest("assets/dist/css/"))
        .pipe(size({ showFiles: true, gzip: true }));
});


/**
 * Task: gulp js
 */
 gulp.task('js', function () {
    return gulp
        .src([
            'node_modules/speakingurl/speakingurl.min.js'
        ])
        .pipe(concat('admin.min.js'))
        .pipe(size({ showFiles: true }))
        .pipe(gulp.dest('assets/dist/js/'))
        .pipe(gzip())
        .pipe(rename("admin.min.js.gz"))
        .pipe(gulp.dest("assets/dist/js/"))
        .pipe(size({ showFiles: true, gzip: true }));
});

/**
 * Task: gulp default
 */
gulp.task('default', gulp.series(
    'css', 'js'
));

/**
 * Task: gulp watch
 */
gulp.task('watch', function () {
    gulp.watch(["templates/**/*.html", "assets/src/"], gulp.series('default'));
});
