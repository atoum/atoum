<?php

namespace mageekguy\tests\unit;

interface observer
{
	public function manageObservableEvent(observable $observable, $event);
}
