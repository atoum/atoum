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
			->string(testedClass::defaultCacheFileName)->isEqualTo('%s.atoum.cache')
			->string(testedClass::defaultFileSuffix)->isEqualTo('.php')
		;
	}

	public function test__construct()
	{
		$this
			->if($autoloader = new testedClass())
			->then
				->array($autoloader->getClasses())->isEmpty()
				->variable($autoloader->getCacheFileForInstance())->isEqualTo(testedClass::getCacheFile())
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
				->array($autoloader->getClassAliases())->isEqualTo(array('atoum' => 'mageekguy\\atoum\\test', 'mageekguy\\atoum' => 'mageekguy\\atoum\\test'))
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
						'mageekguy\\atoum' => 'mageekguy\\atoum\\test',
						$alias => $target
					)
				)
				->object($autoloader->addClassAlias($alias, $target))->isIdenticalTo($autoloader)
				->array($autoloader->getClassAliases())->isEqualTo(array(
						'atoum' => 'mageekguy\\atoum\\test',
						'mageekguy\\atoum' => 'mageekguy\\atoum\\test',
						$alias => $target
					)
				)
				->object($autoloader->addClassAlias('\\' . ($otherAlias = uniqid()), '\\' . ($otherTarget = uniqid())))->isIdenticalTo($autoloader)
				->array($autoloader->getClassAliases())->isEqualTo(array(
						'atoum' => 'mageekguy\\atoum\\test',
						'mageekguy\\atoum' => 'mageekguy\\atoum\\test',
						$alias => $target,
						$otherAlias => $otherTarget
					)
				)
				->object($autoloader->addClassAlias('\\' . ($anOtherAlias = uniqid()) . '\\', '\\' . ($anOtherTarget = uniqid()) . '\\'))->isIdenticalTo($autoloader)
				->array($autoloader->getClassAliases())->isEqualTo(array(
						'atoum' => 'mageekguy\\atoum\\test',
						'mageekguy\\atoum' => 'mageekguy\\atoum\\test',
						$alias => $target,
						$otherAlias => $otherTarget,
						$anOtherAlias => $anOtherTarget
					)
				)
				->object($autoloader->addClassAlias('FOO', '\\' . ($fooTarget = uniqid()) . '\\'))->isIdenticalTo($autoloader)
				->array($autoloader->getClassAliases())->isEqualTo(array(
						'atoum' => 'mageekguy\\atoum\\test',
						'mageekguy\\atoum' => 'mageekguy\\atoum\\test',
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
			->string(testedClass::getCacheFile())->isEqualTo(rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . sprintf(testedClass::defaultCacheFileName, md5($this->getTestedClassPath())))
			->if(testedClass::setCacheFile($cacheFile = uniqid()))
			->then
				->string(testedClass::getCacheFile())->isEqualTo($cacheFile)
		;
	}

	public function testSetCacheFileForInstance()
	{
		$this
			->if($autoloader = new testedClass())
			->then
				->object($autoloader->setCacheFileForInstance($path = uniqid()))->isIdenticalTo($autoloader)
				->string($autoloader->getCacheFileForInstance())->isEqualTo($path)
		;
	}
}
