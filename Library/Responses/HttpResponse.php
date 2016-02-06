<?php 

namespace UltraMVC\Responses;

final class HttpResponse extends Abstracts\ResponsesAbstract {

	/**
	 * Construct the response
	 *
	 * @param int $response_code
	 * @param string|callable|boolean $message_or_callback
	 * @param int $code
	 * @param \Exception $previous
	 */
	public function __construct($response_code = 0, $message_or_callback = '', $code = 0, \Exception $previous = null)
	{
		parent::__construct($response_code, $message_or_callback, $code, $previous);
		// check the bootstrap
	}


	private $functions = array();

	/**
	 * Executes functions before the redirect
	 * 
	 * @param string $func
	 */
	public function execute($func = '')
	{
		if (is_callable($func)) {
			$this->functions[] = $func;
		}
	}



}