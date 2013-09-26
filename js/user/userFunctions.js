if(resize == true)
{
   resizeCalendar();
   $(window).resize(function() {
      resizeCalendar();
   });
}
function cal_select (start, end, allDay, jsEvent, view) {
   if(allDay === true || view.name == 'month') {
      setDate(start);
   }
   else {
      alert("doing stuff here...");
      updateEvent("Custom", start, false, start.toTimeString().split(" ")[0], end.toTimeString().split(" ")[0]);
      $("#calendar").fullCalendar('unselect');
   }
}
function cal_eventRender (event, element, view) {
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
}
function cal_viewRender (view) {
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
}
function cal_eventClick (event, jsEvent, view) {
   var start, end, states;
   if (event.category == 'scheduled-cover' || event.category == 'scheduled-pickup' && coverRequest === false || event.category == 'emptyShifts')
   {
      start = event.start.toTimeString().split(" ")[0];
      end = event.end.toTimeString().split(" ")[0];
      var form = initializeForm(start, end);

      bootbox.dialog({
         message : event.description, 
         title : "Shift Cover",
         buttons : {
            partial : {
               label : "Partial Shift",
               className : "btn-primary",
               callback : function()
               {
                  partialShiftPickupDialog(event, employeeId, form);
               }
            },
            entire : {
               label : "Entire Shift",
               className : "btn-primary",
               callback : function()
               {
                  pickUpShift(null, null, employeeId, event.employeeId, event.id);
               }
            }
         }
      });   
   }
   if (coverRequest === true && event.category == 'scheduled')
   {
      coverRequest = false;

      cancelCoverRequest();

      bootbox.dialog({
         message : "Are you sure you would like to request a cover for this shift? (Note: the shift will remain on your schedule, and you will still be responsible for it until someone claims it). ",
         title : "Shift Cover",
         buttons : {
            entire : {
               label: "Full Shift",
               className : "btn-primary",
               callback : function() {
                  fullShiftCoverRequest(event);
               }
            },
            partial : {
               label: "Partial Shift",
               className : "btn-primary",
               callback: function() {
                  partialShiftCoverRequest(event);
               }
            },
            cancel : {
               label: "Cancel",
               className : "btn-danger"
            }
         }
      });
   }
}
function setDate(start) {
   selectedDate = new Date(start);
}

function incrementDate() {
   selectedDate = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate() + 1, 0, 0, 0);
   $('#calendar').fullCalendar('select', selectedDate, selectedDate, true);
}
function resizeCalendar () {
   var calendar_width = ($(".leftNavOuter").position().left == 0) ? 200 : 15;
   
   $("#calendar").css("width", $(document).width() - calendar_width);

   $(".leftNav").css("height", $(window).height() - 100);
}

function customEvent(date, increment) {
   form_obj = {
      "name"     : "custom_form",
      "id"       : "custom_form",
      "style"    : "width: 400px;",
      "elements" : []
   };

   var start = (date.getDay() == 6) ? "11:45:00" : "09:15:00";
   var end = (date.getDay() == 5) ? "18:30:00" : (date.getDay() == 6) ? "17:30:00" : "20:30:00";

   buildStartEndInputs(form_obj, start, end, "06:00:00", "21:00:00");
   bootbox.confirm(buildForm(form_obj), function(result) {
      if(result) {
         if(validateStartEndTimes($("#start_time").val(), $("#end_time").val()) === false) {
            alert("The start time must come before the end time.");
            return false;
         }
         updateEvent("Custom", date, false, $("#start_time").val(), $("#end_time").val());
         if (increment === true) {
            incrementDate();
         }
      }
   return true;
   });
}

function initializeForm(start, end) {
   var form = "<form id='customTimes'>Start: <select id='start'>";
   var options = "";
   var time_arr = [];
   time_arr['06:00:00'] = "6:00am";
   time_arr['06:15:00'] = "6:15am";
   time_arr['06:30:00'] = "6:30am";
   time_arr['06:45:00'] = "6:45am";
   time_arr['07:00:00'] = "7:00am";
   time_arr['07:15:00'] = "7:15am";
   time_arr['07:30:00'] = "7:30am";
   time_arr['07:45:00'] = "7:45am";
   time_arr['08:00:00'] = "8:00am";
   time_arr['08:15:00'] = "8:15am";
   time_arr['08:30:00'] = "8:30am";
   time_arr['08:45:00'] = "8:45am";
   time_arr['09:00:00'] = "9:00am";
   time_arr['09:15:00'] = "9:15am";
   time_arr['09:30:00'] = "9:30am";
   time_arr['09:45:00'] = "9:45am";
   time_arr['10:00:00'] = "10:00am";
   time_arr['10:15:00'] = "10:15am";
   time_arr['10:30:00'] = "10:30am";
   time_arr['10:45:00'] = "10:45am";
   time_arr['11:00:00'] = "11:00am";
   time_arr['11:15:00'] = "11:15am";
   time_arr['11:30:00'] = "11:30am";
   time_arr['11:45:00'] = "11:45am";
   time_arr['12:00:00'] = "12:00pm";
   time_arr['12:15:00'] = "12:15pm";
   time_arr['12:30:00'] = "12:30pm";
   time_arr['12:45:00'] = "12:45pm";
   time_arr['13:00:00'] = "1:00pm";
   time_arr['13:15:00'] = "1:15pm";
   time_arr['13:30:00'] = "1:30pm";
   time_arr['13:45:00'] = "1:45pm";
   time_arr['14:00:00'] = "2:00pm";
   time_arr['14:15:00'] = "2:15pm";
   time_arr['14:30:00'] = "2:30pm";
   time_arr['14:45:00'] = "2:45pm";
   time_arr['15:00:00'] = "3:00pm";
   time_arr['15:15:00'] = "3:15pm";
   time_arr['15:30:00'] = "3:30pm";
   time_arr['15:45:00'] = "3:45pm";
   time_arr['16:00:00'] = "4:00pm";
   time_arr['16:15:00'] = "4:15pm";
   time_arr['16:30:00'] = "4:30pm";
   time_arr['16:45:00'] = "4:45pm";
   time_arr['17:00:00'] = "5:00pm";
   time_arr['17:15:00'] = "5:15pm";
   time_arr['17:30:00'] = "5:30pm";
   time_arr['17:45:00'] = "5:45pm";
   time_arr['18:00:00'] = "6:00pm";
   time_arr['18:15:00'] = "6:15pm";
   time_arr['18:30:00'] = "6:30pm";
   time_arr['18:45:00'] = "6:45pm";
   time_arr['19:00:00'] = "7:00pm";
   time_arr['19:15:00'] = "7:15pm";
   time_arr['19:30:00'] = "7:30pm";
   time_arr['19:45:00'] = "7:45pm";
   time_arr['20:00:00'] = "8:00pm";
   time_arr['20:15:00'] = "8:15pm";
   time_arr['20:30:00'] = "8:30pm";
   time_arr['20:45:00'] = "8:45pm";
   time_arr['21:00:00'] = "9:00pm";

   var start_split = start.split(":");
   var end_split = end.split(":");

   var start_hour = Number(start_split[0]);
   var start_minute = (Number(start_split[1]) > 0) ? Number(start_split[1]) / 15 : 0;

   var end_hour = Number(end_split[0]);
   var end_minute = (Number(end_split[1]) > 0) ? Number(end_split[1]) / 15 : 0;
   var rep_number = (end_hour - start_hour) * 4 - start_minute + end_minute;

   for (var i = 0; i < rep_number; i++) {
      options += "<option value='" + start + "'>" + time_arr[start] + "</option>";
      start_split = start.split(":");
      if (start_split[1] == "45")
         start = (Number(start_split[0]) + 1) + ":00:00";
      else
         start = start_split[0] + ":" + (Number(start_split[1]) + 15) + ":" + start_split[2];
      if (start.length < 8)
         start = "0" + start;
   }
   form += options + "</select>" + "<br>End: <select id='end'>" + options + "</select></form>";
   return form;
}

function showClass(className) {
   $(className).each(function() {
      $(this).show();
   });
}

function hideClass(className) {
   $(className).each(function() {
      $(this).hide();
   });
}

function updateEvent(title, date, allDay, start, end) {
   $(".fc-event").tooltip("hide");
   if (start > end) {
      alert("The end time must come before the start");
      return;
   }
   var month = date.getMonth() + 1;
   var _date = date.getDate();
   var year = date.getFullYear();
   var formattedDate = year + '-' + month + '-' + _date;

   sendRequest("POST", url + "index.php/user/updateHourAction", {
      employeeId : employeeId,
      day : formattedDate,
      available : title,
      begin : start,
      end : end
   }, 
   function(msg) {
      var result_arr = jQuery.parseJSON(msg);
      if(result_arr[0] > 0) {
         $("#calendar").fullCalendar("removeEvents", result_arr[0]);
      }
      $("#calendar").fullCalendar("renderEvent", result_arr[1]);

   }, true);
}

function pickUpEmptyShift(event, start, end) {
   start = ( typeof start == "undefined") ? event.start.toTimeString().split(" ")[0] : start;
   end = ( typeof end == "undefined") ? event.end.toTimeString().split(" ")[0] : end;

   sendRequest("GET", url + "index.php/user/pickUpEmptyShift", {
      employeeId : employeeId, start : start, end : end, date : event.start.toDateString(), position : event.position, sfl : event.sfl, shiftId : event.shiftId
   }, function(msg) {
      msg = jQuery.parseJSON(msg);
      if (msg[0] == "false")
            alert(msg[1]);
      else
            $("#calendar").fullCalendar("refetchEvents");
   }, true);
}

function deletePost(id) {
   bootbox.confirm("Are you sure you would like to delete this post?", function(result) {
      sendRequest("GET", url + "index.php/news/deletePost", {
         messageId : id
      }, 
      function(msg) {
         $("#message" + id).hide();
         successMessage("Message Deleted");
      }, false);
   });
}

function addNewPost() {
   var value = $("#newPostTextArea").val();
   sendRequest("GET", url + "index.php/news/addNewsfeedPost", {
      employeeId : employeeId, 
      message : value
   }, function(msg) {
      reloadNewsfeed();
      successMessage("Added new post");
   }, false);
}

function reloadNewsfeed() {
   sendRequest("GET", url + "index.php/news/reloadNewsfeed", {}, 
   function(msg) {
      $("#newsfeed").replaceWith(msg);
   }, false);
}

function getStartAndEndDates(view, selectedDate) {
   var ret = '';
   var start, end;
   if (view == 'agendaWeek' || view == 'basicWeek') {
      start = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate() - selectedDate.getDay());
      end = new Date(start.getFullYear(), start.getMonth(), start.getDate() + 6);
      ret = {
         startDate : start, endDate : end
      };
   }
   else if (view == 'month') {
      start = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), 1);
      end = new Date(start.getFullYear(), start.getMonth() + 1, 0);
      ret = {
         startDate : start, endDate : end
      };
   }
   else if (view == 'agendaDay' || view == 'basicDay') {
      ret = {
         startDate : selectedDate, endDate : selectedDate
      };
   }
   return ret;
}

function copyWeek() {
   var date = $("#calendar").fullCalendar("getDate");
   var date_obj = getStartAndEndDates("agendaWeek", date);
   if (availability == true) {
      var events = $("#calendar").fullCalendar("clientEvents", function(event) {
         if (event.start < date_obj.startDate || event.start > date_obj.endDate || event.category != "availability")
            return false;
         return true;
      });
      clipboard = events;
      if (clipboard.length > 0) {
            successMessage("Copied Week");
      }
      else {
         errorMessage("There are no events here to copy");
         clipboard = null;
      }
   }
   else {
          errorMessage("Availability events must be on to copy the week");
   }
}

function pasteWeek() {
   var date = $("#calendar").fullCalendar("getDate");
   var date_obj = getStartAndEndDates("agendaWeek", date);
   var day, start, end, title;
   if (clipboard === null) {
      errorMessage("You must copy a week first! In the week view, click the 'Copy Week' button in the vertical menu on the left side of the screen");
      return false;
   }
   var event_obj_arr = new Array();
   $.each(clipboard, function(key, val) {
      var end = (val.end === null) ? null : val.end.toTimeString().split(" ")[0];
      var title = (val.allDay === true) ? val.title : "Custom";

      var event_obj = {
         day : val.start.getDay(), start : val.start.toTimeString().split(" ")[0], end : end, available : title, employeeId : employeeId
      };

      event_obj_arr.push(event_obj);

   });
   sendRequest("POST",  url + "index.php/user/pasteWeek", {
      week_start : date_obj.startDate,
      week_end : date_obj.endDate,
      week : JSON.stringify(event_obj_arr)
   }, 
   function(msg) {
      $("#calendar").fullCalendar("refetchEvents");
      successMessage("Pasted Week");
   }, false);
}

function validateDownloadForm(f) {
   if (!f.start)
      return new Array(false, "The Start Date Field is Required");
   if (!f.end)
      return new Array(false, "The End Date Field is Required");
   var start_arr, end_arr;
   start_arr = f.start.split("-");
   end_arr = f.end.split("-");
   var reg = new RegExp('[^0-9\-]');
   if (reg.test(f.start))
      return new Array(false, "Please do not include any characters other than numbers and dashes in the start date.");
   if (reg.test(f.end))
      return new Array(false, "Please do not include any characters other than numbers and dashes in the end date.");
   if (start_arr.length != 3 || start_arr[0].length != 4 || start_arr[1].length != 2 || start_arr[2].length != 2)
      return new Array(false, "Please enter the start date with the following format: YYYY-MM-DD");
   if (end_arr.length != 3 || end_arr[0].length != 4 || end_arr[1].length != 2 || end_arr[2].length != 2)
      return new Array(false, "Please enter the end date with the following format: YYYY-MM-DD");
   if (start_arr[1] > 12 || start_arr[2] > 31)
      return new Array(false, "Please enter a valid start date.");
   if (end_arr[1] > 12 || end_arr[1] > 31)
      return new Array(false, "Please enter a valid end date.");
   if (end_arr[0] < start_arr[0] || end_arr[1] < start_arr[1] || (end_arr[0] == start_arr[0] && end_arr[1] == start_arr[1] && end_arr[2] < start_arr[2]))
      return new Array(false, "The end date must come after the start date");
   return new Array(true);
}

function validateEmpty(variable) {
   if (variable === null || variable === false || variable === "")
      return false;
   return true;
}

function validateDate(variable) {
   var reg = new RegExp('[^0-9\-]');
   var split = variable.split("-");
   if (reg.test(variable) || split.length != 3 || variable.length != 10 || split[0].length != 4 || split[1].length != 2 || split[2].length != 2 || (split[1] > 12 || split[1] < 1) || (split[2] > 31 || split[2] < 1))
      return false;
   return true;
}

function validateStartEndDates(start, end) {
   var start_split, end_split;
   if (validateDate(start) === false || validateDate(end) === false) {
      return new Array(false, "Please enter valid dates");
   }
   start_split = start.split("-");
   end_split = end.split("-");
   if (start_split[0] > end_split[0] || start_split[1] > end_split[1] || start_split[1] == end_split[1] && start_split[2] > end_split[2])
      return new Array(false, "The start date must come after the end date");
   return new Array(true);
}

function isLockedOut(date) {
   var editable_date_split = editable_date.split("-");
   var edit_date = new Date(editable_date_split[0], Number(editable_date_split[1]) - 1, Number(editable_date_split[2]) + 1);
   return (date < edit_date) ? true : false;
}

function updateInfo() {
   var date = $("#calendar").fullCalendar("getDate");
   date = date.getFullYear() + "-" + Number(date.getMonth() + 1);
   sendRequest("POST", url + "index.php/user/populateMonthInfoForm", {
      month : date,
      employeeId: employeeId
   }, 
   function(msg) {
      msg = jQuery.parseJSON(msg);

      var form_obj = {
         "name" : "updateInfoForm",
         "id"   : "updateInfoForm",
         "style" : "width: 400px;",
         "elements" : [
         {
            "type" : "hidden",
            "name" : "date",
            "id"   : "date",
            "value" : date
         },
         {
            "type" : "hidden",
            "name" : "employeeId",
            "id"   : "employeeId",
            "value" : employeeId
         },
         {
            "type" : "text",
            "id" : "minHours",
            "name" : "min",
            "label" : "Minimum Hours",
            "value" : msg.minHours,
            "placeholder" : "Enter minimum desired hours here"
         },
         {
            "type" : "text",
            "id" : "maxHours",
            "name" : "max",
            "label" : "Maximum Hours",
            "value" : msg.maxHours,
            "placeholder" : "Enter maximum desired hours here"
         },
         {
            "type" : "textarea",
            "id" : "notes",
            "name" : "notes",
            "value" : msg.notes,
            "placeholder" : "Enter desired notes for this month here.",
         }]
      };
      bootbox.confirm(buildForm(form_obj), function(result) {
      if (result === true) {
         sendRequest("POST", url + "index.php/user/updateMonthInfo", buildPostDataObj("#updateInfoForm"),
         function(msg) {
            successMessage("Your information has been updated");
         }, false);
      }  
   });
   }, false);
}

function partialShiftPickupDialog(event, employeeId, form) {
   bootbox.confirm(form, function(result) {
      if (result === true) {
         pickUpShift($("#start").val(), $("#end").val(), employeeId, event.employeeId, event.id);
      }
   });
}

function pickUpShift(start, end, employeeId, oldEmployeeId, shiftId) {
   if (start > end) {
       alert("The end time must come before the start");
       return;
   }
   if (start === null || end === null) {
      sendRequest("POST", url + "index.php/user/shiftSwap", {
         employeeId : employeeId,
         originalEmployeeId : oldEmployeeId,
         eventId : shiftId
       },
      function(msg) {
         var ret = jQuery.parseJSON(msg);
         if(ret[0] == "false") {
            alert(ret[1]);
         } 
         else {
            $("#calendar").fullCalendar("refetchEvents");
            successMessage("Picked up entire shift.");
         }
      }, false);
   }
   else {
      sendRequest("POST", url + "index.php/user/partialShiftSwap", {
         start : start,
         end : end,
         employeeId : employeeId,
         originalEmployeeId : oldEmployeeId,
         eventId : shiftId
      }, function(msg) {
         $("#calendar").fullCalendar("refetchEvents");
         successMessage("Picked up partial shift.");
      }, false);
   }
}

function fullShiftCoverRequest(event) {
   sendRequest("POST", url + "index.php/user/scheduleRequest", {
      shiftId : event.id
      }, 
      function() {
         $("#calendar").fullCalendar("refetchEvents");
         successMessage("You have successfully put this shift up for cover");
      }, false);
}

function partialShiftCoverRequest(event) {
   var start, end, shiftId, form;
   start = event.start.toTimeString().split(" ")[0];
   end = event.end.toTimeString().split(" ")[0];
   form = initializeForm(start, end);

   bootbox.confirm(form, function(result) {
      if (result === true) {
         start = $("#start").val();
         end = $("#end").val();
         shiftId = event.id;

         if (start > end) {
            alert("The start must come before the end");
            return false;
         }
         sendRequest("POST", url + "index.php/user/requestPartialShiftCover", {
            requestStart : start,
            requestEnd : end,
            employeeId : employeeId,
            shiftId : shiftId
         }, function (msg) {
            $('#calendar').fullCalendar('refetchEvents');
            successMessage("Your shift is up for cover.");
         }, false);
      }
   });
}