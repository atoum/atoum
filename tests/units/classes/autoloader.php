<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum,
	mageekguy\atoum\autoloader as testedClass
;

require_once __DIR__ . '/../runner.php';

class autoloader extends atoum\test
{
	public function testClassConstants()
	{
		$this
			->string(testedClass::defaultCacheFileName)->isEqualTo('autoload.atoum.cache')
			->string(testedClass::defaultFileSuffix)->isEqualTo('.php')
		;
	}

	public function test__construct()
	{
		$this
			->if($autoloader = new testedClass())
			->then
				->array($autoloader->getClasses())->isEmpty()
				->array($autoloader->getDirectories())->isEqualTo(array(
						'mageekguy\atoum\\' => array(
							array(
								atoum\directory . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR,
								testedClass::defaultFileSuffix
							)
						)
					)
				)
				->array($autoloader->getNamespaceAliases())->isEqualTo(array('atoum\\' => 'mageekguy\\atoum\\'))
		;
	}

	public function testAddNamespaceAlias()
	{
		$this
			->if($autoloader = new testedClass())
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
				->object($autoloader->addNamespaceAlias('FOO', ($fooTarget = uniqid())))->isIdenticalTo($autoloader)
				->array($autoloader->getNamespaceAliases())->isEqualTo(array(
						'atoum\\' => 'mageekguy\\atoum\\',
						$alias . '\\' => $target . '\\',
						$otherAlias . '\\' => $otherTarget . '\\',
						$anOtherAlias . '\\' => $anOtherTarget . '\\',
						'foo\\' => $fooTarget . '\\'
					)
				)
		;
	}

	public function testAddClassAlias()
	{
		$this
			->if($autoloader = new testedClass())
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
				->object($autoloader->addClassAlias('FOO', '\\' . ($fooTarget = uniqid()) . '\\'))->isIdenticalTo($autoloader)
				->array($autoloader->getClassAliases())->isEqualTo(array(
						'atoum' => 'mageekguy\\atoum\\test',
						$alias => $target,
						$otherAlias => $otherTarget,
						$anOtherAlias => $anOtherTarget,
						'foo' => $fooTarget
					)
				)
		;
	}

	public function testGetCacheFile()
	{
		$this
			->string(testedClass::getCacheFile())->isEqualTo(rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . testedClass::defaultCacheFileName)
			->if(testedClass::setCacheFile($cacheFile = uniqid()))
			->then
				->string(testedClass::getCacheFile())->isEqualTo($cacheFile)
		;
	}
}
