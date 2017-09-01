<?php
	// If the IN_APP setting isn't already set
	if(!defined('IN_APP')) {
		exit; // exit application
	}
?>

<div class="row">
	<div class="col-md-4 col-md-offset-4">
		<h1>Recover password</h1>
		<p>&nbsp;</p>
		<form method="post">
			<div class="form-group">
				<label >Password</label>
				<input required="required" type="password" name="data[password]" class="form-control" placeholder="Password">
			</div>

			<div class="form-group">
				<label >Confirm password</label>
				<input required="required" type="password" name="data[confirm_password]" class="form-control" placeholder="Confirm password">
			</div>

			<button type="submit" class="btn btn-default">Change password</button>
		</form>
	</div>
</div>