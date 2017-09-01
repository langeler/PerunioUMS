<?php

$lang = array();

//misc
$lang = array_merge($lang,
	array(
		'back_to_home_page' => 'Återvänd till startsidan.',
		'unknown_error' => 'Oidentifierat fel, var god försök igen.'
	)
);

//users
$lang = array_merge($lang,
	array(
		'users_all_fields_are_required' => 'Var god och fyll i alla fält.',
		'users_username_not_available' => 'Användarnamnet är redan upptaget.',
		'users_email_not_available' => 'Email addressen används redan.',
		'users_short_password' => 'Lösenordet måste innehålla minst 8 tecken.',
		'users_password_confirmation_not_correct' => 'Lösenordet och återgivandet av lösenordet måste matcha varandra.',
		'users_username_invalid_characters' => 'Användarnamn kan bara innehålla alfabetiska och numeriska tecken.',
		'users_email_not_valid' => 'Var god och ange en korrekt email address.',
		'users_registered_success' => 'Ditt konto har skapats, du kan nu logga in.',
		'users_registered_success_email_activation_required' => 'Ditt konto har skapats. Var god och kolla din email för att aktivera ditt konto.',
		'users_captcha_fail' => 'Var god och ange säkerhets testet korrekt.',
		'users_short_username' => 'Användarnamn måste vara mellan 3 och 25 tecken långt.',
		'users_account_activated' => 'Ditt konto har aktiverats.',
		'users_email_not_found' => 'Den angivna email addressen kunde inte hittas.',
		'users_password_changed_success' => 'Ditt lösenord har ändrats.',
		'users_account_updated_successfully' => 'Ditt konto har uppdaterats.',
		'users_username_or_password_incorrect' => 'Användarnamn eller lösenord stämmer inte.',
		'users_password_incorrect' => 'Lösenordet stämmer inte.',
		'users_welcome_message' => 'Välkommen tillbaka.',
		'users_deleted_successfully' => 'Användaren har raderats.',
		'users_permissions_updated_successfully' => 'Användar rättigheten har uppdaterats.',
		'users_active_account_required'=>sprintf(
			'Var god kolla din email för att aktivera ditt konto, %s.',
			'<a href="'.Router::url('/resend-activation',true).'">Skicka email igen</a>'
		),
	)
);

//emails
$lang = array_merge($lang,
	array(
		'email_registration_subject'=>sprintf(
			'Välkommen till %s',
			getOption('website_name','PerunioCMS')
		),
		'email_recovery_subject' => 'Återställ lösenord.',
		'email_recovery_message_sent' => 'Var god kolla din email för återställning av ditt lösenord.',
		'email_activation_sent' => 'Aktiverings email har skickats.',
	)
);

//admin
$lang = array_merge($lang,
	array(
		'admin_website_settings_updated' => 'Hemsidans inställningar har uppdaterats.'
	)
);

//permissions
$lang = array_merge($lang,
	array(
		'permissions_updated_successfully' => 'Rättigheten har uppdaterats.',
		'permissions_deleted_successfully' => 'Rättigheten har raderats.',
		'permissions_added_successfully' => 'Rättigheten har skapats.'
	)
);

//pages
$lang = array_merge($lang,
	array(
		'pages_updated_successfully' => 'Sidan har uppdaterats.',
		'pages_private_only' => 'Denna sida bör inte vara publik!',
		'pages_public_only' => 'Denna sida bör inte vara privat!',
		'pages_permissions_updated_successfully' => 'Sidans rättigheter har uppdaterats.',
	)
);

return $lang;