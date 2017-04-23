<?php

namespace mageekguy\atoum;

class locale
{
    protected $value = null;

    public function __construct($value = null)
    {
        if ($value !== null) {
            $this->set($value);
        }
    }

    public function __toString()
    {
        return ($this->value === null ? 'unknown' : $this->value);
    }

    public function set($value)
    {
        $this->value = (string) $value;

        return $this;
    }

    public function get()
    {
        return $this->value;
    }

    public function _($string, ...$arguments)
    {
        return self::format($string, $arguments);
    }

    public function __($singular, $plural, $quantity, ...$arguments)
    {
        return self::format($quantity <= 1 ? $singular : $plural, $arguments);
    }

    private static function format($string, $arguments)
    {
        if (count($arguments) > 0) {
            $string = vsprintf($string, $arguments);
        }

        return $string;
    }
}
