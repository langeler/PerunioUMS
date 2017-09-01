<?php
	// If the IN_APP setting isn't already set
	if(!defined('IN_APP')) {
		exit; // exit application
	}
?>

<div class="row">
	<div class="col-md-4 col-md-offset-4">
		<h1>Website setting</h1>
		<form method="post">
			<div class="form-group">
				<label >Website name</label>
				<input required="required" type="text" name="data[website_name]" class="form-control" placeholder="Website name" value="<?php echo $data['website_name'] ;?>" />
			</div>
			<div class="form-group">
				<input type="hidden" name="data[email_activation_required]" value="0"/>
				<label>
					<input type="checkbox" name="data[email_activation_required]" value="1" <?php echo $data['email_activation_required'] == 1 ? 'checked="checked"' : '';?> />Email activation required
				</label>
			</div>

			<div class="form-group">
				<label >Language</label>
				<select name="data[website_language]" class="form-control">
					<?php
						// Loop through optional languages
						foreach ($languages as $lang) {
					?>

					<option
						<?php echo $data['website_language'] == $lang ? 'selected="selected"' : '';?> value="<?php echo $lang ;?>" /> <?php echo $lang;?>
					</option>

					<?php
						} // End language loop
					?>
				</select>
			</div>

			<div class="form-group">
				<label >Default permission</label>
				<select name="data[default_permission]"	 class="form-control">
					<?php
						// Loop through permissions
						foreach ($permissions as $k => $per) {
					?>

					<option
						<?php echo $data['default_permission'] == $k ? 'selected="selected"' : '';?> value="<?php echo $k ;?>"><?php echo $per ;?>
					</option>

					<?php
						} // End permissions loop
					?>
				</select>
			</div>

			<h3>Email</h3>
			<div class="form-group">
				<label >From email</label>
				<input required="required" type="email" name="data[email_from_email]" class="form-control" placeholder="From email" value="<?php echo $data['email_from_email'] ;?>">
			</div>

			<div class="form-group">
				<label >From name</label>
				<input required="required" type="text" name="data[email_from_name]" class="form-control" placeholder="From name" value="<?php echo $data['email_from_name'] ;?>">
			</div>

			<button type="submit" class="btn btn-default">Save</button>
		</form>
	</div>
</div>