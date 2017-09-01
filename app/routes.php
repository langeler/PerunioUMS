<?php

// Array containing all default pages with their controller action
$defaultPages = array(

	// fundamental pages
	'/' => 'Controller@index',
	'/index' => 'Controller@index',
	'/login' => 'Controller@login',
	'/logout' => 'Controller@logout',
	'/register' => 'Controller@register',
	'/forgot-password' => 'Controller@forgotPassword',
	'/activate-account' => 'Controller@activateAccount',
	'/resend-activation' => 'Controller@resendActivation',
	'/recover-password' => 'Controller@recoverPassword',
	'/account' => 'Controller@account',

	// admin pages
	'/admin_configuration' => 'Controller@adminConfiguration',
	'/admin_users' => 'Controller@adminUsers',
	'/admin_permissions' => 'Controller@adminPermissions',
	'/admin_pages' => 'Controller@adminPages',
);

// Loop through default pages
foreach ($defaultPages as $path => $action) {

	// Connect a page path and a certain action
	Router::connect($path,$action);
}

// connect custom pages
$allPages = Pages::make()->getAll(
	'list'
);

// Loop through all site pages
foreach ($allPages as $id => $name) {

	// Connect all site pages with a specefic action
	Router::connect(
		'/' . $name ,
		'Controller@customPage',
		array(
			'page_name' => $name
		)
	);
}