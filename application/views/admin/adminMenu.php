<html>
	<div id='menu'>
		<ul id='list'>
			<li><button id='home'>Home</button></li>
			<li><button id='about'>About</button></li>
			<li><button id='tutorial'>Tutorial</button></li>
			<li><button id='logOut'>Log Out</button></li>
		</ul>
	</div>
	<div id='container'>
	</div>
	<style type='text/css'>
		#container {
			position:absolute; top:0; left:40%;
		}
	</style>

<script type='text/javascript'>
	$(document).ready(function(){
		$("#home").button();
		$("#about").button();
		$("#tutorial").button();
		$("#logOut").button();
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
			html: "This area shows the options that can be changed at any point to display various information. ",
			buttons: { Prev: -1, Next: 1},
			focus: 1,
			position: { container: '#options', x:160, y:0, width:300, arrow: 'lt' },
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
			title: "Scheduling",
			html: "When in the month view, clicking on a day will switch to view that week. You can also manually switch between views using these buttons.",
			buttons: { Prev: -1, Next: 1},
			focus: 1,
			position: { container: '#headerButtons', x:-10, y:-10, width:300, arrow: 'rm'},
			submit: function(e,v,m,f) {
				$("#calendar").fullCalendar('changeView', 'agendaWeek');
				$("#calendar").fullCalendar("addEventSource", <?php echo $url ?> + "index.php/manager/tutorialEvents");
				
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
		},
		{
			title: "Scheduling Continued",
			html: "When in the week or day view, employees can be scheduled by simply dragging from the desired start time to the end time. A pop up window will allow for the selection of one or multiple employees for the selected shift.",
			buttons: { Prev: -1, Next: 1},
			focus: 1,
			position: { container: '#headerButtons', x:150, y:200, width:300},
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
			}
		}
		
		];
		$("#tutorial").click(function() {
			$.prompt(tutorialStates);
		});
});
</script>
</html>