<?php

class Leader extends CI_Model
{

   function __construct()
   {
      date_default_timezone_set("UTC");
   }

   function login($employeeId, $password)
   {
      $query = $this->db->query("SELECT id FROM employees WHERE id='$employeeId' && password = '$password' && position='SFL'");

      if ($query->num_rows() == 1)
      {
         $cookie = array(
            'name' => "EmployeeId",
            'value' => $employeeId,
            'expire' => '86500',
         );
         $this->input->set_cookie($cookie);
         return true;
      }
      return false;
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
   
   function floorEventSource($start, $end)
   {
      $finalizedDate = $this->input->cookie('finalized');
      if($end > strtotime($finalizedDate)) {
         $end_date = date("Y-m-d", strtotime($finalizedDate));
      }
      else {
         $end_date = date("Y-m-d", $end); 
      }

      if($start > strtotime($finalizedDate)) {
         return array();
      }
      $start_date = date("Y-m-d", $start);
      return $this->getJSONArray("WHERE scheduled.category != 'SP' && scheduled.event = '0' && scheduled.day >= '$start_date' && scheduled.day <= '$end_date'");
   }

   function supportEventSource($start, $end)
   {
      $finalizedDate = $this->input->cookie('finalized');
      if($end > strtotime($finalizedDate)) {
         $end_date = date("Y-m-d", strtotime($finalizedDate));
      }
      else {
         $end_date = date("Y-m-d", $end);
      }

      if($start > strtotime($finalizedDate)) {
         return array();
      }
      $start_date = date("Y-m-d", $start);
      return $this->getJSONArray("WHERE scheduled.category = 'SP' || scheduled.event = '1' && scheduled.day >= '$start_date' && scheduled.day <= '$end_date'");
   }
   function coEventSource($start, $end)
   {
      $finalizedDate = $this->input->cookie('finalized');
      if($end > strtotime($finalizedDate)) {
         $end_date = date("Y-m-d", strtotime($finalizedDate));
      }
      else {
         $end_date = date("Y-m-d", $end);
      }

      if($start > strtotime($finalizedDate)) {
         return array();
      }
      $start_date = date("Y-m-d", $start);
      $query = $this->db->query("SELECT * FROM events WHERE date >= '$start_date' && date <= '$end_date'");
      $array = array();
      foreach ($query->result() as $row)
      {
         $array[] = json_encode(array(
            "id" => md5("events$row->id"),
            "rowId" => $row->id,
            "title" => $row->title . " (" . date("g:i a", strtotime($row->start)) . "-" . date("g:i a", strtotime($row->end)) . ")",
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

   function getJSONArray($where)
   {
      $_json = array();
      $query = $this->db->query("SELECT employees.id, employees.firstName, employees.lastName, employees.position, event_settings.color, event_settings.border, scheduled.* 
         FROM scheduled 
         LEFT JOIN employees 
         ON scheduled.employeeId = employees.id 
         LEFT JOIN event_settings
         ON scheduled.category = event_settings.category_abbr $where");
      foreach ($query->result() as $row)
      {
         $name = "$row->firstName " . $row->lastName[0];
         $sfl = ($row->sfl == 1) ? "(SFL)" : "";
         $border = ($row->sfl == 1) ? "BLACK" : "";
         $begin = $row->begin;
         $end = $row->end;
         $start = Date('Y-m-d H:i:s', strtotime("$row->day $begin"));
         $_end = Date('Y-m-d H:i:s', strtotime("$row->day $end"));
         $cat = ($row->category == "SF" || $row->category == "SP") ? "" : "({$row->category})";
         
         $color = ($row->color == null) ? "#790ead" : "#" . $row->color;
         $border = ($row->sfl == 1) ? "#000000" : ($row->border == null) ? "#790ead" : "#" . $row->border;

         $_json[] = json_encode(array(
            "title" => " $name $cat $sfl",
            "start" => $start,
            "end" => $_end,
            "allDay" => false,
            'color' => $color,
            "employeeId" => $row->id,
            'category' => 'scheduled',
            'id' => $row->id,
            'position' => $row->position,
            'area' => $row->category,
            'borderColor' => $border,
            'sfl' => $sfl
         ));
      }
      return $_json;
   }

   function addMissedSale($desc, $vendor, $size, $price, $date, $cat, $gender)
   {
      $desc  = ($desc == "")  ? "NA" : $desc;
      $price = ($price == "") ? "NA" : $price;
      $size  = ($size == "")  ? "NA" : $size;
      $vendor = ($vendor == "") ? "NA" : $vendor;
      
      $result = $this->db->insert("missedSales", array(
         'date' => $date,
         'vendor' => $vendor,
         'description' => $desc,
         'size' => $size,
         'price' => $price,
         'category' => $cat,
         'gender' => $gender));
      
      return $result;
   }

   function getMissedSales($date)
   {
      $query = $this->db->query("SELECT * FROM missedSales WHERE date = '$date'");
      if ($query->num_rows() == 0)
         return "There were no missed sales recorded today.";

      $str = "<table class='nightlyEmailTable'><tr><th>Description</th><th>Size</th><th>Gender</th><th>Price</th><th>Total</th></tr>";
      //$_total = 0;
      foreach ($query->result() as $row)
      {
         //$_total += $row->price*$row->quantity;
         $total = '$' . number_format($row->price * $row->quantity, 2, ".", ",");
         $price = '$' . number_format($row->price, 2, '.', ',');
         $str.= "<tr><td>$row->description</td><td>$row->size</td><td>$row->gender</td><td>$price</td><td>$total</td></tr>";         
      }
      //$_total = '$' . number_format($_total, 2, '.',',');
      //$str.= "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td>$_total</td></tr></table>";
      $str.= "</table>";
      return $str;
   }

   function isValidEmployee($employeeId)
   {
      $query = $this->db->query("SELECT firstName, lastName FROM employees WHERE id='$employeeId'");
      if ($query->num_rows() == 0)
         return false;
      return $query->row();
   }

   function addStory($employeeId, $story, $date)
   {
      $name = $this->isValidEmployee($employeeId);
      if ($name === false)
      {
         return "false";
      }
      else
      {
         $formattedName = $name->firstName . " " . $name->lastName[0];
         return $this->db->query("INSERT INTO stories (employeeId, name, date, description) values ('$employeeId', '$formattedName', '$date','$story')");
      }
      return "false";
   }

   function getEmailTemplate($date)
   {
      $employees = $this->getEmployeesClosing($date);
      $goal = $this->getGoal($date);
      $missed_sales = $this->getMissedSales($date);
      $stories = $this->getStories($date);
      
      $str = "<div id='emailTemplate' style='text-align: left;'>";
      $str.= "<b>Staff</b><br>";
      $str.= "<div id='emailEmployees'>";
      
      foreach($employees as $key => $val_arr)
      {
         $str.= $val_arr['name'] . ", ";
      }
      $str.= "</div>";
      $str.= "<b>Morning meeting</b><br><br><br>";
      $str.= "<b>Employee Actions</b><br>";
      $str.= "<div id='emailEmployeeActions'>$stories</div><br>";
      $str.= "<b>Customer Care</b><br><br><br>";
      $str.= "<b>Missed Sales</b><br>";
      $str.= "<div id='emailMissedSales'>$missed_sales</div><br>";
      $str.= "<b>Misc:</b><br><br><br>";
      $str.= "<b>Today Sales $</b><br>";
      $str.= "<b>Sales Goal $goal</b><br>";
      $str.= "</div>";
      
      return $str;
   }      

   function getGoal($date)
   {
      $query = $this->db->query("SELECT goal FROM goals WHERE date = '$date'");
      if ($query->num_rows() == 0)
         return 0;

      $row = $query->row();
      return '$' . number_format($row->goal, 0, ".", ",");
   }

   function getStories($date)
   {
      $str = "";
      $query = $this->db->query("SELECT * FROM stories WHERE date = '$date'");
      foreach($query->result() as $row)
      {
         //$str.= "<b>" . $row->name . "</b><br>";
         $str.= "<div class='story'> - " . $row->description . "</div>";
      }
      return $str;
   }
   
   function getEmployeesWorking($date)
   {
      $query = $this->db->query("SELECT employeeId FROM scheduled WHERE day = '$date' && category != 'SP' && event = 0");
      $employees = array();
      $count = 0;
      foreach($query->result() as $row)
      {
         $count++;
         $_query = $this->db->query("SELECT firstName, lastName FROM employees WHERE id = '$row->employeeId'");
         $_row = $_query->row();
         if(isset($_row->firstName) && isset($_row->lastName))
            $employees[$row->employeeId] = array("name" => $_row->firstName . " " . $_row->lastName, "story" => "NOTHING INPUTTED");
      }
      return $employees;
   }

   function getEmployeesClosing($date) 
   {
      $day = date("w", strtotime($date));
      $closingTime = "20:00:00";
      if($day == 0) {
         $closingTime = "17:00:00";
      } else if($day == 6) {
         $closingTime = "18:00:00";
      }
      $query = $this->db->query("SELECT employeeId FROM scheduled WHERE day = '$date' && category != 'SP' && event = 0 && end >= '$closingTime'");
      $employees = array();
      $count = 0;
      foreach($query->result() as $row)
      {
         $count++;
         $_query = $this->db->query("SELECT firstName, lastName FROM employees WHERE id = '$row->employeeId'");
         $_row = $_query->row();
         if(isset($_row->firstName) && isset($_row->lastName))
            $employees[$row->employeeId] = array("name" => $_row->firstName . " " . $_row->lastName, "story" => "NOTHING INPUTTED");
      }
      return $employees;
   }
}
