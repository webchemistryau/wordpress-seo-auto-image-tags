/*
	INCLUDES ______________________________________________________________________
*/
const gulp = require('gulp');
const autoprefixer = require('gulp-autoprefixer');
const sass = require('gulp-sass');
const cleanCSS = require('gulp-clean-css');
const jshint = require('gulp-jshint');
const jshintstylish = require('jshint-stylish');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const rename = require('gulp-rename');
const htmlmin = require('gulp-htmlmin');
const imagemin = require('gulp-imagemin');
var gutil = require('gulp-util');
var conn,ftp_connect;function createFTPconnection(path){
	try{ 
		ftp_connect  = require(path);
		if(ftp_connect.remote_path[ftp_connect.remote_path.length-1]!='/') ftp_connect.remote_path+='/';
		const ftp = require( 'vinyl-ftp' );
		conn = ftp.create( ftp_connect );
	}catch(e){}}
function sendFTP(){	return (!conn||!ftp_connect)? gutil.noop():conn.dest( ftp_connect.remote_path );}

/*
	DIRECTORIES ______________________________________________________________________

	ALWAYS FINISH DIRECTORIES WITH SLASH '/'
*/

/*ftp_connect.json attributes: host, user, password, parallel, log, remote_path
Comment this line if FTP won't be used
*/
createFTPconnection('./ftp_connect.json');

var lib_dir = './node_modules/';
var src_dir = "./src/";
var build_dir = "./seo-auto-image-tags/";

var script_src = src_dir+'js/**/*.js';
var script_concat='main.js';
var script_build = build_dir+'js/';

var html_src = src_dir+'**/*.html';
var html_build = build_dir;
var php_src = src_dir+'**/*.php';

var lib_src = [
	// lib_dir+'path/to/folder/script.js',
];
var lib_css_src = [
	// lib_dir+'path/to/folder/style.css',
];
var lib_other_src = [ //fonts,etc
	// lib_dir+'path/to/folder/fonts/*.*',
];
var lib_build = build_dir+'lib/';

var scss_src = src_dir+'**/*.scss';

var img_src = src_dir+'img/**';
var img_screenshoots_src = [
	src_dir+'img/screenshot-1.jpg',
	src_dir+'img/screenshot-2.jpg',
];

var fonts_src = src_dir+'fonts/*';

var others_src = [
	src_dir+'license.txt',
	src_dir+'readme.txt',
];

/*
	TASKS ______________________________________________________________________
*/
var tasks = {
	once: [],
	watch: [],
};

/* JAVASCRIPT ____________________________________________________________________________*/
gulp.task('script_err', function() {
	gulp.src(script_src)
		.pipe(jshint())
		.pipe(jshint.reporter(jshintstylish));
});

gulp.task('script', function() {
	gulp.src(script_src)
		.pipe(concat(script_concat))
			.pipe(gulp.dest(script_build))
			.pipe(sendFTP())
		.pipe(rename({suffix:'.min'}))
		.pipe(uglify())
			.pipe(gulp.dest(script_build))
			.pipe(sendFTP())
	;
});
gulp.task('script_w', function(){gulp.watch(script_src,['script']);});
gulp.task('script_watch',['script','script_w']);
tasks.once.push('script');
tasks.watch.push('script_w');


/* JS LIBRARIES ____________________________________________________________________________*/
gulp.task('lib', function() {
	gulp.src(lib_src,{base:lib_dir})
		.pipe(uglify())
		.pipe(gulp.dest(lib_build))
		.pipe(sendFTP())

	gulp.src(lib_css_src,{base:lib_dir})
		.pipe(cleanCSS({compatibility: 'ie9'}))
		.pipe(gulp.dest(lib_build))
		.pipe(sendFTP())

	gulp.src(lib_other_src,{base:lib_dir})
		.pipe(gulp.dest(lib_build))
		.pipe(sendFTP())
});
tasks.once.push('lib');

/* PHP ____________________________________________________________________________*/

gulp.task('php',function() {
	gulp.src(php_src,{base:src_dir})
	// gulp.src(php_src,{base:src_dir,read:false})
		// .pipe(phpMinify({binary: 'C:/xampp/php/php.exe'}))
		// Upload to ftp
		.pipe(gulp.dest(build_dir))
		.pipe(sendFTP())
	;
});

gulp.task('php_w', function() { gulp.watch(php_src,['php']);});
gulp.task('php_watch',['php','php_w']);
tasks.once.push('php');
tasks.watch.push('php_w');

/* HTML ____________________________________________________________________________*/
gulp.task('html',function(){
	gulp.src(html_src,{base:src_dir})
		.pipe(htmlmin({collapseWhitespace: true}))
		.pipe(gulp.dest(build_dir))
		.pipe(sendFTP())
	;
});
gulp.task('html_w',function(){gulp.watch(html_src,['html']);});
gulp.task('html_watch',['html','html_w']);
tasks.once.push('html');
tasks.watch.push('html_w');

/* SCSS ____________________________________________________________________________*/
gulp.task('scss',function(){
	gulp.src(scss_src,{base:src_dir})
		.pipe(sass().on('error', sass.logError))
		.pipe(autoprefixer({
			browsers: ['last 3 versions','safari 5', 'ie 6', 'ie 7', 'ie 8', 'ie 9', 'opera 12.1', 'ios 6', 'android 4'],
			cascade: false
		}))
		.pipe(cleanCSS({compatibility: 'ie9'}))
		.pipe(gulp.dest(build_dir))
		.pipe(sendFTP())
	;
});
gulp.task('scss_w',function(){gulp.watch(scss_src,['scss']);});
gulp.task('scss_watch',['scss','scss_w']);
tasks.once.push('scss');
tasks.watch.push('scss_w');

/* IMAGES ____________________________________________________________________________*/

gulp.task('img', function(){

	gulp.src(img_src,{base:src_dir})
		// .pipe(imagemin())
		.pipe(gulp.dest(build_dir))
		.pipe(sendFTP())
	;
	
});
gulp.task('img_w', function(){gulp.watch(img_src,['img']);});
gulp.task('img_watch',['img','img_w']);
tasks.once.push('img');
// tasks.watch.push('image_min_w');

/* FONTS ____________________________________________________________________________*/
gulp.task('fonts', function() {
	gulp.src(fonts_src,{base:src_dir})
		.pipe(gulp.dest(build_dir))
		.pipe(sendFTP())
	;
});
gulp.task('fonts_w', function() { gulp.watch(fonts_src,['fonts']);});
gulp.task('fonts_watch',['fonts','fonts_w']);
tasks.once.push('fonts');
// tasks.watch.push('fonts_w');

/* OTHERS ____________________________________________________________________________*/
gulp.task('others', function() {
	gulp.src(others_src,{base:src_dir})
		.pipe(gulp.dest(build_dir))
		.pipe(sendFTP())
	;
});
gulp.task('others_w', function() { gulp.watch(others_src,['others']);});
gulp.task('others_watch',['others','others_w']);
tasks.once.push('others');
tasks.watch.push('others_w');

/* GENERAL ____________________________________________________________________________*/
gulp.task('once',tasks.once);
gulp.task('watch',tasks.watch);
gulp.task('default',['once','watch']);

/*
	VERSIONING & RELASES ______________________________________________________________________
*/

// var git = require('gulp-git');