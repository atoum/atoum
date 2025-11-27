<?php

namespace atoum\atoum\tests\units\php84;

use atoum\atoum;
use atoum\atoum\attributes\Php;
use atoum\atoum\tests\units\php84\fixtures\ArrayFunctions;

/**
 * Tests for PHP 8.4 Array Functions
 *
 */
#[Php('8.4')]
class ArrayFunctionsTest extends atoum
{
    public function testFindFirstUser()
    {
        $this
            ->given($examples = new ArrayFunctions())
            ->and($users = [
                ['id' => 1, 'name' => 'Alice', 'role' => 'user'],
                ['id' => 2, 'name' => 'Bob', 'role' => 'admin'],
                ['id' => 3, 'name' => 'Charlie', 'role' => 'user'],
            ])

            ->when($admin = $examples->findFirstUser($users, 'admin'))
            ->then
                ->array($admin)
                    ->hasKey('id')
                    ->string['name']->isEqualTo('Bob')
                    ->string['role']->isEqualTo('admin')

            ->when($moderator = $examples->findFirstUser($users, 'moderator'))
            ->then
                ->variable($moderator)->isNull()
        ;
    }

    public function testFindUserIndex()
    {
        $this
            ->given($examples = new ArrayFunctions())
            ->and($users = [
                ['id' => 10, 'name' => 'Alice'],
                ['id' => 20, 'name' => 'Bob'],
                ['id' => 30, 'name' => 'Charlie'],
            ])

            ->when($index = $examples->findUserIndex($users, 20))
            ->then
                ->integer($index)->isEqualTo(1)

            ->when($notFound = $examples->findUserIndex($users, 999))
            ->then
                ->variable($notFound)->isNull()
        ;
    }

    public function testHasAdminUser()
    {
        $this
            ->given($examples = new ArrayFunctions())
            // With admin
            ->and($usersWithAdmin = [
                ['role' => 'user'],
                ['role' => 'admin'],
                ['role' => 'user'],
            ])
            ->when($result = $examples->hasAdminUser($usersWithAdmin))
            ->then
                ->boolean($result)->isTrue()

            // Without admin
            ->and($usersWithoutAdmin = [
                ['role' => 'user'],
                ['role' => 'moderator'],
            ])
            ->when($result = $examples->hasAdminUser($usersWithoutAdmin))
            ->then
                ->boolean($result)->isFalse()
        ;
    }

    public function testAllUsersActive()
    {
        $this
            ->given($examples = new ArrayFunctions())
            // All active
            ->and($allActive = [
                ['active' => true],
                ['active' => true],
                ['active' => true],
            ])
            ->when($result = $examples->allUsersActive($allActive))
            ->then
                ->boolean($result)->isTrue()

            // One inactive
            ->and($someInactive = [
                ['active' => true],
                ['active' => false],
                ['active' => true],
            ])
            ->when($result = $examples->allUsersActive($someInactive))
            ->then
                ->boolean($result)->isFalse()
        ;
    }

    public function testValidateForm()
    {
        $this
            ->given($examples = new ArrayFunctions())
            // Valid form
            ->and($validFields = [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'age' => '30',
            ])
            ->when($errors = $examples->validateForm($validFields))
            ->then
                ->array($errors)->isEmpty()

            // Form with empty field
            ->and($emptyFields = [
                'name' => 'John',
                'email' => '',
                'age' => '30',
            ])
            ->when($errors = $examples->validateForm($emptyFields))
            ->then
                ->array($errors)
                    ->isNotEmpty()
                    ->contains('Some fields are empty')

            // Form with invalid email
            ->and($invalidEmail = [
                'name' => 'John',
                'email' => 'invalid-email',
                'age' => '30',
            ])
            ->when($errors = $examples->validateForm($invalidEmail))
            ->then
                ->array($errors)
                    ->isNotEmpty()
                    ->hasSize(1)
                    ->string[0]->contains('Invalid email')
        ;
    }

    public function testGetFirstExpiredItem()
    {
        $this
            ->given($examples = new ArrayFunctions())
            // With expired items
            ->and($past = (new \DateTime())->modify('-1 day'))
            ->and($future = (new \DateTime())->modify('+1 day'))
            ->and($items = [
                (object) ['name' => 'Valid1', 'expiresAt' => $future],
                (object) ['name' => 'Expired1', 'expiresAt' => $past],
                (object) ['name' => 'Expired2', 'expiresAt' => $past],
            ])

            ->when($expired = $examples->getFirstExpiredItem($items))
            ->then
                ->object($expired)
                    ->string['name']->isEqualTo('Expired1')

            // Without expired items
            ->and($allValid = [
                (object) ['name' => 'Valid1', 'expiresAt' => $future],
                (object) ['name' => 'Valid2', 'expiresAt' => $future],
            ])
            ->when($expired = $examples->getFirstExpiredItem($allValid))
            ->then
                ->variable($expired)->isNull()
        ;
    }

    public function testPerformanceExample()
    {
        $this
            ->given($examples = new ArrayFunctions())
            ->and($largeArray = range(1, 10000))

            ->when($result = $examples->performanceExample($largeArray))
            ->then
                ->integer($result)->isGreaterThan(1000)
                ->integer($result)->isEqualTo(1001)
        ;
    }
}
