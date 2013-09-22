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
				<tr class='group-<? echo $row["group_id"] ?>'>
					<td class='group-name'><? echo $row["name"] ?></td>
					<td class='group-abbr'><? echo $row["abbr"] ?></td>
					<td class='employees'>
						<table class='table'>
							<? if(count($row["employees"]) == 0): ?>
							<tr class='remove'>
								<td>&nbsp</td>
								<td>&nbsp</td>
							</tr>
							<? endif; ?>
							<? foreach ($row["employees"] as $employee_row): ?>
							<tr class='employee-<? echo $employee_row->id ?>'>
								<td><? echo $employee_row->firstName . " " . $employee_row->lastName ?></td>
								<td>
									<button class='btn btn-primary' onclick="remove_employee_from_group('<? echo $employee_row->id ?>', '<? echo $row["group_id"] ?>');">Remove</button>
								</td>
							</tr>
							<? endforeach; ?>
						</table>	
					</td>
					<? $class = ($row["name"] == "SFL") ? "disabled" : ""; ?>

					<td><button class='btn btn-primary <? echo $class ?>' onclick="edit_group('<? echo $row["group_id"] ?>');">Edit Group</button></td>
					<td><button class='btn btn-danger <? echo $class ?>'  onclick="delete_group('<? echo $row["group_id"] ?>');">Delete Group</button></td>
					<td><button class='btn btn-success' onclick="add_employee_to_group('<? echo $row["group_id"] ?>');">Add Employee</button></td>
				</tr>
				<? endforeach; ?>
			</table>
		</div>
		<button class='btn btn-success' onclick='add_group();'>Create Group</button>
	</body>
</html>
<script type='text/javascript'>
	var employee_select = "<? echo $employee_select ?>";
</script>
<script src="<? echo base_url() ?>js/utility.js"></script>
<script type="text/javascript" src="<? echo base_url() ?>js/settings.js"></script>