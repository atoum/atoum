<?php

/* @php-cs-fixer-ignore-file */

namespace atoum\atoum\tests\units\php84\fixtures;

/**
 * Example class demonstrating PHP 8.4 Property Hooks
 * 
 * This class shows different types of property hooks:
 * - Get-only hook for computed values
 * - Set-only hook for validation
 * - Combined get/set hooks
 * 
 * @requires PHP >= 8.4
 */
class TemperatureWithHooks
{
    /**
     * Temperature in Celsius with validation hook
     */
    public float $celsius {
        set(float $value) {
            if ($value < -273.15) {
                throw new \ValueError(
                    'Temperature cannot be below absolute zero (-273.15°C)'
                );
            }
            $this->celsius = $value;
        }
    }

    /**
     * Temperature in Fahrenheit (computed from Celsius)
     * Demonstrates bidirectional conversion with hooks
     */
    public float $fahrenheit {
        get => ($this->celsius * 9/5) + 32;
        
        set(float $value) {
            $this->celsius = ($value - 32) * 5/9;
        }
    }

    /**
     * Temperature in Kelvin (computed, read-only)
     * Only has a get hook
     */
    public float $kelvin {
        get => $this->celsius + 273.15;
    }

    /**
     * Scale name - uppercase transformation
     */
    public string $scale {
        get => strtoupper($this->scale);
        
        set(string $value) {
            $allowed = ['celsius', 'fahrenheit', 'kelvin'];
            $normalized = strtolower($value);
            
            if (!in_array($normalized, $allowed)) {
                throw new \ValueError(
                    "Invalid scale '$value'. Must be one of: " . implode(', ', $allowed)
                );
            }
            
            $this->scale = $normalized;
        }
    }

    public function __construct(float $celsius = 0.0, string $scale = 'celsius')
    {
        $this->celsius = $celsius;
        $this->scale = $scale;
    }

    /**
     * Get temperature in the specified scale
     */
    public function getValue(): float
    {
        return match($this->scale) {
            'CELSIUS' => $this->celsius,
            'FAHRENHEIT' => $this->fahrenheit,
            'KELVIN' => $this->kelvin,
            default => $this->celsius,
        };
    }

    /**
     * Check if temperature is freezing (below 0°C)
     */
    public function isFreezing(): bool
    {
        return $this->celsius < 0;
    }

    /**
     * Check if temperature is boiling (above 100°C)
     */
    public function isBoiling(): bool
    {
        return $this->celsius > 100;
    }
}

