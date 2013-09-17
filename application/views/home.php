<body>
	<br>
	<div class='jumbotron'>
		<h2 class='text-center'>Choose a calendar</h2>
		<hr>
	<? 
	if($permissions == 0)
	   header("location: " . base_url() . "index.php/site");
	else if($permissions == 1)
	   header("location: " . base_url() . "index.php/user/userCalendar");
	else if($permissions == 2): ?>
	<div class='container'>
		<div class='row'>
			<div class='col-lg-6 col-md-6 text-center'>
	   		<a href='<? echo base_url() . "index.php/user/userCalendar" ?>' class='btn btn-primary btn-large calendar-button'>User Calendar</a>
	   		</div>
	   		<div class='col-lg-6 col-md-6 text-center'>
	   		<a href='<? echo base_url() . "index.php/sfl" ?>' class='btn btn-primary btn-large calendar-button'>SFL Calendar</a>
	   		</div>
	   	</div>
	</div>
	<? elseif($permissions > 2): ?>
	<div class='container'>
		<div class='row'>
			<div class='col-lg-4 col-md-4 text-center'>
	   		<a href='<? echo base_url() . "index.php/user/userCalendar" ?>' class='btn btn-primary btn-large calendar-button'>User Calendar</a>
	   		</div>
	   		<div class='col-lg-4 col-md-4 text-center'>
	   		<a href='<? echo base_url() . "index.php/sfl" ?>' class='btn btn-primary btn-large calendar-button'>SFL Calendar</a>
	   		</div>
	   		<div class='col-lg-4 col-md-4 text-center'>
	   		<a href='<? echo base_url() . "index.php/manager" ?>' class='btn btn-primary btn-large calendar-button'>Manager Calendar</a>
	   		</div>	
	   	</div>
	</div>
	<? endif; ?>
	</div>