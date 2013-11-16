		<div class='notifications top-right'></div>
		<? if(isset($message) && isset($message_type)): ?>
			<div class="alert alert-<? echo $message_type ?> notifications bottom-right"><? echo $message ?></div>
		<? endif; ?>
		<div class='table-container'>
			<table class='table table-condensed table-striped'>
				<th>Date</th>
				<th>Goal</th>
				<th>Edit</th>
				<th>Delete<th>
				<? foreach($goals as $row): ?>
				<tr class='goal-<? echo $row->id ?>'>
					<td class='date'><? echo $row->date ?></td>
					<td class='goal'><? echo $row->goal ?></td>
					<td><button class='btn btn-primary' onclick="edit_goal('<? echo $row->id ?>');">Edit</button></td>
					<td><button class='btn btn-danger' onclick="delete_goal('<? echo $row->id ?>');">Delete</button></td>
				</tr>
				<? endforeach; ?>
			</table>
		</div>
		<form method='post' action='<? echo base_url() ?>index.php/settings/upload_goals' enctype="multipart/form-data">
			<input type='file' name='file' id='file' style='display: inline;' class='btn btn-primary' />
			<input type='submit' value="Upload" class='btn btn-success'>
		</form>
	</body>
</html>

<script src="<? echo base_url() ?>js/utility.js"></script>
<script type="text/javascript" src="<? echo base_url() ?>js/settings.js"></script>
