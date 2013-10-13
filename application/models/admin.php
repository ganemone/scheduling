<?php

class admin extends CI_Model
{

   function __construct()
   {
      date_default_timezone_set("UTC");
      $this->load->helper('cookie');
   }

   /*	Log in function not implemented by the demo version
    *
    */
   function login($employeeId, $password)
   {
      $query = $this->db->query("SELECT * FROM companies WHERE company = '$employeeId' && password = '$password'");
      if ($query->num_rows() == 1)
      {
         // Sets cookie, should md5 encode latter...
         $cookie = array(
            'name' => 'Company',
            'value' => $employeeId,
            'expire' => '86500'
         );
         $this->input->set_cookie($cookie);
         return $query->result();
      }
      else
      {
         return false;
      }
   }

   /*
    * 	Gets the current list of employees
    */
   function getEmployeeList()
   {
      $sql = "SELECT * FROM employees ORDER BY CASE position WHEN 'SFL' THEN 1 WHEN 'SA' THEN 2 WHEN 'SP' THEN 3 END, firstName ASC";

      $query = $this->db->query($sql);
      $employees = array();
      foreach ($query->result() as $row)
      {
         $employee = array();
         if($row->position == "SFL") {
            $employee["sfl"] = true;
         }
         else {
            $employee["sfl"] = false;
         }

         $employee["firstName"] = $row->firstName;
         $employee["lastName"] = $row->lastName;
         $employee["position"] = $row->position;
         $employee["employeeId"] = $row->id;

         if($row->position == "SFL") {
            $employee["button_class"] = "btn-danger";
         }
         else if($row->position == "SP") {
            $employee["button_class"] = "btn-primary";
         }
         else if($row->position == "SA") {
            $employee["button_class"] = "btn-default";
         }
         else {
            $employee["button_class"] = "btn-info";
         }


         $_query = $this->db->query("SELECT * FROM groups 
            LEFT JOIN employee_groups 
            ON employee_groups.group_id = groups.group_id 
            WHERE employee_groups.employee_id = '$row->id'");
         
         if($_query->num_rows() == 0) {
            $employee["groups"] = array();
         }
         foreach($_query->result() as $_row) {
            $employee["groups"][] = $_row;
         }

         $employees[] = $employee;       
      }
      return $employees;
   }
   function getGraphs($start, $end, $view)
   {
      $query = $this->db->query("SELECT * FROM groups");
      $group_arr = array();
      foreach($query->result() as $row) {
         $group_arr[$row->name] = 0;
      }
      $group_arr["Male"] = 0;
      $group_arr["Female"] = 0;

      $query = $this->db->query("SELECT employees.groups, employees.gender 
         FROM employees 
         LEFT JOIN scheduled 
         ON employees.id = scheduled.employeeId
         WHERE scheduled.day >= '$start' && scheduled.day <= '$end'");

      foreach ($query->result() as $row) {
         
         $emp_group_arr = explode(" ", $row->groups);
         foreach ($emp_group_arr as $group) {
            if(isset($group_arr[$group])) {
               $group_arr[$group] = $group_arr[$group] + 1;
            }
         }

         if($row->gender == "M") {
            $group_arr["Male"]++;
         }
         else {
            $group_arr["Female"]++;
         }
      }

      $query = $this->db->query("SELECT scheduled.employeeId, weekInfo.*, SUM(TIME_TO_SEC(TIMEDIFF(scheduled.end, scheduled.begin))/3600) as sum 
         FROM employees
         LEFT JOIN scheduled
         ON scheduled.employeeId = employees.id
         AND scheduled.day >= '$start' && scheduled.day <= '$end'
         LEFT JOIN weekInfo ON weekInfo.employeeId = scheduled.employeeId
         AND weekInfo.month LIKE '" . date("Y-m", strtotime($start)) . "-%%'
         GROUP BY scheduled.employeeId");
      $emp_met = 0;
      $emp_over = 0;
      $emp_under = 0;
      foreach ($query->result() as $row) {
         $min = $row->minHours;
         $max = $row->maxHours;
   
         if($view == "month") {
            $min *= 4;
            $max *= 4;
         }

         if($row->sum < $min) {
            $emp_under++;
         }
         else if($row->sum > $max) {
            $emp_over++;
         }
         else {
            $emp_met++;
         }

      }
      if($view == "agendaDay")
      {
         $graph_data = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);

         $query = $this->db->query("SELECT begin, end FROM scheduled WHERE day = '$start' && category != 'SP' && event = 0");
         foreach ($query->result() as $row) {
            $start_num = date("H", strtotime($row->begin));
            $end_num = date("H", strtotime($row->end));
            while($start_num < $end_num) {
               $start_num++;
               $graph_data[$start_num - 6]++;
            }
         }
         $graph_obj = array(
            "type"  => "line",
            "width" => 190,
            "height" => 120);
      }
      else {
         $graph_data = array($emp_met, $emp_under, $emp_over);
         $graph_obj = array(
            "type"        => "pie",
            "sliceColors" => array('#468847','#c09853','#b94a48'),
            "height"      => 120,
            "borderColor" => "#000000");
      }
      $graph_data_two = $group_arr;
      $graph_obj_two = array(
         "type" => "bar",
         "height" => 120,
         "barWidth" => 15,
         "chartRangeMin" => 0);

      return array($graph_data, $graph_obj, $graph_data_two, $graph_obj_two);
   }
   function getGroups()
   {
      $query = $this->db->query("SELECT name, abbr FROM groups");
      $ret = array();
      foreach ($query->result() as $row)
      {
         $ret[] = $row;
      }
      return $ret;
   }
   /*
    * 	Gets the scheduled events for the manager calendar
    */
   function getScheduledEventFeed($employee_obj, $start_date, $end_date)
   {
      $json = array();
      $query = $this->db->query("SELECT employees.id, employees.firstName, employees.lastName, employees.position, scheduled.*
      FROM scheduled 
      LEFT JOIN employees ON scheduled.employeeId = employees.id
      WHERE scheduled.day >= '$start_date' && scheduled.day <= '$end_date'
      ORDER BY employees.firstName");

      foreach ($query->result() as $row)
      {
         if($employee_obj->{$row->employeeId}->scheduled)
         {
            $name = $row->firstName . " " . $row->lastName[0];
            $sfl = ($row->sfl == 1) ? "(SFL)" : "";
            $border = ($row->sfl == 1) ? "BLACK" : "";
            $begin = $row->begin;
            $end = $row->end;
            $start = Date('Y-m-d H:i:s', strtotime("$row->day $begin"));
            $_end = Date('Y-m-d H:i:s', strtotime("$row->day $end"));
            $cat = "($row->category)";
            $event = "false";
            if ($row->category == "SP")
               $color = "#EB8F00";
            else if ($row->sfl == 1)
               $color = "#B81900";
            else if ($row->category == "SF" || $row->category == "M" || $row->category == "W" || $row->category == "C" || $row->category == "G" || $row->category == "S" || $row->category == "SS")
               $color = "#3366CC";
            else
            {
               $color = "#790ead";
               $event = "true";
            }

            array_push($json, json_encode(array(
               "title" => "$name $cat $sfl",
               "start" => $start,
               "end" => $_end,
               "allDay" => false,
               'color' => "$color",
               "employeeId" => $row->employeeId,
               'category' => 'scheduled',
               'id' => md5("scheduled$row->id"),
               'rowId' => $row->id,
               'position' => $row->position,
               'area' => $row->category,
               'borderColor' => $border,
               'event' => $event,
               'sfl' => $row->sfl
            )));
         }
      }
      return $json;
   }

   /*
    * Returns the the total event feed for all visible employees.
    */
   function getEventFeed($employee_obj, $start_date, $end_date)
   {
      $query = $this->db->query("SELECT employees.id, employees.firstName, employees.lastName, employees.position, hours.* 
         FROM hours 
         LEFT JOIN employees 
         ON employees.id = hours.employeeId
         WHERE hours.day >= '$start_date'
         AND hours.day <= '$end_date' ORDER BY firstName, lastName");

      $json = $this->getEmptyShifts();
      foreach ($query->result() as $row)
      {
         $name = $row->firstName . " " . $row->lastName[0];
         $date = $row->day;
         $begin = $row->begin;
         $end = $row->end;
         $availability = $row->available;
         $title = $name;
         if ($availability == 'Available' && $employee_obj->{$row->employeeId}->available)
         {
            $color = "#32CD32";
            array_push($json, json_encode(array(
               "title" => $title,
               "start" => $date . " 01:00:01",
               "allDay" => true,
               'color' => $color,
               'employeeId' => $row->employeeId,
               'category' => $availability,
               'id' => md5($availability . $row->id),
               'rowId' => $row->id,
               "position" => $row->position,
            )));
         }
         else if ($availability == 'Busy' && $employee_obj->{$row->employeeId}->busy)
         {
            $color = "BLACK";
            array_push($json, json_encode(array(
               "title" => $title,
               "start" => $date . " 01:00:00",
               "allDay" => true,
               'color' => $color,
               'employeeId' => $row->employeeId,
               'category' => $availability,
               'id' => md5($availability . $row->id),
               'rowId' => $row->id,
               "position" => $row->position
            )));
         }
         else if ($availability == "Custom" && $employee_obj->{$row->employeeId}->available)
         {
            $color = '#32CD32';
            $startTime = Date("g:i a", strtotime($begin));
            $endTime = Date("g:i a", strtotime($end));
            array_push($json, json_encode(array(
               "title" => $title . " " . $startTime . ' - ' . $endTime,
               "start" => $date . ' ' . $begin,
               "end" => $date . ' ' . $end,
               "allDay" => true,
               'color' => $color,
               'employeeId' => $row->employeeId,
               'category' => $availability,
               'id' => md5($availability . $row->id),
               'rowId' => $row->id,
               "position" => $row->position
            )));
         }
      }
      return $json;
   }

   /*
    * 	Gets the employee information about hours per week
    */
   function employeeHPW($employeeId, $date, $dayNum)
   {
      if ($dayNum == 0)
      {
         $weekStart = $date;
      }
      else
      {
         $weekStart = Date('Y-m-d', strtotime($date . "last Sunday"));
      }
      if ($dayNum == 6)
      {
         $weekEnd = $date;
      }
      else
      {
         $weekEnd = Date('Y-m-d', strtotime($date . "next Saturday"));
      }
      $split = explode("-", $weekStart);
      $month = $split[0] . "-" . $split[1];
      $query = $this->db->query("SELECT * FROM scheduled WHERE employeeId = '$employeeId' && day >= '$weekStart' && day <= '$weekEnd'");
      $_query = $this->db->query("SELECT * FROM weekInfo WHERE employeeId = '$employeeId' && month like '$month-%%'");
      $result = array();
      if ($_query->num_rows() < 1)
      {
         $result['desired'] = 'Not Updated';
         $result['notes'] = 'Not Updated';
      }
      else
      {
         $_row = $_query->row();
         $result['desired'] = $_row->minHours . '-' . $_row->maxHours;
         $result['notes'] = $_row->notes;
      }
      $hours = 0;
      foreach ($query->result() as $row)
      {
         $end = explode(":", $row->end);
         $begin = explode(":", $row->begin);

         $h = $end[0] - $begin[0];
         $m = $end[1] - $begin[1];

         $hours += $h;
         $hours += ($m / 60);
      }
      $result['scheduled'] = $hours;
      return $result;

   }

   /*
    * Finalizes the current month being edited by the manager
    */
   function finalizeSchedule($date)
   {
      return $this->db->query("UPDATE settings SET viewable = '$date'");
   }

   function lockSchedule($date)
   {
      return $this->db->query("UPDATE settings SET editable = '$date'");
   }
   function getSettings()
   {
      return $this->db->query("SELECT viewable, editable FROM SETTINGS")->row();
   }
   function createTemplate($employeeId, $title, $date)
   {
      $split = explode("-", $date);
      $endDate = Date('Y-m-d', mktime(0, 0, 0, $split[1], $split[2] + 6, $split[0]));
      $query = $this->db->query("SELECT * FROM scheduled WHERE employeeId = '$employeeId' && day >= '$date' && day <= '$endDate'");
      $_query = $this->db->query("SELECT MAX(templateId) AS templateId FROM templates");
      $return = '';
      $templateId = 0;
      if ($_query->num_rows() == 0)
         $templateId = 1;
      else
         $templateId = $_query->row()->templateId + 1;
      foreach ($query->result() as $row)
      {
         $q = $this->db->query("INSERT INTO templates (templateId, name, day, start, end, category) VALUES ('$templateId', '$title', '$row->day', '$row->begin','$row->end', '$row->category')");
         $return += $q;
      }
      return $return;
   }

   function loadTemplates()
   {
      $query = $this->db->query("SELECT DISTINCT templateId, name FROM templates");

      $json = $days = $starts = $ends = $temp = array();
      $days = $starts = $ends = $cats = array();

      foreach ($query->result() as $row)
      {
         // Could this be refactored into just selecting from the scheduled database..?
         $_query = $this->db->query("SELECT * FROM templates WHERE templateId = '$row->templateId' ORDER BY day");
         $string = "";
         foreach ($_query->result() as $_row)
         {

            $days[] = Date('w', strtotime($_row->day));
            $starts[] = Date("g:i a", strtotime($_row->start));
            $ends[] = Date("g:i a", strtotime($_row->end));
            $cats[] = $_row->category;

            $string .= Date('D', strtotime($_row->day));
            $string .= " ";
            $string .= Date('g:ia', strtotime($_row->start));
            $string .= " ";
            $string .= Date('g:ia', strtotime($_row->end));
            $string .= " ";
            $string .= ($_row->category != "SF") ? "($_row->category)" : "";
            $string .= '<br>';
         }
         array_push($json, json_encode(array(
            "templateId" => $row->templateId,
            'templateName' => $row->name,
            'days' => $days,
            'starts' => $starts,
            'ends' => $ends,
            'categories' => $cats,
            'description' => $string
         )));
         $days = $starts = $ends = $cats = array();
      }
      return $json;
   }

   function deleteTemplates($templateId)
   {
      return $this->db->query("DELETE FROM templates WHERE templateId = '$templateId'");
   }

   function getInfoSpan($start, $end)
   {
      $query = $this->db->query("SELECT SUM(TIME_TO_SEC(TIMEDIFF(scheduled.end, scheduled.begin))/3600*employees.wage) AS wages, 
         SUM(TIME_TO_SEC(TIMEDIFF(end, begin))/3600) AS hours 
         FROM scheduled 
         LEFT JOIN employees 
         ON scheduled.employeeId = employees.id 
         WHERE scheduled.day >= '$start' && scheduled.day <= '$end'");
      $result = $query->row_array();
      $settings = $this->db->query("SELECT editable, viewable FROM settings")->row();
      $goal = $this->getGoal($start, $end, false);

      $str = "<table class='table table-condensed'><tr><td>Finalized Date: </td><td>" . Date("M d, Y", strtotime($settings->viewable)) . "</td>";
      $str .= "<tr><td>Locked Date: </td><td>" . Date("M d, Y", strtotime($settings->editable)) . "</td>";
      $str .= "<tr><td>Total Hours:</td><td> " . number_format($result['hours'], 1, '.', ',') . "</td></tr>";
      $str .= "<tr><td>Total Wages:</td><td>$" . number_format($result['wages'], 1, '.', ',') . "</td></tr>";
      $str .= "<tr><td>Sales Goal:</td><td>$" . number_format($goal, 1, '.', ',') . "</td></tr>";
      $str .= "<tr><td>Labor Percentage:</td><td>";

      if($goal > 0)
      {
         $str .= number_format(($result['wages'] / $goal) * 100, 1, '.', ',');
      }
      else
      {
         $str .= "0";
      }
      $str .= "%</td></tr></table>";
      return $str;
   }

   function getEmployeeHourTotals($start, $end, $view)
   {
      $graph_data = array();
      $array = array();
      $q = $this->db->query("SELECT scheduled.employeeId, employees.firstName, employees.lastName, weekInfo.*, SUM(TIME_TO_SEC(TIMEDIFF(scheduled.end, scheduled.begin))/3600) as sum 
         FROM employees
         LEFT JOIN scheduled
         ON scheduled.employeeId = employees.id
         AND scheduled.day >= '$start' && scheduled.day <= '$end'
         LEFT JOIN weekInfo ON weekInfo.employeeId = scheduled.employeeId
         AND weekInfo.month LIKE '" . date("Y-m", strtotime($start)) . "-%%'
         GROUP BY employees.id");

      $ret = "<table class='table table-striped table-condensed'><tr><th>Name</th><th>Scheduled</th><th>Desired</th></tr>";
      
      foreach ($q->result() as $row)
      {
         $min = $max = 0;
         if($row->minHours == null && $row->maxHours == null)
         {
            $result = $this->db->query("SELECT MAX(month), minHours, maxHours FROM weekInfo WHERE employeeId = '{$row->employeeId}'")->row();
            $min = $result->minHours;
            $max = $result->maxHours;
         }
         else {
            $min = $row->minHours;
            $max = $row->maxHours;
         }
         if ($view == 'month')
         {
            $min *= 4;
            $max *= 4;
         }
         $class = "";
         if($row->sum > $max)
         {
            $class = "danger";
         } 
         else if($row->sum < $min)
         {
            $class = "warning";
         }
         else
         {
            $class = "success";
         }
         $ret .= "<tr><td class='$class'>{$row->firstName}&nbsp{$row->lastName[0]}</td><td class='$class'>" . number_format($row->sum, 2, '.', ',') . "</td><td class='$class'>" . "$min-$max" . "</tr>";
      }
      $ret .= "</table>";

      return $ret;
   }

   function getGoal($startDate, $endDate, $formatted = true)
   {
      $query = $this->db->query("SELECT SUM(goal) FROM goals WHERE date >= '$startDate' && date <= '$endDate'");
      $arr = $query->row_array();
      return ($formatted) ? number_format($arr['SUM(goal)'], 0, ".", ",") : $arr['SUM(goal)'];
   }

   function getGoalArray($startDate, $endDate)
   {
      $json = array();
      while ($startDate <= $endDate)
      {
         $query = $this->db->query("SELECT goal FROM goals WHERE date = '$startDate'");
         if ($query->num_rows() > 0)
         {
            $result = $query->row();
            array_push($json, ' <br><small>($' . number_format($result->goal, 0, '.', ',') . '</small>)');
         }
         else
            array_push($json, " ");
         $startDate = Date("Y-m-d", strtotime("$startDate +1 day"));
      }
      return json_encode($json);
   }

   function getSFLArray()
   {
      $query = $this->db->query("SELECT id FROM employees WHERE position = 'SFL'");
      $array = array();
      foreach ($query->result() as $row)
      {
         array_push($array, $row->id);
      }
      return $array;
   }

   function updateShiftCategory($id, $category)
   {
      $this->db->query("UPDATE scheduled SET category = '$category' WHERE id = '$id'");
      return $this->buildScheduledEventObj($id);
   }

   function updateSFL($id, $sfl)
   {
      $this->db->query("UPDATE scheduled SET sfl = '$sfl' WHERE id = '$id'");
      return $this->buildScheduledEventObj($id);
   }

   function buildScheduledEventObj($event_id)
   {
      $query = $this->db->query("SELECT scheduled.*, event_settings.color, event_settings.border, employees.firstName, employees.lastName, employees.position
         FROM scheduled 
         LEFT JOIN event_settings 
         ON scheduled.category = event_settings.category_abbr
         LEFT JOIN employees
         ON scheduled.employeeId = employees.id
         WHERE scheduled.id='$event_id'");

      foreach($query->result() as $row)
      {
         $_sfl = ($row->sfl == 1) ? "(SFL)" : "";

         $start = Date('Y-m-d H:i:s', strtotime($row->day . " " . $row->begin));
         $end = Date('Y-m-d H:i:s', strtotime($row->day . " " . $row->end));
         $name = $row->firstName . " " . $row->lastName[0];
         $event = false;
         if ($row->sfl == 1)
         {
            $color = "#B81900";
            $border = "#000000";
         }
         else if($row->color == null)
         {
            $color = "#790ead";
            $border = "#790ead";
            $event = true;
         }
         else
         {
            $color = "#" . $row->color;
            $border = "#" . $row->border;
         }

         $id = md5("scheduled" . $row->id);
         $ret = json_encode(array(
            "id"          => md5("scheduled" . $row->id),
            "employeeId"  => $row->employeeId,
            "category"    => "scheduled",
            "rowId"       => $row->id,
            "title"       => $name . " (" . $row->category . ") " . $_sfl,
            "start"       => "$start",
            "end"         => "$end",
            "color"       => $color,
            "area"        => $row->category,
            "position"    => $row->position,
            "sfl"         => $row->sfl,
            "allDay"      => false,
            "event"       => $event,
            'borderColor' => $border
         ));
      }
      return $ret;
   }
   function buildAvailabilityEventObj($event_id)
   {
      $query = $this->db->query("SELECT hours.*, event_settings.color, event_settings.border, employees.firstName, employees.lastName, employees.position
         FROM hours 
         LEFT JOIN event_settings 
         ON hours.availability = event_settings.category_name
         LEFT JOIN employees
         ON hours.employeeId = employees.id
         WHERE hours.id='$event_id'");

      $row = $query->result();
      if($row->start == "00:00:00")
      {
         $start = date("Y-m-d", strtotime($row->day));
         $end = null;
         $all_day = true;
      }
      else
      {
         $start = date("Y-m-d H:i:s", strtotime($row->day . " " . $row->start));
         $end = date("Y-m-d H:i:s", strtotime($row->day . " " . $row->end));
         $all_day = false;
      }

      $id = md5("scheduled" . $row->id);
      return json_encode(array(
         "id"          => md5("scheduled" . $row->id),
         "employeeId"  => $row->employeeId,
         "category"    => "scheduled",
         "rowId"       => $row->id,
         "title"       => $row->firstName . " " . $row->lastName[0] . " (" . $row->category . ") " . $_sfl,
         "start"       => $start,
         "end"         => $end,
         "color"       => "#" + $row->color,
         "area"        => $row->category,
         "position"    => $row->position,
         "sfl"         => $row->sfl,
         "allDay"      => $all_day,
         "event"       => $row->event,
         'borderColor' => "#" + $row->border
      ));
   }

   function coEventSource($start_date, $end_date, $array = array())
   {
      $query = $this->db->query("SELECT * FROM events WHERE date >= '$start_date' && date <= '$end_date'");
      foreach ($query->result() as $row)
      {
         $array[] = json_encode(array(
            "id" => md5("events$row->id"),
            "rowId" => $row->id,
            "editTitle" => $row->title,
            "editStart" => $row->start,
            "editEnd" => $row->end,
            "title" => $row->title . " (" . date("g:i a", strtotime($row->start)) . "-" . date("g:i a", strtotime($row->end)) . ")",
            "allDay" => true,
            "start" => $row->date . " " . $row->start,
            "end" => $row->date . " " . $row->end,
            "category" => "events",
            "color" => "#480091",
            "editDate" => $row->date,
            "location" => $row->location,
            "repeating" => $row->repeating
         ));
      }
      return $array;
   }

   function addExternalEvent($title, $date, $start, $end, $location, $repeating, $finalDate)
   {
      $finalDate = date("Y-m-d", strtotime($finalDate));
      if ($repeating == 0)
      {
         $query = $this->db->query("INSERT INTO events (title, date, start, end, location, repeating) VALUES ('$title', '$date','$start', '$end','$location','$repeating')");
      }
      else
      {
         //for ($i = 0; $i < 12; $i++)
         $increment = 0;
         $_date = Date("Y-m-d", strtotime("$date + $increment days"));
         $counter = 1;
         $time = strtotime($date);
         if($repeating == "1/7") {
            $repeating = 1/7;
         }
         while ($_date <= $finalDate && $counter <= 52)
         {
            $query = $this->db->query("INSERT INTO events (title, date, start, end, location, repeating) VALUES ('$title', '$_date', '$start', '$end', '$location', '$repeating')");
            $increment += (7 * $repeating);
            $_date = Date("Y-m-d", strtotime("+$increment days", $time));
            $counter++;
         }
      }
      return true;
   }

   function editExternalEvent($event_id, $update_arr)
   {
      return $this->db->where("id", $event_id)->update("events", $update_arr);
   }
   function scheduleEmployeeTemplate($employeeId_arr, $day_arr, $begin_arr, $end_arr, $category_arr)
   {
      $ret = array();
      $refetch = false;
      for ($j=0; $j < count($day_arr); $j++) 
      { 
         $this->scheduleEmployeeFloor($employeeId_arr, $day_arr[$j], $begin_arr[$j], $end_arr[$j], $category_arr[$j], 0);
      }
      return true;
   }
   function scheduleEmployeeFloor($employee_arr, $day, $begin, $end, $category, $sfl)
   {
       $ret = array();
      foreach ($employee_arr as $employeeId) 
      {
         $ids = $this->getOverlappingEvents($employeeId, $day, $begin, $end);
         $refetch = (count($ids) > 0) ? true : false;
         
         $calIds = array();
         foreach ($ids as $value)
         {
            $calIds[] = md5("scheduled$value");
            $this->db->query("DELETE FROM scheduled WHERE id='$value'");
         }
         $_query = $this->db->query("INSERT INTO scheduled (employeeId, day, begin, end, category, SFL) values ('$employeeId','$day','$begin','$end','$category','$sfl')");
         $query = $this->db->query('SELECT LAST_INSERT_ID()');
         $result = $query->result_array();
         $id = $result[0]['LAST_INSERT_ID()'];
         $query = $this->db->query("SELECT firstName, lastName, position FROM employees WHERE id = '$employeeId'");
         $row = $query->row();
         $array = array(
            "id" => $id,
            "firstName" => $row->firstName,
            "lastName" => $row->lastName,
            "position" => $row->position,
            "employeeId" => $employeeId,
            "date" => $day,
            "start" => $begin,
            "end" => $end,
            "event" => "false",
            "category" => $category,
            "sfl" => $sfl
         );
         $ret[] = json_encode(array($this->makeScheduledJSON($array), $refetch));
      }
      return json_encode($ret);
   }

   function scheduleEmployeeEvent($employee_arr, $date, $start, $end, $eventTitle)
   {
      $ret = array();
      foreach ($employee_arr as $employeeId) 
      {
         $ids = $this->getOverlappingEvents($employeeId, $date, $start, $end);
         $refetch = (count($ids) > 0) ? true : false;
         $calIds = array();
         foreach ($ids as $value)
         {
            $calIds[] = md5("scheduled$value");
            $this->db->query("DELETE FROM scheduled WHERE id='$value'");
         }
         $_query = $this->db->query("INSERT INTO scheduled (employeeId, day, begin, end, category, SFL) values ('$employeeId','$date','$start','$end','$eventTitle','0')");
         $query = $this->db->query("SELECT firstName, lastName, position FROM employees WHERE id='$employeeId'");
         $row = $query->row();
         $query = $this->db->query('SELECT LAST_INSERT_ID()');
         $result = $query->result_array();
         $id = $result[0]['LAST_INSERT_ID()'];
         $array = array(
            "id" => $id,
            "firstName" => $row->firstName,
            "lastName" => $row->lastName,
            "position" => $row->position,
            "employeeId" => $employeeId,
            "date" => $date,
            "start" => $start,
            "end" => $end,
            "event" => "true",
            "category" => $eventTitle,
            "sfl" => 0
         );
         $ret[] = json_encode(array($this->makeScheduledJSON($array), $refetch));
      }
      return json_encode($ret);
   }
   function updateScheduledEvent($shift_id, $employee_id, $date, $start, $end)  
   {
      $ids = $this->getOverlappingEvents($employee_id, $date, $start, $end, $shift_id);

      $this->db->where("id", $shift_id);
      $this->db->update("scheduled", array("day" => $date, "begin" => $start, "end" => $end));
   
      if(count($ids) > 0)
      {
         $this->db->where_in("id", $ids);
         $this->db->delete("scheduled");
      }

      return json_encode($ids);
   }

   function makeScheduledJSON($infoArray)
   {
      $_sfl = ($infoArray['sfl'] == 1) ? "(SFL)" : "";
      $border = ($infoArray['sfl'] == 1) ? "BLACK" : "";
      $start = Date('Y-m-d H:i:s', strtotime($infoArray['date'] . " " . $infoArray['start']));
      $end = Date('Y-m-d H:i:s', strtotime($infoArray['date'] . " " . $infoArray['end']));
      $name = $infoArray['firstName'] . " " . $infoArray['lastName'][0];
      if ($infoArray['category'] == "SP")
         $color = "#EB8F00";
      else if ($infoArray['sfl'] == 1)
      {
         $color = "#B81900";
         $border = "BLACK";
      }
      else if ($infoArray['category'] == "SF" || $infoArray['category'] == "M" || $infoArray['category'] == "W" || $infoArray['category'] == "C" || $infoArray['category'] == "G" || $infoArray['category'] == "S" || $infoArray['category'] == "SS")
         $color = "#3366CC";
      else
         $color = "#790ead";
      $id = md5("scheduled" . $infoArray['id']);
      $json = json_encode(array(
         "id" => $id,
         "employeeId" => $infoArray['employeeId'],
         "category" => "scheduled",
         "rowId" => $infoArray['id'],
         "title" => $name . " (" . $infoArray['category'] . ") " . $_sfl,
         "start" => "$start",
         "end" => "$end",
         "color" => $color,
         "area" => $infoArray['category'],
         "position" => $infoArray['position'],
         "sfl" => $infoArray['sfl'],
         "allDay" => false,
         "event" => $infoArray['event'],
         'borderColor' => $border,
      ));
      return $json;
   }

   function getOverlappingEvents($employeeId, $day, $start, $end, $shift_id = 0)
   {
      $query = $this->db->query("SELECT id, day, begin, end FROM scheduled WHERE employeeId = '$employeeId' && day = '$day' && id != '$shift_id'");
      $ids = array();
      foreach ($query->result() as $row)
      {
         if($start >= $row->begin && $start < $row->end)
         {
            $ids[] = $row->id;
         }
         else if($row->begin >= $start && $row->begin < $end)
         {
            $ids[] = $row->id;
         }
      }
      return $ids;
   }

   function addEmptyShift($start, $end, $date, $category, $sfl)
   {
      $query = $this->db->query("INSERT INTO emptyShifts (start, end, date, category, sfl) VALUES ('$start','$end','$date','$category','$sfl')");
      return $query;
   }

   function getEmptyShifts()
   {
      $query = $this->db->query("SELECT * FROM emptyShifts");
      $array = array();
      foreach ($query->result() as $row)
      {
         $prettyName = $this->getPositionName($row->category);
         $event = "false";
         if ($prettyName == "event")
         {
            $event = "true";
            $prettyName = $row->category;
         }
         $prettyStart = Date("g:i a", strtotime($row->start));
         $prettyEnd = Date("g:i a", strtotime($row->end));
         $array[] = json_encode(array(
            "title" => "Empty Shift ($row->category)",
            "description" => "We need more hours on $row->date.  Would you like to pick up a shift working from $prettyStart until $prettyEnd on $row->date?",
            "start" => $row->date . ' ' . $row->start,
            "end" => $row->date . ' ' . $row->end,
            "allDay" => false,
            "tip" => "We need more hours on this date. Click on the shift to help out!",
            "color" => "RED",
            "category" => "emptyShifts",
            "position" => "$row->category",
            "event" => "$event",
            "id" => md5("emptyShifts$row->id"),
            "rowId" => $row->id
         ));
      }
      return $array;
   }

   function getPositionName($abbreviation)
   {
      switch ($abbreviation)
      {
         case "SF" :
            return "Floor";
         case "SS" :
            return "Shoe Sherpa";
         case "S" :
            return "Soccer";
         case "G" :
            return "Greeter";
         case "W" :
            return "Womens";
         case "M" :
            return "Mens";
         case "SP" :
            return "Support";
         case "C" :
            return "Cash";
      }
      return "event";
   }

   function getHoursLeft($startDate, $endDate, $employees)
   {
      $returnArray = array();
      for ($i = 0; $i < count($employees); $i++)
      {
         $returnArray[$employees[$i]] = $this->employeeHPW($employees[$i], $startDate, 0);
      }
      return json_encode($returnArray);
   }

   function getShiftsPerHour()
   {
      $sph = array();
      $startDate = Date("Y-m-d", strtotime("-1 month"));
      $endDate = Date("Y-m-d", strtotime("+2 months"));
      $tempDate = $startDate;
      $counter = 0;
      while ($tempDate <= $endDate && $counter < 100)
      {
         $sph[$tempDate] = array(
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0
         );
         $tempDate = Date("Y-m-d", strtotime("+1 day " . $tempDate));
         $counter++;
      }

      $query = $this->db->query("SELECT begin, end, day, category FROM scheduled WHERE day >= '$startDate' && day <= '$endDate' && category != 'SP' ORDER BY day");
      foreach ($query->result() as $row)
      {
         $positionName = $this->getPositionName($row->category);
         if ($positionName != "event")
         {
            $startHour = Date("H", strtotime($row->begin) + 59 * 60);
            $endHour = Date("H", strtotime($row->end));
            $day = Date("Y-m-d", strtotime($row->day));
            for ($i = $startHour - 6; $i <= $endHour - 6; $i++)
            {
               $sph[$day][$i]++;
            }
         }
      }
      return $sph;
   }

   function overrideAvailability($id, $availability, $start, $end)
   {
      $query = $this->db->query("UPDATE hours SET available = '$availability', begin = '$start', end='$end' WHERE id='$id'");
      return $query;
   }
   
   function initializeGoals()
   {
      $start_date = Date("Y-m-d", strtotime("-3 months"));
      $end_date = Date("Y-m-d", strtotime("+3 months"));
      $ret_arr = array();
      $query = $this->db->query("SELECT * FROM goals WHERE date >= '$start_date' && date <= '$end_date'");
      foreach($query->result() as $row)
      {
         $ret_arr[$row->date] = '$' . number_format($row->goal, 0, '.', ',');
      }
      while ($start_date <= $end_date)
      {
         if(!isset($ret_arr[$start_date]))
            $ret_arr[$start_date] = "$0";
         $start_date = Date("Y-m-d", strtotime($start_date . " +1 day"));
      }
      
      return $ret_arr;
   }
   
   function getShiftCategories()
   {
      return $this->db->query("SELECT category_name, category_abbr FROM event_settings WHERE category_abbr != 'SF' && category_abbr != 'SP' && category_abbr != 'SFL' && category_abbr != 'A' && category_abbr != 'B' && category_abbr != 'C'");
   }

}
