function setDate(start)
{
   selectedDate = new Date(start);
}

function incrementDate()
{
   selectedDate = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate() + 1, 0, 0, 0);
   $('#calendar').fullCalendar('select', selectedDate, selectedDate, 'true');
}

function pickUpShift(start, end, employeeId, oldEmployeeId, shiftId)
{
   if (start > end)
   {
      alert("The end time must come before the start");
      return;
   }
   if (start == null || end == null)
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
            if (msg == "false")
               alert("You can't pick up this shift because it overlaps with a shift you are already scheduled for.");
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
            if (msg == "false")
               alert("You can't pick up this shift because it overlaps with a shift you are already scheduled for.");
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
            if (increment == true)
               incrementDate();
         }
         return true;
      }
   }
   $.prompt(html, state);
}

function initializeForm(start, end)
{

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
   });
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
         if (msg == "false")
            alert("You cannot take this shift because you are already scheduled for an overlapping period of time.");
         else
            $("#calendar").fullCalendar("refetchEvents");
      },
      error : function(msg, textStatus, errorThrown)
      {
         alert(errorThrown + " pickUpEmptyShift");
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
function closeForm()
{
   $("#enterAvailability").hide();
   return false;
}
function updateAvailability()
{
   var availability = $("input:radio[name=availability]:checked").val();
   var start, end, allDay;
   allDay = true;
   if(availability == "Custom")
   {
      start = $("[name=start]", $("#availabilityForm")).val();
      end = $("[name=end]", $("#availabilityForm")).val();
      allDay = false;
   }
   updateEvent(availability, selectedDate, allDay, start, end);
   $("#enterAvailability").hide();
   return false;
}
function focusStart()
{
   $("[name=start]", $("#availabilityForm")).focus();
}
function getStartAndEndDates(view, selectedDate)
{
   var ret = '';
   if (view == 'agendaWeek' || view == 'basicWeek')
   {
      var start = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate() - selectedDate.getDay());
      var end = new Date(start.getFullYear(), start.getMonth(), start.getDate() + 6);
      ret =
      {
         startDate : start,
         endDate : end
      };
   }
   else if (view == 'month')
   {
      var start = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), 1);
      var end = new Date(start.getFullYear(), start.getMonth() + 1, 0);
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
      }
   }
   return ret;
}

function copyWeek()
{
   var date = $("#calendar").fullCalendar("getDate");
   var date_obj = getStartAndEndDates("basicWeek", date);
   if(availability == true)
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
   var date_obj = getStartAndEndDates("basicWeek", date);
   if(clipboard == null)
   {
      alert("You must copy a week first! In the week view, click the 'Copy Week' button in the vertical menu on the left side of the screen");
      return false;
   }
   $.each(clipboard, function(key, val) {
      var day = val.start.getDay();
      var start = new Date(date.getFullYear(), date.getMonth(), date_obj.startDate.getDate() + day, val.start.getHours(), val.start.getMinutes());
      var end = start;
      var title = (val.allDay == false) ? "Custom": val.title;
      if(val.allDay == false)
         end = new Date(date.getFullYear(), date.getMonth(), date_obj.startDate.getDate() + day, val.end.getHours(), val.end.getMinutes());
      updateEvent(title, start, val.allDay, start.toTimeString().split(" ")[0], end.toTimeString().split(" ")[0]);
   });
}
