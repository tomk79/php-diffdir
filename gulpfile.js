var gulp = require('gulp');
var sass = require('gulp-sass');//CSSコンパイラ
var autoprefixer = require("gulp-autoprefixer");//CSSにベンダープレフィックスを付与してくれる
var minifyCss = require('gulp-minify-css');//CSSファイルの圧縮ツール
var uglify = require("gulp-uglify");//JavaScriptファイルの圧縮ツール
var concat = require('gulp-concat');//ファイルの結合ツール
var plumber = require("gulp-plumber");//コンパイルエラーが起きても watch を抜けないようになる
var rename = require("gulp-rename");//ファイル名の置き換えを行う
var twig = require("gulp-twig");//Twigテンプレートエンジン
var browserify = require("gulp-browserify");//NodeJSのコードをブラウザ向けコードに変換
var packageJson = require(__dirname+'/package.json');
var _tasks = [
	'.js',
	'.css.scss'
];

// src 中の *.css.scss を処理
gulp.task('.css.scss', function(){
	gulp.src("src/**/*.css.scss")
		.pipe(plumber())
		.pipe(sass({
			"sourceComments": false
		}))
		.pipe(autoprefixer())
		.pipe(rename({
			extname: ''
		}))
		.pipe(rename({
			extname: '.css'
		}))
		.pipe(gulp.dest( './dist/' ))

		.pipe(minifyCss({compatibility: 'ie8'}))
		.pipe(rename({
			extname: '.min.css'
		}))
		.pipe(gulp.dest( './dist/' ))
	;
});

// src 中の *.js (frontend) を処理
gulp.task(".js", function() {
	gulp.src(["src/**/*.js"])
		.pipe(plumber())
		.pipe(browserify({}))
		.pipe(gulp.dest( './dist/' ))
		.pipe(rename({
			extname: '.min.js'
		}))
		.pipe(uglify())
		.pipe(gulp.dest( './dist/' ))
	;
});

// common.js (frontend) を処理
gulp.task("common.js", function() {
	gulp.src(["src/resources/common.js"])
		.pipe(plumber())
		.pipe(browserify({}))
		.pipe(concat('broccoli-preview-contents.js'))
		.pipe(gulp.dest( './client/dist/' ))
		.pipe(concat('broccoli-preview-contents.min.js'))
		.pipe(uglify())
		.pipe(gulp.dest( './client/dist/' ))
	;
});

// src 中のすべての拡張子を監視して処理
gulp.task("watch", function() {
	gulp.watch(["src/**/*"], _tasks);
});

// src 中のすべての拡張子を処理(default)
gulp.task("default", _tasks);
