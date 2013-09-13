bootbox.animate(false);
var monthInfo = {
   "minHours": null,
   "maxHours": null,
   "notes": null
};

$('#calendar').fullCalendar(
{
   header :
   {
      left : 'prev,next today',
      center : 'title',
      right : "month,agendaWeek,agendaDay"
   },
   editable : true,
   droppable : true,
   draggable : true,
   minTime : 8,
   maxTime : 21,
   dropAccept : '.external-event, .fc-event',
   loading : function(bool)
   {
      if (bool)
         $('#loading').show();
      else
         $('#loading').fadeOut();
   },
   selectable : true,
   selectHelper : true,
   snapMinutes : 15,
   timeFormat :
   {
      '' : 'h:mm{ - h:mm}'
   },
   eventResize : function(event, dayDelta, minuteDelta, revertFunc, jsEvent, ui, view)
   {
      if(dayDelta > 0)
         return 0;
      updateEvent("Custom", event.start, false, event.start.toTimeString().split(" ")[0], event.end.toTimeString().split(" ")[0]);
   },
   select : function(start, end, allDay, jsEvent, view)
   {
      if(!isLockedOut(start))
      {
         if(allDay === true || view.name == 'month')
            setDate(start);
         else
         {
            updateEvent("Custom", start, false, start.toTimeString().split(" ")[0], end.toTimeString().split(" ")[0]);
            $("#calendar").fullCalendar('unselect');
         }
      }
      else
      {
         $("#calendar").fullCalendar('unselect');
         selectedDate = null;
         bootbox.alert("It is too late to update your availability on this day. Please contact Tim Martin at tmartin@gazellesports.com");
      }
   },
   eventRender : function(event, element, view)
   {
      var position;
      switch(event.start.getDay())
      {
         case 6 : position = "left"; break;
         case 0 : position = "right"; break;
         default: position = "top"; break;
      }
      element.tooltip({
         animation: false,
         title: event.tip,
         container: 'body',
         placement: position      
      });
      if (event.category == 'scheduled')
      {
         event.editable = false;
      }
   },
   viewRender : function(view)
   {
      if(view.name == "agendaWeek")
      {
         $("#copyWeek").removeClass("disabled");
         $("#pasteWeek").removeClass("disabled");
      }
      else
      {
         $("#copyWeek").addClass("disabled", "disabled");
         $("#pasteWeek").addClass("disabled", "disabled");
      }
      var _month = view.start.getFullYear() + "-" + (view.start.getMonth() + 1);
      $.ajax(
      {
         type : "POST",
         url : url + "index.php/user/populateMonthInfoForm",
         data :
         {
            employeeId : employeeId,
            month : _month
         },
         success : function(msg)
         {
            var json = JSON.parse(msg);
            monthInfo.minHours = (typeof json["minHours"] !== "undefined") ? json["minHours"] : "";
            monthInfo.maxHours = (typeof json["maxHours"] !== "undefined") ? json["maxHours"] : "";
            monthInfo.notes = (typeof json["notes"] !== "undefined") ? json["notes"] : "";
         },
         error : function()
         {
            alert("ERROR!!!");
         }
      });
   },
   drop : function(date, allDay)
   {
      var draggedEvent = $(this).data('eventObject');
      if(!isLockedOut(date))
      {
         if (draggedEvent.title == 'Custom')
            customEvent(date, false);
         else
            updateEvent(draggedEvent.title, date, true, '', '');
      }
      else
      {
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
      console.log(event);
      var start, end, states;
      if (event.category == 'scheduled-cover' || event.category == 'scheduled-pickup' && coverRequest === false || event.category == 'emptyShifts')
      {
         start = event.start.toTimeString().split(" ")[0];
         end = event.end.toTimeString().split(" ")[0];
         var form = initializeForm(start, end);

         bootbox.dialog(event.description, [
         {
            "label" : "Partial Shift",
            "class" : "btn-primary",
            "callback" : function()
            {
               partialShiftPickupDialog(event, employeeId, form);
            }
         },
         {
            "label" : "Entire Shift",
            "class" : "btn-primary",
            "callback" : function()
            {
               pickUpShift(null, null, employeeId, event.employeeId, event.id);
            }
         },   
         {
            "label" : "Cancel",
            "class" : "btn-danger"
         }  
         ]);
      }
      if (coverRequest === true && event.category == 'scheduled')
      {
         coverRequest = false;

         cancelCoverRequest();

         bootbox.dialog("Are you sure you would like to request a cover for this shift? (Note: the shift will remain on your schedule, and you will still be responsible for it until someone claims it). ", [
            {
               "label": "Full Shift",
               "class": "btn-primary",
               "callback" : function() {
                  fullShiftCoverRequest(event);
               }
            },
            {
               "label": "Partial Shift",
               "class": "btn-primary",
               "callback": function() {
                  partialShiftCoverRequest(event);
               }
            },
            {
               "label": "Cancel",
               "class": "btn-danger"
            }
         ]);
      }
   },
   eventSources :
   [
      {
         url : url + "index.php/user/scheduledEventSource",
         error : function(msg, textStatus, errorThrown)
         {
            alert(errorThrown);
         }
      },
      {
         url : url + "index.php/user/availabilityEventSource",
         error : function(msg, textStatus, errorThrown)
         {
            alert(errorThrown);
         }
      }
   ]
});
