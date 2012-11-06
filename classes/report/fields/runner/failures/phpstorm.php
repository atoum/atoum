<?php

namespace mageekguy\atoum\report\fields\runner\failures;

class phpstorm extends execute
{
	public function getCommand()
	{
		return $this->command . ' --line %2$s %1$s &';
	}
}
