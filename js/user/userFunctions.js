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
   var form = "<form id='customTimes'>Start:<select id='start'>";
   var options = "";
   var start_split;
   var time_arr = [];
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

   while(start <= end)
   {
      options += "<option value='" + start + "'>" + time_arr[start] + "</option>";
      start_split = start.split(":");
      if (start_split[1] == "45")
         start = (Number(start_split[0]) + 1) + ":00:00";
      else
         start = start_split[0] + ":" + (Number(start_split[1]) + 15) + ":" + start_split[2];
   }

   form += options + "</select>" + "End<select id='end'>" + options + "</select></form>";
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

function requestPartialShiftCover(start, end, employeeId, shiftId)
{
   if (start > end)
   {
      alert("The end time must come before the start");
      return;
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
         $('#calendar').fullCalendar('refetchEvents');
      },
      error : function(msg, textStatus, errorThrown)
      {
         alert(errorThrown);
      }
   });
}

function updateEvent(title, date, allDay, start, end)
{
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
         alert(errorThrown);
         $("#calendar").fullCalendar('refetchEvents');
      }
   });
}

function pickUpEmptyShift(event, start, end)
{
   start = ( typeof start == "undefined") ? event.start.toTimeString().split(" ")[0] : start;
   end = ( typeof end == "undefined") ? event.end.toTimeString().split(" ")[0] : end;
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
         error_handler(msg, textStatus, errorThrown, "pickUpEmptyShift");
      }
   });
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
               },
               error : function(msg, textStatus, errorThrown)
               {
                  alert("deletePost errorThrown");
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
            alert("Whoops, an error occured");
         else
            alert("You have successfully updated this post");
      },
      error : function(msg, textStatus, errorThrown)
      {
         alert("updateNewsfeedPost " + errorThrown);
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
         alert("addNewsfeedPost " + errorThrown);
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
         alert("reloadNewsfeed " + errorThrown);
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
   if(availability === true)
   {
      var events = $("#calendar").fullCalendar("clientEvents", function(event)
      {
         if(event.start < date_obj.startDate || event.start > date_obj.endDate || event.category != "availability")
            return false;
         return true;
      });
      clipboard = events;
   }
}
function pasteWeek()
{
   var date = $("#calendar").fullCalendar("getDate");
   var date_obj = getStartAndEndDates("agendaWeek", date);
   var day, start, end, title;
   if(clipboard === null)
   {
      alert("You must copy a week first! In the week view, click the 'Copy Week' button in the vertical menu on the left side of the screen");
      return false;
   }
   $.each(clipboard, function(key, val) {
      day = val.start.getDay();
      start = new Date(date.getFullYear(), date.getMonth(), date_obj.startDate.getDate() + day, val.start.getHours(), val.start.getMinutes());
      end = start;
      title = (val.allDay === false) ? "Custom": val.title;
      if(val.allDay === false)
         end = new Date(date.getFullYear(), date.getMonth(), date_obj.startDate.getDate() + day, val.end.getHours(), val.end.getMinutes());
      updateEvent(title, start, val.allDay, start.toTimeString().split(" ")[0], end.toTimeString().split(" ")[0]);
   });
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
               error_handler(msg, textStatus, errorThrown, "pickUpEmptyShift");
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
               alert(msg[1]);
            else
               $("#calendar").fullCalendar("refetchEvents");
         },
         error : function(msg, textStatus, errorThrown)
         {
            alert(errorThrown);
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
               alert(msg[1]);
            else
               $("#calendar").fullCalendar("refetchEvents");
         },
         error : function(msg, textStatus, errorThrown)
         {
            alert(errorThrown);
         }
      });
   }
}

function shiftCoverRequest(event)
{

}

function showLeftMenuItem(show_element_id, nav_element)
{
   $(".leftMenu").each(function()
   {
      if ($(this).attr("id") != show_element_id)
         $(this).hide();
   });
   $("#" + show_element_id).toggle();
   $(".nav-pills").children("li").each(function()
   {
      $(this).removeClass("active");
   });
   $(nav_element).addClass("active");

   if (show_element_id == "newsfeed")
      $('.leftNav').css("overflow", "scroll");
   else
      $('.leftNav').css("overflow", "visible");
}

function error_handler(error_part, error_part2, error_part3, origin)
{
   $(".bottom-right").notify({
      type: "error",
      message: { text: "Oops, something went wrong... Quick go get G! (ps, he is sorry in advance)" },
   }).show();

   $.ajax({
      type: "POST",
      url: url + "index.php/user/error_handler",
      data: {
         message: error_part + " " + error_part2 + " " + error_part3 + " " + origin + " " + employeeId
      },
      success: function(msg)
      {
         alert(msg);
      },
      error: function() 
      {}
   });
}

function successMessage(msg)
{
   $('.bottom-right').notify({
      type: "success",
      message: { text: msg },
      fadeOut: { enabled: true, delay: 3000 }
   }).show();
}
