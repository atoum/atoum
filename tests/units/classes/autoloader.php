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
				->array($autoloader->getNamespaceAliases())->isEqualTo(array('atoum\\' => 'mageekguy\\atoum\\'))
		;
	}

	public function testAddNamespaceAlias()
	{
		$this
			->if($autoloader = new atoum\autoloader())
			->then
				->object($autoloader->addNamespaceAlias($alias = uniqid(), $target = uniqid()))->isIdenticalTo($autoloader)
				->array($autoloader->getNamespaceAliases())->isEqualTo(array(
						'atoum\\' => 'mageekguy\\atoum\\',
						$alias . '\\' => $target . '\\'
					)
				)
				->object($autoloader->addNamespaceAlias($alias, $target))->isIdenticalTo($autoloader)
				->array($autoloader->getNamespaceAliases())->isEqualTo(array(
						'atoum\\' => 'mageekguy\\atoum\\',
						$alias . '\\' => $target . '\\'
					)
				)
				->object($autoloader->addNamespaceAlias('\\' . ($otherAlias = uniqid()), '\\' . ($otherTarget = uniqid())))->isIdenticalTo($autoloader)
				->array($autoloader->getNamespaceAliases())->isEqualTo(array(
						'atoum\\' => 'mageekguy\\atoum\\',
						$alias . '\\' => $target . '\\',
						$otherAlias . '\\' => $otherTarget . '\\'
					)
				)
				->object($autoloader->addNamespaceAlias('\\' . ($anOtherAlias = uniqid()) . '\\', '\\' . ($anOtherTarget = uniqid()) . '\\'))->isIdenticalTo($autoloader)
				->array($autoloader->getNamespaceAliases())->isEqualTo(array(
						'atoum\\' => 'mageekguy\\atoum\\',
						$alias . '\\' => $target . '\\',
						$otherAlias . '\\' => $otherTarget . '\\',
						$anOtherAlias . '\\' => $anOtherTarget . '\\'
					)
				)
		;
	}

	public function testAddClassAlias()
	{
		$this
			->if($autoloader = new atoum\autoloader())
			->then
				->object($autoloader->addClassAlias($alias = uniqid(), $target = uniqid()))->isIdenticalTo($autoloader)
				->array($autoloader->getClassAliases())->isEqualTo(array(
						'atoum' => 'mageekguy\\atoum\\test',
						$alias => $target
					)
				)
				->object($autoloader->addClassAlias($alias, $target))->isIdenticalTo($autoloader)
				->array($autoloader->getClassAliases())->isEqualTo(array(
						'atoum' => 'mageekguy\\atoum\\test',
						$alias => $target
					)
				)
				->object($autoloader->addClassAlias('\\' . ($otherAlias = uniqid()), '\\' . ($otherTarget = uniqid())))->isIdenticalTo($autoloader)
				->array($autoloader->getClassAliases())->isEqualTo(array(
						'atoum' => 'mageekguy\\atoum\\test',
						$alias => $target,
						$otherAlias => $otherTarget
					)
				)
				->object($autoloader->addClassAlias('\\' . ($anOtherAlias = uniqid()) . '\\', '\\' . ($anOtherTarget = uniqid()) . '\\'))->isIdenticalTo($autoloader)
				->array($autoloader->getClassAliases())->isEqualTo(array(
						'atoum' => 'mageekguy\\atoum\\test',
						$alias => $target,
						$otherAlias => $otherTarget,
						$anOtherAlias => $anOtherTarget
					)
				)
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
				->object($autoloader->addDirectory($otherNamespace, rtrim($otherDirectory, DIRECTORY_SEPARATOR)))->isIdenticalTo($autoloader)
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
				->object($autoloader->addDirectory($mixedCaseNamespace = 'a\MiXED\CASE\NameSPACE', $mixedCaseDirectory = (uniqid() . DIRECTORY_SEPARATOR)))->isIdenticalTo($autoloader)
				->array($autoloader->getDirectories())->isEqualTo(array(
						'mageekguy\atoum\\' => array(atoum\directory . (\phar::running() ? '/' : DIRECTORY_SEPARATOR) . 'classes' . DIRECTORY_SEPARATOR),
						$namespace . '\\' => array(
							$directory . DIRECTORY_SEPARATOR,
							$secondDirectory
						),
						$otherNamespace => array($otherDirectory),
						strtolower($mixedCaseNamespace) . '\\' => array($mixedCaseDirectory)
					)
				)
		;
	}
}
