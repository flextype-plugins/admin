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
            // Trumbowyg
            'node_modules/trumbowyg/dist/ui/trumbowyg.min.css',
            'node_modules/trumbowyg/dist/plugins/table/ui/trumbowyg.table.css',
            
            // Blocks
            'blocks/InputEditorTrumbowyg/block.scss',

            // Admin
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
            // SpeakingURL
            'node_modules/speakingurl/speakingurl.min.js',

            // Trumbowyg
            'node_modules/trumbowyg/dist/trumbowyg.min.js',
            'node_modules/trumbowyg/dist/plugins/noembed/trumbowyg.noembed.min.js',
            'node_modules/trumbowyg/dist/plugins/table/trumbowyg.table.min.js',

            // Blocks
            'blocks/InputEditorTrumbowyg/block.js'
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
 * Task: gulp trumbowyg-fonts
 */
 gulp.task('trumbowyg-fonts', function () {
    return gulp
        .src(['node_modules/trumbowyg/dist/ui/icons.svg'])
        .pipe(size({ showFiles: true }))
        .pipe(gulp.dest('assets/dist/fonts/trumbowyg'));
});

/**
 * Task: gulp trumbowyg-langs
 */
gulp.task('trumbowyg-langs', function () {
    return gulp
        .src(['node_modules/trumbowyg/dist/*langs/**/*.min.js'])
        .pipe(size({ showFiles: true }))
        .pipe(gulp.dest('assets/dist/lang/trumbowyg'));
});

/**
 * Task: gulp default
 */
gulp.task('default', gulp.series(
    'trumbowyg-fonts', 'trumbowyg-langs', 'css', 'js'
));

/**
 * Task: gulp watch
 */
gulp.task('watch', function () {
    gulp.watch(["templates/**/*.html", "assets/src/"], gulp.series('default'));
});
