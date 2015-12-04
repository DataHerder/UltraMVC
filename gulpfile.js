var gulp = require('gulp'),
		sass = require('gulp-sass'),
		autoprefixer = require('gulp-autoprefixer'),
		minifycss = require('gulp-minify-css'),
		rename = require('gulp-rename'),
		gutil = require('gulp-util'),
		jsmin = require('gulp-jsmin'),
		uglify = require('gulp-uglify'),
		fs = require('fs')
;

var ultra = JSON.parse(fs.readFileSync('ultramvc.json'));

gulp.task('styles', function() {
	gutil.log('saving default styles');
	gulp.src(ultra['styles']).pipe(sass('sass', {style:'expanded'}))
			.on('error', sass.logError)
			.pipe(gulp.dest('./resources/css'))
			.pipe(minifycss())
			.pipe(rename({suffix:'.min'}))
			.pipe(gulp.dest('./resources/css'))

});


gulp.task('script', function() {
	gulp.src(ultra['script'])
			.pipe(uglify())
			.pipe(jsmin())
			.pipe(rename({suffix: '.min'}))
			.pipe(gulp.dest('dist'))
	;
});


gulp.task('watch', function() {
	for (var j in ultra['watch']) {
		gulp.watch(ultra['watch'][j], [j]);
	}
});


gulp.task('default', ['watch']);
