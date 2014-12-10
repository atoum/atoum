# `dev-master`

* #379 Fix `newTestedInstance` assertion when constructor contains a variable-length argument (@mageekguy)

# 1.1.0

* #377 Hide error when publishing report to coveralls.io fails (@jubianchi)
* #368 Improve dataset key reporting in case of failure (@mageekguy)
* #376 Add branch-alias (@stephpy, @hywan)
* #367 Add the `isFinal` assertion on the `phpClass`/`class`/`testedClass` asserters (@mageekguy)

# 1.0.0

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

# 0.0.1
