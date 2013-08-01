$(document).keypress(function(e)
{
   // gets the ascii code of the key pressed
   if ($(e.target).is('input') || $(e.target).is('select') || $(e.target).is('textarea'))
   {
      return;
   }
   var code = (e.keyCode ? e.keyCode : e.which);
   if (selectedDate != null)
   {
      if (code == 97 || code == 65)
      {
         updateEvent('Available', selectedDate, true, '', '');
         incrementDate();
      }
      else
      if (code == 98 || code == 66)
      {
         updateEvent('Busy', selectedDate, true, '', '');
         incrementDate();
      }
      else
      if (code == 99 || code == 67)
         customEvent(selectedDate, true);
      else
      if (code == 37)
      {
         var newDate = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate() - 1, 0, 0, 0);
         setDate(newDate);
         $("#calendar").fullCalendar('select', selectedDate, selectedDate, true);
         e.preventDefault();
      }
      else
      if (code == 39)
      {
         var newDate = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate() + 1, 0, 0, 0);
         setDate(newDate);
         $("#calendar").fullCalendar('select', selectedDate, selectedDate, true);
         e.preventDefault();
      }
      else
      if (code == 38)
      {
         var newDate = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate() - 7, 0, 0, 0);
         setDate(newDate);
         $("#calendar").fullCalendar('select', selectedDate, selectedDate, true);
         e.preventDefault();
      }
      else
      if (code == 40)
      {
         var newDate = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate() + 7, 0, 0, 0);
         setDate(newDate);
         $("#calendar").fullCalendar('select', selectedDate, selectedDate, true);
         e.preventDefault();
      }
   }
}); 