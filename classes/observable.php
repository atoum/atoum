<?php

namespace atoum;

interface observable
{
	public function callObservers($event);
}
