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
				->array($autoloader->getClasses())->isEmpty()
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
}
