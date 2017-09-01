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
				<label >Email</label>
				<input required="required" type="email" name="data[email]" class="form-control" placeholder="Email">
			</div>

			<?php
				// Add the form captcha element
				echo $this->element('captcha');
			?>

			<button type="submit" class="btn btn-default">Send password recovery instructions</button>
		</form>
	</div>
</div>