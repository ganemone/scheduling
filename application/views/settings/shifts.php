		<div class='notifications top-right'></div>
		<div class='table-container'>
			<table class='table table-condensed table-striped'>
				<th>Category Name</th>
				<th>Category Abbreviation</th>
				<th>Edit</th>
				<th>Delete<th>
				<? foreach($shifts as $row): ?>
				<tr class="<? echo $row->category_abbr ?>">
					<? foreach($row as $td) : ?>
					<td><? echo $td ?></td>
					<? endforeach; ?>
					<td><button class='btn btn-primary' onclick="edit_shift_category('<? echo $row->category_abbr ?>', '<? echo $row->category_name ?>');">Edit</button></td>
					<td><button class='btn btn-danger' onclick="delete_shift_category('<? echo $row->category_abbr ?>');">Delete</button></td>
				</tr>
				<? endforeach; ?>
			</table>
		</div>
		<button class='btn btn-success' onclick="add_shift_category();">New Shift Category</button>
	</body>
</html>
<script src="<? echo base_url() ?>js/utility.js"></script>
<script type="text/javascript" src="<? echo base_url() ?>js/settings.js"></script>