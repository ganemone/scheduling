<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head></head>
<body>
<div id='errors'>
</div>
</body>
<script type='text/javascript'>

	$(document).ready(function() {
		var employeeId = <?php echo $employeeId ?>;
		var selectedDate;
		$("#dialog").dialog({
			autoOpen: false,
			show: 'slide',
			hide: 'slide'
		});
		
		$('#external-events div.external-event').each(function() {
			var eventObject = {
				title: $.trim($(this).text()), 
			};
			
			$(this).data('eventObject', eventObject);
			
			$(this).draggable({
				zIndex: 999,
				revert: true,    
				revertDuration: 0  
			});
			
		});
		function setDate(start) {
			selectedDate = new Date(start); }
			
		function incrementDate() {
			selectedDate = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate() + 1, 0,0,0);
			$('#calendar').fullCalendar('select', selectedDate, selectedDate, 'true');}
			//$('#calendar').fullCalendar('gotoDate', selectedDate); }
		$(document).keypress(function(e)
		{
			// gets the ascii code of the key pressed
			code = (e.keyCode ? e.keyCode : e.which);
			if(selectedDate)
			{
				if(code == 97 || code == 65) {
					updateEvent('Available', selectedDate, true, '', '');
					incrementDate(); }
				else if(code == 98 || code == 66) {
					updateEvent('Busy', selectedDate, true, '', '');
					incrementDate(); }
				else if(code == 99 || code == 67)
					customEvent(selectedDate, true); 
			}
        });
		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title'
			},
			editable: false,
			droppable: true, 
			loading: function (bool) 
			{ 
 				if (bool) 
     				$('#loading').show(); 
  				else 
    				$('#loading').fadeOut(); 
			},
			selectable: true,
			selectHelper: true,
			timeFormat: {
				'':'h:mm{ - h:mm}',
			},
			select: function(start)
			{
				var split = <?php echo $maxMonth ?>.split("-");
				var d = new Date(split[0], split[1]-1, split[2]);
				if(start >= d)
					setDate(start);	
				else {
					alert("You can't alter this data anymore");
					$("#calendar").fullCalendar('unselect') 
				}
			},
			viewDisplay: function(view) {
				var now = new Date();
				var end = new Date();
				end.setMonth(now.getMonth() + 2);
				
				var cal_date_string = view.start.getMonth()+'/'+view.start.getFullYear();
      			var cur_date_string = now.getMonth()+'/'+now.getFullYear();
      			var end_date_string = end.getMonth()+'/'+end.getFullYear();
      			
      			var _month = view.start.getFullYear() + "-" + (view.start.getMonth() + 1);
   
      			$.ajax({
      				type:"POST",
      				url:"<?php echo $url ?>index.php/user/populateMonthInfoForm",
      				data: {
      					employeeId: employeeId,
      					month: _month
      				},
      				success: function(msg) {
      					var json = JSON.parse(msg);
      					
      					$("#minHours").val(json["minHours"]);
      					$("#maxHours").val(json["maxHours"]);
      					$("#notes").val(json["notes"]);
      				}
      			});
				// Makes only three months at a time viewable
   			  	if(cal_date_string == cur_date_string) 
   			   		$('.fc-button-prev').addClass("fc-state-disabled"); 
      	  		else 
      				$('.fc-button-prev').removeClass("fc-state-disabled"); 
      				
     			if(end_date_string == cal_date_string) 
     				$('.fc-button-next').addClass("fc-state-disabled"); 
    			else
    				$('.fc-button-next').removeClass("fc-state-disabled"); 	
			},
			drop: function(date, allDay) 
			{
				var draggedEvent = $(this).data('eventObject');

				if(draggedEvent.title == 'Custom')
					customEvent(date, false);
				else 
					updateEvent(draggedEvent.title, date, true, '', '');
	   	},
		// Sets some stuff for the dragged item
		eventDragStart: function( event, jsEvent, ui, view ) { 
			dragged = [ ui.helper[0], event ];
		},
		eventSources: [
		{
			url: "<?php echo $url ?>index.php/user/eventSources",
			error: function() {
				alert("An error ocurred"); }
		}]});
		$("#submitMonthForm").button();
		$("#submitMonthForm").click(function() {
			var date = $('#calendar').fullCalendar("getDate");
			var id = employeeId;
			var min = $("#minHours").val();
			var max = $("#maxHours").val();
			var notes = $("#notes").val();
			$.ajax({
				type:"POST",
				url:"<?php echo $url ?>index.php/user/updateMonthInfo",
				data: {
					employeeId: id,
					date: date,
					min: min,
					max: max,
					notes: notes
				},
				success:function(msg) {
					$("#dialog").dialog('open');
					$("#monthInfo").slideUp();
					$("#showMonthInfoForm").slideDown();
					
				}
			});
		});
		$("#showMonthInfoForm").button();
		$("#showDraggable").button();
		$("#quickEnter").button();
		$("#cancelMonthForm").button();
		
		$("#showMonthInfoForm").click(function() {
			$(this).hide();
			$('#showDraggable').hide();
			$('#quickEnter').hide();
			$("#monthInfo").slideDown();	
		});
		
		$("#cancelMonthForm").click(function() {
			$("#monthInfo").slideUp();
			setTimeout(function() {
				$("#showMonthInfoForm").show();
				$("#showDraggable").show();
				$("#quickEnter").show();
			}, 500);
		});
		$("#showDraggable").click(function() {
			$("#external-events").slideToggle();
		});
		$("#quickEnter").click(function() {
			var split = <?php echo $maxMonth ?>.split("-");
			var d = new Date(split[0], split[1]-1, split[2]);
			$('#calendar').fullCalendar('gotoDate', d);
			$('#calendar').fullCalendar("select", d);
		});
		
		function customEvent(date, increment)
		{
			var today = new Date();
			if(date <= today) {
				alert("You cannot alter this data anymore");
			}
			else { 
			$('#customTimes').dialog(
			{
				modal:true,
				autoOpen: true,
				show: "blind",
				hide: "explode",
        		buttons: 
		        {
	        		// when submitted do this
    				"Submit":function() 
    				{
    					$(this).dialog('close');  
	    				if(increment == true)
	    					incrementDate();

						// gets the values inputted for the begin and end of the event
    					var start =  $("#start").val().split(':');
						var end = $("#end").val().split(':');
				
						// puts the times on the right days, and creates date objects for them
						var startDate = new Date(date.getFullYear(),date.getMonth(),date.getDate(),start[0],start[1],start[2]);
						var endDate = new Date(date.getFullYear(),date.getMonth(),date.getDate(),end[0],end[1],end[2]);
						
						// Creates times for storing in the database.
						var startTime = start[0] + ":" + start[1] + ":" + start[2];
						var endTime = end[0] + ":" + end[1] + ":" + end[2];
						
						var month = startDate.getMonth() + 1;
						var _date = startDate.getDate();
						var year = startDate.getFullYear();
						var formattedDate = year + '-' + month + '-' + _date;
						if(endDate <= startDate)
							alert("The end of your availability must come after the start");
						else
							updateEvent("Custom", date, false, startTime, endTime);
					}
				}
    		});
    	}}
		function updateEvent(title, date, allDay, start, end)
		{
			var today = new Date();
			if(date < today) {
				alert("You cannot alter this data anymore");
			}
			else {
				var month = date.getMonth() + 1;
				var _date = date.getDate();
				var year = date.getFullYear();
				var formattedDate = year + '-' + month + '-' + _date;
				$.ajax({
					type: "POST",
					url: "<?php echo $url ?>index.php/user/updateHourAction",
					data: { employeeId: <?php echo $employeeId ?>, 
							day: formattedDate, 
							available:title,
							begin:start,
							end:end
					},
					error: function() {
						alert("An error occured");
						$("#calendar").fullCalendar('refetchEvents');
					},
					success:function(errors) {
						document.getElementById("errors").innerHTML += errors;
						$("#calendar").fullCalendar('refetchEvents');
					},
});}}});

</script>
<style type='text/css'>

	body {
		margin-top: 0px;
		margin-left: 5px;
		margin-right: 5px;
		text-align: center;
		font-size: 14px;
		font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
		}
		
	#wrap {
		width: 1100px;
		margin: 0 auto;
		}
		
	#external-events {
		float: left;
		position:absolute; top:20em;
		width: 150px;
		padding: 0 10px;
		border: 1px solid #ccc;
		background: #eee;
		text-align: left;
		display:none;
		}
	#external-events h4 {
		font-size: 16px;
		margin-top: 0;
		padding-top: 1em;
		}
		
	.external-event { /* try to mimick the look of a real event */
		margin: 10px 0;
		padding: 2px 4px;
		color: #fff;
		font-size: .85em;
		cursor: pointer;
		}
	#busy {
		background:Black;
	}
	#available {
		background:#32CD32;
	}
	#custom {
		background:#3366CC;
	}
		
	#external-events p {
		margin: 1.5em 0;
		font-size: 11px;
		color: #666;
		}
		
	#external-events p input {
		margin: 0;
		vertical-align: middle;
		}

	#calendar {
		float: right;
		width: 900px;
		}
	#customTimes {
		display:none;
	}
	#buttons {
		position:absolute; top:4.5em; left:0px;
		text-align:left;
	}
	#monthInfo {
		float:left;
		position:absolute;top:5em;
		width:8em;
		display:none;
	}
	#monthTable {
		font-size: 8pt;
	}
	#minHours, #maxHours {
		width:7em;
	}
	#notes {
		width: 12em;
		height:9em;
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
	#buttons li {
		display:block;
		list-style-type:none;
		margin:2px;
	}
	#buttons li button {
		width:12em;
	}

</style>
</head>
<body>
<div id='dialog' title='Success'>
	You have successfully updated your information! :)
</div>
<div id='customTimes' title='Custom Shift'>
	Start: <select id='start'>
		   <option value='09:45:00'>9:45am</option>
		   <option value='10:00:00'>10:00am</option>
		   <option value='10:15:00'>10:15am</option>
		   <option value='10:30:00'>10:30am</option>
		   <option value='10:45:00'>10:45am</option>
		   <option value='11:00:00'>11:00am</option>
		   <option value='11:15:00'>11:15am</option>
		   <option value='11:30:00'>11:30am</option>
		   <option value='11:45:00'>11:45am</option>
		   <option value='12:00:00'>12:00pm</option>
		   <option value='12:15:00'>12:15pm</option>
		   <option value='12:30:00'>12:30pm</option>
		   <option value='12:45:00'>12:45pm</option>
		   <option value='13:00:00'>1:00pm</option>
		   <option value='13:15:00'>1:15pm</option>
		   <option value='13:30:00'>1:30pm</option>
		   <option value='13:45:00'>1:45pm</option>
		   <option value='14:00:00'>2:00pm</option>
		   <option value='14:15:00'>2:15pm</option>
		   <option value='14:30:00'>2:30pm</option>
		   <option value='14:45:00'>2:45pm</option> 
		   <option value='15:00:00'>3:00pm</option>
		   <option value='15:15:00'>3:15pm</option>
		   <option value='15:30:00'>3:30pm</option>
		   <option value='15:45:00'>3:45pm</option>
		   <option value='16:00:00'>4:00pm</option>
		   <option value='16:15:00'>4:15pm</option>
		   <option value='16:30:00'>4:30pm</option>
		   <option value='16:45:00'>4:45pm</option>
		   <option value='17:00:00'>5:00pm</option>
	</select><br>
	End: <select id='end'>
		   	<option value='12:00:00'>12:00pm</option>
		   	<option value='12:15:00'>12:15pm</option>
		   	<option value='12:30:00'>12:30pm</option>
		   	<option value='12:45:00'>12:45pm</option>
		   	<option value='13:00:00'>1:00pm</option>
		   	<option value='13:15:00'>1:15pm</option>
		   	<option value='13:30:00'>1:30pm</option>
		   	<option value='13:45:00'>1:45pm</option>
		   	<option value='14:00:00'>2:00pm</option>
		   	<option value='14:15:00'>2:15pm</option>
		   	<option value='14:30:00'>2:30pm</option>
			<option value='14:45:00'>2:45pm</option> 
		   	<option value='15:00:00'>3:00pm</option>
		   	<option value='15:15:00'>3:15pm</option>
		   	<option value='15:30:00'>3:30pm</option>
		   	<option value='15:45:00'>3:45pm</option>
		  	<option value='16:00:00'>4:00pm</option>
		 	<option value='16:15:00'>4:15pm</option>
		 	<option value='16:30:00'>4:30pm</option>
		  	<option value='16:45:00'>4:45pm</option>
		   	<option value='17:00:00'>5:00pm</option>
			<option value='17:15:00'>5:15pm</option>
			<option value='17:30:00'>5:30pm</option>
			<option value='17:45:00'>5:45pm</option>
			<option value='18:00:00'>6:00pm</option>
		</select>
</div>
<div id='wrap'>

<div id='external-events'>
<h4>Draggable Events</h4>
<div class='external-event fc-view' id='available'>Available</div>
<div class='external-event fc-view' id='busy'>Busy</div>
<div class='external-event fc-view' id='custom'>Custom</div>
</div>
<div id='calendar'></div>

<div style='clear:both'></div>
</div>

<div id='buttons'>
<ul>
<li><button id='showMonthInfoForm'>Update Information</button></li>
<li><button id='showDraggable'>Drag Events</button></li>
<li><button id='quickEnter'>Quick Enter</button></li>
</ul>
</div>

<div id='monthInfo'>
	<table id='monthTable'>
	<tr><td>Min Hours</td><td><input type='text' name='min' id='minHours'></input></td></tr>
	<tr><td>Max Hours</td><td><input type='text' name='max' id='maxHours'></input></td></tr>
	<tr><td>Notes: </td><td><textarea name='notes' id='notes'>Enter Notes for this month here</textarea></td></tr>
	<td><button id='submitMonthForm'>SUBMIT</button></td>
	<td><button id='cancelMonthForm'>CANCEL</button></td>
	</table>
</div>
<img src='<?php echo $url ?>images/ajax-loader.gif' id='loading' class='loading'/>
</body>
</html>
