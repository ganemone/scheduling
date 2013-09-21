		<div class='notifications top-right'></div>
		<div class='table-container'>
			<table class='table table-condensed'>
				<tr>
					<th>Group Name</th>
					<th>Group Abbreviation</th>
					<th>Employees</th>
					<th>Edit Group</th>
					<th>Delete Group</th>
					<th>Add Employee</th>
				</tr>
				<? foreach($groups as $row): ?>
				<tr>
					<td><? echo $row["name"] ?></td>
					<td><? echo $row["abbr"] ?></td>
					<td>
						<table>
							<? foreach ($row["employees"] as $employee_row): ?>
							<tr>
								<td><? echo $employee_row->firstName . " " . $employee_row->lastName ?></td>
								<td><button class='btn btn-primary' onclick="remove_employee_from_group($row->group_id, '<? echo $employee_row->id ?>');">Remove</td>
							</tr>
							<? endforeach; ?>
						</table>	
					</td>
					<td><button class='btn btn-primary'>Edit Group</button></td>
					<td><button class='btn btn-danger'>Delete Group</button></td>
					<td><button class='btn btn-success'>Add Employee</button></td>
				</tr>
				<? endforeach; ?>
			</table>
		</div>
		<button class='btn btn-success'>Create Group</button>
	</body>
</html>
<script src="<? echo base_url() ?>js/utility.js"></script>
<script type="text/javascript" src="<? echo base_url() ?>js/settings.js"></script>