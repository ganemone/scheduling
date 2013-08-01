var tutorialSubmit = function(e, v, m, f)
{
   if (v === -1)
   {
      $.prompt.prevState();
      $(".jqibox").css("height", $(document).height());
      $(".jqifade").css("height", $(document).height());
      return false;
   }
   else
   if (v === 1)
   {
      $.prompt.nextState();
      $(".jqibox").css("height", $(document).height());
      $(".jqifade").css("height", $(document).height());
      return false;
   }
}
var tutorialStates = [
{
   title : "Welcome",
   html : "Would you like to take a tutorial of the scheduling software?",
   buttons :
   {
      Sure : 1
   },
   focus : 1,
   position :
   {
      container : '#container',
      x : 0,
      y : 0,
      width : 300
   },
   submit : tutorialSubmit
},
{
   title : "Menu",
   html : "Here is your menu bar.  It contains useful buttons like \"Logout\".",
   buttons :
   {
      Prev : -1,
      Next : 1
   },
   position :
   {
      container : "#menu",
      x : -300,
      y : 0,
      width : 300,
      arrow : 'rt'
   },
   submit : tutorialSubmit
},
{
   title : "Toolbar",
   html : "This list of buttons determines how to enter availability.  Click \"Update Info\" to update hours per week and specific notes for the given month.",
   buttons :
   {
      Prev : -1,
      Next : 1
   },
   position :
   {
      container : "#buttons2",
      x : 230,
      y : 5,
      width : 300,
      arrow : 'lt'
   },
   submit : function(e, v, m, f)
   {
      if (v === -1)
      {
         $.prompt.prevState();
         $(".jqibox").css("height", $(document).height());
         $(".jqifade").css("height", $(document).height());
         return false;
      }
      else if (v === 1)
      {
         $.prompt.nextState();
         $("#showDraggable").trigger("click");
         $("#external-events").css("backgroundColor", "white");
         $("#external-events").effect("highlight", {color: "#7FFF00"}, 500);
         $("#external-events").effect("highlight", {color: "#7FFF00"}, 500);
         $("#external-events").effect("highlight", {color: "#7FFF00"}, 5000);

         $(".jqibox").css("height", $(document).height());
         $(".jqifade").css("height", $(document).height());
         return false;
      }
   }
},
{
   title : "Drag and Drop",
   html : "Clicking drag events will allow you to drag an available option to any day still able to be edited from the box shown below labeled \"Draggable Events\".",
   buttons :
   {
      Prev : -1,
      Next : 1
   },
   position :
   {
      container : "#buttons",
      x : 158,
      y : 30,
      width : 300,
      arrow : 'lt'
   },
   submit : function(e, v, m, f)
   {
      $("#showDraggable").trigger("click");
      if (v === -1)
      {
         $.prompt.prevState();
         $(".jqibox").css("height", $(document).height());
         $(".jqifade").css("height", $(document).height());
         return false;
      }
      else if (v === 1)
      {
         $.prompt.nextState();
         $(".jqibox").css("height", $(document).height());
         $(".jqifade").css("height", $(document).height());
         return false;
      }
   }
},
{
   title : "Color Code",
   html : "This shows a color code of all the different types of events. If you are unsure of the purpose of a certain event, simply hover the mouse over the event for a description.",
   buttons :
   {
      Prev : -1,
      Next : 1
   },
   position :
   {
      container : "#buttons",
      x : 158,
      y : 50,
      width : 300,
      arrow : 'lt'
   },
   submit : function(e,v,m,f)
   {
      if(v == 1)
         $("#calendar").fullCalendar("changeView", "agendaWeek");
      return tutorialSubmit(e,v,m,f);
   }
},
{
   title : "Copy Week",
   html : "This feature is only available when in the week view.  Once a week is copied, it can be pasted into any other week. Think of this functionality to be similar to copying text in a document.",
   buttons :
   {
      Prev : -1,
      Next : 1
   },
   position :
   {
      container : "#buttons",
      x : 158,
      y : 75,
      width : 300,
      arrow : 'lt'
   },
   submit : function(e,v,m,f)
   {
      $("#calendar").fullCalendar("changeView", "month");
      return tutorialSubmit(e,v,m,f);
   }
},
{
   title : "Shift Cover",
   html : "This button allows for putting shifts up for cover.  When a shift is up for cover, other employees will see it on their calendars, and will be able to pick up the shift.",
   buttons :
   {
      Prev : -1,
      Next : 1
   },
   position :
   {
      container : "#buttons",
      x : 158,
      y : 124,
      width : 300,
      arrow : 'lt'
   },
   submit : function(e,v,m,f)
   {
      if(v == 1)
         $("#newsfeed").show();
      return tutorialSubmit(e,v,m,f);
   }
},
{
   title : "Newsfeed",
   html : "This button will show a newsfeed. The newsfeed can be used for any form of staff to staff communication. Managers can post notes here about special circumstance, floor staff can post about shift covers, etc.",
   buttons :
   {
      Prev : -1,
      Next : 1
   },
   position :
   {
      container : "#buttons",
      x : 158,
      y : 148,
      width : 300,
      arrow : 'lt'
   },
   submit : function(e, v, m, f)
   {
      $("#newsfeed").hide();
      if (v === 1)
         $("#showEvents").trigger("click");
      return tutorialSubmit(e,v,m,f);
   }
},
{
   title : "Store Events",
   html: "This button will toggle the visibility of store events, shown as purple events on the calendar.",
   buttons :
   {
      Prev : -1,
      Next : 1
   },
   position :
   {
      container : "#buttons2",
      x : 230,
      y : 5,
      width : 300,
      arrow : 'lt'
   },
   submit : function(e, v, m, f)
   {
      $("#showEvents").trigger("click");
      events = true;
      if (v === -1)
      {
         $.prompt.prevState();
         $(".jqibox").css("height", $(document).height());
         $(".jqifade").css("height", $(document).height());
         return false;
      }
      else if (v === 1)
      {
         $("#showEvents").trigger("click");
         events = false;
         $.prompt.nextState();
         $(".jqibox").css("height", $(document).height());
         $(".jqifade").css("height", $(document).height());
         return false;
      }
   }
},
{
   title : "All Staff ",
   html : "This button allows you to view the entire staff's schedules.",
   buttons :
   {
      Prev : -1,
      Next : 1
   },
   position :
   {
      container : "#buttons2",
      x : 230,
      y : 30,
      width : 300,
      arrow : 'lt'
   },
   submit : function(e, v, m, f)
   {
      if (v === -1)
      {
         $("#showEvents").trigger("click");
         $.prompt.prevState();
         $(".jqibox").css("height", $(document).height());
         $(".jqifade").css("height", $(document).height());
         return false;
      }
      else if (v === 1)
      {
         $.prompt.nextState();
         $(".jqibox").css("height", $(document).height());
         $(".jqifade").css("height", $(document).height());
         return false;
      }
   }
},
{
   title : "Toggle Availability",
   html : "This button allows you to control whether your availability events are shown on the calendar. It would be useful to remove your availability when printing your schedule.",
   buttons :
   {
      Prev : -1,
      Next : 1
   },
   position :
   {
      container : "#buttons2",
      x : 230,
      y : 50,
      width : 300,
      arrow : 'lt'
   },
   submit : tutorialSubmit
},
{
   title : "Download Calendar",
   html : "The calendar can be downloaded in .ics format.  To add to ICal or Outlook, simply download the file to your computer and open it with the respective calendar application.  You can also import it into google calendar.",
   buttons :
   {
      Prev : -1,
      Next : 1
   },
   position :
   {
      container : "#buttons2",
      x : 230,
      y : 75,
      width : 300,
      arrow : 'lt'
   },
   submit : tutorialSubmit
},
{
   title : "Printable Schedule",
   html : "This button will bring up a printable version of the calendar. It will contain the events that you currently have on the page. If you would only like to see your schedule, be sure to remove your availability by hitting the \"Toggle Availability\" button.",
   buttons :
   {
      Prev : -1,
      Next : 1
   },
   position :
   {
      container : "#buttons2",
      x : 230,
      y : 100,
      width : 300,
      arrow : 'lt'
   },
   submit : tutorialSubmit
},
{
   title : "The End!",
   html : "You have completed the tutorial. If you have any questions, feel free to email Giancarlo Anemone at ganemone@gmail.com.",
   buttons :
   {
      Done: 0
   },
   position :
   {
      container : '#container',
      x : 0,
      y : 0,
      width : 300
   },
   submit : tutorialSubmit
}
]; 