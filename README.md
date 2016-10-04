# *atoum* [![Package version](https://img.shields.io/packagist/v/atoum/atoum.svg)](https://packagist.org/packages/atoum/atoum) [![Build Status](https://travis-ci.org/atoum/atoum.svg?branch=master)](https://travis-ci.org/atoum/atoum) [![Build status](https://ci.appveyor.com/api/projects/status/ir4vt58yxkt615i8/branch/master?svg=true)](https://ci.appveyor.com/project/jubianchi/atoum/branch/master)  [![Coverage Status](https://coveralls.io/repos/github/atoum/atoum/badge.svg?branch=master)](https://coveralls.io/github/atoum/atoum?branch=master) [![Gitter](https://badges.gitter.im/atoum/atoum.svg)](https://gitter.im/atoum/atoum?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

## A simple, modern and intuitive unit testing framework for PHP!

Just like SimpleTest or PHPUnit, *atoum* is a unit testing framework specific to the [PHP](http://www.php.net) language.  
However, it has been designed from the start with the following ideas in mind:

* Can be implemented *rapidly* ;
* *Simplify* test development ;
* Allow for writing *reliable, readable, and clear* unit tests ;

To accomplish that, it massively uses capabilities provided by *PHP*, to give the developer *a whole new way* of writing unit tests.  
Therefore, it can be installed and integrated inside an existing project extremely easily, since it is only a *single PHAR archive*, which is the one and only entry point for the developper.  
Also, thanks to its *fluent interface*, it allows for writing unit tests in a fashion close to natural language.  
It also makes it easier to implement stubbing within tests, thanks to intelligent uses of *anonymous functions and closures*.  
*atoum* natively, and by default, performs the execution of each unit test within a separate [PHP](http://www.php.net) process, to warrant *isolation*.  
Of course, it can be used seamlessly for continuous integration, and given its design, it can be made to cope with specific needs extremely easily.  
*atoum* also accomplishes all of this without affecting performance, since it has been developed to boast a reduced memory footprint while allowing for hastened test execution.  
It can also generate unit test execution reports in the Xunit format, which makes it compatible with continuous integration tools such as [Jenkins](http://jenkins-ci.org/).  
*atoum* also generates code coverage reports, in order to make it possible to supervise unit tests.  
Finally, even though it is developed mainly on UNIX, it can also work on Windows.  

## Why atoum?

* *atoum* is really [easy to install](http://docs.atoum.org/en/latest/installation.html): clone it [from github](https://github.com/atoum/atoum), download [its PHAR](https://github.com/atoum/atoum/releases/download/2.6.0/atoum.phar) or simply [use composer](https://packagist.org/packages/atoum/atoum),
* *atoum* provides a high level of security during test execution by isolating each test method in its own PHP process. Of course, this feature is available out of the box, no need to install any additional extension,
* *atoum* runs tests in a parallelized environment making the suite run as fast as possible by taking advantage of today's multi-core CPUs,
* *atoum* provides a [full-featured set of natural and expressive assertions](http://docs.atoum.org/en/latest/asserters.html) making tests as readable as possible. Here is an example:

``` php
<?php
$this
	->integer(150)
		->isGreaterThan(100)
		->isLowerThanOrEqualTo(200)
;
```

* *atoum* supports a BDD-like syntax with a lot of structural keywords:

``` php
<?php
$this
	->given($testedInstance = new testedClass())
	->and($testedClass[] = $firstValue = uniqid())
	->then
		->sizeof($testedInstance)->isEqualTo(1)
		->string($testedClass[0])->isEqualTo($firstValue)
;
```

* *atoum* provides a dead simple, yet very powerful, [mock engine](http://docs.atoum.org/en/latest/chapter2.html#mock):

``` php
<?php
$this
	->given($testedInstance = new testedClass())
	->and($aMock = new \mock\foo\bar()) // here a mock of the class \foo\bar is created dynamically
	->and($this->calling($aMock)->doOtherThing = true) // each call to doOtherThing() by the instance will return true
	->and($testedInstance->setDependency($aMock))
	->then
		->boolean($testedInstance->doSomething())->isTrue()
		->mock($aMock)
			->call('doOtherThing')->withArguments($testedInstance)->once() // Asserts that the doOtherThing() method of $aMock was called once
;
```

* *atoum* provides a clear API to assert on exceptions:

``` php
<?php
$this
	->given($testedInstance = new testedClass())
	->and($aMock = new \mock\foo\bar()) // here a mock of the class \foo\bar is created dynamically
	->and($this->calling($aMock)->doOtherThing->throw = $exception = new \exception()) // Call to doOtherThing() will throw an exception
	->and($testedInstance->setDependency($aMock))
	->then
		->exception(function() use ($testedInstance) { $testedInstance->doSomething(); })
			->isIdenticalTo($exception)
;
```

* *atoum* also lets you mock native PHP functions. Again, this is available out of the box:

``` php
<?php
$this
	->given($this->function->session_start = false)
	->and($session = new testedClass())
	->then
		->exception(function() use ($session) { $session->start(); })
			->isInstanceOf('project\namespace\exception')
			->hasMessage('Unable to start session')
		->function('session_start')->wasCalled()->once()
;
```

* *atoum* is able to produce several reports like TAP, clover, xUnit to be easily integrated with Jenkins or any other continuous integration tool,
* *atoum* supports [data providers](http://docs.atoum.org/en/latest/written_help.html#data-providers),
* *atoum* tests support autorun: just include the *atoum* runner and launch your test using `php path/to/test/file.php`,
* *atoum*'s [configuration file](http://docs.atoum.org/en/latest/chapter3.html#configuration-files) is exclusively written in PHP (no XML, YAML or any other format) giving you the best flexibility:

``` php
<?php
$script->addDefaultArguments('--test-it', '-ncc');

$runner->addTestsFromDirectory(__DIR__ . '/tests/units/classes');

$testGenerator = new mageekguy\atoum\test\generator();
$testGenerator
	->setTestClassesDirectory(__DIR__ . '/tests/units/classes');
	->setTestClassNamespace('mageekguy\atoum\tests\units');
	->setTestedClassesDirectory(__DIR__ . '/classes');
	->setTestedClassNamespace('mageekguy\atoum')
	->setRunnerPath(__DIR__ . '/scripts/runner.php')
;

$runner->setTestGenerator($testGenerator);
```

* *atoum* provides an automatic test template generator,
* *atoum* provides a [loop mode](http://docs.atoum.org/en/latest/chapter3.html#l-loop) to easily retrigger failed tests,
* *atoum* is full of other interesting features that you will discover over the time.

## Prerequisites to use *atoum*

*atoum* absolutely requires *PHP `>= 5.3.3`* or later to work.
On UNIX, in order to check whether you have the right PHP version, you just need to run the following command in your terminal:

	# php -v | grep -oE 'php 5\.3\.(?:[3-9]|[1-9][0-9])|5\.[4-6]\.[0-9]+|[5-7]\.[0-9]+\.[0-9]+'

If `PHP 5.6.x` or equivalent gets displayed, then you have the right PHP version installed.
Should you want to use *atoum* using its PHAR archive, you also need [PHP](http://www.php.net) to be able to access the `phar` module, which is normally available by default.  
On UNIX, in order to check whether you have this module or not, you just need to run the following command in your terminal:

	# php -m | grep -i phar

If `Phar` or equivalent gets displayed, then the module is properly installed.  
Generating reports in the Xunit format requires the `xml` module.  
On UNIX, in order to check whether you have this module or not, you just need to run the following command in your terminal:

	# php -m | grep -i xml

If `Xml` or equivalent gets displayed, then the module is properly installed.  
Should you wish to monitor the coverage rate of your code by the unit tests, the [Xdebug](http://xdebug.org/) 2.2 module will be required.  
On UNIX, in order to check whether you have this module or not, you just need to run the following command in your terminal:

	# php -v | grep -oi 'xdebug v2.2.[0-9]*'

If `Xdebug v2.2.x` or equivalent gets displayed, then the module is properly installed.

## A unit testing framework that can be made operational in 5 minutes!

### Step 1: Install *atoum*

You just have to download [its PHAR archive](https://github.com/atoum/atoum/releases/download/2.6.0/atoum.phar) and store it where you wish, for example under `/path/to/project/tests/atoum.phar`.  
This PHAR archive contains the latest development version to pass the totality of *atoum*'s unit tests.  
*atoum*'s source code is also available via [the github repository](https://github.com/atoum/atoum).  
To check if *atoum* works correctly with your configuration, you can execute all its unit tests.  
To do that, you just need to run the following command in your terminal:

	# php atoum.phar --test-it

### Step 2: Write your tests

Using your preferred text editor, create the file `path/to/project/tests/units/helloWorld.php` and add the following code:

``` php
<?php

namespace vendor\project\tests\units;

require_once 'path/to/atoum.phar';

include_once 'path/to/project/classes/helloWorld.php';

use \mageekguy\atoum;
use \vendor\project;

class helloWorld extends atoum\test
{
	public function testSay()
	{
		$helloWorld = new project\helloWorld();

		$this->string($helloWorld->say())->isEqualTo('Hello World!')
		;
	}
}
```

### Step 3: Run your test with the commandline

Launch your terminal and run the following command:

	# php path/to/test/file[enter]

You should get the following result, or something equivalent:

	> atoum version XXX by Frédéric Hardy.
	Error: Unattended exception: Tested class 'vendor\project\helloWorld' does not exist for test class 'vendor\project\tests\units\helloWorld'

### Step 4: Write the class corresponding to your test

Using again your preferred text editor, create the file `path/to/project/classes/helloWorld.php` and add the following code:

``` php
<?php

namespace vendor\project;

class helloWorld
{
	public function say()
	{
		return 'Hello World!';
	}
}
```

### Step 5: Run your test once more

In the same terminal, run the following command once again:

	# php path/to/test/file[enter]

You should get the following result, or something equivalent:

	> atoum version 288 by Frédéric Hardy.
	> Run vendor\project\tests\units\helloWorld...
	[S___________________________________________________________][1/1]
	=> Test duration: 0.00 second.
	=> Memory usage: 0.25 Mb.
	> Total test duration: 0.00 second.
	> Total test memory usage: 0.25 Mb.
	> Code coverage value: 100.00%
	> Running duration: 0.08 second.
	> Success (1 test, 1 method, 2 assertions, 0 error, 0 exception)!

### Step 6: Complete your tests and restart the cycle from Step 3

``` php
<?php

namespace vendor\project\tests\units;

require_once 'path/to/atoum.phar';

include_once 'path/to/project/classes/helloWorld.php';

use mageekguy\atoum;
use vendor\project;

class helloWorld extends atoum\test
{
	public function test__construct()
	{
		$helloWorld = new project\helloWorld();

		$this
			->string($helloWorld->say())->isEqualTo('Hello!')
			->string($helloWorld->say($name = 'Frédéric Hardy'))->isEqualTo('Hello ' . $name . '!')
		;
	}
}
```

## To go further

[*atoum*'s documentation](http://docs.atoum.org) is still being written.  
Any help to improve it will be appreciated.
However, if you want to further explore immediately *atoum*'s possibilities, we recommend:

* Running in your terminal, either the command `php atoum.phar -h`, or the command `php scripts/runner.php -h` ;
* Exploring the contents of the `configurations` directory in *atoum*'s source, as it contains configuration file samples ;
* Exploring the contents of the `tests/unit/classes` directory in *atoum*'s source, as it contains all of the unit tests ;
* Read the (french) [conference slides](http://www.slideshare.net/impossiblium/atoum-le-framework-de-tests-unitaires-pour-php-53-simple-moderne-et-intuitif) about it, available online ;
* Read the (french) [wiki](https://github.com/atoum/atoum/wiki) ;
* Join the IRC channel *##atoum* on the *freenode* network ;
* Ask questions by e-mail at the address *support[AT]atoum(DOT)org* ;

## Troubleshooting

### *atoum*'s PHAR archive seems to not be working

In this case, the first thing you will want to do is confirm whether you have the latest version of the archive.  
You just need to [download](https://github.com/atoum/atoum/releases/download/2.6.0/atoum.phar) it again.  
If it still doesn't work, run the following command in a terminal window:

	# php -n atoum.phar -v

If you get *atoum*'s version number, then the problem is coming from your PHP configuration.  
In most cases, the cause would be within extensions, that might be incompatible with the PHAR format, or that would prevent executing PHAR archives as a security measure.  
The `ioncube` extension for instance seems incompatible with PHAR archives, and you must therefore deactivate it if you are using it, by commenting the following line out of your `php.ini`, by prefixing it with the `;` character:

	zend_extension = /path/to/ioncube_loader*.*

The `suhosin` extension prevents executing PHAR archives, therefore its default configuration must be modified in order to be able to use *atoum*, by adding the following line in your `php.ini` file:

	suhosin.executor.include.whitelist="phar"

Finally, if running *atoum* causes the screen to display characters looking like `???%`, this would be because the `detect_unicode` directive inside your `php.ini` file is set to 1.  
To fix the problem, you just need to set it to 0 by editing your `php.ini` file or by running *atoum* with the following command:

	# php -d detect_unicode=0 atoum.phar [options]

If these three operations do not allow *atoum* to work, we suggest you send an e-mail to the address *support[AT]atoum(DOT)org*, describing in detail your configuration and your problem.  
You can also ask for help from the *atoum* development staff on the IRC channel *##atoum* on the *freenode* network.

### Error: Constant __COMPILER_HALT_OFFSET__ already defined /path/to/atoum.phar

This error comes from the fact the *atoum* PHAR archive is included in more than one place within your code using `include` or `require`.  
To fix this problem, you just need to include the archive by using only `include_once` or `require_once`, in order to ensure it is not included several times.

### APC seems not work with *atoum*
[APC](http://fr.php.net/manual/en/apc.configuration.php)  is a free, open, and robust framework for caching and optimizing PHP intermediate code distributed under the form of a PHP extension.  
When testing classes that use APC, you may get some failure message showing that `apc_fetch` function is unable to retrieve a value.  
As all PHP extension, APC has some configuration options to enable it:

* `apc.enabled` whether to enable or disable APC ;
* `apc.enable_cli`, whether to enable or disable APC for PHP CLI ;

In order to use [APC](http://fr.php.net/manual/en/apc.configuration.php) with *atoum*, you have to set `apc.enabled` and `apc.enable_cli` to `1`, otherwise, it won't be enabled for the PHP CLI version, which is used by *atoum*.

### Getting segfault when mocking objects
When using *atoum* and mocking objects, you will sometime get segfaults coming from [PHP](http://www.php.net).  
These segfaults are caused by [XDebug](http://xdebug.org/) in version less than 2.1.0 which has problem handling reflection in some cases.  
To check the current version of [XDebug](http://xdebug.org/), you can run `php -v`.  
To fix this issue, you have to update [XDebug](http://xdebug.org/) to the latest [stable version](http://xdebug.org/download.php).  
If you can't update [XDebug](http://xdebug.org/) on your system, you can still disable the extension to avoid getting segfaults.  
To be sure that [XDebug](http://xdebug.org/) has been succefully updated or disabled, you can run `php -v`.  
When you are done updating or disabling [XDebug](http://xdebug.org/), run `php atoum.phar --test-it` to be sure that all the segfaults have gone and that *atoum* is working.

---

## Roadmap

Looking for a roadmap?

- [Here is](https://github.com/atoum/atoum/labels/In%20progress) the work in progress,
- [And there](https://github.com/atoum/atoum/milestones/*) what will come in next releases.

## Credits

atoum has been created by [Frédéric Hardy]. It is now led by a strong community of contributors. You can find them 
in the [committer list](https://github.com/atoum/atoum/graphs/contributors) or in the 
[Contributors team](https://github.com/orgs/atoum/teams/contributors).

We also would like to thank people who participated in the project in any way:

* Mathieu Robin ([@MathRobin])
* Geoffrey Bachelet ([@ubermuda])
* Nathanaël Louison
* Simon Jodet ([@simonjodet])

## License

atoum is released under the BSD-3-Clause License. See the bundled [LICENSE](LICENSE) file for details.

[Frédéric Hardy]: https://github.com/mageekguy
[@MathRobin]: https://github.com/MathRobin
[@ubermuda]: https://github.com/ubermuda
[@simonjodet]: https://github.com/simonjodet