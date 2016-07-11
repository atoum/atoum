<?php

namespace mageekguy\atoum\script;

use
	mageekguy\atoum\script,
	mageekguy\atoum\scripts
;

interface visitor
{
	public function __toString();

	public function visitScript(script $script);

	public function visitConfigurable(script\configurable $configurable);

	public function visitRunner(scripts\runner $runner);
}