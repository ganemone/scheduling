<?php
class employee extends CI_Model
{

   function __construct()
   {
      date_default_timezone_set("UTC");
   }

   function login($employeeId, $password)
   {
      $query = $this->db->query("SELECT id, permissions FROM employees WHERE id='$employeeId' && password = '$password'");
      $row = $query->row();
      if ($query->num_rows() > 0)
      {
         $cookie = array(
            'name' => "EmployeeId",
            'value' => $employeeId,
            'expire' => '86500',
         );
         $cookie2 = array(
            'name' => "permissions",
            'value' => $row->permissions,
            'expire' => '86500',
         );
         $this->input->set_cookie($cookie);
         $this->input->set_cookie($cookie2);
         return $row;
      }
      return false;
   }

   function createAccount($data)
   {
      $md5email = MD5($data['email']);
      $md5password = MD5($data['password']);
      $firstName = $data['firstName'];
      $lastName = $data['lastName'];

      $this->db->query("INSERT INTO employees (firstName, lastName,
        email, password) VALUES ('$firstName','$lastName',
        '$md5email','$md5password')");

      return true;
   }

   function getInfo($employeeId)
   {
      $query = $this->db->query("SELECT firstName, lastName FROM employees WHERE id='$employeeId'");
      $row = $query->row();
      $ret = array(
         "firstName" => $row->firstName,
         "lastName" => $row->lastName,
         "start_select" => "<select name='start' class='mobileSelect'>
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
            </select>",
            "end_select" => "<select name='end' class='mobileSelect'>
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
               </select>"
            );
      return $ret;
   }

   function updateHour($employeeId, $date, $available, $begin, $end)
   {
      $query = $this->db->query("SELECT id FROM hours WHERE employeeId='$employeeId' && day = '$date'");
      $result = $query->row();
      $id = 0;
      if($query->num_rows > 0) {
         $this->db->query("DELETE FROM hours WHERE id='{$result->id}'");
         $id = $result->id;
      }
      $insert_arr = array("employeeId" => $employeeId, "day" => $date, "available" => $available, "begin" => $begin, "end" => $end);
      $insert = $this->db->insert("hours", $insert_arr);
      $result_arr = $this->db->query("SELECT hours.*, event_settings.color FROM hours LEFT JOIN event_settings ON event_settings.category_name = hours.available WHERE hours.employeeId = '$employeeId' && hours.day = '$date'")->row();
      return array($id, $this->buildAvailableEvent($result_arr));      
   }
   function buildAvailableEvent($row)
   {
      $allDay; $title;
      $start = "";
      $end = "";
      if($row->available == "Custom") {
         $start = " " . $row->begin;
         $end = " " . $row->end;
         $allDay = false;
         $description = "This means you are available for the times listed below.  You can drag this to another day to duplicate it!";
      }
      else {
         $allDay = true;
         $description = "This means you are " . strtolower($row->available) . " for the entire day.";
      }
      return array(
         "title"  => ($row->available == "Custom") ? "" : $row->available,
         "allDay" => $allDay,
         "start"  => $row->day . $start,
         "end"    => $row->day . $end,
         "color"  => "#" . $row->color,
         "tip"    => $description,
         "id"     => $row->id
      );
   }

   function getFinalizedDate()
   {
      $query = $this->db->query("SELECT viewable FROM settings");
      if ($query->num_rows() > 0)
         return Date('Y-m-d', strtotime($query->row()->viewable));
      return Date('Y-m', mktime(0, 0, 0, Date('m') + 2, 1, Date('Y')));
   }

   function getScheduledEvents($employeeId)
   {
      $finalizedDate = $this->getFinalizedDate();
      $json = array();
      // put get empty shifts here if expanded...
      $query = $this->db->query("SELECT scheduled.* FROM scheduled INNER JOIN requests ON scheduled.id = requests.shiftId");
      foreach ($query->result() as $row)
      {
         if (!($row->sfl == 1 && $this->isEmployeeSFL($employeeId) == FALSE))
         {
            $title = ($row->category != "SF") ? "($row->category)" : "Floor";
            $color = '';
            $description = '';
            $category = '';
            $tip = '';
            if ($row->employeeId == $employeeId)
            {
               $tip = "This shift is up for cover. Click on it to take it back. You are still responsible for it until someone takes it.";
               $color = "Purple";
               $description = 'Would you like to remove your shift from cover? This means it will no longer be available for other employees to take.';
               $category = "scheduled-cover";
            }
            else
            {
               $tip = "Click on the event to pick up this shift.";
               $title = "Pick Up " . $title;
               $color = "Red";
               $name = $this->db->query("SELECT firstName, lastName FROM employees WHERE id = '$row->employeeId'");
               $names = $name->row();
               $description = "Would you like to pick up this shift for $names->firstName $names->lastName?";
               $category = "scheduled-pickup";
            }
            array_push($json, json_encode(array(
               "title" => $title,
               "start" => "$row->day $row->begin",
               "end" => "$row->day $row->end",
               "allDay" => false,
               "color" => $color,
               "id" => $row->id,
               'description' => $description,
               "tip" => $tip,
               'employeeId' => $row->employeeId,
               'category' => $category
            )));
         }
      }
      $_query = $this->db->query("SELECT scheduled.* FROM scheduled LEFT JOIN requests ON scheduled.id = requests.shiftId WHERE requests.shiftId IS Null && scheduled.day < '$finalizedDate' && scheduled.employeeId = '$employeeId'");
      foreach ($_query->result() as $row)
      {
         $title = ($row->category != "SF") ? "($row->category)" : "Floor";
         $title .= ($row->sfl == 1) ? " (SFL)" : "";
         array_push($json, json_encode(array(
            "title" => "$title",
            "start" => "$row->day $row->begin",
            "end" => "$row->day $row->end",
            "allDay" => false,
            "color" => 'Orange',
            'employeeId' => $row->employeeId,
            'id' => $row->id,
            'tip' => "You are scheduled for this day during the times listed below.",
            'category' => "scheduled"
         )));
      }
      return $json;
   }

   function getAvailableEvents($employeeId)
   {
      $finalizedMonth = $this->getFinalizedDate();
      $json = array();
      $query = $this->db->query("SELECT * FROM hours WHERE employeeId='$employeeId'");
      foreach ($query->result() as $row)
      {
         if ($row->available == "Custom")
         {
            array_push($json, json_encode(array(
               "title" => "",
               "start" => "$row->day $row->begin",
               "end" => "$row->day $row->end",
               "allDay" => FALSE,
               'id' => $row->id,
               'employeeId' => $row->employeeId,
               'category' => "availability",
               "color" => "#3366CC",
               "tip" => "This means you are available for the times listed below.  You can drag this to another day to duplicate it!"
            )));
         }
         else if ($row->available == "Available")
         {
            array_push($json, json_encode(array(
               "title" => "$row->available",
               "start" => "$row->day",
               "color" => "#32CD32",
               'id' => $row->id,
               'employeeId' => $row->employeeId,
               'category' => "availability",
               "tip" => "This means you are available for the entire day",
            )));
         }
         else
         {
            array_push($json, json_encode(array(
               "title" => "$row->available",
               "start" => "$row->day",
               "color" => "#000000",
               'id' => $row->id,
               'employeeId' => $row->employeeId,
               'category' => "availability",
               "tip" => "This means you are busy for the entire day."
            )));
         }
      }
      return $json;
   }

   function coEventSource($array = array())
   {
      $query = $this->db->query("SELECT * FROM events");
      foreach ($query->result() as $row)
      {
         $array[] = json_encode(array(
            "id" => $row->id,
            "title" => $row->title,
            "allDay" => true,
            "start" => $row->date . " " . $row->start,
            "end" => $row->date . " " . $row->end,
            "category" => "events",
            "color" => "#480091",
            "location" => $row->location,
            "repeating" => $row->repeating,
            "tip" => "$row->title At $row->location"
         ));
      }
      return $array;
   }

   function insertMonthInfo($employeeId, $date, $min, $max, $notes)
   {
      $query = $this->db->query("DELETE FROM weekInfo WHERE employeeId = '$employeeId' && month ='$date'");
      $_query = $this->db->query("INSERT INTO weekInfo (employeeId, month, minHours, maxHours, notes) VALUES ('$employeeId','$date','$min','$max','$notes')");
      return $query;
   }

   function popMonthInfoForm($employeeId, $month)
   {
      $employeeId = $this->input->cookie('EmployeeId');
      $query = $this->db->query("SELECT * FROM weekInfo WHERE employeeId = '$employeeId' && month = '$month'");

      if ($query->num_rows() > 0)
         return $query->row();
      else
         return "";
   }

   function inputScheduleRequest($id)
   {
      $q = $this->db->query("DELETE FROM requests WHERE shiftId = '$id'");
      $query = $this->db->query("INSERT INTO requests (shiftId) VALUES ('$id')");
      return $query;
   }

   function employeeShiftSwap($oldId, $newId, $eventId)
   {
      $query = $this->db->query("SELECT begin, end, day FROM scheduled WHERE id = '$eventId'");
      $row = $query->row();
      $ret_arr = $this->canEmployeeTakeShift($row->begin, $row->end, $newId, $row->day);
      if ($ret_arr[0] == false)
      {
         if ($oldId != $newId)
            return array("false", $ret_arr[1]);
      }
      $query = $this->db->query("UPDATE scheduled SET employeeId = '$newId' WHERE id='$eventId'");
      $query = $this->db->query("DELETE FROM requests WHERE shiftId = '$eventId'");
      $query = $this->db->query("SELECT day FROM scheduled WHERE id='$eventId'");
      $result = $query->row();
      $this->mergeShifts($result->day, $newId);
      $this->mergeShifts($result->day, $oldId);
      return true;
   }

   function partialShiftSwap($start, $end, $newId, $oldId, $eventId)
   {
      $query = $this->db->query("SELECT * FROM scheduled WHERE id = '$eventId'");
      $result = $query->row();
      $ret_arr = $this->canEmployeeTakeShift($start, $end, $newId, $result->day);
      if ($ret_arr[0] == false)
      {
         if ($oldId != $newId)
            return json_encode(array("false", $ret_arr[1]));
      }
      if ($start == $result->begin && $end == $result->end) {
         $this->employeeShiftSwap($oldId, $newId, $eventId);
      }
      else if ($start > $result->begin)
      {
         $query = $this->db->query("UPDATE scheduled SET end = '$start' WHERE id = '$eventId'");
         $query = $this->db->query("INSERT INTO scheduled (employeeId, day, begin, end, category, sfl) values ('$newId','$result->day', '$start','$end', '$result->category', '$result->sfl')");
         if ($end < $result->end)
         {
            $query = $this->db->query("INSERT INTO scheduled (employeeId, day, begin, end, category, sfl) values ('$oldId', '$result->day','$end', '$result->end', '$result->category','$result->sfl')");
            $query = $this->db->query('SELECT LAST_INSERT_ID()');
            $row = $query->row_array();
            $this->inputScheduleRequest($row['LAST_INSERT_ID()']);
         }
      }
      else
      {
         $query = $this->db->query("UPDATE scheduled SET begin = '$end' WHERE id = '$eventId'");
         $query = $this->db->query("INSERT INTO scheduled (employeeId, day, begin, end, category, sfl) values ('$newId', '$result->day', '$start','$end', '$result->category','$result->sfl')");
      }
      $this->mergeShifts($result->day, $oldId);
      $this->mergeShifts($result->day, $newId);
      return true;
   }

   function mergeShifts($day, $employeeId)
   {
      $query = $this->db->query("SELECT scheduled.* FROM scheduled LEFT JOIN requests ON scheduled.id = requests.shiftId WHERE requests.shiftId is null && scheduled.day = '$day' && employeeId = '$employeeId' ORDER BY scheduled.begin");
      if ($query->num_rows() > 1)
      {
         $counter = 0;
         $previousStart;
         $previousEnd;
         $previousId;
         foreach ($query->result() as $row)
         {
            if ($counter > 0)
            {
               if ($previousStart == $row->end)
               {
                  $this->db->query("UPDATE scheduled SET end = '$previousEnd' WHERE id='$row->id'");
                  $this->db->query("DELETE FROM scheduled WHERE id='$previousId'");
                  return $this->mergeShifts($day, $employeeId);
               }
               else if ($previousEnd == $row->begin)
               {
                  $this->db->query("UPDATE scheduled SET begin = '$previousStart' WHERE id='$row->id'");
                  $this->db->query("DELETE FROM scheduled WHERE id='$previousId'");
                  return $this->mergeShifts($day, $employeeId);
               }
               
            }
            $previousStart = $row->begin;
            $previousEnd = $row->end;
            $previousId = $row->id;
            $counter++;
         }
      }
   }

   function canEmployeeTakeShift($start, $end, $newId, $day)
   {
      $query = $this->db->query("SELECT begin, end FROM scheduled WHERE employeeId = '$newId' && day='$day'");
      $result = $query->row();
      if ($query->num_rows() > 0)
      {
         if ($result->begin < $end && $result->begin >= $start)
            return array(FALSE, "You cannot pick up this shift becuase it overlaps with another shift you are currently scheduled for.");
         if ($result->end < $end && $result->end > $start)
            return array(FALSE, "You cannot pick up this shift becuase it overlaps with another shift you are currently scheduled for.");
         if ($result->begin <= $start && $result->end > $start)
            return array(FALSE, "You cannot pick up this shift becuase it overlaps with another shift you are currently scheduled for.");
      }
      return $this->overTimeCheck($newId, $start, $end, $day);
   }
   
   function overTimeCheck($employeeId, $start, $end, $date)
   {
      $shift_hours = abs(strtotime($end) - strtotime($start))/(60*60);
      $dayNum = Date("w", strtotime($date));
      $week_start = Date("Y-m-d", strtotime($date . "-$dayNum days"));
      $week_end = Date("Y-m-d", strtotime($week_start . "+6 days"));
      $query = $this->db->query("SELECT SUM(TIME_TO_SEC(TIMEDIFF(scheduled.end, scheduled.begin))/3600) FROM scheduled WHERE day >= '$week_start' && day <= '$week_end' && employeeId = '$employeeId'");
      $hours = $query->row_array();
      if($hours['SUM(TIME_TO_SEC(TIMEDIFF(scheduled.end, scheduled.begin))/3600)'] + $shift_hours > 40)
         return array(FALSE, "You cannot pick up this shift because it puts you into overtime hours.");
      return array(TRUE, 1);
   }
   
   function requestPartialShiftCover($start, $end, $employeeId, $shiftId)
   {
      $query = $this->db->query("SELECT * FROM scheduled WHERE id='$shiftId'");
      $row = $query->row();
      if ($start == $row->begin && $end == $row->end)
      {
         return $this->inputScheduleRequest($shiftId);
      }
      else if ($start > $row->begin)
      {
         if ($end < $row->end)
         {
            $this->db->query("UPDATE scheduled SET begin='$start', end='$end' WHERE id='$shiftId'");
            $this->inputScheduleRequest($shiftId);
            $this->db->query("INSERT INTO scheduled (employeeId, begin, end, day, category, sfl) VALUES ('$employeeId','$row->begin','$start','$row->day', '$row->category','$row->sfl')");
            $this->db->query("INSERT INTO scheduled (employeeId, begin, end, day, category, sfl) VALUES ('$employeeId','$end','$row->end','$row->day','$row->category','$row->sfl')");
         }
         else
         {
            $this->db->query("UPDATE scheduled set begin='$start' WHERE id='$shiftId'");
            $this->inputScheduleRequest($shiftId);
            $this->db->query("INSERT INTO scheduled (employeeId, begin, end, day, category, sfl) VALUES ('$employeeId','$row->begin','$start','$row->day', '$row->category','$row->sfl')");
         }
      }
      else
      {
         $this->db->query("UPDATE scheduled SET end='$end' WHERE id='$shiftId'");
         $this->inputScheduleRequest($shiftId);
         $this->db->query("INSERT INTO scheduled (employeeId, begin, end, day, category, sfl) VALUES ('$employeeId','$end', '$row->end','$row->day', '$row->category','$row->sfl')");
      }
   }

   function allStaffSource($employeeId)
   {
      $query = $this->db->query("SELECT id, firstName, lastName, position FROM employees WHERE id != '$employeeId'");
      $_json = array();
      $busy = $this->input->cookie("busy");
      foreach ($query->result() as $row)
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
            $cat = "";
            if ($_row->category != "SF" && $_row->category != null && strpos($_row->category, "event") == false)
               $cat = "($_row->category)";
            $color = "#EB8F00";

            array_push($_json, json_encode(array(
               "title" => "$name $cat $sfl",
               "start" => $start,
               "end" => $_end,
               "allDay" => false,
               'color' => "$color",
               "employeeId" => $row->id,
               'category' => 'scheduled',
               'id' => $_row->id,
               'position' => $row->position,
               'area' => $_row->category,
               'borderColor' => $border,
               'sfl' => $sfl
            )));
         }
      }
      return $_json;
   }

   function pickUpEmptyShift($employeeId, $start, $end, $date, $position, $sfl, $shiftId)
   {
      $start = Date("H:i:s", strtotime($start));
      $end = Date("H:i:s", strtotime($end));
      $date = Date("Y-m-d", strtotime($date));
      $ret_arr = $this->canEmployeeTakeShift($start, $end, $employeeId, $date);
      if ($ret_arr[0] == FALSE)
      {
         return array("false", $ret_arr[1]);
      }
      $query = $this->db->query("SELECT start, end FROM emptyShifts WHERE id='$shiftId'");
      $row = $query->row();
      if ($row->start == $start && $row->end == $end)
      {
         $query = $this->db->query("DELETE FROM emptyShifts WHERE id='$shiftId'");
      }
      else if ($row->start == $start)
      {
         $query = $this->db->query("UPDATE emptyShifts SET start = '$end' WHERE id='$shiftId'");
      }
      else if ($row->end == $end)
      {
         $query = $this->db->query("UPDATE emptyShifts SET end = '$start' WHERE id='$shiftId'");
      }
      else
      {
         $query = $this->db->query("UPDATE emptyShifts SET start = '$end' WHERE id='$shiftId'");
         $query = $this->db->query("INSERT INTO emptyShifts (start, end, date, category, sfl) VALUES ('$row->start','$start','$date','$position','$sfl')");
      }
      $query = $this->db->query("INSERT INTO scheduled (employeeId, begin, end, day, category, sfl) VALUES ('$employeeId','$start','$end','$date','$position','$sfl')");
      return $query;
   }

   function isEmployeeSFL($employeeId)
   {
      $query = $this->db->query("SELECT position FROM employees WHERE id='$employeeId'");
      $row = $query->row();
      if ($row->position == 'SFL')
         return TRUE;
      return FALSE;
   }

   function pasteWeek($week_start, $week_end, $week_obj)
   {
      $del_query = "DELETE FROM hours WHERE day >= '$week_start' && day <= '$week_end'";
      $this->db->query($del_query);

      $add_query = "INSERT INTO hours (employeeId, available, day, begin, end) VALUES ";
      $add_val_arr = array();
      foreach($week_obj as $event_obj)
      {
         $employeeId = $event_obj->employeeId;
         $available = $event_obj->available;
         $day = Date("Y-m-d", strtotime($week_start) + ($event_obj->day)*24*60*60);
         $start = Date("H:i:s", strtotime($event_obj->start));
         $end = ($available == "Custom") ? Date("H:i:s", strtotime($event_obj->end)) : "00:00:00";
         $add_val_arr[] = "(" . $employeeId . ", '" . $available . "', '" . $day . "', '" . $start . "', '" . $end . "')";
      }
      $add_query.= implode(", ", $add_val_arr);
      
      return $this->db->query($add_query);
      
   }

}
