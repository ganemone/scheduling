		<div class='notifications top-right'></div>
		<div class='table-container'>
			<table class='table table-condensed'>
				<tr>
					<th>Employee Id</th>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Position</th>
					<th>Permissions</th>
					<th>Email</th>
					<th>Wage</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>
				<? foreach($employees as $row): ?>
				<tr>
					<? foreach($row as $td) : ?>
					<td><? echo $td ?></td>
					<? endforeach; ?>
					<td><button class='btn btn-primary' onclick="edit_employee(this, '<? echo $row->id ?>')">Edit</button></td>
					<td><button class='btn btn-danger' onclick="delete_employee(this, '<? echo $row->firstName?>','<? echo $row->id ?>')">Delete</button></td>
				</tr>
				<? endforeach; ?>
			</table>
		</div>
		<button class='btn btn-success' onclick='add_employee();'>Add Employee</button>
	</body>
</html>
<script src="<? echo base_url() ?>js/utility.js"></script>
<script type="text/javascript" src="<? echo base_url() ?>js/settings.js"></script>
