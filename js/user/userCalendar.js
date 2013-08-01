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
         $.prompt("It is too late to update your availability on this day. Please contact Tim Martin at tmartin@gazellesports.com",
         {
            title : "Oh No!",
            buttons :
            {
               Ok : 1
            }
         });
      }
   },
   eventRender : function(event, element, view)
   {
      element.qtip(
      {
         content : event.tip,
         position :
         {
            at : "top center",
            my : "bottom center"
         },
         style :
         {
            tip : "bottomMiddle",
            classes : "qtip-dark"
         }
      });
      if (event.category == 'scheduled')
      {
         event.editable = false;
      }
   },
   viewDisplay : function(view)
   {
      if(view.name == "agendaWeek")
      {
         $("#copyWeek").removeClass("disabled");
         $("#pasteWeek").removeClass("disabled");
      }
      else
      {
         $("#copyWeek").addClass("disabled");
         $("#pasteWeek").addClass("disabled");
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

            monthInfo.minHours = json["minHours"];
            monthInfo.maxHours = json["maxHours"];
            monthInfo.notes = json["notes"];
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
         $.prompt("It is too late to update your availability on this day. Please contact Tim Martin at tmartin@gazellesports.com",
         {
            title : "Oh No!",
            buttons :
            {
               Ok : 1
            }
         });
      }
   },
   eventDrop : function(event, dayDelta, minuteDelta, allDay, revertFunc, jsEvent, ui, view)
   {
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
         $("#calendar").fullCalendar("renderEvent", draggedEvent);
      }
      else if(event.category == 'availability')
      {
         $.prompt("It is too late to update your availability on this day. Please contact Tim Martin at tmartin@gazellesports.com",
         {
            title : "Oh No!",
            buttons :
            {
               Ok : 1
            }
         });
      }
      revertFunc();
   },
   eventClick : function(event, jsEvent, view)
   {
      var start, end, states;
      if (event.category == 'scheduled-cover' || event.category == 'scheduled-pickup' && coverRequest === false || event.category == 'emptyShifts')
      {
         start = event.start.toTimeString().split(" ")[0];
         end = event.end.toTimeString().split(" ")[0];
         var form = initializeForm(start, end);

         bootbox.dialog(event.description, [
         {
            "label" : "Cancel",
            "class" : "btn-danger"
         },
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
         }]);
         /*
         states = [
         {
            title : "Pick Up Shift",
            html : event.description,
            buttons :
            {
               'Entire Shift' : 1,
               'Partial Shift' : 2,
               Cancel : 0
            },
            submit : function(e, v, m, f)
            {
               if (v == 1)
               {
                  if(event.category == 'emptyShifts')
                     pickUpEmptyShift(event);
                  else
                     pickUpShift(null, null, employeeId, event.employeeId, event.id);
                  return true;
               }
               else if (v == 2)
               {
                  $.prompt.nextState();
                  return false;
               }
               return true;
            }
         },
         {
            title : "Custom Shift",
            html : document.getElementById('customTimes').innerHTML,
            buttons :
            {
               Submit : 1,
               Cancel : 0
            },
            submit : function(e, v, m, f)
            {
               if (v == 1)
               {
                  if(event.category == "emptyShifts")
                     pickUpEmptyShift(event, f.start, f.end);
                  else
                     pickUpShift(f.start, f.end, employeeId, event.employeeId, event.id);
               }
               return true;
            }
         }];
         $.prompt(states);
         */
      }
      if (coverRequest === true && event.category == 'scheduled')
      {
         coverRequest = false;
         start = event.start;
         end = event.end;
         eId = employeeId;

         var _start = event.start.toTimeString().split(" ")[0];
         var _end = event.end.toTimeString().split(" ")[0];
         initializeForm(_start, _end);

         $("#shiftCoverRequest").dialog('close');
         states =
         {
            State0 :
            {
               title : "Confirmation",
               html : "Are you sure you would like to request a cover for this shift? (Note: the shift will remain on your schedule, and you will still be responsible for it until someone claims it).",
               buttons :
               {
                  "Full Shift" : 1,
                  "Partial Shift" : 2,
                  "Cancel" : 0
               },
               submit : function(e, v, m, f)
               {
                  if (v == 1)
                  {
                     $.ajax(
                     {
                        type : "POST",
                        url : url + "index.php/user/scheduleRequest",
                        data :
                        {
                           shiftId : event.id
                        },
                        success : function(msg)
                        {
                           if (msg === 0)
                              $.prompt.goToState('successState0');
                           else
                           {
                              $("#calendar").fullCalendar('refetchEvents');
                              $.prompt.goToState('successState1');
                           }
                        },
                        error : function(msg, textStatus, errorThrown)
                        {
                           alert(errorThrown);
                        }
                     });
                     return false;
                  }
                  else if (v == 2)
                  {
                     $.prompt.goToState('customState');
                     return false;
                  }
                  return true;
               }
            },
            successState0 :
            {
               title : "Oops!",
               html : "This shift is already out for cover!",
               buttons :
               {
                  Ok : 0
               },
               submit : function(e, v, m, f)
               {
                  return true;
               }
            },
            successState1 :
            {
               title : "Success",
               html : "You have successfully put this shift up for cover",
               buttons :
               {
                  OK : 0
               },
               submit : function(e, v, m, f)
               {
                  return true;
               }
            },
            customState :
            {
               title : "Custom Shift",
               html : document.getElementById("customTimes").innerHTML,
               buttons :
               {
                  Submit : 1,
                  Cancel : 0
               },
               submit : function(e, v, m, f)
               {
                  if (v == 1)
                  {
                     requestPartialShiftCover(f.start, f.end, employeeId, event.id);
                  }
               }
            }
         };
         $.prompt(states);
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
   }
   ]
});
