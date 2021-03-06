<html>
   <head></head>
   <body>
      <div id='overlay' class='overlay'></div> 
      <div class='overlay-container'><h4>Loading... Please Wait</h4>  
         <div class='progress progress-striped active'>  
            <div class='progress-bar progress-bar-info' style='width: 100%;'></div>  
         </div>
      </div>
      <div class='notifications top-right'></div>
      <div class='notifications top-left'></div>
      <div id='calendar'></div>
      <div class='slide'>
         <div class='leftNavOuter'>
            <div class='leftNav'>
               <button class="menu-toggle" id='menu-toggle-outer' type="button" onclick="showLeftNav('resize');">
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
               </button>
               <br>
               <div class="nav nav-pills">
                  <li class='active' onclick="showLeftMenuItem('newsfeed', this);"><a><small>Newsfeed</small></a></li>
                  <li onclick="showLeftMenuItem('external-events', this);"><a><small>Drag Menu</small></a></li>   
                  <li onclick="showLeftMenuItem('colorCode', this);"><a><small>Color Code</small></a></li>
                  <li onclick="showLeftNav('resize');">
                     <button class="menu-toggle" id='menu-toggle-inner' type="button" onclick="showLeftNav('resize');">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                     </button>
                  </li>
               </div>
               <hr style='margin: 10px;'>
               <? if (isset($newsfeed)): ?>
               <? echo $newsfeed ?>
               <? endif; ?>
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
         </div>
      </div>

      <br class='clearLeft clearRight'>
      <br class='clearLeft clearRight'>
   </body>
   <script type = 'text/javascript'>
      // All global scope variables
         var employeeId = <?php echo $employeeId ?>;
         var coverRequest = false;
         var events = !<? echo $events ?>;
         var staff = !<? echo $staff ?>;
         var availability = <? echo $availability ?>;
         var resize = <? echo $resize ?>;
         var editable_date = "<? echo $editable_date ?>";

         var mobile = false;
         <? if($mobile == true): ?>
            mobile = true; 
         <? endif; ?>
      
         var clipboard = null;
         var selectedDate;
         var availability_due = new Date();
         availability_due.setMonth(availability_due.getMonth() + 3);
         availability_due.setDate(-1);
         availability_due.setDate(6 - availability_due.getDay() + availability_due.getDate());

         var alerts = "<? echo $alerts ?>";
         if(alerts != "none") {
            bootbox.alert(alerts, function() {
               $('.top-left').notify({
                  type: "important",
                  message: { text: "Availability is due until " + availability_due.toDateString() + " at the end of the month." },
                  fadeOut: { enabled: false }
               }).show();
            });
         }
         else {
            $('.top-left').notify({
               type: "important",
               message: { text: "Availability is due until " + availability_due.toDateString() + " at the end of the month." },
               fadeOut: { enabled: false }
               }).show();
         }

         


         //var timeout_id;
   </script>
   <script src="<? echo base_url() ?>js/utility.js"></script>
   <? if($mobile == true): ?>
   <script src="<? echo base_url() ?>js/user/mobile/userMobileFunctions.js"></script>
   <? endif; ?>
   <script src="<? echo base_url() ?>js/user/userFunctions.js"></script>
   <script src="<? echo base_url() ?>js/user/userTutorial.js"></script>
   <script src="<? echo base_url() ?>js/user/userKeyPress.js"></script>
   <script src="<? echo base_url() ?>js/user/userCalendar.js"></script>
   <script src="<? echo base_url() ?>js/user/userEventHandlerFunctions.js"></script>   

</html>
