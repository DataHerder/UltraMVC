<?php

namespace UltraMVC\Api\Library;

class ApiException extends \Exception {
	// codes are required
	public function __construct($message, $code = null, \Exception $previous = null) {
		parent::__construct($message, $code, $previous);
	}
}
