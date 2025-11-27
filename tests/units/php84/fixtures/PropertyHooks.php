<?php

/* @php-cs-fixer-ignore-file */

namespace atoum\atoum\tests\units\php84\fixtures;

/**
 * Various examples of PHP 8.4 Property Hooks
 * 
 * @requires PHP >= 8.4
 */

/**
 * Example 1: Simple validation
 */
class EmailAddress
{
    public string $email {
        set(string $value) {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                throw new \ValueError("Invalid email address: $value");
            }
            $this->email = strtolower($value);
        }
    }
}

/**
 * Example 2: Lazy initialization
 */
class LazyProperty
{
    private ?string $cachedValue = null;

    public string $value {
        get {
            if ($this->cachedValue === null) {
                // Simulate expensive operation
                $this->cachedValue = $this->computeExpensiveValue();
            }
            return $this->cachedValue;
        }
    }

    private function computeExpensiveValue(): string
    {
        // Simulate expensive computation
        return 'computed-' . uniqid();
    }
}

/**
 * Example 3: Virtual property (computed from others)
 */
class Rectangle
{
    public float $width;
    public float $height;

    public float $area {
        get => $this->width * $this->height;
    }

    public float $perimeter {
        get => 2 * ($this->width + $this->height);
    }

    public function __construct(float $width, float $height)
    {
        $this->width = $width;
        $this->height = $height;
    }
}

/**
 * Example 4: Range validation
 */
class Percentage
{
    public float $value {
        set(float $value) {
            if ($value < 0 || $value > 100) {
                throw new \ValueError("Percentage must be between 0 and 100, got $value");
            }
            $this->value = $value;
        }
    }

    public string $formatted {
        get => number_format($this->value, 2) . '%';
    }
}

/**
 * Example 5: Transformation hook
 */
class FullName
{
    public string $firstName {
        set(string $value) {
            $this->firstName = ucfirst(strtolower(trim($value)));
        }
    }

    public string $lastName {
        set(string $value) {
            $this->lastName = ucfirst(strtolower(trim($value)));
        }
    }

    public string $fullName {
        get => $this->firstName . ' ' . $this->lastName;
        
        set(string $value) {
            $parts = explode(' ', trim($value), 2);
            $this->firstName = $parts[0] ?? '';
            $this->lastName = $parts[1] ?? '';
        }
    }
}

