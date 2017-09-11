<?php

class InternalException extends \Exception{

	public function __construct($message = 'Internal error',$code = 500 ,$previous = null) {

		parent::__construct($message,$code,$previous);

		Utils::header($code);

		Template::make()->setVar('errorCode',$code);

		Template::make()->setVar('message',$message);

		Template::make()->render('system/errors');
	}
}