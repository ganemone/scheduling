		<div class='notifications top-right'></div>
		<div class='table-container'>
			<table class='table table-condensed'>
				<th>Date</th>
				<th>Goal</th>
				<th>Edit</th>
				<th>Delete<th>
				<? foreach($goals as $row): ?>
				<tr>
					<? foreach($row as $td) : ?>
					<td><? echo $td ?></td>
					<? endforeach; ?>
					<td><button class='btn btn-primary'>Edit</button></td>
					<td><button class='btn btn-danger'>Delete</button></td>
				</tr>
				<? endforeach; ?>
			</table>
		</div>
		<button class='btn btn-success'>Insert Goals</button>
	</body>
</html>
<script src="<? echo base_url() ?>js/utility.js"></script>
<script type="text/javascript" src="<? echo base_url() ?>js/settings.js"></script>