# atoum upgrade guide

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
