<?php
	// If the IN_APP setting isn't already set
	if(!defined('IN_APP')) {
		exit; // exit application
	}
?>

<div class="row">
	<div class="col-md-12">
		<?php
			// If currently editing or creating permission
			if($action == 'edit' || $action == 'add') {
		?>

		<div class="col-md-6">
			<?php
				// If currently creating permission
				if($action == 'add') {
			?>

			<h1>Add permission</h1>

			<?php
				} // End if creating permission

				// If editing permission
				else {
			?>

			<h1>Edit permission</h1>

			<?php
				} // End if editing permission
			?>

			<form method="post">
				<div class="form-group">
					<label >Name</label>
					<input required="required" type="text" name="data[name]" class="form-control" placeholder="Permission name" value="<?php echo $data['name'] ;?>" >
				</div>

				<button type="submit" class="btn btn-default">Save</button>
			</form>
		</div>

		<?php
			} // End if currently editing or creating a permission

			// If not currently editing or creating a permission
			else {
		?>

		<h1>Permissions list</h1>

		<p>
			<a class="btn btn-success btn-xs" href="<?php echo Router::url('/admin_permissions?action=add');?>" >Add</a>
		</p>

		<table class="table table-bordered">
			<tr>
				<th>Id</th>
				<th>Name</th>
				<th style="width: 10%">Actions</th>
			</tr>

			<?php
				// Loop through permissions
				foreach ($permissions as $per) {
			?>

			<tr>
				<td><?php echo $per['id'] ;?></td>
				<td><?php echo $per['name'] ;?></td>
				<td>
					<a class="btn btn-info btn-xs" href="<?php echoRouter::url('/admin_permissions?action=edit&id='.$per['id']);?>" >Edit</a> &bull;
					<a onclick="return confirm('Are you sure?');" class="btn btn-danger btn-xs" href="<?php echo Router::url('/admin_permissions?action=delete&id='.$per['id']);?>">Delete</a>
				</td>
			</tr>

			<?php
				} // End permissions loop
			?>
		</table>

		<?php
			} // End if not currently editing or creating a permission
		?>
	</div>
</div>