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
      $query = $this->db->query("SELECT firstName, lastName, id, position FROM employees ORDER BY position, firstName");
      $employees = array();
      foreach ($query->result() as $row)
      {
         $employees[] = $row;        
         $class = "btn-info";
         if ($row->position == "SA")
            $class = "btn-default";
         else if ($row->position == "SFL")
            $class = "btn-danger";
      }
      return $employees;
   }
   /*
    * 	Gets the scheduled events for the manager calendar
    */
   function getScheduledEventFeed()
   {
      $query = $this->db->query("SELECT id, firstName, lastName, position FROM employees ORDER BY firstName");
      $_json = array();
      $busy = $this->input->cookie("busy");
      foreach ($query->result() as $row)
      {
         if ((($this->input->cookie("display") == "true") && !($this->input->cookie($row->id) == 'false')) or ($this->input->cookie($row->id) == "true"))
         {
            $name = "$row->firstName " . $row->lastName[0];

            $_query = $this->db->query("SELECT * FROM scheduled WHERE employeeId = '$row->id'");
            foreach ($_query->result() as $_row)
            {
               $sfl = ($_row->sfl == 1) ? "(SFL)" : "";
               $border = ($_row->sfl == 1) ? "BLACK" : "";
               $begin = $_row->begin;
               $end = $_row->end;
               $start = Date('Y-m-d H:i:s', strtotime("$_row->day $begin"));
               $_end = Date('Y-m-d H:i:s', strtotime("$_row->day $end"));
               $cat = "($_row->category)";
               $event = "false";
               if ($_row->category == "SP")
                  $color = "#EB8F00";
               else if ($_row->sfl == 1)
                  $color = "#B81900";
               else if ($_row->category == "SF" || $_row->category == "M" || $_row->category == "W" || $_row->category == "C" || $_row->category == "G" || $_row->category == "S" || $_row->category == "SS")
                  $color = "#3366CC";
               else
               {
                  $color = "#790ead";
                  $event = "true";
               }

               array_push($_json, json_encode(array(
                  "title" => "$name $cat $sfl",
                  "start" => $start,
                  "end" => $_end,
                  "allDay" => false,
                  'color' => "$color",
                  "employeeId" => $row->id,
                  'category' => 'scheduled',
                  'id' => md5("scheduled$_row->id"),
                  'rowId' => $_row->id,
                  'position' => $row->position,
                  'area' => $_row->category,
                  'borderColor' => $border,
                  'event' => $event,
                  'sfl' => $sfl
               )));
            }
         }
      }
      return $_json;
   }

   /*
    * Returns the the total event feed for all visible employees.
    */
   function getEventFeed($employee_obj)
   {
      $query = $this->db->query("SELECT id, firstName, lastName, position FROM employees");
      $json = $this->getEmptyShifts();
      $busy = $this->input->cookie('busy');
      foreach ($query->result() as $row)
      {
         $name = "$row->firstName " . $row->lastName[0];
         $_query = $this->db->query("SELECT * FROM hours WHERE employeeId = '$row->id'");
         // Create json stuff and add it here...
         foreach ($_query->result() as $_row)
         {
            $date = $_row->day;
            $begin = $_row->begin;
            $end = $_row->end;
            $availability = $_row->available;

            $title = $name;
            if ($availability == 'Available')
            {
               $color = "#32CD32";
               array_push($json, json_encode(array(
                  "title" => $title,
                  "start" => $date . " 01:00:01",
                  "allDay" => true,
                  'color' => $color,
                  'employeeId' => $row->id,
                  'category' => $availability,
                  'id' => md5($availability . $row->id),
                  'rowId' => $_row->id,
                  "position" => $row->position,
               )));
            }
            else if ($availability == 'Busy')
            {
               if ($busy == 'true')
               {
                  $color = "BLACK";
                  array_push($json, json_encode(array(
                     "title" => $title,
                     "start" => $date . " 01:00:00",
                     "allDay" => true,
                     'color' => $color,
                     'employeeId' => $row->id,
                     'category' => $availability,
                     'id' => md5($availability . $row->id),
                     'rowId' => $_row->id,
                     "position" => $row->position
                  )));
               }
            }
            else
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
                  'employeeId' => $row->id,
                  'category' => $availability,
                  'id' => md5($availability . $row->id),
                  'rowId' => $_row->id,
                  "position" => $row->position
               )));
            }
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
   function finalizeSchedule($start)
   {
      $date = Date('Y-m-d', strtotime($start));
      $this->db->query("DELETE FROM settings WHERE id > 0");
      $this->db->query("INSERT INTO settings (viewable) VALUES ('$date')");
      return $date;
   }

   function createTemplate($employeeId, $title, $date)
   {
      $startDate = Date('Y-m-d', strtotime($date));
      $split = explode("-", $startDate);
      $endDate = Date('Y-m-d', mktime(0, 0, 0, $split[1], $split[2] + 6, $split[0]));
      $query = $this->db->query("SELECT * FROM scheduled WHERE employeeId = '$employeeId' && day >= '$startDate' && day <= '$endDate'");
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

   function getInfoSpan($startDate, $endDate)
   {
      $start = Date('Y-m-d', strtotime($startDate));
      $end = Date('Y-m-d', strtotime($endDate));

      $query = $this->db->query("SELECT SUM(TIME_TO_SEC(TIMEDIFF(scheduled.end, scheduled.begin))/3600*employees.wage) FROM scheduled LEFT JOIN employees ON scheduled.employeeId = employees.id WHERE scheduled.day >= '$start' && scheduled.day <= '$end'");
      $totalMoney = $query->row_array();

      $hours = $this->db->query("SELECT SUM(TIME_TO_SEC(TIMEDIFF(end, begin))/3600) FROM scheduled WHERE day >= '$start' && day <= '$end'");
      $hour_result = $hours->row_array();
      //array_values($hours->row_array());
      $goal = $this->getGoal($start, $end, false);
      $wages = $totalMoney['SUM(TIME_TO_SEC(TIMEDIFF(scheduled.end, scheduled.begin))/3600*employees.wage)'];
      $str = "<table><tr><th>Total Hours:</th><th> " . number_format($hour_result['SUM(TIME_TO_SEC(TIMEDIFF(end, begin))/3600)'], 1, '.', ',') . "</th></tr>";
      $str .= "<tr><th>Total Wages:</th><th>$" . number_format($wages, 1, '.', ',') . "</th></tr>";
      $str .= "<tr><th>Sales Goal:</th><th>$" . number_format($goal, 1, '.', ',') . "</th></tr>";
      $str .= "<tr><th>Labor Percentage:</th><th>" . number_format(($wages / $goal) * 100, 1, '.', ',') . "%</th></tr></table>";
      return $str;
   }

   function getEmployeeHourTotals($startDate, $endDate, $view)
   {
      $start = Date('Y-m-d', strtotime($startDate));
      $end = Date('Y-m-d', strtotime($endDate));
      $array = array();
      $q = $this->db->query("SELECT employeeId, SUM(TIME_TO_SEC(TIMEDIFF(scheduled.end, scheduled.begin))/3600) FROM scheduled WHERE day >= '$start' && day <= '$end' GROUP BY employeeId");
      foreach ($q->result() as $row)
      {
         $employee_arr = array_values((array)$row);
         $_q = $this->db->query("SELECT firstName, lastName FROM employees WHERE id='$employee_arr[0]'");
         $month = Date("Y-m", strtotime($start));
         $hpwInfo = $this->db->query("SELECT * FROM weekInfo WHERE month like '$month-%%' && employeeId = '$row->employeeId'");
         $min = $max = "";
         if ($hpwInfo->num_rows() > 0)
         {
            $result = $hpwInfo->row();
            $min = $result->minHours;
            $max = $result->maxHours;
            if ($view == 'month')
            {
               $min *= 4;
               $max *= 4;
            }
            else if ($view == 'agendaDay' || $view == 'basicDay')
            {
               $min /= 4;
               $max /= 4;
            }
         }
         foreach ($_q->result() as $_row)
         {
            $style = "";
            if (!($min == "") && !($max == ""))
            {
               if (($min - $employee_arr[1]) > 0)
                  $style = "border:2px dotted blue";
               else if (($max - $employee_arr[1]) < 0)
                  $style = "border:2px dotted red";
            }
            array_push($array, "<tr><td>$_row->firstName", " $_row->lastName</td> ", "<td style='$style'>" . number_format($employee_arr[1], 2, '.', ',') . "</td><td style='$style'>" . "$min-$max" . "</tr>");
         }
      }
      return json_encode($array);
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
      $query = $this->db->query("UPDATE scheduled SET category = '$category' WHERE id = '$id'");
      return $query;
   }

   function updateSFL($id, $sfl)
   {
      return $this->db->query("UPDATE scheduled SET sfl = '$sfl' WHERE id = '$id'");
   }

   function coEventSource($array = array())
   {
      $query = $this->db->query("SELECT * FROM events");
      foreach ($query->result() as $row)
      {
         $array[] = json_encode(array(
            "id" => md5("events$row->id"),
            "rowId" => $row->id,
            "title" => $row->title,
            "allDay" => true,
            "start" => $row->date . " " . $row->start,
            "end" => $row->date . " " . $row->end,
            "category" => "events",
            "color" => "#480091",
            "location" => $row->location,
            "repeating" => $row->repeating
         ));
      }
      return $array;
   }

   function addCOEvent($title, $date, $start, $end, $location, $repeating, $finalDate)
   {
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
         while ($_date <= $finalDate && $counter <= 52)
         {
            $query = $this->db->query("INSERT INTO events (title, date, start, end, location, repeating) VALUES ('$title', '$_date', '$start', '$end', '$location', '$repeating')");
            $increment += 7 * $repeating;
            if ($repeating == 4)
               $_date = Date("Y-m-d", strtotime("+$counter months", $time));
            else
               $_date = Date("Y-m-d", strtotime("+$increment days", $time));
            $counter++;
         }
      }
      return $query;
   }

   function scheduleEmployeeFloor($employeeId, $day, $begin, $end, $category, $sfl)
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
      $json[] = $this->makeScheduledJSON($array);
      $json[] = $refetch;
      return json_encode($json);
   }

   function scheduleEmployeeEvent($employeeId, $date, $start, $end, $eventTitle)
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
      $json[] = $this->makeScheduledJSON($array);
      $json[] = $refetch;
      return json_encode($json);
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

   function getOverlappingEvents($employeeId, $day, $start, $end)
   {
      $query = $this->db->query("SELECT * FROM scheduled WHERE employeeId = '$employeeId' && day = '$day'");
      $ids = array();
      foreach ($query->result() as $row)
      {
         if ($start >= $row->end || $end <= $row->begin)
            continue;
         if ($start < $row->begin)
         {
            if ($end > $row->begin)
            {
               $ids[] = $row->id;
            }
         }
         else if ($start = $row->begin)
         {
            $ids[] = $row->id;
         }
         if($end < $row->end)
         {
            if($end > $row->begin)
            {
               $ids[] = $row->id;
            }
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

   /*
    function getEmptyShifts()
    {
    $query = $this->db->query("SELECT * FROM emptyShifts");
    $array = array();
    foreach ($query->result() as $row)
    {
    $prettyStart = Date("g:i a", strtotime($row->start));
    $prettyEnd = Date("g:i a", strtotime($row->end));
    $array[] = json_encode(array(
    "title" => "Empty Shift ($row->category)",
    "description" => "We need more hours on $row->date.  Would you like to pick up a shift from $prettyStart until $prettyEnd on $row->date?",
    "start" => $row->date . ' ' . $row->start,
    "end" => $row->date . ' ' . $row->end,
    "allDay" => false,
    "tip" => "We need more hours on this date. Click on the shift to help out!",
    "color" => "RED",
    "category" => "emptyShifts",
    "position" => "$row->category",
    "id" => md5("emptyShifts$row->id"),
    "rowId" => $row->id
    ));
    }
    return $array;
    }*/

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

}
