const gulp = require('gulp');
const sass = require('gulp-sass');
const sourcemaps = require('gulp-sourcemaps');
const notifier = require('gulp-notifier');
const webpack = require('webpack-stream');

sass.compiler = require('node-sass');

// type 'gulp webpack' to compile webpack with the mode set to none
// ====================================================================
gulp.task('webpack', done => {
  gulp.src('js/index.js')
    .pipe(webpack( require('./webpack.config.js') ))
    .pipe(gulp.dest('../'));
    done();
});

// type 'gulp sass' to compile CSS
// ====================================================================
gulp.task('sass', () => {
  return gulp.src([
      'css/**/*.scss',
      'css/**/*.css'
    ])
    .pipe(sourcemaps.init())
    .pipe(sass())
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest('../'))
    .pipe(notifier.success());
});

// type 'gulp watch' to watch and compile both CSS and webpack
// turn off by typing 'Ctrl + c'
// ====================================================================
gulp.task('watch', () => {
    gulp.watch([
        'css/**/*.scss',
        'css/**/*.css'
      ],
      gulp.series('webpack', 'sass'));
});
