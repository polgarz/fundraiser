'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass')(require('sass'));
var concat = require('gulp-concat');
var autoprefixer = require('gulp-autoprefixer');
var csso = require('gulp-csso');
var uglify = require('gulp-uglify-es').default;
var rename = require('gulp-rename');
var del = require('del');

sass.compiler = require('node-sass');

// GENERAL

gulp.task('scripts-general', function() {
    return gulp.src([
            './web/src/js/general.js'
        ])
        .pipe(uglify())
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest('./web/dist/js'));
});

// ADMIN

gulp.task('scripts-admin', function() {
    return gulp.src([
            './web/src/js/admin.js'
        ])
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest('./web/dist/js')).on('end', function(e) {
            return gulp.src([
                './node_modules/bootstrap/dist/js/bootstrap.bundle.js',
                './web/dist/js/admin.min.js',
            ])
            .pipe(uglify())
            .pipe(concat('admin.min.js'))
            .pipe(gulp.dest('./web/dist/js'))
        });
});

// GENERAL
gulp.task('styles', function() {
    return gulp.src([
            './web/src/scss/*.scss',
        ])
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer({
            cascade: false
        }))
        .pipe(csso())
        .pipe(gulp.dest('./web/dist/css/'));
});

gulp.task('clean', function(){
    return del('./web/dist/**', {force: true});
});

gulp.task('copy', function() {
    return gulp.src([
        './web/src/favicon/**',
        './web/src/svg/**',
        './web/src/img/**',
        ], {base: './web/src'}).pipe(gulp.dest('./web/dist'));
});

gulp.task('styles:watch', function() {
    gulp.watch('./web/src/scss/*.scss', gulp.series(['styles']));
});

gulp.task('scripts:watch', function() {
    gulp.watch('./web/src/js/*.js', gulp.series(['scripts']));
});

gulp.task('scripts', gulp.series(['scripts-admin', 'scripts-general']));

gulp.task('watch', function() {
    gulp.watch('./web/src/**', gulp.series('default'));
});

gulp.task('default', gulp.series(['clean', 'copy', 'scripts', 'styles']));
