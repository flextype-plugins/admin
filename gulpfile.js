const gulp = require('gulp');

/**
 * Task: gulp vendor-css
 */
 gulp.task("vendor-css", function() {
   const concat = require('gulp-concat');
   const csso = require('gulp-csso');
   const autoprefixer = require('gulp-autoprefixer');

   return gulp
     .src([
          // Swal2
          'node_modules/sweetalert2/dist/sweetalert2.min.css',

          // AnimateCSS
          'node_modules/animate.css/animate.min.css'])
     .pipe(autoprefixer({
         overrideBrowserslist: [
             "last 1 version"
         ],
         cascade: false
     }))
     .pipe(csso())
     .pipe(concat('admin-vendor-build.min.css'))
     .pipe(gulp.dest("assets/dist/css/"));
 });


/**
 * Task: gulp admin-css
 */
gulp.task("admin-css", function() {
  const concat = require('gulp-concat');
  const csso = require('gulp-csso');
  const sourcemaps = require('gulp-sourcemaps');
  const autoprefixer = require('gulp-autoprefixer');
  const atimport = require("postcss-import");
  const postcss = require("gulp-postcss");

  return gulp
    .src(['assets/src/css/admin-panel.css'])
    .pipe(postcss([atimport()]))
    .pipe(autoprefixer({
        overrideBrowserslist: [
            "last 1 version"
        ],
        cascade: false
    }))
    .pipe(csso())
    .pipe(concat('admin-build.min.css'))
    .pipe(gulp.dest("assets/dist/css/"));
});

/**
 * Task: gulp ace-js
 */
gulp.task('ace-js', function(){
    return gulp.src(['assets/src/js/ace/*'])
        .pipe(gulp.dest('assets/dist/js/ace/'));
});

/**
 * Task: gulp vendor-js
 */
gulp.task('vendor-js', function(){
   const sourcemaps = require('gulp-sourcemaps');
   const concat = require('gulp-concat');

   return gulp.src([
                    // Swal2
                    'node_modules/sweetalert2/dist/sweetalert2.min.js',
                    
                    // SpeakingURL
                    'node_modules/speakingurl/speakingurl.min.js',

                    // Clipboard
                    'node_modules/clipboard/dist/clipboard.min.js'
                 ])
     .pipe(sourcemaps.init())
     .pipe(concat('admin-vendor-build.min.js'))
     .pipe(sourcemaps.write())
     .pipe(gulp.dest('assets/dist/js/'));
});

/**
 * Task: gulp default
 */
gulp.task('default', gulp.series(
    'vendor-css', 'vendor-js', 'admin-css', 'ace-js'
));

/**
 * Task: gulp watch
 */
gulp.task('watch', function () {
    gulp.watch(["templates/**/*.html", "assets/src/"], gulp.series('vendor-css', 'admin-css'));
});
