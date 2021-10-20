var gulp       = require('gulp'),
		concat     = require('gulp-concat'),
		cssnano    = require('gulp-cssnano'),
		uglify     = require('gulp-uglify'),
		sass       = require('gulp-sass');

var paths = {
	scss: ['src/scss/**/*.scss'],
	js: [
		'src/js/awlert.module.js',
		'src/js/awlert.constants.js',
		'src/js/awlert.directive.js',
		'src/js/awlert.service.js',

	]
};

gulp.task('js', function(){
	return  gulp.src(paths.js)
					.pipe(concat('awlert.min.js'))
					.pipe(uglify())
					.pipe(gulp.dest(('demo/www/lib/awlert/js')));
});

gulp.task('jsProd', function(){
	return  gulp.src(paths.js)
					.pipe(concat('awlert.min.js'))
					.pipe(uglify())
					.pipe(gulp.dest(('dist/js')));
});

gulp.task('sass', function(){
	return  gulp.src(paths.scss)
					.pipe(sass().on('error', sass.logError))
					.pipe(concat('awlert.css'))
					.pipe(cssnano({zindex: false}))
					.pipe(gulp.dest('demo/www/lib/awlert/css'));
});

gulp.task('sassProd', function(){
	return  gulp.src(paths.scss)
					.pipe(sass().on('error', sass.logError))
					.pipe(concat('awlert.css'))
					.pipe(cssnano({zindex: false}))
					.pipe(gulp.dest('dist/css'));
});


gulp.task('watch', function(){
	gulp.watch(paths.scss, ['sass']);
	gulp.watch(paths.js, ['js']);
});

gulp.task('build', ['jsProd', 'sassProd'], function(){
	console.log('Good Luck Fella! S2');
});
