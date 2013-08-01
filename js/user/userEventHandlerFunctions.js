$('#external-events div.external-event').each(function()
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

$("#showDraggable").click(function()
{
   $(".leftMenu").each(function()
   {
      if ($(this).attr("id") != 'external-events')
         $(this).hide();
   });
   $("#external-events").slideToggle();
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
   if (availability === true)
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

$("#showColorCode").click(function()
{
   $(".leftMenu").each(function()
   {
      if ($(this).attr("id") != 'colorCode')
         $(this).hide();
   });
   $("#colorCode").slideToggle();
});
$("#showNewsfeed").click(function()
{
   $(".leftMenu").each(function()
   {
      if ($(this).attr("id") != 'newsfeed')
         $(this).hide();
   });
   coverRequest = false;
   $("#newsfeed").toggle();
});

$("#downloadCalendar").click(function()
{
   $.prompt($("#downloadForm").html(),
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
   });
});
