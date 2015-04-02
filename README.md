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

### サンプルデータを比較する例 - Exsample of diff sample data.

```
$ php ./diffdir.php ./php/tests/sample_a/ ./php/tests/sample_b/
```

### オプション

#### -o 結果の出力先ディレクトリを指定する

`-o` オプションをつけて、出力先ディレクトリを指定します。

```
$ php ./diffdir.php -o ./result_sample/ ./php/tests/sample_a/ ./php/tests/sample_b/
```

#### --strip-crlf 改行コードを無視する

`--strip-crlf` オプションをつけて比較すると、改行コードだけの違いは無視されます。

```
$ php ./diffdir.php --strip-crlf ./php/tests/sample_a/ ./php/tests/sample_b/
```

#### -v 詳細なメッセージを出力する

`-v` オプションをつけて比較すると、ターミナル上に処理の詳細が表示されます。

```
$ php ./diffdir.php -v ./php/tests/sample_a/ ./php/tests/sample_b/
```


### PHPスクリプト内で使用する

```
<?php
require_once( './vendor/autoload.php' );
$diffdir = new tomk79\diffdir(
	'/path/before/', // path before
	'/path/after/',  // path after
	array( // options
		'output'=>'/path/path_output_dir/', // -o
		'strip_crlf'=>true, // --strip-crlf
		'verbose'=>true // -v
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



## 付録 - Appendix

### composer のインストール

`composer` のインストール方法について
詳しくは [composerの公式サイト(英語)](https://getcomposer.org/doc/00-intro.md) を参照してください。

下記は公式サイトからの抜粋です。参考までに。

#### Macの方

Mac の方は、次のコマンドでグローバルインストールできます。

```
$ curl -sS https://getcomposer.org/installer | php
$ mv composer.phar /usr/local/bin/composer
```

#### Windowsの方

Windows の方は、GUIインストーラ Composer-Setup.exe が用意されています。
次のコマンドでもインストールできますので、お好みの方法でインストールしてください。

```
$ cd C:\bin
$ php -r "readfile('https://getcomposer.org/installer');" | php
```

### 開発者向け情報 - for Developer

#### テスト - Test

```
$ cd (project directory)
$ ./vendor/phpunit/phpunit/phpunit php/tests/diffdirTest
```

