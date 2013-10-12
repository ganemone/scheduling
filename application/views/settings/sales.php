		<div class='notifications top-right'></div>
		<? if(isset($message) && isset($message_type)): ?>
			<div class="alert alert-<? echo $message_type ?> notifications bottom-right"><? echo $message ?></div>
		<? endif; ?>
		<div class='table-container'>
			<table class='table table-condensed'>
				<th class='text-center'><a href='<? echo base_url() ?>index.php/settings/sales?order_by=1' class='btn btn-primary'>Date</a></th>
				<th class='text-center'><a href='<? echo base_url() ?>index.php/settings/sales?order_by=2' class='btn btn-primary'>Price</a></th>
				<th class='text-center'><a href='<? echo base_url() ?>index.php/settings/sales?order_by=3' class='btn btn-primary'>Vendor</a></th>
				<th class='text-center'><a href='<? echo base_url() ?>index.php/settings/sales?order_by=4' class='btn btn-primary'>Description</a></th>
				<th class='text-center'><a href='<? echo base_url() ?>index.php/settings/sales?order_by=5' class='btn btn-primary'>Size</a></th>
				<th class='text-center'><a href='<? echo base_url() ?>index.php/settings/sales?order_by=6' class='btn btn-primary'>Category</a></th>
				<th class='text-center'><a href='<? echo base_url() ?>index.php/settings/sales?order_by=8' class='btn btn-primary'>Gender</a></th>
				<th class='text-center'><a href='<? echo base_url() ?>index.php/settings/sales?order_by=7' class='btn btn-primary'>Quantity</a></th>
				<th class='text-center'>Edit</th>
				<th class='text-center'>Delete</th>
				<? foreach($sales as $row): ?>
				<tr class='sale-<? echo $row->id ?>'>
					<td class='date'><? echo $row->date ?></td>
					<td class='price'><? echo $row->price ?></td>
					<td class='vendor'><? echo $row->vendor ?></td>
					<td class='description'><? echo $row->description ?></td>
					<td class='size'><? echo $row->size ?></td>
					<td class='category'><? echo $row->category ?></td>
					<td class='gender'><? echo $row->gender ?></td>
					<td class='quantity'><? echo $row->quantity ?></td>

					<td><button class='btn btn-primary' onclick="edit_missed_sale('<? echo $row->id ?>');">Edit</button></td>
					<td><button class='btn btn-danger'  onclick="delete_missed_sale('<? echo $row->id ?>');">Delete</button></td>
				</tr>
				<? endforeach; ?>
			</table>
		</div>
	</body>
</html>

<script src="<? echo base_url() ?>js/utility.js"></script>
<script type="text/javascript" src="<? echo base_url() ?>js/settings.js"></script>
