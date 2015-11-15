<?php
namespace Application;

// skeleton to the mvc bootstrap
class Bootstrap extends \UltraMVC\UltraMVCBootstrap{

	public function __construct() {
		parent::__construct();
	}

	/**
	 * This is a required function of the bootstrap where you setup your auto loading features
	 * for other libraries you want to link easily into the UltraMVC framework using the
	 * namespaces that already come with it
	 *
	 * @return mixed|void
	 */
	protected function _init()
	{

		// example function of how autoload is used
		// the parts of the path is shows as in the $parts array and passed
		// as reference, you can then manipulate the directory location
		// of the libraries by directly assigning to the $parts array
		$this->registerPath(function(&$parts){
			if ($parts[0] == 'DsnForm' || $parts[0] == 'SqlBuilder') {
				array_unshift($parts,'Modules');
				array_unshift($parts,'Application');
			}
		});


		/**
		 * Register a Route Example
		 *
		 * Registering routes can take some of the headache out of directly mapping
		 * Controllers to files which may happen when you embed into directories
		 *
		 * If you have an issue with embedded files and casing, this will help
		 * alleviate the issue
		 *
		 * Generally speaking you won't need this if you name your files
		 * Proper case, keep apache urls case insensitive in your config,
		 * set the application to case insensitive and follow the documentation.
		 *
		 * However there will always be a straggling case.  You can directly
		 * modify a route by URL here.
		 *
		 * Below you will find 2 flavors:
		 *
		 	$this->registerRoute(
				'my/url/thats-very-specific',
				'SomeOther/Folder/UnrelatedController.php->the_function_that_runs'
			);
			$this->registerRoute('my/url/thats-very-specific', array(
				'file' => 'SomeOther/Folder/UnrelatedController.php',
				'function' => 'the_function_that_runs'
			));
		 */

	}


	/**
	 * This is the hook class
	 *
	 * Right now it's only function really is to set the global database connection when the application
	 * loads.  The function $this->setModel($my_db_class) will then be used in the abstract model
	 * so every model you instantiate will have the instance of the database connection without instantiating
	 * it over and over and over again in your code.
	 *
	 * You can access this instantiation with $this->db when creating a Model class in the /Application/Models directory
	 * and extending the ModelAbstract class
	 *
	 * Example:
	 * $Bootstrap = get_bootstrap();
	 * $Sql= $Bootstrap->db;
	 *
	 */
	protected function _initHook()
	{
		// my model class
		#$Sql = new \SqlBuilder\Sql('mysql', 'host=localhost database=your_database user=root password=');
		#$Sql->setCharset();
		// set the global model
		#$this->setModel($Sql);
	}
}