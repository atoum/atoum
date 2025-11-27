<?php

/* @php-cs-fixer-ignore-file */

namespace atoum\atoum\tests\units\php84\fixtures;

/**
 * Examples of PHP 8.4 Asymmetric Visibility
 * 
 * Asymmetric visibility allows properties to have different visibility
 * for reading and writing.
 * 
 * @requires PHP >= 8.4
 */

/**
 * Example 1: Bank Account with read-only balance
 */
class BankAccount
{
    /**
     * Balance is public for reading but private for writing
     * Can only be modified through deposit() and withdraw()
     */
    public private(set) float $balance = 0.0;
    
    /**
     * Account ID is public for reading but private for writing
     * Set once in constructor, never modified
     */
    public private(set) string $accountId;
    
    /**
     * Transaction history (read-only from outside)
     */
    public private(set) array $transactions = [];

    public function __construct(string $accountId)
    {
        $this->accountId = $accountId;
    }

    public function deposit(float $amount): void
    {
        if ($amount <= 0) {
            throw new \ValueError('Deposit amount must be positive');
        }
        
        $this->balance += $amount;
        $this->transactions[] = [
            'type' => 'deposit',
            'amount' => $amount,
            'date' => new \DateTimeImmutable()
        ];
    }

    public function withdraw(float $amount): void
    {
        if ($amount <= 0) {
            throw new \ValueError('Withdrawal amount must be positive');
        }
        
        if ($amount > $this->balance) {
            throw new \ValueError('Insufficient funds');
        }
        
        $this->balance -= $amount;
        $this->transactions[] = [
            'type' => 'withdrawal',
            'amount' => $amount,
            'date' => new \DateTimeImmutable()
        ];
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function getTransactionCount(): int
    {
        return count($this->transactions);
    }
}

/**
 * Example 2: User with immutable properties
 */
class User
{
    /**
     * User ID set at creation, public read, private write
     */
    public private(set) int $id;
    
    /**
     * Creation timestamp, immutable
     */
    public private(set) \DateTimeImmutable $createdAt;
    
    /**
     * Name is public read/write
     */
    public string $name;
    
    /**
     * Email protected read, private write (controlled update)
     */
    protected private(set) string $email;

    public function __construct(int $id, string $name, string $email)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function updateEmail(string $newEmail): void
    {
        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            throw new \ValueError('Invalid email address');
        }
        
        $this->email = $newEmail;
    }

    protected function getEmail(): string
    {
        return $this->email;
    }
}

/**
 * Example 3: Counter with increment-only value
 */
class Counter
{
    /**
     * Value is public for reading, private for writing
     * Can only be incremented, never decremented or set directly
     */
    public private(set) int $value = 0;
    
    /**
     * Maximum value reached (auto-tracked)
     */
    public private(set) int $maxValue = 0;

    public function increment(int $by = 1): void
    {
        if ($by <= 0) {
            throw new \ValueError('Increment must be positive');
        }
        
        $this->value += $by;
        
        if ($this->value > $this->maxValue) {
            $this->maxValue = $this->value;
        }
    }

    public function reset(): void
    {
        $this->value = 0;
    }
}

/**
 * Example 4: Protected with private write
 */
class Configuration
{
    /**
     * Protected read (accessible in child classes)
     * Private write (only this class can modify)
     */
    protected private(set) array $settings = [];
    
    /**
     * Protected read/write
     */
    protected bool $isLoaded = false;

    public function load(array $settings): void
    {
        if ($this->isLoaded) {
            throw new \RuntimeException('Configuration already loaded');
        }
        
        $this->settings = $settings;
        $this->isLoaded = true;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->settings[$key] ?? $default;
    }

    protected function getSetting(string $key): mixed
    {
        return $this->settings[$key] ?? null;
    }
}

/**
 * Example 5: Validation state
 */
class ValidatedData
{
    /**
     * Validated flag - public read, private write
     */
    public private(set) bool $isValidated = false;
    
    /**
     * Validation errors - public read, private write
     */
    public private(set) array $errors = [];
    
    /**
     * Raw data - public read/write
     */
    public array $data = [];

    public function validate(): bool
    {
        $this->errors = [];
        
        // Example validation
        if (empty($this->data)) {
            $this->errors[] = 'Data cannot be empty';
        }
        
        $this->isValidated = empty($this->errors);
        
        return $this->isValidated;
    }

    public function getData(): array
    {
        if (!$this->isValidated) {
            throw new \RuntimeException('Data not validated');
        }
        
        return $this->data;
    }
}

