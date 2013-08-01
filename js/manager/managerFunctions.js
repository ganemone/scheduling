function setTutorial(bool)
{
   tutorial = bool;
}

function toggleEvents(id)
{
   var disp = false;
   var color = document.getElementById(id).style.color;
   if (color == "Black" || color == "black")
   {
      document.getElementById(id).style.color = 'Green';
      disp = true;
   }
   else
   {
      document.getElementById(id).style.color = 'Black';
      $("#calendar").fullCalendar('removeEvents', function(event)
      {
         if (event.employeeId == id)
            return true;
      });
      disp = false;
   }
   $.ajax(
   {
      type : "POST",
      data :
      {
         employeeId : id,
         display : disp
      },
      url : url + "index.php/manager/toggleDisplay",
      success : function(msg)
      {
         $("#calendar").fullCalendar('refetchEvents');
      },
      error : function(msg, textStatus, errorThrown)
      {
         alert(textStatus + "toggleDisplay");
      }
   });
}

function selectHidden()
{
   $("#hiddenRadio").attr("checked", "true");
}

function initSelectMenu()
{
   $("#firstOption").attr("selected", "true");
}

function stringToTime(string)
{
   var split = string.split(':');
   var second = split[1].split(" ");
   hour = Number(split[0]);
   if (second[1] == 'pm')
      hour += 12;
   return new Date(0,0,0,hour, second[0], 00).toTimeString().split(" ")[0];
}

function timeToString(time)
{
   var string = time.toTimeString().split(" ")[0].split(":");
   var am_pm = 'am';
   var hour = Number(string[0]);
   if (Number(string[0]) > 12)
   {
      var am_pm = 'pm';
      hour -= 12;
   }
   return (hour + ":" + string[1] + " " + am_pm);
}

function deleteTemplate(htmlObject)
{
   if ($("#deleteOption").is(":checked"))
   {
      $("#deleteConfirmation").dialog(
      {
         autoOpen : true,
         position : ['middle', 100],
         buttons :
         {
            "Delete" : function()
            {
               $(this).dialog('close');
               $.ajax(
               {
                  type : "POST",
                  url : url + "index.php/manager/deleteTemplate",
                  data :
                  {
                     templateId : $(htmlObject).data('eventObject').templateId
                  },
                  success : function(msg)
                  {
                     $(htmlObject).qtip('hide').remove();
                  },
                  error : function(textStatus)
                  {
                     alert(textStatus + "deleteTemplate");
                  }
               });
            },
            "Cancel" : function()
            {
               $(this).dialog('close');
            }
         }
      });
   }
}

function showGoal(date, view)
{
   var finalDate;
   if (view.name == "month")
   {
      date.setDate(1);
      finalDate = new Date(date.getFullYear(), date.getMonth() + 1, 0);
   }
   else if (view.name == 'basicWeek')
   {
      date.setDate(date.getDate() - date.getDay());
      finalDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() + 6);
   }
   else
      finalDate = date;
   $.ajax(
   {
      type : "GET",
      url : url + "index.php/manager/getGoal",
      data :
      {
         startDate : date.toDateString(),
         endDate : finalDate.toDateString()
      },
      success : function(msg)
      {
      },
      error : function(textStatus, msg, errorThrown)
      {
         alert(errorThrown + "getGoal");
      }
   });
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

function initializeGoalTips(view)
{
   var dateObj = getStartAndEndDates(view.name, $("#calendar").fullCalendar("getDate"));
   if (view.name == 'agendaWeek' || view.name == 'basicWeek')
   {
      $.ajax(
      {
         type : "GET",
         url : url + "index.php/manager/getGoal",
         data :
         {
            startDate : dateObj.startDate.toDateString(),
            endDate : dateObj.endDate.toDateString()
         },
         success : function(msg)
         {
            var sum = msg;
            var text = $("span.fc-header-title h2").text();
            var textSplit = text.split("(");
            $("span.fc-header-title h2").text(textSplit[0] + " ($" + sum + ")");
            $("span.fc-header-title h2").fadeIn();
         },
         error : function(textStatus, msg, errorThrown)
         {
            alert(errorThrown + "getGoal");
         }
      });
   }
   else
   {
      $.ajax(
      {
         type : "GET",
         url : url + "index.php/manager/getGoal",
         data :
         {
            startDate : dateObj.startDate.toDateString(),
            endDate : dateObj.endDate.toDateString()
         },
         success : function(msg)
         {
            var text = $("span.fc-header-title h2").text();
            var textSplit = text.split("(");
            $("span.fc-header-title h2").text(textSplit[0] + " ($" + msg + ")");
            $("span.fc-header-title h2").fadeIn();
         },
         error : function(textStatus, msg, error)
         {
            alert(error + "getGoal");
         }
      })
   }
}

/* Function called when the event is moved or resized in any way.
 *    event = Calendar Event
 *    dayDelta = Difference in days on a moved event
 *    minuteDelta = Difference in minutes on a moved event
 *    revertFunc = Function that reverts the element back to its original position.
 *
 */
function eventMove(event, dayDelta, minuteDelta, revertFunc, method)
{
   var title = event.title;
   if (title == "Test Schedule")
   {
      return true;
   }
   else if (!(event.category == 'scheduled'))
   {
      revertFunc();
   }
   else
   {
      var id = event.employeeId;
      var day = new Date(event.start.getFullYear(), event.start.getMonth(), event.start.getDate() - dayDelta);

      var _day = event.start;
      var start = event.start.toTimeString();
      var end = event.end.toTimeString();
      $.ajax(
      {
         type : "POST",
         url : url + "index.php/manager/deleteEvent",
         data :
         {
            id : event.rowId,
            table : "scheduled"
         },
         success : function(msg)
         {
            $.ajax(
            {
               type : "POST",
               url : url + "index.php/manager/scheduleEmployee",
               data :
               {
                  employeeId : id,
                  day : _day.toDateString(),
                  begin : start.split(" ")[0],
                  end : end.split(" ")[0],
                  category : event.area,
                  sfl : event.sfl
               },
               success: function(msg)
               {
                  var result_arr = jQuery.parseJSON(msg);
                  var refetch = result_arr[1];
                  if(refetch == true)
                     $("#calendar").fullCalendar("refetchEvents");
               },
               error : function()
               {
                  alert("Error: scheduleEmployee");
               }
            })
         },
         error : function()
         {
            alert("Error: deleteEvent");
         }
      });
   }
   var oldEvent =
   {
      start : new Date(event.start.getFullYear(), event.start.getMonth(), event.start.getDate(), event.start.getHours(), event.start.getMinutes() - minuteDelta),
      end : new Date(event.end.getFullYear(), event.end.getMonth(), event.end.getDate(), event.end.getHours(), event.end.getMinutes() - minuteDelta),
      category : event.category,
      event : event.category
   };
   if (method == "resized")
   {
      oldEvent =
      {
         start : new Date(event.start.getFullYear(), event.start.getMonth(), event.start.getDate(), event.start.getHours(), event.start.getMinutes()),
         end : new Date(event.end.getFullYear(), event.end.getMonth(), event.end.getDate(), event.end.getHours(), event.end.getMinutes() - minuteDelta),
         category : event.category,
         event : event.category
      };
   }
   //updateRowColumns(event, false);
   //updateRowColumns(oldEvent, true);

}

function createTemplate(employeeId, templateName)
{
   var currentDate = $("#calendar").fullCalendar('getDate');
   var id = ( typeof employeeId == "object") ? employeeId[0] : employeeId;
   currentDate.setDate(currentDate.getDate() - currentDate.getDay());
   $.ajax(
   {
      type : 'POST',
      url : url + 'index.php/manager/makeTemplate',
      data :
      {
         id : id,
         title : templateName,
         date : currentDate.toDateString()
      },
      success : function(msg)
      {
         loadTemplates();
      },
      error : function()
      {
         alert("ERROR makeTemplate");
      }
   });
}

function getEmployeesAvailable(begin, end)
{
   var usedIds = [];
   var events = $("#calendar").fullCalendar('clientEvents', function(event)
   {
      startDate = new Date(event.start.getFullYear(), event.start.getMonth(), event.start.getDate());
      beginDate = new Date(begin.getFullYear(), begin.getMonth(), begin.getDate());
      endDate = new Date(end.getFullYear(), end.getMonth(), end.getDate());

      if (startDate >= beginDate && startDate <= endDate && (event.category == "Available" || event.category == "Custom") && $.inArray(event.employeeId, usedIds) == -1)
      {
         usedIds.push(event.employeeId);
         return true;
      }
      return false;
   });
   return events;
}

function loadTemplates()
{
   var html = '';
   $('.external-event').each(function()
   {
      $(this).remove();
   });
   $.ajax(
   {
      type : "GET",
      url : url + "index.php/manager/loadTemplates",
      success : function(msg)
      {
         var a = jQuery.parseJSON(msg);
         for (var i = 0; i < a.length; i++)
         {
            var currentTemplateObject = jQuery.parseJSON(a[i]);
            var id = "#template" + i;
            html = "<div class='external-event' id='template" + i + "' title='" + currentTemplateObject.description + "' onclick=deleteTemplate('" + id + "');>" + currentTemplateObject.templateName + "</div>";
            $("#external-events").append(html);
            $("#template" + i).qtip(
            {
               content : currentTemplateObject.description,
               position :
               {
                  my : "bottom left",
                  at : "top right"
               },
               style :
               {
                  tip : "bottomLeft",
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
                     api.elements.tooltip.click(api.hide)
                  }
               }
            });

            $("#template" + i).data('eventObject', currentTemplateObject);
            $("#template" + i).draggable(
            {
               zIndex : 999,
               revert : true,
               revertDuration : 0,
               scroll : false,
               start : function()
               {
                  $('div.fc-event').qtip('disable');
                  $('div.external-event').qtip('hide');
                  $('div.external-event').qtip('disable');

               },
               stop : function()
               {
                  $('div.fc-event').qtip('enable');
                  $('div.external-event').qtip('enable');
               }
            });
         }
      },
      error : function(textStatus)
      {
         alert(textStatus + "loadTemplates");
      }
   });
}

// Creates a form based on the current employees for scheduling
function makeForm(begin, end, type, options, employeeHoursLeft)
{
   employeeHoursLeft = ( typeof employeeHoursLeft == 'undefined') ? false : employeeHoursLeft;
   options = ( typeof options !== 'undefined') ? options : true;
   var name = 'group';
   var htmlForm = '<table>';
   var employees = getEmployeesAvailable(begin, end);
   for (var j = 0; j < employees.length; j += 2)
   {
      var n = 'group';
      if (type == 'checkbox')
         n = j;
      var name = employees[j].title.split(" ");
      var name = name[0] + " " + name[1];
      //name += (employeeHoursLeft == false) ? "" : "Hours Left: " + Number(employeeHoursLeft[employees[j].employeeId].desired.split("-")[1]) - Number(employeeHoursLeft[employees[j].employeeId].scheduled);
      if (employeeHoursLeft != false)
      {
         var desired = employeeHoursLeft[employees[j].employeeId].desired.split("-")[1];
         var scheduled = employeeHoursLeft[employees[j].employeeId].scheduled;
         var hoursLeft = desired - scheduled;
         var style = (hoursLeft > 0) ? "Green" : "Red";
         name += "<div style='display:inline; color:" + style + ";' >(Hours Left:" + hoursLeft + ")</div>";
      }
      var id = employees[j].employeeId;
      htmlForm += '<tr>';
      htmlForm += '<td><label>' + name + '</td><td><input type="' + type + '" name="' + n + '" value=' + id + ' onclick="initSelectMenu();"></label></td>';
      if (j + 1 < employees.length)
      {
         var name2 = employees[j + 1].title.split(" ");
         var name2 = name2[0] + " " + name2[1];
         if (employeeHoursLeft != false)
         {
            var desired = employeeHoursLeft[employees[j+1].employeeId].desired.split("-")[1];
            var scheduled = employeeHoursLeft[employees[j + 1].employeeId].scheduled;
            var hoursLeft = desired - scheduled;
            var style = (hoursLeft > 0) ? "Green" : "Red";
            name2 += "<div style='display:inline; color:" + style + ";' >(Hours Left:" + hoursLeft + ")</div>";
         }
         var id2 = employees[j + 1].employeeId;
         if (type == 'checkbox')
            n++;
         htmlForm += '<td><label>' + name2 + '</td><td><input type="' + type + '" name="' + n + '" value=' + id2 + ' onclick="initSelectMenu();"></label></td>';
      }
      htmlForm += "</tr>";
   }
   htmlForm += '</table><table><tr><td>Other Employees:</td><td>' + selectList + '</td></tr>';
   if (type == 'checkbox' && options == true)
   {
      $("#floorOption").attr("checked", "checked");
      htmlForm += $("#editEventPopup").html();
   }
   return htmlForm;
}

function makeTemplateForm(begin, end, type)
{
   var name = 'group';
   var htmlForm = '<table>';
   var employees = getEmployeesAvailable(begin, end);
   for (var j = 0; j < employees.length; j += 2)
   {
      var n = 'group';
      if (type == 'checkbox')
         n = j;
      var name = employees[j].title.split("(")[0];
      var id = employees[j].employeeId;
      htmlForm += '<tr>';
      htmlForm += '<td><label>' + name + '</td><td><input type="' + type + '" name="' + n + '" value=' + id + ' onclick="initSelectMenu();"></label></td>';
      if (j + 1 < employees.length)
      {
         var name2 = employees[j + 1].title.split("(")[0];
         var id2 = employees[j + 1].employeeId;
         if (type == 'checkbox')
            n++;
         htmlForm += '<td><label>' + name2 + '</td><td><input type="' + type + '" name="' + n + '" value=' + id2 + ' onclick="initSelectMenu();"></label></td>';
      }
      htmlForm += "</tr>";
   }
   htmlForm += '</table><table><tr><td>Other Employees:</td><td>' + selectList + '</td></tr>';
   return htmlForm;
}

function scheduleEmployee(start, end, startTime, endTime)
{
   var weekStart = new Date(start.getFullYear(), start.getMonth(), start.getDate() - start.getDay());
   var weekEnd = new Date(weekStart.getFullYear(), weekStart.getMonth(), weekStart.getDate() + 6);
   var employees = getEmployeesAvailable(start, end);
   for (var i = 0; i < employees.length; i++)
   {
      employees[i] = employees[i].employeeId;
   }
   $.ajax(
   {
      type : "POST",
      url : url + "index.php/manager/getHoursLeft",
      data :
      {
         start : weekStart,
         end : weekEnd,
         employees : employees
      },
      success : function(msg)
      {
         var employeeInfo = jQuery.parseJSON(msg);
         continueScheduling(start, end, employeeInfo);
      },
      error : function(msg, text, error)
      {
         alert(error + " getHoursLeft");
      }
   });
}

function continueScheduling(start, end, employeeInfo)
{
   var state =
   {
      state0 :
      {
         title : "Shift: " + (start.getMonth() + 1) + "/" + start.getDate() + "/" + start.getFullYear() + "<br>From: " + startTime + " Until: " + endTime,
         html : makeForm(start, start, 'checkbox', true, employeeInfo) + "</table>",
         submit : function(e, v, m, f)
         {
            var title = "";
            day = start.getFullYear() + "-" + (start.getMonth() + 1) + "-" + start.getDate();
            var sfl = 0;
            for (var key in f)
            {
               sfl = (f.SFL == 1) ? 1 : sfl;
               if (key == 'category')
                  continue;
               if (key == 'emptyShift')
               {
                  addEmptyShift(start, end, f.category, sfl)
                  continue;
               }
               var id = f[key];
               if (id == "NA")
                  continue;
               if ( typeof id == 'undefined' || id == "" || id == 0 || key == "SFL")
                  continue;
               $.ajax(
               {
                  type : "POST",
                  data :
                  {
                     employeeId : id,
                     day : day,
                     begin : start.toTimeString().split(" ")[0],
                     end : end.toTimeString().split(" ")[0],
                     category : f.category,
                     sfl : sfl,
                     eventTitle : -1
                  },
                  url : url + "index.php/manager/scheduleEmployee",
                  success : function(msg)
                  {
                     var msgArr = jQuery.parseJSON(msg);
                     var event = jQuery.parseJSON(msgArr[0]);
                     var refetch = msgArr[1];
                     if(refetch == true)
                        $("#calendar").fullCalendar("refetchEvents");
                     else
                        $("#calendar").fullCalendar("renderEvent", event);
   
                  },
                  error : function(msg, textStatus, errorThrown)
                  {
                     alert(errorThrown + "/manager/scheduleEmployee");
                  }
               });
            }
         }
      }
   }
   $.prompt(state);
}

function promptEmployeeHPW(calEvent)
{
   $.ajax(
   {
      type : "POST",
      url : url + "index.php/manager/getEmployeeWeekHours",
      data :
      {
         employeeId : calEvent.employeeId,
         dayNum : calEvent.start.getDay(),
         date : calEvent.start.toDateString()
      },
      success : function(msg)
      {

         var hourInfo = JSON.parse(msg);

         document.getElementById('desired').innerHTML = "Desired Hours: " + hourInfo['desired'];
         document.getElementById('current').innerHTML = "Scheduled Hours: " + hourInfo['scheduled'];
         document.getElementById('notes').innerHTML = "Notes: " + hourInfo['notes'];
         $("#employeeInfo").dialog();
         $("#employeeInfo").dialog('option', 'title', calEvent.title);
         $("#employeeInfo").dialog('option', 'position', ['middle', 100]);

      }
   });
}

function scheduleShiftClick(calEvent)
{
   var start, end;
   var employees = [calEvent.employeeId];
   var date = calEvent.start;
   var weekStart = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay());
   var weekEnd = new Date(weekStart.getFullYear(), weekStart.getMonth(), weekStart.getDate() + 6);
   if (calEvent.category == 'Custom' || calEvent.category == "events")
   {
      start = calEvent.start.toTimeString().split(" ")[0];
      end = calEvent.end.toTimeString().split(" ")[0];
   }
   else if (calEvent.category == 'Available')
   {
      if (calEvent.start.getDay() == 0)
      {
         start = '11:45:00';
         end = '18:00:00';
      }
      else if (calEvent.start.getDay() == 6)
      {
         start = '09:45:00';
         end = '18:30:00';
      }
      else
      {
         start = '09:45:00';
         end = '20:30:00';
      }
   }
   if (calEvent.category != "events")
   {
      $.ajax(
      {
         type : "POST",
         url : url + "index.php/manager/getHoursLeft",
         data :
         {
            start : weekStart,
            end : weekEnd,
            employees : employees
         },
         success : function(msg)
         {
            var employeeInfo = jQuery.parseJSON(msg);
            continueScheduleShiftClick(calEvent, employeeInfo, start, end);
         },
         error : function(msg, text, error)
         {
            alert(error + " getHoursLeft");
         }
      });
   }
   else
   {
      continueScheduleShiftClick(calEvent, null, start, end);
   }
}

function continueScheduleShiftClick(calEvent, employeeHoursLeft, start, end)
{
   if (employeeHoursLeft != null)
   {
      var desired = employeeHoursLeft[calEvent.employeeId].desired.split("-")[1];
      var scheduled = employeeHoursLeft[calEvent.employeeId].scheduled;
      var hoursLeft = desired - scheduled;
      var style = (hoursLeft > 0) ? "Green" : "Red";
      hoursLeft = "<div style='display:inline; color:" + style + ";' >(Hours Left:" + hoursLeft + ")</div>";
   }
   $("#start").children("option").each(function()
   {
      if ($(this).val() == start)
         $(this).attr("selected", "selected");
      else
         $(this).removeAttr("selected");
   });
   $("#end").children("option").each(function()
   {
      if ($(this).val() == end)
         $(this).attr("selected", "selected");
      else
         $(this).removeAttr("selected");
   });
   var html = $("#customTimes").html();
   $(".rightClickMenuItem").each(function()
   {
      $(this).removeAttr('checked');
   });
   if (calEvent.position == "SP")
      $("#supportOption").attr("checked", "checked");
   else
      $("#floorOption").attr("checked", "checked");
   html += "Add Empty Shift:<input type='checkbox' name='emptyShift' />";
   html += $("#editEventPopup").html();
   $("#sflRightClickItem").removeAttr("checked");
   eventTitle = -1;
   var name = calEvent.title.split(" ");
   name = name[0] + " " + name[1] + " ";
   var title = "Schedule " + name;
   if (calEvent.category == "events")
   {
      title = "Schedule Employee for " + calEvent.title.split("(")[0] + " At " + calEvent.location;
      html = "Employee: " + selectList;
      html += $("#customTimes").html();
      html += "Add Empty Shift:<input type='checkbox' name='emptyShift' />";
      eventTitle = calEvent.title.split("(")[0];
   }
   title += (hoursLeft) ? hoursLeft: "";
   $.prompt(html,
   {
      title : title,
      buttons :
      {
         "Submit" : 1,
         "Cancel" : 2
      },
      submit : function(e, v, m, f)
      {
         var id = (calEvent.category == "events") ? f.group : calEvent.employeeId;
         var emptyShift = f.emptyShift;

         if (v == 1)
         {
            if (id != "NA")
            {
               $.ajax(
               {
                  type : "POST",
                  data :
                  {
                     employeeId : id,
                     day : calEvent.start.toDateString(),
                     begin : f.start,
                     end : f.end,
                     category : f.category,
                     sfl : f.SFL,
                     eventTitle : eventTitle,

                  },
                  url : url + "index.php/manager/scheduleEmployee",
                  success : function(msg)
                  {
                     var msgArr = jQuery.parseJSON(msg);
                     var event = jQuery.parseJSON(msgArr[0]);
                     var refetch = msgArr[1];
                     if(refetch == true)
                        $("#calendar").fullCalendar("refetchEvents");
                     else
                        $("#calendar").fullCalendar("renderEvent", event);
                  },
                  error : function(msg1, msg2, msg3)
                  {
                     alert(msg1 + " scheduleEmployee");
                  }
               });
            }
            if (emptyShift == 'on')
            {
               var start = calEvent.start;
               start.setHours(f.start.split(":")[0]);
               start.setMinutes(f.start.split(":")[1]);
               var end = new Date(start.getFullYear(), start.getMonth(), start.getDate(), f.end.split(":")[0], f.end.split(":")[1], 0);
               var cat = (eventTitle == -1) ? f.category : eventTitle;
               addEmptyShift(start, end, cat, f.sfl);
            }
         }
         return true;
      }
   });
}

function clearEditEventPopup()
{
   $(".rightClickMenuItem").each(function()
   {
      if ($(this).val() != "SP")
      {
         $(this).removeAttr("selected");
         $(this).removeAttr("checked");
      }
   });
}

function toggleEmployeeAvailability()
{
   var id = $("#toggleEmployeeAvailability").data("employeeId");
   var position = $.inArray(Number(id), removedEmployees);
   if (position == -1)
   {
      removedEmployees.push(Number(id));
      $("#calendar").fullCalendar("removeEvents", function(event)
      {
         if (event.employeeId == id && event.category == 'Available' || event.category == 'Custom' || event.category == 'Busy')
            return true;
         return false;
      });
   }
   else
   {
      removedEmployees.splice(position, 1);
      $("#calendar").fullCalendar("refetchEvents");
   }
}

function resetAvailability()
{
   removedEmployees = [0];
   $("#calendar").fullCalendar("refetchEvents");
   $("#employeeRightClickDiv").hide();
}

function repeatOptionChanged()
{
   value = $("#coEventRepeating").val();

   if (value == '0')
   {
      $("#coEventRepeatEnd").attr("disabled", "disabled").val("");
   }
   else
      $("#coEventRepeatEnd").removeAttr("disabled");
}

function addCOEvent(title, date, start, end, location, repeating, endRepeat)
{
   $.ajax(
   {
      type : "POST",
      url : url + "index.php/manager/addCOEvent",
      data :
      {
         title : title,
         date : date,
         start : start,
         end : end,
         location : location,
         repeating : repeating,
         endRepeat : endRepeat
      },
      success : function(msg)
      {
         $("#calendar").fullCalendar("removeEventSource", url + "index.php/manager/coEventSource");
         $("#calendar").fullCalendar("addEventSource", url + "index.php/manager/coEventSource");
      },
      error : function(msg, textStatus, errorThrown)
      {
         alert(textStatus + " addCOEvent");
      }
   })
}

function addEmptyShift(start, end, category, sfl)
{
   $.ajax(
   {
      type : "GET",
      url : url + "index.php/manager/addEmptyShift",
      data :
      {
         date : start.toDateString(),
         start : start.toTimeString().split(" ")[0],
         end : end.toTimeString().split(" ")[0],
         category : category,
         sfl : sfl
      },
      success : function(msg)
      {
         $("#calendar").fullCalendar("refetchEvents");
      },
      error : function(msg, textStatus, errorThrown)
      {
         alert(textStatus + " addEmptyShift");
      }
   })
}

function deleteEvent(table, id, calId)
{
   $.ajax(
   {
      type : "POST",
      url : url + "index.php/manager/deleteEvent",
      data :
      {
         id : id,
         table : table
      },
      success : function(msg)
      {
         calendar.fullCalendar("removeEvents", calId);
      },
      error : function(textStatus, msg, errorThrown)
      {
         alert(errorThrown + " deleteEvent");
      }
   });
}

function fillEmptyShift(calEvent)
{
   calEvent.start.setSeconds(0);
   calEvent.end.setSeconds(0);
   var start = calEvent.start.toTimeString().split(" ")[0];
   var end = calEvent.end.toTimeString().split(" ")[0];
   var position = calEvent.position;
   var eventName = (calEvent.event == "true") ? position : -1;
   var title = "Fill Empty Shift from " + start + " until " + end;
   var html;
   if (eventName == -1)
   {
      $("#editEventPopup").each(function()
      {
         if ($(this).id == position)
            $(this).attr("selected", "selected");
         else
            $(this).removeAttr('selected');
      });
   }
   $("#start").children("option").each(function()
   {
      if ($(this).val() == start)
         $(this).attr("selected", "selected");
      else
         $(this).removeAttr("selected");
   });
   $("#end").children("option").each(function()
   {
      if ($(this).val() == end)
         $(this).attr("selected", "selected");
      else
         $(this).removeAttr("selected");
   });
   html = makeForm(calEvent.start, calEvent.end, "radio");
   html += $("#customTimes").html() + "</table>";
   $.prompt(html,
   {
      title : "Fill Empty Shift for " + calEvent.position,
      buttons :
      {
         "Submit" : 1,
         "Cancel" : 2
      },
      submit : function(e, v, m, f)
      {
         if (v == 2)
            return true;
         deleteEvent("emptyShifts", calEvent.rowId, calEvent.id);
         var id = ( typeof f.group == 'object') ? f.group[0] : f.group;
         if ( typeof id == "undefined" || !(id > 0))
            return true;
         $.ajax(
         {
            type : "POST",
            url : url + "index.php/manager/scheduleEmployee",
            data :
            {
               employeeId : id,
               day : calEvent.start.toDateString(),
               begin : f.start,
               end : f.end,
               category : calEvent.position,
               sfl : calEvent.sfl,
               eventTitle : eventName
            },
            error : function()
            {
               alert("Error: scheduleEmployee");
            },
            success : function(msg)
            {
               var msgArr = jQuery.parseJSON(msg);
               var event = jQuery.parseJSON(msgArr[0]);
               var refetch = msgArr[1];
               if(refetch == true)
                  $("#calendar").fullCalendar("refetchEvents");
               else
                  $("#calendar").fullCalendar("renderEvent", event);
            }
         });
      }
   });
}
/*
function fillRowColumns()
{
   var date = $("#calendar").fullCalendar("getDate");
   var formattedDate = date.getFullYear() + "-";
   formattedDate += (date.getMonth() > 9) ? date.getMonth() + 1 : "0" + (date.getMonth() + 1);
   formattedDate += "-";
   formattedDate += (date.getDate() > 9) ? date.getDate() : "0" + (date.getDate());
   var hourArray = peoplePerHour[formattedDate];
   $("th.fc-agenda-axis.fc-widget-header").each(function()
   {
      var text = $(this).text();
      if (text.indexOf("m") != -1)
      {
         var num = Number((text.indexOf("am") == -1) ? text.split("pm")[0] : text.split("am")[0]);
         if (text.indexOf("pm") == -1)
            num -= 6;
         else if (text.indexOf("am") == -1)
         {
            if (num == 12)
               num -= 6;
            else
               num += 6;
         }
         $(this).text(text.split(":")[0] + ":" + hourArray[num]);
      }
   });
}

function updateRowColumns(event, removed)
{
   var view = $("#calendar").fullCalendar('getView');
   if (event.category == "scheduled" && event.area != "SP" && event.event != "true")
   {
      var date = event.start;
      var formattedDate = date.getFullYear() + "-";
      formattedDate += (date.getMonth() > 9) ? date.getMonth() + 1 : "0" + (date.getMonth() + 1);
      formattedDate += "-";
      formattedDate += (date.getDate() > 9) ? date.getDate() : "0" + (date.getDate());
      var hourArray = peoplePerHour[formattedDate];
      var hourStart = (event.start.getMinutes() == 0) ? event.start.getHours() : event.start.getHours() + 1;
      var hourEnd = (event.end.getMinutes() == 0) ? event.end.getHours() : event.end.getHours() + 1;
      for (var i = hourStart - 6; i < hourEnd - 6; i++)
      {
         if (removed == false)
         {
            hourArray[i]++;
         }
         else
         {
            hourArray[i]--;
         }
      }
      peoplePerHour[formattedDate] = hourArray;
      if (view.name == 'month' || view.name == 'agendaWeek' || view.name == 'basicWeek')
         return false;
      fillRowColumns();
   }
   return true;
}*/

function updateSort()
{
   var sorting = $("#sorting").val();
   $("#calendar").fullCalendar("option", "sorting", sorting);
   $("#calendar").fullCalendar("refetchEvents");
}

