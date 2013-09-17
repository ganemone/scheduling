function cancelShiftEdit () {
   global_options_obj["eventClick"] = "standard";
   $(".top-right").remove();
   $("body").append("<div class='notifications top-right'></div>");
}
function mobile_addShift () {
   global_options_obj["eventClick"] = "addShift";
   $(".top-right").notify({
      type : "bangTidy", 
      message : {
      html : "Select an employees availability or an event.<button onclick='cancelShiftEdit()' class='btn btn-small btn-primary'>Cancel</button>"
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
function mobile_promptEditShiftCategory () {
   global_options_obj["eventClick"] = "editCategory";
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
function mobile_promptEditShiftTime () {
   global_options_obj["eventClick"] = "editTime";
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
function mobile_editShiftTime(calEvent) {
   var form_obj = {
      name : "shiftEditForm",
      id   : "shiftEditForm",
      elements : []
   };
   buildStartEndInputs(form_obj, calEvent.start.toTimeString().split(" ")[0], calEvent.end.toTimeString().split(" ")[0], "06:00:00", "21:00:00");
   form_obj["title"] = "Edit Shift Time";
   bootbox.confirm(buildForm(form_obj), function(result) {
      if(result) {
         if(validateStartEndTimes($("#start_time").val(), $("#end_time").val()) == false) {
            alert("The start time must come before the end time");
            return false;
         }
         sendRequest("POST", url + "index.php/manager/updateScheduledEvent", {
              shift_id : calEvent.rowId, 
              employee_id : calEvent.employeeId,
              day : calEvent.start.toDateString(),
              start_time : $("#start_time").val(),
              end_time : $("#end_time").val(),
          }, function(msg) {
            $("#calendar").fullCalendar("removeEventSource", scheduledEventSource);
            $("#calendar").fullCalendar("addEventSource", scheduledEventSource);
            if($("#statistics").is(":visible")) {
               updateStatistics();
            }
            else if($("#graphs").is(":visible")) {
               updateGraphs();
            }
         }, false);
      }
      return true;
   });
}