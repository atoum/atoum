<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum
;

require_once __DIR__ . '/../runner.php';

class autoloader extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($autoloader = new atoum\autoloader())
			->then
				->array($directories = $autoloader->getDirectories())->hasKey('mageekguy\atoum\\')
				->array($directories['mageekguy\atoum\\'])->isEqualTo(array(atoum\directory . (\phar::running() ? '/' : DIRECTORY_SEPARATOR) . 'classes' . DIRECTORY_SEPARATOR))
		;
	}

	public function testAddDirectory()
	{
		$this
			->if($autoloader = new atoum\autoloader())
			->then
				->object($autoloader->addDirectory($namespace = uniqid(), $directory = uniqid()))->isIdenticalTo($autoloader)
				->array($autoloader->getDirectories())->isEqualTo(array(
						'mageekguy\atoum\\' => array(atoum\directory . (\phar::running() ? '/' : DIRECTORY_SEPARATOR) . 'classes' . DIRECTORY_SEPARATOR),
						$namespace . '\\' => array($directory . DIRECTORY_SEPARATOR)
					)
				)
				->object($autoloader->addDirectory($otherNamespace = (uniqid() . '\\'), $otherDirectory = (uniqid() . DIRECTORY_SEPARATOR)))->isIdenticalTo($autoloader)
				->array($autoloader->getDirectories())->isEqualTo(array(
						'mageekguy\atoum\\' => array(atoum\directory . (\phar::running() ? '/' : DIRECTORY_SEPARATOR) . 'classes' . DIRECTORY_SEPARATOR),
						$namespace . '\\' => array($directory . DIRECTORY_SEPARATOR),
						$otherNamespace => array($otherDirectory)
					)
				)
				->object($autoloader->addDirectory($namespace, $secondDirectory = (uniqid() . DIRECTORY_SEPARATOR)))->isIdenticalTo($autoloader)
				->array($autoloader->getDirectories())->isEqualTo(array(
						'mageekguy\atoum\\' => array(atoum\directory . (\phar::running() ? '/' : DIRECTORY_SEPARATOR) . 'classes' . DIRECTORY_SEPARATOR),
						$namespace . '\\' => array(
							$directory . DIRECTORY_SEPARATOR,
							$secondDirectory
						),
						$otherNamespace => array($otherDirectory)
					)
				)
		;
	}
}
