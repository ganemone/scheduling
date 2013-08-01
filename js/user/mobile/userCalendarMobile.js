$('#calendar').fullCalendar(
{
   aspectRatio : 1.8,
   header :
   {
      left : 'prev,next today',
      center : 'title',
      right : "month,basicWeek,basicDay"
   },
   defaultView : 'basicWeek',
   editable : true,
   minTime : 8,
   maxTime : 21,
   selectable : true,
   loading : function(bool)
   {
      if (bool)
         $('#loading').show();
      else
         $('#loading').fadeOut();
   },
   timeFormat :
   {
      '' : 'h:mm{ - h:mm}'
   },
   select : function(start, end, allDay, jsEvent, view)
   {
      if (view.name == 'month' || allDay == true)
      {
         var d = new Date();
         d.setDate(1);
         d.setMonth(d.getMonth() + 0);
         var compareDate = new Date(start.getFullYear(), start.getMonth(), start.getDate() + 1, 0, 0, 0);

         if (compareDate >= d)
         {
            setDate(start);
            $("#enterAvailability").show().offset(
            {
               top : jsEvent.pageY - 200,
               left : jsEvent.pageX - 350
            });
         }
         else
         {
            $("#calendar").fullCalendar('unselect');
            selectedDate = null;
            $.prompt("It is too late to upate your availability on this day. Please contact Tim Martin at tmartin@gazellesports.com",
            {
               title : "Oh No!",
               buttons :
               {
                  Ok : 1
               }
            });
         }
      }
   },
   eventRender : function(event, element, view)
   {
      if (event.category == 'scheduled-pickup' || event.category == 'scheduled-cover')
         element.css("height", "200px");
      element.css("font-size", "15pt");
   },
   viewDisplay : function(view)
   {
      if(view.name == "basicWeek")
      {
         $("#copyWeek").removeClass("disabled").button();
         $("#pasteWeek").removeClass("disabled").button();
      }
      else
      {
         $("#copyWeek").addClass("disabled");
         $("#pasteWeek").addClass("disabled");
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

            $("#minHours").val(json["minHours"]);
            $("#maxHours").val(json["maxHours"]);
            $("#notes").val(json["notes"]);
         },
         error : function()
         {
            alert("ERROR!!!");
         }
      });
   },
   eventClick : function(event, jsEvent, view)
   {
      if (event.category == 'scheduled-cover' || event.category == 'scheduled-pickup' && coverRequest == false || event.category == 'emptyShifts')
      {
         var start = event.start.toTimeString().split(" ")[0];
         var end = event.end.toTimeString().split(" ")[0];
         initializeForm(start, end);
         var states = [
         {
            title : "Pick Up Shift",
            html : event.description,
            buttons :
            {
               'Entire Shift' : 1,
               'Partial Shift' : 2,
               Cancel : 0
            },
            submit : function(e, v, m, f)
            {
               if (v == 1)
               {
                  if (event.category == 'emptyShifts')
                     pickUpEmptyShift(event);
                  else
                     pickUpShift(null, null, employeeId, event.employeeId, event.id);
                  return true;
               }
               else if (v == 2)
               {
                  $.prompt.nextState();
                  return false;
               }
               return true;
            }
         },
         {
            title : "Custom Shift",
            html : document.getElementById('customTimes').innerHTML,
            buttons :
            {
               Submit : 1,
               Cancel : 0
            },
            submit : function(e, v, m, f)
            {
               if (v == 1)
               {
                  if (event.category == "emptyShifts")
                     pickUpEmptyShift(event, f.start, f.end);
                  else
                     pickUpShift(f.start, f.end, employeeId, event.employeeId, event.id);
               }
               return true;
            }
         }];
         $.prompt(states);
      }
      if (coverRequest == true && event.category == 'scheduled')
      {
         coverRequest = false;
         start = event.start;
         end = event.end;
         eId = employeeId;

         var _start = event.start.toTimeString().split(" ")[0];
         var _end = event.end.toTimeString().split(" ")[0];
         initializeForm(_start, _end);

         $("#shiftCoverRequest").dialog('close');
         var states =
         {
            State0 :
            {
               title : "Confirmation",
               html : "Are you sure you would like to request a cover for this shift? (Note: the shift will remain on your schedule, and you will still be responsible for it until someone claims it).",
               buttons :
               {
                  "Full Shift" : 1,
                  "Partial Shift" : 2,
                  "Cancel" : 0
               },
               submit : function(e, v, m, f)
               {
                  if (v == 1)
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
                           if (msg == 0)
                              $.prompt.goToState('successState0');
                           else
                           {
                              $("#calendar").fullCalendar('refetchEvents');
                              $.prompt.goToState('successState1');
                           }
                        },
                        error : function(msg, textStatus, errorThrown)
                        {
                           alert(errorThrown);
                        }
                     });
                     return false;
                  }
                  else if (v == 2)
                  {
                     $.prompt.goToState('customState');
                     return false;
                  }
                  return true;
               }
            },
            successState0 :
            {
               title : "Oops!",
               html : "This shift is already out for cover!",
               buttons :
               {
                  Ok : 0
               },
               submit : function(e, v, m, f)
               {
                  return true;
               }
            },
            successState1 :
            {
               title : "Success",
               html : "You have successfully put this shift up for cover",
               buttons :
               {
                  OK : 0
               },
               submit : function(e, v, m, f)
               {
                  return true;
               }
            },
            customState :
            {
               title : "Custom Shift",
               html : document.getElementById("customTimes").innerHTML,
               buttons :
               {
                  Submit : 1,
                  Cancel : 0
               },
               submit : function(e, v, m, f)
               {
                  if (v == 1)
                  {
                     requestPartialShiftCover(f.start, f.end, employeeId, event.id);
                  }
               }
            }
         };
         $.prompt(states);
      }
   },
   eventSources : [
   {
      url : url + "index.php/user/scheduledEventSource",
      error : function(msg, textStatus, errorThrown)
      {
         alert(errorThrown);
      }
   }]
});
