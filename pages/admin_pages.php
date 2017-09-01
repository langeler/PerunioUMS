<?php
	// If the IN_APP setting isn't already set
	if(!defined('IN_APP')) {
		exit; // exit application
	}
?>

<div class="row">
	<div class="col-md-12">

		<?php
			// If currently editing page
			if($action == 'edit') {
		?>

		<div class="col-md-6">
			<h1>
				Edit page :
				<a target="_blank" href="<?php echo Router::url('/' . $data['name']);?>"><?php echo $data['name'] ;?></a>
			</h1>

			<form method="post">
				<input type="hidden" name="edit_type" value="page" />

				<div class="form-group">
					<input type="hidden" name="data[private]" value="0"/>
					<label>
						<input type="checkbox" name="data[private]" <?php echo $data['private'] == 1 ? 'checked="checked"' : '';?> value="1" /> private page
					</label>
				</div>

				<button type="submit" class="btn btn-default">Save</button>
			</form>
		</div>

		<div class="col-md-6">
			<h1>Edit page permissions</h1>

			<form method="post">
				<input type="hidden" name="edit_type" value="permissions" />

				<table class="table table-bordered">
					<tr>
						<th>Active</th>
						<th>Permission name</th>
					</tr>

					<?php
						// Loop through page permissions
						foreach ($pagePermissions as $k => $per) {
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
						} // End page permissions loop
					?>

				</table>

				<button type="submit" class="btn btn-default">Save</button>
			</form>
		</div>

		<?php
			} // End if currently editing page

			// If not currently editing page
			else {
		?>

		<h1>Pages list</h1>

		<table class="table table-bordered">
			<tr>
				<th>Id</th>
				<th>Page</th>
				<th>Private</th>
				<th style="width: 6%">Actions</th>
			</tr>

			<?php
				// Loop through pages
				foreach ($pages as $page) {
			?>

			<tr>
				<td><?php echo $page['id'] ;?></td>
				<td><a target="_blank" href="<?php echo Router::url('/'.$page['name'] );?>" ><?php echo $page['name'] ;?></a></td>

				<?php
					// If page is public
					if($page['private'] == 0) {
				?>

				<th><span class="label label-danger">No</span></th>

				<?php
					} // End if page is public

					// If page isn't public
					else {
				?>

				<th><span class="label label-success">Yes</span></th>

				<?php
					} // End if page isn't public
				?>

				<td>
					<a class="btn btn-info btn-xs" href="<?php echo Router::url('/admin_pages?action=edit&id='.$page['id']);?>" >Edit</a>
				</td>
			</tr>

			<?php
				} // End page loop
			?>
		</table>

		<?php
			} // End if not currently editing page
		?>
	</div>
</div>