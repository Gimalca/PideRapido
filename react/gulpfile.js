// gulpfile.js
var gulp = require('gulp');
var browserify = require('browserify');
var babelify = require('babelify');
var uglify = require('gulp-uglify');
var buffer = require('vinyl-buffer');
var source = require('vinyl-source-stream');
// css
var concat = require('gulp-concat');
var minifyCSS = require('gulp-minify-css');
var autoprefixer = require('gulp-autoprefixer');
var rename = require('gulp-rename');
 
gulp.task('build', function () {
  browserify({
    entries: ['views/index.jsx'],
    extensions: ['.jsx'],
    debug: true
  })
  .transform(babelify)
  .bundle()
  .pipe(source('bundle.min.js'))
  .pipe(buffer())
  .pipe(uglify())
  .pipe(gulp.dest('../public/assets/js/dist'));
});

gulp.task('css', function () {

    gulp.src('styles/**/*.css')
        .pipe(minifyCSS())
        .pipe(autoprefixer('last 2 version', 'safari 5', 'ie 8', 'ie 9'))
        .pipe(concat('bundle.min.css'))
        .pipe(gulp.dest('../public/assets/css'));
});
 
gulp.task('watch', function () {
    gulp.watch('src/**/*.*', ['build']);
    gulp.watch('styles/**/*.*.css', ['css']);
});

gulp.task('default', ['css','build', 'watch']);
