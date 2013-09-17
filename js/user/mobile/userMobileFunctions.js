function cal_selectMobile (start, end, allDay, jsEvent, view) {
   if (view.name == 'month' || allDay == true)
   {
      setDate(start);
      var form_obj = { 
         "name" : "mobileAvailabilityForm",
         "id"   : "mobileAvailabilityForm",
         "elements" : [
         {
            "type" : "radio",
            "name" : "availability",
            "value" : "Available",
            "id"   : "available_radio",
            "label": "Available All Day",
            "input_class" : "col-1",
            "label_class" : "col-9 control-label label-success radio-label"
         },
         {
            "type" : "radio",
            "name" : "availability",
            "value" : "Busy",
            "id"   : "busy_radio",
            "label": "Busy All Day",
            "input_class" : "col-1",
            "label_class" : "col-9 control-label label-busy radio-label"
         },
         {
            "type" : "radio",
            "name" : "availability",
            "value" : "Custom",
            "id"   : "custom_radio",
            "label": "Custom All Day",
            "input_class" : "col-1",
            "label_class" : "col-9 control-label label-custom radio-label"
         }]
      };
      bootbox.confirm(buildForm(form_obj), function(result) {
         if(result) {
            if($("#mobileAvailabilityForm input[name=availability]:checked").val() == "Custom") {
               var form_obj = {
                  "name" : "mobileCustomForm",
                  "id"   : "mobileCustomForm",
                  "elements" : []
               };
               var default_start = "09:15:00", 
                   default_end = "20:30:00";
               if(start.getDay() == 5) {
                  default_end = "18:30:00";
               }
               else if(start.getDay() == 6) {
                  default_start = "11:15:00";
                  default_end = "17:30:00";
               }
               buildStartEndInputs(form_obj, default_start, default_end, "06:00:00", "21:00:00");
               bootbox.confirm(buildForm(form_obj), function(result) {
                  if(result) {
                     updateEvent("Custom", start, true, $("#start_time").val(), $("#end_time").val());
                  }
               });
            }
            else {
               updateEvent($("#mobileAvailabilityForm input[name=availability]:checked").val(), start, true, null, null);
            }
         }
      });
   }
}
function cal_eventRenderMobile(event, element, view) {
   if (event.category == 'scheduled-pickup' || event.category == 'scheduled-cover') {
      element.css("height", "200px");
   }
   element.css("font-size", "15pt");
}
