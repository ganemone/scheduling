		<div class='notifications top-right'></div>
		<h2>Missed Sales Statistics</h2>
		<hr>
		<div class='jumbotron'>
			<div class='container'>
				<div class='row'>
					<div class='col-6'>
						<div class='well'>
							<h2>Over Time</h2>
							<div id="time" style='height: 250px;'>&nbsp</div>
						</div>
					</div>
					<div class='col-6'>
						<div class='well'>
							<h2>By Vendor</h2>
							<div id="vendor" style='height: 250px;'>&nbsp</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<hr>
		<br>
		<div class='jumbotron'>
			<div class='container'>
				<div class='row'>
					<div class='col-6'>
						<div class='well'>
							<h2>By Category</h2>
							<div id="category" style='height: 250px;'>&nbsp</div>
						</div>
					</div>
					<div class='col-6'>
						<div class='well'>
							<h2>By Gender</h2>
							<div id="gender" style='height: 250px;'>&nbsp</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
	<table id='date_table' style='display:none;'>
		<? foreach($time as $data): ?>
		<tr>
			<td class='date'><? echo $data['date'] ?></td>
			<td class='amount'><? echo $data['amount'] ?></td>
		</tr>
		<? endforeach; ?>
	</table>
	
	<hr>
	
	<table id='vendor_table' style='display:none;'>
		<? foreach($vendor as $data): ?>
		<tr>
			<td class='vendor'><? echo $data['vendor'] ?></td>
			<td class='amount'><? echo $data['amount'] ?></td>
		</tr>
		<? endforeach; ?>
	</table>

	<hr>

	<table id='category_table' style='display:none;'>
		<? foreach($category as $data): ?>
		<tr>
			<td class='category'><? echo $data['category'] ?></td>
			<td class='amount'><? echo $data['amount'] ?></td>
		</tr>
		<? endforeach; ?>
	</table>

	<hr>

	<table id='gender_table' style='display:none;'>
		<? foreach($gender as $data): ?>
		<tr>
			<td class='gender'><? echo $data['gender'] ?></td>
			<td class='amount'><? echo $data['amount'] ?></td>
		</tr>
		<? endforeach; ?>
	</table>
</html>
<script src="<? echo base_url() ?>js/utility.js"></script>
<link rel="stylesheet" href="http://cdn.oesmith.co.uk/morris-0.4.3.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="http://cdn.oesmith.co.uk/morris-0.4.3.min.js"></script>
<script type="text/javascript" src="<? echo base_url() ?>js/stats.js"></script>
