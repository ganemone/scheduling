<?php
class validator extends CI_Model
{
   function __construct()
   {
      date_default_timezone_set("UTC");
   }

   function valid_date($date)
   {
      return ($date == '0000-00-00' || $date == "" || $date == null || $date == '1970-01-01') ? false : preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $date);
   }

   function valid_time($time)
   {
      return ($time == '0000:00:00' || $time == "" || $time == null) ? false : preg_match("/^[0-9]{2}:[0-9]{2}:[0-9]{2}$/", $time);
   }

   function valid_employee_id($employee_id)
   {
      return ($this->db->where("id", $employee_id)->from("employees")->count_all_results() > 0) ? true : false;
   }

   function valid_availability($availability)
   {
      switch ($availability) {
         case 'Available':return true;
         case 'Custom':return true;
         case 'Busy':return true;
         default:return false;
      }
   }
   function valid_view($view) 
   {
      switch ($view) {
         case 'agendaDay'  : return true;
         case 'basicDay'   : return true;
         case 'agendaWeek' : return true;
         case 'basicWeek'  : return true;
         case 'month'      : return true;
         default: return false;
      }
   }
   function valid_manager($employee_id)
   {
      return ($this->db->query("SELECT permissions FROM employees WHERE id = '$employee_id'")->row()->permissions == 3) ? true : false;
   }
   function valid_sfl($employee_id)
   {
      return ($this->db->query("SELECT permissions FROM employees WHERE id = '$employee_id'")->row()->permissions >= 2) ? true : false;
   }
   function valid_name($name)
   {
      $result = preg_match("/^[a-zA-Z]{2,30}$/", $name);
      if($result == false) {
         error_log("valid_name");
      }
      return $result;
   }
   function valid_group_name($name)
   {
      $result = preg_match("/^[a-zA-Z\ \-_]{2,30}$/", $name);
      if($result == false) {
         error_log("valid_group_name");
      }
      return $result;
   }
   function valid_email($email)
   {
      $result = preg_match("/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/", $email);
      if($result == false) {
         error_log("valid_email");
      }
      return $result;
   }
   function valid_permissions($permissions)
   {
      return ($permissions == '1' || $permissions == '2' || $permissions == '3') ? true : false;
   }
   function valid_position($position)
   {
      return ($position == "SA" || $position == "SFL" || $position == "SP") ? true : false;
   }
   function valid_wage($wage)
   {
      $result = preg_match("/^([0-9]+)((\.[0-9])*)$/", $wage);
      if($result == false) {
         error_log("valid_wage");
      }
      return $result;
   }
   function valid_password($password)
   {
      $result = preg_match("/^[0-9a-zA-Z\-_]{3,40}$/", $password);
      if($result == false) {
         error_log("valid_password");
      }
      return $result;
   }
   function valid_group_id($group_id)
   {
      return ($this->db->query("SELECT COUNT(*) AS count FROM groups WHERE group_id = '$group_id'")->row()->count > 0) ? true : false;
   }
   function valid_shift_category($category_abbr)
   {
      return ($this->db->query("SELECT Count(*) AS count FROM event_settings WHERE category_abbr = '$category_abbr'")->row()->count > 0 ? true : false);
   }
   function valid_csv($file)
   {
      return ($file['type'] == "text/csv") ? true : false;
   }
   function valid_external_event($event_id)
   {
      return ($this->db->query("SELECT COUNT(*) AS count FROM events WHERE id = '$event_id'")->row()->count > 0) ? true : false;
   }
}
