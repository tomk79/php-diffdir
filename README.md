diffdir
=======

## Install

```
$ mkdir diffdir
$ cd diffdir
$ composer create-project tomk79/diffdir ./
```

## Usage

Basic usage.

```
$ php ./diffdir.php {$path_dirA} {$path_dirB}
```

Exsample of diff sample data.

```
$ php ./diffdir.php ./php/tests/sample_a/ ./php/tests/sample_b/
```


## Test

```
$ cd (project directory)
$ ./vendor/phpunit/phpunit/phpunit php/tests/diffdirTest
```

