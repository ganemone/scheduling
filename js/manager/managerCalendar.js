showLoading();
var availabilityEventSource = {
   url : url + "index.php/manager/eventSource", 
   data : function() {
      return {
         employee_obj : getEmployeeObj()
     }
  }, 
   beforeSend: function()
   {
      global_ajax_requests++;
      showLoading();
   },
   error : function(msg, textStatus, errorThrown) {
      alert(textStatus + "/manager/eventSource");
   },
   complete: function()
   {
      global_ajax_requests--;
      hideLoading();
   }
}
var scheduledEventSource = {
   url : url + "index.php/manager/scheduledEventSource", data : function() {
      return {
         employee_obj : getEmployeeObj()
      }
   },
   beforeSend: function()
   {
      global_ajax_requests++;
      showLoading();
   },
   error : function(msg, textStatus, errorThrown) {
      alert(textStatus + "/manager/scheduledEventSource");
   },
   complete: function()
   {
      global_ajax_requests--;
      hideLoading();
   }
}
var coEventSource = {
   url : url + "index.php/manager/coEventSource", 
   data : function() {
      return {
         options_obj : getOptionsObj()
      }
   },
   beforeSend: function()
   {
      global_ajax_requests++;
      showLoading();
   },
   error : function(msg, textStatus, errorThrown) {
      alert(textStatus + "coEventSource");
   },
   complete: function()
   {
      global_ajax_requests--;
      hideLoading();
   }
}
var header = {};
var title_format = {};
if(mobile == true)
{
   header = {
      left : 'prev,next',
      center : 'title',
      right : "month,basicWeek"
   };
   title_format = {
      month: "MMM yyyy",
      week: "MMM yyyy",
      day: "MMM yyyy"
   };
} 
else {
   header = {
      left : 'prev,next today',
      center : 'title',
      right : "month,basicWeek,agendaDay"
   };
   title_format = {
      month: 'MMMM yyyy',                             
      week: "MMM d[ yyyy]{ '&#8212;'[ MMM] d, yyyy}", 
      day: 'dddd, MMM d, yyyy'                  
   }
}
function renderCalendar(slotMinutes, view, date) {
   $("#calendar").fullCalendar('destroy');
   $("#calendar").css("width", $(document).width() - 290);
   var options = {
      header : header,
      titleFormat : title_format,
      allowCalEventOverlap : false,
      selectable : true,
      slotMinutes : slotMinutes,
      slotEventOverlap : false,
      minTime : 6,
      maxTime : 22,
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
      viewRender : function(view) {
         if (view.name == 'month') {
            h = NaN;
            if(global_options_obj["prevView"] != "month" && numberEmployeesOn() > 3 ) {
               removeAllEmployees();
            }
         }
         else {
            h = 6000;
         }
         showLoading();
         if(mobile == false) {
           $("span.fc-header-title h2").hide();
           initializeGoalTips(view);
         }
            
         if($("#statistics").is(":visible")) {
            updateStatistics();
         }
         else if($("#graphs").is(":visible")) {
            updateGraphs();
         }
         global_options_obj["prevView"] = view.name;
         $('#calendar').fullCalendar('option', 'contentHeight', h);

      },
      drop : function(date, allDay) {
         var draggedEvent = $(this).data('eventObject');
         var days = draggedEvent.days;
         var starts = draggedEvent.starts;
         var ends = draggedEvent.ends;
         var categories = draggedEvent.categories;
         var startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay());
         var endDate = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate() + 6);

         bootbox.confirm(makeTemplateForm(startDate, endDate, 'checkbox'), function(result) {
            if (result) {
                  var employee_id_arr = new Array();
                  var day_arr = new Array();
                  $("#templateForm > div > label > input").each(function() {
                  if ($(this).is(":checked")) {
                     employee_id_arr.push($(this).val());
                  }                  
               });
               if($("#employee_select_list").val() != "NA" && $.inArray($("#employee_select_list").val(), employee_id_arr) == -1) {
                  employee_id_arr.push($("#employee_select_list").val());
               }
               for (var i = 0; i < days.length; i++) {
                  var day = new Date();
                  day.setFullYear(startDate.getFullYear());
                  day.setMonth(startDate.getMonth());
                  day.setDate(startDate.getDate() + Number(days[i][0]));
                  day_arr.push(day.toDateString());
               }
               if(employee_id_arr.length == 0) {
                  alert("No employee selected");
                  return false;
               }
               if(day_arr.length == 0 || starts.length == 0 || ends.length == 0 || categories.length == 0) {
                  alert("Invalid Template");
                  return false;
               }
                      
               sendRequest("POST", url + "index.php/manager/scheduleEmployeeTemplate", {
                  employee_id_arr : JSON.stringify(employee_id_arr),
                  day_arr : JSON.stringify(day_arr),
                  begin_arr : JSON.stringify(starts),
                  end_arr : JSON.stringify(ends),
                  category_arr : JSON.stringify(categories)
               }, 
               function(msg) {
                  $("#calendar").fullCalendar("removeEventSource", scheduledEventSource);
                  $("#calendar").fullCalendar("addEventSource", scheduledEventSource);
                  successMessage("Scheduled Employee With Template");
               }, true);
            }
         });
      }, 
      eventRender : function(event, element) {
         if (event.category == 'scheduled') {
            element.bind("contextmenu", function(e) {
               $(".fc-event").tooltip("hide");
               $("input.rightClickMenuItem").each(function() {
                  if ($(this).val() == event.area) {
                     $(this).prop("checked", true);
                  }
                  else {
                     $(this).prop("checked", false);
                  }
                  $(this).data("element", element);
                  $(this).data("event", event);
               });

               $("#sflRightClickItem").data("element", element).data("event", event);
               if (event.sfl == 1) {
                  $("#sflRightClickItem").prop("checked", true);
               }
               else {
                  $("#sflRightClickItem").prop("checked", false);
               }
               $("#editEventPopup").show().offset({
                    top : e.pageY, left : e.pageX
               }).dropdown();
               if($("#editEventPopup").offset().top + $("#editEventPopup").height() > $(window).height()) {
                  $("#editEventPopup").offset({
                     top: $(window).height() - $("#editEventPopup").height() - 10
                  });
               }
               e.preventDefault();
            });
         }
         if (event.category == 'Available' || event.category == 'Busy' || event.category == 'Custom') {
            element.bind("contextmenu", function(e) {
               $(".fc-event").tooltip("hide");

               $("#overrideAvailability").show().offset({
                  top : e.pageY, left : e.pageX
               }).dropdown();

               $("#overrideAvailability li").each(function() {
                  var element = $(this).children("a").children("input");
                  if (element.val() == event.category) {
                     element.prop("checked", true);
                  }
                  else {
                     element.prop("checked", false);
                  }
                  $(this).data("element", element);
                  $(this).data("event", event);
               });

                e.preventDefault();
            });
         }
         var content = "";
         if (event.category == 'scheduled') {
            if (event.start != null && event.end != null) {
               content = event.title + " " + timeToString(event.start) + "-" + timeToString(event.end);
            }
         }
         else if (event.category == 'Custom') {
            content = event.title;
         }
         else if (event.category == "events") {
            content = event.title + " At " + event.location;
         }
         else {
            content = event.title + " " + event.category;
         }
         var position;
         switch(event.start.getDay()) {
            case 6 : position = "left"; break;
            case 0 : position = "right"; break;
            default: position = "top"; break;
         }
         if($("#calendar").fullCalendar("getView").name == "agendaDay") {
            position = "top";
         }
         element.tooltip({
            animation : false, title : content, container : 'body', placement : position, trigger : "manual"
         }).on({
            click : function() {
               $(this).tooltip("show");
            },
            mouseleave : function() {
               $(this).tooltip("hide");
            }
         });
      },
      /* Function called when an event is clicked on.
       *    calEvent = calendar event object clicked on.
       *    jsEvent is not currently used.
       *    view = the view in which the action was executed.
       */
      eventClick : function(calEvent, jsEvent, view) {
         if(global_options_obj["eventClick"] == "standard") {
            if (calEvent.category != 'scheduled') {
               if (jsEvent.shiftKey) {
                  if (calEvent.category == "emptyShifts")
                     fillEmptyShift(calEvent);
                  else if (!(calEvent.category == 'Busy')) {
                     scheduleShiftClick(calEvent);
                  }
               }
            }
            if (calEvent.category == "scheduled" || calEvent.category == "events" || calEvent.category == 'emptyShifts') {
               if (jsEvent.shiftKey && calEvent.category == "scheduled") {
                  promptEmployeeHPW(calEvent);
               }
               else if (global_options_obj["delete"] == true && !jsEvent.shiftKey) {
                  $(".fc-event").tooltip("hide");
                  bootbox.confirm("Are you sure you want to delete this event?", function(result) {
                     if (result) {
                        deleteEvent(calEvent.category, calEvent.rowId, calEvent.id);
                     }
                  });
               }
            }
         }
         else if(global_options_obj["eventClick"] == "editTime") {
            cancelShiftEdit();
            return mobile_editShiftTime(calEvent);
         }
         else if(global_options_obj["eventClick"] == "editCategory") {
            cancelShiftEdit();
            var that = $(this);
            $("input.rightClickMenuItem").each(function() {
               if ($(this).val() == calEvent.area) {
                  $(this).prop("checked", true);
               }
               else {
                  $(this).prop("checked", false);
               }
               $(this).data("element", that);
               $(this).data("event", calEvent);
            });

            $("#sflRightClickItem").data("element", that).data("event", calEvent);

            if (calEvent.sfl == 1) {
               $("#sflRightClickItem").prop("checked", true);
            }
            else {
               $("#sflRightClickItem").prop("checked", false);
            }
            $("#editEventPopup").show().offset({
                 top : 100, left : 100
            }).dropdown();
         }
         else if(global_options_obj["eventClick"] == "addShift" && (calEvent.category == "Available" || calEvent.category == "Custom" || calEvent.category == "events")) {
            cancelShiftEdit();
            scheduleShiftClick(calEvent);
         }
         else if(global_options_obj["eventClick"] == "editExternalEvent") {
            editEvent(calEvent);
         }
      },
      /* Function called when an event is droped onto the calendar. Simply calls the eventMove function.
       *
       */
      eventDrop : function(event, dayDelta, minuteDelta, allDay, revertFunc) {
         eventMove(event, dayDelta, minuteDelta, revertFunc, "dragged");
      },
      /* Function called when an event is resized on the calendar. Calls the eventMove function.
       *
       */
      eventResize : function(event, dayDelta, minuteDelta, revertFunc, jsEvent, ui, direction, view) {
         eventMove(event, dayDelta, minuteDelta, revertFunc, "resized", direction);
      },
      /* Function called when a day selected, or a time range is selected.
       *    start = the beggining selected time.
       *    end = the ending selected time.
       *    allDay = bool, is the event
       *
       */
      select : function(start, end, allDay, jsEvent) {
         $("#editEventPopup").hide();
         $("#employeeRightClickDiv").hide();
         $("#overrideAvailability").hide  
         if (allDay) {
            calendar.fullCalendar('gotoDate', start);
            calendar.fullCalendar('changeView', 'agendaDay');
            return true;
         }
         if (!(jsEvent.shiftKey)) {
            if (tutorial == true) {
               return true;
            }
            var view = calendar.fullCalendar('getView').name;
            var startHour = start.getHours();
            var endHour = end.getHours();
            var startMin = start.toTimeString().split(" ")[0].split(":")[1];
            var endMin = end.toTimeString().split(" ")[0].split(":")[1];

            if (startHour > 12) {
               startHour -= 12;
               startTime = startHour + ":" + startMin + "pm";
            }
            else {
               startTime = startHour + ":" + startMin + "am";
            }
            if (endHour > 12) {
               endHour -= 12;
               endTime = endHour + ":" + endMin + "pm";
            }
            else {
               endTime = endHour + ":" + endMin + "am";
            }
            scheduleEmployee(start, end, startTime, endTime);
            calendar.fullCalendar('unselect');
         }
      },
      /* Specifies the urls for retrieving the event source
       *
       */
      eventSources : [availabilityEventSource, scheduledEventSource, coEventSource]
   };
   calendar = $('#calendar').fullCalendar(options);
   calendar.fullCalendar("gotoDate", date);
   calendar.fullCalendar('changeView', view);
}

renderCalendar(30, "month", new Date());
