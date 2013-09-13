var tutorialSubmit = function(e, v, m, f) {
   if (v === -1) {
      $.prompt.prevState();
      $(".jqibox").css("height", $(document).height());
      $(".jqifade").css("height", $(document).height());
      return false;
   }
   else if (v === 1) {
      $.prompt.nextState();
      $(".jqibox").css("height", $(document).height());
      $(".jqifade").css("height", $(document).height());
      return false;
   }
}
/* The tutorialStates object is used to create a dynamic tutorial of the page. The tutorial will
 * run through the given states, displaying the html to the user.
 */
var tutorialStates = [{
   title : "Welcome", html : "Would you like to take a tutorial of the scheduling software?", buttons : {
      Sure : 1
   }, focus : 1, position : {
      container : '#container', x : 0, y : 0, width : 300
   }, submit : function(e, v, m, f) {
      if (v === -1) {
         $.prompt.prevState();
         return false;
      }
      else if (v === 1) {
         $("#top").removeClass('top');
         $("#top").addClass("temp");
         $("#menu").removeClass('menu');
         $("#menu").addClass('menuTemp');
         $.prompt.nextState();
         return false;
      }
   }
}, {
   title : "Menu Bar", html : "Here is your menu bar. There is alot of important stuff here! Lets take a closer look...", buttons : {
      Prev : -1, Next : 1
   }, focus : 1, position : {
      container : '#menu', x : -300, y : 10, width : 300, arrow : 'rt'
   }, submit : tutorialSubmit
}, {
   title : "Templates", html : "This button allows you to make scheduling templates.  When in the week view, you can make a template of a given employees schedule.", buttons : {
      Prev : -1, Next : 1
   }, focus : 1, position : {
      container : '#menu', x : 10, y : 60, width : 300, arrow : 'tc'
   }, submit : tutorialSubmit
}, {
   title : "Templates", html : "Once a template is created, it will show up in this section.  You can name your templates anything you like.  Clicking on the template will show you the details of the template. You can now drag it onto to calendar to schedule employees based on the template.", buttons : {
      Prev : -1, Next : 1
   }, focus : 1, position : {
      container : '#external-events', x : 190, y : 0, width : 300, arrow : 'lt'
   }, submit : tutorialSubmit
}, {
   title : "Tutorial", html : "Click the tutorial button if you ever need to see this tutorial again.", buttons : {
      Prev : -1, Next : 1
   }, focus : 1, position : {
      container : '#menu', x : 120, y : 60, width : 300, arrow : 'tc'
   }, submit : tutorialSubmit
}, {
   title : "Add Event", html : "This button allows you to add store related events to the Calendar. You can either add a single event, or a weekly repeating event. Employees can then be scheduled to work the events.", buttons : {
      Prev : -1, Next : 1
   }, focus : 1, position : {
      container : '#menu', x : 215, y : 60, width : 300, arrow : 'tc'
   }, submit : tutorialSubmit
}, {
   title : "Finalize Schedule", html : "This button allows you to push the schedule out to all employees. Clicking finalize schedule will make the schedule viewable for employees for all days until the last day in your current view.", buttons : {
      Prev : -1, Next : 1
   }, focus : 1, position : {
      container : '#menu', x : 200, y : 60, width : 300, arrow : 'tr'
   }, submit : tutorialSubmit
}, {
   title : "Employees", html : "This box contains a list of your employees. Clicking on their name will toggle the visibility of their availability and scheduled days", buttons : {
      Prev : -1, Next : 1
   }, focus : 1, position : {
      container : '#employees', x : 150, y : 0, width : 300, arrow : 'lt'
   }, submit : tutorialSubmit
}, {
   title : "Options", html : "This area shows the options that can be changed at any point to display various information. Click to delete allows for deleting of scheduled events by clicking.", buttons : {
      Prev : -1, Next : 1
   }, focus : 1, position : {
      container : '#options', x : 170, y : 0, width : 300, arrow : 'lt'
   }, submit : tutorialSubmit
}, {
   title : "Time Slot", html : "This drop down list will update the time slots shown in the week and day views", buttons : {
      Prev : -1, Next : 1
   }, focus : 1, position : {
      container : '#selectTimeSlot', x : 170, y : -10, width : 300, arrow : 'lt'
   }, submit : tutorialSubmit
}, {
   title : "Availability", html : "Availability is color coded. Black indicates that the employee is busy and cannot work that day. Green indicates the employee is free all day, and Blue indicates a custom availability.", position : {
      container : '#container', x : 0, y : 0, width : 300
   }, buttons : {
      Prev : -1, Next : 1
   }, focus : 1, submit : tutorialSubmit
}, {
   title : "Scheduling", html : "When in the month view, clicking on a day will switch to view that day. You can also manually switch between views using these buttons.", buttons : {
      Prev : -1, Next : 1
   }, focus : 1, position : {
      container : '#headerButtons', x : -10, y : -10, width : 300, arrow : 'rm'
   }, submit : function(e, v, m, f) {
      if (v === -1) {
         $.prompt.prevState();
         return false;
      }
      else if (v === 1) {
         $("#calendar").fullCalendar('changeView', 'agendaDay');
         $("#calendar").fullCalendar("addEventSource", url + "index.php/manager/tutorialEvents");
         $("#calendar").fullCalendar('gotoDate', new Date('2012', '0', '1'));
         $.prompt.nextState();
         return false;
      }

   }
}, {
   title : "Scheduling Continued", html : "When in the day view, employees can be scheduled by simply dragging from the desired start time to the end time. A pop up window will allow for the selection of one or multiple employees for the selected shift. You can also schedule employees by holding the shift key and clicking on an availability event.", buttons : {
      Prev : -1, Next : 1
   }, focus : 1, position : {
      container : '#headerButtons', x : 150, y : 100, width : 300
   }, submit : tutorialSubmit
}, {
   title : "Shift Editing", html : "Scheduled events can be shifted and resized by clicking and dragging", buttons : {
      Prev : -1, Next : 1
   }, focus : 1, position : {
      container : '#headerButtons', x : 150, y : 200, width : 300
   }, submit : tutorialSubmit
}, {
   title : "Employee Information", html : "Clicking on an employees scheduled shift while holding down the shift key will show a window with information about their weekly desired hours, current week scheduled hours, and special notes.", buttons : {
      Prev : -1, Next : 1
   }, focus : 1, position : {
      container : '#headerButtons', x : 150, y : 200, width : 300
   }, submit : tutorialSubmit
}, {
   title : "Statistical Information", html : "This button provides a popup containing information about sales goals, labor costs, total hours, and more.  It also provides a summary of all scheduled employees and their current scheduled hours vs. their desired hours.", buttons : {
      Prev : -1, Next : 1
   }, focus : 1, position : {
      container : '#employees', x : 165, y : -45, width : 300, arrow : 'lt'
   }, submit : tutorialSubmit
}, {
   title : "The End!", html : "Now you are ready to use the Scheduling Software! If you have any questions, feel free to contact Giancarlo Anemone at ganemone@gmail.com", buttons : {
      Done : 2
   }, focus : 1, position : {
      container : '#container', x : 0, y : 0, width : 300
   }, submit : function(e, v, m, f) {
      $("#calendar").fullCalendar('removeEventSource', url + "index.php/manager/tutorialEvents");
      $("#calendar").fullCalendar('gotoDate', new Date());
      $("#calendar").fullCalendar('changeView', "month");
      setTutorial(false);
      $("#menu").removeClass('temp');
      $("#top").removeClass('temp');
      $("#menu").addClass("menu");
      $("#top").addClass("top");
   }
}];
/* When the tutorial button is clicked, various changes to the css are made in order to provide a
 * better experience for the user.  Also the variable tutorial is set to true. This disables some
 * interaction with the rest of the site while the tutorial is running.
 */
$("#tutorial").click(function() {
   setTutorial(true);
   $.prompt(tutorialStates, {
      close : function(e, v, m, f) {
         tutorial = false;
         $("#menu").removeClass('menuTemp');
         $("#top").removeClass('temp');
         $("#menu").addClass("menu");
         $("#top").addClass("top");
      }
   });
});
