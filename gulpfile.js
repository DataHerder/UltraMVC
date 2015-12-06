var gulp = require('gulp'),
		sass = require('gulp-sass'),
		autoprefixer = require('gulp-autoprefixer'),
		minifycss = require('gulp-minify-css'),
		rename = require('gulp-rename'),
		gutil = require('gulp-util'),
		jsmin = require('gulp-jsmin'),
		uglify = require('gulp-uglify'),
		concat = require('gulp-concat'),
		fs = require('fs')
		;

var ultra = JSON.parse(fs.readFileSync('ultramvc.json'));

watch_list = [];
(function() {
	for (var i = 0; i < ultra['watch']['sass'].length; i++) {
		var style_data = ultra['watch']['sass'][i];
		var read = style_data[0];
		var write = style_data[1];
		var watch = style_data[2];
		var concat_styles = style_data[3];
		watch_list.push(['sass' + i, watch]);
		if (concat_styles) {
			(function(read, write, concat_styles) {
				gulp.task('sass' + i, function () {
					gulp.src(read)
							.pipe(concat(concat_styles))
							.pipe(sass('sass', {style: 'expanded'}))
							.on('error', sass.logError)
							.pipe(gulp.dest(write))
							.pipe(minifycss())
							.pipe(rename({suffix: '.min'}))
							.pipe(gulp.dest(write))
					;
				});
			}(read, write, concat_styles));
		} else {
			(function(read, write) {
				gulp.task('sass' + i, function() {
					gulp.src(read)
							.pipe(sass('sass', {style: 'expanded'}))
							.on('error', sass.logError)
							.pipe(gulp.dest(write))
							.pipe(minifycss())
							.pipe(rename({suffix:'.min'}))
							.pipe(gulp.dest(write))
					;
				});
			})(read, write);
		}
	}

	for (i = 0; i < ultra['watch']['scripts'].length; i++) {
		var script_data = ultra['watch']['scripts'][i];
		read = script_data[0];
		write = script_data[1];
		watch = script_data[2];
		var concat_scripts = script_data[3];

		watch_list.push(['scripts' + i, watch]);
		if (concat_scripts) {
			(function(read, write, concat_scripts) {
				gulp.task('scripts' + i, function() {
					gulp.src(read)
							.pipe(concat(concat_scripts))
							.pipe(gulp.dest(write))
							.pipe(uglify())
							.pipe(jsmin())
							.pipe(rename({suffix: '.min'}))
							.pipe(gulp.dest(write))
					;
				});
			})(read, write, concat_scripts)
		} else {
			(function(read, write) {
				gulp.task('scripts' + i, function() {
					gulp.src(read)
							.pipe(uglify())
							.pipe(jsmin())
							.pipe(rename({suffix: '.min'}))
							.pipe(gulp.dest(write))
					;
				});
			})(read, write);
		}
	}
})();

gulp.task('init', function() {
	for (i = 0; i < watch_list.length; i++) {
		console.log(watch_list[i]);
		gulp.watch(watch_list[i][1], [watch_list[i][0]]);
	}
});

// add your personal tasks here after 'init'
gulp.task('default', ['init']);
