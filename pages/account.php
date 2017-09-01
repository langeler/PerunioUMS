<?php
	// If the IN_APP setting isn't already set
	if(!defined('IN_APP')) {
		exit; // exit application
	}
?>

<div class="row">
	<?php
		// If user isn't editing their account
		if(!isset($edit_account)) {
	?>

	<div class="col-md-4 col-md-offset-4">
		<h1>Change account setting</h1>
		<form method="post">
			<div class="form-group">
				<label >Password</label>
				<input required="required" type="password" name="data[password]" class="form-control" placeholder="Password">
			</div>

			<button type="submit" class="btn btn-default">Continue</button>
		</form>
	</div>

	<?php
		} // End if user isn't editing their account

		// If user is editing their account
		else {
	?>

	<div class="col-md-4 col-md-offset-4">
		<h1>Change account setting</h1>
		<form method="post">
			<input type="hidden" name="data[password]"	value="<?php echo $data['password'] ;?>"/>
			<div class="form-group">
				<label >Username</label>
				<input required="required" type="text" name="data[username]" class="form-control" placeholder="Username" value="<?php echo $data['username'] ;?>" readonly="readonly">
			</div>
			<div class="form-group">
				<label >Display name</label>
				<input required="required" type="text" name="data[display_name]" class="form-control" placeholder="Display name" value="<?php echo $data['display_name'] ;?>">
			</div>
			<div class="form-group">
				<label >Email</label>
				<input required="required" type="email" name="data[email]" class="form-control" placeholder="Email" value="<?php echo $data['email'] ;?>">
			</div>

			<p>
				<a href="javascript:;" id="show_password_field">Change password</a>
			</p>

			<div class="form-group" id="password_field" style="display: none;">
				<label >New password</label>
				<input type="password" name="data[new_password]" class="form-control" placeholder="Password">
			</div>

			<button type="submit" class="btn btn-default">Save</button>
		</form>
	</div>

	<?php
		} // End if user is editing their account
	?>
</div>