<?php
/**
 * UltraMVC
 * A fast lightweight Model View Controller framework
 *
 * Copyright (C) 2014  Paul Carlton
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author      Paul Carlton
 * @category    UltraMVC
 * @package     Library
 * @license     GNU license
 * @version     1.0
 * @link        my.public.repo
 * @since       File available since
 */

namespace UltraMVC;

/**
 * Bootstrap class
 *
 * Sets up the environment
 * @package Library
 * @subpackage Bootstrap
 */
abstract class UltraMVCBootstrap {

	/**
	 * Defines the current controller and keeps track of the root
	 *
	 * @var array
	 * @access private
	 */
	private $globals = array();

	/**
	 * The root directory of the site, used for redirects
	 * and correct routing
	 *
	 * @var string
	 * @access public
	 */
	const ROOT_DIR = ROOT_DIR;

	/**
	 * The root directory of the site, used for redirects
	 * and correct routing
	 *
	 * @var string
	 * @access public
	 */
	const CUR_DIR = CUR_DIR;

	/**
	 * An array of functions that customizes the namespace path
	 *
	 * @var array
	 * @access protected
	 */
	protected static $registerPaths = array();

	/**
	 * The array that holds registered paths
	 *
	 * @var array
	 */
	protected static $registerRoutes = array();

	/**
	 * Init method must exist in the class
	 *
	 * This is the user defined Bootstrap method primarily used for registering module paths that do not
	 * adhere to the MVC namespace
	 * 
	 * @return mixed
	 */
	abstract protected function _init();

	/**
	 * Hook method must exist in the class
	 *
	 * This is the user defined Bootstrap method primarily used for setting up globally accessible data that the
	 * Application can use, example: setModel() method after registering paths for a custom database adapter
	 */
	abstract protected function _initHook();

	/**
	 * constructor function
	 */
	public function __construct()
	{
		$this->requireHelpers();
		$this->globals['root'] = 'index';
		$this->globals['default_controller'] = DEFAULT_CONTROLLER;
		$this->_init();
		spl_autoload_register('UltraMVC\UltraMVCBootstrap::requireLibrary');
		// hook the initialization after loading the library
		$this->_initHook();
	}



	/**
	 * @var bool|callable
	 */
	private static $autoload_fail_func = false;
	/**
	 * Set AutoLoadFail is the function called for setting
	 * customized fail functionality for autoloading libraries
	 * in the /Application folder.
	 *
	 * @param $callable_func
	 */
	public function setAutoloadFail($callable_func)
	{
		if (is_callable($callable_func)) {
			self::$autoload_fail_func = $callable_func;
		} else {
			self::$autoload_fail_func = false;
		}
	}


	/**
	 * Autoload classes without instantiating bootstrap
	 */
	public static function loadSplRegister()
	{
		spl_autoload_register('UltraMVC\UltraMVCBootstrap::requireLibrary');
	}


	/**
	 * Require Helpers requires the needed helper function
	 * classes made to make the developers life easier
	 *
	 * @access private
	 * @param null
	 * @return null
	 */
	private function requireHelpers()
	{
		require_once('Helpers/Functions/Array.php');
		require_once('Helpers/Functions/Request.php');
	}


	public function __get($name)
	{
		if (!isSet($this->globals[$name])) {
			return null;
		} else {
			return $this->globals[$name];
		}
	}


	/**
	 * Carries the paths needed to autoload classes
	 *
	 * @var array
	 * @access protected
	 */
	protected static $paths = array(
		//'/Library/',
		//'/Application/',
	);

	/**
	 * Require Library is an auto load function
	 * It is registered in when constructing the bootstrap
	 *
	 * @param String $class_name
	 * @throws BootstrapException
	 * @return null
	 */
	public static function requireLibrary($class_name)
	{
		// for here we want to shift the first
		$dir = ROOT_DIR;
		if (preg_match("@Api/?$@", $dir)) {
			$dir = preg_replace("@Api/?$@", "", $dir);
		}
		$parts = explode("\\", $class_name);
		if ($parts[0] == 'UltraMVC') {
			// We are taking of the package name UltraMVC
			array_shift($parts);
			array_unshift($parts,'Library');
		} elseif (substr($parts[0], 0, 5) == 'Ultra' && count($parts) == 1) {
			// short hand sugar for controller extension
			array_unshift($parts, substr($parts[0], 5) . 's');
			array_unshift($parts, 'Library');
		}

		if (!empty(self::$registerPaths)) {
			foreach (self::$registerPaths as $Func) {
				if (is_callable($Func)) {
					$Func($parts);
				}
			}
		}

		$file_dir = $dir.'/'.join("/", $parts).".php";
		if (is_readable($file_dir)) {
			require_once($file_dir);
		} elseif (is_callable(self::$autoload_fail_func)) {
			$func = self::$autoload_fail_func;
			$func($file_dir);
		} elseif (REQUIRE_FAIL_ON_AUTOLOAD) {
			throw new BootstrapException('Required Library not found: '.$file_dir);
		}

	}


	public function getRegisteredRoutes()
	{
		return self::$registerRoutes;
	}


	/**
	 * @param $func
	 */
	protected function registerPath($func)
	{
		if (is_callable($func)) {
			array_push(self::$registerPaths, $func);
		}
	}


	/**
	 * @param array $paths
	 */
	protected function registerRoute($url = '', $route_meta = array())
	{
		$route = array();
		if (is_array($route_meta)) {
			$route['file'] = $route_meta['file'];
			$route['function'] = $route_meta['function'];
		} elseif (is_string($route_meta)) {
			$tmp = explode("->", $route_meta);
			$route['file'] = $tmp[0];
			$route['function'] = $tmp[1];
		}
		self::$registerRoutes[$url] = $route;
	}

	protected function setModel($variable_or_callable = null)
	{
		if (is_null($variable_or_callable)) {
			throw new BootstrapException('setModel expects class or function');
		}
		if (is_callable($variable_or_callable)) {
			$this->globals['db'] = $variable_or_callable();
		} elseif (is_object($variable_or_callable)) {
			$this->globals['db'] = $variable_or_callable;
		}
	}

}

/**
 * A shell class for bootstrap exception
 *
 */
class BootstrapException extends \Exception {}

