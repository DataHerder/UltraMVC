<?php 
/**
 * UltraMVC
 * A fast lightweight Model View Controller framework
 * 
 * Copyright (C) 2015 Paul Carlton
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
 * @package     Routers
 * @license     GNU license
 * @version     1.0
 * @link        my.public.repo
 * @since       File available since
 */

namespace UltraMVC\Routers;

/**
 * The Router class routes URLs to the correct destination and controller
 * 
 * @package Routers
 * @subpackage Router
 */
final class Router {


	/**
	 * 
	 * @var array
	 * @access private
	 */
	private $route = array();


	/**
	 * 
	 * @var \UltraMVC\Views\Errors\Error
	 * @access private
	 */
	private $Error = null;


	/**
	 * 
	 * @var \UltraMVC\UltraMVCBootstrap
	 * 
	 */
	protected $Bootstrap = null;


	/**
	 * Sets the route and variables
	 * needed by the router
	 *
	 * @param \UltraMVC\UltraMVCBootstrap $Bootstrap
	 */
	public function __construct(\UltraMVC\UltraMVCBootstrap $Bootstrap)
	{
		//debug_array($_GET);
		$route = $_GET['__library_router_route'];
		unset($_GET['__library_router_route']);
		$this->dir = $Bootstrap::ROOT_DIR;
		$this->route = explode("/", $route);
		$this->Bootstrap = $Bootstrap;
		$this->Error = new \UltraMVC\Views\Errors\Error;
	}

	/**
	 * Gets the the controller needed to finish the request
	 * 
	 * If controller not found, throws a not found router exception
	 * 
	 * @throws \UltraMVC\Routers\RouterException
	 */
	public function callController()
	{
		list($page, $controller, $path) = $this->_traverseRoute();

		$full_path = join('/', $this->route);
		if (!CONTROLLER_CASE_SENSITIVE) {
			$full_path = strtolower($full_path);
		}

		$controllers = array();
		$registered_routes = $this->Bootstrap->getRegisteredRoutes();


		if (!empty($path)) {
			$namespace = 'Application\Controllers\\'.join('\\', array_map([$this, '_formatController'], $path));
			$root_dir = $this->dir.'/Application/Controllers/'.join('/',array_map([$this, '_formatController'], $path));
		} else {
			$namespace = 'Application\Controllers';
			$root_dir = $this->dir.'/Application/Controllers';
		}

		// special case where controller needs to be landing
		if ($controller == "" && $page == '') {
			$controller = $this->Bootstrap->default_controller;
			$page = $this->Bootstrap->root;
		}

		// this needs to be looked at here
		if (!CONTROLLER_CASE_SENSITIVE) {
			//$page = ucwords($page);
			$controller = ucwords($controller);
		}



		if (isSet($registered_routes[$full_path])) {

			// need to do this in this case
			$root_dir = explode("/", $root_dir);
			array_pop($root_dir);
			$controllers[] = array(
				'page' => preg_replace("@-@", '_', $page),
				'controller' => $controller,
				'php_file' => join('/', $root_dir).'/'. $registered_routes[$full_path]['file'],
				'class' => $namespace . '\\' . $controller,
				'_page' => false,
			);

		} else {

			// if controller case sensitivity is set to true, then the literal url
			// signifies the case of the controller, otherwise it follows proper case
			// \Application\Controllers\ControllerName
			$controllers = [];
			$without_slash = $page != $this->Bootstrap->root;

			if ($without_slash) {
				if ($controller == '') {
					$controller = $this->Bootstrap->default_controller;
				}

				// first we check to see if page is actually a directory
				if (
					is_dir($root_dir . '/' . $page) ||
					is_dir($root_dir . '/' . $this->_formatController($page))) {

					$url = parse_url($_SERVER['REQUEST_URI']);
					$full_url = $url['path'] . '/';
					if (isSet($url['query'])) {
						$full_url .= '?' . $url['query'];
					}
					// let's keep things simple, add the slash!
					// lets make it a permanently moved 301 redirect for now
					header('Location: ' . $full_url, true, 301);
					die;
				}

				/* dbg_array([
					'slash' => 0,
					'bootstrap->root' => $this->Bootstrap->root,
					'page' => $page,
					'root_dir' => $root_dir,
					'under_score_page' => $under_score_page,
					'unser_score_controller' => $under_score_controller,
					'namespace' => $namespace,
					'controller' => $controller,
				]);*/

				$controllers[] = array(
					'page' => 'index',
					'controller' => $this->_formatController($page),
					'php_files' => array(
						join('/', [
							$root_dir,
							$this->_formatController($controller),
							$page . '.php'
						]),
						join('/', [
							$root_dir,
							$this->_formatController($controller),
							$this->_formatController($page) . '.php'
						]),
					),
					'class' => join('\\', [
						$namespace,
						$this->_formatController($controller),
						$this->_formatController($page)
					]),
					'comment' => 'First check that the page is in fact a controller page',
				);

				$controllers[] = array(
					// always format the page!
					'page' => $this->_formatPage($page),
					'controller' => $this->_formatController($controller),
					'php_files' => [join('/', [$root_dir, $this->_formatController($controller).'.php'])],
					'class' => join('\\', [$namespace, $this->_formatController($controller)]),
					'comment' => 'The actual check as controller page',
				);

				// that's all we should need as far as logic!

			} else {
				$controllers[] = array(
					'page' => $page,
					'controller' => 'Index',
					'php_files' => [$root_dir . '/' . $this->_formatController($controller) . '/Index.php'],
					'class' => $namespace.'\\'.$this->_formatController($controller).'\\Index',
				);
				$controllers[] = array(
					'page' => $page,
					'controller' => $controller,
					'php_files' => [$root_dir . '/' . $this->_formatController($controller) . '.php'],
					'class' => $namespace.'\\'.$this->_formatController($controller),
					'_page' => false,
				);
			}
		}



		for ($i = 0; $i < count($controllers); $i++) {
			$php_files = $controllers[$i]['php_files'];
			$class = $controllers[$i]['class'];
			$page = $controllers[$i]['page'];
			foreach ($php_files as $php_file) {
				if (is_readable($php_file)) {
					$cr = new $class;
					if (method_exists($cr, $page)) {
						call_user_func(array($cr, $page));
						return true;
					} elseif (!method_exists($cr, $page) && method_exists($cr, '__call')) {
						$arguments = array();
						$cr->_call($page, $arguments);
						return true;
					}
				}
			}
		}

		throw new RouterException('Document not found');

	}


	/**
	 * Formats the initial page by special case
	 * @param string $page_name
	 * @return string
	 */
	private function _formatPage($page_name = '')
	{
		$tmp = explode('-', $page_name);
		$first = array_shift($tmp);
		return $first . join('', explode(' ', ucwords(join(' ', $tmp))));
		return join('_', explode(' ', $tmp));
	}


	private function _formatController($controller_name = '')
	{
		$tmp = ucwords(join(' ', explode('-', $controller_name)));
		return join('', explode(' ', $tmp));
	}

	/**
	 * @param string $page_controller
	 * @return string
	 */
	private function _formatPageController($page_controller = '')
	{
		$tmp = ucwords(join(' ', explode('-', $page_controller)));
		return join('_', explode(' ', $tmp));
	}


	/**
	 * Helper function to get file name, controller and route
	 * from the request
	 * 
	 * @return array
	 */
	private function _traverseRoute()
	{
		$route = $this->route;
		$last = end($route);
		if (count($this->route) == 1) {
			$page = array_pop($route);
			$controller = '';
			return array($page, $controller, $route);

		} elseif (count($this->route) > 1) {
			$page = array_pop($route);
			$controller = array_pop($route);
			if ($page == '') {
				$page = $this->Bootstrap->root;
			}
			return array($page, $controller, $route);

		} else {
			return array('index', '', $route);
		}

	}


	/**
	 * Gets the file name from traversing the route
	 * 
	 * @return string
	 */
	public function getFileName()
	{
		list($file_name, $controller, $path) = $this->_traverseRoute();

		// this is how to handle the trailing slash
		// the controller doesn't exist, which means we are accessing
		// a controller without a backslash in the URL, so we set
		// the file_name = '' so that it defaults - the file_name
		// becomes the empty while the controller becomes the
		// file_name
		if ($controller == '') {
			return '';
		} else {

			if ($file_name == '') {
				return $this->Bootstrap->root;
			} else {
				return $file_name;
			}
		}
	}

}

/**
 * Shell for the router exception thrown
 */
class RouterException extends \Exception {}
