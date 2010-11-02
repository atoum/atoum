<?php

namespace mageekguy\atoum;

interface observable
{
	public function callObservers($method);
	public function getObserverEvents();
}

?>
