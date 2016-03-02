var gulp     = require('gulp');
var minifyCss = require('gulp-minify-css');
var rename   = require('gulp-rename');
var uglify = require('gulp-uglify');
var imageop = require('gulp-image-optimization');
 

var config = {
  css: {
    main: './public/assets/css/style.css',
    bootstrap: './public/assets/css/style.css',
    watch: './public/assets/css/**/*.css',
    output: './public/css'
  },
  js:{
      source: './public/assets/js/**/*.js',
      output: './public/js'
  },
  img:{
      source: ['./public/img/**/*.png','./public/img/**/*.jpg','./public/img/**/*.gif','./public/img/**/*.jpeg'],
      output: './public/img'
  }
};
 
gulp.task('min-css', function() {
  return gulp.src(config.css.main)
    .pipe(minifyCss({compatibility: 'ie8'}))
    .pipe(rename({
           suffix: '.min'
       }))
   .pipe(gulp.dest(config.css.output));
});

gulp.task('boot-min-css', function() {
  return gulp.src(config.css.bootstrap)
    .pipe(minifyCss({compatibility: 'ie8'}))
    .pipe(rename({
           suffix: '.min'
       }))
   .pipe(gulp.dest(config.css.output));
});

gulp.task('js-min', function() {
  return gulp.src(config.js.source)
    .pipe(uglify())
    .pipe(gulp.dest(config.js.output));
});

gulp.task('img', function() {
    return gulp.src(config.img.source)
    .pipe(imageop({
        optimizationLevel: 5,
        progressive: true,
        interlaced: true
    })).pipe(gulp.dest(config.img.output));
});


gulp.task('watch', function() {
  gulp.watch(config.css.watch, ['min-css']);
  gulp.watch(config.css.watch, ['boot-min-css']);
  gulp.watch(config.js.source, ['js-min']);
  gulp.watch(config.img.source, ['images']);
});


gulp.task('default', ['min-css','boot-min-css','js-min','img']);