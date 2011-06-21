<?php

namespace mageekguy\atoum\tests\functional\selenium;

class by
{
	protected $locatorStrategy;
	protected $locatorValue;
	
	protected function __construct($locatorStrategy, $locatorValue)
	{
		$this->locatorStrategy = $locatorStrategy;
		$this->locatorValue = $locatorValue;
	}
	
	public function __toString()
	{
		return '{\'using\':\'' . $this->locatorStrategy . '\', \'value\':\'' . $this->locatorValue . '\'}';
	}
	
	public static function className($className)
	{
		return new by('class name', $className);
	}
	
	public static function cssSelector($cssSelector)
	{
		return new by('css selector', $cssSelector);
	}
	
	public static function id($id)
	{
		return new by('id', $id);
	}
	
	public static function name($name)
	{
		return new by('name', $name);
	}
	
	public static function linkText($linkText)
	{
		return new by('link text', $linkText);
	}
	
	public static function partialLinkText($partialLinkText)
	{
		return new by('partial link text', $partialLinkText);
	}
	
	public static function tagName($tagName)
	{
		return new by('tag name', $tagName);
	}
	
	public static function xpath($xpath)
	{
		return new by('xpath', $xpath);
	}
}

?>
