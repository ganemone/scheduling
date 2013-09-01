function setDate(start)
{
   selectedDate = new Date(start);
}

function incrementDate()
{
   selectedDate = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate() + 1, 0, 0, 0);
   $('#calendar').fullCalendar('select', selectedDate, selectedDate, 'true');
}

function customEvent(date, increment)
{
   var html = "<div>" + $("#customTimes").html() + "</div>";

   var state =
   {
      title : "Custom Event",
      buttons :
      {
         "Submit" : 1,
         "Cancel" : 2
      },
      submit : function(e, v, m, f)
      {
         if (v == 1)
         {
            var start = f.start;
            var end = f.end;
            var startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - 1, start[0], start[1], start[2]);
            var endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - 1, end[0], end[1], end[2]);
            if (endDate <= startDate)
               alert("The end of your availability must come after the start");
            else
               updateEvent("Custom", date, false, start, end);
            if (increment === true)
               incrementDate();
         }
         return true;
      }
   };
   $.prompt(html, state);
}

function initializeForm(start, end)
{
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
   var start_minute = (Number(start_split[1]) > 0) ? Number(start_split[1])/15 : 0;

   var end_hour = Number(end_split[0]);
   var end_minute =  (Number(end_split[1]) > 0) ? Number(end_split[1])/15 : 0;
   var rep_number = (end_hour-start_hour)*4 - start_minute + end_minute;

   for(var i = 0; i < rep_number; i++)
   {
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

/*
   $("#start").children("option").each(function()
   {
      $(this).removeAttr('disabled');
      $(this).removeAttr('selected');
      if ($(this).val() < start)
      {
         $(this).attr("disabled", true);
      }
      else if ($(this).val() == start)
      {
         $(this).attr("selected", true);
      }
      else
         $(this).removeAttr("disabled");
   });
   $("#end").children("option").each(function()
   {
      $(this).removeAttr('disabled');
      $(this).removeAttr('selected');
      if ($(this).val() > end)
         $(this).attr("disabled", true);
      else if ($(this).val() == end)
         $(this).attr("selected", true);
      else
         $(this).removeAttr("disabled");
   });*/
}

function showClass(className)
{
   $(className).each(function()
   {
      $(this).show();
   });
}

function hideClass(className)
{
   $(className).each(function()
   {
      $(this).hide();
   });
}

function updateEvent(title, date, allDay, start, end)
{
   $(".fc-event").tooltip("hide");
   if (start > end)
   {
      alert("The end time must come before the start");
      return;
   }
   var month = date.getMonth() + 1;
   var _date = date.getDate();
   var year = date.getFullYear();
   var formattedDate = year + '-' + month + '-' + _date;
   $("#start").each(function()
   {
      $(this).removeAttr('disabled');
   });
   $("#end").each(function()
   {
      $(this).removeAttr('disabled');
   });
   sendRequest("POST", url + "index.php/user/updateHourAction", 
   {
      employeeId : employeeId,
      day : formattedDate,
      available : title,
      begin : start,
      end : end   
   },
   function(msg)
   {
      $("#calendar").fullCalendar("removeEvents", msg);
      $("#calendar").fullCalendar("refetchEvents");
   },
   true);
   /*
   $.ajax(
   {

      type : "POST",
      url : url + "index.php/user/updateHourAction",
      data :
      {
         employeeId : employeeId,
         day : formattedDate,
         available : title,
         begin : start,
         end : end
      },
      success : function(msg)
      {
         $("#calendar").fullCalendar("removeEvents", msg);
         $("#calendar").fullCalendar("refetchEvents");
      },
      error : function(msg, textStatus, errorThrown)
      {
         error_handler(textStatus, errorThrown, "updateHourAction");
         $("#calendar").fullCalendar('refetchEvents');
      }
   });*/
}

function pickUpEmptyShift(event, start, end)
{
   start = ( typeof start == "undefined") ? event.start.toTimeString().split(" ")[0] : start;
   end = ( typeof end == "undefined") ? event.end.toTimeString().split(" ")[0] : end;
   
   sendRequest("GET", url + "index.php/user/pickUpEmptyShift", 
   {
      employeeId : employeeId,
      start : start,
      end : end,
      date : event.start.toDateString(),
      position : event.position,
      sfl : event.sfl,
      shiftId : event.shiftId
   },
   function(msg)
   {
      msg = jQuery.parseJSON(msg);
      if (msg[0] == "false")
         alert(msg[1]);
      else
         $("#calendar").fullCalendar("refetchEvents");
   },
   true);
   /*
   $.ajax(
   {
      type : "GET",
      url : url + "index.php/user/pickUpEmptyShift",
      data :
      {
         employeeId : employeeId,
         start : start,
         end : end,
         date : event.start.toDateString(),
         position : event.position,
         sfl : event.sfl,
         shiftId : event.shiftId
      },
      success : function(msg)
      {
         msg = jQuery.parseJSON(msg);
         if (msg[0] == "false")
            alert(msg[1]);
         else
            $("#calendar").fullCalendar("refetchEvents");
      },
      error : function(msg, textStatus, errorThrown)
      {
         error_handler(textStatus, errorThrown, "pickUpEmptyShift");
      }
   });
   */
}

function deletePost(id)
{
   setTimeout(function()
   {
      $(".jqibox").css("height", $(document).height());
      $(".jqifade").css("height", $(document).height());
   }, 20);
   $.prompt("Are you sure you would like to delete this post?",
   {
      title : "Confirmation",
      buttons :
      {
         "Yes" : 1,
         "Cancel" : 2
      },
      submit : function(e, v, m, f)
      {
         if (v == 1)
         {
            $.ajax(
            {
               type : "GET",
               url : url + "index.php/news/deletePost",
               data :
               {
                  messageId : id
               },
               success : function(msg)
               {
                  $("#message" + id).hide();
                  successMessage("Message Deleted");
               },
               error : function(msg, textStatus, errorThrown)
               {
                  error_handler(textStatus, errorThrown, "deletePost errorThrown");
               }
            });
         }
         return true;
      }
   });
}

function updatePost(id)
{
   var text = $("#message" + id + " textarea").val();
   $.ajax(
   {
      type : "GET",
      url : url + "index.php/news/updateNewsfeedPost",
      data :
      {
         employeeId : employeeId,
         messageId : id,
         message : text
      },
      success : function(msg)
      {
         if (msg == "false")
            errorMessage("Whoops, an error Occurred... Please try again or go get G.");
         else
            successMessage("Successfully updated this post.");
      },
      error : function(msg, textStatus, errorThrown)
      {
         error_handler(textStatus, errorthrown, "updateNewsfeedPost");
      }
   });
}

function addNewPost()
{
   var value = $("#newPostTextArea").val();
   $.ajax(
   {
      type : "GET",
      url : url + "index.php/news/addNewsfeedPost",
      data :
      {
         employeeId : employeeId,
         message : value
      },
      success : function(msg)
      {
         reloadNewsfeed();
      },
      error : function(msg, textStatus, errorThrown)
      {
         error_handler(textStatus, errorThrown, "addNewsfeedPost");
      }
   });
}

function reloadNewsfeed()
{
   $.ajax(
   {
      type : "GET",
      url : url + "index.php/news/reloadNewsfeed",
      success : function(msg)
      {
         $("#newsfeed").replaceWith(msg);
      },
      error : function(msg, textStatus, errorThrown)
      {
         error_handler(textStatus, errorThrown, "reloadNewsfeed");
      }
   });
}
function getStartAndEndDates(view, selectedDate)
{
   var ret = '';
   var start, end;
   if (view == 'agendaWeek' || view == 'basicWeek')
   {
      start = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate() - selectedDate.getDay());
      end = new Date(start.getFullYear(), start.getMonth(), start.getDate() + 6);
      ret =
      {
         startDate : start,
         endDate : end
      };
   }
   else if (view == 'month')
   {
      start = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), 1);
      end = new Date(start.getFullYear(), start.getMonth() + 1, 0);
      ret =
      {
         startDate : start,
         endDate : end
      };
   }
   else if (view == 'agendaDay' || view == 'basicDay')
   {
      ret =
      {
         startDate : selectedDate,
         endDate : selectedDate
      };
   }
   return ret;
}
function copyWeek()
{
   var date = $("#calendar").fullCalendar("getDate");
   var date_obj = getStartAndEndDates("agendaWeek", date);
   if(availability == true)
   {
      var events = $("#calendar").fullCalendar("clientEvents", function(event)
      {
         if(event.start < date_obj.startDate || event.start > date_obj.endDate || event.category != "availability")
            return false;
         return true;
      });
      clipboard = events;
      if(clipboard.length > 0)
         successMessage("Copied Week");
      else {
         errorMessage("There are no events here to copy");
         clipboard = null;
      }
   }
   else {
      errorMessage("Availability events must be on to copy the week");
   }
}
function pasteWeek()
{
   var date = $("#calendar").fullCalendar("getDate");
   var date_obj = getStartAndEndDates("agendaWeek", date);
   var day, start, end, title;
   if(clipboard === null)
   {
      errorMessage("You must copy a week first! In the week view, click the 'Copy Week' button in the vertical menu on the left side of the screen");
      return false;
   }
   var event_obj_arr = new Array();
   $.each(clipboard, function(key, val)
   {
      var end = (val.end === null) ? null : val.end.toTimeString().split(" ")[0];
      var title = (val.allDay === true) ? val.title : "Custom";

      var event_obj = {
         day: val.start.getDay(),
         start: val.start.toTimeString().split(" ")[0],
         end: end,
         available: title,
         employeeId: employeeId
      };

      event_obj_arr.push(event_obj);

   });
   $.ajax({
      type: "POST",
      url: url + "index.php/user/pasteWeek",
      data: {
         week_start: date_obj.startDate,
         week_end: date_obj.endDate,
         week: JSON.stringify(event_obj_arr)
      },
      success: function(msg)
      {
         console.log(msg);
         successMessage("Pasted Week");
         $("#calendar").fullCalendar("refetchEvents");
      },
      error: function(msg, textStatus, errorThrown)
      {
         console.log(msg);
         errorMessage("An error occurred");
      }
   });
   /*$.each(clipboard, function(key, val) {
      day = val.start.getDay();
      start = new Date(date.getFullYear(), date.getMonth(), date_obj.startDate.getDate() + day, val.start.getHours(), val.start.getMinutes());
      end = start;
      title = (val.allDay === false) ? "Custom": val.title;
      if(val.allDay === false)
         end = new Date(date.getFullYear(), date.getMonth(), date_obj.startDate.getDate() + day, val.end.getHours(), val.end.getMinutes());
      updateEvent(title, start, val.allDay, start.toTimeString().split(" ")[0], end.toTimeString().split(" ")[0]);
   });*/
}

function validateDownloadForm(f)
{
   if(!f.start)
      return new Array(false, "The Start Date Field is Required");
   if(!f.end)
      return new Array(false, "The End Date Field is Required");
   var start_arr, end_arr;
   start_arr = f.start.split("-");
   end_arr = f.end.split("-");
   var reg = new RegExp('[^0-9\-]');
   if(reg.test(f.start))
      return new Array(false, "Please do not include any characters other than numbers and dashes in the start date.");
   if(reg.test(f.end))
      return new Array(false, "Please do not include any characters other than numbers and dashes in the end date.");
   if(start_arr.length != 3 || start_arr[0].length != 4 || start_arr[1].length != 2 || start_arr[2].length != 2)
      return new Array(false, "Please enter the start date with the following format: YYYY-MM-DD");
   if(end_arr.length != 3 || end_arr[0].length != 4 || end_arr[1].length != 2 || end_arr[2].length != 2)
      return new Array(false, "Please enter the end date with the following format: YYYY-MM-DD");
   if(start_arr[1] > 12 || start_arr[2] > 31)
      return new Array(false, "Please enter a valid start date.");
   if(end_arr[1] > 12 || end_arr[1] > 31)
      return new Array(false, "Please enter a valid end date.");
   if(end_arr[0] < start_arr[0] || end_arr[1] < start_arr[1] || (end_arr[0] == start_arr[0] && end_arr[1] == start_arr[1] && end_arr[2] < start_arr[2]))
      return new Array(false, "The end date must come after the start date");
   return new Array(true);
}

function validateEmpty(variable)
{
   if(variable === null || variable === false || variable === "")
      return false;
   return true;
}

function validateDate(variable)
{
   var reg = new RegExp('[^0-9\-]');
   var split = variable.split("-");
   if (reg.test(variable)             || 
      split.length != 3               || 
      variable.length != 10           ||
      split[0].length != 4            || 
      split[1].length != 2            || 
      split[2].length != 2            ||
      (split[1] > 12 || split[1] < 1) ||
      (split[2] > 31 || split[2] < 1))
      return false;
   return true;
}

function validateStartEndDates(start, end)
{
   var start_split, end_split;
   if (validateDate(start) === false || validateDate(end) === false)
   {
      return new Array(false, "Please enter valid dates");
   }
   start_split = start.split("-");
   end_split = end.split("-");
   if(start_split[0] > end_split[0] ||
      start_split[1] > end_split[1] ||
      start_split[1] == end_split[1] && start_split[2] > end_split[2])
      return new Array(false, "The start date must come after the end date");
   return new Array(true);
}

function isLockedOut(date)
{
   var editableDate = new Date();
   editableDate.setDate(1);
   editableDate.setMonth(editableDate.getMonth() + 0);
   return (date < editableDate) ? true : false;
}

function updateInfo()
{
   var monthInfoForm = "<div id='monthInfo'>" +
      "<form class='form-inline'>" + 
         "<fieldset>" + 
         "<div class='form-group text-left'>" + 
            "<label for='minHours'>Min Weekly Hours: </label>" + 
            "<input type='text' name='min' class='form-control' id='minHours' value='" + monthInfo.minHours + "'>" +
         "</div>" +
         "<div class='form-group text-left'>" +
            "<label for='maxHours'>Max Weekly Hours: </label>" + 
            "<input type='text' name='max' class='form-control' id='maxHours' value='" + monthInfo.maxHours + "'>" +
         "</div>" +
         "<div class='form-group text-left'>" +
            "<textarea cols='40' rows='5' name='notes' id='notes' placeholder='Enter Notes for this month here...'>" + monthInfo.notes + "</textarea>" +
         "</div>" +
         "</fieldset>" + 
      "</form>" + 
   "</div>";

   bootbox.confirm(monthInfoForm, function(result)
   {
      if (result === true)
      {
         $.ajax({
            type: "POST",
            url: url + "index.php/user/updateMonthInfo",
            data: {
               date: $("#calendar").fullCalendar("getDate"),
               employeeId: employeeId,
               min: $("#minHours").val(),
               max: $("#maxHours").val(),
               notes: $("#notes").val()
            },
            success: function(msg)
            {
               successMessage("Your information has been updated");
            },
            error: function(msg, textStatus, errorThrown)
            {
               error_handler(textStatus, errorThrown, "pickUpEmptyShift");
            }
         });
      }
   });

}

function partialShiftPickupDialog(event, employeeId, form)
{
   bootbox.confirm(form, function(result)
   {
      if (result === true)
      {
         pickUpShift($("#start").val(), $("#end").val(), employeeId, event.employeeId, event.id);
      }
   });
}

function pickUpShift(start, end, employeeId, oldEmployeeId, shiftId)
{
   if (start > end)
   {
      alert("The end time must come before the start");
      return;
   }
   if (start === null || end === null)
   {
      $.ajax(
      {
         type : "POST",
         url : url + "index.php/user/shiftSwap",
         data :
         {
            employeeId : employeeId,
            originalEmployeeId : oldEmployeeId,
            eventId : shiftId
         },
         success : function(msg)
         {
            msg = jQuery.parseJSON(msg);
            if(msg[0] == "false")
               errorMessage(msg[1]);
            else
            {
               $("#calendar").fullCalendar("refetchEvents");
               successMessage("You successfully picked up this shift.");
            }
         },
         error : function(msg, textStatus, errorThrown)
         {
            error_handler(textStatus, errorThrown, "shiftSwap");
         }
      });
   }
   else
   {
      $.ajax(
      {
         type : "POST",
         url : url + "index.php/user/partialShiftSwap",
         data :
         {
            start : start,
            end : end,
            employeeId : employeeId,
            originalEmployeeId : oldEmployeeId,
            eventId : shiftId
         },
         success : function(msg)
         {
            msg = jQuery.parseJSON(msg);
            if (msg[0] == "false")
               errorMessage(msg[1]);
            else {
               successMessage("You successfully picked up part of this shift.");
               $("#calendar").fullCalendar("refetchEvents");
            }
         },
         error : function(msg, textStatus, errorThrown)
         {
            error_handler(textStatus, errorThrown, "partialShiftSwap");
         }
      });
   }
}

function fullShiftCoverRequest(event)
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
            errorMessage("This shift is already up for cover");
         else
         {
            $("#calendar").fullCalendar('refetchEvents');
            successMessage("You have successfully put this shift up for cover");
         }
      },
      error : function(msg, textStatus, errorThrown)
      {
         error_handler(textStatus, errorThrown, "scheduleRequest");
      }
   });
}

function partialShiftCoverRequest(event) 
{
   var start, end, shiftId, form;
   start = event.start.toTimeString().split(" ")[0];
   end = event.end.toTimeString().split(" ")[0];
   form = initializeForm(start, end);
   
   bootbox.confirm(form, function(result)
   {
      if (result === true)
      {
         start = $("#start").val();
         end = $("#end").val();
         shiftId = event.id;

         if (start > end)
         {
            errorMessage("The start must come before the end");
            return false;
         }
         $.ajax(
         {
            type : "POST",
            url : url + "index.php/user/requestPartialShiftCover",
            data :
            {
               requestStart : start,
               requestEnd : end,
               employeeId : employeeId,
               shiftId : shiftId
            },
            success : function(msg)
            {
               successMessage("Your shift is up for cover.");
               $('#calendar').fullCalendar('refetchEvents');
            },
            error : function(msg, textStatus, errorThrown)
            {
               error_handler(textStatus, errorThrown, "requestPartialShiftCover");
            }
         });
      }
   })
}