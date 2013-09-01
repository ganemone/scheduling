bootbox.animate(false);
var availabilityEventSource = {
   url : url + "index.php/manager/eventSource",
   data : function() {
      return {
      employee_obj : getEmployeeObj()
      }
   },
   error : function(msg, textStatus, errorThrown)
   {
      alert(textStatus + "/manager/eventSource");
   }
}
var scheduledEventSource = {
   url : url + "index.php/manager/scheduledEventSource",
   data : function() {
      return {
      employee_obj : getEmployeeObj()
      }
   },
   error : function(msg, textStatus, errorThrown)
   {
      alert(textStatus + "/manager/scheduledEventSource");
   }
}
var coEventSource = {
   url : url + "index.php/manager/coEventSource",
   error : function(msg, textStatus, errorThrown)
   {
      alert(textStatus + "coEventSource");
   }
}
function renderCalendar(slotMinutes, view, date)
{
   $("#calendar").fullCalendar('destroy');
   $("#calendar").css("width", $(document).width() - 290);
   var options =
   {
      header :
      {
         left : 'prev,next',
         center : 'title',
         right : 'month,basicWeek,agendaDay'
      },
      allowCalEventOverlap : false,
      selectable : true,
      slotMinutes : slotMinutes,
      minTime : 6,
      maxTime : 21,
      editable : true,
      resizeable : true,
      droppable : true,
      defaultView : 'month',
      snapMinutes : 15,
      timeFormat : 'h:mm{ - h:mm}',
      dropAccept : '.external-event, .fc-event',
      /* Function to be executed when the view changes. Updates the contentHeight for scrolling in the week and day
       *  views. Takes the view changed to as the input.
       *
       */
      viewRender : function(view)
      {
         initializeGoalTips(view);
         if (view.name == 'month')
         {
            h = NaN;
         }
         else
         {
            h = 6000;
         }
         $("span.fc-header-title h2").hide();
         if (view.name == 'agendaDay')
         {
         }
         $('#calendar').fullCalendar('option', 'contentHeight', h);
         
      },
      /* Function to display the loading gif.
       *
       */
      drop : function(date, allDay)
      {
         //$(".fc-event").qtip('hide');
         var draggedEvent = $(this).data('eventObject');
         var days = draggedEvent.days;
         var starts = draggedEvent.starts;
         var ends = draggedEvent.ends;
         var categories = draggedEvent.categories;
         var startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay());
         var endDate = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate() + 6);
         
         bootbox.confirm(makeTemplateForm(startDate, endDate, 'checkbox'), function(result)
         {
            if(result)
            {
               var employee_id_arr = new Array();
               var day_arr = new Array();
               $("#templateForm > div > label > input").each(function()
               {
                  if($(this).is(":checked"))
                  {
                     employee_id_arr.push($(this).val());
                  }         

               });
               for (var i = 0; i < days.length; i++)
               {
                  var day = new Date();
                  day.setFullYear(startDate.getFullYear());
                  day.setMonth(startDate.getMonth());
                  day.setDate(startDate.getDate() + Number(days[i][0]));
                  day_arr.push(day.toDateString());
               }
               console.log(day_arr);
               $.ajax(
               {
                  type : "POST",
                  data :
                  {
                     employee_id_arr : JSON.stringify(employee_id_arr),
                     day_arr         : JSON.stringify(day_arr),
                     begin_arr       : JSON.stringify(starts),
                     end_arr         : JSON.stringify(ends),
                     category_arr    : JSON.stringify(categories)
                  },
                  url : url + "index.php/manager/scheduleEmployeeTemplate",
                  success : function(msg)
                  {
                     var result_arr = jQuery.parseJSON(msg);
                     if(result_arr.pop())
                     {
                        $("#calendar").fullCalendar("removeEventSource", scheduledEventSource);
                        $("#calendar").fullCalendar("addEventSource", scheduledEventSource);
                     }
                     else
                     {
                        var cal_event;
                        for (var i = 0; i < result_arr.length; i++) 
                        {
                           cal_event = jQuery.parseJSON(result_arr[i]);
                           console.log(cal_event);
                           $("#calendar").fullCalendar("renderEvent", cal_event, true);
                        }
                     }
                  },
                  error : function(textStatus)
                  {
                     alert(textStatus + "/manager/scheduleEmployeeTemplate");
                  }
               });
            }
         });
      },
      loading : function(bool)
      {
         if (bool)
            $('#loading').show();
         else
            $('#loading').fadeOut();
      },
      eventRender : function(event, element)
      {
         if (event.category == 'scheduled')
         {
            element.bind("contextmenu", function(e)
            {
               $("input.rightClickMenuItem").each(function()
               {
                  if ($(this).val() == event.area)
                     $(this).attr("checked", "checked");
                  else
                     $(this).removeAttr('checked');
                  $(this).data("element", element);
                  $(this).data("event", event);
               });

               $("#sflRightClickItem").data("element", element).data("event", event);
               if (event.sfl == 1)
               {
                  $("#sflRightClickItem").attr("checked", "checked");
               }
               else
                  $("#sflRightClickItem").removeAttr("checked");
               $("#editEventPopup").show().offset(
               {
                  top : e.pageY,
                  left : e.pageX
               });
               e.preventDefault();
            });
         }
         if(event.category == 'Available' || event.category == 'Busy' || event.category == 'Custom')
         {
            element.bind("contextmenu", function(e)
            {
               $(".overrideRightClick").each(function()
               {
                  if($(this).val() == event.category)
                     $(this).attr("checked", "checked");
                  else
                     $(this).removeAttr('checked');
                  $(this).data("element", element);
                  $(this).data("event", event);
               });
               $("#overrideAvailability").show().offset(
               {
                  top : e.pageY,
                  left : e.pageX
               });
               e.preventDefault();
            });
         }
         var content = "";
         if (event.category == 'scheduled')
         {
            if (event.start != null && event.end != null)
            {
               content = event.title + " " + timeToString(event.start) + "-" + timeToString(event.end);
            }
         }
         else if (event.category == 'Custom')
         {
            content = event.title;
         }
         else if (event.category == "events")
         {
            content = event.title + " At " + event.location;
         }
         else
         {
            content = event.title + " " + event.category;
         }
         var position;
         switch(event.start.getDay())
         {
            case 6 : position = "left"; break;
            case 0 : position = "right"; break;
            default: position = "top"; break;
         }
         element.tooltip({
            animation: false,
            title: content,
            container: 'body',
            placement: position      
         });
         /*element.qtip(
         {
            content : content,
            position :
            {
               at : "top center",
               my : "bottom center"
            },
            style :
            {
               tip : "bottomMiddle",
               classes : "qtip-dark"
            },
            show :
            {
               event : "click"
            },
            events :
            {
               render : function(event, api)
               {
                  api.elements.tooltip.click(api.hide);
               }
            }
         });*/
      },
      /* Function called when an event is clicked on.
       *    calEvent = calendar event object clicked on.
       *    jsEvent is not currently used.
       *    view = the view in which the action was executed.
       */
      eventClick : function(calEvent, jsEvent, view)
      {
         console.log(calEvent);
         $("#editEventPopup").hide();
         $("#employeeRightClickDiv").hide();
         $("#overrideAvailability").hide();
         
         /*if (calEvent.title == 'Test Schedule')
         {
         $("#employeeInfo").title("Test");
         document.getElementById('desired').innerHTML = "Desired Hours: 20-30(Sample)";
         document.getElementById('current').innerHTML = "Scheduled Hours: 15.25(Sample)";
         document.getElementById('notes').innerHTML = "Notes: I would like to have at least one monday off this month (Sample)";
         $("#employeeInfo").dialog();
         // Executed if the event is an availability event
         }*/
         //else
         if (calEvent.category != 'scheduled')
         {
            if (jsEvent.shiftKey)
            {
               if (calEvent.category == "emptyShifts")
                  fillEmptyShift(calEvent);
               else if (!(calEvent.category == 'Busy'))
               {
                  scheduleShiftClick(calEvent);
               }
            }
         }
         if (calEvent.category == "scheduled" || calEvent.category == "events" || calEvent.category == 'emptyShifts')
         {
            if (jsEvent.shiftKey && calEvent.category == "scheduled")
            {
               promptEmployeeHPW(calEvent);
            }
            else if (global_options_obj["delete"] == true && !jsEvent.shiftKey)
            {
               bootbox.confirm("Are you sure you want to delete this event?", function(result)
               {
                  if(result)
                  {
                     deleteEvent(calEvent.category, calEvent.rowId, calEvent.id);
                  }
               });
            }
         }
      },
      eventDragStart : function(event, jsEvent, ui, view)
      {
         //$('.fc-event').qtip('hide');
      },
      eventDragStop : function(event, jsEvent, ui, view)
      {
         //$('.fc-event').qtip('hide');
      },
      /* Function called when an event is droped onto the calendar. Simply calls the eventMove function.
       *
       */
      eventDrop : function(event, dayDelta, minuteDelta, allDay, revertFunc)
      {
         //$('.fc-event').qtip('hide');
         eventMove(event, dayDelta, minuteDelta, revertFunc, "dragged");

      },
      /* Function called when an event is resized on the calendar. Calls the eventMove function.
       *
       */
      eventResize : function(event, dayDelta, minuteDelta, revertFunc, jsEvent, ui, view)
      {
         eventMove(event, dayDelta, minuteDelta, revertFunc, "resized");
      },
      /* Function called when a day selected, or a time range is selected.
       *    start = the beggining selected time.
       *    end = the ending selected time.
       *    allDay = bool, is the event
       *
       */
      select : function(start, end, allDay, jsEvent)
      {
         $("#editEventPopup").hide();
         $("#employeeRightClickDiv").hide();
         $("#overrideAvailability").hide();
         
         if (allDay)
         {
            calendar.fullCalendar('gotoDate', start);
            calendar.fullCalendar('changeView', 'agendaDay');
            return true;
         }
         if (!(jsEvent.shiftKey))
         {
            if (tutorial == true)
            {
               return true;
            }
            var view = calendar.fullCalendar('getView').name;
            var startHour = start.getHours();
            var endHour = end.getHours();
            var startMin = start.toTimeString().split(" ")[0].split(":")[1];
            var endMin = end.toTimeString().split(" ")[0].split(":")[1];

            if (startHour > 12)
            {
               startHour -= 12;
               startTime = startHour + ":" + startMin + "pm";
            }
            else
            {
               startTime = startHour + ":" + startMin + "am";
            }
            if (endHour > 12)
            {
               endHour -= 12;
               endTime = endHour + ":" + endMin + "pm";
            }
            else
            {
               endTime = endHour + ":" + endMin + "am";
            }
            scheduleEmployee(start, end, startTime, endTime);
            calendar.fullCalendar('unselect');
         }
      },
      /* Specifies the urls for retrieving the event source
       *
       */
<<<<<<< HEAD
      eventSources : [availabilityEventSource, scheduledEventSource, coEventSource]
=======
      eventSources : [
      {
         url : url + "index.php/manager/eventSource",
         data : function() {
            return {
            employee_obj : getEmployeeObj(),
            options_obj  : getOptionsObj()
            }
         },
         error : function(msg, textStatus, errorThrown)
         {
            alert(textStatus + "/manager/eventSource");
         }
      },
      {
         url : url + "index.php/manager/scheduledEventSource",
         data :
         {
            employee_obj : getEmployeeObj(),
            options_obj  : getOptionsObj()
         },
         error : function(msg, textStatus, errorThrown)
         {
            alert(textStatus + "/manager/scheduledEventSource");
         }
      },
      {
         url : url + "index.php/manager/coEventSource",
         data :
         {
            employee_obj : getEmployeeObj(),
            options_obj  : getOptionsObj()
         },
         error : function(msg, textStatus, errorThrown)
         {
            alert(textStatus + "coEventSource");
         }
      }]
>>>>>>> 8c8203b1c97c118b81bb63531dac5a3ead007367
   };
   calendar = $('#calendar').fullCalendar(options);
   calendar.fullCalendar("gotoDate", date);
   calendar.fullCalendar('changeView', view);
}
renderCalendar(30, "month", new Date());
