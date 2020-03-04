# `dev-master`

* [#841](https://github.com/atoum/atoum/pull/841) Fix variadic support when you use all is interface in your mock ([@Grummfy])

# 3.4.1 - 2020-01-22

* [#840](https://github.com/atoum/atoum/pull/840) Remove unnecessary imports ([@Hywan])
* [#835](https://github.com/atoum/atoum/pull/835) Fix deprecated, unparenthesized (PHP 7.4) ([@trasher])
* [#830](https://github.com/atoum/atoum/pull/830) Add 7.4 to Travis ([@remicollet])
* [#833](https://github.com/atoum/atoum/pull/833) Patch CLI output ([@Grummfy])
* [#829](https://github.com/atoum/atoum/pull/829) fix appvoyer build ([@cedric-anne])
* [#827](https://github.com/atoum/atoum/pull/827) Fix “`ReflectionType::__toString()` is deprecated” ([@cedric-anne])
* [#777](https://github.com/atoum/atoum/pull/777) cli output changed for displaying errors and exceptions clearly ([@macintoshplus])

# 3.3.0 - 2018-03-15

* [#771](https://github.com/atoum/atoum/pull/771) Normalize and simplify the asserter name when a test case fails ([@hywan])
* [#754](https://github.com/atoum/atoum/pull/754) Add the dot report ([@jubianchi])
* [#769](https://github.com/atoum/atoum/pull/769) CLI: Align options to the left, and increase contrast ([@hywan])
* [#757](https://github.com/atoum/atoum/pull/757) Take the error reporting level into account to exit the runner ([@hywan])
* [#752](https://github.com/atoum/atoum/pull/752) Add an os annotation to only run tests on specific OS ([@jubianchi])
* [#585](https://github.com/atoum/atoum/pull/585) Memory usage is based on the peak & real allocations ([@hywan])
* [#740](https://github.com/atoum/atoum/pull/740) String asserter now has `notMatches` assertion ([@fvilpoix])

## Bugfix

* [#756](https://github.com/atoum/atoum/pull/756) Configuration, autoloader and bootstrap files are correctly loaded when using the PHAR ([@jubianchi])
* [#755](https://github.com/atoum/atoum/pull/755) String asserter's failure messages are clear ([@jubianchi])
* [#773](https://github.com/atoum/atoum/pull/773) Directory is the current working directory ([@hywan])
* [#770](https://github.com/atoum/atoum/pull/770) Fix path to the runner for the help ([@hywan])
* [#768](https://github.com/atoum/atoum/pull/768) Fix typos in the CLI help ([@hywan])
* [#767](https://github.com/atoum/atoum/pull/767) Fix typos in the exception messages ([@hywan])

# 3.2.0 - 2017-09-07

* [#739](https://github.com/atoum/atoum/pull/739) Avoid many memory allocations in error report field ([@hywan])
* [#736](https://github.com/atoum/atoum/pull/736) Display clear errors when mocking function fails ([@jubianchi])
* [#737](https://github.com/atoum/atoum/pull/737) Command line switches override configuration file directives ([@jubianchi])
* [#733](https://github.com/atoum/atoum/pull/733) Uncompleted methods make atoum exit with an error code ([@jubianchi])
* [#734](https://github.com/atoum/atoum/pull/734) The `exception::isInstanceOf` asserter correctly works with interfaces ([@jubianchi])
* [#731](https://github.com/atoum/atoum/pull/731) Remove dependency on `ext-session` ([@jubianchi], [@hywan])

## Bugfix

* [#746](https://github.com/atoum/atoum/pull/746) CLI commands are correctly escaped ([@agallou], [@jubianchi])

# 3.1.1 - 2017-07-19

## Bugfix

* [#727](https://github.com/atoum/atoum/pull/727) Add alias on `phpObject` to restore compatibility ([@grummfy])

# 3.1.0 - 2017-07-11

* [#726](https://github.com/atoum/atoum/pull/726) Remove an autoloader cache warning ([@hywan])
* [#719](https://github.com/atoum/atoum/pull/719) Add nullable type support in the mock engine ([@grummfy])
* [#723](https://github.com/atoum/atoum/pull/724) `object` is a reserved keyword as of PHP 7.2 ([@trasher])
* [#713](https://github.com/atoum/atoum/pull/713) Results are folded on Travis CI ([@jubianchi])
* [#709](https://github.com/atoum/atoum/pull/709) Exception asserter now has `isInstanceOf` without parenthesis ([@guiled])
* [#705](https://github.com/atoum/atoum/pull/705) Stream asserter now has `isRead` and `isWritten` assertion (without brackets) ([@guiled])
* [#701](https://github.com/atoum/atoum/pull/701) Mock generator supports `strict_types` ([@jubianchi])

## Bugfix

* [#701](https://github.com/atoum/atoum/pull/701) Mock generator correctly handles `void` return type ([@jubianchi])

# 3.0.0 - 2017-02-22

* [#664](https://github.com/atoum/atoum/pull/664) New asserter: `generator` ([@agallou])
* [#694](https://github.com/atoum/atoum/pull/694) The VIM plugin has been moved to atoum/vim-plugin ([@jubianchi])
* [#615](https://github.com/atoum/atoum/pull/615) Remove reserved keyword, replace void by blank ([@vonglasow])
* [#643](https://github.com/atoum/atoum/pull/643) atoum now requires PHP `>=5.6.0` ([@jubianchi])

## Bugfix

* [#691](https://github.com/atoum/atoum/pull/691) Fix how annotations are extracted. Only those actually starting with `@` are handled ([@jubianchi])
* [#688](https://github.com/atoum/atoum/pull/688) Avoid reporting incorrect atoum path ([@hywan])

# 2.9.0 - 2017-02-11

* [#667](https://github.com/atoum/atoum/pull/667) Assert on array values using `mageekguy\atoum\asserters\phpArray::$values` ([@krtek4])
* [#682](https://github.com/atoum/atoum/pull/682) Do not call parent class when mocking as interface ([@mageekguy])
* [#679](https://github.com/atoum/atoum/pull/679) Copy `PHP_IDE_CONFIG` into forked processes ([@mvrhov])
* [#678](https://github.com/atoum/atoum/pull/678) Each mock instance can be made unique by calling `eachInstanceIsUnique` on the mock generator ([@mageekguy])

# 2.9.0-beta1 - 2016-10-08

* [#604](https://github.com/atoum/atoum/pull/604) Add a `addConfigurationCallable` method on the runner to allow extensions to register themselves ([@agallou], [@jubianchi])
* [#634](https://github.com/atoum/atoum/pull/634) Only one extension of a kind can be loaded. Extensions can be unloaded ([@agallou], [@jubianchi])
* [#619](https://github.com/atoum/atoum/pull/619) Add branches and paths coverage support to AtoumTask for Phing ([@oxman])

## Bugfix

* [#633](https://github.com/atoum/atoum/pull/633) Mock generator correctly handles the `self` return type ([@jubianchi])
* [#637](https://github.com/atoum/atoum/pull/637) Errors are displayed in the TAP report ([@jubianchi])

# 2.8.2 - 2016-08-12

* [#620](https://github.com/atoum/atoum/pull/620) Add HTML coverage report from [reports extension](https://github.com/atoum/reports-extension) to AtoumTask for Phing ([@oxman])
* [#612](https://github.com/atoum/atoum/pull/612) Add telemetry support to AtoumTask ([@oxman])

# 2.8.1 - 2016-07-01

* [#611](https://github.com/atoum/atoum/pull/611) Exclude vendor and composer.lock from phar ([@jubianchi], [@agallou])

# 2.8.0 - 2016-07-01

* [#605](https://github.com/atoum/atoum/pull/605) Automatically include Composer's autoloader if it exists ([@jubianchi], [@agallou])
* [#605](https://github.com/atoum/atoum/pull/605) Handle `.autoloader.atoum.php` files to define tests autoloader ([@jubianchi])
* [#605](https://github.com/atoum/atoum/pull/605) Add the `--autoloader-file`/`-af` CLI argument to define which autoloader file to user ([@jubianchi])
* [#596](https://github.com/atoum/atoum/pull/596) Test methods' tags are inherited from test classes ([@jubianchi])

# 2.7.0 - 2016-06-20

* [#594](https://github.com/atoum/atoum/pull/594) Add telemtry report to CI builds ([@jubianchi])

## Bugfix

* [#600](https://github.com/atoum/atoum/pull/600) Reports override correctly when using -ulr/-utr ([@jubianchi])
* [#593](https://github.com/atoum/atoum/pull/593) Assertions on PHP 7 exceptions/throwables/errors are now working correctly ([@jubianchi])

# 2.6.1 - 2016-04-08

* [#590](https://github.com/atoum/atoum/pull/590) The `dateTime` asserter now fully supports `\dateTimeImmutable` ([@fferriere])

# 2.6.0 - 2016-03-08

* [#569](https://github.com/atoum/atoum/pull/569) Use in-memory cache for resolved asserters ([@jubianchi])
* [#567](https://github.com/atoum/atoum/pull/567) Extract loop logic from runner and add a looper interface ([@jubianchi], [@agallou])

## Bugfix

* [#583](https://github.com/atoum/atoum/pull/578) Fix asserting on zeroes with the phpFloat asserter ([@jubianchi])
* [#581](https://github.com/atoum/atoum/pull/578) Fix how arguments are passed when using loop mode ([@jubianchi])
* [#578](https://github.com/atoum/atoum/pull/578) Fix arguments priority parsing when they have no priority ([@agallou])

# 2.5.2 - 2016-01-28

* [#561](https://github.com/atoum/atoum/pull/561) Use the fully qualified name when the return type is not `builtin` ([@GuillaumeDievart])

# 2.5.1 - 2016-01-18

* [#556](https://github.com/atoum/atoum/pull/556) The autoloader now handles traits ([@jubianchi])

# 2.5.0 - 2016-01-08

* [#539](https://github.com/atoum/atoum/pull/539) Add a `newMockInstance` helper method on test class ([@Grummfy])
* [#548](https://github.com/atoum/atoum/pull/548) The `dateTime` asserter now supports `\dateTimeImmutable` ([@jubianchi])
* [#540](https://github.com/atoum/atoum/pull/540) Assert on child arrays using the `phpArray` asserter ([@jubianchi])
* [#541](https://github.com/atoum/atoum/pull/541) New `toArray` (along with `toArray` method on `phpString` and `object` asserters) and `iterator` asserters ([@jubianchi])
* [#535](https://github.com/atoum/atoum/pull/535) New `resource` asserter group (with `isOfType` or `is*` wildcard like `isStream`) ([@hywan])
* [#529](https://github.com/atoum/atoum/pull/529) Allow extensions to define configuration ([@jubianchi])
* [#496](https://github.com/atoum/atoum/pull/496) Mock generator supports variadic arguments passed by reference ([@jubianchi])
* [#496](https://github.com/atoum/atoum/pull/496) Auto generate and inject mocks in test methods ([@jubianchi])

## Bugfix

* [#350](https://github.com/atoum/atoum/pull/350) PHAR can be built on Windows ([@kao98])
* [#530](https://github.com/atoum/atoum/pull/530) Extracted mocked method signature generation to make it work with visibility extension ([@jubianchi])
* [#537](https://github.com/atoum/atoum/pull/537) `exception` asserter handles PHP 7 throwables ([@jubianchi])

# 2.4.0 - 2015-12-04

* [#520](https://github.com/atoum/atoum/pull/520) Introduce the constant mocker ([@hywan])
* [#518](https://github.com/atoum/atoum/pull/518) Update atoum's PHAR against Github releases with `--github-update` ([@jubianchi])
* [#515](https://github.com/atoum/atoum/pull/515) Fix PHP7 support in the basic resolver ([@hywan])
* [#516](https://github.com/atoum/atoum/pull/516) Add a "callStaticOnTestedClass" method ([@mikaelrandy])
* [#530](https://github.com/atoum/atoum/pull/530) Reset PHP7 configuration for tests ([@jubianchi])

## Bugfix

* [#526](https://github.com/atoum/atoum/pull/526) Mock asserter is not case sensitive ([@mageekguy])


# 2.3.0 - 2015-10-22

* [#501](https://github.com/atoum/atoum/pull/501) Add atoum path and version to default CLI report ([@jubianchi])
* [#502](https://github.com/atoum/atoum/pull/502) Improve `setTestNamespace` parameters validation ([@remicollet])

## Bugfix

* [f28a6ee](https://github.com/atoum/atoum/commit/f28a6eeb6de80ccea3619e228b7a16ddd03637fc) "DOMElement::setIdAttribute(): ID otherMethod already defined" error ([@jubianchi])

# 2.2.2 - 2015-09-17

* [#497](https://github.com/atoum/atoum/pull/497) Fix fail message forwarding from `match` to `matches` in string asserter ([@vonglasow])
* [#477](https://github.com/atoum/atoum/pull/477) Fix exit code when there is something wrong in the configuration file ([@jubianchi])

# 2.2.1 - 2015-08-27

* [#491](https://github.com/atoum/atoum/pull/491) Fix `getTestMethodPrefix` when the prefix is `"0"` ([@remicollet])
* [#384](https://github.com/atoum/atoum/pull/384) Short syntax for base assertions ([@jubianchi])

# 2.2.0 - 2015-07-31

* [#467](https://github.com/atoum/atoum/pull/467) Hide classes and methods coverage details in CLI report ([@jubianchi])
* [#474](https://github.com/atoum/atoum/pull/474) Add the method return type and parameter type in the mock generator ([@guillaumeDievart])
* [#470](https://github.com/atoum/atoum/pull/470) Add `isNotEmpty` asserter on `array` ([@metalaka])
* [#476](https://github.com/atoum/atoum/pull/476) Add relative url root choice for code coverage report ([@n-couet])

# 2.1.0 - 2015-05-08

* [#459](https://github.com/atoum/atoum/issues/459) Support branches and paths coverage with [Xdebug](http://xdebug.org/) 2.3 ([@jubianchi])
* [#436](https://github.com/atoum/atoum/issues/436) Support old-style constructors in mocks ([@jubianchi])
* [#453](https://github.com/atoum/atoum/issues/453) `phpClass` asserter will throw atoum's logic exceptions instead of native reflection exceptions ([@jubianchi])
* [#340](https://github.com/atoum/atoum/issues/340) Fixed an error when using `DebugClassLoader` autoloader and [atoum-bundle](https://github.com/atoum/AtoumBundle) ([@jubianchi])
* [#454](https://github.com/atoum/atoum/pull/454) Rename asserters classes for PHP7 ([@jubianchi])
* [#457](https://github.com/atoum/atoum/pull/457) Removed usage of die in deprecated methods ([@jubianchi])
* [#442](https://github.com/atoum/atoum/issues/442) [#444](https://github.com/atoum/atoum/pull/444) Properly report skipped method due to a missing extension ([@jubianchi])
* [#441](https://github.com/atoum/atoum/pull/441) Add PHP 7.0 in the build matrix ([@jubianchi])
* [#399](https://github.com/atoum/atoum/pull/399) Add the `let` assertion handler ([@hywan])
* [#443](https://github.com/atoum/atoum/pull/443) Autoloader should resolve classes step by step ([@jubianchi])

# 2.0.1 - 2015-02-27

* [#440](https://github.com/atoum/atoum/pull/440) `--configurations` option should be handled first ([@jubianchi])
* [#439](https://github.com/atoum/atoum/pull/439) Since atoum is 2.*, branch-alias must follow ([@hywan])
* [#437](https://github.com/atoum/atoum/pull/437) Autoloader should not try to resolve alias if requested class exists ([@jubianchi])
* Generalize method call checking in mock ([@mageekguy])
* [#435](https://github.com/atoum/atoum/pull/435) Partially revert BC break introduced in [#420](https://github.com/atoum/atoum/pull/420) ([@mageekguy])

# 2.0.0 - 2015-02-13

## BC break updates

* [#420](https://github.com/atoum/atoum/pull/420) `atoum\test::beforeTestMethod` is called before the tested class is loaded ([@mageekguy])

## Other updates
* [#431](https://github.com/atoum/atoum/pull/431) Tested class should not be mock as an interface. ([@mageekguy])
* [#430](https://github.com/atoum/atoum/pull/430) Add `atoum\mock\generator::allIsInterface()` to definitely disable all parent classes' behaviors in mocks ([@mageekguy])
* [#427](https://github.com/atoum/atoum/pull/427) `atoum\asserters\mock::receive` is an alias to `atoum\asserters\mock::call` ([@mageekguy])


# 1.2.2 - 2015-01-12

* [#415](https://github.com/atoum/atoum/pull/415) Fix a bug in the coverage report with excluded classes ([@mageekguy])
* [#406](https://github.com/atoum/atoum/pull/406) Fix a bug in the HTML coverage with stylesheet URLs ([@jubianchi])
* [#418](https://github.com/atoum/atoum/pull/418) Fix a bug when a mocked method returns a reference ([@hywan])

# 1.2.1 - 2015-01-09

* [#413](https://github.com/atoum/atoum/pull/413) Fix a bug in the exit code management ([@mageekguy])
* [#412](https://github.com/atoum/atoum/pull/412) Use semantics dates in `CHANGELOG.md` ([@hywan])

# 1.2.0 - 2014-12-28

* [#408](https://github.com/atoum/atoum/pull/408) Extract mock autoloader ([@jubianchi])
* [#403](https://github.com/atoum/atoum/pull/403) Fix a bug when setting the default mock namespace ([@hywan])
* [#387](https://github.com/atoum/atoum/pull/387) Support assertion without parenthesis on `dateInterval`, `error`, `extension` and `hash` asserters ([@jubianchi])
* [#401](https://github.com/atoum/atoum/pull/401) Use new Travis container infrastructure ([@jubianchi])
* [#405](https://github.com/atoum/atoum/pull/405) Add the Santa report and an example configuration file ([@jubianchi])
* [#394](https://github.com/atoum/atoum/pull/394) Mock generator now handles variadic arguments in method ([@jubianchi])
* [#398](https://github.com/atoum/atoum/pull/398) Replace broken documentation links ([@jubianchi])
* [#396](https://github.com/atoum/atoum/pull/396) Rename `match` to `matches` on the string asserter ([@hywan])
* [#385](https://github.com/atoum/atoum/pull/385) Rename the PHAR to `atoum.phar` ([@hywan])
* [#392](https://github.com/atoum/atoum/pull/392) Fix broken links in `README.md` ([@evert])
* [#391](https://github.com/atoum/atoum/pull/391) Add dates in `CHANGELOG.md` ([@hywan])
* [#379](https://github.com/atoum/atoum/pull/379) Fix `newTestedInstance` assertion when constructor contains a variable-length argument ([@mageekguy])

# 1.1.0 - 2014-12-09

* [#377](https://github.com/atoum/atoum/pull/377) Hide error when publishing report to coveralls.io fails ([@jubianchi])
* [#368](https://github.com/atoum/atoum/pull/368) Improve dataset key reporting in case of failure ([@mageekguy])
* [#376](https://github.com/atoum/atoum/pull/376) Add branch-alias ([@stephpy], [@hywan])
* [#367](https://github.com/atoum/atoum/pull/367) Add the `isFinal` assertion on the `phpClass`/`class`/`testedClass` asserters ([@mageekguy])

# 1.0.0 - 2014-12-01

* Allow/Disallow mocking undefined methods
* Pass test instance as first parameters of closures in `exception`, `when`, `output`
* Add `--fail-if-void-methods` and `--fail-if-skipped-methods`
* `--init` option now accepts a path to a directory to initialize with atoum configuration
* Add coverage script to automatically produce reports
* Add `isFluent`
* Add `isNull`, `isNotNull`, `isCallable`, `isNotCallable`, `isNotTrue`, `isNotFalse` assertions on `variable`
* Add `isTestedInstance` assertion on `object` asserter
* Add `testedInstance` helper
* Add `newTestedInstance` and `newInstance` helpers
* Add `isNotInstanceOf` assertion on `object` asserter
* Alias assertions from test classes
* Register asserters from test classes
* Define new assertion directly from test classes
* Change test method prefix using `@methodPrefix` on test classes
* Add `CHANGELOG.md`

# 0.0.1 - 2013-11-05

[@mageekguy]: https://github.com/mageekguy
[@jubianchi]: https://github.com/jubianchi
[@hywan]: https://github.com/hywan
[@metalaka]: https://github.com/metalaka
[@GuillaumeDievart]: https://github.com/GuillaumeDievart
[@n-couet]: https://github.com/n-couet
[@remicollet]: https://github.com/remicollet
[@vonglasow]: https://github.com/vonglasow
[@mikaelrandy]: https://github.com/mikaelrandy
[@kao98]: https://github.com/kao98
[@Grummfy]: https://github.com/Grummfy
[@GuillaumeDievart]: https://github.com/GuillaumeDievart
[@stephpy]: https://github.com/stephpy
[@evert]: https://github.com/evert
[@agallou]: https://github.com/agallou
[@fferriere]: https://github.com/fferriere
[@oxman]: https://github.com/blackprism
[@mvrhov]: https://github.com/mvrhov
[@krtek4]: https://github.com/krtek4
[@guiled]: https://github.com/guiled
[@trasher]: https://github.com/trasher
[@fvilpoix]: https://github.com/fvilpoix
[@macintoshplus]: https://github.com/macintoshplus
[@cedric-anne]: https://github.com/cedric-anne
