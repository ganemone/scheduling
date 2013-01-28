<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head></head>
<body>
<div id='errors'>
</div>
<script type='text/javascript'>
	$(document).ready(function() {
		// Messing with event resize, try and fix latter.
		$(window).resize(function()
		{
			//$("#calendar").css("height",$(document).height()-30);
			//var width = $(document).width() - $("#options").width() - 30;
			//$("#calendar").css("width", width);
			//calendar.fullCalendar('render');
		});
		var url = <?php echo $url ?>;
		$("#deleteOption").attr('checked', false);
		$("#toggleAll").attr('checked', false);
		//$("#calendar").css("height",$(document).height() - 30);
		$("#calendar").css("width", $(document).width() - $("#options").width() - 50);
		var offsetLeft = $(document).width() - $("#options").width() - 350;
		$("#headerButtons").offset({ top: 10, left: offsetLeft });
		var busy = "true";
		var scheduled = "true";
		var available = "true";
		var employees = "false";
		var deleteOption = "false";
		var names = <?php echo json_encode($names) ?>;
		
		var htmlForm = "";
		
		 for(var j=0; j<names.length; j++)
		 {
		 	var input = names[j].split(":");
		 	var split = input[0].replace(" ", ":");	
		 	htmlForm += '<label>' + input[0] + '<input type="checkbox" name=' + split + ' value=' + input[1] + '></label><br>';
		 }
				 
		htmlForm += '<br>';
		var calendar = $('#calendar').fullCalendar({
			header: {
				left: 'prev,next',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			height: $(document).height() - 30,
			selectable: true,
			//selectHelper: true,
			slotMinutes: 15,
			minTime: 9,
			maxTime: 21,
			editable: true,
			resizeable: true,
			loading: function (bool) 
			{ 
 				if (bool) 
     				$('#loading').show(); 
  				else 
    				$('#loading').fadeOut(); 
			},
			eventClick: function(calEvent, jsEvent, view) 
			{
				if(calEvent.title == 'Test Schedule')
				{
					document.getElementById('desired').innerHTML = "Desired Hours: 20-30(Sample)";
					document.getElementById('current').innerHTML = "Scheduled Hours: 15.25(Sample)";
					document.getElementById('notes').innerHTML = "Notes: I would like to have at least one monday off this month (Sample)";
					$("#employeeInfo").dialog();
				}
				else if(calEvent.title.indexOf(":") == -1)
				{
					calendar.fullCalendar("changeView","agendaWeek");
				}
				else
				{
					if(deleteOption == 'true')
					{
						$("#deleteConfirmation").dialog({
						autoOpen: false,
						show: "fold",
						hide: "fold",
						modal: true,
						buttons: { 
						Delete: function() 
						{
							$.ajax({
							type:"POST",
							url: <?php echo $url ?> + "index.php/manager/deleteEvent",
							data: 
							{
								employeeId: calEvent.title.split(":")[1],
								day: calEvent.start
							},
							success: function(msg) 
							{
								calendar.fullCalendar("removeEvents", function(e)
								{
									if(e.start == calEvent.start && e.title == calEvent.title)
									{
										return true;
									}
									return false;
								});
							}
							});
							$(this).dialog("close");
						},
						Cancel: function()
						{
							$(this).dialog('close');
						}
					}
					});
					$("#deleteConfirmation").dialog('open');
					}			
					else 
					{ 
						$.ajax({
							type:"POST",
							url: <?php echo $url ?> + "index.php/manager/getEmployeeWeekHours",
							data: {
								employeeId: calEvent.title.split(":")[1],
								dayNum: calEvent.start.getDay(),
								date: calEvent.start
							},
							success: function(msg) {
								var hourInfo = JSON.parse(msg);
								document.getElementById('desired').innerHTML = "Desired Hours: " + hourInfo['desired'];
								document.getElementById('current').innerHTML = "Scheduled Hours: " + hourInfo['scheduled'];
								document.getElementById('notes').innerHTML = "Notes: " + hourInfo['notes'];
								$("#employeeInfo").dialog();
					
							}
						});
					}
				}
			},
			eventDrop: function(event,dayDelta,minuteDelta,allDay,revertFunc)
			{
				eventMove(event,dayDelta,minuteDelta,revertFunc);
			},
			eventResize: function( event, dayDelta, minuteDelta, revertFunc, jsEvent, ui, view ) 
			{ 
				eventMove(event,dayDelta,minuteDelta,revertFunc);	
			},
			select: function(start, end, allDay) {
				var view = calendar.fullCalendar('getView').name;	
				var startHour = start.getHours();
				var endHour = end.getHours();
				var startMin = start.toTimeString().split(" ")[0].split(":")[1];
				var endMin = end.toTimeString().split(" ")[0].split(":")[1];

				if(startHour > 12)
				{
					startHour-=12;
					startTime = startHour + ":" + startMin + "pm"; 
				}
				else
				{
					startTime = startHour + ":" + startMin + "am";
				}
				if(endHour > 12)
				{
					endHour-=12;
					endTime = endHour + ":" + endMin + "pm";
				}
				else 
				{
					endTime = endHour + ":" + endMin + "am";
				}
				if(view == 'month')
				{
					calendar.fullCalendar('changeView', 'agendaWeek');
					calendar.fullCalendar('gotoDate', start);
				}
				else { 
				var state = {
				state0:	{
				title: "Shift: " + (start.getMonth() + 1)  + "/" + start.getDate() + "/" + start.getFullYear() + 
				"<br>From: " + startTime + " Until: " + endTime,
				html: htmlForm,
				submit:function(e,v,m,f) 
				{ 
					var title = "";
					for (var key in f) 
					{
 					//title += key.replace(":", " ");
 					//title += ":";
 					var id = f[key];
 					day = start.getFullYear() + "-" + (start.getMonth()+1) + "-" + start.getDate();
 					// Ajax request to update the database
 					$.ajax({
							type: "POST",
							data: { employeeId: id,
							day: day,	
							begin: start.toTimeString().split(" ")[0],
							end: end.toTimeString().split(" ")[0] 
							},
							url: <?php echo $url ?> + "index.php/manager/scheduleEmployee",
							success: function(msg) {
								calendar.fullCalendar("refetchEvents");
							}
						});	
					}	
					
				}
			}}
			$.prompt(state);
			};
			
			calendar.fullCalendar('unselect');
			},
			eventSources: [
				
				{
					url: <?php echo $url ?> + "index.php/manager/eventSource",
					data: { busy: busy },
					error: function()
					{
						alert("An error ocurred");
					},
					success: function()
					{
						//alert("Success");
					}
				},
				{
					url: <?php echo $url ?> + "index.php/manager/scheduledEventSource",
					error: function()
					{
						alert("An error ocurred");
					},
					success: function()
					{
						//alert("success");
					}
				}
			]
		});
		
	// Function that handles an event begin moved or resized
	function eventMove(event,dayDelta,minuteDelta,revertFunc)
	{
		var title = event.title;
		if(title == "Test Schedule")
		{
			return true;
		}
		else if(title.indexOf(":") == -1)
		{
			revertFunc();
		}
		else {
			var id = title.split(":")[1];
			var day = new Date(event.start.getFullYear(), event.start.getMonth(), event.start.getDate() - dayDelta);
			
			var _day = event.start;	
			var start = event.start.toTimeString();
			var end = event.end.toTimeString();
			
			
			$.ajax({
				type:"POST",
				url: <?php echo $url ?> + "index.php/manager/deleteEvent",
				data: { 
					employeeId: id,
					day: day
				},
				success: function() {
					$.ajax({
						type:"POST",
						url: <?php echo $url ?> + "index.php/manager/scheduleEmployee",
						data: {
							employeeId: id,
							day: _day,
							begin: start.split(" ")[0],
							end: end.split(" ")[0]
						},
						success: function(msg) {
							
						}
						
					})
				}
			});
		}
	}
	$("#busyOption").click(function() {
		$.ajax({
			type: "GET",
			url: <?php echo $url ?> + "index.php/manager/toggleOption",
			data: { option: "busy" },
			success: function(msg) {
				calendar.fullCalendar("refetchEvents");
			}
		});
	});
	$("#scheduledOption").click(function() {
		if(scheduled == "true")
		{
			scheduled = "false";
			calendar.fullCalendar("removeEventSource",<?php echo $url ?> + "index.php/manager/scheduledEventSource");
		}
		else {
			scheduled = "true";
			calendar.fullCalendar("addEventSource", <?php echo $url ?> + "index.php/manager/scheduledEventSource");
		}
	});
	
	$("#availableOption").click(function() {
		if(available == "true")
		{
			available = "false";
			calendar.fullCalendar("removeEventSource",<?php echo $url ?> + "index.php/manager/eventSource");
		}
		else {
			available = "true";
			calendar.fullCalendar("addEventSource",<?php echo $url ?> + "index.php/manager/eventSource");
		}
	});
	$("#toggleAll").click(function() 
	{
		var disp='';
		if(employees == 'false')
		{
			disp = '1';
			employees = 'true';
			$("#employees").children("button").each(function() {
				document.getElementById($(this).attr("id")).style.color = 'Green';
			});
		}
		else 
		{
			disp = '0';
			employees = 'false';
			$("#employees").children("button").each(function() {
				document.getElementById($(this).attr("id")).style.color = 'Black';
			});
		}
		$.ajax({
			type:"POST",
			data: {
				display: disp
			},
			url: <?php echo $url ?> + "index.php/manager/toggleAll",
			success: function(msg)
			{
				$("#calendar").fullCalendar('refetchEvents');
				//alert(msg);
			},
			error: function(msg, textStatus, errorThrown)
			{
				alert(errorThrown);
			}
		});
	});
	$("#deleteOption").click(function() {
		if(deleteOption == 'false')
		{
			deleteOption = 'true';
		}
		else {
			deleteOption = 'false';
		}
	});
	
});
		function toggleEvents(id)
		{
			if(document.getElementById(id).style.color == 'Black')
			{
				document.getElementById(id).style.color = 'Green';
			}
			else
			{
				document.getElementById(id).style.color = 'Black';
			}
			
			$.ajax({
				type:"POST",
				data: { employeeId: id },
				url: <?php echo $url ?> + "index.php/manager/toggleDisplay",
				success: function(msg)
				{
					$("#calendar").fullCalendar('refetchEvents');
				},
				error: function(msg)
				{
					alert(msg);
				}
			});
		}
</script>
<style type='text/css'>

	body {
		margin-top: 5px;
		text-align: center;
		font-size: 14px;
		font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
		background-color:snow;
		opacity: 0.8;
		z-index: -2;
		}

	#calendar {
		position:absolute; left:13.5em; top:5em;
		width: 70em;
		margin: 0 auto;
		white-space:nowrap;
		z-index:-1;
		}
	#employees {
		float:left;
		position:absolute;top:8em;
		width:10em;
		text-align:left;
		background:lightGrey;
		height:20em;
		overflow:auto;
		overflow-x:hidden;
		
	}
	.employeeName {
		width:8em;
	}
	#options {
		float:left;
		position:absolute;top:29em;
		width: 13em;
		text-align:left;
	}
	.employeeInfo {
		display:none;
		text-align:left;
	}
	.loading {
		display:none;
	    position: fixed;
	    top: 50%;
	    left: 50%;
	    margin-left: -50px; 
	    margin-top: -50px; 
	    text-align:center;
	    z-index:9999;
        overflow: auto;
	}
	.table {
		font:inherit;
		font-size:inherit;
		margin:0px;
	}
	li {
		display:inline;
		list-style-type:none;
	}
	#menu {
		position:fixed;right:0;top:0;
		z-index:1;

	}
	#top {
		position:absolute;top:0;left:0;
		position: fixed;
		width:100%;
		height:4.5em;
		background-color:lightgrey;
		text-align:left;
		z-index: 0;
	}
	#h2 {

		background-color: Black;
		z-index:1;
	}
	#middleContainer {
		position:fixed;
		top:25%;
		left:50%;
	}
	#headerButtons {
	}
	#title { 
	margin-left:20px;
	}

</style>
<div id='calendar'></div>
<div id='employees'>
	<?php 
	for($j=0; $j<count($names); ++$j)
	{
		$split = explode(":", $names[$j]);
		$name = $split[0];
		$id = $split[1];
		echo <<<END
		<button class='employeeName' id='$id' onclick="toggleEvents('$id');">$name</button>
		<br>
		<script>$("#$id").button();
		document.getElementById($id).style.color = 'Black';
		</script>
END;
	}
	

	?>
</div>
<div id='deleteConfirmation' title='Confirmation'>
	Would You like to delete this event?
</div>
<div id='options'>
	Toggle Display Options:
	<table class='table'>
	<tr><td><label>All Employees:</td><td> <input type='checkbox' name='toggleAll' id='toggleAll'></input></label></td></tr>
	<tr><td><label>Busy Events:</td>    <td> <input type="checkbox" name='busyOption' id='busyOption' checked></input></label></td></tr>
	<tr><td><label>Scheduled Events:</td><td><input type="checkbox" name="scheduledOption" id="scheduledOption" checked></input></label></td></tr>
	<tr><td><label>Available Events:</td><td><input type="checkbox" name="availableOption" id="availableOption" checked></input></label></td></tr>
	<tr><td><label>Click to Delete:</td><td><input type="checkbox" name="deleteOption" id="deleteOption"></input></label></td></tr>
	</table>
</div>
<div id='employeeInfo' class='employeeInfo' title='Employee Info'>
	<div id='desired'></div>
	<div id='current'></div>
	<div id='notes'></div>
</div>
<div id='middleContainer'></div>
<div id='headerButtons'></div>
<img src="http://unknown60c54789b344/~giancarloanemone/ScheduleRepo/images/ajax-loader.gif" id='loading' class='loading'/>
<span id='top'><div id='title'><h2><?php echo $company ?> Scheduling Page</h2></div></span>
</body>
</html>