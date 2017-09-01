<?php
	// If the IN_APP setting isn't already set
	if(!defined('IN_APP')) {
		exit; // exit application
	}
?>

<div class="row">
	<div class="col-md-12">

		<?php
			// If currently editing a user
			if($action == 'edit') {
		?>

		<div class="col-md-6">
			<h1>Edit user account</h1>

			<form method="post">
				<input type="hidden" name="edit_type" value="account"/>

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

				<div class="form-group">
					<input type="hidden" name="data[active]" value="0"/>

					<label>
						<input type="checkbox" name="data[active]" <?php echo $data['active'] == 1 ? 'checked="checked"' : '';?> value="1" /> Email activated
					</label>
				</div>

				<p>
					<a href="javascript:;" id="show_password_field">Change password</a>
				</p>

				<div class="form-group" id="password_field" style="display: none;">
					<label>New password</label>
					<input type="password" name="data[new_password]" class="form-control" placeholder="Password">
				</div>

				<button type="submit" class="btn btn-default">Save</button>
			</form>
		</div>

		<div class="col-md-6">
			<h1>Edit user permissions</h1>

			<form method="post">
				<input type="hidden" name="edit_type" value="permissions"/>

				<table class="table table-bordered">
					<tr>
						<th>Active</th>
						<th>Permission name</th>
					</tr>

					<?php
						// Loop through user permissions
						foreach ($userPermissions as $k => $per) {
					?>

					<tr>
						<td>
							<input type="checkbox" name="data[permissions][]" value="<?php echo $per['id'] ;?>" <?php echo $per['active'] == 1 ? 'checked="checked"' : '';?> />
						</td>
						<td>
							<?php echo $per['name'] ;?>
						</td>
					</tr>

					<?php
						} // End user permissions loop
					?>
				</table>

				<button type="submit" class="btn btn-default">Save</button>
			</form>
		</div>

		<?php
			} // End if editing user

			// If currently not editing a user
			else {
		?>

		<h1>Users list</h1>
		<table class="table table-bordered">
			<tr>
				<th>Id</th>
				<th>Username</th>
				<th>Registered at</th>
				<th>Last seen</th>
				<th style="width: 10%">Actions</th>
			</tr>

			<?php
				// Loop through users
				foreach ($users as $user) {
			?>

			<tr>
				<td><?php echo $user['id'] ;?></td>
				<td><?php echo $user['username'];?></td>
				<td><?php echo date("j M, Y h:i a" , strtotime($user['registered_at']));?></td>
				<td><?php echo empty($user['last_seen']) ? 'Never' : date("j M, Y h:i a" , strtotime($user['last_seen']));?></td>
				<td>
					<a class="btn btn-info btn-xs" href="<?php echo Router::url('/admin_users?action=edit&id='.$user['id']);?>" >Edit</a> &bull;
					<a onclick="return confirm('Are you sure?');" class="btn btn-danger btn-xs" href="<?php echo Router::url('/admin_users?action=delete&id='.$user['id']);?>">Delete</a>
				</td>
			</tr>

		<?php
			} // End users loop
		?>
	</table>

	<?php
		} // End if currently not editing a suer
	?>
	</div>
</div>