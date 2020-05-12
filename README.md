```bash
$ composer require hoalqq/commoncsv
```
```php
<?php

use Hoalqq\Commoncsv\Commoncsv;

// create a object
$filename = '会員種別.csv';
$header = ['会員種別', 'メールアドレス', 'モバイル会員番号'];
$data = [
    ['取引回','単価','金額'],
    ['取引回','単価','金額']
];
$test = new TestPackage($filename,$header,$data);

// add attribute to the object
$test->setShowHeader(false); // true is show header, false is not show, default is true
$test->downloadFile();// call function make output file
```
edit composer
```php
"require-dev": {
    "hoalqq/commoncsv": "dev-master"
}
```
===
run: composer update