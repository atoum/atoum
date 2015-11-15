# Indenting and Whitespace

Use only tabs, with no space. 
Lines should have no trailing whitespace at the end. 
Files should be formatted with \n as the line ending (Unix line endings), not \r\n (Windows line endings). 
PHP files should be in UTF-8. 
PHP files should begin with `<?php ` and should not have an end tag (no `?>`).
The reasons for this can be summarized as:
* Removing it eliminates the possibility for unwanted whitespace at the end of files which can cause "header already sent" errors, XHTML/XML validation issues, and other problems.
* The closing delimiter at the end of a file is optional.
* PHP.net itself removes the closing delimiter from the end of its files (example: prepend.inc), so this can be seen as a "best practice."

# Operators

All binary operators (operators that come between two values), such as `+`, `-`, `=`, `!=`, `==`, `>`, etc. should have a space before and after the operator, for readability. 
For example, an assignment should be formatted as `$foo = $bar;` rather than `$foo=$bar;`. 
Unary operators (operators that operate on only one value), such as `++`, should not have a space between the operator and the variable or number they are operating on.

# Casting

Put a space between the (type) and the `$variable` in a cast: `(int) $mynumber`.

# Control Structures

Control structures include `if`, `for`, `while`, `switch`, etc. 
Here is a sample `if` statement, since it is the most complicated of them:

```php
if (condition1 || condition2)
{
	action1;
}
else if (condition3 && condition4)
{
	action2;
}
else
{
	defaultaction;
}
```

Don't use "elseif", always use `else if`, and dont' use alternative syntax. 
If there are more than three `else if`, replace them by a `switch` to improve readability:

```php
switch (true)
{
	case condition1 || condition2:
		break;

	case condition3 && condition4:
		break;

	case condition5:
		break;

	default:
		defaultaction;
}
```

Control statements should have one space between the control keyword and opening parenthesis, to distinguish them from function calls. 
Always use curly braces even in situations where they are technically optional. 
Having them increases readability and decreases the likelihood of logic errors being introduced when new lines are added. 
For `switch` statements:

```php
switch (condition)
{
	case 1:
		action1;
		break;

	case 2:
		action2;
		break;

	default:
		defaultaction;
}
```

For do-while statements:

```php
do
{
	actions;
}
while ($condition);
```
	
# Line length and wrapping

There is no limit to line length.

# Function Calls

Functions should be called with no spaces between the function name, the opening parenthesis, and the first parameter; spaces between commas and each parameter, and no space between the last parameter, the closing parenthesis, and the semicolon. 
Here's an example:

```php
$var = $object->foo($bar, $baz, $quux);
```

As displayed above, there should be one space on either side of an equals sign used to assign the return value of a function to a variable. 
In the case of a block of related assignments, more space should not be used to indent them.
This is a bad practice in the sense of this coding convention:

```php
$short         = foo($bar);
$long_variable = foo($baz);
```

Always attempt to return a meaningful value from a function if one is appropriated.
If there is no meaningful value, always return `$this` (fluent syntax).

# Class Constructor Calls

When calling class constructors with no arguments, always include parentheses:

```php
$foo = new MyClassName();
```

Note that if the class name is a variable, the variable will be evaluated first to get the class name, and then the constructor will be called. Use the same syntax:

```php
$bar = 'MyClassName';
$foo = new $bar();
$foo = new $bar($arg1, $arg2);
```

# Arrays

Arrays should be formatted with a space separating each element (after the comma), and spaces around the `=>` key association operator, if applicable.
If an array contains several values, put each of them on one line:

```php
$array = array(
	'hello',
	'world',
	'foo' => 'bar'
);
```

To get lenght of an array, use `sizeof` instead of `count` (no technical reason about that, it's just the current convention).

# Quotes

You should always use single quote and always use a space between the dot and the concatenated parts to improve readability:

```php
$string = 'Foo' . $bar;
$string = $bar . 'foo';
$string = bar() . 'foo';
$string = 'foo' . 'bar';
```

When using the concatenating assignment operator ('.='), use a space on each side as with the assignment operator:

```php
$string .= 'Foo';
$string .= $bar;
$string .= baz();
```

# Comments

Try to avoid useless comment, ie. use them only to add a link to a technical documentation or an explanation about the code.
You should use `//` or `#` to comment, and they should be in english.
This is a good example:

```php
# Default bootstrap file MUST be included here because some arguments on the command line can include some tests which depends of this file.
# So, this file must be included BEFORE argument parsing which is done in script::run().
# Default bootstrap file can be overrided in a default config file included in script\configurable::run() which extends script::run().
# So, if a bootstrap file is defined in a default config file, it will be available when arguments on CLI will be parsed

// see http://www.floating-point-gui.de/errors/comparison/ for more informations
```

Currently, atoum does not support PHPDoc.

# Including code

Anywhere you are unconditionally including a class file, use `require_once`. 
Anywhere you are conditionally including a class file, use `include_once`. 
Both of these will ensure that class files are included only once. 
They share the same file list, so you don't need to worry about mixing them (a file included with `require_once` will not be included again by `include_once`).
You don't need parenthesis around the file name to be included.
When including code, you should always use a relative path from the current directory:

```php
require_once __DIR__ . '/../../../path/to/the/included/php/file.php';
```

# Naming Conventions

Function, variable, constant, class, interface and method must be named using lowerCamelCase.
Protected or private properties and methods should not use an underscore prefix.

# Global Variables

You should not use global variable.

Thanks to the Drupal community for its work about its [coding convention](https://drupal.org/coding-standards).
