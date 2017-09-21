<?php
	// If the IN_APP setting isn't already set
	if(!defined('IN_APP')) {
		exit; // exit application
	}
?>

<div class="row">
	<div class="col-md-4 col-md-offset-4">
		<h1>Login</h1>
		<p>&nbsp;</p>
		<form method="post">
			<div class="form-group">
				<label >Username</label>
				<input required="required" type="text" name="data[username]" class="form-control" placeholder="Username" value="<?php echo $data['username'] ;?>">
			</div>

			<div class="form-group">
				<label >Password</label>
				<input required="required" type="password" name="data[password]" class="form-control" placeholder="Password">
			</div>

			<?php
				// Add the form captcha element
				echo $this->element('captcha');
			?>

			<p>
				<small>
					<a href="<?php echo Router::url('/forgot-password') ;?>">Trouble logging in?</a>
				</small>
			</p>

			<button type="submit" class="btn btn-default">Login</button>
		</form>
	</div>
</div>