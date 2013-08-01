$("#external-events").draggable();
$("#options").draggable();
$("#employeeWrapper").resizable(
{
   alsoResize : "#employees",
   handles : "se"
});
$("#colors").draggable();

$(document).click(function(e)
{
   if ($("#editEventPopup").is(":visible"))
   {
      var offset = $("#editEventPopup").offset();
      var width = $("#editEventPopup").width();
      var height = $("#editEventPopup").height();
      if (e.pageX < offset.left || e.pageX > offset.left + width)
      {
         $("#editEventPopup").hide();
      }
      else if (e.pageY < offset.top || e.pageY > offset.top + height)
      {
         $("#editEventPopup").hide();
      }
   }
   if ($("#employeeRightClickDiv").is(":visible"))
   {
      var offset = $("#employeeRightClickDiv").offset();
      var width = $("#employeeRightClickDiv").width();
      var height = $("#employeeRightClickDiv").height();
      if (e.pageX < offset.left || e.pageX > offset.left + width)
      {
         $("#employeeRightClickDiv").hide();
      }
      else if (e.pageY < offset.top || e.pageY > offset.top + height)
      {
         $("#employeeRightClickDiv").hide();
      }
   }
   if ($("#overrideAvailability").is(":visible"))
   {
      var offset = $("#overrideAvailability").offset();
      var width = $("#overrideAvailability").width();
      var height = $("#overrideAvailability").height();
      if (e.pageX < offset.left || e.pageX > offset.left + width)
      {
         $("#overrideAvailability").hide();
      }
      else if (e.pageY < offset.top || e.pageY > offset.top + height)
      {
         $("#overrideAvailability").hide();
      }
   }
});
$(".overrideRightClick").click(function()
{
   var eventObject = $(this).data("event");
   var element = $(this).data("element");
   var category = $(this).val();
   var data =
   {
      id : eventObject.rowId,
      category : category
   };
   var start, end;
   if (category == "Custom")
   {
      $.prompt($("#customTimes").html(),
      {
         title : "Custom Times For " + eventObject.title.split("-")[0],
         buttons :
         {
            Submit : 1,
            Cancel : 0
         },
         submit : function(e, v, m, f)
         {
            if (v == 0)
               return true;
            start = f.start;
            end = f.end;
            data =
            {
               id : eventObject.rowId,
               category : category,
               start : start,
               end : end
            };
            $.ajax(
            {
               type : "POST",
               url : url + "index.php/manager/overrideAvailability",
               data : data,
               success : function(msg)
               {
                  $("#calendar").fullCalendar("refetchEvents");
               },
               error : function(msg, textStatus, errorThrown)
               {
                  alert("overrideAvailability " + errorThrown);
               }
            });
         }
      });
   }
   else
   {
      $.ajax(
      {
         type : "POST",
         url : url + "index.php/manager/overrideAvailability",
         data : data,
         success : function(msg)
         {
            $("#calendar").fullCalendar("refetchEvents");
         },
         error : function(msg, textStatus, errorThrown)
         {
            alert("overrideAvailability " + errorThrown);
         }
      });
   }
   $("#overrideAvailability").hide();
});
$(".rightClickMenuItem").click(function()
{
   var eventObject = $(this).data("event");
   var element = $(this).data("element");
   var category = $(this).val();
   $.ajax(
   {
      type : "POST",
      url : url + "index.php/manager/updateShiftCategory",
      data :
      {
         id : eventObject.rowId,
         category : category
      },
      success : function(msg)
      {
         eventObject.area = category;
         eventObject.title = eventObject.title.split("(")[0] + "(" + category + ")";
         
         if (eventObject.area == 'SP')
            eventObject.color = "#EB8F00";
         else
            eventObject.color = "#3366CC";
         $("#calendar").fullCalendar("updateEvent", eventObject);
      },
      error : function(textStatus, msg, errorThrown)
      {
         alert(errorThrown + "updateShiftCategory");
      }
   });
   $("#editEventPopup").hide();
});
$("#sflRightClickItem").click(function()
{
   var eventObject = $(this).data("event");
   var element = $(this).data("element");
   var sfl = ($(this).is(":checked")) ? 1 : 0;
   $.ajax(
   {
      type : "POST",
      url : url + "index.php/manager/updateSFL",
      data :
      {
         id : eventObject.rowId,
         sfl : sfl
      },
      success : function(msg)
      {
         eventObject.sfl = sfl;
         if (sfl == 1)
         {
            eventObject.title += " (SFL)";
            eventObject.color = '#B81900';
            eventObject.borderColor = '#000000';
         }
         else
         {
            eventObject.title = eventObject.title.split("(SFL)")[0];
            eventObject.borderColor = "";
            eventObject.color = (eventObject.area == 'SP') ? "#EB8F00" : "#3366CC";
         }
         $("#calendar").fullCalendar("updateEvent", eventObject);
      },
      error : function(textStatus, msg, errorThrown)
      {
         alert(errorThrown + "updateSFL");
      }
   });
});
$("#viewInfo").click(function()
{
   var startDate = $("#calendar").fullCalendar("getDate");
   var view = $("#calendar").fullCalendar('getView');
   var endDate = $("#calendar").fullCalendar('getDate');
   if (view.name == 'month')
   {
      startDate.setDate(1);
      endDate.setMonth(endDate.getMonth() + 1);
      endDate.setDate(0);
   }
   else if (view.name == 'agendaWeek' || view.name == 'basicWeek')
   {
      endDate.setDate(endDate.getDate() + 6 - endDate.getDay());
      startDate.setDate(startDate.getDate() - startDate.getDay());
   }
   var html = "";
   $.ajax(
   {
      type : "POST",
      url : url + 'index.php/manager/getTotalInfo',
      data :
      {
         start : startDate.toDateString(),
         end : endDate.toDateString(),
         view : view.name
      },
      success : function(msg)
      {
         var a = jQuery.parseJSON(msg);
         html += "<table><th>Name</th><th>Total Hours</th><th>Desired Hours</th>";
         for (var key in a)
         {
            html += a[key];
         }
         html += "</table>";
         $.ajax(
         {
            type : "POST",
            url : url + 'index.php/manager/getHourWageInfo',
            data :
            {
               start : startDate.toDateString(),
               end : endDate.toDateString()
            },
            success : function(msg)
            {
               var states = [
               {
                  title : "Current View Summary",
                  html : msg,
                  buttons :
                  {
                     Exit : 0,
                     'Employee Summary' : 1

                  },
                  submit : function(e, v, m, f)
                  {
                     if (v == 1)
                     {
                        $.prompt.nextState();
                        return false;
                     }
                  }
               },
               {
                  title : "Scheduled Employee Summary",
                  html : html,
                  buttons :
                  {
                     Back : -1,
                     Done : 2
                  },
                  submit : function(e, v, m, f)
                  {
                     if (v == -1)
                     {
                        $.prompt.prevState();
                     }
                  }
               }]
               $.prompt(states);
            },
            error : function(textStatus)
            {
               alert(textStatus + "getHourWageInfo")
            }
         });
      },
      error : function(textStatus)
      {
         alert(textStatus + "getTotalInfo")
      }
   });
});

$('select').each(function()
{
   $(this).wrap('<div class="styled-select" />');
});

/* Finalizes the schedule for the current month. This causes the availability to no longer be editable
 *    by employees, and makes their scheduled events visible.
 */
$("#finalize").click(function()
{
   var date = $("#calendar").fullCalendar('getDate');
   // This is where we set the editable dates for the employees!
   var dates = getStartAndEndDates($("#calendar").fullCalendar('getView').name, date);
   var endDate = dates.endDate;
   document.getElementById("finalizeConfirmation").innerHTML = "Are you sure you would like to finalize the schedule until " + dates.endDate.toDateString();
   endDate.setDate(endDate.getDate() + 1);
   $("#finalizeConfirmation").dialog(
   {
      autoOpen : false,
      position : ['middle', 200],
      buttons :
      {
         Yes : function()
         {
            $(this).dialog('close');
            $.ajax(
            {
               type : "POST",
               url : url + "index.php/manager/finalize",
               data :
               {
                  start : endDate.toDateString()
               },
               success : function(msg)
               {
               },
               error : function(msg, textStatus, errorThrown)
               {
                  alert(errorThrown + "finalize");
               }
            });
         },
         Cancel : function()
         {
            $(this).dialog('close');
         }
      }
   });
   $("#finalizeConfirmation").dialog('open');
});

$("#logOut").click(function()
{
   window.location = url + "index.php/manager/logOut";

});

$("#home").click(function()
{
   window.location = url + "index.php/user";
});

$("#template").click(function()
{
   if ($("#calendar").fullCalendar('getView').name == "basicWeek")
   {
      var startDate = $("#calendar").fullCalendar('getDate');
      startDate.setDate(startDate.getDate() - startDate.getDay());
      var endDate = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate() + 6);

      var state =
      {
         state0 :
         {
            title : "Select the employee to use for the model of the template.",
            html : makeTemplateForm(startDate, endDate, 'radio') + "<tr><td>Template Name:</td><td><input type='text' name='templateName'></input></td></tr></table>",
            submit : function(e, v, m, f)
            {
               createTemplate(f.group, f.templateName);
            }
         }
      }
      $.prompt(state);
   }
});

// Function for updating time slots in the agendaWeek and agendaDay Views
$("#selectTime").change(function()
{
   var view = $("#calendar").fullCalendar('getView').name;
   var date = $("#calendar").fullCalendar('getDate');
   renderCalendar(parseInt($(this).val()), view, date);
});

/* Function called to toggle busy events on or off in the calendar
 *
 */
$("#busyOption").click(function()
{
   var option = $(this);
   $.ajax(
   {
      type : "GET",
      url : url + "index.php/manager/toggleOption",
      data :
      {
         option : "busy"
      },
      success : function(msg)
      {
         if (option.is(":checked"))
         {
            $("#calendar").fullCalendar("refetchEvents");
         }
         else
         {
            $("#calendar").fullCalendar("removeEvents", function(event)
            {
               return (event.category == "Busy") ? true : false;
            });
         }
      }
   });
});
/*
 *    Function called to toggle on or off scheduled events.
 */
$("#scheduledOption").click(function()
{
   if ($(this).is(":checked"))
   {
      $("#calendar").fullCalendar("addEventSource", url + "index.php/manager/scheduledEventSource");
   }
   else
      $("#calendar").fullCalendar("removeEventSource", url + "index.php/manager/scheduledEventSource");
});
/*
 *    Function called to toggle on or off available events.
 */
$("#availableOption").click(function()
{
   if ($(this).is(":checked"))
      $("#calendar").fullCalendar("addEventSource", url + "index.php/manager/eventSource");
   else
      $("#calendar").fullCalendar("removeEventSource", url + "index.php/manager/eventSource");
});

$("#sflOption").click(function()
{
   //$("#calendar").fullCalendar('removeEvents');
   $("#toggleAll").removeAttr("checked");
   employees = false;
   $(".employeeName").each(function()
   {
      document.getElementById($(this).attr("id")).style.color = 'Black';
   });
   var b = false;
   if ($(this).is(":checked"))
   {
      b = true;
   }
   $.ajax(
   {
      type : "POST",
      url : url + "index.php/manager/showSFLOnly",
      data :
      {
         value : b
      },
      success : function(msg)
      {
         var sflArray = jQuery.parseJSON(msg);
         var style = (b == true) ? "Green" : "Black";
         for (var i = 0; i < sflArray.length; i++)
         {
            document.getElementById(sflArray[i]).style.color = style;
         }
         $("#calendar").fullCalendar("refetchEvents");
      },
      error : function(textStatus, msg, errorThrown)
      {
         alert(textStatus + "getSFLArray");
      }
   });
})
/* Function to toggle the visibility of all employees.
 *
 */
$("#toggleAll").click(function()
{
   var disp = false;
   $("#sflOption").removeAttr('checked');
   if ($("#toggleAll").is(":checked"))
   {
      disp = true;
      employees = true;
      $("#employees").children("button").each(function()
      {
         document.getElementById($(this).attr("id")).style.color = 'Green';
      });
   }
   else
   {
      disp = false;
      employees = false;
      $("#employees").children("button").each(function()
      {
         document.getElementById($(this).attr("id")).style.color = 'Black';
      });
      $("#calendar").fullCalendar('removeEvents');
   }
   $.ajax(
   {
      type : "POST",
      data :
      {
         disp : employees
      },
      url : url + "index.php/manager/toggleAll",
      success : function(msg)
      {
         if (disp == true)
         {
            $("#calendar").fullCalendar("refetchEvents");
         }
      },
      error : function(msg, textStatus, errorThrown)
      {
         alert(textStatus + "toggleAll");
      }
   });
});
$("#addCOEvent").click(function()
{
   $("#coEventDatePicker").datepicker(
   {
      "dateFormat" : "yy-mm-dd",
      showButtonPanel : true,
      prevText : "__",
      nextText : "__"
   });
   $("#addCoEvent").dialog(
   {
      buttons :
      {
         "Submit" : function()
         {
            var title = $("#coEventTitle").val();
            var date = $("#coEventDatePicker").val();
            var repeating = $("#coEventRepeating").val();
            var location = $("#coEventLocation").val();
            var start = $("#coEventStart").val();
            var end = $("#coEventEnd").val();
            var endRepeat = $("#coEventRepeatEnd").val();
            var start_split = start.split(":");
            var end_split = end.split(":");
            if (title == "")
            {
               alert("You Must Enter a Title");
               return false;
            }
            else if (date == "")
            {
               alert("You Must Enter a Date");
               return false;
            }
            else if (repeating > 0 && endRepeat == "")
            {
               alert("You must enter an End Repeat Value");
               return false;
            }
            $(this).dialog('close');

            title += "(" + timeToString(new Date(0, 0, 0, start_split[0], start_split[1], start_split[2])) + "-" + timeToString(new Date(0, 0, 0, end_split[0], end_split[1], end_split[2])) + ")";
            addCOEvent(title, date, start, end, location, repeating, endRepeat);
         },
         "Cancel" : function()
         {
            $(this).dialog('close');
         }
      }
   });
})

$("#eventOption").click(function()
{
   if ($(this).is(":checked"))
      $("#calendar").fullCalendar("addEventSource", url + "index.php/manager/coEventSource");
   else
      $("#calendar").fullCalendar("removeEventSource", url + "index.php/manager/coEventSource");
});

$(".employeeName").bind("contextmenu", function(e)
{
   if ($.inArray(Number(e.currentTarget.id), removedEmployees) != -1)
   {
      $("#toggleEmployeeAvailability").attr("checked", "checked");
   }
   else
      $("#toggleEmployeeAvailability").removeAttr('checked');
   $("#employeeRightClickDiv").show().offset(
   {
      top : e.pageY,
      left : e.pageX
   });
   $("#employeeRightClickDiv input").data("employeeId", Number(e.currentTarget.id));
   e.preventDefault();
});
