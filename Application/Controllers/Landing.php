<?php 
namespace Application\Controllers;

class Landing extends \UltraMVC\Controllers\ControllerAbstract {
	public function __construct(){
		parent::__construct();
	}

	/**
	 * This gives the immediate landing page
	 * 
	 * 
	 */
	public function index()
	{
		$this->Load->view('Landings/welcome');
	}

}