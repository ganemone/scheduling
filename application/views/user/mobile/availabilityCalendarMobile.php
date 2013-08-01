<html>
   <head></head>
   <body>
      <div id='shiftCoverRequest' title='Cover Request'>
         Click on the shift you would like to put up for covering.
      </div>
      <div id='coverRequestConfirmation' title='Confirmation'>
         Are you sure you would like to request a cover for this shift? (Note: the shift will remain on your schedule, and you will still be responsible for it until someone claims it).
      </div>
      <div id='customTimes' title='Custom Shift'>
         <label>Start:
            <select id='start' name='start'>
               <option value='08:00:00'>8:00am</option>
               <option value='08:15:00'>8:15am</option>
               <option value='08:30:00'>8:30am</option>
               <option value='08:45:00'>8:45am</option>
               <option value='09:00:00'>9:00am</option>
               <option value='09:15:00'>9:15am</option>
               <option value='09:30:00'>9:30am</option>
               <option value='09:45:00'>9:45am</option>
               <option value='10:00:00'>10:00am</option>
               <option value='10:15:00'>10:15am</option>
               <option value='10:30:00'>10:30am</option>
               <option value='10:45:00'>10:45am</option>
               <option value='11:00:00'>11:00am</option>
               <option value='11:15:00'>11:15am</option>
               <option value='11:30:00'>11:30am</option>
               <option value='11:45:00'>11:45am</option>
               <option value='12:00:00'>12:00pm</option>
               <option value='12:15:00'>12:15pm</option>
               <option value='12:30:00'>12:30pm</option>
               <option value='12:45:00'>12:45pm</option>
               <option value='13:00:00'>1:00pm</option>
               <option value='13:15:00'>1:15pm</option>
               <option value='13:30:00'>1:30pm</option>
               <option value='13:45:00'>1:45pm</option>
               <option value='14:00:00'>2:00pm</option>
               <option value='14:15:00'>2:15pm</option>
               <option value='14:30:00'>2:30pm</option>
               <option value='14:45:00'>2:45pm</option>
               <option value='15:00:00'>3:00pm</option>
               <option value='15:15:00'>3:15pm</option>
               <option value='15:30:00'>3:30pm</option>
               <option value='15:45:00'>3:45pm</option>
               <option value='16:00:00'>4:00pm</option>
               <option value='16:15:00'>4:15pm</option>
               <option value='16:30:00'>4:30pm</option>
               <option value='16:45:00'>4:45pm</option>
               <option value='17:00:00'>5:00pm</option>
               <option value='17:15:00'>5:15pm</option>
               <option value='17:30:00'>5:30pm</option>
               <option value='17:45:00'>5:45pm</option>
               <option value='18:00:00'>6:00pm</option>
               <option value='18:15:00'>6:15pm</option>
               <option value='18:30:00'>6:30pm</option>
               <option value='18:45:00'>6:45pm</option>
               <option value='19:00:00'>7:00pm</option>
               <option value='19:15:00'>7:15pm</option>
               <option value='19:30:00'>7:30pm</option>
               <option value='19:45:00'>7:45pm</option>
               <option value='20:00:00'>8:00pm</option>
            </select></label>
         <br>
         <label>End:
            <select id='end' name='end'>
               <option value='09:00:00'>9:00am</option>
               <option value='09:15:00'>9:15am</option>
               <option value='09:30:00'>9:30am</option>
               <option value='09:45:00'>9:45am</option>
               <option value='10:00:00'>10:00am</option>
               <option value='10:15:00'>10:15am</option>
               <option value='10:30:00'>10:30am</option>
               <option value='10:45:00'>10:45am</option>
               <option value='11:00:00'>11:00am</option>
               <option value='11:15:00'>11:15am</option>
               <option value='11:30:00'>11:30am</option>
               <option value='11:45:00'>11:45am</option>
               <option value='12:00:00'>12:00pm</option>
               <option value='12:15:00'>12:15pm</option>
               <option value='12:30:00'>12:30pm</option>
               <option value='12:45:00'>12:45pm</option>
               <option value='13:00:00'>1:00pm</option>
               <option value='13:15:00'>1:15pm</option>
               <option value='13:30:00'>1:30pm</option>
               <option value='13:45:00'>1:45pm</option>
               <option value='14:00:00'>2:00pm</option>
               <option value='14:15:00'>2:15pm</option>
               <option value='14:30:00'>2:30pm</option>
               <option value='14:45:00'>2:45pm</option>
               <option value='15:00:00'>3:00pm</option>
               <option value='15:15:00'>3:15pm</option>
               <option value='15:30:00'>3:30pm</option>
               <option value='15:45:00'>3:45pm</option>
               <option value='16:00:00'>4:00pm</option>
               <option value='16:15:00'>4:15pm</option>
               <option value='16:30:00'>4:30pm</option>
               <option value='16:45:00'>4:45pm</option>
               <option value='17:00:00'>5:00pm</option>
               <option value='17:15:00'>5:15pm</option>
               <option value='17:30:00'>5:30pm</option>
               <option value='17:45:00'>5:45pm</option>
               <option value='18:00:00'>6:00pm</option>
               <option value='18:15:00'>6:15pm</option>
               <option value='18:30:00'>6:30pm</option>
               <option value='18:45:00'>6:45pm</option>
               <option value='19:00:00'>7:00pm</option>
               <option value='19:15:00'>7:15pm</option>
               <option value='19:30:00'>7:30pm</option>
               <option value='19:45:00'>7:45pm</option>
               <option value='20:00:00'>8:00pm</option>
               <option value='20:15:00'>8:15pm</option>
               <option value='20:30:00' selected="selected">8:30pm</option>
            </select></label>
      </div>
      <div id='buttons'>
         <ul>
            <li>
               <button id='showMonthInfoForm' class='mainButton'>
                  Update Info
               </button>
            </li>
            <li>
               <button id='coverRequest' class='mainButton'>
                  Shift Cover
               </button>
            </li>
            <li>
               <button id='showNewsfeed' class='mainButton'>
                  Newsfeed
               </button>
            </li>
            <li>
               <button id='copyWeek' class='mainButton' onclick='copyWeek();'>
                  Copy Week
               </button>
            </li>
            <li>
               <button id='pasteWeek' class='mainButton' onclick="pasteWeek();">
                  Paste Week
               </button>
            </li>
            <li>
               <button id='showEvents' class='mainButton'>
                  Toggle Store Events
               </button>
            </li>
            <li>
               <button id='showAllStaff' class='mainButton'>
                  Toggle All Staff
               </button>
            </li>
            <li>
               <button id='toggleAvailability' class='mainButton'>
                  Toggle Availability
               </button>
            </li>
            <li>
               <button id='downloadCalendar' class='mainButton'>
                  Download Calendar
               </button>
            </li>
            <li>
               <a href="#" id='backToTop' style="font-size:16pt;">Back to Top</a>
            </li>
         </ul>
      </div>
      <br class='clearLeft'>
         <form name='downloadForm' title='Download Calendar' id='downloadForm' action='<? echo base_url() ?>index.php/ics' method='get' style='display:none'>
            <input type='text' name='start' id='startDatePicker' placeholder='Start Date'/>
            <input type='text' name='end' id='endDatePicker' placeholder="End Date" />
            <input type='hidden' value='<? echo $employeeId ?>' name='employeeId' /><br>
            <label for='events'>Include Store Events</label><input type='checkbox' name='events' id='events' /><br>
            <label for='google'>Google Calendar Format</label><input type='checkbox' name='google' id='google' />
         </form>
      <br class='clearLeft clearRight'>
      <div id='calendar'></div>
      <div id='monthInfo'>
         Min Weekly Hours: <input type='text' name='min' id='minHours'></input><br>
         Max Weekly Hours: <input type='text' name='max' id='maxHours'></input><br>
         <textarea cols='35' rows='8' name='notes' id='notes' placeholder='Enter Notes for this month here...'></textarea></td>
      </div>
      <br class='clearLeft'>
      <div id='successAlert' title='Alert!'></div>
      <div id='pickUpShift' title='Pick Up Shift'></div>
      <img src='/images/ajax-loader.gif' id='loading' class='loading'/>
      <br class='clearLeft clearRight'>
      <br class='clearLeft clearRight'>
      <div id='enterAvailability'>
         <form id='availabilityForm'>
            <table>
            <tr><td>Available:</td><td><input type='radio' class='mobileRadio' name='availability' value='Available'></input></td>
            <td><button id='closeButton' onclick='return closeForm();'>X</button></td></tr>               
            <tr><td>Busy:</td><td><input type='radio' class='mobileRadio' name='availability' value='Busy'></input></td></tr>
            <tr><td>Custom:</td><td><input type='radio' class='mobileRadio' name='availability' value='Custom' onclick="focusStart();"></input></td></tr>
            <tr><td>Start:</td><td><? echo $start_select ?></td></tr>
            <tr><td>End:</td><td><? echo $end_select ?></input></td></tr>
            <tr><td></td><td><button id='availabilitySubmit' onclick='return updateAvailability()'>Submit</button></td></tr>
            </table>
         </form>
      </div>
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
         if(resize == true)
         {
            $("#calendar").css("width", $(document).width() - 320);
         }
         $(".mainButton").each(function() {
            $(this).button();
         });
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

         $("#downloadCalendar").click(function()
         {
              $("#downloadForm").dialog({
              modal: true,
              buttons : {
                 "Submit": function() {
                    $(this).dialog('close');
                    document.downloadForm.submit();
                 },
                 "Cancel": function() {
                    $(this).dialog('close');
                 }
              },
              });
         });
   </script>
   <link href='http://fonts.googleapis.com/css?family=Noto+Sans' rel='stylesheet' type='text/css'>
   <script src="<? echo base_url() ?>js/user/userTutorial.js"></script>
   <script src="<? echo base_url() ?>js/user/mobile/userCalendarMobile.js"></script>
   <script src="<? echo base_url() ?>js/user/mobile/userFunctionsMobile.js"></script>
   <script src="<? echo base_url() ?>js/user/mobile/userEventHandlerFunctionsMobile.js"></script>

</html>
