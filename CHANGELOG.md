# `dev-master`

# 2.0.1 - 2015-02-27

* [#440](https://github.com/atoum/atoum/pull/440) `--configurations` option should be handled first ([@jubianchi][jubianchi])
* [#439](https://github.com/atoum/atoum/pull/439) Since atoum is 2.*, branch-alias must follow ([@hywan][hywan])
* [#437](https://github.com/atoum/atoum/pull/437) Autoloader should not try to resolve alias if requested class exists ([@jubianchi][jubianchi])
* Generalize method call checking in mock ([@mageekguy][mageekguy])
* [#435](https://github.com/atoum/atoum/pull/435) Partially revert BC break introduced in [#420](https://github.com/atoum/atoum/pull/420) ([@mageekguy][mageekguy])

# 2.0.0 - 2015-02-13

## BC break updates
* [#420](https://github.com/atoum/atoum/pull/420) `atoum\test::beforeTestMethod` is called before the tested class is loaded (@mageekguy)

## Other updates
* [#431](https://github.com/atoum/atoum/pull/431) Tested class should not be mock as an interface. (@mageekguy)
* [#430](https://github.com/atoum/atoum/pull/430) Add `atoum\mock\generator::allIsInterface()` to definitely disable all parent classes' behaviors in mocks (@mageekguy)
* [#427](https://github.com/atoum/atoum/pull/427) `atoum\asserters\mock::receive` is an alias to `atoum\asserters\mock::call` (@mageekguy)


# 1.2.2 - 2015-01-12

* #415 Fix a bug in the coverage report with excluded classes (@mageekguy)
* #406 Fix a bug in the HTML coverage with stylesheet URLs (@jubianchi)
* #418 Fix a bug when a mocked method returns a reference (@hywan)

# 1.2.1 - 2015-01-09

* #413 Fix a bug in the exit code management (@mageekguy)
* #412 Use semantics dates in `CHANGELOG.md` (@hywan)

# 1.2.0 - 2014-12-28

* #408 Extract mock autoloader (@jubianchi)
* #403 Fix a bug when setting the default mock namespace (@hywan)
* #387 Support assertion without parenthesis on `dateInterval`, `error`, `extension` and `hash` asserters (@jubianchi)
* #401 Use new Travis container infrastructure (@jubianchi)
* #405 Add the Santa report and an example configuration file (@jubianchi)
* #394 Mock generator now handles variadic arguments in method (@jubianchi)
* #398 Replace broken documentation links (@jubianchi)
* #396 Rename `match` to `matches` on the string asserter (@hywan)
* #385 Rename the PHAR to `atoum.phar` (@hywan)
* #392 Fix broken links in `README.md` (@evert)
* #391 Add dates in `CHANGELOG.md` (@hywan)
* #379 Fix `newTestedInstance` assertion when constructor contains a variable-length argument (@mageekguy)

# 1.1.0 - 2014-12-09

* #377 Hide error when publishing report to coveralls.io fails (@jubianchi)
* #368 Improve dataset key reporting in case of failure (@mageekguy)
* #376 Add branch-alias (@stephpy, @hywan)
* #367 Add the `isFinal` assertion on the `phpClass`/`class`/`testedClass` asserters (@mageekguy)

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

[mageekguy]: https://github.com/mageekguy
[jubianchi]: https://github.com/jubianchi
[hywan]: https://github.com/hywan
