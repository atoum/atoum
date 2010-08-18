<?php

namespace mageekguy\atoum;

interface observer
{
	public function manageObservableEvent(observable $observable, $event);
}
