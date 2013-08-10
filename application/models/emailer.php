<?php
class emailer extends CI_Model
{
   public $emailList;
   public $leaderList;
   function __construct()
   {
      date_default_timezone_set("UTC");
      $this->load->library("email");     
      $query = $this->db->query("SELECT email FROM employees");
      foreach($query->result() as $row)
      {
         $this->emailList[] = $row->email;
      }
      $query = $this->db->query("SELECT email FROM employees WHERE position = 'SFL'");
      foreach($query->result() as $row)
      {
         $this->leaderList[] = $row->email;
      }
   }
   function emailToAll($subject, $message)
   {
      $this->email->from("admin@gazellescheduling.nfshost.com", "Gazelle Sports Scheduling");
      $this->email->to($this->emailList);
      $this->email->subject($subject);
      $this->email->message($message);
      $this->email->send();
      return $this->email->print_debugger();
   }
   function shiftCoverRequestEmail($shiftId, $employeeId, $start = null, $end = null)
   {
      $subject = "Shift Cover Alert";
      $name = $this->getName($employeeId);
      if($start == null)
         $start = Date("g:i a", strtotime($this->getStart($shiftId)));
      else 
         $start = Date("g:i a", strtotime($start));          
      if($end == null)
         $end = Date("g:i a", strtotime($this->getEnd($shiftId)));
      else 
         $end = Date("g:i a", strtotime($end));
      $message = "$name needs a shift on " . $this->getDate($shiftId) . " covered from $start until $end.";
      return $this->emailToAll($subject, $message);
   }
   function shiftSwap($employeeId, $originalEmployeeId, $shiftId, $start = null, $end = null)
   {
      $newName = $this->getName($employeeId);
      $oldName = $this->getName($originalEmployeeId);
      $newEmail = $this->getEmail($employeeId);
      $oldEmail = $this->getEmail($originalEmployeeId);
      
      $cc[] = $newEmail;
      $cc[] = $oldEmail;
      
      $start = ($start == null) ? $this->getStart($shiftId) : $start;
      $end = ($end == null) ? $this->getEnd($shiftId) : $end;
      
      $start = Date("g:i a", strtotime($start));
      $end = Date("g:i a", strtotime($end));
      $date = $this->getDate($shiftId);
      $subject = "Shift Cover Update";
      $message = "";
      if ($employeeId == $originalEmployeeId)
         $message = "$oldName has removed their shift on $date from $start until $end from cover. ";
      else
         $message = "$newName has picked up a shift for $oldName on $date from $start until $end. ";
      $message.= "Please check your calendar for updates.";
      
      return $this->emailManagersAndLeaders($subject, $message, $cc);
   }
   function getName($employeeId)
   {
      $query = $this->db->query("SELECT firstName, lastName FROM employees WHERE id='$employeeId' LIMIT 1");
      foreach($query->result() as $row)
      {
         return "$row->firstName $row->lastName";
      }
   }
   function getStart($shiftId)
   {
      $query = $this->db->query("SELECT begin FROM scheduled WHERE id='$shiftId'");
      foreach($query->result() as $row)
      {
         return $row->begin;
      }
   }
   function getEnd($shiftId)
   {
      $query = $this->db->query("SELECT end FROM scheduled WHERE id='$shiftId'");
      foreach($query->result() as $row)
      {
         return $row->end;
      }
   }
   function getDate($shiftId)
   {
      $query = $this->db->query("SELECT day FROM scheduled WHERE id='$shiftId'");
      foreach($query->result() as $row)
      {
         return $row->day;
      }
   }
   function getEmail($employeeId)
   {
      $query = $this->db->query("SELECT email FROM employees WHERE id='$employeeId'");
      foreach($query->result() as $row)
      {
         return $row->email;
      }
   }
   function emailManagersAndLeaders($subject, $message, $cc)
   {
      $this->email->from("admin@gazellescheduling.nfshost.com", "Gazelle Sports Scheduling");
      $this->email->to($this->leaderList);
      $this->email->cc($cc);
      $this->email->subject($subject);
      $this->email->message($message);
      $this->email->send();
      return $this->email->print_debugger();
   }
   function emailError($message)
   {
      $this->email->from("admin@gazellescheduling.nfshost.com", "Gazelle Sports Scheduling");
      $this->email->to("ganemone@gmail.com");
      $this->email->subject("Error Report");
      $this->email->message($message);
      $this->email->send();
      return $this->email->print_debugger();
   }
}
?>