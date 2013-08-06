<html>
   <head></head>
   <body>
      <div class='notifications bottom-right'></div>
      <div class="alert alert-danger" style='display:none; position: fixed; top: 70px; width: 100%;'>
         <button type="button" class="close" data-dismiss="alert">&times;</button>
      </div>
      <div class="alert alert-success" style='display:none; position: fixed; top: 70px; width: 100%;'>
         <button type="button" class="close" data-dismiss="alert">&times;</button>
      </div>
      
      <div id='calendar'></div>
      <div class='leftNav'>
         <br>
         <div class="nav nav-pills">
            <li class='active' onclick="showLeftMenuItem('newsfeed', this);"><a><small>Newsfeed</small></a></li>
            <li onclick="showLeftMenuItem('external-events', this);"><a><small>Drag Menu</small></a></li>   
            <li onclick="showLeftMenuItem('colorCode', this);"><a><small>Color Code</small></a></li>
         </div>
         <hr style='margin: 10px;'>
         <? echo $newsfeed ?>
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
      </div>

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
         var clipboard = null;
         //var timeout_id;
         if(resize == true)
         {
            $("#calendar").css("width", $(document).width() - 340);
            $(window).resize(function()
            {
               //clearTimeout(timeout_id);
               //timeout_id = window.setTimeout(function(){
               $("#calendar").css("width", $(document).width() - 340);
               $(".leftNav").css("height", $(window).height() - 40);   
               //}, 200);
            });
         }
         /*
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
         });*/

         
   </script>
   <link href='http://fonts.googleapis.com/css?family=Noto+Sans' rel='stylesheet' type='text/css'>
   <script src="<? echo base_url() ?>js/user/userTutorial.js"></script>
   <script src="<? echo base_url() ?>js/user/userKeyPress.js"></script>
   <script src="<? echo base_url() ?>js/user/userCalendar.js"></script>
   <script src="<? echo base_url() ?>js/user/userFunctions.js"></script>
   <script src="<? echo base_url() ?>js/user/userEventHandlerFunctions.js"></script>

</html>
