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
 * @package     Controllers
 * @license     GNU license
 * @version     1.0
 * @link        https://github.com/DataHerder/UltraMVC
 * @since       File available since
 */


/**
 * Abstract controller class
 * Constructs the needed classes and calls the page
 * 
 * @package Controllers
 * @subpackage ControllerAbstract
 */
abstract class UltraController
{

	/**
	 * Loader class - used to load view primarily
	 * ex: 
	 *  // $this is in Controller context
	 *  $this->Load->view("hi");
	 * 
	 * @var \UltraMVC\Views\Loader
	 * @access public
	 */
	public $Load = null;

	/**
	 * Loader Class
	 * 
	 * @var \UltraMVC\Views\Errors\Error
	 * @access protected
	 */
	protected $Error = null;

	/**
	 * 
	 * @var \UltraMVC\UltraMVCBootstrap
	 * @access protected
	 */
	protected $Bootstrap = null;

	/**
	 * Constructor
	 * Loads the classes on instantiation
	 */
	public function __construct()
	{
		$this->Error = new \UltraMVC\Views\Errors\Error;
		$this->Load = new \UltraMVC\Views\Loader;
	}

	/**
	 * Calls the Page
	 *
	 * @param \UltraMVC\Routers\Router $Router $Router
	 */
	public function callPage(\UltraMVC\Routers\Router $Router)
	{
	}
}
