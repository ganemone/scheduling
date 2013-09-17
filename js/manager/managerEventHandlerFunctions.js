$(window).resize(function() {
   resizeCalendar();
})
$(document).click(function(e) {
   if ($("#editEventPopup").is(":visible")) {
      var offset = $("#editEventPopup").offset();
      var width = $("#editEventPopup").width();
      var height = $("#editEventPopup").height();
      if (e.pageX < offset.left || e.pageX > offset.left + width) {
         $("#editEventPopup").hide();
      }
      else if (e.pageY < offset.top || e.pageY > offset.top + height) {
         $("#editEventPopup").hide();
      }
   }
   if ($("#overrideAvailability").is(":visible")) {
      var offset = $("#overrideAvailability").offset();
      var width = $("#overrideAvailability").width();
      var height = $("#overrideAvailability").height();
      if (e.pageX < offset.left || e.pageX > offset.left + width) {
         $("#overrideAvailability").hide();
      }
      else if (e.pageY < offset.top || e.pageY > offset.top + height) {
         $("#overrideAvailability").hide();
      }
   }
});
$("#overrideAvailability li").click(function() {
   var eventObject = $(this).data("event");
   var element = $(this).data("element");
   var category = $(this).children("a").children("input").val();
   var form_obj = {
      "name" : "override_form", "id" : "override_form", "style" : "width: 420px;", "elements" : [{
         "type" : "hidden", "name" : "id", "id" : "id", "value" : eventObject.rowId
      }, {
         "type" : "hidden", "name" : "category", "id" : "category", "value" : category
      }]
   };
   if (category == "Custom") {
      var start = "06:00:00";
      var end = "21:00:00";
      if (eventObject.category == "Custom") {
         start = eventObject.start.getHours() + ":" + eventObject.start.getMinutes() + ":00";
         end = eventObject.end.getHours() + ":" + eventObject.end.getMinutes() + ":00";
      }
      buildStartEndInputs(form_obj, start, end, "06:00:00", "21:00:00");

      form_obj["title"] = eventObject.title.split("-")[0];

      var form = buildForm(form_obj);

      bootbox.confirm(form, function(result) {
         if (result) {
            sendRequest("POST", url + "index.php/manager/overrideAvailability", buildPostDataObj("#override_form"), function(msg) {
                  $("#calendar").fullCalendar("refetchEvents");
                  successMessage("Overrided Employee Availability");
            }, false);
         }
      });
   }
   else {
      sendRequest("POST", url + "index.php/manager/overrideAvailability", {
         "id" : eventObject.rowId, "category" : category
         }, function(msg) {
            $("#calendar").fullCalendar("refetchEvents");
            successMessage("Overrided Employee Availability");
      }, false);
   }
   $("#overrideAvailability").hide();
});

$('select').each(function() {
   $(this).wrap('<div class="styled-select" />');
});

/* Finalizes the schedule for the current month. This causes the availability to no longer be editable
 *    by employees, and makes their scheduled events visible.
 */
$("#finalize").click(function() {
   var date = $("#calendar").fullCalendar('getDate');
   // This is where we set the editable dates for the employees!
   var dates = getStartAndEndDates($("#calendar").fullCalendar('getView').name, date);
   var endDate = dates.endDate;
   endDate.setDate(endDate.getDate() + 1);
   bootbox.confirm("Are you sure you would like to finalize the schedule until " + dates.endDate.toDateString(), function(result) {
      if(result) {
         sendRequest("POST", url + "index.php/manager/finalize", {
            start : endDate.toDateString()
         }, 
         function(msg) {
            successMessage("Finalized schedule until " + endDate.toDateString());
         }, false);
      }
   });
});

$("#logOut").click(function() {
   window.location = url + "index.php/manager/logOut";

});

$("#home").click(function() {
   window.location = url + "index.php/user";
});

$("#template").click(function() {
   if ($("#calendar").fullCalendar('getView').name == "basicWeek") {
      var startDate = $("#calendar").fullCalendar('getDate');
      startDate.setDate(startDate.getDate() - startDate.getDay());
      var endDate = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate() + 6);
      var form_obj = {
         "name" : "make_template",
         "id"   : "make_template",
         "style": "width: 440px",
         "elements" : [
            {
               "type" : "text",
               "name" : "template_name",
               "id"   : "template_name",
               "label" : "Template Name: ",
               "placeholder" : "Enter template name here...",
            }
         ]
      };

      buildTemplateEmployeeSelectObj(startDate, endDate, form_obj);

      bootbox.confirm(buildForm(form_obj), function(result)
      {
         if(result)
         {
            createTemplate($("#template_employee_id").val(), $("#template_name").val());
         }
      });
   }
   else {
      bootbox.alert("Please switch to the week view before making a template.");
   }
});

// Function for updating time slots in the agendaWeek and agendaDay Views
$("#selectTime").change(function() {
   var view = $("#calendar").fullCalendar('getView').name;
   var date = $("#calendar").fullCalendar('getDate');
   renderCalendar(parseInt($(this).val()), view, date);
});

$("#addCOEvent").click(function() {
   $("#coEventDatePicker").datepicker({
      "dateFormat" : "yy-mm-dd", showButtonPanel : true, prevText : "__", nextText : "__"
   });
   $("#addCoEvent").dialog({
      buttons : {
         "Submit" : function() {
            var title = $("#coEventTitle").val();
            var date = $("#coEventDatePicker").val();
            var repeating = $("#coEventRepeating").val();
            var location = $("#coEventLocation").val();
            var start = $("#coEventStart").val();
            var end = $("#coEventEnd").val();
            var endRepeat = $("#coEventRepeatEnd").val();
            var start_split = start.split(":");
            var end_split = end.split(":");
            if (title == "") {
               alert("You Must Enter a Title");
               return false;
            }
            else if (date == "") {
               alert("You Must Enter a Date");
               return false;
            }
            else if (repeating > 0 && endRepeat == "") {
               alert("You must enter an End Repeat Value");
               return false;
            }
            $(this).dialog('close');

            title += "(" + timeToString(new Date(0, 0, 0, start_split[0], start_split[1], start_split[2])) + "-" + timeToString(new Date(0, 0, 0, end_split[0], end_split[1], end_split[2])) + ")";
            addCOEvent(title, date, start, end, location, repeating, endRepeat);
         }, "Cancel" : function() {
            $(this).dialog('close');
         }
      }
   });
});

$("#statistics").on("show", function()
{
   alert("shown");
});
