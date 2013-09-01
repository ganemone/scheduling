<html>
   <head></head>
   <body>
      <div id='overlay' class='overlay'>  
      </div> 
         <div class='overlay-container'><h4>Loading... Please Wait</h4>  
         <div class='progress progress-striped active'>  
            <div class='progress-bar progress-bar-info' style='width: 100%;'></div>  
         </div>  
      </div>
      <div class='notifications top-right'></div>
<<<<<<< HEAD
=======
      <div id='calendar'></div>
>>>>>>> 8c8203b1c97c118b81bb63531dac5a3ead007367
      <div class='leftNav adminNav'>
         <div class="nav nav-pills">
            <li class='active' onclick="showLeftMenuItem('employees', this);"><a><small>Employees</small></a></li>
            <li onclick="showLeftMenuItem('options', this);"><a><small>Options</small></a></li>
            <li onclick="showLeftMenuItem('colors', this);"><a><small>Color Code</small></a></li>
            <li onclick="showLeftMenuItem('templates', this);"><a><small>Templates</small></a></li>
         </div>
         <div id='employees' class='leftMenu'>
         <? foreach(json_decode($names) as $employee): ?>
            <!-- Split button -->
<<<<<<< HEAD
            <div class="btn-group employee_list">
              <button type="button" class="btn btn-default btn-employee" onclick="toggleEmployee('<? echo $employee->id ?>');"><?= $employee->firstName . " " . $employee->lastName[0] ?></button>
              <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                <span class="caret"></span>
              </button>
              <input type="checkbox" class="all_employees<? foreach($employee->groups as $group): echo " " . strtolower(str_replace(" ", "_", trim($group))) . "_employees"; endforeach; ?>" name="employee_<? echo $employee->id ?>" id="employee_<? echo $employee->id ?>" onclick="toggleEmployee('<? echo $employee->id ?>', event);">
              <ul class="dropdown-menu" role="menu">
                <li><a href="#" onclick="toggleAvailability('<? echo $employee->id ?>');">Available <input type="checkbox" name="available" class="all_available<? foreach($employee->groups as $group): echo " " . strtolower(str_replace(" ", "_", trim($group))) . "_available"; endforeach; ?>" id="available_<? echo $employee->id ?>"  onclick="toggleAvailability('<? echo $employee->id ?>');" /></a></li> 
                <li><a href="#" onclick="toggleBusy('<? echo $employee->id ?>');"        >Busy      <input type="checkbox" name="busy"      class="all_busy<? foreach($employee->groups as $group): echo " " . strtolower(str_replace(" ", "_", trim($group))) . "_busy"; endforeach; ?>"      id="busy_<? echo $employee->id ?>"       onclick="toggleBusy('<? echo $employee->id ?>');"         /></a></li> 
                <li><a href="#" onclick="toggleScheduled('<? echo $employee->id ?>');"   >Schedule  <input type="checkbox" name="scheduled" class="all_scheduled<? foreach($employee->groups as $group): echo " " . strtolower(str_replace(" ", "_", trim($group))) . "_scheduled"; endforeach; ?>" id="scheduled_<? echo $employee->id ?>"  onclick="toggleScheduled('<? echo $employee->id ?>');"  /></a></li> 
=======
            <div class="btn-group">
              <button type="button" class="btn btn-default btn-employee" onclick="toggleEmployee('<? echo $employee->id ?>')"><?= $employee->firstName . " " . $employee->lastName[0] ?></button>
              <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                <span class="caret"></span>
              </button>
              <input type="checkbox" name="employee_<? echo $employee->id ?>" id="employee_<? echo $employee->id ?>">
              
              <ul class="dropdown-menu" role="menu">
                <li><a href="#" onclick="toggleAvailability('<? echo $employee->id ?>');">Available <input type="checkbox" name="available" id="available_<? echo $employee->id ?>"  onclick="toggleAvailability('<? echo $employee->id ?>');" /></a></li> 
                <li><a href="#" onclick="toggleBusy('<? echo $employee->id ?>');"        >Busy      <input type="checkbox" name="busy"      id="busy_<? echo $employee->id ?>"       onclick="toggleBusy('<? echo $employee->id ?>');"         /></a></li> 
                <li><a href="#" onclick="toggleScheduled('<? echo $employee->id ?>');"   >Schedule  <input type="checkbox" name="scheduled"  id="scheduled_<? echo $employee->id ?>" onclick="toggleScheduled('<? echo $employee->id ?>');"  /></a></li> 
>>>>>>> 8c8203b1c97c118b81bb63531dac5a3ead007367
              </ul>
            </div>
         <? endforeach; ?>
         </div>
         <div id='colors' class='leftMenu'>
            <h4>Color Code</h4>
            <div style="background: #32CD32">
               Available
            </div>
            <div style="background: #000000">
               Busy
            </div>
            <div style="background: #3366CC">
               Floor Staff Scheduled
            </div>
            <div style="background: #B81900">
               SFL Staff Scheduled
            </div>
            <div style="background: #EB8F00">
               Support Staff Scheduled
            </div>
            <div style="background: #480091">
               Event
            </div>
            <div style="background: #790ead">
               Scheduled for Event
            </div>
         </div>
         <div id='templates' class='templates left-bar leftMenu'>
            <h4>Templates</h4>
         </div>

      <!--<div id='deleteConfirmation' title='Confirmation'>
         Would You like to delete this event?
      </div>
      <div id='finalizeConfirmation' title='Confirmation'>
         Are you sure you would like to finalize the schedule for
      </div>
      -->
         <div id='options' class='left-bar leftMenu'>
<<<<<<< HEAD
            <!-- all employees -->            
             <div class="btn-group all">
              <button type="button" class="btn btn-default btn-employee" onclick="toggleAll();">All Employees</button>
              <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                <span class="caret"></span>
              </button>
              <input type="checkbox" name="all_employees" id="all_employees" onclick="toggleAll(event);">
              <ul class="dropdown-menu" role="menu">
                <li><a href="#" onclick="toggleAllCategory('available');">Available <input type="checkbox" name="all_available"  id="all_available" onclick="toggleAllCategory('available');" /></a></li> 
                <li><a href="#" onclick="toggleAllCategory('busy');">Busy           <input type="checkbox" name="all_busy"       id="all_busy"      onclick="toggleAllCategory('busy');"         /></a></li> 
                <li><a href="#" onclick="toggleAllCategory('scheduled');">Schedule  <input type="checkbox" name="all_scheduled"  id="all_scheduled" onclick="toggleAllCategory('scheduled');"    /></a></li> 
              </ul>
            </div>
            <!-- allow for employee groups and list them here... -->
            <!-- sfl employees -->
            <? foreach (json_decode($groups) as $group): ?> 
            <? $safe_group = strtolower(str_replace(" ", "_", trim($group))); ?>
             <div class="btn-group <? echo $safe_group ?>">
              <button type="button" class="btn btn-default btn-employee" onclick="toggleGroup('<? echo $safe_group ?>', 'employees');"><? echo $group ?></button>
              <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                <span class="caret"></span>
              </button>
              <input type="checkbox" name="<? echo $safe_group ?>_employees" id="<? echo $safe_group ?>_employees" class="group <? echo $safe_group ?> group_all" onclick="toggleGroup('<? echo $safe_group ?>', 'employees', event);">
              <ul class="dropdown-menu" role="menu">
                <li><a href="#" onclick="toggleGroup('<? echo $safe_group ?>', 'available');">Available <input type="checkbox" name="<? echo $safe_group ?>_available"  class="group <? echo $safe_group ?> available" id="<? echo $safe_group ?>_available" onclick="toggleGroup('<? echo $safe_group ?>', 'available');"    /></a></li> 
                <li><a href="#" onclick="toggleGroup('<? echo $safe_group ?>', 'busy');">Busy           <input type="checkbox" name="<? echo $safe_group ?>_busy"       class="group <? echo $safe_group ?> busy"      id="<? echo $safe_group ?>_busy"      onclick="toggleGroup('<? echo $safe_group ?>', 'busy');"         /></a></li> 
                <li><a href="#" onclick="toggleGroup('<? echo $safe_group ?>', 'scheduled');">Schedule  <input type="checkbox" name="<? echo $safe_group ?>_scheduled"  class="group <? echo $safe_group ?> scheduled" id="<? echo $safe_group ?>_scheduled" onclick="toggleGroup('<? echo $safe_group ?>', 'scheduled');"    /></a></li> 
              </ul>
            </div>
         <? endforeach ?>
            <!-- Create Event Types Here and list them -->
            <div class="btn-group">
              <button type="button" class="btn btn-default btn-employee" onclick="toggleEvents('all');">Events</button>
              <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                <span class="caret"></span>
              </button>
              <input type="checkbox" name="event" id="event_all" class="event" onclick="toggleEvents('all', event);">
              <ul class="dropdown-menu" role="menu">
                <li><a href="#" onclick="toggleEvents('group_one');">Group One    <input type="checkbox" name="event_group_one"   class="event" id="event_group_one"   onclick="toggleEvents('group_one');"   /></a></li> 
                <li><a href="#" onclick="toggleEvents('group_two');">Group Two    <input type="checkbox" name="event_group_two"   class="event" id="event_group_two"   onclick="toggleEvents('group_two');"   /></a></li> 
                <li><a href="#" onclick="toggleEvents('group_three');">Group Three<input type="checkbox" name="event_group_three" class="event" id="event_group_three" onclick="toggleEvents('group_three');" /></a></li> 
              </ul>
            </div>
            <div>
              <button type="button" class="btn btn-default" onclick='toggleDelete();'>Click to Delete</button>
              <input type='checkbox' name="deleteOption" id='deleteOption' onclick='toggleDelete(e);'>
            </div>

<!--            <table class='table'>
=======
            <h4>Options</h4>
            <table class='table'>
>>>>>>> 8c8203b1c97c118b81bb63531dac5a3ead007367
               <tr>
                  <td><label>All <b>E</b>mployees:</td><td>
                  <input type='checkbox' name='toggleAll' id='toggleAll' checked='checked'>
                  </input></label></td>
               </tr>
               <tr>
                  <td><label>S<b>F</b>L Employees:</td><td>
                  <input type="checkbox" name="sflOption" id="sflOption">
                  </input></label></td>
               </tr>
               <tr>
                  <td><label><b>B</b>usy:</td><td>
                  <input type="checkbox" name='busyOption' id='busyOption' checked='checked'>
                  </input></label></td>
               </tr>
               <tr>
                  <td><label><b>S</b>cheduled:</td><td>
                  <input type="checkbox" name="scheduledOption" id="scheduledOption" checked="checked">
                  </input></label></td>
               </tr>
               <tr>
                  <td><label><b>A</b>vailable:</td><td>
                  <input type="checkbox" name="availableOption" id="availableOption" checked="checked">
                  </input></label></td>
               </tr>
               <tr>
                  <td><label>Events:</td><td>
                  <input type="checkbox" name="eventOption" id="eventOption" checked="checked">
                  </input></label></td>
               </tr>
               <tr>
                  <td><label>Click to Delete:</td><td>
                  <input type="checkbox" name="deleteOption" id="deleteOption">
                  </input></label></td>
               </tr>
<<<<<<< HEAD
            </table>-->
=======
            </table>
>>>>>>> 8c8203b1c97c118b81bb63531dac5a3ead007367
            <div id='selectTimeSlot' class='styled-select'>
               Slot:
               <select id='selectTime' class='styled-select'>
                  <option value='60'>Hour</option>
                  <option value='30' selected='selected'>Half Hour</option>
                  <option value='15'>15 Minute</option>
               </select>
            </div>
            <div>
               Sorting:
               <select id='sorting' class='slyled-select' onchange="updateSort();">
                  <option value='firstName' selected='selected'>First Name</option>
                  <option value='employeeId'>Employee Id</option>
                  <option value='standard'>Calendar Default</option>
               </select>
            </div>
         </div>
      </div>
      <input type='radio' name='group' id='hiddenRadio'>
      </input>

      <div id='employeeInfo' class='employeeInfo' title=''>
         <div id='desired'></div>
         <div id='current'></div>
         <div id='notes'></div>
      </div>
      <div id='middleContainer'></div>
      <div id='headerButtons'></div>

      <img src="/images/ajax-loader.gif" id='loading' class='loading'/>

      <div id='customTimes' class='styled-select' title='Custom Shift'>
         <table>
            <tr>
               <td>Start:</td>
               <td>
               <select id='start' name='start'>
                  <option value="06:00:00">6:00am</option>
                  <option value='06:15:00'>6:15am</option>
                  <option value='06:30:00'>6:30am</option>
                  <option value='06:45:00'>6:45am</option>
                  <option value="07:00:00">7:00am</option>
                  <option value='07:15:00'>7:15am</option>
                  <option value='07:30:00'>7:30am</option>
                  <option value='07:45:00'>7:45am</option>
                  <option value="08:00:00">8:00am</option>
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
                  <option value='20:15:00'>8:15pm</option>
                  <option value='20:30:00'>8:30pm</option>
               </select></td>
            </tr>
            <tr>
               <td>End:</td>
               <td>
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
                  <option value='20:30:00'>8:30pm</option>
                  <option value='20:45:00'>8:45pm</option>
                  <option value='21:00:00'>9:00pm</option>
               </select></td>
            </tr>
         </table>
      </div>

      <button id='viewInfo'>
         Get Info
      </button>

      <div id="editEventPopup" class='contextMenu'>
         <table>
            <tr>
               <td>Floor</td><td>
               <input type="radio" name="category" value="SF" class="rightClickMenuItem" id='floorOption' />
               </td>
            </tr>
            <tr>
               <td>Mens</td><td>
               <input type="radio" name="category" value="M" class="rightClickMenuItem" />
               </td>
            </tr>
            <tr>
               <td>Womens</td><td>
               <input type="radio" name="category" value="W" class="rightClickMenuItem" />
               </td>
            </tr>
            <tr>
               <td>Cash</td><td>
               <input type="radio" name="category" value="C" class="rightClickMenuItem" />
               </td>
            </tr>
            <tr>
               <td>Greeter</td><td>
               <input type="radio" name="category" value="G" class="rightClickMenuItem" />
               </td>
            </tr>
            <tr>
               <td>Soccer</td><td>
               <input type="radio" name="category" value="S" class="rightClickMenuItem" id='soccerOption'/>
               </td>
            </tr>
            <tr>
               <td>Shoe Sherpa</td><td>
               <input type="radio" name="category" value="SS" class="rightClickMenuItem" id='shoeOption'/>
               </td>
            </tr>
            <tr>
               <td>SFL</td><td>
               <input type="checkbox" name="SFL" value="1" id="sflRightClickItem" />
               </td>
            </tr>
            <tr>
               <td>Support</td><td>
               <input type="radio" name="category" value="SP" class="rightClickMenuItem" id='supportOption' onclick="clearEditEventPopup();" />
               </td>
            </tr>
         </table>
      </div>
      <div id='employeeRightClickDiv' style='display:none; background:rgba(0,0,0,0.5);'>
         Hide Availability
         <input type='checkbox' name='removeAvailability' id='toggleEmployeeAvailability' onclick="toggleEmployeeAvailability();">
      </div>
      <!--<form id='coEventForm' style="display: none">
      <input type='text' name='eventName' />
      <input type='text' id='datePickerStart' />
      <input type='text' id='datePickerEnd' />
      </form>-->
      <div id='addCoEvent' style="display:none">
         <input type="text" value="" placeholder="Event Title..." name="co_title" id='coEventTitle'  />
         <input type="text" value="" placeholder="Event Date..." name="co_eventDate" id="coEventDatePicker"  />
         <br style="clear:left;">
         <input type="text" value="" placeholder="Location..." name="co_location" id="coEventLocation"  />
         <label for="coEventRepeating" >Repeat:</label>
         <select name="co_repeating" id="coEventRepeating" onchange="repeatOptionChanged();">
            <option value="0">None</option>
            <option value="1">Weekly</option>
            <option value="2">Bi-Weekly</option>
            <option value="4">Monthly</option>
         </select>
         <br style="clear:left;">
         <input type="text" value="" placeholder="Repeat Until..." name="co_eventRepeatEnd" id="coEventRepeatEnd" />
         <br style="clear:left;">
         <label for="coEventStart">Start</label>
         <select id='coEventStart' name='start'>
            <option value="06:00:00">6:00am</option>
            <option value='06:15:00'>6:15am</option>
            <option value='06:30:00'>6:30am</option>
            <option value='06:45:00'>6:45am</option>
            <option value="07:00:00">7:00am</option>
            <option value='07:15:00'>7:15am</option>
            <option value='07:30:00'>7:30am</option>
            <option value='07:45:00'>7:45am</option>
            <option value="08:00:00">8:00am</option>
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
            <option value='20:15:00'>8:15pm</option>
            <option value='20:30:00'>8:30pm</option>
            <option value='20:45:00'>8:45pm</option>
            <option value='21:00:00'>9:00pm</option>
         </select>
         <label for="coEventEnd"> End</label>
         <select id='coEventEnd' name='end'>
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
            <option value='20:30:00'>8:30pm</option>
            <option value='20:45:00'>8:45pm</option>
            <option value='21:00:00' selected="selected">9:00pm</option>
         </select>
         <br style="clear:left;">
      </div>
      <div id='overrideAvailability' class='contextMenu'>
         Available<input type="radio" name='availability' value="Available" class='overrideRightClick'><br>
         Custom<input type="radio" name='availability' value="Custom" class='overrideRightClick'><br>
         Busy<input type="radio" name="availability" value="Busy" class='overrideRightClick'>
      </div>
      <div id='calendar'></div>
   </body>
</html>
<script type='text/javascript'>
   var peoplePerHour = <? echo $peoplePerHour ?>;
   var tutorial = false;
   var url = "<? echo base_url(); ?>";
   var employees = false;
   var busy = false;
   var names =  <? echo $names ?>;
   var unstoredEvents = [];
   var removedEmployees = [-1];
<<<<<<< HEAD
   
   var global_groups_obj = {}

   var global_employee_id_arr = new Array();
   
   var global_employee_obj = new Array(); 
   
   var global_options_obj = {
      "events"  : true,
      "delete"  : false
   };       

   for (var j = 0; j < names.length; j++)
   {
      global_employee_id_arr.push(names[j]['id']);
=======
   var global_employee_obj = new Array(); 
   var global_options_obj = {
      "availability" : false,
      "busy"         : false,
      "scheduled"    : false,
      "events"       : false
   };

   for (var j = 0; j < names.length; j++)
   {
>>>>>>> 8c8203b1c97c118b81bb63531dac5a3ead007367
      global_employee_obj[names[j]['id']] = {
         "firstName"    : names[j]['firstName'],
         "lastName"     : names[j]['lastName'],
         "employeeId"   : names[j]['employeeId'],
<<<<<<< HEAD
         "sfl"          : names[j]['sfl'],
         "available"    : false,
         "busy"         : false,
         "scheduled"    : false
      };
      for (var i = 0; i < names[j]["groups"].length; i++) 
      {
         var group = names[j]["groups"][i];
         if(typeof group != "undefined" && group != "")
         {
            if(typeof global_groups_obj[group] == "undefined")
            {
               global_groups_obj[group] = {
                  "available" : false,
                  "busy"      : false,
                  "scheduled" : false,
                  "employees" : new Array()
               }
            }
            global_groups_obj[group]["employees"].push(names[j]["id"]);
         }  
      };
   }
=======
         "availability" : false,
         "busy"         : false,
         "scheduled"    : false
      };
   }
   console.log(global_employee_obj);
</script>

<script src="<? echo base_url() ?>js/utility.js"></script>
<script src="<? echo base_url() ?>js/manager/managerEventHandlerFunctions.js"></script>
<script src="<? echo base_url() ?>js/manager/managerFunctions.js"></script>
<script src="<? echo base_url() ?>js/manager/managerKeypress.js"></script>
<script src="<? echo base_url() ?>js/manager/managerTutorial.js"></script>
<script src="<? echo base_url() ?>js/manager/managerCalendar.js"></script>
<!--<script src="<? echo base_url() ?>jsMin/manager/manager.min.js"></script>-->
<script type="text/javascript">
>>>>>>> 8c8203b1c97c118b81bb63531dac5a3ead007367
   $(document).ready(function()
   {
      $("#deleteOption").attr('checked', false);
      $("#toggleAll").attr('checked', false);
      $("#busyOption").attr('checked', true);
      $("#sflOption").attr('checked', false);
      $("#availableOption").attr('checked', true);
      $("#scheduledOption").attr('checked', true);

      // Sets the calendar size based on the page size
      $("#calendar").css("width", $(document).width() - $("#options").width() - 100);
      $("#coEventRepeatEnd").attr("disabled", "disabled");

      var templates;
      loadTemplates();
   });
   $("#coEventRepeatEnd").datepicker(
   {
      showButtonPanel : true,
      prevText : "__",
      nextText : "__",
      dateFormat : "yy-mm-dd"
   }); 
</script>

<script src="<? echo base_url() ?>js/utility.js"></script>
<script src="<? echo base_url() ?>js/manager/managerEventHandlerFunctions.js"></script>
<script src="<? echo base_url() ?>js/manager/managerFunctions.js"></script>
<script src="<? echo base_url() ?>js/manager/managerKeypress.js"></script>
<script src="<? echo base_url() ?>js/manager/managerTutorial.js"></script>
<script src="<? echo base_url() ?>js/manager/managerCalendar.js"></script>
<!--<script src="<? echo base_url() ?>jsMin/manager/manager.min.js"></script>-->

