<?php

namespace atoum\atoum\tests\units\php84;

use atoum\atoum;
use atoum\atoum\attributes\Php;
use atoum\atoum\tests\units\php84\fixtures\Configuration;
use atoum\atoum\tests\units\php84\fixtures\LegacyApi;
use atoum\atoum\tests\units\php84\fixtures\ModernService;
use atoum\atoum\tests\units\php84\fixtures\OldService;
use atoum\atoum\tests\units\php84\fixtures\VersionedApi;

/**
 * Tests for PHP 8.4 #[\Deprecated] attribute support
 */
class DeprecatedAttributeTest extends atoum
{
    /**
     * Test detecting deprecated methods via Reflection
     *
     */
    #[Php('8.4')]
    public function testDeprecatedMethodIsDetectable()
    {
        $this
            ->given($reflection = new \ReflectionMethod(LegacyApi::class, 'process'))
            ->and($attributes = $reflection->getAttributes(\Deprecated::class))
            ->then
                // Should have one Deprecated attribute
                ->array($attributes)->hasSize(1)

                // Get the attribute instance
                ->when($deprecatedAttr = $attributes[0]->newInstance())
                ->then
                    ->object($deprecatedAttr)->isInstanceOf(\Deprecated::class)

                    // Check message
                    ->if(property_exists($deprecatedAttr, 'message'))
                    ->then
                        ->string($deprecatedAttr->message)
                            ->contains('Use processV2() instead')
                            ->contains('version 3.0')
        ;
    }

    /**
     * Test deprecated class detection
     *
     */
    #[Php('8.4')]
    public function testDeprecatedClassIsDetectable()
    {
        $this
            ->given($reflection = new \ReflectionClass(OldService::class))
            ->and($attributes = $reflection->getAttributes(\Deprecated::class))
            ->then
                ->array($attributes)->hasSize(1)

                ->when($deprecatedAttr = $attributes[0]->newInstance())
                ->then
                    ->object($deprecatedAttr)->isInstanceOf(\Deprecated::class)
                    ->if(property_exists($deprecatedAttr, 'message'))
                    ->then
                        ->string($deprecatedAttr->message)
                            ->contains('ModernService')
        ;
    }

    /**
     * Test deprecated constant detection
     *
     */
    #[Php('8.4')]
    public function testDeprecatedConstantIsDetectable()
    {
        $this
            ->given($reflection = new \ReflectionClassConstant(Configuration::class, 'OLD_FORMAT'))
            ->and($attributes = $reflection->getAttributes(\Deprecated::class))
            ->then
                ->array($attributes)->hasSize(1)

                ->when($deprecatedAttr = $attributes[0]->newInstance())
                ->then
                    ->object($deprecatedAttr)->isInstanceOf(\Deprecated::class)
        ;
    }

    /**
     * Test deprecated property detection
     *
     */
    #[Php('8.4')]
    public function testDeprecatedPropertyIsDetectable()
    {
        $this
            ->given($reflection = new \ReflectionProperty(Configuration::class, 'oldValue'))
            ->and($attributes = $reflection->getAttributes(\Deprecated::class))
            ->then
                ->array($attributes)->hasSize(1)
        ;
    }

    /**
     * Test that non-deprecated methods don't have the attribute
     *
     */
    #[Php('8.4')]
    public function testNonDeprecatedMethodHasNoAttribute()
    {
        $this
            ->given($reflection = new \ReflectionMethod(LegacyApi::class, 'processV2'))
            ->and($attributes = $reflection->getAttributes(\Deprecated::class))
            ->then
                ->array($attributes)->isEmpty()
        ;
    }

    /**
     * Test deprecated method with minimal parameters
     *
     */
    #[Php('8.4')]
    public function testDeprecatedMethodWithoutParameters()
    {
        $this
            ->given($reflection = new \ReflectionMethod(LegacyApi::class, 'oldCalculate'))
            ->and($attributes = $reflection->getAttributes(\Deprecated::class))
            ->then
                ->array($attributes)->hasSize(1)
        ;
    }

    /**
     * Test mocking a deprecated method still works
     *
     */
    #[Php('8.4')]
    public function testMockingDeprecatedMethod()
    {
        $this
            ->given($mock = new \mock\examples\php84\LegacyApi())
            ->and($this->calling($mock)->process = ['mocked', 'result'])
            ->when($result = $mock->process(['test']))
            ->then
                ->array($result)->isEqualTo(['mocked', 'result'])
                ->mock($mock)
                    ->call('process')->once()
        ;
    }

    /**
     * Test mocking a deprecated class
     *
     */
    #[Php('8.4')]
    public function testMockingDeprecatedClass()
    {
        $this
            ->given($mock = new \mock\examples\php84\OldService())
            ->and($this->calling($mock)->doSomething = 'mocked')
            ->when($result = $mock->doSomething())
            ->then
                ->string($result)->isEqualTo('mocked')
        ;
    }

    /**
     * Test that deprecated method can still be called
     * (PHP doesn't prevent calling deprecated code, it just emits E_USER_DEPRECATED)
     */
    #[Php('8.4')]
    public function testDeprecatedMethodIsStillCallable()
    {
        $this
            ->given($api = new LegacyApi())
            ->when($result = $api->process(['hello', 'world']))
            ->then
                ->array($result)->isEqualTo(['HELLO', 'WORLD'])
        ;
    }

    /**
     * Test versioned deprecations
     *
     */
    #[Php('8.4')]
    public function testVersionedDeprecations()
    {
        $this
            ->given($api = new VersionedApi())
            ->and($v1Reflection = new \ReflectionMethod($api, 'v1Method'))
            ->and($v2Reflection = new \ReflectionMethod($api, 'v2Method'))
            ->and($v3Reflection = new \ReflectionMethod($api, 'v3Method'))
            ->then
                // v1 is deprecated
                ->array($v1Reflection->getAttributes(\Deprecated::class))
                    ->hasSize(1)

                // v2 is deprecated
                ->array($v2Reflection->getAttributes(\Deprecated::class))
                    ->hasSize(1)

                // v3 is NOT deprecated
                ->array($v3Reflection->getAttributes(\Deprecated::class))
                    ->isEmpty()
        ;
    }

    /**
     * Test extracting "since" parameter from deprecation
     *
     */
    #[Php('8.4')]
    public function testDeprecationSinceParameter()
    {
        $this
            ->given($reflection = new \ReflectionMethod(LegacyApi::class, 'process'))
            ->and($attributes = $reflection->getAttributes(\Deprecated::class))
            ->and($deprecatedAttr = $attributes[0]->newInstance())
            ->then
                ->if(property_exists($deprecatedAttr, 'since'))
                ->then
                    ->string($deprecatedAttr->since)->isEqualTo('2.0')
        ;
    }

    /**
     * Test helper method to check if method is deprecated
     *
     */
    #[Php('8.4')]
    public function testIsMethodDeprecated()
    {
        $this
            ->boolean($this->isMethodDeprecated(LegacyApi::class, 'process'))
                ->isTrue()

            ->boolean($this->isMethodDeprecated(LegacyApi::class, 'processV2'))
                ->isFalse()
        ;
    }

    /**
     * Test helper to check if class is deprecated
     *
     */
    #[Php('8.4')]
    public function testIsClassDeprecated()
    {
        $this
            ->boolean($this->isClassDeprecated(OldService::class))
                ->isTrue()

            ->boolean($this->isClassDeprecated(ModernService::class))
                ->isFalse()
        ;
    }

    /**
     * Helper method: Check if a method has the Deprecated attribute
     */
    protected function isMethodDeprecated(string $class, string $method): bool
    {
        try {
            $reflection = new \ReflectionMethod($class, $method);
            $attributes = $reflection->getAttributes(\Deprecated::class);
            return !empty($attributes);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Helper method: Check if a class has the Deprecated attribute
     */
    protected function isClassDeprecated(string $class): bool
    {
        try {
            $reflection = new \ReflectionClass($class);
            $attributes = $reflection->getAttributes(\Deprecated::class);
            return !empty($attributes);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Helper method: Get deprecation message
     */
    protected function getDeprecationMessage(string $class, string $method): ?string
    {
        try {
            $reflection = new \ReflectionMethod($class, $method);
            $attributes = $reflection->getAttributes(\Deprecated::class);

            if (empty($attributes)) {
                return null;
            }

            $deprecated = $attributes[0]->newInstance();

            return property_exists($deprecated, 'message') ? $deprecated->message : null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
