<?php

namespace mageekguy\atoum;

interface observer
{
	public function handleEvent($event, observable $observable);
}
