<?php

$lang = array();

//misc
$lang = array_merge($lang,
	array(
		'back_to_home_page' => 'Back to home page',
		'unknown_error' => 'Unknown error, please try again'
	)
);

//users
$lang = array_merge($lang,
	array(
		'users_all_fields_are_required' => 'Please fill out all fields',
		'users_username_not_available' => 'Username is already in use',
		'users_email_not_available' => 'Email is already in use',
		'users_short_password' => 'Password must be at least 8 characters in length',
		'users_password_confirmation_not_correct' => 'Your password and confirmation password must match',
		'users_username_invalid_characters' => 'Username can only include alpha-numeric characters',
		'users_email_not_valid' => 'Please enter a valid email address',
		'users_registered_success' => 'You have successfully registered. You can now login now',
		'users_registered_success_email_activation_required' => 'You have successfully registered. check your email to activate your account',
		'users_captcha_fail' => 'Please answer the captcha challenge correctly.',
		'users_short_username' => 'Username must be between 3 and 25 characters in length',
		'users_account_activated' => 'Account activated successfully',
		'users_email_not_found' => 'This email was not found in our database',
		'users_password_changed_success' => 'Your password has been changed successfully',
		'users_account_updated_successfully' => 'Account has been updated successfully',
		'users_username_or_password_incorrect' => 'Username or password is incorrect',
		'users_password_incorrect' => 'Password is incorrect',
		'users_welcome_message' => 'Welcome back.',
		'users_deleted_successfully' => 'User deleted successfully',
		'users_permissions_updated_successfully' => 'User permissions updated successfully',
		'users_active_account_required'=>sprintf(
			'Please check you email to activate your account, %s.',
			'<a href="'.Router::url('/resend-activation',true).'">resend activation email</a>'
		),
	)
);

//emails
$lang = array_merge($lang,
	array(
		'email_registration_subject'=>sprintf(
			'Welcome to %s',
			getOption('website_name','PerunioCMS')
		),
		'email_recovery_subject' => 'Password recovery ',
		'email_recovery_message_sent' => 'Please check your email address for password recovery instructions',
		'email_activation_sent' => 'Activation email has been sent',
	)
);

//admin
$lang = array_merge($lang,
	array(
		'admin_website_settings_updated' => 'Website settings updated successfully'
	)
);

//permissions
$lang = array_merge($lang,
	array(
		'permissions_updated_successfully' => 'Permission updated successfully',
		'permissions_deleted_successfully' => 'Permission deleted successfully',
		'permissions_added_successfully' => 'Permission added successfully'
	)
);

//pages
$lang = array_merge($lang,
	array(
		'pages_updated_successfully' => 'Page updated successfully',
		'pages_private_only' => 'This page should not be public',
		'pages_public_only' => 'This page should not be private',
		'pages_permissions_updated_successfully' => 'Page permissions updated successfully',
	)
);

return $lang;