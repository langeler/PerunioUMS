<?php
	// If the IN_APP setting isn't already set
	if(!defined('IN_APP')) {
		exit; // exit application
	}
?>

<div class="bs-component">
	<div class="jumbotron">
		<?php
			// If user isn't logged in
			if(!Utils::isUserLoggedIn()) {
		?>
		<h1>
			Welcome to
			<?php
				// Print website name
				echo getOption('website_name','PerunioCMS');
			?>
		</h1>
		<p>
			If you are new here, start by
			<a href="<?php echo Router::url('/register') ;?>" class="label label-info">creating an account</a>
			or <a href="<?php echo Router::url('/login') ;?>" class="label label-success">login</a> if you already	have one.
		</p>
		<?php
			} // End if user isn't logged in

			// If user is logged in
			else {
		?>
		<h1>
			Hey,
			<?php
				// Print user display_name
				echo Utils::currentUserInfo('display_name');
			?>
		</h1>
		<p>
			this page need some customization , just saying !
		</p>
		<?php
			} // End if user is logged in
		?>
	</div>
</div>