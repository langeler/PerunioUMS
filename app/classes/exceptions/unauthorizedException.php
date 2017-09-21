<?php

class unauthorizedException extends \Exception{

	public function __construct($message = 'You are not allowed to access this page',$code = 401, $previous = null) {

		parent::__construct($message, $code,$previous);

		Utils::header($code);

		Template::make()->setVar('errorCode', $code);
		Template::make()->setVar('message', $message);
		Template::make()->render('system/errors');
	}
}