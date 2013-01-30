<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head></head>
<body>
<div id='errors'>
</div>
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
	#selectTimeSlot {
		position:absolute; left:0.5em; top:6em;
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
	#deleteConfirmation, #finalizeConfirmation {
		display:none;
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
	.menu {
		position:fixed;right:0;top:0;
		z-index:1;
	}
	.menuTemp {
		position:absolute; right:0; top:0;
		z-index:1;
	}
	.temp {
		position:absolute; top:0; left:0;
		/*z-index: -1;*/
		width:100%;
		height:4.5em;
		background-color:lightgrey;
		text-align:left;
		z-index: 0;
		
	}
	.top {
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
<div id='finalizeConfirmation' title='Confirmation'>
	Are you sure you would like to finalize the schedule for 
</div>
<div id='selectTimeSlot'>Time Slot:<select id='selectTime'>
<option value='60'>Hour</option>
<option value='30'>Half Hour</option>
<option value='15'>15 Minute</option>
</select></div>
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

<img src="/~giancarloanemone/images/ajax-loader.gif" id='loading' class='loading'/>
<span id='top' class='top'><div id='title'><h2><?php echo $company ?> Scheduling Page</h2></div></span>
</body>
</html>
<script type='text/javascript'>
	var tutorial = false;
	$(document).ready(function() {
	
		
		var url = <?php echo $url ?>;
		var busy = "true";
		var scheduled = "true";
		var available = "true";
		var employees = "false";
		var deleteOption = "false";
		var names = <?php echo json_encode($names) ?>;
		
		$("#deleteOption").attr('checked', false);
		$("#toggleAll").attr('checked', false);
		
		$("#calendar").css("width", $(document).width() - $("#options").width() - 50);
		var offsetLeft = $(document).width() - $("#options").width() - 350;
		$("#headerButtons").offset({ top: 10, left: offsetLeft });
		
		var htmlForm = "";
		
		 for(var j=0; j<names.length; j++)
		 {
		 	var input = names[j].split(":");
		 	var split = input[0].replace(" ", ":");	
		 	htmlForm += '<label>' + input[0] + '<input type="checkbox" name=' + split + ' value=' + input[1] + '></label><br>';
		 }
				 
		htmlForm += '<br>';
		
		$("#selectTime").change(function() {
			var view = $("#calendar").fullCalendar('getView').name;
			renderCalendar(parseInt($(this).val()),view);
		});
		function renderCalendar(timeSlot, view) 
		{
		$("#calendar").fullCalendar('destroy');
		var calendar = $('#calendar').fullCalendar({
			header: {
				left: 'prev,next',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			selectable: true,
			//selectHelper: true,
			slotMinutes: timeSlot,
			minTime: 8,
			maxTime: 21,
			editable: true,
			resizeable: true,
			defaultView: view,
			viewDisplay: function(view)
			{
				if(view.name == 'month')
				{
					h = NaN;
				}
				else
				{
					h = 3000;
				}
				$('#calendar').fullCalendar('option', 'contentHeight',h);
			},
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
				if(tutorial == true)
				{
					return true;
				}
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
					error: function(msg, textStatus, errorThrown)
					{
						alert(errorThrown);
					}
				},
				{
					url: <?php echo $url ?> + "index.php/manager/scheduledEventSource",
					error: function(msg, textStatus, errorThrown)
					{
						alert(errorThrown);
					},
				}
			]
		});
		}
		renderCalendar(30, 'month');
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
		function setTutorial(bool)
		{
			tutorial = bool;
		}
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
		$("#finalize").button();
		$("#home").button();
		$("#about").button();
		$("#tutorial").button();
		$("#logOut").button();
		
		$("#finalize").click(function() {
			var date = $("#calendar").fullCalendar('getDate');
			var startDate = new Date(date.getFullYear(), date.getMonth(), 1);
			var endDate = new Date(date.getFullYear(), date.getMonth() + 1, 0);
			document.getElementById("finalizeConfirmation").innerHTML = "Are you sure you would like to finalize the schedule from " + startDate.toDateString() + " until " + endDate.toDateString();
			$("#finalizeConfirmation").dialog({
				autoOpen:false,
				show:'fold',
				hide:'fold',
				buttons: {
					Yes: function()
					{
						$(this).dialog('close');
						$.ajax({
						type: "POST",
						url: <?php echo $url ?> + "index.php/manager/finalize",
						data: {
						start: startDate,
						end: endDate
						},
						success: function(msg) {
							$("#calendar").fullCalendar("refetchEvents");
						},
						error: function(msg, textStatus, errorThrown) {
							alert(errorThrown);
						}
					});
					},
					Cancel: function()
					{
						$(this).dialog('close');
					}
				}
			});
			$("#finalizeConfirmation").dialog('open');
		});
		$("#logOut").click(function() {
		window.location = <?php echo $url ?> + 'index.php/manager/logOut';
		});
		$("#about").click(function() {
			window.location = <?php echo $url ?> + "index.php/site/about";
		});
		$("#home").click(function () {
			window.location = <?php echo $url ?> + "index.php/manager";
		});
		
		var tutorialSubmit = function(e,v,m,f) {
			if(v === -1)
			{
				$.prompt.prevState();
				return false;
			}
			else if(v === 1)
			{
				$.prompt.nextState();
				return false;
			}
		}
		var tutorialStates = [
		{
			title: "Welcome",
			html: "Would you like to take a tutorial of the scheduling software?",
			buttons: { Sure: 1 },
			focus: 1,
			position: { container: '#container', x:0, y:0, width:300 },
			submit: function(e,v,m,f)
			{
				if(v === -1)
				{
					$.prompt.prevState();
					return false;
				}
				else if(v === 1)
				{
					$("#top").removeClass('top');
					$("#top").addClass("temp");
					$("#menu").removeClass('menu');
					$("#menu").addClass('menuTemp');
					$.prompt.nextState();
					return false;
				}
			}
		},
		{
			title:"Time Slot",
			html: "This drop down list will update the time slots shown in the week and day views",
			buttons: { Prev: -1, Next: 1},
			focus: 1,
			position: { container: '#selectTimeSlot', x:170, y:-10, width:300, arrow: 'lt'},
			submit: tutorialSubmit
		},
		{
			title: "Employees",
			html: "This box contains a list of your employees. Clicking on their name will toggle the visibility of their availability and scheduled days",
			buttons: { Prev: -1, Next: 1 },
			focus: 1,
			position: { container: '#employees', x:150, y:0, width:300, arrow: 'lt' },
			submit: tutorialSubmit
		},
		{
			title: "Options",
			html: "This area shows the options that can be changed at any point to display various information. Click to delete allows for deleting of scheduled events by clicking.",
			buttons: { Prev: -1, Next: 1},
			focus: 1,
			position: { container: '#options', x:170, y:0, width:300, arrow: 'lt' },
			submit: tutorialSubmit
		},
		{
			title: "Availability",
			html: "Availability is color coded. Black indicates that the employee is busy and cannot work that day. Green indicates the employee is free all day, and Blue indicates a custom availability.",
			position: { container: '#container', x:0, y:0, width:300 },
			buttons: { Prev: -1, Next: 1},
			focus: 1,
			submit: tutorialSubmit
		},
		{
			title: "Menu",
			html: "Here is your main menu! Pay attention to the Finalize Schedule button. Clicking this button will finalize the schedule for the current month, and make it visible to employees.",
			position: { container: '#container', x:-170, y:10, width:300, arrow: 'rt' },
			buttons: { Prev: -1, Next: 1},
			focus: 1,
			submit: tutorialSubmit
		},
		{
			title: "Scheduling",
			html: "When in the month view, clicking on a day will switch to view that week. You can also manually switch between views using these buttons.",
			buttons: { Prev: -1, Next: 1},
			focus: 1,
			position: { container: '#headerButtons', x:-10, y:-10, width:300, arrow: 'rm'},
			submit: function(e,v,m,f) {
				if(v === -1)
				{
					$.prompt.prevState();
					return false;
				}
				else if(v === 1)
				{
					$("#calendar").fullCalendar('changeView', 'agendaWeek');
					$("#calendar").fullCalendar("addEventSource", <?php echo $url ?> + "index.php/manager/tutorialEvents");
					$("#calendar").fullCalendar('gotoDate', new Date('2012','0','1'));
					$.prompt.nextState();
					return false;
				}
				
			}
		},
		{
			title: "Scheduling Continued",
			html: "When in the week or day view, employees can be scheduled by simply dragging from the desired start time to the end time. A pop up window will allow for the selection of one or multiple employees for the selected shift.",
			buttons: { Prev: -1, Next: 1},
			focus: 1,
			position: { container: '#headerButtons', x:150, y:100, width:300},
			submit: tutorialSubmit
		},
		{
			title: "Shift Editing",
			html: "Scheduled events can be shifted and resized by clicking and dragging (Try it!)",
			buttons: { Prev: -1, Next: 1 },
			focus: 1,
			position: { container: '#headerButtons', x:150, y:200, width:300},
			submit: tutorialSubmit
		},
		{
			title: "Employee Information",
			html: "Clicking on an employees scheduled shift will show a window with information about their weekly desired hours, current week scheduled hours, and special notes (Try it!)",
			buttons: { Prev: -1, Next: 1 },
			focus: 1,
			position: { container: '#headerButtons', x:150, y:200, width:300},
			submit: tutorialSubmit
		},
		{
			title: "The End!",
			html: "Now you are ready to use the Scheduling Software! If you have any questions, feel free to contact Giancarlo Anemone at ganemone@gmail.com",
			buttons: { Done: 2},
			focus: 1,
			position: { container: '#container', x:0, y:0, width:300 },
			submit: function(e,v,m,f)
			{
				$("#calendar").fullCalendar('removeEventSource', <?php echo $url ?> + "index.php/manager/tutorialEvents");
				setTutorial(false);
				$("#menu").removeClass('temp');
				$("#top").removeClass('temp');
				$("#menu").addClass("menu");
				$("#top").addClass("top");
			}
		}
		];
		$("#tutorial").click(function() {
			setTutorial(true);
			$.prompt(tutorialStates, {
				close: function(e,v,m,f)
				{
					tutorial = false;
					$("#menu").removeClass('temp');
					$("#top").removeClass('temp');
					$("#menu").addClass("menu");
					$("#top").addClass("top");
				}
			});
		});
</script>

