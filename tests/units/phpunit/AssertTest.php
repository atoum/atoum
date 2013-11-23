<?php
/**
 * PHPUnit
 *
 * Copyright (c) 2001-2013, Sebastian Bergmann <sebastian@phpunit.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    PHPUnit
 * @author     Sebastian Bergmann <sebastian@phpunit.de>
 * @author     Bernhard Schussek <bschussek@2bepublished.at>
 * @copyright  2001-2013 Sebastian Bergmann <sebastian@phpunit.de>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link       http://www.phpunit.de/
 * @since      File available since Release 2.0.0
 */

namespace {
	class ParentClassWithPrivateAttributes
	{
		private static $privateStaticParentAttribute = 'foo';
		private $privateParentAttribute = 'bar';
	}

	class ParentClassWithProtectedAttributes extends ParentClassWithPrivateAttributes
	{
		protected static $protectedStaticParentAttribute = 'foo';
		protected $protectedParentAttribute = 'bar';
	}

	class ClassWithNonPublicAttributes extends ParentClassWithProtectedAttributes
	{
		public static $publicStaticAttribute = 'foo';
		protected static $protectedStaticAttribute = 'bar';
		protected static $privateStaticAttribute = 'baz';

		public $publicAttribute = 'foo';
		public $foo = 1;
		public $bar = 2;
		protected $protectedAttribute = 'bar';
		protected $privateAttribute = 'baz';

		public $publicArray = array('foo');
		protected $protectedArray = array('bar');
		protected $privateArray = array('baz');
	}

	class SampleClass
	{
		public $a;
		protected $b;
		protected $c;

		public function __construct($a, $b, $c)
		{
			$this->a = $a;
			$this->b = $b;
			$this->c = $c;
		}
	}

	class Struct
	{
		public $var;

		public function __construct($var)
		{
			$this->var = $var;
		}
	}

	class ClassWithToString
	{
		public function __toString()
		{
			return 'string representation';
		}
	}

	class SampleArrayAccess implements ArrayAccess
	{
		private $container;

		public function __construct() {
			$this->container = array();
		}
		public function offsetSet($offset, $value) {
			if (is_null($offset)) {
				$this->container[] = $value;
			} else {
				$this->container[$offset] = $value;
			}
		}
		public function offsetExists($offset) {
			return isset($this->container[$offset]);
		}
		public function offsetUnset($offset) {
			unset($this->container[$offset]);
		}
		public function offsetGet($offset) {
			return isset($this->container[$offset]) ? $this->container[$offset] : null;
		}
	}

	class TestIterator implements Iterator
	{
		protected $array;
		protected $position;

		public function __construct($array = array())
		{
			$this->array = $array;
		}

		public function rewind()
		{
			$this->position = 0;
		}

		public function valid()
		{
			return $this->position < count($this->array);
		}

		public function key()
		{
			return $this->position;
		}

		public function current()
		{
			return $this->array[$this->position];
		}

		public function next()
		{
			$this->position++;
		}
	}

	class Book
	{
		// the order of properties is important for testing the cycle!
		public $author = NULL;
	}

	class Author
	{
		// the order of properties is important for testing the cycle!
		public $books = array();

		private $name = '';

		public function __construct($name)
		{
			$this->name = $name;
		}
	}
}

namespace mageekguy\atoum\phpunit {
    class Framework_Assert {}
}

namespace mageekguy\atoum\tests\phpunit {
    use mock\mageekguy\atoum\test\phpunit\test as testedClass;

	require_once __DIR__ . '/bootstrap.php';

	class Framework_AssertTest extends \PHPUnit_Framework_TestCase
	{
		protected $filesDirectory;
		protected $html;

		public function setUp()
		{
			$this->filesDirectory = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR;

			$this->html = file_get_contents(
				$this->filesDirectory . 'SelectorAssertionsFixture.html'
			);
		}

        public function beforeTestMethod($testMethod)
        {
            parent::beforeTestMethod($testMethod);

            $this->setUp();
        }

		public function testFail()
		{
			$this
				->if($test = new testedClass())
				->then
					->exception(function() use ($test) {
							$test->fail();
						}
					)
						->isInstanceOf('PHPUnit_Framework_AssertionFailedError')
			;
		}

		public function testAssertSplObjectStorageContainsObject()
		{
			$this
				->given(
					$a = new \stdClass(),
					$b = new \stdClass(),
					$c = new \SplObjectStorage(),
					$c->attach($a)
				)
				->if($test = new testedClass())
				->then
					->object($test->assertContains($a, $c))
					->exception(function() use ($test, $b, $c) {
							$test->assertContains($b, $c);
						}
					)
						->isInstanceOf('PHPUnit_Framework_AssertionFailedError')
			;
		}

		public function testAssertArrayContainsObject()
		{
			$this
				->given(
					$a = new \stdClass,
					$b = new \stdClass
				)
				->if($test = new testedClass())
				->then
					->object($test->assertContains($a, array($a)))
					->exception(function() use ($test, $a, $b) {
							$test->assertContains($a, array($b));
						}
					)
						->isInstanceOf('PHPUnit_Framework_AssertionFailedError')
			;
		}

		public function testAssertArrayContainsString()
		{
			$this
				->if($test = new testedClass())
				->then
					->object($test->assertContains('foo', array('foo')))
					->exception(function() use ($test) {
							$test->assertContains('foo', array('bar'));
						}
					)
						->isInstanceOf('PHPUnit_Framework_AssertionFailedError')
			;
		}
		/**
		 * @covers PHPUnit_Framework_Assert::assertContainsOnlyInstancesOf
		 */
		public function testAssertContainsOnlyInstancesOf()
		{
			$test = new testedClass();
			$data = array(
				new \Book(),
				new \Book
			);
			$test->assertContainsOnlyInstancesOf('Book', $data);
			$test->assertContainsOnlyInstancesOf('stdClass', array(new \stdClass()));

			$data = array(
				new \Author('Test')
			);
			try {
				$test->assertContainsOnlyInstancesOf('Book', $data);
			} catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}
			$this->fail();
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertArrayHasKey
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertArrayHasKeyThrowsException()
		{
			$test = new testedClass();

			$test->assertArrayHasKey(NULL, array());
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertArrayHasKey
		 */
		public function testAssertArrayHasIntegerKey()
		{
			$test = new testedClass();

			$test->assertArrayHasKey(0, array('foo'));

			try {
				$test->assertArrayHasKey(1, array('foo'));
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertArrayNotHasKey
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertArrayNotHasKeyThrowsException()
		{
			$test = new testedClass();

			$test->assertArrayNotHasKey(NULL, array());
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertArrayNotHasKey
		 */
		public function testAssertArrayNotHasIntegerKey()
		{
			$test = new testedClass();

			$test->assertArrayNotHasKey(1, array('foo'));

			try {
				$test->assertArrayNotHasKey(0, array('foo'));
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertArrayHasKey
		 */
		public function testAssertArrayHasStringKey()
		{
			$test = new testedClass();

			$test->assertArrayHasKey('foo', array('foo' => 'bar'));

			try {
				$test->assertArrayHasKey('bar', array('foo' => 'bar'));
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertArrayNotHasKey
		 */
		public function testAssertArrayNotHasStringKey()
		{
			$test = new testedClass();

			$test->assertArrayNotHasKey('bar', array('foo' => 'bar'));

			try {
				$test->assertArrayNotHasKey('foo', array('foo' => 'bar'));
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertArrayHasKey
		 */
		public function testAssertArrayHasKeyAcceptsArrayObjectValue()
		{
			$test = new testedClass();

			$array = new \ArrayObject();
			$array['foo'] = 'bar';
			$test->assertArrayHasKey('foo', $array);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertArrayHasKey
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertArrayHasKeyProperlyFailsWithArrayObjectValue()
		{
			$test = new testedClass();

			$array = new \ArrayObject();
			$array['bar'] = 'bar';
			$test->assertArrayHasKey('foo', $array);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertArrayHasKey
		 */
		public function testAssertArrayHasKeyAcceptsArrayAccessValue()
		{
			$test = new testedClass();

			$array = new \SampleArrayAccess();
			$array['foo'] = 'bar';
			$test->assertArrayHasKey('foo', $array);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertArrayHasKey
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertArrayHasKeyProperlyFailsWithArrayAccessValue()
		{
			$test = new testedClass();

			$array = new \SampleArrayAccess();
			$array['bar'] = 'bar';
			$test->assertArrayHasKey('foo', $array);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertArrayNotHasKey
		 */
		public function testAssertArrayNotHasKeyAcceptsArrayAccessValue()
		{
			$test = new testedClass();

			$array = new \ArrayObject();
			$array['foo'] = 'bar';
			$test->assertArrayNotHasKey('bar', $array);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertArrayNotHasKey
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertArrayNotHasKeyPropertlyFailsWithArrayAccessValue()
		{
			$test = new testedClass();

			$array = new \ArrayObject();
			$array['bar'] = 'bar';
			$test->assertArrayNotHasKey('bar', $array);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertContains
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertContainsThrowsException()
		{
			$test = new testedClass();

			$test->assertContains(NULL, NULL);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertContains
		 */
		public function testAssertIteratorContainsObject()
		{
			$test = new testedClass();
			$foo = new \stdClass;

			$test->assertContains($foo, new \TestIterator(array($foo)));

			try {
				$test->assertContains($foo, new \TestIterator(array(new \stdClass)));
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertContains
		 */
		public function testAssertIteratorContainsString()
		{
			$test = new testedClass();

			$test->assertContains('foo', new \TestIterator(array('foo')));

			try {
				$test->assertContains('foo', new \TestIterator(array('bar')));
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertContains
		 */
		public function testAssertStringContainsString()
		{
			$test = new testedClass();

			$test->assertContains('foo', 'foobar');

			try {
				$test->assertContains('foo', 'bar');
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertNotContains
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertNotContainsThrowsException()
		{
			$test = new testedClass();

			$test->assertNotContains(NULL, NULL);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertNotContains
		 */
		public function testAssertSplObjectStorageNotContainsObject()
		{
			$test = new testedClass();

			$a = new \stdClass;
			$b = new \stdClass;
			$c = new \SplObjectStorage;
			$c->attach($a);

			$test->assertNotContains($b, $c);

			try {
				$test->assertNotContains($a, $c);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertNotContains
		 */
		public function testAssertArrayNotContainsObject()
		{
			$test = new testedClass();

			$a = new \stdClass;
			$b = new \stdClass;

			$test->assertNotContains($a, array($b));

			try {
				$test->assertNotContains($a, array($a));
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertNotContains
		 */
		public function testAssertArrayNotContainsString()
		{
			$test = new testedClass();

			$test->assertNotContains('foo', array('bar'));

			try {
				$test->assertNotContains('foo', array('foo'));
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertNotContains
		 */
		public function testAssertStringNotContainsString()
		{
			$test = new testedClass();

			$test->assertNotContains('foo', 'bar');

			try {
				$test->assertNotContains('foo', 'foo');
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertContainsOnly
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertContainsOnlyThrowsException()
		{
			$test = new testedClass();

			$test->assertContainsOnly(NULL, NULL);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertContainsOnlyInstancesOf
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertContainsOnlyInstancesOfThrowsException()
		{
			$test = new testedClass();

			$test->assertContainsOnlyInstancesOf(NULL, NULL);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertContainsOnly
		 */
		public function testAssertArrayContainsOnlyIntegers()
		{
			$test = new testedClass();

			$test->assertContainsOnly('integer', array(1, 2, 3));

			try {
				$test->assertContainsOnly('integer', array("1", 2, 3));
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertNotContainsOnly
		 */
		public function testAssertArrayNotContainsOnlyIntegers()
		{
			$test = new testedClass();

			$test->assertNotContainsOnly('integer', array("1", 2, 3));

			try {
				$test->assertNotContainsOnly('integer', array(1, 2, 3));
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertContainsOnly
		 */
		public function testAssertArrayContainsOnlyStdClass()
		{
			$test = new testedClass();

			$test->assertContainsOnly('StdClass', array(new \StdClass));

			try {
				$test->assertContainsOnly('StdClass', array('StdClass'));
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertNotContainsOnly
		 */
		public function testAssertArrayNotContainsOnlyStdClass()
		{
			$test = new testedClass();

			$test->assertNotContainsOnly('StdClass', array('StdClass'));

			try {
				$test->assertNotContainsOnly('StdClass', array(new \StdClass));
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		protected function createDOMDocument($content)
		{
			$document = new \DOMDocument;
			$document->preserveWhiteSpace = FALSE;
			$document->loadXML($content);

			return $document;
		}

		protected function sameValues()
		{
			$object = new \SampleClass(4, 8, 15);
			// cannot use $filesDirectory, because neither setUp() nor
			// setUpBeforeClass() are executed before the data providers
			$file = $this->filesDirectory . 'foo.xml';
			$resource = fopen($file, 'r');

			return array(
				// NULL
				array(NULL, NULL),
				// strings
				array('a', 'a'),
				// integers
				array(0, 0),
				// floats
				array(2.3, 2.3),
				//array(1/3, 1 - 2/3),
				array(log(0), log(0)),
				// arrays
				array(array(), array()),
				array(array(0 => 1), array(0 => 1)),
				array(array(0 => NULL), array(0 => NULL)),
				array(array('a', 'b' => array(1, 2)), array('a', 'b' => array(1, 2))),
				// objects
				array($object, $object),
				// resources
				array($resource, $resource),
			);
		}

		protected function notEqualValues()
		{
			// cyclic dependencies
			$book1 = new \Book;
			$book1->author = new \Author('Terry Pratchett');
			$book1->author->books[] = $book1;
			$book2 = new \Book;
			$book2->author = new \Author('Terry Pratch');
			$book2->author->books[] = $book2;

			$book3 = new \Book;
			$book3->author = 'Terry Pratchett';
			$book4 = new \stdClass;
			$book4->author = 'Terry Pratchett';

			$object1 = new \SampleClass( 4,  8, 15);
			$object2 = new \SampleClass(16, 23, 42);
			$object3 = new \SampleClass( 4,  8, 15);
			$storage1 = new \SplObjectStorage;
			$storage1->attach($object1);
			$storage2 = new \SplObjectStorage;
			$storage2->attach($object3); // same content, different object

			// cannot use $filesDirectory, because neither setUp() nor
			// setUpBeforeClass() are executed before the data providers
			$file = $this->filesDirectory . 'foo.xml';

			return array(
				// strings
				array('a', 'b'),
				array('a', 'A'),
				// integers
				array(1, 2),
				array(2, 1),
				// floats
				array(2.3, 4.2),
				//array(2.3, 4.2, 0.5),
				//array(array(2.3), array(4.2), 0.5),
				array(array(array(2.3)), array(array(4.2)), 0.5),
				array(new \Struct(2.3), new \Struct(4.2), 0.5),
				array(array(new \Struct(2.3)), array(new \Struct(4.2)), 0.5),
				// NAN
				array(NAN, NAN),
				// arrays
				array(array(), array(0 => 1)),
				array(array(0 => 1), array()),
				array(array(0 => NULL), array()),
				array(array(0 => 1, 1 => 2), array(0 => 1, 1 => 3)),
				array(array('a', 'b' => array(1, 2)), array('a', 'b' => array(2, 1))),
				// objects
				array(new \SampleClass(4, 8, 15), new \SampleClass(16, 23, 42)),
				array($object1, $object2),
				//array($book1, $book2),
				array($book3, $book4), // same content, different class
				// resources
				array(fopen($file, 'r'), fopen($file, 'r')),
				// SplObjectStorage
				array($storage1, $storage2),
				// DOMDocument
				array(
					$this->createDOMDocument('<root></root>'),
					$this->createDOMDocument('<bar/>'),
				),
				array(
					$this->createDOMDocument('<foo attr1="bar"/>'),
					$this->createDOMDocument('<foo attr1="foobar"/>'),
				),
				array(
					$this->createDOMDocument('<foo> bar </foo>'),
					$this->createDOMDocument('<foo />'),
				),
				array(
					$this->createDOMDocument('<foo xmlns="urn:myns:bar"/>'),
					$this->createDOMDocument('<foo xmlns="urn:notmyns:bar"/>'),
				),
				array(
					$this->createDOMDocument('<foo> bar </foo>'),
					$this->createDOMDocument('<foo> bir </foo>'),
				),
				// Exception
				array(new \Exception('Exception 1'), new \Exception('Exception 2')),
				// different types
				array(new \SampleClass(4, 8, 15), FALSE),
				array(FALSE, new \SampleClass(4, 8, 15)),
				array(array(0 => 1, 1 => 2), FALSE),
				array(FALSE, array(0 => 1, 1 => 2)),
				array(array(), new \stdClass),
				array(new \stdClass, array()),
				// PHP: 0 == 'Foobar' => TRUE!
				// We want these values to differ
				array(0, 'Foobar'),
				array('Foobar', 0),
				array(3, acos(8)),
				array(acos(8), 3)
			);
		}

		protected function equalValues()
		{
			// cyclic dependencies
			$book1 = new \Book;
			$book1->author = new \Author('Terry Pratchett');
			$book1->author->books[] = $book1;
			$book2 = new \Book;
			$book2->author = new \Author('Terry Pratchett');
			$book2->author->books[] = $book2;

			$object1 = new \SampleClass(4, 8, 15);
			$object2 = new \SampleClass(4, 8, 15);
			$storage1 = new \SplObjectStorage;
			$storage1->attach($object1);
			$storage2 = new \SplObjectStorage;
			$storage2->attach($object1);

			return array(
				// strings
				array('a', 'A', 0, FALSE, TRUE), // ignore case
				// arrays
				array(array('a' => 1, 'b' => 2), array('b' => 2, 'a' => 1)),
				array(array(1), array('1')),
				array(array(3, 2, 1), array(2, 3, 1), 0, TRUE), // canonicalized comparison
				// floats
				array(2.3, 2.5, 0.5),
				//array(array(2.3), array(2.5), 0.5),
				//array(array(array(2.3)), array(array(2.5)), 0.5),
				//array(new \Struct(2.3), new \Struct(2.5), 0.5),
				//array(array(new \Struct(2.3)), array(new \Struct(2.5)), 0.5),
				// numeric with delta
				array(1, 2, 1),
				// objects
				array($object1, $object2),
				//array($book1, $book2),
				// SplObjectStorage
				array($storage1, $storage2),
				// DOMDocument
				array(
					$this->createDOMDocument('<root></root>'),
					$this->createDOMDocument('<root/>'),
				),
				array(
					$this->createDOMDocument('<root attr="bar"></root>'),
					$this->createDOMDocument('<root attr="bar"/>'),
				),
				array(
					$this->createDOMDocument('<root><foo attr="bar"></foo></root>'),
					$this->createDOMDocument('<root><foo attr="bar"/></root>'),
				),
				array(
					$this->createDOMDocument("<root>\n  <child/>\n</root>"),
					$this->createDOMDocument('<root><child/></root>'),
				),
				// Exception
				array(new \Exception('Exception 1'), new \Exception('Exception 1')),
				// mixed types
				array(0, '0'),
				array('0', 0),
				array(2.3, '2.3'),
				array('2.3', 2.3),
				//array((string)(1/3), 1 - 2/3),
				//array(1/3, (string)(1 - 2/3)),
				array('string representation', new \ClassWithToString),
				array(new \ClassWithToString, 'string representation'),
			);
		}

		public function equalProvider()
		{
			// same |= equal
			return array_merge($this->equalValues(), $this->sameValues());
		}

		public function notEqualProvider()
		{
			return $this->notEqualValues();
		}

		public function sameProvider()
		{
			return $this->sameValues();
		}

		public function notSameProvider()
		{
			// not equal |= not same
			// equal, ¬same |= not same
			return array_merge($this->notEqualValues(), $this->equalValues());
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertEquals
		 * @dataProvider equalProvider
		 */
		public function testAssertEqualsSucceeds($a, $b, $delta = 0, $canonicalize = FALSE, $ignoreCase = FALSE)
		{
			$test = new testedClass();

			$test->assertEquals($a, $b, '', $delta, 10, $canonicalize, $ignoreCase);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertEquals
		 * @dataProvider notEqualProvider
		 */
		public function testAssertEqualsFails($a, $b, $delta = 0, $canonicalize = FALSE, $ignoreCase = FALSE)
		{
			$test = new testedClass();

			try {
				$test->assertEquals($a, $b, '', $delta, 10, $canonicalize, $ignoreCase);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			var_dump($a, $b);
			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertNotEquals
		 * @dataProvider notEqualProvider
		 */
		public function testAssertNotEqualsSucceeds($a, $b, $delta = 0, $canonicalize = FALSE, $ignoreCase = FALSE)
		{
			$test = new testedClass();

			$test->assertNotEquals($a, $b, '', $delta, 10, $canonicalize, $ignoreCase);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertNotEquals
		 * @dataProvider equalProvider
		 */
		public function testAssertNotEqualsFails($a, $b, $delta = 0, $canonicalize = FALSE, $ignoreCase = FALSE)
		{
			$test = new testedClass();

			try {
				$test->assertNotEquals($a, $b, '', $delta, 10, $canonicalize, $ignoreCase);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertSame
		 * @dataProvider sameProvider
		 */
		public function testAssertSameSucceeds($a, $b)
		{
			$test = new testedClass();

			$test->assertSame($a, $b);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertSame
		 * @dataProvider notSameProvider
		 */
		public function testAssertSameFails($a, $b)
		{
			$test = new testedClass();

			try {
				$test->assertSame($a, $b);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertNotSame
		 * @dataProvider notSameProvider
		 */
		public function testAssertNotSameSucceeds($a, $b)
		{
			$test = new testedClass();

			$test->assertNotSame($a, $b);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertNotSame
		 * @dataProvider sameProvider
		 */
		public function testAssertNotSameFails($a, $b)
		{
			$test = new testedClass();

			try {
				$test->assertNotSame($a, $b);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertXmlFileEqualsXmlFile
		 */
		public function testAssertXmlFileEqualsXmlFile()
		{
			$test = new testedClass();

			$test->assertXmlFileEqualsXmlFile(
			  $this->filesDirectory . 'foo.xml',
			  $this->filesDirectory . 'foo.xml'
			);

			try {
				$test->assertXmlFileEqualsXmlFile(
				  $this->filesDirectory . 'foo.xml',
				  $this->filesDirectory . 'bar.xml'
				);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertXmlFileNotEqualsXmlFile
		 */
		public function testAssertXmlFileNotEqualsXmlFile()
		{
			$test = new testedClass();

			$test->assertXmlFileNotEqualsXmlFile(
			  $this->filesDirectory . 'foo.xml',
			  $this->filesDirectory . 'bar.xml'
			);

			try {
				$test->assertXmlFileNotEqualsXmlFile(
				  $this->filesDirectory . 'foo.xml',
				  $this->filesDirectory . 'foo.xml'
				);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertXmlStringEqualsXmlFile
		 */
		public function testAssertXmlStringEqualsXmlFile()
		{
			$test = new testedClass();

			$test->assertXmlStringEqualsXmlFile(
			  $this->filesDirectory . 'foo.xml',
			  file_get_contents($this->filesDirectory . 'foo.xml')
			);

			try {
				$test->assertXmlStringEqualsXmlFile(
				  $this->filesDirectory . 'foo.xml',
				  file_get_contents($this->filesDirectory . 'bar.xml')
				);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertXmlStringNotEqualsXmlFile
		 */
		public function testXmlStringNotEqualsXmlFile()
		{
			$test = new testedClass();

			$test->assertXmlStringNotEqualsXmlFile(
			  $this->filesDirectory . 'foo.xml',
			  file_get_contents($this->filesDirectory . 'bar.xml')
			);

			try {
				$test->assertXmlStringNotEqualsXmlFile(
				  $this->filesDirectory . 'foo.xml',
				  file_get_contents($this->filesDirectory . 'foo.xml')
				);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertXmlStringEqualsXmlString
		 */
		public function testAssertXmlStringEqualsXmlString()
		{
			$test = new testedClass();

			$test->assertXmlStringEqualsXmlString('<root/>', '<root/>');

			try {
				$test->assertXmlStringEqualsXmlString('<foo/>', '<bar/>');
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertXmlStringNotEqualsXmlString
		 */
		public function testAssertXmlStringNotEqualsXmlString()
		{
			$test = new testedClass();

			$test->assertXmlStringNotEqualsXmlString('<foo/>', '<bar/>');

			try {
				$test->assertXmlStringNotEqualsXmlString('<root/>', '<root/>');
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertEqualXMLStructure
		 */
		public function testXMLStructureIsSame()
		{
			$test = new testedClass();

			$expected = new \DOMDocument;
			$expected->load($this->filesDirectory . 'structureExpected.xml');

			$actual = new \DOMDocument;
			$actual->load($this->filesDirectory . 'structureExpected.xml');

			$test->assertEqualXMLStructure(
			  $expected->firstChild, $actual->firstChild, TRUE
			);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertEqualXMLStructure
		 * @expectedException PHPUnit_Framework_ExpectationFailedException
		 */
		public function testXMLStructureWrongNumberOfAttributes()
		{
			$test = new testedClass();

			$expected = new \DOMDocument;
			$expected->load($this->filesDirectory . 'structureExpected.xml');

			$actual = new \DOMDocument;
			$actual->load($this->filesDirectory . 'structureWrongNumberOfAttributes.xml');

			$test->assertEqualXMLStructure(
			  $expected->firstChild, $actual->firstChild, TRUE
			);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertEqualXMLStructure
		 * @expectedException PHPUnit_Framework_ExpectationFailedException
		 */
		public function testXMLStructureWrongNumberOfNodes()
		{
			$test = new testedClass();

			$expected = new \DOMDocument;
			$expected->load($this->filesDirectory . 'structureExpected.xml');

			$actual = new \DOMDocument;
			$actual->load($this->filesDirectory . 'structureWrongNumberOfNodes.xml');

			$test->assertEqualXMLStructure(
			  $expected->firstChild, $actual->firstChild, TRUE
			);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertEqualXMLStructure
		 */
		public function testXMLStructureIsSameButDataIsNot()
		{
			$test = new testedClass();

			$expected = new \DOMDocument;
			$expected->load($this->filesDirectory . 'structureExpected.xml');

			$actual = new \DOMDocument;
			$actual->load($this->filesDirectory . 'structureIsSameButDataIsNot.xml');

			$test->assertEqualXMLStructure(
			  $expected->firstChild, $actual->firstChild, TRUE
			);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertEqualXMLStructure
		 */
		public function testXMLStructureAttributesAreSameButValuesAreNot()
		{
			$test = new testedClass();

			$expected = new \DOMDocument;
			$expected->load($this->filesDirectory . 'structureExpected.xml');

			$actual = new \DOMDocument;
			$actual->load($this->filesDirectory . 'structureAttributesAreSameButValuesAreNot.xml');

			$test->assertEqualXMLStructure(
			  $expected->firstChild, $actual->firstChild, TRUE
			);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertEqualXMLStructure
		 */
		public function testXMLStructureIgnoreTextNodes()
		{
			$test = new testedClass();

			$expected = new \DOMDocument;
			$expected->load($this->filesDirectory . 'structureExpected.xml');

			$actual = new \DOMDocument;
			$actual->load($this->filesDirectory . 'structureIgnoreTextNodes.xml');

			$test->assertEqualXMLStructure(
			  $expected->firstChild, $actual->firstChild, TRUE
			);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertEquals
		 */
		public function testAssertStringEqualsNumeric()
		{
			$test = new testedClass();

			$test->assertEquals('0', 0);

			try {
				$test->assertEquals('0', 1);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertNotEquals
		 */
		public function testAssertStringEqualsNumeric2()
		{
			$test = new testedClass();

			$test->assertNotEquals('A', 0);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertFileExists
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertFileExistsThrowsException()
		{
			$test = new testedClass();

			$test->assertFileExists(NULL);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertFileExists
		 */
		public function testAssertFileExists()
		{
			$test = new testedClass();

			$test->assertFileExists(__FILE__);

			try {
				$test->assertFileExists(__DIR__ . DIRECTORY_SEPARATOR . 'NotExisting');
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertFileNotExists
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertFileNotExistsThrowsException()
		{
			$test = new testedClass();

			$test->assertFileNotExists(NULL);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertFileNotExists
		 */
		public function testAssertFileNotExists()
		{
			$test = new testedClass();

			$test->assertFileNotExists(__DIR__ . DIRECTORY_SEPARATOR . 'NotExisting');

			try {
				$test->assertFileNotExists(__FILE__);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertObjectHasAttribute
		 */
		public function testAssertObjectHasAttribute()
		{
			$test = new testedClass();

			$o = new \Author('Terry Pratchett');

			$test->assertObjectHasAttribute('name', $o);

			try {
				$test->assertObjectHasAttribute('foo', $o);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertObjectNotHasAttribute
		 */
		public function testAssertObjectNotHasAttribute()
		{
			$test = new testedClass();

			$o = new \Author('Terry Pratchett');

			$test->assertObjectNotHasAttribute('foo', $o);

			try {
				$test->assertObjectNotHasAttribute('name', $o);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertNull
		 */
		public function testAssertNull()
		{
			$test = new testedClass();

			$test->assertNull(NULL);

			try {
				$test->assertNull(new \stdClass);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertNotNull
		 */
		public function testAssertNotNull()
		{
			$test = new testedClass();

			$test->assertNotNull(new \stdClass);

			try {
				$test->assertNotNull(NULL);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTrue
		 */
		public function testAssertTrue()
		{
			$test = new testedClass();

			$test->assertTrue(TRUE);

			try {
				$test->assertTrue(FALSE);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertFalse
		 */
		public function testAssertFalse()
		{
			$test = new testedClass();

			$test->assertFalse(FALSE);

			try {
				$test->assertFalse(TRUE);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertRegExp
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertRegExpThrowsException()
		{
			$test = new testedClass();

			$test->assertRegExp(NULL, NULL);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertRegExp
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertRegExpThrowsException2()
		{
			$test = new testedClass();

			$test->assertRegExp('', NULL);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertNotRegExp
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertNotRegExpThrowsException()
		{
			$test = new testedClass();

			$test->assertNotRegExp(NULL, NULL);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertNotRegExp
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertNotRegExpThrowsException2()
		{
			$test = new testedClass();

			$test->assertNotRegExp('', NULL);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertRegExp
		 */
		public function testAssertRegExp()
		{
			$test = new testedClass();

			$test->assertRegExp('/foo/', 'foobar');

			try {
				$test->assertRegExp('/foo/', 'bar');
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertNotRegExp
		 */
		public function testAssertNotRegExp()
		{
			$test = new testedClass();

			$test->assertNotRegExp('/foo/', 'bar');

			try {
				$test->assertNotRegExp('/foo/', 'foobar');
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertSame
		 */
		public function testAssertSame()
		{
			$test = new testedClass();

			$o = new \stdClass;

			$test->assertSame($o, $o);

			try {
				$test->assertSame(
				  new \stdClass,
				  new \stdClass
				);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertSame
		 */
		public function testAssertSame2()
		{
			$test = new testedClass();

			$test->assertSame(TRUE, TRUE);
			$test->assertSame(FALSE, FALSE);

			try {
				$test->assertSame(TRUE, FALSE);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertNotSame
		 */
		public function testAssertNotSame()
		{
			$test = new testedClass();

			$test->assertNotSame(
			  new \stdClass,
			  NULL
			);

			$test->assertNotSame(
			  NULL,
			  new \stdClass
			);

			$test->assertNotSame(
			  new \stdClass,
			  new \stdClass
			);

			$o = new \stdClass;

			try {
				$test->assertNotSame($o, $o);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertNotSame
		 */
		public function testAssertNotSame2()
		{
			$test = new testedClass();

			$test->assertNotSame(TRUE, FALSE);
			$test->assertNotSame(FALSE, TRUE);

			try {
				$test->assertNotSame(TRUE, TRUE);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertNotSame
		 */
		public function testAssertNotSameFailsNull()
		{
			$test = new testedClass();

			try {
				$test->assertNotSame(NULL, NULL);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertGreaterThan
		 */
		public function testGreaterThan()
		{
			$test = new testedClass();

			$test->assertGreaterThan(1, 2);

			try {
				$test->assertGreaterThan(2, 1);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertAttributeGreaterThan
		 */
		public function testAttributeGreaterThan()
		{
			$test = new testedClass();

			$test->assertAttributeGreaterThan(
			  1, 'bar', new \ClassWithNonPublicAttributes
			);

			try {
				$test->assertAttributeGreaterThan(
				  1, 'foo', new \ClassWithNonPublicAttributes
				);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertGreaterThanOrEqual
		 */
		public function testGreaterThanOrEqual()
		{
			$test = new testedClass();

			$test->assertGreaterThanOrEqual(1, 2);

			try {
				$test->assertGreaterThanOrEqual(2, 1);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertAttributeGreaterThanOrEqual
		 */
		public function testAttributeGreaterThanOrEqual()
		{
			$test = new testedClass();

			$test->assertAttributeGreaterThanOrEqual(
			  1, 'bar', new \ClassWithNonPublicAttributes
			);

			try {
				$test->assertAttributeGreaterThanOrEqual(
				  2, 'foo', new \ClassWithNonPublicAttributes
				);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertLessThan
		 */
		public function testLessThan()
		{
			$test = new testedClass();

			$test->assertLessThan(2, 1);

			try {
				$test->assertLessThan(1, 2);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertAttributeLessThan
		 */
		public function testAttributeLessThan()
		{
			$test = new testedClass();

			$test->assertAttributeLessThan(
			  2, 'foo', new \ClassWithNonPublicAttributes
			);

			try {
				$test->assertAttributeLessThan(
				  1, 'bar', new \ClassWithNonPublicAttributes
				);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertLessThanOrEqual
		 */
		public function testLessThanOrEqual()
		{
			$test = new testedClass();

			$test->assertLessThanOrEqual(2, 1);

			try {
				$test->assertLessThanOrEqual(1, 2);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertAttributeLessThanOrEqual
		 */
		public function testAttributeLessThanOrEqual()
		{
			$test = new testedClass();

			$test->assertAttributeLessThanOrEqual(
			  2, 'foo', new \ClassWithNonPublicAttributes
			);

			try {
				$test->assertAttributeLessThanOrEqual(
				  1, 'bar', new \ClassWithNonPublicAttributes
				);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::readAttribute
		 */
		public function testReadAttribute()
		{
			$test = new testedClass();

			$obj = new \ClassWithNonPublicAttributes;

			$test->assertEquals('foo', $test->readAttribute($obj, 'publicAttribute'));
			$test->assertEquals('bar', $test->readAttribute($obj, 'protectedAttribute'));
			$test->assertEquals('baz', $test->readAttribute($obj, 'privateAttribute'));
			$test->assertEquals('bar', $test->readAttribute($obj, 'protectedParentAttribute'));
			//$this->assertEquals('bar', $this->readAttribute($obj, 'privateParentAttribute'));
		}

		/**
		 * @covers PHPUnit_Framework_Assert::readAttribute
		 */
		public function testReadAttribute2()
		{
			$test = new testedClass();

			$test->assertEquals('foo', $test->readAttribute('ClassWithNonPublicAttributes', 'publicStaticAttribute'));
			$test->assertEquals('bar', $test->readAttribute('ClassWithNonPublicAttributes', 'protectedStaticAttribute'));
			$test->assertEquals('baz', $test->readAttribute('ClassWithNonPublicAttributes', 'privateStaticAttribute'));
			$test->assertEquals('foo', $test->readAttribute('ClassWithNonPublicAttributes', 'protectedStaticParentAttribute'));
			$test->assertEquals('foo', $test->readAttribute('ClassWithNonPublicAttributes', 'privateStaticParentAttribute'));
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::readAttribute
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testReadAttribute3()
		{
			$test = new testedClass();

			$test->readAttribute('StdClass', NULL);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::readAttribute
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testReadAttribute4()
		{
			$test = new testedClass();

			$test->readAttribute('NotExistingClass', 'foo');
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::readAttribute
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testReadAttribute5()
		{
			$test = new testedClass();

			$test->readAttribute(NULL, 'foo');
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertAttributeContains
		 */
		public function testAssertPublicAttributeContains()
		{
			$test = new testedClass();

			$obj = new \ClassWithNonPublicAttributes;

			$test->assertAttributeContains('foo', 'publicArray', $obj);

			try {
				$test->assertAttributeContains('bar', 'publicArray', $obj);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertAttributeContainsOnly
		 */
		public function testAssertPublicAttributeContainsOnly()
		{
			$test = new testedClass();

			$obj = new \ClassWithNonPublicAttributes;

			$test->assertAttributeContainsOnly('string', 'publicArray', $obj);

			try {
				$test->assertAttributeContainsOnly('integer', 'publicArray', $obj);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertAttributeNotContains
		 */
		public function testAssertPublicAttributeNotContains()
		{
			$test = new testedClass();

			$obj = new \ClassWithNonPublicAttributes;

			$test->assertAttributeNotContains('bar', 'publicArray', $obj);

			try {
				$test->assertAttributeNotContains('foo', 'publicArray', $obj);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertAttributeNotContainsOnly
		 */
		public function testAssertPublicAttributeNotContainsOnly()
		{
			$test = new testedClass();

			$obj = new \ClassWithNonPublicAttributes;

			$test->assertAttributeNotContainsOnly('integer', 'publicArray', $obj);

			try {
				$test->assertAttributeNotContainsOnly('string', 'publicArray', $obj);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertAttributeContains
		 */
		public function testAssertProtectedAttributeContains()
		{
			$test = new testedClass();

			$obj = new \ClassWithNonPublicAttributes;

			$test->assertAttributeContains('bar', 'protectedArray', $obj);

			try {
				$test->assertAttributeContains('foo', 'protectedArray', $obj);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertAttributeNotContains
		 */
		public function testAssertProtectedAttributeNotContains()
		{
			$test = new testedClass();

			$obj = new \ClassWithNonPublicAttributes;

			$test->assertAttributeNotContains('foo', 'protectedArray', $obj);

			try {
				$test->assertAttributeNotContains('bar', 'protectedArray', $obj);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertAttributeContains
		 */
		public function testAssertPrivateAttributeContains()
		{
			$test = new testedClass();

			$obj = new \ClassWithNonPublicAttributes;

			$test->assertAttributeContains('baz', 'privateArray', $obj);

			try {
				$test->assertAttributeContains('foo', 'privateArray', $obj);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertAttributeNotContains
		 */
		public function testAssertPrivateAttributeNotContains()
		{
			$test = new testedClass();

			$obj = new \ClassWithNonPublicAttributes;

			$test->assertAttributeNotContains('foo', 'privateArray', $obj);

			try {
				$test->assertAttributeNotContains('baz', 'privateArray', $obj);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertAttributeEquals
		 */
		public function testAssertPublicAttributeEquals()
		{
			$test = new testedClass();

			$obj = new \ClassWithNonPublicAttributes;

			$test->assertAttributeEquals('foo', 'publicAttribute', $obj);

			try {
				$test->assertAttributeEquals('bar', 'publicAttribute', $obj);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertAttributeNotEquals
		 */
		public function testAssertPublicAttributeNotEquals()
		{
			$test = new testedClass();

			$obj = new \ClassWithNonPublicAttributes;

			$test->assertAttributeNotEquals('bar', 'publicAttribute', $obj);

			try {
				$test->assertAttributeNotEquals('foo', 'publicAttribute', $obj);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertAttributeSame
		 */
		public function testAssertPublicAttributeSame()
		{
			$test = new testedClass();

			$obj = new \ClassWithNonPublicAttributes;

			$test->assertAttributeSame('foo', 'publicAttribute', $obj);

			try {
				$test->assertAttributeSame('bar', 'publicAttribute', $obj);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertAttributeNotSame
		 */
		public function testAssertPublicAttributeNotSame()
		{
			$test = new testedClass();

			$obj = new \ClassWithNonPublicAttributes;

			$test->assertAttributeNotSame('bar', 'publicAttribute', $obj);

			try {
				$test->assertAttributeNotSame('foo', 'publicAttribute', $obj);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertAttributeEquals
		 */
		public function testAssertProtectedAttributeEquals()
		{
			$test = new testedClass();

			$obj = new \ClassWithNonPublicAttributes;

			$test->assertAttributeEquals('bar', 'protectedAttribute', $obj);

			try {
				$test->assertAttributeEquals('foo', 'protectedAttribute', $obj);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertAttributeNotEquals
		 */
		public function testAssertProtectedAttributeNotEquals()
		{
			$test = new testedClass();

			$obj = new \ClassWithNonPublicAttributes;

			$test->assertAttributeNotEquals('foo', 'protectedAttribute', $obj);

			try {
				$test->assertAttributeNotEquals('bar', 'protectedAttribute', $obj);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertAttributeEquals
		 */
		public function testAssertPrivateAttributeEquals()
		{
			$test = new testedClass();

			$obj = new \ClassWithNonPublicAttributes;

			$test->assertAttributeEquals('baz', 'privateAttribute', $obj);

			try {
				$test->assertAttributeEquals('foo', 'privateAttribute', $obj);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertAttributeNotEquals
		 */
		public function testAssertPrivateAttributeNotEquals()
		{
			$test = new testedClass();

			$obj = new \ClassWithNonPublicAttributes;

			$test->assertAttributeNotEquals('foo', 'privateAttribute', $obj);

			try {
				$test->assertAttributeNotEquals('baz', 'privateAttribute', $obj);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertAttributeEquals
		 */
		public function testAssertPublicStaticAttributeEquals()
		{
			$test = new testedClass();

			$test->assertAttributeEquals('foo', 'publicStaticAttribute', 'ClassWithNonPublicAttributes');

			try {
				$test->assertAttributeEquals('bar', 'publicStaticAttribute', 'ClassWithNonPublicAttributes');
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertAttributeNotEquals
		 */
		public function testAssertPublicStaticAttributeNotEquals()
		{
			$test = new testedClass();

			$test->assertAttributeNotEquals('bar', 'publicStaticAttribute', 'ClassWithNonPublicAttributes');

			try {
				$test->assertAttributeNotEquals('foo', 'publicStaticAttribute', 'ClassWithNonPublicAttributes');
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertAttributeEquals
		 */
		public function testAssertProtectedStaticAttributeEquals()
		{
			$test = new testedClass();

			$test->assertAttributeEquals('bar', 'protectedStaticAttribute', 'ClassWithNonPublicAttributes');

			try {
				$test->assertAttributeEquals('foo', 'protectedStaticAttribute', 'ClassWithNonPublicAttributes');
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertAttributeNotEquals
		 */
		public function testAssertProtectedStaticAttributeNotEquals()
		{
			$test = new testedClass();

			$test->assertAttributeNotEquals('foo', 'protectedStaticAttribute', 'ClassWithNonPublicAttributes');

			try {
				$test->assertAttributeNotEquals('bar', 'protectedStaticAttribute', 'ClassWithNonPublicAttributes');
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertAttributeEquals
		 */
		public function testAssertPrivateStaticAttributeEquals()
		{
			$test = new testedClass();

			$test->assertAttributeEquals('baz', 'privateStaticAttribute', 'ClassWithNonPublicAttributes');

			try {
				$test->assertAttributeEquals('foo', 'privateStaticAttribute', 'ClassWithNonPublicAttributes');
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertAttributeNotEquals
		 */
		public function testAssertPrivateStaticAttributeNotEquals()
		{
			$test = new testedClass();

			$test->assertAttributeNotEquals('foo', 'privateStaticAttribute', 'ClassWithNonPublicAttributes');

			try {
				$test->assertAttributeNotEquals('baz', 'privateStaticAttribute', 'ClassWithNonPublicAttributes');
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertClassHasAttribute
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertClassHasAttributeThrowsException()
		{
			$test = new testedClass();

			$test->assertClassHasAttribute(NULL, NULL);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertClassHasAttribute
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertClassHasAttributeThrowsException2()
		{
			$test = new testedClass();

			$test->assertClassHasAttribute('foo', NULL);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertClassNotHasAttribute
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertClassNotHasAttributeThrowsException()
		{
			$test = new testedClass();

			$test->assertClassNotHasAttribute(NULL, NULL);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertClassNotHasAttribute
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertClassNotHasAttributeThrowsException2()
		{
			$test = new testedClass();

			$test->assertClassNotHasAttribute('foo', NULL);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertClassHasStaticAttribute
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertClassHasStaticAttributeThrowsException()
		{
			$test = new testedClass();

			$test->assertClassHasStaticAttribute(NULL, NULL);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertClassHasStaticAttribute
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertClassHasStaticAttributeThrowsException2()
		{
			$test = new testedClass();

			$test->assertClassHasStaticAttribute('foo', NULL);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertClassNotHasStaticAttribute
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertClassNotHasStaticAttributeThrowsException()
		{
			$test = new testedClass();

			$test->assertClassNotHasStaticAttribute(NULL, NULL);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertClassNotHasStaticAttribute
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertClassNotHasStaticAttributeThrowsException2()
		{
			$test = new testedClass();

			$test->assertClassNotHasStaticAttribute('foo', NULL);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertObjectHasAttribute
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertObjectHasAttributeThrowsException()
		{
			$test = new testedClass();

			$test->assertObjectHasAttribute(NULL, NULL);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertObjectHasAttribute
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertObjectHasAttributeThrowsException2()
		{
			$test = new testedClass();

			$test->assertObjectHasAttribute('foo', NULL);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertObjectNotHasAttribute
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertObjectNotHasAttributeThrowsException()
		{
			$test = new testedClass();

			$test->assertObjectNotHasAttribute(NULL, NULL);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertObjectNotHasAttribute
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertObjectNotHasAttributeThrowsException2()
		{
			$test = new testedClass();

			$test->assertObjectNotHasAttribute('foo', NULL);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertClassHasAttribute
		 */
		public function testClassHasPublicAttribute()
		{
			$test = new testedClass();

			$test->assertClassHasAttribute('publicAttribute', 'ClassWithNonPublicAttributes');

			try {
				$test->assertClassHasAttribute('attribute', 'ClassWithNonPublicAttributes');
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertClassNotHasAttribute
		 */
		public function testClassNotHasPublicAttribute()
		{
			$test = new testedClass();

			$test->assertClassNotHasAttribute('attribute', 'ClassWithNonPublicAttributes');

			try {
				$test->assertClassNotHasAttribute('publicAttribute', 'ClassWithNonPublicAttributes');
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertClassHasStaticAttribute
		 */
		public function testClassHasPublicStaticAttribute()
		{
			$test = new testedClass();

			$test->assertClassHasStaticAttribute('publicStaticAttribute', 'ClassWithNonPublicAttributes');

			try {
				$test->assertClassHasStaticAttribute('attribute', 'ClassWithNonPublicAttributes');
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertClassNotHasStaticAttribute
		 */
		public function testClassNotHasPublicStaticAttribute()
		{
			$test = new testedClass();

			$test->assertClassNotHasStaticAttribute('attribute', 'ClassWithNonPublicAttributes');

			try {
				$test->assertClassNotHasStaticAttribute('publicStaticAttribute', 'ClassWithNonPublicAttributes');
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertObjectHasAttribute
		 */
		public function testObjectHasPublicAttribute()
		{
			$test = new testedClass();

			$obj = new \ClassWithNonPublicAttributes;

			$test->assertObjectHasAttribute('publicAttribute', $obj);

			try {
				$test->assertObjectHasAttribute('attribute', $obj);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertObjectNotHasAttribute
		 */
		public function testObjectNotHasPublicAttribute()
		{
			$test = new testedClass();

			$obj = new \ClassWithNonPublicAttributes;

			$test->assertObjectNotHasAttribute('attribute', $obj);

			try {
				$test->assertObjectNotHasAttribute('publicAttribute', $obj);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertObjectHasAttribute
		 */
		public function testObjectHasOnTheFlyAttribute()
		{
			$test = new testedClass();

			$obj = new \StdClass;
			$obj->foo = 'bar';

			$test->assertObjectHasAttribute('foo', $obj);

			try {
				$test->assertObjectHasAttribute('bar', $obj);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertObjectNotHasAttribute
		 */
		public function testObjectNotHasOnTheFlyAttribute()
		{
			$test = new testedClass();

			$obj = new \StdClass;
			$obj->foo = 'bar';

			$test->assertObjectNotHasAttribute('bar', $obj);

			try {
				$test->assertObjectNotHasAttribute('foo', $obj);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertObjectHasAttribute
		 */
		public function testObjectHasProtectedAttribute()
		{
			$test = new testedClass();

			$obj = new \ClassWithNonPublicAttributes;

			$test->assertObjectHasAttribute('protectedAttribute', $obj);

			try {
				$test->assertObjectHasAttribute('attribute', $obj);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertObjectNotHasAttribute
		 */
		public function testObjectNotHasProtectedAttribute()
		{
			$test = new testedClass();

			$obj = new \ClassWithNonPublicAttributes;

			$test->assertObjectNotHasAttribute('attribute', $obj);

			try {
				$test->assertObjectNotHasAttribute('protectedAttribute', $obj);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertObjectHasAttribute
		 */
		public function testObjectHasPrivateAttribute()
		{
			$test = new testedClass();

			$obj = new \ClassWithNonPublicAttributes;

			$test->assertObjectHasAttribute('privateAttribute', $obj);

			try {
				$test->assertObjectHasAttribute('attribute', $obj);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertObjectNotHasAttribute
		 */
		public function testObjectNotHasPrivateAttribute()
		{
			$test = new testedClass();

			$obj = new \ClassWithNonPublicAttributes;

			$test->assertObjectNotHasAttribute('attribute', $obj);

			try {
				$test->assertObjectNotHasAttribute('privateAttribute', $obj);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertThat
		 * @covers PHPUnit_Framework_Assert::attribute
		 * @covers PHPUnit_Framework_Assert::equalTo
		 */
		public function testAssertThatAttributeEquals()
		{
			$test = new testedClass();

			$test->assertThat(
			  new \ClassWithNonPublicAttributes,
			  $test->attribute(
				$test->equalTo('foo'),
				'publicAttribute'
			  )
			);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertThat
		 * @covers            PHPUnit_Framework_Assert::attribute
		 * @covers            PHPUnit_Framework_Assert::equalTo
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertThatAttributeEquals2()
		{
			$test = new testedClass();

			$test->assertThat(
			  new \ClassWithNonPublicAttributes,
			  $test->attribute(
				$test->equalTo('bar'),
				'publicAttribute'
			  )
			);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertThat
		 * @covers PHPUnit_Framework_Assert::attribute
		 * @covers PHPUnit_Framework_Assert::equalTo
		 */
		public function testAssertThatAttributeEqualTo()
		{
			$test = new testedClass();

			$test->assertThat(
			  new \ClassWithNonPublicAttributes,
			  $test->attributeEqualTo('publicAttribute', 'foo')
			);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertThat
		 * @covers PHPUnit_Framework_Assert::anything
		 */
		public function testAssertThatAnything()
		{
			$test = new testedClass();

			$test->assertThat('anything', $test->anything());
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertThat
		 * @covers PHPUnit_Framework_Assert::anything
		 * @covers PHPUnit_Framework_Assert::logicalAnd
		 */
		public function testAssertThatAnythingAndAnything()
		{
			$test = new testedClass();

			$test->assertThat(
			  'anything',
			  $test->logicalAnd(
				$test->anything(), $test->anything()
			  )
			);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertThat
		 * @covers PHPUnit_Framework_Assert::anything
		 * @covers PHPUnit_Framework_Assert::logicalOr
		 */
		public function testAssertThatAnythingOrAnything()
		{
			$test = new testedClass();

			$test->assertThat(
			  'anything',
			  $test->logicalOr(
				  $test->anything(), $test->anything()
			  )
			);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertThat
		 * @covers PHPUnit_Framework_Assert::anything
		 * @covers PHPUnit_Framework_Assert::logicalNot
		 * @covers PHPUnit_Framework_Assert::logicalXor
		 */
		public function testAssertThatAnythingXorNotAnything()
		{
			$test = new testedClass();

			$test->assertThat(
			  'anything',
			  $test->logicalXor(
				$test->anything(),
				$test->logicalNot($test->anything())
			  )
			);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertThat
		 * @covers PHPUnit_Framework_Assert::contains
		 */
		public function testAssertThatContains()
		{
			$test = new testedClass();

			$test->assertThat(array('foo'), $test->contains('foo'));
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertThat
		 * @covers PHPUnit_Framework_Assert::stringContains
		 */
		public function testAssertThatStringContains()
		{
			$test = new testedClass();

			$test->assertThat('barfoobar', $test->stringContains('foo'));
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertThat
		 * @covers PHPUnit_Framework_Assert::containsOnly
		 */
		public function testAssertThatContainsOnly()
		{
			$test = new testedClass();

			$test->assertThat(array('foo'), $test->containsOnly('string'));
		}
		/**
		 * @covers PHPUnit_Framework_Assert::assertThat
		 * @covers PHPUnit_Framework_Assert::containsOnlyInstancesOf
		 */
		public function testAssertThatContainsOnlyInstancesOf()
		{
			$test = new testedClass();

			$test->assertThat(array(new \Book), $test->containsOnlyInstancesOf('Book'));
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertThat
		 * @covers PHPUnit_Framework_Assert::arrayHasKey
		 */
		public function testAssertThatArrayHasKey()
		{
			$test = new testedClass();

			$test->assertThat(array('foo' => 'bar'), $test->arrayHasKey('foo'));
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertThat
		 * @covers PHPUnit_Framework_Assert::classHasAttribute
		 */
		public function testAssertThatClassHasAttribute()
		{
			$test = new testedClass();

			$test->assertThat(
			  new \ClassWithNonPublicAttributes,
			  $test->classHasAttribute('publicAttribute')
			);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertThat
		 * @covers PHPUnit_Framework_Assert::classHasStaticAttribute
		 */
		public function testAssertThatClassHasStaticAttribute()
		{
			$test = new testedClass();

			$test->assertThat(
			  new \ClassWithNonPublicAttributes,
			  $test->classHasStaticAttribute('publicStaticAttribute')
			);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertThat
		 * @covers PHPUnit_Framework_Assert::objectHasAttribute
		 */
		public function testAssertThatObjectHasAttribute()
		{
			$test = new testedClass();

			$test->assertThat(
			  new \ClassWithNonPublicAttributes,
			  $test->objectHasAttribute('publicAttribute')
			);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertThat
		 * @covers PHPUnit_Framework_Assert::equalTo
		 */
		public function testAssertThatEqualTo()
		{
			$test = new testedClass();

			$test->assertThat('foo', $test->equalTo('foo'));
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertThat
		 * @covers PHPUnit_Framework_Assert::identicalTo
		 */
		public function testAssertThatIdenticalTo()
		{
			$test = new testedClass();

			$value      = new \StdClass;
			$constraint = $test->identicalTo($value);

			$test->assertThat($value, $constraint);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertThat
		 * @covers PHPUnit_Framework_Assert::isInstanceOf
		 */
		public function testAssertThatIsInstanceOf()
		{
			$test = new testedClass();

			$test->assertThat(new \StdClass, $test->isInstanceOf('StdClass'));
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertThat
		 * @covers PHPUnit_Framework_Assert::isType
		 */
		public function testAssertThatIsType()
		{
			$test = new testedClass();

			$test->assertThat('string', $test->isType('string'));
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertThat
		 * @covers PHPUnit_Framework_Assert::fileExists
		 */
		public function testAssertThatFileExists()
		{
			$test = new testedClass();

			$test->assertThat(__FILE__, $test->fileExists());
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertThat
		 * @covers PHPUnit_Framework_Assert::greaterThan
		 */
		public function testAssertThatGreaterThan()
		{
			$test = new testedClass();

			$test->assertThat(2, $test->greaterThan(1));
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertThat
		 * @covers PHPUnit_Framework_Assert::greaterThanOrEqual
		 */
		public function testAssertThatGreaterThanOrEqual()
		{
			$test = new testedClass();

			$test->assertThat(2, $test->greaterThanOrEqual(1));
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertThat
		 * @covers PHPUnit_Framework_Assert::lessThan
		 */
		public function testAssertThatLessThan()
		{
			$test = new testedClass();

			$test->assertThat(1, $test->lessThan(2));
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertThat
		 * @covers PHPUnit_Framework_Assert::lessThanOrEqual
		 */
		public function testAssertThatLessThanOrEqual()
		{
			$test = new testedClass();

			$test->assertThat(1, $test->lessThanOrEqual(2));
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertThat
		 * @covers PHPUnit_Framework_Assert::matchesRegularExpression
		 */
		public function testAssertThatMatchesRegularExpression()
		{
			$test = new testedClass();

			$test->assertThat('foobar', $test->matchesRegularExpression('/foo/'));
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertTagTypeTrue()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'html');
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertTag
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertTagTypeFalse()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'code');
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertTagIdTrue()
		{
			$test = new testedClass();

			$matcher = array('id' => 'test_text');
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertTag
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertTagIdFalse()
		{
			$test = new testedClass();

			$matcher = array('id' => 'test_text_doesnt_exist');
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertTagStringContentTrue()
		{
			$test = new testedClass();

			$matcher = array('id' => 'test_text',
							 'content' => 'My test tag content');
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertTag
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertTagStringContentFalse()
		{
			$test = new testedClass();

			$matcher = array('id' => 'test_text',
							 'content' => 'My non existent tag content');
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertTagRegexpContentTrue()
		{
			$test = new testedClass();

			$matcher = array('id' => 'test_text',
							 'content' => 'regexp:/test tag/');
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertTagRegexpModifierContentTrue()
		{
			$test = new testedClass();

			$matcher = array('id' => 'test_text',
							 'content' => 'regexp:/TEST TAG/i');
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertTag
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertTagRegexpContentFalse()
		{
			$test = new testedClass();

			$matcher = array('id' => 'test_text',
							 'content' => 'regexp:/asdf/');
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertTagCdataContentTrue()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'script',
							 'content' => 'alert(\'Hello, world!\');');
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertTag
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertTagCdataontentFalse()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'script',
							 'content' => 'asdf');
			$test->assertTag($matcher, $this->html);
		}



		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertTagAttributesTrueA()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'span',
							 'attributes' => array('class' => 'test_class'));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertTagAttributesTrueB()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'div',
							 'attributes' => array('id' => 'test_child_id'));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertTag
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertTagAttributesFalse()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'span',
							 'attributes' => array('class' => 'test_missing_class'));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertTagAttributesRegexpTrueA()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'span',
							 'attributes' => array('class' => 'regexp:/.+_class/'));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertTagAttributesRegexpTrueB()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'div',
							 'attributes' => array('id' => 'regexp:/.+_child_.+/'));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertTagAttributesRegexpModifierTrue()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'div',
							 'attributes' => array('id' => 'regexp:/.+_CHILD_.+/i'));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertTag
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertTagAttributesRegexpModifierFalse()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'div',
							 'attributes' => array('id' => 'regexp:/.+_CHILD_.+/'));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertTag
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertTagAttributesRegexpFalse()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'span',
							 'attributes' => array('class' => 'regexp:/.+_missing_.+/'));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertTagAttributesMultiPartClassTrueA()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'div',
							 'id'  => 'test_multi_class',
							 'attributes' => array('class' => 'multi class'));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertTagAttributesMultiPartClassTrueB()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'div',
							 'id'  => 'test_multi_class',
							 'attributes' => array('class' => 'multi'));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertTag
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertTagAttributesMultiPartClassFalse()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'div',
							 'id'  => 'test_multi_class',
							 'attributes' => array('class' => 'mul'));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertTagParentTrue()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'head',
							 'parent' => array('tag' => 'html'));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertTag
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertTagParentFalse()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'head',
							 'parent' => array('tag' => 'div'));
			$test->assertTag($matcher, $this->html);
		}

		/**
		* @covers PHPUnit_Framework_Assert::assertTag
		*/
		public function testAssertTagMultiplePossibleChildren()
		{
			$test = new testedClass();

			$matcher = array(
				'tag' => 'li',
				'parent' => array(
					'tag' => 'ul',
					'id' => 'another_ul'
				)
			);
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertTagChildTrue()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'html',
							 'child' => array('tag' => 'head'));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertTag
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertTagChildFalse()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'html',
							 'child' => array('tag' => 'div'));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertTagAncestorTrue()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'div',
							 'ancestor' => array('tag' => 'html'));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertTag
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertTagAncestorFalse()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'html',
							 'ancestor' => array('tag' => 'div'));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertTagDescendantTrue()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'html',
							 'descendant' => array('tag' => 'div'));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertTag
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertTagDescendantFalse()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'div',
							 'descendant' => array('tag' => 'html'));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertTagChildrenCountTrue()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'ul',
							 'children' => array('count' => 3));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertTag
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertTagChildrenCountFalse()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'ul',
							 'children' => array('count' => 5));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertTagChildrenLessThanTrue()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'ul',
							 'children' => array('less_than' => 10));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertTag
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertTagChildrenLessThanFalse()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'ul',
							 'children' => array('less_than' => 2));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertTagChildrenGreaterThanTrue()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'ul',
							 'children' => array('greater_than' => 2));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertTag
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertTagChildrenGreaterThanFalse()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'ul',
							 'children' => array('greater_than' => 10));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertTagChildrenOnlyTrue()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'ul',
							 'children' => array('only' => array('tag' =>'li')));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertTag
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertTagChildrenOnlyFalse()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'ul',
							 'children' => array('only' => array('tag' =>'div')));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertTagTypeIdTrueA()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'ul', 'id' => 'my_ul');
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertTagTypeIdTrueB()
		{
			$test = new testedClass();

			$matcher = array('id' => 'my_ul', 'tag' => 'ul');
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertTagTypeIdTrueC()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'input', 'id'  => 'input_test_id');
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertTag
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertTagTypeIdFalse()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'div', 'id'  => 'my_ul');
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertTagContentAttributes()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'div',
							 'content'    => 'Test Id Text',
							 'attributes' => array('id' => 'test_id',
												   'class' => 'my_test_class'));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertParentContentAttributes()
		{
			$test = new testedClass();

			$matcher = array('tag'        => 'div',
							 'content'    => 'Test Id Text',
							 'attributes' => array('id'    => 'test_id',
												   'class' => 'my_test_class'),
							 'parent'     => array('tag' => 'body'));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertChildContentAttributes()
		{
			$test = new testedClass();

			$matcher = array('tag'        => 'div',
							 'content'    => 'Test Id Text',
							 'attributes' => array('id'    => 'test_id',
												   'class' => 'my_test_class'),
							 'child'      => array('tag'        => 'div',
												   'attributes' => array('id' => 'test_child_id')));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertChildSubChildren()
		{
			$test = new testedClass();

			$matcher = array('id' => 'test_id',
							 'child' => array('id' => 'test_child_id',
											  'child' => array('id' => 'test_subchild_id')));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertAncestorContentAttributes()
		{
			$test = new testedClass();

			$matcher = array('id'         => 'test_subchild_id',
							 'content'    => 'My Subchild',
							 'attributes' => array('id' => 'test_subchild_id'),
							 'ancestor'   => array('tag'        => 'div',
												   'attributes' => array('id' => 'test_id')));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertDescendantContentAttributes()
		{
			$test = new testedClass();

			$matcher = array('id'         => 'test_id',
							 'content'    => 'Test Id Text',
							 'attributes' => array('id'  => 'test_id'),
							 'descendant' => array('tag'        => 'span',
												   'attributes' => array('id' => 'test_subchild_id')));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertTag
		 */
		public function testAssertChildrenContentAttributes()
		{
			$test = new testedClass();

			$matcher = array('id'         => 'test_children',
							 'content'    => 'My Children',
							 'attributes' => array('class'  => 'children'),

							 'children' => array('less_than'    => '25',
												 'greater_than' => '2',
												 'only'         => array('tag' => 'div',
																		 'attributes' => array('class' => 'my_child'))
												));
			$test->assertTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertNotTag
		 */
		public function testAssertNotTagTypeIdFalse()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'div', 'id'  => 'my_ul');
			$test->assertNotTag($matcher, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertNotTag
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertNotTagContentAttributes()
		{
			$test = new testedClass();

			$matcher = array('tag' => 'div',
							 'content'    => 'Test Id Text',
							 'attributes' => array('id' => 'test_id',
												   'class' => 'my_test_class'));
			$test->assertNotTag($matcher, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertSelectCount
		 */
		public function testAssertSelectCountPresentTrue()
		{
			$test = new testedClass();

			$selector = 'div#test_id';
			$count    = TRUE;

			$test->assertSelectCount($selector, $count, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertSelectCount
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertSelectCountPresentFalse()
		{
			$test = new testedClass();

			$selector = 'div#non_existent';
			$count    = TRUE;

			$test->assertSelectCount($selector, $count, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertSelectCount
		 */
		public function testAssertSelectCountNotPresentTrue()
		{
			$test = new testedClass();

			$selector = 'div#non_existent';
			$count    = FALSE;

			$test->assertSelectCount($selector, $count, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertSelectCount
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertSelectNotPresentFalse()
		{
			$test = new testedClass();

			$selector = 'div#test_id';
			$count    = FALSE;

			$test->assertSelectCount($selector, $count, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertSelectCount
		 */
		public function testAssertSelectCountChildTrue()
		{
			$test = new testedClass();

			$selector = '#my_ul > li';
			$count    = 3;

			$test->assertSelectCount($selector, $count, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertSelectCount
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertSelectCountChildFalse()
		{
			$test = new testedClass();

			$selector = '#my_ul > li';
			$count    = 4;

			$test->assertSelectCount($selector, $count, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertSelectCount
		 */
		public function testAssertSelectCountDescendantTrue()
		{
			$test = new testedClass();

			$selector = '#my_ul li';
			$count    = 3;

			$test->assertSelectCount($selector, $count, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertSelectCount
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertSelectCountDescendantFalse()
		{
			$test = new testedClass();

			$selector = '#my_ul li';
			$count    = 4;

			$test->assertSelectCount($selector, $count, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertSelectCount
		 */
		public function testAssertSelectCountGreaterThanTrue()
		{
			$test = new testedClass();

			$selector = '#my_ul > li';
			$range    = array('>' => 2);

			$test->assertSelectCount($selector, $range, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertSelectCount
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertSelectCountGreaterThanFalse()
		{
			$test = new testedClass();

			$selector = '#my_ul > li';
			$range    = array('>' => 3);

			$test->assertSelectCount($selector, $range, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertSelectCount
		 */
		public function testAssertSelectCountGreaterThanEqualToTrue()
		{
			$test = new testedClass();

			$selector = '#my_ul > li';
			$range    = array('>=' => 3);

			$test->assertSelectCount($selector, $range, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertSelectCount
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertSelectCountGreaterThanEqualToFalse()
		{
			$test = new testedClass();

			$selector = '#my_ul > li';
			$range    = array('>=' => 4);

			$test->assertSelectCount($selector, $range, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertSelectCount
		 */
		public function testAssertSelectCountLessThanTrue()
		{
			$test = new testedClass();

			$selector = '#my_ul > li';
			$range    = array('<' => 4);

			$test->assertSelectCount($selector, $range, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertSelectCount
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertSelectCountLessThanFalse()
		{
			$test = new testedClass();

			$selector = '#my_ul > li';
			$range    = array('<' => 3);

			$test->assertSelectCount($selector, $range, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertSelectCount
		 */
		public function testAssertSelectCountLessThanEqualToTrue()
		{
			$test = new testedClass();

			$selector = '#my_ul > li';
			$range    = array('<=' => 3);

			$test->assertSelectCount($selector, $range, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertSelectCount
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertSelectCountLessThanEqualToFalse()
		{
			$test = new testedClass();

			$selector = '#my_ul > li';
			$range  = array('<=' => 2);

			$test->assertSelectCount($selector, $range, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertSelectCount
		 */
		public function testAssertSelectCountRangeTrue()
		{
			$test = new testedClass();

			$selector = '#my_ul > li';
			$range    = array('>' => 2, '<' => 4);

			$test->assertSelectCount($selector, $range, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertSelectCount
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertSelectCountRangeFalse()
		{
			$test = new testedClass();

			$selector = '#my_ul > li';
			$range    = array('>' => 1, '<' => 3);

			$test->assertSelectCount($selector, $range, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertSelectEquals
		 */
		public function testAssertSelectEqualsContentPresentTrue()
		{
			$test = new testedClass();

			$selector = 'span.test_class';
			$content  = 'Test Class Text';

			$test->assertSelectEquals($selector, $content, TRUE, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertSelectEquals
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertSelectEqualsContentPresentFalse()
		{
			$test = new testedClass();

			$selector = 'span.test_class';
			$content  = 'Test Nonexistent';

			$test->assertSelectEquals($selector, $content, TRUE, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertSelectEquals
		 */
		public function testAssertSelectEqualsContentNotPresentTrue()
		{
			$test = new testedClass();

			$selector = 'span.test_class';
			$content  = 'Test Nonexistent';

			$test->assertSelectEquals($selector, $content, FALSE, $this->html);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertSelectEquals
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertSelectEqualsContentNotPresentFalse()
		{
			$test = new testedClass();

			$selector = 'span.test_class';
			$content  = 'Test Class Text';

			$test->assertSelectEquals($selector, $content, FALSE, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertSelectRegExp
		 */
		public function testAssertSelectRegExpContentPresentTrue()
		{
			$test = new testedClass();

			$selector = 'span.test_class';
			$regexp   = '/Test.*Text/';

			$test->assertSelectRegExp($selector, $regexp, TRUE, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertSelectRegExp
		 */
		public function testAssertSelectRegExpContentPresentFalse()
		{
			$test = new testedClass();

			$selector = 'span.test_class';
			$regexp   = '/Nonexistant/';

			$test->assertSelectRegExp($selector, $regexp, FALSE, $this->html);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertFileEquals
		 */
		public function testAssertFileEquals()
		{
			$test = new testedClass();

			$test->assertFileEquals(
			  $this->filesDirectory . 'foo.xml',
			  $this->filesDirectory . 'foo.xml'
			);

			try {
				$test->assertFileEquals(
				  $this->filesDirectory . 'foo.xml',
				  $this->filesDirectory . 'bar.xml'
				);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertFileNotEquals
		 */
		public function testAssertFileNotEquals()
		{
			$test = new testedClass();

			$test->assertFileNotEquals(
			  $this->filesDirectory . 'foo.xml',
			  $this->filesDirectory . 'bar.xml'
			);

			try {
				$test->assertFileNotEquals(
				  $this->filesDirectory . 'foo.xml',
				  $this->filesDirectory . 'foo.xml'
				);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertStringEqualsFile
		 */
		public function testAssertStringEqualsFile()
		{
			$test = new testedClass();

			$test->assertStringEqualsFile(
			  $this->filesDirectory . 'foo.xml',
			  file_get_contents($this->filesDirectory . 'foo.xml')
			);

			try {
				$test->assertStringEqualsFile(
				  $this->filesDirectory . 'foo.xml',
				  file_get_contents($this->filesDirectory . 'bar.xml')
				);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertStringNotEqualsFile
		 */
		public function testAssertStringNotEqualsFile()
		{
			$test = new testedClass();

			$test->assertStringNotEqualsFile(
			  $this->filesDirectory . 'foo.xml',
			  file_get_contents($this->filesDirectory . 'bar.xml')
			);

			try {
				$test->assertStringNotEqualsFile(
				  $this->filesDirectory . 'foo.xml',
				  file_get_contents($this->filesDirectory . 'foo.xml')
				);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertStringStartsWith
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertStringStartsWithThrowsException()
		{
			$test = new testedClass();

			$test->assertStringStartsWith(NULL, NULL);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertStringStartsWith
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertStringStartsWithThrowsException2()
		{
			$test = new testedClass();

			$test->assertStringStartsWith('', NULL);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertStringStartsNotWith
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertStringStartsNotWithThrowsException()
		{
			$test = new testedClass();

			$test->assertStringStartsNotWith(NULL, NULL);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertStringStartsNotWith
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertStringStartsNotWithThrowsException2()
		{
			$test = new testedClass();

			$test->assertStringStartsNotWith('', NULL);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertStringEndsWith
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertStringEndsWithThrowsException()
		{
			$test = new testedClass();

			$test->assertStringEndsWith(NULL, NULL);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertStringEndsWith
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertStringEndsWithThrowsException2()
		{
			$test = new testedClass();

			$test->assertStringEndsWith('', NULL);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertStringEndsNotWith
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertStringEndsNotWithThrowsException()
		{
			$test = new testedClass();

			$test->assertStringEndsNotWith(NULL, NULL);
		}

		/**
		 * @covers            PHPUnit_Framework_Assert::assertStringEndsNotWith
		 * @expectedException PHPUnit_Framework_Exception
		 */
		public function testAssertStringEndsNotWithThrowsException2()
		{
			$test = new testedClass();

			$test->assertStringEndsNotWith('', NULL);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertStringStartsWith
		 */
		public function testAssertStringStartsWith()
		{
			$test = new testedClass();

			$test->assertStringStartsWith('prefix', 'prefixfoo');

			try {
				$test->assertStringStartsWith('prefix', 'foo');
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertStringStartsNotWith
		 */
		public function testAssertStringStartsNotWith()
		{
			$test = new testedClass();

			$test->assertStringStartsNotWith('prefix', 'foo');

			try {
				$test->assertStringStartsNotWith('prefix', 'prefixfoo');
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertStringEndsWith
		 */
		public function testAssertStringEndsWith()
		{
			$test = new testedClass();

			$test->assertStringEndsWith('suffix', 'foosuffix');

			try {
				$test->assertStringEndsWith('suffix', 'foo');
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertStringEndsNotWith
		 */
		public function testAssertStringEndsNotWith()
		{
			$test = new testedClass();

			$test->assertStringEndsNotWith('suffix', 'foo');

			try {
				$test->assertStringEndsNotWith('suffix', 'foosuffix');
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertStringMatchesFormat
		 */
		public function testAssertStringMatchesFormat()
		{
			$test = new testedClass();

			$test->assertStringMatchesFormat('*%s*', '***');
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertStringMatchesFormat
		 * @expectedException PHPUnit_Framework_AssertionFailedError
		 */
		public function testAssertStringMatchesFormatFailure()
		{
			$test = new testedClass();

			$test->assertStringMatchesFormat('*%s*', '**');
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertStringNotMatchesFormat
		 */
		public function testAssertStringNotMatchesFormat()
		{
			$test = new testedClass();

			$test->assertStringNotMatchesFormat('*%s*', '**');

			try {
				$test->assertStringMatchesFormat('*%s*', '**');
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertEmpty
		 */
		public function testAssertEmpty()
		{
			$test = new testedClass();

			$test->assertEmpty(array());

			try {
				$test->assertEmpty(array('foo'));
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertNotEmpty
		 */
		public function testAssertNotEmpty()
		{
			$test = new testedClass();

			$test->assertNotEmpty(array('foo'));

			try {
				$test->assertNotEmpty(array());
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertAttributeEmpty
		 */
		public function testAssertAttributeEmpty()
		{
			$test = new testedClass();

			$o    = new \StdClass;
			$o->a = array();

			$test->assertAttributeEmpty('a', $o);

			try {
				$o->a = array('b');
				$test->assertAttributeEmpty('a', $o);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertAttributeNotEmpty
		 */
		public function testAssertAttributeNotEmpty()
		{
			$test = new testedClass();

			$o    = new \StdClass;
			$o->a = array('b');

			$test->assertAttributeNotEmpty('a', $o);

			try {
				$o->a = array();
				$test->assertAttributeNotEmpty('a', $o);
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::markTestIncomplete
		 */
		public function testMarkTestIncomplete()
		{
			$test = new testedClass();

			try {
				$test->markTestIncomplete('incomplete');
			}

			catch (\PHPUnit_Framework_IncompleteTestError $e) {
				$test->assertEquals('incomplete', $e->getMessage());

				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::markTestSkipped
		 */
		public function testMarkTestSkipped()
		{
			$test = new testedClass();

			try {
				$test->markTestSkipped('skipped');
			}

			catch (\PHPUnit_Framework_SkippedTestError $e) {
				$test->assertEquals('skipped', $e->getMessage());

				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertCount
		 */
		public function testAssertCount()
		{
			$test = new testedClass();

			$test->assertCount(2, array(1,2));

			try {
				$test->assertCount(2, array(1,2,3));
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertCount
		 */
		public function testAssertCountThrowsExceptionIfExpectedCountIsNoInteger()
		{
			$test = new testedClass();

			try {
				$test->assertCount('a', array());
			}

			catch (\mageekguy\atoum\exceptions\logic\invalidArgument $e) {
				$test->assertEquals('Argument #1 of assertCount must be an integer', $e->getMessage());

				return;
			}

			$this->fail();
		}


		/**
		 * @covers PHPUnit_Framework_Assert::assertCount
		 */
		public function testAssertCountThrowsExceptionIfElementIsNotCountable()
		{
			$test = new testedClass();

			try {
				$test->assertCount(2, '');
			}

			catch (\mageekguy\atoum\exceptions\logic\invalidArgument $e) {
				$test->assertEquals('Argument #2 of assertCount must be countable', $e->getMessage());

				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertSameSize
		 */
		public function testAssertSameSize()
		{
			$test = new testedClass();

			$test->assertSameSize(array(1,2), array(3,4));

			try {
				$test->assertSameSize(array(1,2), array(1,2,3));
			}

			catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertSameSize
		 */
		public function testAssertSameSizeThrowsExceptionIfExpectedIsNotCoutable()
		{
			$test = new testedClass();

			try {
				$test->assertSameSize('a', array());
			}

			catch (\mageekguy\atoum\exceptions\logic\invalidArgument $e) {
				$test->assertEquals('Argument #1 of assertSameSize must be countable', $e->getMessage());

				return;
			}

			$this->fail();
		}


		/**
		 * @covers PHPUnit_Framework_Assert::assertSameSize
		 */
		public function testAssertSameSizeThrowsExceptionIfActualIsNotCountable()
		{
			$test = new testedClass();

			try {
				$test->assertSameSize(array(), '');
			}

			catch (\mageekguy\atoum\exceptions\logic\invalidArgument $e) {
				$test->assertEquals('Argument #2 of assertSameSize must be countable', $e->getMessage());

				return;
			}

			$this->fail();
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertJsonStringEqualsJsonString
		 */
		public function testAssertJsonStringEqualsJsonString()
		{
			$test = new testedClass();

			$expected = '{"Mascott" : "Tux"}';
			$actual   = '{"Mascott" : "Tux"}';
			$message  = 'Given Json strings do not match';

			$test->assertJsonStringEqualsJsonString($expected, $actual, $message);
		}

		/**
		 * @dataProvider validInvalidJsonDataprovider
		 * @covers PHPUnit_Framework_Assert::assertJsonStringEqualsJsonString
		 */
		public function testAssertJsonStringEqualsJsonStringErrorRaised($expected, $actual)
		{
			$test = new testedClass();

			try {
				$test->assertJsonStringEqualsJsonString($expected, $actual);
			} catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}
			$this->fail('Expected exception not found');
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertJsonStringNotEqualsJsonString
		 */
		public function testAssertJsonStringNotEqualsJsonString()
		{
			$test = new testedClass();

			$expected = '{"Mascott" : "Beastie"}';
			$actual   = '{"Mascott" : "Tux"}';
			$message  = 'Given Json strings do match';

			$test->assertJsonStringNotEqualsJsonString($expected, $actual, $message);
		}

		/**
		 * @dataProvider validInvalidJsonDataprovider
		 * @covers PHPUnit_Framework_Assert::assertJsonStringNotEqualsJsonString
		 */
		public function testAssertJsonStringNotEqualsJsonStringErrorRaised($expected, $actual)
		{
			$test = new testedClass();

			try {
				$test->assertJsonStringNotEqualsJsonString($expected, $actual);
			} catch (\mageekguy\atoum\asserter\exception $e) {
				return;
			}
			$this->fail('Expected exception not found');
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertJsonStringEqualsJsonFile
		 */
		public function testAssertJsonStringEqualsJsonFile()
		{
			$test = new testedClass();

			$file = __DIR__ . '/../_files/JsonData/simpleObject.js';
			$actual = json_encode(array("Mascott" => "Tux"));
			$message = '';
			$test->assertJsonStringEqualsJsonFile($file, $actual, $message);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertJsonStringEqualsJsonFile
		 */
		public function testAssertJsonStringEqualsJsonFileExpectingExpectationFailedException()
		{
			$test = new testedClass();

			$file = __DIR__ . '/../_files/JsonData/simpleObject.js';
			$actual = json_encode(array("Mascott" => "Beastie"));
			$message = '';
			try {
				$test->assertJsonStringEqualsJsonFile($file, $actual, $message);
			} catch (\PHPUnit_Framework_ExpectationFailedException $e) {
				$test->assertEquals(
					'Failed asserting that \'{"Mascott":"Beastie"}\' matches JSON string "{"Mascott":"Tux"}".',
					$e->getMessage()
				);
				return;
			}

			$this->fail('Expected Exception not thrown.');
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertJsonStringEqualsJsonFile
		 */
		public function testAssertJsonStringEqualsJsonFileExpectingException()
		{
			$test = new testedClass();

			$file = __DIR__ . '/../_files/JsonData/simpleObject.js';
			try {
				$test->assertJsonStringEqualsJsonFile($file, NULL);
			} catch (\PHPUnit_Framework_Exception $e) {
				return;
			}
			$this->fail('Expected Exception not thrown.');
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertJsonStringNotEqualsJsonFile
		 */
		public function testAssertJsonStringNotEqualsJsonFile()
		{
			$test = new testedClass();

			$file = __DIR__ . '/../_files/JsonData/simpleObject.js';
			$actual = json_encode(array("Mascott" => "Beastie"));
			$message = '';
			$test->assertJsonStringNotEqualsJsonFile($file, $actual, $message);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertJsonStringNotEqualsJsonFile
		 */
		public function testAssertJsonStringNotEqualsJsonFileExpectingException()
		{
			$test = new testedClass();

			$file = __DIR__ . '/../_files/JsonData/simpleObject.js';
			try {
				$test->assertJsonStringNotEqualsJsonFile($file, NULL);
			} catch (\PHPUnit_Framework_Exception $e) {
				return;
			}
			$this->fail('Expected exception not found.');
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertJsonFileNotEqualsJsonFile
		 */
		public function testAssertJsonFileNotEqualsJsonFile()
		{
			$test = new testedClass();

			$fileExpected = __DIR__ . '/../_files/JsonData/simpleObject.js';
			$fileActual   = __DIR__ . '/../_files/JsonData/arrayObject.js';
			$message = '';
			$test->assertJsonFileNotEqualsJsonFile($fileExpected, $fileActual, $message);
		}

		/**
		 * @covers PHPUnit_Framework_Assert::assertJsonFileEqualsJsonFile
		 */
		public function testAssertJsonFileEqualsJsonFile()
		{
			$test = new testedClass();

			$file = __DIR__ . '/../_files/JsonData/simpleObject.js';
			$message = '';
			$test->assertJsonFileEqualsJsonFile($file, $file, $message);
		}

		public static function validInvalidJsonDataprovider()
		{
			return array(
				'error syntax in expected JSON' => array('{"Mascott"::}', '{"Mascott" : "Tux"}'),
				'error UTF-8 in actual JSON'    => array('{"Mascott" : "Tux"}', '{"Mascott" : :}'),
			);
		}

	}
}
