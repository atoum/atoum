<?php

namespace atoum;

interface observable
{
	public function callObservers($event);
	public function getScore();
	public function getBootstrapFile();
}
