<?php

namespace mageekguy\atoum\mock;

interface observer
{
	public function getCalls($functionName, array $arguments = null);
	public function resetCalls();
}

?>
