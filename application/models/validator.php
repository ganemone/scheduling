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
   
}
