<?php

namespace mageekguy\atoum;

interface observable
{
	public function addObserver(observer $observer);
	public function sendEventToObservers($event);
}

?>
