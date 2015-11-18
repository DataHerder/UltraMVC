<?php
namespace UltraMVC\Framework;

class UltraSessions {

	public function __construct() {
		if (!isSet($_SESSION['$$ULTRA-MVC'])) {
			$_SESSION['$$ULTRA-MVC'] = array();
			$this->setDefaults();
		}
	}

	public function setDefaults()
	{
		$_SESSION['$$ULTRA-MVC'] = array(
			'IGNORE_AUTOLOAD' => false,
		);
	}

	public function setVal($name, $value)
	{
		self::sval($name, $value);
	}

	public function getVal($name)
	{
		return self::gval($name);
	}

	public static function sval($name, $value)
	{
		$name = strtoupper($name);
		$_SESSION['$$ULTRA-MVC'][$name] = $value;
	}

	public static function gval($name)
	{
		$name = strtoupper($name);
		if ($name !== '') {
			if (isSet($_SESSION['$$ULTRA-MVC'][$name])) {
				return $_SESSION['$$ULTRA-MVC'][$name];
			} else {
				return null;
			}
		} else {
			return null;
		}
	}
}