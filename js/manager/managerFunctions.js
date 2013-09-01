function setTutorial(bool)
{
   tutorial = bool;
}

/*function toggleEvents(id)
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
}*/

function selectHidden()
{
   $("#hiddenRadio").prop("checked", true);
}

function initSelectMenu()
{
   $("#firstOption").prop("selected", true);
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
                     //$(htmlObject).qtip('hide').remove();
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
            $("#templates").append(html);
            /*$("#template" + i).qtip(
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
            });*/

            $("#template" + i).data('eventObject', currentTemplateObject);
            $("#template" + i).draggable(
            {
               zIndex : 999,
               revert : true,
               revertDuration : 0,
               scroll : false,
               start : function()
               {
                  /*$('div.fc-event').qtip('disable');
                  $('div.external-event').qtip('hide');
                  $('div.external-event').qtip('disable');*/

               },
               stop : function()
               {
                  /*$('div.fc-event').qtip('enable');
                  $('div.external-event').qtip('enable');*/
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
      $("#floorOption").prop("checked", true);
      htmlForm += $("#editEventPopup").html();
   }
   return htmlForm;
}

function makeTemplateForm(begin, end, type)
{
   var form_obj = {
      name     : "templateForm",
      id       : "templateForm",
      style    : "width: 400px;",
      elements : new Array()
   };
   var name = 'group';
   var htmlForm = '<table>';
   var employees = getEmployeesAvailable(begin, end);
   for (var j = 0; j < employees.length; j++)
   {
      form_obj.elements.push({
         id          : "template_employee_" + employees[j].employeeId,
         name        : "template_employee[]",
         label       : employees[j].title.split("(")[0] + ": ",
         value       : employees[j].employeeId,
         type        : type
      });
   }
   if(employees.length == 0)
      return "Please turn on some employees before attempting to schedule with a template.";
   return buildForm(form_obj);
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
         var message = "<div style='width: 500px;'>";
         message += "<h3>" + calEvent.title + "</h3><hr>";
         message += "<table><tr><td>";
         message += "Desired Hours: </td><td>" + hourInfo['desired'] + "</td></tr><br>";
         message += "Scheduled Hours: " + hourInfo['scheduled'] + "<br>";
         message += "Notes: " + hourInfo['notes'] + "<br>";
         message += "</div>";
         bootbox.alert(message);
         /*
         $("#employeeInfo").dialog();
         $("#employeeInfo").dialog('option', 'title', calEvent.title);
         $("#employeeInfo").dialog('option', 'position', ['middle', 100]);*/

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
         $(this).prop("selected", true);
      else
         $(this).prop("selected", false);
   });
   $("#end").children("option").each(function()
   {
      if ($(this).val() == end)
         $(this).prop("selected", true);
      else
         $(this).prop("selected", false);
   });
   var html = $("#customTimes").html();
   $(".rightClickMenuItem").each(function()
   {
      $(this).prop("checked", false);
   });
   if (calEvent.position == "SP")
      $("#supportOption").prop("checked", true);
   else
      $("#floorOption").prop("checked", true);
   html += "Add Empty Shift:<input type='checkbox' name='emptyShift' />";
   html += $("#editEventPopup").html();
   $("#sflRightClickItem").prop("checked", false);
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
         $(this).prop("selected", false);
         $(this).prop("checked", false);
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
            $(this).prop("selected", true);
         else
            $(this).prop("selected", false);
      });
   }
   $("#start").children("option").each(function()
   {
      if ($(this).val() == start)
         $(this).prop("selected", true);
      else
         $(this).prop("selected", false);
   });
   $("#end").children("option").each(function()
   {
      if ($(this).val() == end)
         $(this).prop("selected", true);
      else
         $(this).prop("selected", false);
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
function toggleDelete(e)
{
   var element = $("#deleteOption");
   if (typeof e != "undefined")
   {
      if(element.is(":checked"))
         element.prop("checked", false);
      else
         element.prop("checked", true);
   }
   if(element.is(":checked"))
   {
      element.prop("checked", false);
      global_options_obj['delete'] = false;
   }
   else
   {
      element.prop("checked", true);
      global_options_obj['delete'] = true;
   }
}

function updateSort()
{
   var sorting = $("#sorting").val();
   $("#calendar").fullCalendar("option", "sorting", sorting);
   $("#calendar").fullCalendar("refetchEvents");
}

/* Toggle Functions */
function toggleAll (e) 
{
   $("#all_busy").prop("indeterminate", false);
   $("#all_scheduled").prop("indeterminate", false);
   $("#all_available").prop("indeterminate", false);

   var element = $("#all_employees");
   element.prop("indeterminate", false);
   if (typeof e != "undefined")
   {
      if(element.is(":checked"))
         element.prop("checked", false);
      else
         element.prop("checked", true);
   }
   var bool_set = false;
   var busy_bool = false;
   var scheduled_bool = false;
   var availability_bool = false;
   if(element.is(":checked"))
   {
      element.prop("checked", false);

      $(".group").each(function() {
         $(this).prop("checked", false);
      });
      $("#all_busy").prop("checked", false);
      $("#all_scheduled").prop("checked", false);
      $("#all_available").prop("checked", false);
   }
   else
   {
      element.prop("checked", true);
      
      $(".group").each(function() {
         $(this).prop("checked", true);
      });

      $("#all_busy").prop("checked", true);
      $("#all_scheduled").prop("checked", true);
      $("#all_available").prop("checked", true);

      bool_set = true;
      if($("#all_busy").is(":checked"))
         busy_bool = true;
      if($("#all_scheduled").is(":checked"))
         scheduled_bool = true;
      if($("#all_available").is(":checked"))
         availability_bool = true;
   }

   for (var i = 0; i < global_employee_id_arr.length; i++) 
   {
      global_employee_obj[global_employee_id_arr[i]]['scheduled'] = scheduled_bool;
      global_employee_obj[global_employee_id_arr[i]]['available'] = availability_bool;
      global_employee_obj[global_employee_id_arr[i]]['busy'] = busy_bool;
   }

   if(bool_set === false)
   {
      $("#calendar").fullCalendar("removeEvents", function(event)
      {
         if(event.category == "Available" || event.category == "scheduled" || event.category == "Custom" || event.category == "Busy")
            return true;
         return false;
      });
   }
   else
   {
      $("#calendar").fullCalendar("removeEventSource", availabilityEventSource);
      $("#calendar").fullCalendar("addEventSource", availabilityEventSource);
      $("#calendar").fullCalendar("removeEventSource", scheduledEventSource);
      $("#calendar").fullCalendar("addEventSource", scheduledEventSource);
   }
   setGlobals();
   setGroupCheckBoxes("all");
}
function toggleAllCategory (category, e) 
{
   var element = $("#all_" + category);
   var bool_set = false;
   element.prop("indeterminate", false);
   if (typeof e != "undefined")
   {
      if(element.is(":checked"))
         element.prop("checked", false);
      else
         element.prop("checked", true);
   }
   if(!element.is(":checked"))
      bool_set = true;

   for (var i = 0; i < global_employee_id_arr.length; i++) 
   {
      global_employee_obj[global_employee_id_arr[i]][category] = bool_set;
   }

   if(element.is(":checked"))
   {
      element.prop("checked", false);
      $("#calendar").fullCalendar("removeEvents", function(event)
      {
         if(event.category == "Custom")
            return true;
         if(category == "available" && (event.category == "Available" || event.category == "Custom"))
            return true;
         else if(category == "busy" && event.category == "Busy")
            return true;
         else if(category == "scheduled" && event.category == "scheduled")
            return true;
         return false;
      });
   }
   else
   {
      element.prop("checked", true);
      if(category == "scheduled")
      {
         $("#calendar").fullCalendar("removeEventSource", scheduledEventSource);
         $("#calendar").fullCalendar("addEventSource", scheduledEventSource);  
      }
      else
      {
         $("#calendar").fullCalendar("removeEventSource", availabilityEventSource);
         $("#calendar").fullCalendar("addEventSource", availabilityEventSource); 
      }    
   }

   if($("#all_scheduled").is(":checked") && $("#all_busy").is(":checked") && $("#all_available").is(":checked"))
   {
      $("#all_employees").prop("checked", true);
   }
   else if($("#all_scheduled").is(":checked") || $("#all_busy").is(":checked") || $("#all_available").is(":checked"))
   {
      $("#all_employees").prop("checked", false).prop("indeterminate", true);
   }
   else
   {
      $("#all_employees").prop("checked", false);
   }
   setGlobals();
   setGroupCheckBoxes();
}
function toggleEvents (category, e) 
{
   var element = $("#event_" + category);
   if (typeof e != "undefined")
   {
      if(element.is(":checked"))
         element.prop("checked", false);
      else
         element.prop("checked", true);
   }
   if(element.is(":checked"))
   {
      element.prop("checked", false);
      if(category == "all")
      {
         $("." + group).each(function() {
            $(this).prop("checked", false);
         });
         for(var category in global_groups_obj[group])
         {
            global_groups_obj[group][category] = false;
         }
      }
      global_options_obj['events'] = false;

      $("#calendar").fullCalendar("removeEvents", function(event)
      {
         if(event.category == "events" && group == "event")
            return true;
         return false;
      });
   }
   else
   {
      $("#calendar").fullCalendar("removeEventSource", coEventSource);
      $("#calendar").fullCalendar("addEventSource", coEventSource);
      global_options_obj['events'] = true;
   }
}
function toggleGroup (group, category, e) 
{
   var element = $("#" + group + "_" + category);
   element.prop("indeterminate", false);
   if (typeof e != "undefined")
   {
      if(element.is(":checked"))
      {
         element.prop("checked", false);
      }
      else
      {
         element.prop("checked", true);
      }
   }
   if(element.is(":checked"))
   {
      element.prop("checked", false);
      if(category == "employees")
      {
         $("." + group).each(function() {
            $(this).prop("checked", false).prop("indeterminate", false);
         });
         global_groups_obj[group]["available"] = false;
         global_groups_obj[group]["busy"] = false;
         global_groups_obj[group]["scheduled"] = false;
      }
      else
      {
         global_groups_obj[group][category] = false;
      }

      $("#calendar").fullCalendar("removeEvents", function(event)
      {
         if(typeof event.employeeId != "undefined" && global_employee_obj[event.employeeId][group] && (category == "all" || (category == "busy" && event.category == "Busy") || (category == "available" && event.category == "Available" || event.category == "Custom") || (category == "scheduled" && event.category == "scheduled")))
               return true;
         return false;
      });
   }
   else
   {
      element.prop("checked", true);
      if(category == "employees")
      {
         $("." + group).each(function() {
            $(this).prop("checked", true);
         });
         for(var cat in global_groups_obj[group])
         {
            if(global_groups_obj[group].hasOwnProperty(cat) && cat != "employees")
            {
               global_groups_obj[group][cat] = true;
            }
         }
      }
      else
      {
         global_groups_obj[group][category] = true;
      }
   }

   if($("#" + group + "_available").is(":checked") && $("#" + group + "_busy").is(":checked") && $("#" + group + "_scheduled").is(":checked"))
   {
      $("#" + group + "_employees").prop("checked", true).prop("indeterminate", false);
   }
   else if($("#" + group + "_available").is(":checked") || $("#" + group + "_busy").is(":checked") || $("#" + group + "_scheduled").is(":checked"))
   {
      $("#" + group + "_employees").prop("checked", false).prop("indeterminate", true);
   }
   else
   {
      $("#" + group + "_employees").prop("checked", false).prop("indeterminate", false);
   }
   
   setEmployeeCheckBoxes(group);
   setGroupCheckBoxes(group);
}
function toggleEmployee (employeeId, e) 
{
   var element = $("#employee_" + employeeId);
   element.prop("indeterminate", false);
   if (typeof e != "undefined")
   {
      if(element.is(":checked"))
         element.prop("checked", false);
      else
         element.prop("checked", true);
   }
   if(element.is(":checked"))
   {
      element.prop("checked", false);
      global_employee_obj[employeeId]['available'] = false;
      global_employee_obj[employeeId]['busy']      = false;
      global_employee_obj[employeeId]['scheduled'] = false;
      $("#available_" + employeeId).prop("checked", false);
      $("#busy_" + employeeId).prop("checked", false);
      $("#scheduled_" + employeeId).prop("checked", false);
      $("#calendar").fullCalendar("removeEvents", function(event)
      {
         return (event.employeeId == employeeId) ? true : false;
      });      
   }
   else
   {
      element.prop("checked", true);
      global_employee_obj[employeeId]['available'] = true;
      global_employee_obj[employeeId]['busy']      = true;
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

function toggleAvailability (employeeId) 
{
   var element = $("#available_" + employeeId);
   if(element.is(":checked"))
   {
      element.prop("checked", false);
      global_employee_obj[employeeId]['available'] = false;
      $("#calendar").fullCalendar("removeEvents", function(event) {
         if(event.employeeId == employeeId && event.category == "Available" || event.category == "Custom")
            return true;
         return false;
      });
   }
   else
   {
      element.prop("checked", true);
      global_employee_obj[employeeId]['available'] = true;
      $("#calendar").fullCalendar("removeEventSource", availabilityEventSource);
      $("#calendar").fullCalendar("addEventSource", availabilityEventSource);
   }
   updateCheckbox(employeeId);
   setGroupCheckBoxes();
}

function toggleBusy (employeeId) 
{
   var element = $("#busy_" + employeeId);
   if(element.is(":checked"))
   {
      element.prop("checked", false);
      element.prop("checked", false);
      global_employee_obj[employeeId]['busy'] = false;
      $("#calendar").fullCalendar("removeEvents", function(event)
      {
         if(event.employeeId == employeeId && event.category == 'Busy')
            return true;
         return false;
      });
   }
   else
   {
      element.prop("checked", true);
      global_employee_obj[employeeId]['busy'] = true;
      $("#calendar").fullCalendar("removeEventSource", availabilityEventSource);
      $("#calendar").fullCalendar("addEventSource", availabilityEventSource);
   }
   updateCheckbox(employeeId);
   setGroupCheckBoxes();
}

function toggleScheduled (employeeId) 
{
   var element = $("#scheduled_" + employeeId);
   if(element.is(":checked"))
   {
      element.prop("checked", false);
      element.prop("checked", false);
      global_employee_obj[employeeId]['scheduled'] = false;
      $("#calendar").fullCalendar("removeEvents", function(event) {
         if(event.employeeId == employeeId && event.category == 'scheduled')
            return true;
         return false;
      });
   }
   else
   {
      element.prop("checked", true);
      global_employee_obj[employeeId]['scheduled'] = true;
      $("#calendar").fullCalendar("removeEventSource", scheduledEventSource);
      $("#calendar").fullCalendar("addEventSource", scheduledEventSource);
   }
   updateCheckbox(employeeId);
   setGroupCheckBoxes();
}

function updateCheckbox (employeeId) 
{
   if (global_employee_obj[employeeId]['available'] && global_employee_obj[employeeId]['busy'] && global_employee_obj[employeeId]['scheduled'])
   {
      $("#employee_" + employeeId).prop("checked", true).prop("indeterminate", false);
   }
   else if (!global_employee_obj[employeeId]['available'] && !global_employee_obj[employeeId]['busy'] && !global_employee_obj[employeeId]['scheduled'])
   {
      $("#employee_" + employeeId).prop("checked", false).prop("indeterminate", false);
   }
   else
   {
      $("#employee_" + employeeId).prop("checked", false).prop("indeterminate", true);
   }
}
function setGlobals () 
{
   var schedule_set = false;
   var available_set = false;
   var busy_set = false;
   if($("#all_scheduled").is(":checked"))
   {
      schedule_set = true;
      checkAllOptions("scheduled_");
   }
   else {
      uncheckAllOptions("scheduled_");
   }
   if($("#all_available").is(":checked"))
   {
      available_set = true;
      checkAllOptions("available_");
   }
   else {
      uncheckAllOptions("available_");
   }
   if($("#all_busy").is(":checked"))
   {
      busy_set = true;
      checkAllOptions("busy_");
   }
   else {
      uncheckAllOptions("busy_");
   }
   for (var i = 0; i < global_employee_id_arr.length; i++) {
      global_employee_obj[global_employee_id_arr[i]]["scheduled"] = schedule_set;
      global_employee_obj[global_employee_id_arr[i]]["busy"]      = busy_set;
      global_employee_obj[global_employee_id_arr[i]]["available"] = available_set;
   };

   if(schedule_set && busy_set && available_set)
      checkAllOptions("employee_");
   else if(schedule_set || busy_set || available_set)
      indeterminateAllOptions("employee_");
   else
      uncheckAllOptions("employee_");
}
function checkAllOptions (prefix)
{
   for (var i = 0; i < global_employee_id_arr.length; i++) 
   {
      
      $("#" + prefix + global_employee_id_arr[i]).prop("checked", true).prop("indeterminate", false);
   }
}
function uncheckAllOptions (prefix) 
{
   for (var i = 0; i < global_employee_id_arr.length; i++) 
   {
      
      $("#" + prefix + global_employee_id_arr[i]).prop("checked", false).prop("indeterminate", false);
   }
}
function indeterminateAllOptions (prefix) 
{
   for (var i = 0; i < global_employee_id_arr.length; i++) 
   {
      
      $("#" + prefix + global_employee_id_arr[i]).prop("checked", false).prop("indeterminate", true);
   }
}
function setEmployeeCheckBoxes(group)
{
   var add_obj = {
      "available" : false,
      "busy"      : false,
      "scheduled" : false
   };
   var remove_obj = {
      "available" : false,
      "busy"      : false,
      "scheduled" : false
   };

   for(var i = 0; i < global_groups_obj[group]["employees"].length; i++)
   {
      var employee_id = global_groups_obj[group]["employees"][i];
      var group_obj = global_groups_obj[group];
      var checked_counter = 0;
      var unchecked_counter = 0;
      var cat_arr = ["available", "busy", "scheduled"];
      for(var j = 0; j < cat_arr.length; j++)
      {
         var category = cat_arr[j];
         if(group_obj[category])
         {
            if(!$("#" + category + "_" + employee_id).is(":checked"))
            {
               add_obj[category] = true;
               $("#" + category + "_" + employee_id).prop("checked", true);
            }
            global_employee_obj[employee_id][category] = true;
            checked_counter++;
         }
         else
         {
            remove_obj[category] = true;
            $("#" + category + "_" + employee_id).prop("checked", false);
            global_employee_obj[employee_id][category] = false; 
            unchecked_counter++;
         }
      }
      if(unchecked_counter == 0)
      {
         $("#employee_" + employee_id).prop("checked", true).prop("indeterminate", false);
      }
      else if(checked_counter == 0)
      {
         $("#employee_" + employee_id).prop("checked", false).prop("indeterminate", false);
      }
      else
      {
         $("#employee_" + employee_id).prop("checked", false).prop("indeterminate", true);
      }
   }
   if(add_obj["scheduled"])
   {
      $("#calendar").fullCalendar("removeEventSource", scheduledEventSource);
      $("#calendar").fullCalendar("addEventSource", scheduledEventSource);
   }
   if(add_obj["available"] || add_obj["busy"])
   {
      $("#calendar").fullCalendar("removeEventSource", availabilityEventSource);
      $("#calendar").fullCalendar("addEventSource", availabilityEventSource);               
   }
   if(remove_obj["available"] || remove_obj["busy"] || remove_obj["scheduled"])
   {
      $("#calendar").fullCalendar("removeEvents", function(event)
      {
         if(!event[group])
         {
            return false
         }
         if(remove_obj["available"] && (event.category == "Available" || event.category == "Custom"))
         {
            return true;
         }
         else if(remove_obj["busy"] && event.category == "Busy")
         {
            return true;
         }
         else if(remove_obj["scheduled"] && event.category == "Scheduled")
         {
            return true;
         }
         return false;
      });
   }
}
function setGroupCheckBoxes (g)
{
   var employee_id, available_counter, busy_counter, scheduled_counter;
   for (group in global_groups_obj) 
   {
      if (global_groups_obj.hasOwnProperty(group) && (typeof g == "undefined" || group != g))
      {
         checkGroup(group);
      }
   }
   if(g !== "all")
      checkGroup("all");
}
function checkGroup (group) 
{
   var selector_arr = ["_employees", "_available", "_busy", "_scheduled"];
   
   for (var i = 0; i < selector_arr.length; i++) 
   {
      var element = $("#" + group + selector_arr[i]);
      element.prop("indeterminate", false);

      if($("." + group + selector_arr[i] + ":not(:checked)").length == 0)
      {
         element.prop("checked", true);
      }
      else if($("." + group + selector_arr[i] + ":checked").length == 0 && $("." + group + selector_arr[i] + ":indeterminate").length == 0)
      {
         element.prop("checked", false);

      }
      else
      {
         element.prop("indeterminate", true);
         element.prop("checked", false);
      }
   };
}
function getEmployeeObj () {
   return JSON.stringify(global_employee_obj);
}
function getOptionsObj () {
   return JSON.stringify(global_options_obj);
}