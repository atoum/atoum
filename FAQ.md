# Frequently Asked questions

## How to write "atoum"? "ATOUM"? "Atoum"?

The official name is "atoum".

## How to contribute to atoum?
Just send an email to team@atoum.org to say that you want to contribute to atoum.
Moreover, you should read the `CONTRIBUTING.md` file about coding convention.

## Why some classes has name `*\phpClass` instead of `*\class`?

The word `class` is [reserved by PHP](http://www.php.net/manual/en/reserved.keywords.php), so it's not possible to use this name for a class.
In this case, the atoum convention is to prefix name with word `php`.

## Where is the documentation?
There is an [english](http://docs.atoum.org/) and a [french documentation](http://docs.atoum.org/fr/latest/) available.
As a documentation is a continual work in progress, you're welcome to help us to [improve it](https://github.com/atoum/atoum-documentation).
Moreover, atoum's unit test is the documentation.
You find them in the `tests/` directory.

## Why `php atoum.phar` does not works?
Here are some known issues :

* Try a `php -n atoum.phar` in a terminal.
    * If it works, the problem is in your PHP configuration.
* Try to remove `ioncube` extension, which seems not compatible with atoum.
* If you use suhosin, you can also add `suhosin.executor.include.whitelist="phar"` to your `php.ini`.
* Try to add `detect_unicode=0` in your php.ini.

## What can i do to avoid error about `__COMPILER_HALT_OFFSET__`?

Use only require_once to include `atoum.phar` in your scripts.

## Why I get a fail message when testing a class that uses APC?
[APC](http://php.net/apc.configuration) is "a free, open, and robust framework for caching and optimizing PHP intermediate code" distributed under the form of a PHP extension.
When testing classes that use APC, you may get some failure messages showing that apc_fetch is unable to retrieve a value.
As all PHP extension, APC has some configuration options to enable it:

* `apc.enabled`, whether to enable or disable APC,
* `apc.enable_cli`, whether to enable or disable APC for PHP CLI.

Setting `apc.enabled` to 1 in your CLI configuration does not do the trick: to avoid these failure messages, you have to set the `apc.enable_cli` option to 1, otherwise, the extension won't be enabled for the PHP CLI version, which is used by atoum.

## Why ABOUT does not use markdown syntax?
This file is used by PHAR to display useful information to the user in CLI, so using markdown in it is a bad idea.
