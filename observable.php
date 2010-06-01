<?php

namespace mageekguy\tests\unit;

interface observable
{
	public function addObserver(observer $observer);
	public function sendEventToObservers($event);
}

?>
