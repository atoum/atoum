<?php

namespace atoum\atoum\tests\units\php84;

use atoum\atoum;
use atoum\atoum\attributes\Php;
use atoum\atoum\tests\units\php84\fixtures\Configuration;
use atoum\atoum\tests\units\php84\fixtures\ExpensiveService;
use atoum\atoum\tests\units\php84\fixtures\LazyObjectFactory;
use atoum\atoum\tests\units\php84\fixtures\ServiceContainer;
use atoum\atoum\tests\units\php84\fixtures\UserRepository;

/**
 * Tests for PHP 8.4 Lazy Objects
 *
 */
#[Php('8.4')]
class LazyObjectsTest extends atoum
{
    public function testLazyGhostIsNotInitializedUntilAccess(): void
    {
        $this
            ->given($lazy = LazyObjectFactory::createLazyGhost())

            ->then
                // The object exists but is not initialized
                ->object($lazy)->isInstanceOf(ExpensiveService::class)
                ->boolean(LazyObjectFactory::isLazy($lazy))->isTrue()

            ->when($result = $lazy->process('test'))

            ->then
                // Now the object is initialized
                ->boolean(LazyObjectFactory::isLazy($lazy))->isFalse()
                ->string($result)->isEqualTo('TEST - processed')
                ->boolean($lazy->isInitialized())->isTrue()
        ;
    }

    public function testLazyProxyCreatesRealObjectOnAccess(): void
    {
        $this
            ->given($lazy = LazyObjectFactory::createLazyProxy())

            ->then
                // The proxy exists but real object not created yet
                ->object($lazy)->isInstanceOf(ExpensiveService::class)
                ->boolean(LazyObjectFactory::isLazy($lazy))->isTrue()

            ->when($data = $lazy->getData())

            ->then
                // Real object is now created
                ->boolean(LazyObjectFactory::isLazy($lazy))->isFalse()
                ->array($data)
                    ->hasKey('config')
                    ->hasKey('cache')
        ;
    }

    public function testForceInitializationOfLazyObject()
    {
        $this
            ->given($lazy = LazyObjectFactory::createLazyGhost())

            ->then
                ->boolean(LazyObjectFactory::isLazy($lazy))->isTrue()

            ->when(function () use ($lazy) {
                LazyObjectFactory::initialize($lazy);
            })

            ->then
                ->boolean(LazyObjectFactory::isLazy($lazy))->isFalse()
                ->boolean($lazy->isInitialized())->isTrue()
        ;
    }

    public function testReflectionDetectsLazyObject()
    {
        $this
            ->given($lazy = LazyObjectFactory::createLazyGhost())
            ->and($reflector = new \ReflectionClass($lazy))

            ->then
                ->boolean($reflector->isUninitializedLazyObject($lazy))
                    ->isTrue()

            ->when($lazy->process('data'))

            ->then
                ->boolean($reflector->isUninitializedLazyObject($lazy))
                    ->isFalse()
        ;
    }

    public function testServiceContainerWithLazyServices()
    {
        $this
            ->given($container = new ServiceContainer())
            ->and($container->register('expensive', fn () => new ExpensiveService()))
            ->then
                // Service not initialized yet
                ->boolean($container->isServiceInitialized('expensive'))
                    ->isFalse()

            ->when($service = $container->get('expensive'))

            ->then
                // Service is retrieved but still lazy
                ->object($service)->isInstanceOf(ExpensiveService::class)

            ->when($result = $service->process('hello'))

            ->then
                // Now initialized after method call
                ->string($result)->contains('HELLO')
                ->boolean($container->isServiceInitialized('expensive'))
                    ->isTrue()
        ;
    }

    public function testConfigurationLazyLoading()
    {
        $this
            ->given($config = Configuration::createLazy('/path/to/config.php'))
            ->and($reflector = new \ReflectionClass($config))

            ->then
                // Config object exists but not loaded
                ->boolean($reflector->isUninitializedLazyObject($config))
                    ->isTrue()

            ->when($appName = $config->get('app_name'))

            ->then
                // Config loaded on first access
                ->string($appName)->isEqualTo('My App')
                ->boolean($reflector->isUninitializedLazyObject($config))
                    ->isFalse()
        ;
    }

    public function testMockingLazyObject()
    {
        $this
            ->given($mock = new \mock\examples\php84\ExpensiveService())
            ->and($this->calling($mock)->process = 'mocked result')

            ->when($result = $mock->process('test'))

            ->then
                ->string($result)->isEqualTo('mocked result')
                ->mock($mock)
                    ->call('process')
                        ->withArguments('test')
                        ->once()
        ;
    }

    public function testLazyObjectWithMockDependency()
    {
        $this
            ->given($mockRepo = new \mock\examples\php84\UserRepository('mock-db'))
            ->and($this->calling($mockRepo)->findById = ['id' => 1, 'name' => 'Mocked User'])

            ->when($user = $mockRepo->findById(1))

            ->then
                ->array($user)
                    ->string['name']->isEqualTo('Mocked User')
                ->mock($mockRepo)
                    ->call('findById')->once()
        ;
    }

    public function testUserRepositoryLazyLoading()
    {
        $this
            ->given($repo = new UserRepository('test-db'))

            // Users not loaded yet (no database query)
            ->when($user = $repo->findById(1))

            ->then
                // Now users are loaded
                ->array($user)
                    ->integer['id']->isEqualTo(1)
                    ->string['name']->isEqualTo('Alice')

            // Second call uses cached data
            ->when($user2 = $repo->findById(2))

            ->then
                ->array($user2)
                    ->integer['id']->isEqualTo(2)
                    ->string['name']->isEqualTo('Bob')
        ;
    }

    public function testLazyObjectPerformanceBenefit()
    {
        $this
            ->given($startTime = microtime(true))

            // Creating 10 lazy objects should be fast
            ->and($lazyObjects = array_map(
                fn () => LazyObjectFactory::createLazyGhost(),
                range(1, 10)
            ))

            ->and($creationTime = microtime(true) - $startTime)

            ->then
                ->array($lazyObjects)->hasSize(10)
                // All are lazy
                ->boolean(LazyObjectFactory::isLazy($lazyObjects[0]))->isTrue()
                ->boolean(LazyObjectFactory::isLazy($lazyObjects[9]))->isTrue()

                // Creation should be very fast (< 10ms for 10 objects)
                ->float($creationTime)->isLessThan(0.01)
        ;
    }

    public function testLazyObjectBehavesLikeNormalObject()
    {
        $this
            ->given($lazy = LazyObjectFactory::createLazyGhost())
            ->and($normal = new ExpensiveService())

            ->when($lazyResult = $lazy->process('test'))
            ->and($normalResult = $normal->process('test'))

            ->then
                // Same behavior
                ->string($lazyResult)->isEqualTo($normalResult)
                ->array($lazy->getData())->isEqualTo($normal->getData())
        ;
    }
}
