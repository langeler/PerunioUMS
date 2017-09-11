<?php

class NotFoundException extends \Exception{

	public function __construct($message = 'Page not found',$code = 404 ,$previous = null) {

		parent::__construct($message,$code,$previous);

		Utils::header($code);

		Template::make()->setVar('errorCode',$code);

		Template::make()->setVar('message',$message);

		Template::make()->render('system/errors');
	}
}