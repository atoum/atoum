<?php

namespace atoum;

interface observer
{
	public function handleEvent($event, observable $observable);
}
