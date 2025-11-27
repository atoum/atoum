<?php

namespace atoum\atoum\tools\variable;

class analyzer
{
    public function getTypeOf(mixed $mixed): string
    {
        switch (gettype($mixed)) {
            case 'boolean':
                return sprintf('boolean(%s)', $mixed == false ? 'false' : 'true');

            case 'integer':
                return sprintf('integer(%s)', $mixed);

            case 'double':
                return sprintf('float(%s)', $mixed);

            case 'NULL':
                return 'null';

            case 'object':
                return sprintf('object(%s)', get_class($mixed));

            case 'resource':
                return sprintf('%s of type %s', $mixed, get_resource_type($mixed));

            case 'string':
                return sprintf('string(%s) \'%s\'', strlen($mixed), $mixed);

            case 'array':
                return sprintf('array(%s)', count($mixed));
        }
    }

    public function dump(mixed $mixed): string
    {
        ob_start();

        var_dump($mixed);

        return trim(ob_get_clean());
    }

    public function isObject(mixed $mixed): bool
    {
        return (is_object($mixed) === true);
    }

    public function isException(mixed $mixed): bool
    {
        return ($mixed instanceof \exception);
    }

    public function isBoolean(mixed $mixed): bool
    {
        return (is_bool($mixed) === true);
    }

    public function isInteger(mixed $mixed): bool
    {
        return (is_int($mixed) === true);
    }

    public function isFloat(mixed $mixed): bool
    {
        return (is_float($mixed) === true);
    }

    public function isString(mixed $mixed): bool
    {
        return (is_string($mixed) === true);
    }

    public function isUtf8(mixed $mixed): bool
    {
        return ($this->isString($mixed) === true && preg_match('/^.*$/us', $mixed) === 1);
    }

    public function isArray(mixed $mixed): bool
    {
        return (is_array($mixed) === true);
    }

    public function isResource(mixed $mixed): bool
    {
        return (is_resource($mixed) === true);
    }

    public function isRegex(string $namespace): bool
    {
        return false !== @preg_match($namespace, '');
    }

    public function isValidIdentifier(string $identifier): bool
    {
        return 0 !== \preg_match('#^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$#', $identifier);
    }

    public function isValidNamespace(string $namespace): bool
    {
        foreach (explode('\\', trim($namespace, '\\')) as $sub) {
            if (!self::isValidIdentifier($sub)) {
                return false;
            }
        }

        return true;
    }
}
