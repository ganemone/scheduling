function resizeCalendar () {
   var calendar_width = ($(".leftNavOuter").position().left == 0) ? 340 : 15;
   
   $("#calendar").css("width", $(document).width() - calendar_width);

   $(".leftMenu").css("height", $(window).height() - 160);
}

function stringToTime(string) {
   var split = string.split(':');
   var second = split[1].split(" ");
   hour = Number(split[0]);
   if (second[1] == 'pm')
      hour += 12;
   return new Date(0,0,0,hour, second[0], 00).toTimeString().split(" ")[0];
}

function timeToString(time) {
   var string = time.toTimeString().split(" ")[0].split(":");
   var am_pm = 'am';
   var hour = Number(string[0]);
   if (Number(string[0] == 12)) {
      am_pm = 'pm';
   }
   else if (Number(string[0]) > 12) {
      am_pm = 'pm';
      hour -= 12;
   }
   return (hour + ":" + string[1] + " " + am_pm);
}
function editEvent(calEvent) {
   var form_obj = {
      name : "edit_event",
      id : "edit_event",
      elements : [{
            type : "hidden",
            id : "eventId",
            name : "eventId",
            value : calEvent.rowId
         },
         {
            name        : "event_title",
            id          : "event_title",
            type        : "text",
            placeholder : "Enter event title here...",
            value       : calEvent.editTitle,
            label       : "Event Title: "
         },
         {
            name        : "event_location",
            id          : "event_location",
            type        : "text",
            placeholder : "Enter event location here",
            value       : calEvent.location,
            label       : "Location: "
         },
         {
            name        : "event_date",
            id          : "event_date",
            type        : "text",
            placeholder : "YYYY-MM-DD",
            value       : calEvent.editDate,
            label       : "Date: "
         }
      ]
   };
   cancelShiftEdit();
   
   buildStartEndInputs(form_obj, calEvent.editStart, calEvent.editEnd, "06:00:00", "22:00:00");

   bootbox.confirm(buildForm(form_obj), function(result) {
      if(result) {
         sendRequest("POST", url + "index.php/manager/editExternalEvent", buildPostDataObj("#edit_event"), function(msg) {
            $("#calendar").fullCalendar("removeEventSource", coEventSource);
            $("#calendar").fullCalendar("addEventSource", coEventSource);
            successMessage("Updated Event");
         }, false);
      }
   });
}

function promptEditEvent () {
   global_options_obj["eventClick"] = "editExternalEvent";
   $(".top-right").notify({
      type : "bangTidy", 
      message : {
      html : "Select the shift you would like to edit. <button onclick='cancelShiftEdit()' class='btn btn-small btn-primary'>Cancel</button>"
      },
      closable : false,
      fadeOut : {
         enabled : false
      }, 
      onClose : function() {
         global_options_obj["eventClick"] = "standard";
      }
   }).show();
}

function showGoal(date, view) {
   var finalDate;
   if (view.name == "month") {
      date.setDate(1);
      finalDate = new Date(date.getFullYear(), date.getMonth() + 1, 0);
   }
   else if (view.name == 'basicWeek') {
      date.setDate(date.getDate() - date.getDay());
      finalDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() + 6);
   }
   else
      finalDate = date;
   sendRequest("GET", url + "index.php/manager/getGoal", {
      startDate : date.toDateString(),
      endDate : finalDate.toDateString()
   }, function(msg) {

   }, false);
}
function finalizeAvailability () {
   var date = $("#calendar").fullCalendar('getDate');
   var dates = getStartAndEndDates($("#calendar").fullCalendar('getView').name, date);
   var endDate = dates.endDate;
   bootbox.confirm("Are you sure you would like to lock the schedule until " + dates.endDate.toDateString(), function(result) {
      if(result) {
         sendRequest("POST", url + "index.php/manager/lock", {
            date : endDate.toDateString()
         }, 
         function(msg) {
            successMessage("Locked schedule until " + endDate.toDateString());
         }, false);
      }
   });
}

function getStartAndEndDates(view, selectedDate) {
   var ret = '';
   if (view == 'agendaWeek' || view == 'basicWeek') {
      var start = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate() - selectedDate.getDay());
      var end = new Date(start.getFullYear(), start.getMonth(), start.getDate() + 6);
      ret = {
         startDate : start, endDate : end
      };
   }
   else if (view == 'month') {
      var start = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), 1);
      var end = new Date(start.getFullYear(), start.getMonth() + 1, 0);
      ret = {
         startDate : start, endDate : end
      };
   }
   else if (view == 'agendaDay' || view == 'basicDay') {
      ret = {
         startDate : selectedDate, endDate : selectedDate
      }
   }
   return ret;
}

function initializeGoalTips(view) {
   if(mobile == true) {
      $("span.fc-header-title h2").fadeIn();
      return false;
   }
   var dateObj = getStartAndEndDates(view.name, $("#calendar").fullCalendar("getDate"));
   sendRequest("GET", url + "index.php/manager/getGoal", {
      startDate : dateObj.startDate.toDateString(),
      endDate : dateObj.endDate.toDateString()
   }, function(msg) {
         var sum = msg;
         var text = $("span.fc-header-title h2").text();
         var textSplit = text.split("(");
         $("span.fc-header-title h2").text(textSplit[0] + " ($" + sum + ")");
         $("span.fc-header-title h2").fadeIn();
   }, false);
}

/* Function called when the event is moved or resized in any way.
 *    event = Calendar Event
 *    dayDelta = Difference in days on a moved event
 *    minuteDelta = Difference in minutes on a moved event
 *    revertFunc = Function that reverts the element back to its original position.
 *
 */
function eventMove(event, dayDelta, minuteDelta, revertFunc, method, direction) {
   var title = event.title;
   if(typeof direction != undefined && direction == "north") {
      event.end.setMinutes(event.end.getMinutes() - minuteDelta);
      event.start.setMinutes(event.start.getMinutes() - minuteDelta);
   }
   if(dayDelta > 0) {
      sendRequest("POST", url + "index.php/manager/scheduleEmployee", {
         employeeId: event.employeeId,
         day: event.start.toDateString(),
         start_time : event.start.toTimeString().split(" ")[0],
         end_time : event.end.toTimeString().split(" ")[0],
         category : event.area,
         sfl : event.sfl,
         eventTitle : (event.event == "false") ? -1 : event.area
      }, function(msg) {
         var result_arr = jQuery.parseJSON(msg);
         var refetch = false;
         var cal_event_arr = new Array();
         for (var i = 0; i < result_arr.length; i++) {
            var msgArr = jQuery.parseJSON(result_arr[i]);
            cal_event_arr.push(jQuery.parseJSON(msgArr[0]));
            refetch = (msgArr[1] || refetch) ? true : false;
         }
         if (refetch) {
            $("#calendar").fullCalendar("refetchEvents");
         }
         else {
            for (var i = 0; i < cal_event_arr.length; i++) {
               $("#calendar").fullCalendar("renderEvent", cal_event_arr[i]);
            }
         }
         if($("#statistics").is(":visible")) {
            updateStatistics();
         }
         else if($("#graphs").is(":visible")) {
            updateGraphs();
         }

         successMessage((cal_event_arr.length > 1) ? "Scheduled Employees" : "Scheduled Employee");
      }, false);
      
      revertFunc();

   }
   else if (!(event.category == 'scheduled')) {
      revertFunc();
   }
   else {
      var id = event.employeeId;
      sendRequest("POST", url + "index.php/manager/updateScheduledEvent", {
         shift_id : event.rowId, 
         employee_id : event.employeeId,
         day : event.start.toDateString(),
         start_time : event.start.toTimeString().split(" ")[0],
         end_time : event.end.toTimeString().split(" ")[0]
      }, function(msg) {
         if (msg != "[]") {
            remove_arr = jQuery.parseJSON(msg);
            for (var i = 0; i < remove_arr.length; i++) {
               $("#calendar").fullCalendar("removeEvents", function(e) {
                  return (e.rowId == remove_arr[i]) ? true : false;
               });
            }
         }
         if($("#statistics").is(":visible")) {
            updateStatistics();
         }
         else if($("#graphs").is(":visible")) {
            updateGraphs();
         }
      }, false);
   }
}
function buildTemplateEmployeeSelectObj(start_date, end_date, form_obj) {
   var usedIds = new Array();
   var employees = $("#calendar").fullCalendar('clientEvents', function(event) {
      if(event.start >= start_date && event.start <= end_date && event.category == "scheduled" && $.inArray(event.employeeId, usedIds) == -1) {
         return true
         usedIds.push(event.employeeId);
      }
      return false;
   });

   var data = {};

   for (var i = 0; i < employees.length; i++) {
      data[employees[i].employeeId] = {
         "name" : global_employee_obj[employees[i].employeeId]["firstName"] + " " + global_employee_obj[employees[i].employeeId]["lastName"][0],
         "selected" : false
      };
   };

   form_obj["elements"].push({
      "type"        : "select",
      "name"        : "employee_id",
      "id"          : "template_employee_id",
      "label"       : "Employee: ",
      "label_class" : "control-label col-3",
      "input_class" : "col-9",
      "data"        : data
   });
}
function createTemplate(employeeId, templateName) {
   var currentDate = $("#calendar").fullCalendar('getDate');
   var id = ( typeof employeeId == "object") ? employeeId[0] : employeeId;
   currentDate.setDate(currentDate.getDate() - currentDate.getDay());
   sendRequest("POST", url + 'index.php/manager/makeTemplate', {
     id : id,
     title : templateName,
     date : currentDate.toDateString()
   }, function(msg) {
      loadTemplates();
      successMessage("Created Template");
   }, false);
}

function getEmployeesAvailable(begin, end) {
   var usedIds = [];
   var events = $("#calendar").fullCalendar('clientEvents', function(event) {
      startDate = new Date(event.start.getFullYear(), event.start.getMonth(), event.start.getDate());
      beginDate = new Date(begin.getFullYear(), begin.getMonth(), begin.getDate());
      endDate = new Date(end.getFullYear(), end.getMonth(), end.getDate());

      if (startDate >= beginDate && startDate <= endDate && (event.category == "Available" || event.category == "Custom") && $.inArray(event.employeeId, usedIds) == -1) {
         usedIds.push(event.employeeId);
         return true;
      }
      return false;
   });
   return events;
}

function loadTemplates() {
   var html = '';
   $('#templates .external-event').each(function() {
      $(this).remove();
   });

   sendRequest("GET", url + "index.php/manager/loadTemplates", {}, function(msg) {
      var a = jQuery.parseJSON(msg);
      for (var i = 0; i < a.length; i++) {
         var currentTemplateObject = jQuery.parseJSON(a[i]);
         var id = "#template" + i;
         html = "<div class='external-event' style='background: black;' id='template" + i + "'>" + currentTemplateObject.templateName + "</div>";
         $("#templates").append(html);
         $("#template" + i).tooltip({
            animation : false,
            html      : true,
            title     : currentTemplateObject.description,
            container : 'body',
            placement : "right"
         });
         $("#template" + i).click(function() {
            var that = $(this);
            if(global_options_obj["delete"] === true) {
               $(".fc-event").tooltip("hide");
               bootbox.confirm("Are you sure you want to delete this template?", function(result) {
                  if(result) {
                     sendRequest("POST", url + "index.php/manager/deleteTemplate",  {
                        templateId : currentTemplateObject.templateId
                     }, function(msg) {
                        that.remove();
                        successMessage("Deleted Template");
                     }, false);
                  }
               });
            }   
         });
         $("#template" + i).data('eventObject', currentTemplateObject);
         $("#template" + i).draggable({
            zIndex : 999,
            revert : true,
            revertDuration : 0,
            scroll : false,
            start : function() {
               if(global_options_obj["delete"] === true)
               {
                  $("#deleteOption").prop("checked", false);
                  global_options_obj["delete"] = false;
               }
               $('div.fc-event').tooltip('hide');
               $('div.external-event').tooltip('hide');
               $('div.fc-event').tooltip('disable');
               $('div.external-event').tooltip('disable');
            },
            stop : function() {
               $('div.fc-event').tooltip('enable');
               $('div.external-event').tooltip('enable');
            }
         });
      }
   }, false);
}

function makeTemplateForm(begin, end, type) {
   var form_obj = {
      name     : "templateForm",
      id       : "templateForm",
      style    : "width: 400px;",
      elements : [
      {
         "type" : "group",
         "data" : new Array()
      }]
   };
   var employees = getEmployeesAvailable(begin, end);
   for (var i = 0; i < employees.length; i++) {
      form_obj.elements[0].data.push({
         "type"        : "checkbox",
         "name"        : "template_employees[]",
         "id"          : "template_employee_" + employees[i].employeeId,
         "value"       : employees[i].employeeId,
         "label"       : global_employee_obj[employees[i].employeeId].firstName + " " + global_employee_obj[employees[i].employeeId].lastName[0],
         "label_class" : "border-label col-5 label-success",
         "input_class" : "",
         "attr"        : "onchange='disableScheduleOptions(this);'"
      });
   };

   buildEmployeeSelectObj(form_obj);

   return buildForm(form_obj);

}

function scheduleEmployee(start, end, startTime, endTime) {
   var weekStart = new Date(start.getFullYear(), start.getMonth(), start.getDate() - start.getDay());
   var weekEnd = new Date(weekStart.getFullYear(), weekStart.getMonth(), weekStart.getDate() + 6);
   var employees = getEmployeesAvailable(start, end);
   for (var i = 0; i < employees.length; i++) {
      employees[i] = employees[i].employeeId;
   }
   if(employees.length == 0) {
      return continueScheduling(start, end, null);
   }
   sendRequest("POST", url + "index.php/manager/getHoursLeft", {
      start : weekStart.toDateString(),
      end : weekEnd.toDateString(),
      employees : employees
   }, function(msg) {
         var employeeInfo = jQuery.parseJSON(msg);
         continueScheduling(start, end, employeeInfo);
   }, false);
}

function buildEmployeeChecklistObj(form_obj, employeeInfo) {
   var employee_id;
   var element_arr = new Array();
   for (employee_id in employeeInfo) {
      if (employeeInfo.hasOwnProperty(employee_id)) {
         if ( typeof global_employee_obj[employee_id] != "undefined") {
            var hoursLeft = buildHoursLeft(employeeInfo, employee_id);
            var label = "";
            var _class = "label-success";
            if (!isNaN(hoursLeft)) {
               _class = (hoursLeft > 0) ? "label-success" : "label-danger";
            }
            else {
               hoursLeft = "";
            }
            label = global_employee_obj[employee_id]["firstName"] + " " + global_employee_obj[employee_id]["lastName"][0] + "<span class='badge pull-right'>" + hoursLeft + "</span>";

            element_arr.push({
               "name"        : "employees[]",
               "label_class" : "border-label col-5 " + _class,
               "input_class" : "",
               "attr"        : "onchange='disableScheduleOptions(this);'",
               "id"          : "employee_schedule_" + employee_id,
               "label"       : label,
               "value"       : employee_id,
               "type"        : "checkbox"
            });
         }
      }
   }
   form_obj["elements"].push({
      "type" : "group",
      "data" : element_arr
   });
}

function buildCategorySelectObj(form_obj, defaultTo) {
   var data = {}
   var category;
   for (category in global_categories_obj["select_list"]["elements"]) {
      if (global_categories_obj["select_list"]["elements"].hasOwnProperty(category)) {
         var selected = (defaultTo == global_categories_obj["select_list"]["elements"][category]["abbr"]) ? true : false;
         data[global_categories_obj["select_list"]["elements"][category]["abbr"]] = {
            "name" : global_categories_obj["select_list"]["elements"][category]["name"], "selected" : selected
         };
      }
   }
   form_obj["elements"].push({
      "type" : "select", "label" : global_categories_obj["select_list"]["label"], "name" : global_categories_obj["select_list"]["name"], "id" : global_categories_obj["select_list"]["id"], "data" : data, "label_class" : "col-3", "input_class" : "form_control col-9"
   });
}

function buildCategoryAdditionsObj(form_obj, defaultTo) {
   var element_arr = new Array();
   for (var i = 0; i < global_categories_obj["additions"].length; i++) {
      var attr = (global_categories_obj["additions"][i]["abbr"] == defaultTo) ? "checked='checked'" : "";
      element_arr.push({
         "type" : global_categories_obj["additions"][i]["type"], "label" : global_categories_obj["additions"][i]["label"], "label_class" : "border-label col-5", "input_class" : "", "id" : global_categories_obj["additions"][i]["id"], "name" : global_categories_obj["additions"][i]["name"], "value" : global_categories_obj["additions"][i]["abbr"], "attr" : attr
      });
   }
   form_obj["elements"].push({
      "type" : "group", 
      "data" : element_arr
   });
}

function continueScheduling(start, end, employeeInfo) {
   var form_obj = {
      "name" : "schedule_employee", "id" : "schedule_employee", "style" : "width: 420px;", "elements" : new Array()
   };

   form_obj["elements"].push({
      "name" : "day", "label" : "", "type" : "hidden", "id" : "day", "value" : start.toDateString()
   });

   if(employeeInfo != null) {
      buildEmployeeChecklistObj(form_obj, employeeInfo);
   }

   buildEmployeeSelectObj(form_obj);

   buildCategorySelectObj(form_obj, "SF");

   buildStartEndInputs(form_obj, start.getHours() + ":" + start.getMinutes() + ":00", end.getHours() + ":" + end.getMinutes() + ":00", "06:00:00", "22:00:00");

   buildCategoryAdditionsObj(form_obj, "");

   form_obj["title"] = "Date: " + (start.getMonth() + 1) + "/" + start.getDate() + "/" + start.getFullYear() + "<br>Time: " + timeToString(start) + " - " + timeToString(end);

   $(".fc-event").tooltip("hide");
   bootbox.confirm(buildForm(form_obj), function(result) {
      if(result) {
         var data = buildPostDataObj("#schedule_employee");
         var emptyShift = ($("#emptyShift").is(":checked")) ? true : false;
         data['sfl'] = $("#SFL").is(":checked") ? 1 : 0;
         if(data.hasOwnProperty("employees[]") || data.hasOwnProperty("employee_select_list")) {
            sendRequest("POST", url + "index.php/manager/scheduleEmployee", data, function(msg) {
               var result_arr = jQuery.parseJSON(msg);
               var refetch = false;
               var cal_event_arr = new Array();
               for (var i = 0; i < result_arr.length; i++) {
                  var msgArr = jQuery.parseJSON(result_arr[i]);
                  cal_event_arr.push(jQuery.parseJSON(msgArr[0]));
                  refetch = (msgArr[1] || refetch) ? true : false;
               }
               if (refetch) {
                  $("#calendar").fullCalendar("refetchEvents");
               }
               else {
                  for (var i = 0; i < cal_event_arr.length; i++) {
                     $("#calendar").fullCalendar("renderEvent", cal_event_arr[i]);
                  }
               }
               if($("#statistics").is(":visible")) {
                  updateStatistics();
               }
               else if($("#graphs").is(":visible")) {
                  updateGraphs();
               }

               successMessage((cal_event_arr.length > 1) ? "Scheduled Employees" : "Scheduled Employee");
            }, false);
         }

         if (emptyShift) {
            var sfl = ($("#SFL").is(":checked")) ? 1 : 0;
            addEmptyShift(start, end, $("#category").val(), sfl);
         }
      }
   });
}

function promptEmployeeHPW(calEvent) {
   sendRequest("POST", url + "index.php/manager/getEmployeeWeekHours", {
         employeeId : calEvent.employeeId,
         dayNum : calEvent.start.getDay(),
         date : calEvent.start.toDateString()
      }, 
      function(msg) {
         var hourInfo = JSON.parse(msg);
         var message = "<div style='width: 300px;'>";
         message += "<h3>" + calEvent.title.split("(")[0] + "</h3><hr>";
         message += "<table class='table table-striped'><tr><td>";
         message += "Desired Hours: </td><td>" + hourInfo['desired'] + "</td></tr><tr><td>";
         message += "Scheduled Hours: </td><td>" + hourInfo['scheduled'] + "</td></tr></tr><td>";
         message += "Notes: </td><td>" + hourInfo['notes'] + "</td></tr></table>";
         message += "</div>";
         $(".fc-event").tooltip("hide");
         bootbox.alert(message);
   }, false);
}

function scheduleShiftClick(calEvent) {
   $(".fc-event").tooltip("hide");
   var start, end;
   var employees = [calEvent.employeeId];
   var date = calEvent.start;
   var weekStart = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay());
   var weekEnd = new Date(weekStart.getFullYear(), weekStart.getMonth(), weekStart.getDate() + 6);

   if (calEvent.category == 'Custom' || calEvent.category == "events") {
      start = calEvent.start.toTimeString().split(" ")[0];
      end = calEvent.end.toTimeString().split(" ")[0];
   }
   else if (calEvent.category == 'Available') {
      if (calEvent.start.getDay() == 0) {
         start = '11:45:00';
         end = '18:00:00';
      }
      else if (calEvent.start.getDay() == 6) {
         start = '09:45:00';
         end = '18:30:00';
      }
      else {
         start = '09:45:00';
         end = '20:30:00';
      }
   }
   if (calEvent.category != "events") {
      sendRequest("POST", url + "index.php/manager/getHoursLeft", {
         start : weekStart.toDateString(),
         end : weekEnd.toDateString(),
         employees : employees
      }, function(msg) {
         var employeeInfo = jQuery.parseJSON(msg);
         continueScheduleShiftClick(calEvent, employeeInfo, start, end);
      }, false);
   }
   else {
      continueScheduleShiftClick(calEvent, null, start, end);
   }
}

function buildHoursLeft(employeeHoursLeft, employee_id) {
   var desired = employeeHoursLeft[employee_id].desired.split("-")[1];
   var scheduled = employeeHoursLeft[employee_id].scheduled;
   var hoursLeft = desired - scheduled;
   return hoursLeft;
}

function buildEmployeeSelectObj(form_obj) {
   var data = {}
   var employee_obj;
   data["NA"] = {
      "name" : "--------------", "selected" : true
   };
   for (employee_id in global_employee_obj) {
      if (global_employee_obj.hasOwnProperty(employee_id)) {
         data[employee_id] = {
            "name" : global_employee_obj[employee_id]["firstName"] + " " + global_employee_obj[employee_id]["lastName"],
         }
      }
   }
   form_obj["elements"].push({
      "type" : "select", "label" : "Employees: ", "name" : "employee_select_list", "id" : "employee_select_list", "label_class" : "control-label col-3", "input_class" : "col-9", "data" : data
   });
}

function continueScheduleShiftClick(calEvent, employeeHoursLeft, start, end) {
   if (employeeHoursLeft != null) {
      var hoursLeft = buildHoursLeft(employeeHoursLeft, calEvent.employeeId);
   }

   var form_obj = {
      "name" : "schedule_employee", "id" : "schedule_employee", "style" : "width: 500px;", "elements" : new Array()
   };

   var eventTitle = -1;
   var name = calEvent.title.split(" ");
   name = name[0] + " " + name[1] + " ";
   var title = "Schedule " + name;
   if (calEvent.category == "events") {
      title = "Schedule Employee for " + calEvent.title.split("(")[0] + " At " + calEvent.location;
      buildEmployeeSelectObj(form_obj);
      eventTitle = calEvent.title.split("(")[0];
      buildStartEndInputs(form_obj, start, end, "06:00:00", "22:00:00");
      form_obj["elements"].push({
         "type" : "group", 
         "data" : [{
            "type"        : "checkbox",
            "label"       : "Add Empty Shift",
            "label_class" : "col-6",
            "input_class" : "",
            "id"          : "emptyShift",
            "name"        : "emptyShift",
            "value"       : "",
            "attr"        : ""
         }]
      });
   }
   else {
      var defaultTo = ( typeof calEvent.defaultTo != "undefined") ? calEvent.defaultTo : "SF";

      buildCategorySelectObj(form_obj, defaultTo);
      buildStartEndInputs(form_obj, start, end, "06:00:00", "22:00:00");
      buildCategoryAdditionsObj(form_obj, "");
   }

   if ( typeof hoursLeft !== "undefined" && !isNaN(hoursLeft)) {
      var color = (hoursLeft > 0) ? "green" : "red";
      hoursLeft = "<span style='color:" + color + "'>(Hours Left: " + hoursLeft + ")</span>";
   }

   title += (hoursLeft) ? hoursLeft : "";
   form_obj["title"] = title;
   var form = buildForm(form_obj);
   $(".fc-event").tooltip("hide");
   bootbox.confirm(form, function(result) {
      if (result) {
         var id = (calEvent.category == "events") ? $("#employee_select_list").val() : calEvent.employeeId;
         var emptyShift = ($("#emptyShift").is(":checked")) ? true : false;
         var sfl = ($("#SFL").is(":checked")) ? 1 : 0;
         if (validateStartEndTimes($("#start_time").val(), $("#end_time").val()) === false) {
            alert("The start time must come before the end time");
            return false;
         }
         if (id != "NA" && id > 0) {
            sendRequest("POST", url + "index.php/manager/scheduleEmployee", {
               employeeId : id,
               day : calEvent.start.toDateString(),
               start_time : $("#start_time").val(),
               end_time : $("#end_time").val(),
               category : $("#category").val(),
               sfl : sfl,
               eventTitle : eventTitle
            }, 
            function(msg) {
               var result_arr = jQuery.parseJSON(msg);
               var refetch = false;
               var cal_event_arr = new Array();
               for (var i = 0; i < result_arr.length; i++) {
                  var msgArr = jQuery.parseJSON(result_arr[i]);
                  cal_event_arr.push(jQuery.parseJSON(msgArr[0]));
                  refetch = (msgArr[1] || refetch) ? true : false;
               }
               if (refetch) {
                  $("#calendar").fullCalendar("refetchEvents");
               }
               else {
                  for (var i = 0; i < cal_event_arr.length; i++) {
                     $("#calendar").fullCalendar("renderEvent", cal_event_arr[i]);
                  }
               }
               if($("#statistics").is(":visible")) {
                  updateStatistics();
               }
               else if($("#graphs").is(":visible")) {
                  updateGraphs();
               }
               successMessage("Scheduled Employee");
            }, false);
         }
         if (emptyShift) {
            var start = calEvent.start;
            start.setHours($("#start_time").val().split(":")[0]);
            start.setMinutes($("#start_time").val().split(":")[1]);
            var end = new Date(start.getFullYear(), start.getMonth(), start.getDate(), $("#end_time").val().split(":")[0], $("#end_time").val().split(":")[1], 0);
            var cat = (eventTitle == -1) ? $("#category").val() : eventTitle;
            addEmptyShift(start, end, cat, sfl);
         }
      }
      return true;
   });
}

function disableScheduleOptions(element) {
   $("#employee_select_list").children("option").each(function() {
      if (element.checked && $(this).val() == element.value) {
         $(this).prop("disabled", true);
      }
      else if ($(this).val() == element.value) {
         $(this).prop("disabled", false);
      }
   });
}

function clearEditEventPopup() {
   $(".rightClickMenuItem").each(function() {
      if ($(this).val() != "SP") {
         $(this).prop("selected", false);
         $(this).prop("checked", false);
      }
   });
}

function addEmptyShift(start, end, category, sfl) {
   sendRequest("GET", url + "index.php/manager/addEmptyShift", {
      date : start.toDateString(), start : start.toTimeString().split(" ")[0],
      end : end.toTimeString().split(" ")[0],
      category : category,
      sfl : sfl
   }, 
   function(msg) {
      $("#calendar").fullCalendar("refetchEvents");
      successMessage("Added Empty Shift");
   }, false);
}

function deleteEvent(table, id, calId) {
   sendRequest("POST", url + "index.php/manager/deleteEvent", {
      id : id,
      table : table
   },
   function(msg) {
      calendar.fullCalendar("removeEvents", calId);
      if($("#statistics").is(":visible")) {
         updateStatistics();
      }
      else if($("#graphs").is(":visible")) {
         updateGraphs();
      }
      successMessage("Deleted Event");
   }, false);
}

function fillEmptyShift(calEvent) {
   calEvent.start.setSeconds(0);
   calEvent.end.setSeconds(0);
   var start = calEvent.start.toTimeString().split(" ")[0];
   var end = calEvent.end.toTimeString().split(" ")[0];
   var form_obj = {
      "name" : "schedule_employee",
      "id" : "schedule_employee",
      "style" : "width: 500px;",
      "elements" : new Array()
   };

   var eventTitle = -1;
   var title = "Fill Empty Shift: ";
   if (calEvent.event == "false") {
      var defaultTo = "SF";
      buildEmployeeSelectObj(form_obj);
      buildCategorySelectObj(form_obj, defaultTo);
      buildStartEndInputs(form_obj, start, end, "06:00:00", "22:00:00");
      buildCategoryAdditionsObj(form_obj, "");
   }
   else {
      eventTitle = calEvent.title.substring(calEvent.title.indexOf("(") + 1, calEvent.title.indexOf(")"));

      title = "Schedule Employee for " + eventTitle;

      buildEmployeeSelectObj(form_obj);
      
      buildStartEndInputs(form_obj, start, end, "06:00:00", "22:00:00");
   }
   form_obj["title"] = title;
   var form = buildForm(form_obj);
   $(".fc-event").tooltip("hide");
   bootbox.confirm(form, function(result) {
      if (result) {
         var id = $("#employee_select_list").val();
         var emptyShift = ($("#emptyShift").is(":checked")) ? true : false;
         var sfl = ($("#SFL").is(":checked")) ? 1 : 0;
         if (validateStartEndTimes($("#start_time").val(), $("#end_time").val()) === false) {
            alert("The start time must come before the end time");
            return false;
         }
         if (id != "NA" && id > 0) {
            sendRequest("POST", url + "index.php/manager/scheduleEmployee", {
               employeeId : id,
               day        : calEvent.start.toDateString(),
               start_time : $("#start_time").val(),
               end_time   : $("#end_time").val(),
               category   : $("#category").val(),
               sfl        : sfl,
               eventTitle : eventTitle
            },
            function(msg) {
               var result_arr = jQuery.parseJSON(msg);
               var refetch = false;
               var cal_event_arr = new Array();
               for (var i = 0; i < result_arr.length; i++) {
                  var msgArr = jQuery.parseJSON(result_arr[i]);
                  cal_event_arr.push(jQuery.parseJSON(msgArr[0]));
                  refetch = (msgArr[1] || refetch) ? true : false;
               }
               if (refetch) {
                  $("#calendar").fullCalendar("refetchEvents");
               }
               else {
                  for (var i = 0; i < cal_event_arr.length; i++) {
                     $("#calendar").fullCalendar("renderEvent", cal_event_arr[i]);
                  }
               }
               if($("#statistics").is(":visible")) {
                  updateStatistics();
               }
               else if($("#graphs").is(":visible")) {
                  updateGraphs();
               }
               successMessage("Filled Empty Shift");
            }, false);
         }
      }
      return true;
   });
}

function toggleDelete(e) {
   var element = $("#deleteOption");
   if ( typeof e != "undefined") {
      if (element.is(":checked"))
         element.prop("checked", false);
      else
         element.prop("checked", true);
   }
   if (element.is(":checked")) {
      element.prop("checked", false);
      global_options_obj['delete'] = false;
   }
   else {
      element.prop("checked", true);
      global_options_obj['delete'] = true;
   }
}

function updateSort() {
   var sorting = $("#sorting").val();
   $("#calendar").fullCalendar("option", "sorting", sorting);
   $("#calendar").fullCalendar("refetchEvents");
}

function updateCategory(element) {
   var child = $(element).children("input");
   var eventObject = child.data("event");
   var category = child.val();
   var data = {};
   var _url = url + "index.php/manager/";
   if ($(element).text() == "SFL") {
      data = {
         id : eventObject.rowId, sfl : (child.is(":checked")) ? 0 : 1
      };
      _url += "updateSFL";
   }
   else {
      _url += "updateShiftCategory";
      data = {
         id : eventObject.rowId, category : category
      };
   }
   sendRequest("POST", _url, data, function(eventObj) {
      eventObj = jQuery.parseJSON(eventObj);
      for (var prop in eventObj) {
         if (eventObj.hasOwnProperty(prop)) {
            eventObject[prop] = eventObj[prop];
         }
      }
      $("#calendar").fullCalendar("updateEvent", eventObject);
      successMessage("Updated Shift Category");
   }, false);

   $("#editEventPopup").hide();
}

/* Toggle Functions */
function toggleAll(e) {
   if($("#calendar").fullCalendar("getView").name == "month") {
      $("#calendar").fullCalendar("changeView", "agendaDay");
   }
   
   $("#all_busy").prop("indeterminate", false);
   $("#all_scheduled").prop("indeterminate", false);
   $("#all_available").prop("indeterminate", false);

   var element = $("#all_employees");
   element.prop("indeterminate", false);
   if ( typeof e != "undefined") {
      if (element.is(":checked"))
         element.prop("checked", false);
      else
         element.prop("checked", true);
   }
   var bool_set = false;
   var busy_bool = false;
   var scheduled_bool = false;
   var availability_bool = false;
   if (element.is(":checked")) {
      element.prop("checked", false);

      $(".group").each(function() {
         $(this).prop("checked", false);
      });
      $("#all_busy").prop("checked", false);
      $("#all_scheduled").prop("checked", false);
      $("#all_available").prop("checked", false);
   }
   else {
      element.prop("checked", true);

      $(".group").each(function() {
         $(this).prop("checked", true);
      });

      $("#all_busy").prop("checked", true);
      $("#all_scheduled").prop("checked", true);
      $("#all_available").prop("checked", true);

      bool_set = true;
      if ($("#all_busy").is(":checked"))
         busy_bool = true;
      if ($("#all_scheduled").is(":checked"))
         scheduled_bool = true;
      if ($("#all_available").is(":checked"))
         availability_bool = true;
   }

   for (var i = 0; i < global_employee_id_arr.length; i++) {
      global_employee_obj[global_employee_id_arr[i]]['scheduled'] = scheduled_bool;
      global_employee_obj[global_employee_id_arr[i]]['available'] = availability_bool;
      global_employee_obj[global_employee_id_arr[i]]['busy'] = busy_bool;
   }

   if (bool_set === false) {
      $("#calendar").fullCalendar("removeEvents", function(event) {
         if (event.category == "Available" || event.category == "scheduled" || event.category == "Custom" || event.category == "Busy")
            return true;
         return false;
      });
   }
   else {
      $("#calendar").fullCalendar("removeEventSource", availabilityEventSource);
      $("#calendar").fullCalendar("addEventSource", availabilityEventSource);
      $("#calendar").fullCalendar("removeEventSource", scheduledEventSource);
      $("#calendar").fullCalendar("addEventSource", scheduledEventSource);
   }
   setGlobals();
   setGroupCheckBoxes("all");
}
$(".preventDefault").click(function(e) { 
   $(this).prop("checked", ($(this).is(":checked")) ? false : true);
});
function toggleAllCategory(category) {
   var element = $("#all_" + category);
   var bool_set = false;
   element.prop("indeterminate", false);
   if (!element.is(":checked"))
      bool_set = true;

   for (var i = 0; i < global_employee_id_arr.length; i++) {
      global_employee_obj[global_employee_id_arr[i]][category] = bool_set;
   }

   if (element.is(":checked")) {
      element.prop("checked", false);
      $("#calendar").fullCalendar("removeEvents", function(event) {
         if (event.category == "Custom")
            return true;
         if (category == "available" && (event.category == "Available" || event.category == "Custom"))
            return true;
         else if (category == "busy" && event.category == "Busy")
            return true;
         else if (category == "scheduled" && event.category == "scheduled")
            return true;
         return false;
      });
   }
   else {
      element.prop("checked", true);
      if (category == "scheduled") {
         $("#calendar").fullCalendar("removeEventSource", scheduledEventSource);
         $("#calendar").fullCalendar("addEventSource", scheduledEventSource);
      }
      else {
         $("#calendar").fullCalendar("removeEventSource", availabilityEventSource);
         $("#calendar").fullCalendar("addEventSource", availabilityEventSource);
      }
   }

   if ($("#all_scheduled").is(":checked") && $("#all_busy").is(":checked") && $("#all_available").is(":checked")) {
      $("#all_employees").prop("checked", true);
   }
   else if ($("#all_scheduled").is(":checked") || $("#all_busy").is(":checked") || $("#all_available").is(":checked")) {
      $("#all_employees").prop("checked", false).prop("indeterminate", true);
   }
   else {
      $("#all_employees").prop("checked", false);
   }
   setGlobals();
   setGroupCheckBoxes();
}

function toggleEvents(e) {
   var element = $("#event_all");
   global_options_obj["events"] = !global_options_obj["events"];
   element.prop("checked", global_options_obj["events"]);
   if (global_options_obj["events"] === false) {
      $("#calendar").fullCalendar("removeEvents", function(event) {
         return (event.category == "events") ? true : false;
      });
   }
   else {
      $("#calendar").fullCalendar("removeEventSource", coEventSource);
      $("#calendar").fullCalendar("addEventSource", coEventSource);
   }

}

function toggleGroup(group, category, event) {
   var element = $("#" + group + "_" + category);
   element.prop("indeterminate", false);

   if(typeof event != "undefined") {
      element.prop("checked", !element.prop("checked"));
   }
   if (element.is(":checked")) {
      element.prop("checked", false);
      if (category == "employees") {
         $("." + group).each(function() {
            $(this).prop("checked", false).prop("indeterminate", false);
         });
         global_groups_obj[group]["available"] = false;
         global_groups_obj[group]["busy"] = false;
         global_groups_obj[group]["scheduled"] = false;
      }
      else {
         global_groups_obj[group][category] = false;
      }

      $("#calendar").fullCalendar("removeEvents", function(event) {
         if ( typeof event.employeeId != "undefined" && $.inArray(event.employeeId, global_groups_obj[group]["employees"]) > -1 && (category == "employees" || (category == "busy" && event.category == "Busy") || (category == "available" && event.category == "Available" || event.category == "Custom") || (category == "scheduled" && event.category == "scheduled")))
            return true;
         return false;
      });
   }
   else {
      element.prop("checked", true);
      if (category == "employees") {
         $("." + group).each(function() {
            $(this).prop("checked", true);
         });
         for (var cat in global_groups_obj[group]) {
            if (global_groups_obj[group].hasOwnProperty(cat) && cat != "employees") {
               global_groups_obj[group][cat] = true;
            }
         }
      }
      else {
         global_groups_obj[group][category] = true;
      }
   }

   if ($("#" + group + "_available").is(":checked") && $("#" + group + "_busy").is(":checked") && $("#" + group + "_scheduled").is(":checked")) {
      $("#" + group + "_employees").prop("checked", true).prop("indeterminate", false);
   }
   else if ($("#" + group + "_available").is(":checked") || $("#" + group + "_busy").is(":checked") || $("#" + group + "_scheduled").is(":checked")) {
      $("#" + group + "_employees").prop("checked", false).prop("indeterminate", true);
   }
   else {
      $("#" + group + "_employees").prop("checked", false).prop("indeterminate", false);
   }

   setEmployeeCheckBoxes(group);
   setGroupCheckBoxes(group);
}

function toggleEmployee(employeeId, event) {
   
   var element = $("#employee_" + employeeId);
   element.prop("indeterminate", false);
   if(typeof event != "undefined") {
      element.prop("checked", !element.prop("checked"));
   }

   if (element.is(":checked")) {
      element.prop("checked", false);
      global_employee_obj[employeeId]['available'] = false;
      global_employee_obj[employeeId]['busy'] = false;
      global_employee_obj[employeeId]['scheduled'] = false;
      $("#available_" + employeeId).prop("checked", false);
      $("#busy_" + employeeId).prop("checked", false);
      $("#scheduled_" + employeeId).prop("checked", false);
      $("#calendar").fullCalendar("removeEvents", function(event) {
         return (event.employeeId == employeeId) ? true : false;
      });
   }
   else {
      element.prop("checked", true);
      global_employee_obj[employeeId]['available'] = true;
      global_employee_obj[employeeId]['busy'] = true;
      global_employee_obj[employeeId]['scheduled'] = true;
      $("#available_" + employeeId).prop("checked", true);
      $("#busy_" + employeeId).prop("checked", true);
      $("#scheduled_" + employeeId).prop("checked", true);

      $("#calendar").fullCalendar("removeEventSource", availabilityEventSource);
      $("#calendar").fullCalendar("addEventSource", availabilityEventSource);
      $("#calendar").fullCalendar("removeEventSource", scheduledEventSource);
      $("#calendar").fullCalendar("addEventSource", scheduledEventSource);
   }
   setGroupCheckBoxes();
}

function toggleAvailability(employeeId) {
   var element = $("#available_" + employeeId);
   if (element.is(":checked")) {
      element.prop("checked", false);
      global_employee_obj[employeeId]['available'] = false;
      $("#calendar").fullCalendar("removeEvents", function(event) {
         if (event.employeeId == employeeId && event.category == "Available" || event.category == "Custom")
            return true;
         return false;
      });
   }
   else {
      element.prop("checked", true);
      global_employee_obj[employeeId]['available'] = true;
      $("#calendar").fullCalendar("removeEventSource", availabilityEventSource);
      $("#calendar").fullCalendar("addEventSource", availabilityEventSource);
   }
   updateCheckbox(employeeId);
   setGroupCheckBoxes();
}

function toggleBusy(employeeId) {
   var element = $("#busy_" + employeeId);
   if (element.is(":checked")) {
      element.prop("checked", false);
      element.prop("checked", false);
      global_employee_obj[employeeId]['busy'] = false;
      $("#calendar").fullCalendar("removeEvents", function(event) {
         if (event.employeeId == employeeId && event.category == 'Busy')
            return true;
         return false;
      });
   }
   else {
      element.prop("checked", true);
      global_employee_obj[employeeId]['busy'] = true;
      $("#calendar").fullCalendar("removeEventSource", availabilityEventSource);
      $("#calendar").fullCalendar("addEventSource", availabilityEventSource);
   }
   updateCheckbox(employeeId);
   setGroupCheckBoxes();
}

function toggleScheduled(employeeId) {
   var element = $("#scheduled_" + employeeId);
   if (element.is(":checked")) {
      element.prop("checked", false);
      element.prop("checked", false);
      global_employee_obj[employeeId]['scheduled'] = false;
      $("#calendar").fullCalendar("removeEvents", function(event) {
         if (event.employeeId == employeeId && event.category == 'scheduled')
            return true;
         return false;
      });
   }
   else {
      element.prop("checked", true);
      global_employee_obj[employeeId]['scheduled'] = true;
      $("#calendar").fullCalendar("removeEventSource", scheduledEventSource);
      $("#calendar").fullCalendar("addEventSource", scheduledEventSource);
   }
   updateCheckbox(employeeId);
   setGroupCheckBoxes();
}

function updateCheckbox(employeeId) {
   if (global_employee_obj[employeeId]['available'] && global_employee_obj[employeeId]['busy'] && global_employee_obj[employeeId]['scheduled']) {
      $("#employee_" + employeeId).prop("checked", true).prop("indeterminate", false);
   }
   else if (!global_employee_obj[employeeId]['available'] && !global_employee_obj[employeeId]['busy'] && !global_employee_obj[employeeId]['scheduled']) {
      $("#employee_" + employeeId).prop("checked", false).prop("indeterminate", false);
   }
   else {
      $("#employee_" + employeeId).prop("checked", false).prop("indeterminate", true);
   }
}

function setGlobals() {
   var schedule_set = false;
   var available_set = false;
   var busy_set = false;
   if ($("#all_scheduled").is(":checked")) {
      schedule_set = true;
      checkAllOptions("scheduled_");
   }
   else {
      uncheckAllOptions("scheduled_");
   }
   if ($("#all_available").is(":checked")) {
      available_set = true;
      checkAllOptions("available_");
   }
   else {
      uncheckAllOptions("available_");
   }
   if ($("#all_busy").is(":checked")) {
      busy_set = true;
      checkAllOptions("busy_");
   }
   else {
      uncheckAllOptions("busy_");
   }

   if (schedule_set && busy_set && available_set)
      checkAllOptions("employee_");
   else if (schedule_set || busy_set || available_set)
      indeterminateAllOptions("employee_");
   else
      uncheckAllOptions("employee_");
}

function checkAllOptions(prefix) {
   for (var i = 0; i < global_employee_id_arr.length; i++) {

      $("#" + prefix + global_employee_id_arr[i]).prop("checked", true).prop("indeterminate", false);
   }
}

function uncheckAllOptions(prefix) {
   for (var i = 0; i < global_employee_id_arr.length; i++) {

      $("#" + prefix + global_employee_id_arr[i]).prop("checked", false).prop("indeterminate", false);
   }
}

function indeterminateAllOptions(prefix) {
   for (var i = 0; i < global_employee_id_arr.length; i++) {

      $("#" + prefix + global_employee_id_arr[i]).prop("checked", false).prop("indeterminate", true);
   }
}

function setEmployeeCheckBoxes(group) {
   var add_obj = {
      "available" : false, "busy" : false, "scheduled" : false
   };
   var remove_obj = {
      "available" : false, "busy" : false, "scheduled" : false
   };
   for (var i = 0; i < global_groups_obj[group]["employees"].length; i++) {
      var employee_id = global_groups_obj[group]["employees"][i];
      var group_obj = global_groups_obj[group];
      var checked_counter = 0;
      var unchecked_counter = 0;
      var cat_arr = ["available", "busy", "scheduled"];
      for (var j = 0; j < cat_arr.length; j++) {
         var category = cat_arr[j];
         if (group_obj[category]) {
            if (!$("#" + category + "_" + employee_id).is(":checked")) {
               add_obj[category] = true;
               $("#" + category + "_" + employee_id).prop("checked", true);
            }
            global_employee_obj[employee_id][category] = true;
            checked_counter++;
         }
         else {
            remove_obj[category] = true;
            $("#" + category + "_" + employee_id).prop("checked", false);
            global_employee_obj[employee_id][category] = false;
            unchecked_counter++;
         }
      }
      if (unchecked_counter == 0) {
         $("#employee_" + employee_id).prop("checked", true).prop("indeterminate", false);
      }
      else if (checked_counter == 0) {
         $("#employee_" + employee_id).prop("checked", false).prop("indeterminate", false);
      }
      else {
         $("#employee_" + employee_id).prop("checked", false).prop("indeterminate", true);
      }
   }
   if (add_obj["scheduled"]) {
      $("#calendar").fullCalendar("removeEventSource", scheduledEventSource);
      $("#calendar").fullCalendar("addEventSource", scheduledEventSource);
   }
   if (add_obj["available"] || add_obj["busy"]) {
      $("#calendar").fullCalendar("removeEventSource", availabilityEventSource);
      $("#calendar").fullCalendar("addEventSource", availabilityEventSource);
   }
   if (remove_obj["available"] || remove_obj["busy"] || remove_obj["scheduled"]) {
      $("#calendar").fullCalendar("removeEvents", function(event) {
         if (!event[group]) {
            return false
         }
         if (remove_obj["available"] && (event.category == "Available" || event.category == "Custom")) {
            return true;
         }
         else if (remove_obj["busy"] && event.category == "Busy") {
            return true;
         }
         else if (remove_obj["scheduled"] && event.category == "Scheduled") {
            return true;
         }
         return false;
      });
   }
}

function setGroupCheckBoxes(g) {
   for (group in global_groups_obj) {
      if (global_groups_obj.hasOwnProperty(group) && ( typeof g == "undefined" || group != g)) {
         checkGroup(group);
      }
   }
   if (g != "all") {
      checkGroup("all");
   }
}

function checkGroup(group) {
   var selector_arr = ["_employees", "_available", "_busy", "_scheduled"];

   for (var i = 0; i < selector_arr.length; i++) {
      var element = $("#" + group + selector_arr[i]);

      element.prop("indeterminate", false);

      if ($("." + group + selector_arr[i] + ":not(:checked)").length == 0) {
         element.prop("checked", true);
      }
      else if ($("." + group + selector_arr[i] + ":checked").length == 0 && $("." + group + selector_arr[i] + ":indeterminate").length == 0) {
         element.prop("checked", false);
      }
      else {
         element.prop("indeterminate", true);
         element.prop("checked", false);
      }
   };
}
function addExternalEvent () 
{
   var data_arr = new Array();
   var form_obj = {
      "name"     : "addExternalEvent",
      "id"       : "addExternalEvent",
      "style"    : "width: 440px",
      "elements" : [
         {
            "name"        : "event_title",
            "id"          : "event_title",
            "type"        : "text",
            "placeholder" : "Enter event title here...",
            "label"       : "Event Title: "
         },
         {
            "name"        : "event_location",
            "id"          : "event_location",
            "type"        : "text",
            "placeholder" : "Enter event location here",
            "label"       : "Location: "
         },
         {
            "name"        : "event_date",
            "id"          : "event_date",
            "type"        : "text",
            "placeholder" : "YYYY-MM-DD",
            "label"       : "Date: "
         },
         {
            "name"        : "event_repeat",
            "id"          : "event_repeat",
            "type"        : "select",
            "label"       : "Repeat: ",
            "label_class" : "control-label col-3",
            "input_class" : "col-9",
            "data"        : {
               "0" : { 
                  "name" : "No Repeat",
                  "selected" : true
               },
               "1/7" : {
                  "name" : "Daily",
                  "selected" : false
               },
               "1" : {
                  "name" : "Weekly",
                  "selected" : false
               },
               "2" : {
                  "name" : "Bi-Weekly",
                  "selected" : false
               },
               "4" : {
                  "name" : "Monthly",
                  "selected" : false
               }
            }
         },
         {
            "name"        : "repeat_end_date",
            "id"          : "repeat_end_date",
            "type"        : "text",
            "placeholder" : "YYYY-MM-DD",
            "label"       : "Repeat Until: ",
         }
      ]
   };

   buildStartEndInputs(form_obj, "06:00:00", "22:00:00", "06:00:00", "22:00:00");

   bootbox.confirm(buildForm(form_obj), function(result)
   {
      if(result)
      {
         if(validateStartEndTimes($("#start_time").val(), $("#end_time").val()) == false)
         {
            alert("The start time must come before the end time");
            return false;
         }
         if($("#event_repeat").val() > 0 && validateStartEndDates($("#event_date").val(), $("#repeat_end_date").val()) == false)
         {
            alert("The event date must come before the end repeat date.");
            return false;
         }
         if($("#event_title").val() == "")
         {
            alert("Please enter an event title");
            return false;
         }
         if($("#event_date").val() == "")
         {
            alert("Please enter an event date.");
            return false;
         }
         sendRequest("POST", url + "index.php/manager/addExternalEvent", buildPostDataObj("#addExternalEvent"),
         function(msg)
         {
            $("#calendar").fullCalendar("removeEventSource", coEventSource);
            $("#calendar").fullCalendar("addEventSource", coEventSource);
            successMessage("Added Event");
         }, false);
      }
      return true;
   });
}
function updateStatistics () {
   var startDate = $("#calendar").fullCalendar("getDate");
   var view = $("#calendar").fullCalendar('getView');
   var endDate = $("#calendar").fullCalendar('getDate');
   if (view.name == 'month') {
      startDate.setDate(1);
      endDate.setMonth(endDate.getMonth() + 1);
      endDate.setDate(0);
   }
   else {
      endDate.setDate(endDate.getDate() + 6 - endDate.getDay());
      startDate.setDate(startDate.getDate() - startDate.getDay());
   }
   sendRequest("POST", url + 'index.php/manager/getTotalInfo', {
      start : startDate.toDateString(),
      end : endDate.toDateString(),
      view : view.name
   }, 
   function(msg) {
      $("#summary").html(msg);
      sendRequest("POST", url + 'index.php/manager/getHourWageInfo', {
         start : startDate.toDateString(),
         end : endDate.toDateString()
      },
      function(msg) {
         $("#expanded").html(msg);
      }, false);
   }, false);
}
function updateGraphs () {
   var startDate = $("#calendar").fullCalendar("getDate");
   var view = $("#calendar").fullCalendar('getView');
   var endDate = $("#calendar").fullCalendar('getDate');
   if (view.name == 'month') {
      startDate.setDate(1);
      endDate.setMonth(endDate.getMonth() + 1);
      endDate.setDate(0);
   }
   
   else {
      endDate.setDate(endDate.getDate() + 6 - endDate.getDay());
      startDate.setDate(startDate.getDate() - startDate.getDay());
   }

   sendRequest("POST", url + "index.php/manager/getGraphs", {
      start : startDate.toDateString(),
      end : endDate.toDateString(),
      view : view.name
   }, 
   function(msg) {
      msg = jQuery.parseJSON(msg);
      var data = msg[0];
      var spark_obj = msg[1];
      var data_two = msg[2];
      var spark_obj_two  = msg[3];
      if(spark_obj["type"] == "pie")
      {
         $("#sparkline-title").html("<h5>Employee Pie Chart</h5>");
         spark_obj["tooltipFormatter"] = function(sparkline, options, fields) {
            switch (fields.offset)
            {
               case 0 : return fields.value + " Met Desired Hours";
               case 1 : return fields.value + " Under Desired Hours";
               case 2 : return fields.value + " Over Desired Hours";
               default: return fields.value + " Whoops... an Error Occurred";
            }
         };
      }
      else {
         $("#sparkline-title").html("<h5>Employees Per Hour</h5>");
         spark_obj["tooltipFormatter"] = function(sparkline, options, fields) {
            var time = (fields.x > 6) ? fields.x - 6 + ":00pm" : (fields.x == 6) ? "12:00pm" : fields.x + 6 + ":00am";
            return time + "<br>" + "Employees:&nbsp" + fields.y + "&nbsp&nbsp&nbsp";
         };
      }

      $("#sparkline").sparkline(data, spark_obj);

      var data_arr = [];
      var key_arr = [];
      for(var key in data_two) {
         if(data_two.hasOwnProperty(key)) {
            data_arr.push(data_two[key]);
            key_arr.push(key);
         }
      }

      spark_obj_two["tooltipFormatter"] = function(sparkline, options, fields) {

         return key_arr[fields[0].offset] + "&nbsp" + fields[0].value + "&nbsp&nbsp&nbsp";
      };

      $("#sparkline-2").sparkline(data_arr, spark_obj_two);

   }, false);
}

function initGraphs (element) {
   showLeftMenuItem("graphs", element);
   updateGraphs();
}
function initStatistics (element) {
   showLeftMenuItem("statistics", element);
   updateStatistics();
}

function getEmployeeObj() {
   return JSON.stringify(global_employee_obj);
}

function getOptionsObj() {
   return JSON.stringify(global_options_obj);
}
function numberEmployeesOn () {
   var number = 0;
   for(var employee_obj in global_employee_obj) {
      if(global_employee_obj.hasOwnProperty(employee_obj)) {
         if(global_employee_obj[employee_obj]["available"] == true) number+=1;
         if(global_employee_obj[employee_obj]["busy"]      == true) number+=1;
         if(global_employee_obj[employee_obj]["scheduled"] == true) number+=1;
      }
   }
   return number;
}
function removeAllEmployees () {
   $("input[type=checkbox]").prop("checked", false).prop("indeterminate", false);

   for(var employee_obj in global_employee_obj) {
      if(global_employee_obj.hasOwnProperty(employee_obj)) {
         global_employee_obj[employee_obj]["available"] = false;
         global_employee_obj[employee_obj]["busy"]      = false;
         global_employee_obj[employee_obj]["scheduled"] = false;
      }
   }
   for(var group_obj in global_groups_obj) {
      if(global_groups_obj.hasOwnProperty(group_obj)) {
         global_groups_obj[group_obj]["available"] = false;
         global_groups_obj[group_obj]["busy"]      = false;
         global_groups_obj[group_obj]["scheduled"] = false;     
      }
   }
   global_options_obj["events"] = false;
   $("#calendar").fullCalendar("removeEvents", function(e) { return true });
}