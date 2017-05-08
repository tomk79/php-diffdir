diffdir
=======

diffdir は、２つのディレクトリを比較し、差分のあったファイルを抽出します。

比較した内容は1ファイル1行のCSVファイルに記録され、ファイルごとの差分を確認できるHTMLファイルを出力します。




## インストール手順 - Install

diffdir のインストールには `composer` を使用します。

```
$ cd {$yourDirectory}
$ composer create-project tomk79/diffdir ./
```

## 使い方 - Usage

### 基本的な使い方 - Basic usage.

```
$ php ./diffdir.php {$path_dirA} {$path_dirB}
```

### サンプルデータを比較する例 - Example: diff of sample data.

```
$ php ./diffdir.php ./tests/sample_a/ ./tests/sample_b/
```

### オプション

#### -o 結果の出力先ディレクトリを指定する

`-o` オプションをつけて、出力先ディレクトリを指定します。

```
$ php ./diffdir.php -o ./result_sample/ ./tests/sample_a/ ./tests/sample_b/
```

#### --readme READMEページを指定する

`--readme` オプションは、HTMLレポートにREADMEページを追加します。

```
$ php ./diffdir.php -o ./result_sample/ --readme ./path/to/README.md ./tests/sample_a/ ./tests/sample_b/
```

#### --strip-crlf 改行コードを無視する

`--strip-crlf` オプションをつけて比較すると、改行コードだけの違いは無視されます。

```
$ php ./diffdir.php --strip-crlf ./tests/sample_a/ ./tests/sample_b/
```

#### -v 詳細なメッセージを出力する

`-v` オプションをつけて比較すると、ターミナル上に処理の詳細が表示されます。

```
$ php ./diffdir.php -v ./tests/sample_a/ ./tests/sample_b/
```


#### -q メッセージを表示しない

`-q` オプションをつけて比較すると、ターミナル上の表示の一切が隠されます。

```
$ php ./diffdir.php -q ./tests/sample_a/ ./tests/sample_b/
```


### PHPスクリプト内で使用する

```php
<?php
require_once( './vendor/autoload.php' );
$diffdir = new tomk79\diffdir(
	'/path/before/', // path before
	'/path/after/',  // path after
	array( // options
		'output'=>'/path/to/path_output_dir/', // -o
		'readme'=>'/path/to/README.md', // --readme
		'strip_crlf'=>true, // --strip-crlf
		'verbose'=>true, // -v
	)
);
if( $diffdir->is_error() ){
	print 'ERROR.'."\n";
	var_dump( $diffdir->get_errors() );
}else{
	print 'success.'."\n";
	print ''."\n";
	print 'see: '.$diffdir->get_output_dir()."\n";
}

```


## ライセンス - License

MIT License


## 作者 - Author

- (C)Tomoya Koyanagi <tomk79@gmail.com>
- website: <http://www.pxt.jp/>
- Twitter: @tomk79 <http://twitter.com/tomk79/>
