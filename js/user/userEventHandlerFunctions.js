$('.leftNav #external-events div.external-event').each(function()
{
   var eventObject =
   {
      title : $.trim($(this).text())
   };

   $(this).data('eventObject', eventObject);

   $(this).draggable(
   {
      zIndex : 999,
      revert : true,
      revertDuration : 0
   });

});

$("#submitMonthForm").click(function()
{
   var date = $('#calendar').fullCalendar("getDate");
   var id = employeeId;
   var min = $("#minHours").val();
   var max = $("#maxHours").val();
   var notes = $("#notes").val();
   $.ajax(
   {
      type : "POST",
      url : url + "index.php/user/updateMonthInfo",
      data :
      {
         employeeId : id,
         date : date.toDateString(),
         min : min,
         max : max,
         notes : notes
      },
      success : function(msg)
      {
         $("#monthInfo").slideUp();
         showClass('.mainButton');
      }
   });
});

$("#coverRequest").click(function()
{
   coverRequest = true;
   $(".bottom-right").notify({
      type: "bangTidy",
      message: { html: "Click on a shift to put it up for cover <button onclick='cancelCoverRequest()' class='btn btn-small btn-primary'>Cancel</button>" },
      closable: false,
      fadeOut: { enabled: false },
      onClose: function() { coverRequest = false; }
   }).show();
});

function cancelCoverRequest()
{
   coverRequest = false;
   $(".bottom-right").remove();
   $("body").append("<div class='notifications bottom-right'></div>");
}

$("#showMonthInfoForm").click(function()
{
   /*hideClass('.mainButton');
   $("#external-events").hide();
   $("#monthInfo").slideDown();*/
   updateInfo();
});

$("#cancelMonthForm").click(function()
{
   /*$("#monthInfo").slideUp();
   showClass(".mainButton");*/
});



$("#tutorial").click(function()
{
   $.prompt(tutorialStates);
});

$("#showEvents").click(function()
{
   if (events === true)
   {
      $("#calendar").fullCalendar("removeEventSource", url + "index.php/user/coEventSource");
      events = false;
   }
   else
   {
      $("#calendar").fullCalendar("addEventSource", url + "index.php/user/coEventSource");
      events = true;
   }
});

$("#showAllStaff").click(function()
{
   if (staff === true)
   {
      $("#calendar").fullCalendar("removeEventSource", url + "index.php/user/allStaffSource");
      staff = false;
   }
   else
   {
      $("#calendar").fullCalendar("addEventSource", url + "index.php/user/allStaffSource");
      staff = true;
   }
});

$("#toggleAvailability").click(function()
{
   if (availability == true)
   {
      $("#calendar").fullCalendar("removeEventSource", url + "index.php/user/availabilityEventSource");
      availability = false;
   }
   else
   {
      $("#calendar").fullCalendar("addEventSource", url + "index.php/user/availabilityEventSource");
      availability = true;
   }
});
$("#printableSchedule").click(function()
{
   window.location = url + "index.php/user/printable?events=" + !events + "&availability=" + availability + "&staff=" + !staff;
});

$(window).load(function()
{
   if (availability === true)
      $("#calendar").fullCalendar("addEventSource", url + "index.php/user/availabilityEventSource");
   if (staff === true)
      $("#calendar").fullCalendar("addEventSource", url + "index.php/user/allStaffSource");
   if (events === true)
      $("#calendar").fullCalendar("addEventSource", url + "index.php/user/coEventSource");
});

$("#downloadCalendar").click(function()
{
   var downloadForm =  "<div style='width: 300px'>" +
         "<form class='form-inline' action='<? echo base_url() ?>index.php/ics' method='get'>" +
            "<fieldset>" +
               "<div class='form-group text-left'>" +
                  "<label for='startDatePicker'>Start Date: </label>" +
                  "<input type='text' name='start' class='form-control' id='startDatePicker' placeholder='YYYY-MM-DD'/>" +
               "</div>" +  
               "<div class='form-group text-left'>" + 
                  "<label for='endDatePicker'>End Date: </label>" + 
                  "<input type='text' name='end' class='form-control' id='endDatePicker' placeholder='YYYY-MM-DD' />" +
               "</div>" +
               "<input type='hidden' value='<? echo $employeeId ?>' name='employeeId' />" +
               "<div class='form-group text-left'>" + 
                  "<div class='checkbox'>" +
                     "<label for='events'>" +
                     "<input type='checkbox' name='events' id='events' />Include Store Events" +
                     "</label>" + 
                  "</div>" +
               "</div>" +
               "<div class='form-group text-left'>" +   
                  "<div class='checkbox'>" +
                     "<label for='google'>" +
                     "<input type='checkbox' name='google' id='google' />Google Calendar Format" +
                     "</label>" +
                  "</div>" +
               "</div>"
            "</fieldset>" +
         "</form>" + 
      "</div>";
   bootbox.confirm(downloadForm, function(result)
   {
      if (result === true)
      {
         $empty_start_check = validateEmpty($("#startDatePicker").val());
         $empty_end_check = validateEmpty($("#endDatePicker").val());
         if($empty_start_check && $empty_end_check)
         {
            $valid_date_arr = validateStartEndDates($("#startDatePicker").val(), $("#endDatePicker").val());
            if ($valid_date_arr[0] === true)
            {
               var start = $("#startDatePicker").val();
               var end = $("#endDatePicker").val();
               var google = ($("#events").is(":checked")) ? "on" : "off";
               var events = ($("#google").is(":checked")) ? "on" : "off";
               var temp_url = url + "index.php/ics?start=" + start + "&end=" + end + "&employeeId=" + employeeId + "&google=" + google + "&events=" + events + "";
               window.location = temp_url;
               return true;
            }
            else
               alert($valid_date_arr[1]);
         }
         else
            alert("Please enter start and end dates");
         return false;
      }
   });
   /*$.prompt($("#downloadForm").html(),
   {
      title: "Download Calendar",
      buttons: {
         "Submit": 1,
         "Cancel": 0
      },
      submit: function(e,v,m,f)
      {
         if(v === 0)
            return true;
         var ret = validateDownloadForm(f);

         if(ret[0] === false)
         {
            alert(ret[1]);
            return false;
         }
         $("#startDatePicker").val(f.start);
         $("#endDatePicker").val(f.end);
         if(f.google)
            $("#google").attr("checked", "checked");
         else
            $("#google").removeAttr('checked');
         if(f.events)
            $("#events").attr("checked", "checked");
         else
            $("#events").removeAttr("checked");
         document.forms.downloadForm.submit();
      }
   });*/
});

/*
$(".leftNav").hover(function() {
   $("body").css("overflow", "hidden");
}, function() {
   $("body").css("overflow", "auto");
}); */