$(document).keypress(function(e) {
   if ($(e.target).is('input') || $(e.target).is('select')) {
      return;
   }
   $(".fc-event").tooltip("hide");
   
   var code = (e.keyCode ? e.keyCode : e.which);
   if (code == 70 || code == 102)
      $("#sfl_employees").trigger('click');
   else if (code == 103 || code == 71)
      $("#viewInfo").trigger('click');
   else if (code == 101 || code == 69)
      $("#all_employees").trigger('click');
   else if (code == 68)
      $("#deleteOption").trigger('click');
   else if (code == 115 || code == 83)
      $("#all_scheduled").trigger('click');
   else if (code == 98 || code == 66)
      $("#all_busy").trigger('click');
   else if (code == 97 || code == 65)
      $("#all_available").trigger('click');
   else if (code == 37) {
      $("#calendar").fullCalendar('prev');
      e.preventDefault();
   }
   else if (code == 39) {
      $("#calendar").fullCalendar('next');
      e.preventDefault();
   }
   else if (code == 100)
      $("#calendar").fullCalendar("changeView", "agendaDay");
   else if (code == 119 || code == 68)
      $("#calendar").fullCalendar("changeView", "basicWeek");
   else if (code == 109 || code == 77)
      $("#calendar").fullCalendar("changeView", "month");
});
