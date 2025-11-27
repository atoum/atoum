# Test Attributes in atoum

atoum 4.1+ ships with a first‑class attribute API so you can configure your
unit tests with native PHP syntax instead of legacy docblock annotations.
This document explains how to use these attributes and how they map to the
historical annotations.

## Quick Start

Import the `atoum\atoum\attributes` namespace (aliased to `Attributes` in the
examples below) and decorate your test classes or methods:

```php
<?php

namespace vendor\project\tests\units;

use atoum\atoum;
use atoum\atoum\attributes as Attributes;

#[Attributes\Tags('unit', 'database')]
class Connection extends atoum\test
{
    #[Attributes\Php('8.1')]
    #[Attributes\Extensions('pdo_mysql')]
    public function testQueryIsExecuted()
    {
        $this->boolean($this->newTestedInstance()->query('SELECT 1'))->isTrue();
    }
}
```

All attributes live in `atoum\atoum\attributes` and can be applied to classes
and/or methods depending on their purpose. They are regular PHP attributes, so
static analysers and IDEs understand them out of the box.

## Available Attributes

| Attribute | Targets | Description |
|-----------|---------|-------------|
| `#[Attributes\Php($version, $operator = '>=')]` | class, method | Restrict execution to specific PHP versions |
| `#[Attributes\Ignore($boolean = true)]` | class, method | Mark a test (or the entire class) as ignored |
| `#[Attributes\Tags(...$tags)]` | class, method | Declare tags used by filter / reporting |
| `#[Attributes\TestNamespace($namespace = null)]` | class | Override the default test namespace mapping |
| `#[Attributes\TestMethodPrefix($prefix)]` | class | Override the default test method prefix |
| `#[Attributes\MaxChildrenNumber($count)]` | class | Configure the maximum number of child processes |
| `#[Attributes\Engine($engine)]` | class, method | Force a specific engine (e.g. `inline`) |
| `#[Attributes\HasVoidMethods]` / `#[Attributes\HasNotVoidMethods]` | class | Hint return type expectations for fluent assertions |
| `#[Attributes\Extensions(...$extensions)]` | class, method | Require (or forbid with `!extension`) PHP extensions |
| `#[Attributes\Os(...$operatingSystems)]` | class | Restrict execution to specific operating systems (prefix with `!` to exclude) |
| `#[Attributes\DataProvider($method = null)]` | method | Configure the data provider for a test method |
| `#[Attributes\IsVoid]` / `#[Attributes\IsNotVoid]` | method | Force the fluent interface behaviour of a test method |

> **Tip**: attributes are repeatable. For instance a method can declare
> multiple `#[Attributes\Extensions]` blocks when it is clearer to group
> requirements.

## Migration Guide

The legacy docblock annotations are still parsed for backward compatibility but
now raise an `E_USER_DEPRECATED` notice. The table below shows the mapping
between old annotations and their attribute counterparts:

| Annotation | Attribute |
|------------|-----------|
| `@php >= 8.1` | `#[Attributes\Php('8.1')]` |
| `@ignore on` | `#[Attributes\Ignore]` |
| `@ignore off` | remove the attribute |
| `@tags foo bar` | `#[Attributes\Tags('foo', 'bar')]` |
| `@namespace vendor\tests` | `#[Attributes\TestNamespace('vendor\\tests')]` |
| `@methodPrefix spec` | `#[Attributes\TestMethodPrefix('spec')]` |
| `@maxChildrenNumber 4` | `#[Attributes\MaxChildrenNumber(4)]` |
| `@engine inline` | `#[Attributes\Engine('inline')]` |
| `@hasVoidMethods` | `#[Attributes\HasVoidMethods]` |
| `@hasNotVoidMethods` | `#[Attributes\HasNotVoidMethods]` |
| `@extensions mbstring redis` | `#[Attributes\Extensions('mbstring', 'redis')]` |
| `@extensions !xdebug` | `#[Attributes\Extensions('!xdebug')]` |
| `@os linux` | `#[Attributes\Os('linux')]` |
| `@os !windows !winnt` | `#[Attributes\Os('!windows', '!winnt')]` |
| `@dataProvider provideData` | `#[Attributes\DataProvider('provideData')]` |
| `@isVoid` | `#[Attributes\IsVoid]` |
| `@isNotVoid` | `#[Attributes\IsNotVoid]` |

## Deprecation of Annotations

- Docblock annotations are still recognised to ease migration, but every
  occurrence now triggers a deprecation warning explaining which attribute to use.
- Future releases will remove support for annotations entirely. Make sure to
  update your test suite as soon as possible.

## Additional Resources

- [Legacy annotation parser tests](tests/units/classes/annotations/extractor.php)
  – kept to ensure backward compatibility during the transition period.
- `classes/test.php` contains the attribute handling logic if you want to add
  your own decorators.

If you bump into an annotation that does not have an equivalent attribute yet,
please open an issue or a pull request – the migration is ongoing and
contributions are welcome!
