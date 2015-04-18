Calculator
==========

Library for the calculation of simple math expressions without using `eval()`.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
$ composer require zhuravljov/calc
```

or add

```
"zhuravljov/calc": "*"
```

to the `require` section of your `composer.json` file.

Usage
-----

```php
$calculator = new \zhuravljov\calc\Calculator();
$calculator->calc('2 + 2 * 2'); // 6
$calculator->calc('(2 + 2) * 2'); // 8
$calculator->calc('(2 + 2'); // CalcException

```

Available operations `+`, `-`, `*` and `/`.
