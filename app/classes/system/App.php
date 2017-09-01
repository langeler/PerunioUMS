<?php

class App extends Obj{

	public static function run() {

		//send content type header
		Utils::header('html');

		$route = Router::find();

		if(is_null($route)){
			throw new NotFoundException;
		}

		App::processRoute($route['route'],$route['args']);
	}

	public static function processRoute($route=array(),$args=array()) {

		if(is_string($route['callback']) && !is_callable($route['callback'])){

			$parts = explode('@',$route['callback']);

			if(count($parts)<2){
				throw new NotFoundException;
			}

			$className = $parts[0];
			$methodName = $parts[1];

			if(!class_exists($className)){
				throw new InternalException(
					'Class not found - ' . $className . '::class',
					500
				);
			}
			if(!method_exists($className,$methodName)){
				throw new InternalException(
					'Method not found - ' . $className . '::'. $methodName,
					500
				);
			}

			//check if page allowed to be accessed by current user
			Permissions::make()->checkIfUserAllowedToAccess();

			try{
				$controller =  $className::make();

				call_user_func_array(
					array($controller,$methodName),
					$route['options']+$args
				);

			}

			catch (\Exception $e){
				throw new InternalException(
					$e->getMessage(),
					500
				);
			}

		}

		elseif(is_callable($route['callback'])){

			//check if page allowed to be accessed by current user
			Permissions::make()->checkIfUserAllowedToAccess();

			call_user_func_array($route['callback'],$route['options'] + $args);
		}

		else{
			throw new NotFoundException;
		}

		return true;
	}
}