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

   function floorEventSource()
   {
      return $this->getJSONArray("category != 'SP'");
   }

   function supportEventSource()
   {
      return $this->getJSONArray("category = 'SP'");
   }

   function getJSONArray($where)
   {
      $_json = array();
      $query = $this->db->query("SELECT id, firstName, lastName, position FROM employees");
      $finalizedDate = $this->input->cookie('finalized');
      foreach ($query->result() as $row)
      {
         $name = "$row->firstName " . $row->lastName[0];
         $_query = $this->db->query("SELECT * FROM scheduled WHERE employeeId = '$row->id' && day <= '$finalizedDate' && $where");
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
            if ($_row->category == "SP")
               $color = "#EB8F00";
            else if ($_row->sfl == 1)
               $color = "#B81900";
            else
               $color = "#3366CC";

            array_push($_json, json_encode(array(
               "title" => " $name $cat $sfl",
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

   function addMissedSale($style, $color, $desc, $size, $price, $date)
   {
      $style = ($style == "") ? "NA": $style;
      $color = ($color == "") ? "NA": $color;
      $desc = ($desc == "") ? "NA": $desc;
      $price = ($price == "") ? "NA": $price;

      $query = $this->db->query("SELECT id FROM missedSales WHERE style = '$style' && color = '$color' && size = '$size' && date = '$date'");
      if ($query->num_rows() > 0)
      {
         $row = $query->row();
         $result = $this->db->query("UPDATE missedSales SET quantity = quantity + 1 WHERE id='$row->id'");
      }
      else
      {
         $result = $this->db->query("INSERT INTO missedSales (style, date, color, description, size, price) values ('$style', '$date', '$color', '$desc', '$size', '$price')");         
      }
      return $result;
   }

   function getMissedSales($date)
   {
      $query = $this->db->query("SELECT * FROM missedSales WHERE date = '$date'");
      if ($query->num_rows() == 0)
         return "There were no missed sales recorded today.";

      $str = "<table class='nightlyEmailTable'><tr><th>Style Number</th><th>Color-Code</th><th>Description</th><th>Size</th><th>Price</th><th>Quantity</th><th>Total</th></tr>";
      //$_total = 0;
      foreach ($query->result() as $row)
      {
         //$_total += $row->price*$row->quantity;
         $total = '$' . number_format($row->price * $row->quantity, 2, ".", ",");
         $price = '$' . number_format($row->price, 2, '.', ',');
         $str.= "<tr><td>$row->style</td><td>$row->color</td><td>$row->description</td><td>$row->size</td><td>$price</td><td>$row->quantity</td><td>$total</td></tr>";         
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
      $employees = $this->getEmployeesWorking($date);
      $goal = $this->getGoal($date);
      $missed_sales = $this->getMissedSales($date);
      $stories = $this->getStories($date, $employees);
      
      $str = "<div id='emailTemplate'>";
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
      $str.= "<b>Transfers</b><br><br><br>";
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

   function getStories($date, $employees)
   {
      $str = "";
      $query = $this->db->query("SELECT * FROM stories WHERE date = '$date'");
      foreach($query->result() as $row)
      {
         if(isset($employees[$row->employeeId]['story']) && $employees[$row->employeeId]['story'] == "NOTHING INPUTTED")
            $employees[$row->employeeId]['story'] = $row->description;
         else if(isset($employees[$row->employeeId]['story']))
            $employees[$row->employeeId]['story'] .= "<br>" . $row->description;
      }
      foreach ($employees as $key => $val_arr) 
      {
         if($val_arr['story'] != "NOTHING INPUTTED" && $val_arr['story'] != "")
         {
            $str.= "<b>" . $val_arr['name'] . "</b><br>";
            $str.= "<div class='story'>" . $val_arr['story'] . "</div>";
         }
      }
      return $str;
   }
   
   function getEmployeesWorking($date)
   {
      $query = $this->db->query("SELECT employeeId FROM scheduled WHERE day = '$date'");
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
