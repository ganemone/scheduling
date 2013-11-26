var monthInfo = {
   "minHours": null,
   "maxHours": null,
   "notes": null
};
var scheduledEventSource = {
   url : url + "index.php/user/scheduledEventSource",
   beforeSend: function()
   {
      global_ajax_requests++;
      showLoading();
   },
   error : function(msg, textStatus, errorThrown)
   {
      alert(errorThrown);
   },
   complete: function()
   {
      global_ajax_requests--;
      hideLoading();
   }
};
var availabilityEventSource = {
   url : url + "index.php/user/availabilityEventSource",
   beforeSend: function()
   {
      global_ajax_requests++;
      showLoading();
   },
   error : function(msg, textStatus, errorThrown)
   {
      alert(errorThrown);
   },
   complete: function()
   {  
      global_ajax_requests--;
      hideLoading();
   }
};
var coEventSource = {
   url : url + "index.php/user/coEventSource",
   beforeSend: function()
   {
      global_ajax_requests++;
      showLoading();
   },
   error : function(msg, textStatus, errorThrown)
   {
      alert(errorThrown);
   },
   complete: function()
   {  
      global_ajax_requests--;
      hideLoading();
   }
}
var allStaffEventSource = {
   url : url + "index.php/user/allStaffSource",
   beforeSend: function()
   {
      global_ajax_requests++;
      showLoading();
   },
   error : function(msg, textStatus, errorThrown)
   {
      alert(errorThrown);
   },
   complete: function()
   {  
      global_ajax_requests--;
      hideLoading();
   }
}
var header = {};
var title_format = {};
if(mobile === true)
{
   header = {
      left : 'prev,next',
      center : 'title',
      right : "basicWeek,basicDay"
   };
   title_format = {
      week: "MMM yyyy",
      day: "MMM yyyy"
   };
} 
else {
   header = {
      left : 'prev,next today',
      center : 'title',
      right : "month,agendaWeek,agendaDay"
   };
   title_format = {
      month: 'MMMM yyyy',                             
      week: "MMM d[ yyyy]{ '&#8212;'[ MMM] d yyyy}", 
      day: 'dddd, MMM d, yyyy'                  
   }
}
$('#calendar').fullCalendar(
{
   header : header,
   defaultView : (mobile == true) ? "basicWeek" : "month",
   editable : true,
   droppable : true,
   draggable : true,
   minTime : 8,
   maxTime : 21,
   dropAccept : '.external-event, .fc-event',
   selectable : true,
   selectHelper : true,
   snapMinutes : 15,
   timeFormat :
   {
      '' : 'h:mm{ - h:mm}'
   },
   titleFormat : title_format,
   eventResize : function(event, dayDelta, minuteDelta, revertFunc, jsEvent, ui, direction, view)
   {
      if(dayDelta > 0)
         return 0;
      
      if(isLockedOut(event.start)) {
         bootbox.alert("It is too late to update your availability on this day. Please contact Tim Martin at tmartin@gazellesports.com");
         return revertFunc();
      }
      if(direction == "north") {
         event.end.setMinutes(event.end.getMinutes() - minuteDelta);
         event.start.setMinutes(event.start.getMinutes() - minuteDelta);
      }
      
      updateEvent("Custom", event.start, false, event.start.toTimeString().split(" ")[0], event.end.toTimeString().split(" ")[0]);
   },
   select : function(start, end, allDay, jsEvent, view)
   {
      if(isLockedOut(start)) {
         $("#calendar").fullCalendar('unselect');
         selectedDate = null;
         bootbox.alert("It is too late to update your availability on this day. Please contact Tim Martin at tmartin@gazellesports.com");
      }
      else {
         if(mobile) {
            cal_selectMobile(start, end, allDay, jsEvent, view);
         }
         else {
            cal_select(start, end, allDay, jsEvent, view);
         }
      }
   },
   eventRender : function(event, element, view)
   {
      if(mobile) {
         return true;
      }
      else {
         cal_eventRender(event, element, view);
      }
   },
   viewRender : function(view)
   {
      cal_viewRender(view);
   },
   drop : function(date, allDay)
   {
      var draggedEvent = $(this).data('eventObject');
      if(!isLockedOut(date))
      {
         if (draggedEvent.category == "availability") {
            if(draggedEvent.allDay == false) {
               customEvent(date, false);   
            }
            else {
               updateEvent(draggedEvent.title, date, true, '', '');
            }
         }
      }
      else {
         bootbox.alert("It is too late to update your availability on this day. Please contact Tim Martin at tmartin@gazellesports.com");
      }
   },
   eventDragStart : function() {
      $(".fc-event").tooltip("hide");
      $(".fc-event").tooltip("disable");
   },
   eventDrop : function(event, dayDelta, minuteDelta, allDay, revertFunc, jsEvent, ui, view)
   {
      $(".fc-event").tooltip("enable");
      var draggedEvent, start, _start, end, _end;
      if(!isLockedOut(event.start) && event.category == 'availability')
      {
         draggedEvent;
         start = event.start;
         _start = new Date(start.getFullYear(), start.getMonth(), start.getDate(), start.getHours(), start.getMinutes(), 0);
         if (!event.allDay)
         {
            end = event.end;
            _end = new Date(end.getFullYear(), end.getMonth(), end.getDate(), end.getHours(), end.getMinutes(), 0);
            draggedEvent =
            {
               title : event.title,
               start : _start,
               end : _end,
               color : event.color,
               allDay : false
            };
            updateEvent("Custom", _start, false, _start.toTimeString().split(" ")[0], _end.toTimeString().split(" ")[0]);
         }
         else if(event.category)
         {
            start = event.start;
            draggedEvent =
            {
               title : event.title,
               start : new Date(start.getFullYear(), start.getMonth(), start.getDate(), start.getHours(), start.getMinutes(), 0),
               color : event.color,
               allDay : true
            };
            updateEvent(draggedEvent.title, start, true, '', '');
         }
         //$("#calendar").fullCalendar("renderEvent", draggedEvent);
      }
      else if(event.category == 'availability')
      {
         bootbox.alert("It is too late to update your availability on this day. Please contact Tim Martin at tmartin@gazellesports.com");
      }
      revertFunc();
   },
   eventClick : function(event, jsEvent, view)
   {
      cal_eventClick(event, jsEvent, view);
   },
   eventSources : [availabilityEventSource, scheduledEventSource]
});
