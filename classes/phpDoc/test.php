<?php

namespace mageekguy\atoum\phpDoc;

/**
 * @method \mageekguy\atoum\test when($mixed)
 * @method \mageekguy\atoum\test assert($case = null)
 * @method \mageekguy\atoum\test\mock\generator mockGenerator()
 * @method \mageekguy\atoum\test mockClass($class, $mockNamespace = null, $mockClass = null)
 * @method \mageekguy\atoum\test mock\mageekguy\atoum\testedClass($mockNamespace = null, $mockClass = null)
 * @method \mageekguy\atoum\test dump()
 * @method \mageekguy\atoum\test stop()
 * @method \mageekguy\atoum\test if()
 * @method \mageekguy\atoum\test and()
 * @method \mageekguy\atoum\test then()
 * @method \mageekguy\atoum\test given()
 * @method \mageekguy\atoum\mock\controller calling(mock\aggregator $mock)
 * @method \mageekguy\atoum\mock\controller ƒ(mock\aggregator $mock)
 * @method mixed define($asserterGenerator)
 *
 * @method \mageekguy\atoum\asserters\adapter adapter($adapter)
 * @method \mageekguy\atoum\asserters\boolean boolean($value, $label = null)
 * @method \mageekguy\atoum\asserters\castToString castToString($value, $label = null, $charlist = null, $checkType = true)
 * @method \mageekguy\atoum\asserters\dateInterval dateInterval($value, $checkType = true)
 * @method \mageekguy\atoum\asserters\dateTime dateTime($value, $checkType = true)
 * @method \mageekguy\atoum\asserters\error error($message = null, $type = null)
 * @method \mageekguy\atoum\asserters\exception exception($value, $label = null, $check = true)
 * @method \mageekguy\atoum\asserters\extension extension($name)
 * @method \mageekguy\atoum\asserters\float float($value, $label = null)
 * @method \mageekguy\atoum\asserters\hash hash($value, $label = null, $charlist = null, $checkType = true)
 * @method \mageekguy\atoum\asserters\integer integer($value, $label = null)
 * @method \mageekguy\atoum\asserters\mock mock($mock)
 * @method \mageekguy\atoum\asserters\mysqlDateTime mysqlDateTime($value, $checkType = true)
 * @method \mageekguy\atoum\asserters\object object($value, $checkType = true)
 * @method \mageekguy\atoum\asserters\output output($value = null, $label = null, $charlist = null, $checkType = true)
 * @method \mageekguy\atoum\asserters\phpArray phpArray($value, $label = null)
 * @method \mageekguy\atoum\asserters\phpArray array($value, $label = null)
 * @method \mageekguy\atoum\asserters\phpClass phpClass($class)
 * @method \mageekguy\atoum\asserters\phpClass class($class)
 * @method \mageekguy\atoum\asserters\sizeOf sizeOf($value, $label = null)
 * @method \mageekguy\atoum\asserters\stream stream($stream)
 * @method \mageekguy\atoum\asserters\string string($value, $label = null, $charlist = null, $checkType = true)
 * @method \mageekguy\atoum\asserters\testedClass testedClass($class)
 * @method \mageekguy\atoum\asserters\utf8String utf8String($value, $label = null, $charlist = null, $checkType = true)
 * @method \mageekguy\atoum\asserters\variable variable($value)
 *
 * @property \mageekguy\atoum\test $if
 * @property \mageekguy\atoum\test $and
 * @property \mageekguy\atoum\test $then
 * @property \mageekguy\atoum\test $given
 * @property \mageekguy\atoum\test $assert
 * @property \mageekguy\atoum\asserters\testedClass $testedClass
 * @property \mageekguy\atoum\asserters\string $string
 * @property \mageekguy\atoum\asserters\output $output
 * @property \mageekguy\atoum\asserters\error $error
 */
interface test {}
