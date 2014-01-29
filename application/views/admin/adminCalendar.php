      <div id='overlay' class='overlay'></div>
      <div class='overlay-container'><h4>Loading... Please Wait</h4>  
         <div class='progress progress-striped active'>  
            <div class='progress-bar progress-bar-info' style='width: 100%;'></div>  
         </div>  
      </div>
      <div class='notifications top-right'></div>
      <div class='notifications top-left'></div>
      <div class='slide'>
         <div class='leftNavOuter'>
            <div class='leftNav'>
               <button class="menu-toggle" id='menu-toggle-outer' type="button" onclick='showLeftNav("admin");'>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
               </button>
               <button class="menu-toggle" id='menu-toggle-inner' type="button" onclick='showLeftNav("admin");'>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
               </button>
               <br>
               <div class="nav nav-pills">
                  <li class='active' onclick="showLeftMenuItem('employees', this);"><a><small>Employees</small></a></li>
                  <li onclick="showLeftMenuItem('options', this);"><a><small>Options</small></a></li>
                  <li onclick="showLeftMenuItem('colorCode', this);"><a><small>Color Code</small></a></li>
                  <li onclick="showLeftMenuItem('templates', this);"><a><small>Templates</small></a></li>
                  <li onclick="initStatistics(this);"><a><small>Statistics</small></a></li>
                  <li onclick="initGraphs(this);"><a><small>Graphs</small></a></li>
               </div>
               <div id='employees' class='leftMenu'>
               <? foreach(json_decode($names) as $employee): ?>
                  <!-- Split button -->
                  <div class="btn-group employee_list">
                     <button type="button" class="btn <? echo $employee->button_class ?> btn-employee" onclick="toggleEmployee('<? echo $employee->employeeId ?>');"><?= $employee->firstName . " " . $employee->lastName[0] ?></button>
                     <button type="button" class="btn <? echo $employee->button_class ?> dropdown-toggle" data-toggle="dropdown">
                        <span class="caret"></span>
                     </button>
                     <input type="checkbox" class="all_employees<? foreach($employee->groups as $group): echo " " . $group->abbr . "_employees"; endforeach; ?>" name="employee_<? echo $employee->employeeId ?>" id="employee_<? echo $employee->employeeId ?>" onclick="toggleEmployee('<? echo $employee->employeeId ?>', event);" />
                     <ul class="dropdown-menu" role="menu">
                        <li><a href="#" onclick="toggleAvailability('<? echo $employee->employeeId ?>');"><table><tr><td style='width: 100px;'>Available</td><td><input type="checkbox" name="available" class="preventDefault all_available<? foreach($employee->groups as $group): echo " " . $group->abbr . "_available"; endforeach; ?>" id="available_<? echo $employee->employeeId ?>" /></td></tr></table></a></li> 
                        <li><a href="#" onclick="toggleBusy('<? echo $employee->employeeId ?>');"        ><table><tr><td style='width: 100px;'>Busy</td>     <td><input type="checkbox" name="busy"      class="preventDefault all_busy<? foreach($employee->groups as $group): echo " " . $group->abbr . "_busy"; endforeach; ?>"           id="busy_<? echo $employee->employeeId ?>"      /></td></tr></table></a></li> 
                        <li><a href="#" onclick="toggleScheduled('<? echo $employee->employeeId ?>');"   ><table><tr><td style='width: 100px;'>Schedule</td> <td><input type="checkbox" name="scheduled" class="preventDefault all_scheduled<? foreach($employee->groups as $group): echo " " . $group->abbr . "_scheduled"; endforeach; ?>" id="scheduled_<? echo $employee->employeeId ?>" /></td></tr></table></a></li> 
                     </ul>
                  </div>
               <? endforeach; ?>
               </div>
               <div id='colorCode' class='leftMenu'>
                  <div class='external-event' style="background: #32CD32;">Available</div>
                  <div class='external-event' style="background: #000000;">Busy</div>
                  <div class='external-event' style="background: #3366CC;">Floor Staff Scheduled</div>
                  <div class='external-event' style="background: #B81900;">SFL Staff Scheduled</div>
                  <div class='external-event' style="background: #EB8F00;">Support Staff Scheduled</div>
                  <div class='external-event' style="background: #480091;">Event</div>
                  <div class='external-event' style="background: #790ead;">Scheduled for Event</div>
               </div>
                
               <div id='templates' class='templates left-bar leftMenu'>
               </div>

               <div id='options' class='left-bar leftMenu'>
                  <!-- all employees -->            
                  <div class="btn-group all">
                     <button type="button" class="btn btn-default btn-employee" onclick="toggleAll();">All Employees</button>
                     <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <span class="caret"></span>
                     </button>
                     <input type="checkbox" name="all_employees" id="all_employees" onclick="toggleAll(event);">
                     <ul class="dropdown-menu" role="menu">
                        <li><a href="#" onclick="toggleAllCategory('available');"><table><tr><td style='width: 100px;'>Available</td><td><input type="checkbox" name="all_available"  id="all_available" class='preventDefault' /></td></tr></table></a></li> 
                        <li><a href="#" onclick="toggleAllCategory('busy');"     ><table><tr><td style='width: 100px;'>Busy</td><td><input      type="checkbox" name="all_busy"       id="all_busy"      class='preventDefault' /></td></tr></table></a></li> 
                        <li><a href="#" onclick="toggleAllCategory('scheduled');"><table><tr><td style='width: 100px;'>Schedule</td><td><input  type="checkbox" name="all_scheduled"  id="all_scheduled" class='preventDefault' /></td></tr></table></a></li> 
                     </ul>
                  </div>
                  <!-- allow for employee groups and list them here... -->
                  <!-- sfl employees -->
                  <? foreach (json_decode($groups) as $group): ?> 
                   <div class="btn-group <? echo $group->abbr ?>">
                     <button type="button" class="btn btn-default btn-employee" onclick="toggleGroup('<? echo $group->abbr ?>', 'employees');"><? echo $group->name ?></button>
                     <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <span class="caret"></span>
                     </button>
                     <input type="checkbox" name="<? echo $group->abbr ?>_employees" id="<? echo $group->abbr ?>_employees" class="group <? echo $group->abbr ?> group_all" onclick="toggleGroup('<? echo $group->abbr ?>', 'employees', event);">
                     <ul class="dropdown-menu" role="menu">
                        <li><a href="#" onclick="toggleGroup('<? echo $group->abbr ?>', 'available');"><table><tr><td style='width: 100px;'>Available</td><td><input type="checkbox" name="<? echo $group->abbr ?>_available"  class="preventDefault group <? echo $group->abbr ?> available" id="<? echo $group->abbr ?>_available"  /></td></tr></table></a></li> 
                        <li><a href="#" onclick="toggleGroup('<? echo $group->abbr ?>', 'busy');"     ><table><tr><td style='width: 100px;'>Busy</td><td><input      type="checkbox" name="<? echo $group->abbr ?>_busy"       class="preventDefault group <? echo $group->abbr ?> busy"      id="<? echo $group->abbr ?>_busy"       /></td></tr></table></a></li> 
                        <li><a href="#" onclick="toggleGroup('<? echo $group->abbr ?>', 'scheduled');"><table><tr><td style='width: 100px;'>Scheduled</td><td><input type="checkbox" name="<? echo $group->abbr ?>_scheduled"  class="preventDefault group <? echo $group->abbr ?> scheduled" id="<? echo $group->abbr ?>_scheduled"  /></td></tr></table></a></li> 
                     </ul>
                  </div>
             <? endforeach ?>
                  <div>
                     <button type="button" class="btn btn-default btn-menu" onclick='toggleEvents();'>Events</button>
                     <input type="checkbox" name="event" id="event_all" class="event" onclick="toggleEvents(event);">
                  </div>
                  <div>
                     <button type="button" class="btn btn-default btn-menu" onclick='toggleDelete();'>Click to Delete</button>
                     <input type='checkbox' name="deleteOption" id='deleteOption' onclick='toggleDelete(event);'>
                  </div>
                  <div id='selectTimeSlot' class='styled-select'>
                     Slot:
                     <select id='selectTime' class='styled-select'>
                        <option value='60'>Hour</option>
                        <option value='30' selected='selected'>Half Hour</option>
                        <option value='15'>15 Minute</option>
                     </select>
                  </div>
               <!-- end options -->
               </div>
               <!-- statistics -->
               <div id='statistics' class='left-bar leftMenu'>
                  <div id="expanded" style='width: 100%;'></div>
                  <div id="summary" style='width: 100%;'></div>
               </div>

               <!-- graphs -->
               <div id='graphs' class='left-bar leftMenu'>
                  <div id="sparkline-title"></div>
                  <div id="sparkline">&nbsp</div>

                  <div><h5>Employees By Group</h5></div>
                  <div id="sparkline-2">&nbsp</div>
               </div>
            <!-- end left menu -->
            </div>
         </div>
      </div>

      <!-- Dropdowns -->
      <div class="dropdown">
          <a href="#"></a>
          <ul id="editEventPopup" class="dropdown-menu" role="menu" aria-labelledby="dropdownmenu2">
            <li rol="presentation"><a role="menuitem" tabindex="-1" href="#" onclick='updateCategory(this);'>Floor<input type="radio" name="category" value="SF" class="rightClickMenuItem" /></a></li>
            <script type='text/javascript'>var global_categories_obj = {
               "select_list" : {
                  "name"     : "category",
                  "label"    : "Category: ",
                   "id"       : "category",
                   "elements" : []
                 }, "additions" : [
             {
                  "type"  : "checkbox",
                  "name"  : "sfl",
                  "id"    : "SFL",
                  "label" : "Sales Floor Leader: ",
                  "abbr"  : "SFL",
             },
             {
                  "name"  : "emptyShift",
                  "id"    : "emptyShift",
                  "label" : "Add Empty Shift: ",
                  "type"  : "checkbox" 
             }]};</script>
            <? foreach ($shift_categories->result() as $row): ?>
            <script type='text/javascript'>global_categories_obj.select_list.elements.push({ "name" : "<? echo $row->category_name; ?>", "abbr" : "<? echo $row->category_abbr ?>"});</script>
              <li rol="presentation"><a role="menuitem" tabindex="-1" href="#" onclick='updateCategory(this);'><? echo $row->category_name ?><input type="radio" name="category" value="<? echo $row->category_abbr ?>" class="rightClickMenuItem" /></a></li>
            <? endforeach; ?>
            <script type="text/javascript">console.log(global_categories_obj);</script>
            <li rol="presentation"><a role="menuitem" tabindex="-1" href="#" onclick='updateCategory(this);'>SFL<input type="checkbox" name="SFL" value="1" id="sflRightClickItem" class='preventDefault' /></a></li>
            <li rol="presentation"><a role="menuitem" tabindex="-1" href="#" onclick='updateCategory(this);'>Support<input type="radio" name="category" value="SP" class="rightClickMenuItem" onclick="clearEditEventPopup();" /></a></li>
         </ul>
      </div>

      <div class='dropdown'>
         <a href="#"></a>
         <ul id='overrideAvailability' class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
            <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Available<input type="radio" name="override" value="Available" /></a></li>
            <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Custom<input    type="radio" name="override" value="Custom"    /></a></li>
            <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Busy<input      type="radio" name="override" value="Busy"      /></a></li>
         </ul>
      </div>

      <div class='dropdown'>
         <a href="#"></a>
         <ul id='overrideAvailability' class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
            <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Available<input type="radio" name="override" value="Available" /></a></li>
            <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Custom<input    type="radio" name="override" value="Custom"    /></a></li>
            <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Busy<input      type="radio" name="override" value="Busy"      /></a></li>
         </ul>
      </div>

      <div id='calendar'></div>
   </body>
</html>
<script type='text/javascript'>
    var tutorial = false;
    var employees = false;
    var busy = false;
    var names =  <? echo $names ?>;
    var unstoredEvents = [];
    var removedEmployees = [-1];
    var mobile = false;
    <? if($mobile == true): ?>
      mobile = true; 
    <? endif; ?>
      
    var global_groups_obj = {}

    var global_employee_id_arr = new Array();
    
    var global_employee_obj = {};
    
    var global_options_obj = {
         "events"  : false,
         "delete"  : false,
         "eventClick" : "standard",
         "prevView" : "month"
    };       

    /*var global_categories_obj = {
         "select_list" : {
             "name"     : "category",
             "label"    : "Category: ",
             "id"       : "category",
             "elements" : [
                  {
                      "name" : "Floor",
                      "abbr" : "SF"
                  },
                  {
                      "name" : "Mens",
                      "abbr" : "M"
                  },
                  {
                      "name" : "Womens",
                      "abbr" : "W"
                  },
                  {
                      "name" : "Cash",
                      "abbr" : "C"
                  },
                  {
                      "name" : "Greeter",
                      "abbr" : "G"
                  },
                  {
                      "name" : "Soccer",
                      "abbr" : "S"
                  },
                  {
                      "name" : "Shoe Sherpa",
                      "abbr" : "SS"
                  },
                  {
                      "name" : "Support",
                      "abbr" : "SP"
                  }
             ]
         },
         "additions" : [
             {
                  "type"  : "checkbox",
                  "name"  : "sfl",
                  "id"    : "SFL",
                  "label" : "Sales Floor Leader: ",
                  "abbr"  : "SFL",
             },
             {
                  "name"  : "emptyShift",
                  "id"    : "emptyShift",
                  "label" : "Add Empty Shift: ",
                  "type"  : "checkbox" 
             }
         ]
    };*/

    for (var j = 0; j < names.length; j++)
    {
         global_employee_id_arr.push(names[j]['employeeId']);
         global_employee_obj[names[j]['employeeId']] = {
             "firstName"    : names[j]['firstName'],
             "lastName"     : names[j]['lastName'],
             "employeeId"   : names[j]['employeeId'],
             "sfl"          : names[j]['sfl'],
             "available"    : false,
             "busy"         : false,
             "scheduled"    : false
         };
         for (var i = 0; i < names[j]["groups"].length; i++) 
         {
             var group = names[j]["groups"][i].abbr;
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
                  global_groups_obj[group]["employees"].push(names[j]["employeeId"]);
             }  
         };
    }
   $(document).ready(function()
   {
      $("input:checked").prop("checked", false);
      // Sets the calendar size based on the page size
       var templates;
       loadTemplates();
       resizeCalendar();
       $("#calendar").fullCalendar("render");
      
    });

</script>

<script src="<? echo base_url() ?>js/utility.js"></script>
<? if ($mobile == true): ?>
<script src="<? echo base_url() ?>js/manager/managerMobileFunctions.js"></script>
<? endif; ?>
<script src="<? echo base_url() ?>js/manager/managerEventHandlerFunctions.js"></script>
<script src="<? echo base_url() ?>js/manager/managerFunctions.js"></script>
<script src="<? echo base_url() ?>js/manager/managerKeypress.js"></script>
<script src="<? echo base_url() ?>js/manager/managerTutorial.js"></script>
<script src="<? echo base_url() ?>js/manager/managerCalendar.js"></script>