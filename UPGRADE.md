# atoum upgrade guide

## From 4.4

atoum `4.4` requires **PHP `>= 8.0`**.

## PHP 8 Typing Upgrade (next major release)

With the transition to native PHP 8+ typing, several signatures in the core have changed.

- **Runtime requirement:** atoum now mandates PHP 8.0 or higher.
- **Method signatures:** overrides of internal classes must adopt the new parameter and return types (e.g. `: static`, `: void`).
- **Magic methods:** `__set()` and `__unset()` now return `void`.
- **Engines and observers:** `engine::run()` and `callObservers()` no longer support fluent chaining.
- **Nullable handling:** methods such as `getScore()` return nullable types and must be checked before use.

For the exhaustive list of affected APIs, concrete before/after examples, and migration tips for extension authors, consult [`BREAKING_CHANGES.md`](BREAKING_CHANGES.md).

## From 2.x to 3.x

### Runtime

atoum `3.x` requires **PHP `>= 5.6.0`**.

If you want to get coverage reports or use step-by-step debugging, you must use **xDebug `>= 2.3.0`**.

### Assertions

atoum `2.x` supported PHP `>= 5.3.3`. Because on lower version `$this` in closures was not bound to the current object context, some assertions provided the test as an argument to closures.

This is not the case anymore.

#### `when`

atoum `2.x`:

```php
$this
    ->when(function(atoum\test $test) { 
        $test->testedInstance->doSomething();
    })
;  
```

atoum `3.x`:

```php
$this
    ->when(function() { 
        $this->testedInstance->doSomething();
    })
;  
```

#### `exception`

atoum `2.x`:
 
```php
$this
    ->exception(function(atoum\test $test) { 
        $test->testedInstance->doSomethingAndThrow();
    })
;    
```

atoum `3.x`:

```php
$this
    ->exception(function() { 
        $this->testedInstance->doSomethingAndThrow();
    })
;    
```

### Reports

Some reports have been moved to a dedicated extension: [`atoum/reports-extension`](https://github.com/atoum/reports-extension).

If you are using one of those reports, consider using the extension or simply remove them as they are not part of atoum anymore:

* `atoum\reports\realtime\nyancat`
* `atoum\reports\realtime\santa`

You will only have to install the `atoum/reports-extension` and everything should work fine as the report classes have the exact same FQCN.
