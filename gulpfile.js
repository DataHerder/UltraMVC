var gulp = require('gulp'),
	sass = require('gulp-sass'),
	autoprefixer = require('gulp-autoprefixer'),
	minifycss = require('gulp-minify-css'),
	rename = require('gulp-rename'),
	gutil = require('gulp-util');

gulp.task('styles', function() {
	gutil.log('Saving Default Styles');
	gulp.src('./resources/sass/app.scss')
			.pipe(sass('sass', {style:'expanded'}))
			.on('error', sass.logError)
			.pipe(gulp.dest('./resources/css'))
			.pipe(minifycss())
			.pipe(rename({suffix:'.min'}))
			.pipe(gulp.dest('./resources/css'))
});

gulp.task('watch', function() {
	gulp.watch('./resources/sass/*.scss', ['styles']);
	gulp.watch('./resources/sass/**/*.scss', ['styles']);
});

gulp.task('default', ['watch']);
