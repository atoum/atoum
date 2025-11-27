<?php

/* @php-cs-fixer-ignore-file */

namespace atoum\atoum\tests\units\php84\fixtures;

/**
 * Examples of PHP 8.4 #[\Deprecated] attribute
 *
 * The Deprecated attribute allows marking code as deprecated natively
 * instead of relying on @deprecated docblock comments.
 *
 * @requires PHP >= 8.4
 */

/**
 * Example class with deprecated methods
 */
class LegacyApi
{
    /**
     * Old method - deprecated with reason
     */
    #[\Deprecated(
        message: "Use processV2() instead. This method will be removed in version 3.0.",
        since: "2.0"
    )]
    public function process(array $data): array
    {
        // Old implementation
        return array_map('strtoupper', $data);
    }

    /**
     * New method that replaces process()
     */
    public function processV2(array $data): array
    {
        // New improved implementation
        return array_map('ucfirst', $data);
    }

    /**
     * Deprecated method without parameters
     */
    #[\Deprecated]
    public function oldCalculate(int $a, int $b): int
    {
        return $a + $b;
    }

    /**
     * New calculation method
     */
    public function calculate(int $a, int $b): int
    {
        return $a + $b;
    }

    /**
     * Deprecated static method
     */
    #[\Deprecated(message: "Use createNew() instead", since: "2.1")]
    public static function create(): self
    {
        return new self();
    }

    /**
     * New factory method
     */
    public static function createNew(): self
    {
        return new self();
    }
}

/**
 * Class that is entirely deprecated
 */
#[\Deprecated(
    message: "This entire class is deprecated. Use ModernService instead.",
    since: "2.5"
)]
class OldService
{
    public function doSomething(): string
    {
        return 'old implementation';
    }
}

/**
 * Modern replacement
 */
class ModernService
{
    public function doSomething(): string
    {
        return 'modern implementation';
    }
}

/**
 * Class with deprecated constants
 */
class Configuration
{
    /**
     * Deprecated constant
     */
    #[\Deprecated(message: "Use NEW_FORMAT instead")]
    public const OLD_FORMAT = 'old';

    /**
     * New constant
     */
    public const NEW_FORMAT = 'new';

    /**
     * Deprecated property (PHP 8.4+)
     */
    #[\Deprecated(message: "Use $newValue instead")]
    public string $oldValue = '';

    /**
     * New property
     */
    public string $newValue = '';
}

/**
 * Interface with deprecated methods
 */
interface PaymentGateway
{
    #[\Deprecated(message: "Use processPaymentV2() with Payment object")]
    public function processPayment(float $amount, string $currency): bool;

    public function processPaymentV2(\examples\php84\Payment $payment): bool;
}

/**
 * Payment DTO
 */
class Payment
{
    public function __construct(
        public readonly float $amount,
        public readonly string $currency
    ) {
    }
}

/**
 * Implementation with deprecated method
 */
class StripeGateway implements PaymentGateway
{
    #[\Deprecated(message: "Use processPaymentV2() with Payment object")]
    public function processPayment(float $amount, string $currency): bool
    {
        // Old implementation
        return true;
    }

    public function processPaymentV2(Payment $payment): bool
    {
        // New implementation
        return true;
    }
}

/**
 * Class showing deprecation with version tracking
 */
class VersionedApi
{
    #[\Deprecated(since: "1.0")]
    public function v1Method(): string
    {
        return 'version 1';
    }

    #[\Deprecated(since: "2.0", message: "Use v3Method()")]
    public function v2Method(): string
    {
        return 'version 2';
    }

    public function v3Method(): string
    {
        return 'version 3';
    }
}

/**
 * Trait with deprecated methods
 */
trait LegacyHelpers
{
    #[\Deprecated(message: "Use formatDataNew() instead")]
    public function formatData(array $data): string
    {
        return implode(',', $data);
    }

    public function formatDataNew(array $data): string
    {
        return json_encode($data);
    }
}

/**
 * Class using deprecated trait
 */
class DataFormatter
{
    use LegacyHelpers;

    public function format(array $data): string
    {
        return $this->formatDataNew($data);
    }
}
