<?php

namespace mageekguy\atoum;

interface observable
{
	public function callObservers($event);
	public function getScore();
	public function getBootstrapFile();
	public function getAutoloaderFile();
}
