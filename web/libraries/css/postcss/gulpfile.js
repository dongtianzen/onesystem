/**
 * @file gulp 4
 * npm install postcss-for --save-dev
 * npm install gulp-rename --save-dev
 *
 * /usr/local/Cellar/node/11.12.0/bin/gulp
 */
var gulp = require('gulp');
var postcss = require('gulp-postcss');
var cssnano = require('cssnano');
var rename = require("gulp-rename");

function defaultTask(){
  return gulp.src('css/*.css')
    .pipe( postcss([require('autoprefixer'), require('postcss-for')]) )
    .pipe( rename({ suffix: "-plugin" }) )
    .pipe( gulp.dest('dest/') );
}

function minifierCss(){
  return gulp.src('css/*.css')
    .pipe( postcss([require('autoprefixer'), cssnano(), require('postcss-for')]) )
    .pipe( rename({ suffix: "-plugin.min" }) )
    .pipe( gulp.dest('min/') );
}

gulp.task('default', gulp.parallel(defaultTask, minifierCss));
