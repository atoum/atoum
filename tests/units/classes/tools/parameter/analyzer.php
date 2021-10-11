<?php

namespace atoum\atoum\tests\units\tools\parameter;

require_once __DIR__ . '/../../../runner.php';

use atoum\atoum
;

class analyzer extends atoum\test
{
    public function getTypeHintStringForUntypedParameter()
    {
        $this
            ->if($analyzer = new testedClass())
            ->and($reflectionParameterController = new mock\controller())
            ->and($reflectionParameterController->__construct = function () {
            })
            ->and($reflectionParameterController->getName = 'param')
            ->and($reflectionParameterController->isPassedByReference = false)
            ->and($reflectionParameterController->isDefaultValueAvailable = false)
            ->and($reflectionParameterController->isOptional = false)
            ->and($reflectionParameterController->isVariadic = false)
            ->and($reflectionParameterController->allowsNull = false)
            ->and($reflectionParameterController->hasType = false)
            ->and($reflectionParameterController->getType = null)
            ->and($reflectionParameter = new \mock\reflectionParameter([uniqid(), uniqid()], 0))
            ->then
                ->string($analyzer->getTypeHintString($reflectionParameter))->isEqualTo('')
        ;
    }

    public function getTypeHintStringForBuiltIn()
    {
        $this
            ->if($analyzer = new testedClass())
            ->and($reflectionNamedTypeController = new mock\controller())
            ->and($reflectionNamedTypeController->__construct = function () {
            })
            ->and($reflectionNamedTypeController->getName = 'string')
            ->and($reflectionNamedTypeController->isBuiltin = true)
            ->and($reflectionNamedType = new \mock\reflectionNamedType())
            ->and($reflectionParameterController = new mock\controller())
            ->and($reflectionParameterController->__construct = function () {
            })
            ->and($reflectionParameterController->getName = 'param')
            ->and($reflectionParameterController->isPassedByReference = false)
            ->and($reflectionParameterController->isDefaultValueAvailable = false)
            ->and($reflectionParameterController->isOptional = false)
            ->and($reflectionParameterController->isVariadic = false)
            ->and($reflectionParameterController->allowsNull = false)
            ->and($reflectionParameterController->hasType = true)
            ->and($reflectionParameterController->getType = $reflectionNamedType)
            ->and($reflectionParameter = new \mock\reflectionParameter([uniqid(), uniqid()], 0))
            ->then
                ->string($analyzer->getTypeHintString($reflectionParameter))->isEqualTo('string')

            ->if($reflectionNamedTypeController->getName = 'bool')
            ->then
                ->string($analyzer->getTypeHintString($reflectionParameter))->isEqualTo('bool')

            ->if($reflectionNamedTypeController->getName = 'int')
            ->then
                ->string($analyzer->getTypeHintString($reflectionParameter))->isEqualTo('int')

            ->if($reflectionNamedTypeController->getName = 'float')
            ->then
                ->string($analyzer->getTypeHintString($reflectionParameter))->isEqualTo('float')

            ->if($reflectionNamedTypeController->getName = 'array')
            ->then
                ->string($analyzer->getTypeHintString($reflectionParameter))->isEqualTo('array')

            ->if($reflectionNamedTypeController->getName = 'callable')
            ->then
                ->string($analyzer->getTypeHintString($reflectionParameter))->isEqualTo('callable')
        ;
    }

    public function getTypeHintStringForNullableBuiltIn()
    {
        $this
            ->if($analyzer = new testedClass())
            ->and($reflectionNamedTypeController = new mock\controller())
            ->and($reflectionNamedTypeController->__construct = function () {
            })
            ->and($reflectionNamedTypeController->getName = 'string')
            ->and($reflectionNamedTypeController->isBuiltin = true)
            ->and($reflectionNamedType = new \mock\reflectionNamedType())
            ->and($reflectionParameterController = new mock\controller())
            ->and($reflectionParameterController->__construct = function () {
            })
            ->and($reflectionParameterController->getName = 'param')
            ->and($reflectionParameterController->isPassedByReference = false)
            ->and($reflectionParameterController->isDefaultValueAvailable = false)
            ->and($reflectionParameterController->isOptional = false)
            ->and($reflectionParameterController->isVariadic = false)
            ->and($reflectionParameterController->allowsNull = true)
            ->and($reflectionParameterController->hasType = true)
            ->and($reflectionParameterController->getType = $reflectionNamedType)
            ->and($reflectionParameter = new \mock\reflectionParameter([uniqid(), uniqid()], 0))
            ->then
                ->string($analyzer->getTypeHintString($reflectionParameter))->isEqualTo('?string')

            ->if($reflectionNamedTypeController->getName = 'bool')
            ->then
                ->string($analyzer->getTypeHintString($reflectionParameter))->isEqualTo('?bool')

            ->if($reflectionNamedTypeController->getName = 'int')
            ->then
                ->string($analyzer->getTypeHintString($reflectionParameter))->isEqualTo('?int')

            ->if($reflectionNamedTypeController->getName = 'float')
            ->then
                ->string($analyzer->getTypeHintString($reflectionParameter))->isEqualTo('?float')

            ->if($reflectionNamedTypeController->getName = 'array')
            ->then
                ->string($analyzer->getTypeHintString($reflectionParameter))->isEqualTo('?array')

            ->if($reflectionNamedTypeController->getName = 'callable')
            ->then
                ->string($analyzer->getTypeHintString($reflectionParameter))->isEqualTo('?callable')
        ;
    }

    public function getTypeHintStringForSelf()
    {
        $this
            ->if($analyzer = new testedClass())
            ->and($class = '\\Foo\\Bar')
            ->and($reflectionNamedTypeController = new mock\controller())
            ->and($reflectionNamedTypeController->__construct = function () {
            })
            ->and($reflectionNamedTypeController->getName = 'self')
            ->and($reflectionNamedTypeController->isBuiltin = false)
            ->and($reflectionNamedType = new \mock\reflectionNamedType())
            ->and($reflectionSelfClassController = new mock\controller())
            ->and($reflectionSelfClassController->__construct = function () {
            })
            ->and($reflectionSelfClassController->getName = $class)
            ->and($reflectionSelfClass = new \mock\reflectionClass(\reflectionClass::class))
            ->and($reflectionParameterController = new mock\controller())
            ->and($reflectionParameterController->__construct = function () {
            })
            ->and($reflectionParameterController->getName = 'param')
            ->and($reflectionParameterController->isPassedByReference = false)
            ->and($reflectionParameterController->isDefaultValueAvailable = false)
            ->and($reflectionParameterController->isOptional = false)
            ->and($reflectionParameterController->isVariadic = false)
            ->and($reflectionParameterController->allowsNull = false)
            ->and($reflectionParameterController->hasType = true)
            ->and($reflectionParameterController->getType = $reflectionNamedType)
            ->and($reflectionParameterController->getDeclaringClass = $reflectionSelfClass)
            ->and($reflectionParameter = new \mock\reflectionParameter([uniqid(), uniqid()], 0))
            ->then
                ->string($analyzer->getTypeHintString($reflectionParameter))->isEqualTo($class)

            ->if($reflectionNamedTypeController->allowsNull = true)
            ->then
                ->string($analyzer->getTypeHintString($reflectionParameter))->isEqualTo('?' . $class)
        ;
    }

    /** @php >= 8.0 */
    public function getTypeHintStringForMixed()
    {
        $this
            ->if($analyzer = new testedClass())
            ->and($reflectionNamedTypeController = new mock\controller())
            ->and($reflectionNamedTypeController->__construct = function () {
            })
            ->and($reflectionNamedTypeController->getName = 'mixed')
            ->and($reflectionNamedTypeController->isBuiltin = true)
            ->and($reflectionNamedType = new \mock\reflectionNamedType())
            ->and($reflectionParameterController = new mock\controller())
            ->and($reflectionParameterController->__construct = function () {
            })
            ->and($reflectionParameterController->getName = 'param')
            ->and($reflectionParameterController->isPassedByReference = false)
            ->and($reflectionParameterController->isDefaultValueAvailable = false)
            ->and($reflectionParameterController->isOptional = false)
            ->and($reflectionParameterController->isVariadic = false)
            ->and($reflectionParameterController->allowsNull = true) // mixed always allows null
            ->and($reflectionParameterController->hasType = true)
            ->and($reflectionParameterController->getType = $reflectionNamedType)
            ->and($reflectionParameter = new \mock\reflectionParameter([uniqid(), uniqid()], 0))
            ->then
                ->string($analyzer->getTypeHintString($reflectionParameter))->isEqualTo('mixed')
        ;
    }

    /** @php >= 8.0 */
    public function getTypeHintStringForUnionType()
    {
        $this
            ->if($analyzer = new testedClass())
            ->and($typeController1 = new mock\controller())
            ->and($typeController1->__construct = function () {
            })
            ->and($typeController1->getName = 'string')
            ->and($typeController1->isBuiltin = true)
            ->and($type1 = new \mock\reflectionNamedType())
            ->and($typeController2 = new mock\controller())
            ->and($typeController2->__construct = function () {
            })
            ->and($typeController2->getName = 'array')
            ->and($typeController2->isBuiltin = true)
            ->and($type2 = new \mock\reflectionNamedType())
            ->and($unionTypeController = new mock\controller())
            ->and($this->calling($unionTypeController)->getTypes = [$type1, $type2])
            ->and($this->calling($unionTypeController)->allowsNull = false)
            ->and($unionType = new \mock\reflectionUnionType())
            ->and($reflectionParameterController = new mock\controller())
            ->and($reflectionParameterController->__construct = function () {
            })
            ->and($reflectionParameterController->getName = 'param')
            ->and($reflectionParameterController->isPassedByReference = false)
            ->and($reflectionParameterController->isDefaultValueAvailable = false)
            ->and($reflectionParameterController->isOptional = false)
            ->and($reflectionParameterController->isVariadic = false)
            ->and($reflectionParameterController->allowsNull = false)
            ->and($reflectionParameterController->hasType = true)
            ->and($reflectionParameterController->getType = $unionType)
            ->and($reflectionParameter = new \mock\reflectionParameter([uniqid(), uniqid()], 0))
            ->then
                ->string($analyzer->getTypeHintString($reflectionParameter))->isEqualTo('string|array')

            ->if($reflectionParameterController->allowsNull = true)
            ->then
                ->string($analyzer->getTypeHintString($reflectionParameter))->isEqualTo('string|array')
        ;
    }
}
