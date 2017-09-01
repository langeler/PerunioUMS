<?php
	// If the IN_APP setting isn't already set
	if(!defined('IN_APP')) {
		exit; // exit application
	}
?>

<div class="row">
	<div class="col-md-4 col-md-offset-4">
		<h1>Register new account</h1>
		<form method="post">
			<div class="form-group">
				<label >Username</label>
				<input required="required" type="text" name="data[username]" class="form-control" placeholder="Username" value="<?php echo $data['username'] ;?>">
			</div>

			<div class="form-group">
				<label >Display name</label>
				<input required="required" type="text" name="data[display_name]" class="form-control" placeholder="Display name" value="<?php echo $data['display_name'] ;?>">
			</div>

			<div class="form-group">
				<label >Email</label>
				<input required="required" type="email" name="data[email]" class="form-control" placeholder="Email" value="<?php echo $data['email'] ;?>">
			</div>

			<div class="form-group">
				<label >Password</label>
				<input required="required" type="password" name="data[password]" class="form-control" placeholder="Password">
			</div>
			<div class="form-group">
				<label >Confirm password</label>
				<input required="required" type="password" name="data[confirm_password]" class="form-control" placeholder="Confirm password">
			</div>

			<?php
				// Add the form captcha element
				echo $this->element('captcha');
			?>

			<button type="submit" class="btn btn-default">Register</button>
		</form>
	</div>
</div>