<?php 
namespace Application\Controllers;


class Index extends \UltraController {
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
		$this->Load->view('welcome');
	}
}
