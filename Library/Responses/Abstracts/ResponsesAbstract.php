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
namespace UltraMVC\Responses\Abstracts;

use UltraMVC\Views\Loader;

/**
 * Class ResponsesAbstract
 * @package UltraMVC\Responses\Abstracts
 */
abstract class ResponsesAbstract extends \Exception {

	/**
	 * @var int
	 */
	protected $response_code = 0;

	/**
	 * @var bool
	 */
	protected $internal_error = false;

	/**
	 * @var null|string
	 */
	protected $error_message = null;

	/**
	 * @var array
	 */
	protected $list_of_response_codes = array();

	/**
	 * @var bool|callable
	 */
	protected $callable = false;

	/**
	 * @var array
	 */
	private $allowed = array();

	/**
	 * Construct the http response exception
	 *
	 * @param int $response_code
	 * @param string|callable $message_or_callback
	 * @param int $code
	 * @param \Exception $previous
	 */
	public function __construct($response_code = 0, $message_or_callback, $code = 0, \Exception $previous = null)
	{

		$this->response_code = $response_code;

		// allowed response numbers
		$this->list_of_response_codes = array(
			'100' => 'Continue',
			'101' => 'Switching Protocols',
			'102' => 'Processing',
			'103' => 'checkpoint',
			'200' => 'OK',
			'201' => 'Created',
			'202' => 'Accepted',
			'203' => 'Non-Authoritative Information',
			'204' => 'No Content',
			'205' => 'Reset Content',
			'206' => 'Partial Content',
			'207' => 'Multi-Status',
			'208' => 'Already Reported',
			'226' => 'IM Used',
			'300' => 'Multiple Choices',
			'301' => 'Moved Permanently',
			'302' => 'Found',
			'303' => 'See Other',
			'304' => 'Not Modified',
			'305' => 'Use Proxy',
			'306' => 'Switch Proxy',
			'307' => 'Temporary Redirect',
			'308' => 'Permanent Redirect',
			//'404' => 'error on German Wikipedia',
			'400' => 'Bad Request',
			'401' => 'Unauthorized',
			'402' => 'Payment Required',
			'403' => 'Forbidden',
			'404' => 'Not Found',
			'405' => 'Method Not Allowed',
			'406' => 'Not Acceptable',
			'407' => 'Proxy Authentication Required',
			'408' => 'Request Timeout',
			'409' => 'Conflict',
			'410' => 'Gone',
			'411' => 'Length Required',
			'412' => 'Precondition Failed',
			'413' => 'Payload Too Large',
			'414' => 'URI Too Long',
			'415' => 'Unsupported Media Type',
			'416' => 'Range Not Satisfiable',
			'417' => 'Expectation Failed',
			'418' => 'I\'m a teapot',
			'419' => 'Authentication Timeout',
			'421' => 'Misdirected Request',
			'422' => 'Unprocessable Entity',
			'423' => 'Locked',
			'424' => 'Failed Dependency',
			'426' => 'Upgrade Required',
			'428' => 'Precondition Required',
			'429' => 'Too Many Requests',
			'431' => 'Request Header Fields Too Large',
			'451' => 'Unavailable For Legal Reasons',
			'500' => 'Internal Server Error',
			'501' => 'Not Implemented',
			'502' => 'Bad Gateway',
			'503' => 'Service Unavailable',
			'504' => 'Gateway Timeout',
			'505' => 'HTTP Version Not Supported',
			'506' => 'Variant Also Negotiates',
			'507' => 'Insufficient Storage',
			'508' => 'Loop Detected',
			'510' => 'Not Extended',
			'511' => 'Network Authentication Required',
			'420' => 'Method Failure',
			//'420' => 'Enhance Your Calm',
			'450' => 'Blocked by Windows Parental Controls',
			'498' => 'Invalid Token',
			'499' => 'Token Required',
			'509' => 'Bandwidth Limit Exceeded',
			'440' => 'Login Timeout',
			'449' => 'Retry With',
			//'451' => 'Redirect',
			'444' => 'No Response',
			'495' => 'SSL Certificate Error',
			'496' => 'SSL Certificate Required',
			'497' => 'HTTP Request Sent to HTTPS Port',
			//'499' => 'Client Closed Request',
			'520' => 'Unknown Error',
			'521' => 'Web Server Is Down',
			'522' => 'Connection Timed Out',
			'523' => 'Origin Is Unreachable',
			'524' => 'A Timeout Occurred',
			'525' => 'SSL Handshake Failed',
			'526' => 'Invalid SSL Certificate',
		);

		$found = in_array($this->response_code, array_keys($this->list_of_response_codes));

		if (!$found) {
			$this->internal_error = true;
			$this->error_message = 'Response Code ' . $response_code . ' is not a valid http response code';
		} else {
			if (is_callable($message_or_callback)) {
				$this->callable = $message_or_callback;
				$message = 'Error: ' . $this->response_code . ' ' . $this->list_of_response_codes[$this->response_code];
			} else {
				$message = $message_or_callback;
			}
			parent::__construct($message, $response_code, $previous);
			$this->response_code = $response_code;
			if ($this->response_code == 0) {
				$this->internal_error = true;
				$this->error_message = 'Response Code set 0: Not a valid http response code';
			}
		}

	}


	/**
	 * @param array $params
	 * @throws ResponseAbstractException
	 */
	public function redirect($params = array())
	{
		if (isSet($params['force_code']) && in_array($params['force_code'], $this->allowed)) {
			$code = $params['force_code'];
		} else {
			$code = $this->response_code;
		}
		if (is_array($params) && empty($params)) {
			throw new ResponseAbstractException('Redirect parameter not set. String or array must be given');
		} else {
			if (is_array($params) && !isSet($params['redirect_url'])) {
				throw new ResponseAbstractException('Redirect url parameter not set. String must be given');
			} elseif (is_array($params)) {
				$redirect = $params['redirect_url'];
			} elseif (is_string($params)) {
				$redirect = $params; 
			} else {
				throw new ResponseAbstractException('Invalid type set for redirect url paramter. String must be given');
			}
		}
		header('Location: ' . $redirect, true, $code);
		die;
		
	}


	/**
	 * @param bool $html
	 * @throws \UltraMVC\Views\LoaderException
	 */
	public function loadView($html = true)
	{
		$response_code_message = $this->list_of_response_codes[$this->response_code];
		header('HTTP/1.1 '.$this->response_code. ' '.$response_code_message);
		$Loader = new Loader();
		if (is_callable($this->callable)) {
			call_user_func($this->{'callable'});
		} elseif ($Loader->viewExists('responses/'.$this->response_code)) {
			$Loader->view('responses/' . $this->response_code);
		} else {
			if ($html) {
			print '
				<!DOCTYPE html>
					<html>
						<head>
							<title>'.$this->response_code.' ' . $response_code_message . '</title>
						</head>
						<body>
							There was an error processing your request.  Response code: '.$this->response_code.' ' .
							$response_code_message . '
						</body>
					</html>'
				;
			}
		}
	}

	/**
	 * @return bool
	 */
	public function hasError()
	{
		return $this->internal_error;
	}

	/**
	 * @return null|string
	 */
	public function getErrorMessage()
	{
		return $this->error_message;
	}
}


class ResponseAbstractException extends \Exception {}