<html>
   <head></head>
   <body>

      <div id='calendar'></div>
      
      <div id='external-events' class='leftMenu'>
         <h4>Draggable Events</h4>
         <div class='external-event fc-view' id='available'>
            Available
         </div>
         <div class='external-event fc-view' id='busy'>
            Busy
         </div>
         <div class='external-event fc-view' id='custom'>
            Custom
         </div>
      </div>
      
      <br class='clearLeft'>
      
      <div id='colorCode' class='leftMenu'>
         <h4>Color Code</h4>
         <div class='external-event fc-view' id='availableCode'>
            Available All Day (A)
         </div>
         <div class='external-event fc-view' id='busyCode'>
            Busy All Day (B)
         </div>
         <div class='external-event fc-view' id='customCode'>
            Available for a custom time (C)
         </div>
         <div class='external-event fc-view' id='scheduledCode'>
            Scheduled to work
         </div>
         <div class='external-event fc-view' id='eventCode'>
            Event
         </div>
         <div class='external-event fc-view' id='pickUpCode'>
            Shift available to pick up
         </div>
         <div class='external-event fc-view' id='coverCode'>
            Your shift up for cover
         </div>

      </div>

      <br class='clearLeft'>
      <div id='errors'></div>

      <img src='/images/ajax-loader.gif' id='loading' class='loading'/>
      <br class='clearLeft clearRight'>
      <br class='clearLeft clearRight'>
   </body>
   <script type = 'text/javascript'>
      // All global scope variables
         var url = "<? echo base_url() ?>";
         var employeeId = <?php echo $employeeId ?>;
         var coverRequest = false;
         var events = !<? echo $events ?>;
         var selectedDate;
         var staff = !<? echo $staff ?>;
         var availability = <? echo $availability ?>;
         var resize = <? echo $resize ?>;
         if(resize == true)
         {
            $("#calendar").css("width", $(document).width() - 340);
            $(window).resize(function()
            {
               $("#calendar").css("width", $(document).width() - 340);
            })
         }
         $("#startDatePicker").datepicker({
            showButtonPanel: true,
            prevText: "__",
            nextText: "__",
            dateFormat: "yy-mm-dd"
         });
         $("#endDatePicker").datepicker({
            showButtonPanel: true,
            prevText: "__",
            nextText: "__",
            dateFormat: "yy-mm-dd"
         });

         
   </script>
   <link href='http://fonts.googleapis.com/css?family=Noto+Sans' rel='stylesheet' type='text/css'>
   <script src="<? echo base_url() ?>js/user/userTutorial.js"></script>
   <script src="<? echo base_url() ?>js/user/userKeyPress.js"></script>
   <script src="<? echo base_url() ?>js/user/userCalendar.js"></script>
   <script src="<? echo base_url() ?>js/user/userFunctions.js"></script>
   <script src="<? echo base_url() ?>js/user/userEventHandlerFunctions.js"></script>

</html>
