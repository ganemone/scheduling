$("#coverRequest").click(function()
{
   coverRequest = true;
   $(".leftMenu").each(function()
   {
      $(this).hide();
   });
   $("#shiftCoverRequest").dialog(
   {
      autoOpen : false,
      position : [5, 250],
      zIndex : 1000,
      width : 200,
      height : 200,
      buttons :
      {
         "Cancel" : function()
         {
            $(this).dialog('close');
            coverRequest = false;
         }
      }
   });
   $("#shiftCoverRequest").dialog('open');
});

$("#showMonthInfoForm").click(function()
{
   $("#monthInfo").dialog(
   {
      zIndex : 1000,
      width : 300,
      height : 300,
      buttons :
      {
         "Submit" : function()
         {
            $(this).dialog("close");
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
         },
         "Cancel" : function()
         {
            $(this).dialog("close");
         }
      }
   });
});

$("#tutorial").click(function()
{
   $.prompt(tutorialStates);
});

$("#showEvents").click(function()
{
   if (events == true)
   {
      $("#calendar").fullCalendar("removeEventSource", url + "index.php/user/coEventSource");
      events = false;
      $("#calendar").fullCalendar("rerenderEvents");
   }
   else
   {
      $("#calendar").fullCalendar("addEventSource", url + "index.php/user/coEventSource");
      events = true;
   }
});

$("#showAllStaff").click(function()
{
   if (staff == true)
   {
      $("#calendar").fullCalendar("removeEventSource", url + "index.php/user/allStaffSource");
      staff = false;
      $("#calendar").fullCalendar("rerenderEvents");

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
   if (availability == true)
      $("#calendar").fullCalendar("addEventSource", url + "index.php/user/availabilityEventSource");
   if (staff == true)
      $("#calendar").fullCalendar("addEventSource", url + "index.php/user/allStaffSource");
   if (events == true)
      $("#calendar").fullCalendar("addEventSource", url + "index.php/user/coEventSource");
});

$("#showNewsfeed").click(function()
{
   $(".leftMenu").each(function()
   {
      if ($(this).attr("id") != 'newsfeed')
         $(this).hide();
   });
   coverRequest = false;
   $("#newsfeed").show();
   location.hash = "backToTop";
});

$("#backToTop").click(function()
{
   location.hash = "logout";
});


