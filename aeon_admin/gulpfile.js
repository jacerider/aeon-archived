/**
 * @file
 * Gulp task definition.
 */

"use strict";

var gulp = require('gulp');
var gutil = require('gulp-util');
var browserSync = require('browser-sync').create();
var fs = require('fs');
var extend = require('extend');
var config = require('./config/dev/config.json');
var execSync = require('child_process').execSync;

// Include plugins.
var sass = require('gulp-sass');
var sassLint = require('gulp-sass-lint');
var imagemin = require('gulp-imagemin');
var pngcrush = require('imagemin-pngcrush');
var plumber = require('gulp-plumber');
var notify = require('gulp-notify');
var autoprefix = require('gulp-autoprefixer');
var glob = require('gulp-sass-glob');
var rename = require('gulp-rename');
var sourcemaps = require('gulp-sourcemaps');
var breakpoints = require('aeon-breakpoints');
var babel = require('gulp-babel');
var uglify = require('gulp-uglify');
var eslint = require('gulp-eslint');
var gulpStylelint = require('gulp-stylelint');

// If config.js exists, load that config for overriding certain values below.
function loadConfig() {
  if (fs.existsSync('./config/dev/config.local.json')) {
    config = extend(true, config, require('./config/dev/config.local'));
  }
  return config;
}
loadConfig();

/**
 * Run drush to clear the theme registry
 */
let drupal;
gulp.task('exo', function () {
  execSync('drush exo-scss');
  drupal = JSON.parse(execSync('drush status --format=json').toString());
  config.css.includePaths.push(drupal['root'] + '/' + drupal['site'] + '/files/exo');
});

// CSS.
gulp.task('css', function () {
  return gulp.src(config.css.src)
    .pipe(glob())
    .pipe(plumber({
      errorHandler: function (error) {
        notify.onError({
          title: "Gulp",
          subtitle: "Failure!",
          message: "Error: <%= error.message %>",
          sound: "Beep"
        })(error);
        this.emit('end');
      }
    }))
    .pipe(sourcemaps.init())
    .pipe(sass({
      style: 'compressed',
      errLogToConsole: true,
      includePaths: config.css.includePaths
    }))
    .pipe(autoprefix('last 2 versions', '> 1%', 'ie 9', 'ie 10'))
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest(config.css.dest))
    .on('finish', function lintCssTask() {
      return gulp
        .src(config.css.src)
        .pipe(gulpStylelint({
          failAfterError: false,
          // reportOutputDir: 'reports/lint',
          reporters: [
            // { formatter: 'verbose', console: true },
            { formatter: 'string', console: true },
            // { formatter: 'json', save: 'report.json' },
          ],
          debug: true
        }));
    })
    // .on('finish', function () {
    //   gulp.src(config.css.src)
    //     .pipe(sassLint({
    //         configFile: 'config/.sass-lint.yml'
    //       }))
    //     .pipe(sassLint.format());
    // })
    .pipe(config.browserSync.enabled ? browserSync.reload({
      stream: true,
      // once: true,
      match: '**/*.css'
    }) : gutil.noop());
});

// Stylelint.
gulp.task('lint-css', function lintCssTask() {
  return gulp
    .src(config.css.src)
    .pipe(gulpStylelint({
      failAfterError: true,
      // reportOutputDir: 'reports/lint',
      reporters: [
        { formatter: 'verbose', console: true },
        { formatter: 'json', save: 'report.json' },
      ],
      debug: true
    }));
});

// Javascript.
gulp.task('js', function () {
  return gulp.src(config.js.src)
    .pipe(plumber())
    .pipe(eslint({
      configFile: 'config/dev/.eslintrc',
      useEslintrc: false
    }))
    .pipe(eslint.format())
    .pipe(uglify())
    .pipe(gulp.dest(config.js.dest))
    .pipe(config.browserSync.enabled ? browserSync.reload({
      stream: true,
      // once: true,
      match: '**/*.js'
    }) : gutil.noop());
});

// Vendor javascript.
// gulp.task('jsVendor', function () {
//   return gulp.src(config.js.vendorPaths)
//     .pipe(babel({
//       presets: ['es2015']
//     }))
//     .pipe(uglify())
//     .pipe(gulp.dest(config.js.vendorDest));
// });

// Vendor javascript.
gulp.task('templates', function () {
  return gulp.src(config.templates.src)
    .pipe(config.browserSync.enabled ? browserSync.reload({
      stream: true
    }) : gutil.noop());
});

// Compress images.
gulp.task('images', function () {
  return gulp.src(config.images.src)
    .pipe(imagemin({
      progressive: true,
      svgoPlugins: [{
        removeViewBox: false
      }],
      use: [pngcrush()]
    }))
    .pipe(gulp.dest(config.images.dest));
});

// Calculate breakpoints.
gulp.task('breakpoints', function () {
  gulp.src('./../aeon_admin.breakpoints.yml')
    .pipe(breakpoints.ymlToScss())
    .pipe(rename('_breakpoints.scss'))
    .pipe(gulp.dest('scss/base'))
});

// Watch task.
gulp.task('watch', function () {
  gulp.watch(config.css.src, ['css']);
  gulp.watch(config.js.src, ['js']);
  gulp.watch(config.templates.src, ['templates']);
});

// Static Server + Watch.
gulp.task('serve', ['breakpoints', 'exo', 'css', 'js', 'watch'], function () {
  if (config.browserSync.enabled) {
    browserSync.init({
      proxy: config.browserSync.proxy,
      port: config.browserSync.port,
      open: config.browserSync.openAutomatically,
      notify: config.browserSync.notify,
    });
  }
});

// Run drush to clear the theme registry.
gulp.task('drush', function () {
  execSync('drush cr', function (err, stdout, stderr) {
    if (config.browserSync.enabled) {
      browserSync.reload();
    }
  });
});

// Run drush to clear the theme registry.
// gulp.task('drush', shell.task([
//   'drush cache-clear theme-registry'
// ]));

// Default Task.
gulp.task('default', ['serve']);
